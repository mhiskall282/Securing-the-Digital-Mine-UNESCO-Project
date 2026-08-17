"""Transfer Learning pipeline: NSL-KDD → SWaT.

This module implements Phase 2 of the UNESCO ICS-IDS project: adapting the
CNN-LSTM model trained on NSL-KDD to detect anomalies in SWaT industrial
sensor/actuator data.

Transfer learning strategy:
  1. Load pre-trained NSL-KDD CNN-LSTM weights (from Phase 1 / models/ dir)
  2. Freeze the CNN feature extraction blocks (layers 0-6)
  3. Replace the final classification head with a SWaT-compatible head
  4. Fine-tune with SWaT data using a lower learning rate
  5. Evaluate on temporal held-out SWaT test set

Architecture adjustments for SWaT:
  - Input reshaping: SWaT has 51 features vs NSL-KDD's 41
  - Binary output: Normal vs Attack (same as NSL-KDD binary)
  - Lower learning rate (1e-4) for fine-tuning frozen layers

References:
  - VAE/AE anomaly detection on SWaT: https://github.com/Andrei3223/VAE_anomaly_detection
  - Seq2Seq on SWaT: https://github.com/kugnojmik/swat-seq2seq
  - SWaT preprocessing: https://github.com/cbhua/swat-preprocess
"""

import os
from typing import Any, Dict, Optional, Tuple

import numpy as np

try:
    import tensorflow as tf

    TF_AVAILABLE = True
except ImportError:
    TF_AVAILABLE = False

try:
    from sklearn.metrics import (
        classification_report,
        confusion_matrix,
        f1_score,
        roc_auc_score,
    )

    SKLEARN_METRICS = True
except ImportError:
    SKLEARN_METRICS = False


class SWaTTransferLearner:
    """Adapts a pre-trained NSL-KDD CNN-LSTM model for SWaT anomaly detection.

    This implements Phase 2 transfer learning from the NSL-KDD baseline trained
    in Phase 1 (notebooks/03_cnn_lstm_baseline.ipynb) to the SWaT industrial
    water treatment dataset.

    The SWaT dataset uses the same binary classification schema (Normal=0,
    Attack=1) but differs in:
    - Number of features (51 vs 41)
    - Temporal nature (1-second resolution sensor/actuator readings)
    - Physical domain (industrial ICS vs IT network traffic)

    Example::

        learner = SWaTTransferLearner(
            n_swat_features=51,
            window_size=10,
            pretrained_model_path="models/best_cnn_lstm.h5",
        )
        model = learner.build_transfer_model()
        history = learner.fine_tune(model, X_train_w, y_train_w, X_val_w, y_val_w)
        results = learner.evaluate(model, X_test_w, y_test_w)
    """

    def __init__(
        self,
        n_swat_features: int = 51,
        window_size: int = 10,
        pretrained_model_path: Optional[str] = None,
        freeze_cnn_blocks: bool = True,
        learning_rate: float = 1e-4,
        filters: int = 64,
        lstm_units: int = 256,
        dropout_rate: float = 0.3,
    ) -> None:
        """Initialises the transfer learner.

        Args:
            n_swat_features: Number of SWaT feature columns (default 51).
            window_size: Sliding window size for sequence input.
            pretrained_model_path: Path to saved NSL-KDD .h5 weights. If None,
                builds from scratch (no transfer).
            freeze_cnn_blocks: If True, freeze CNN layers and only train LSTM
                and new head. If False, fine-tune entire network.
            learning_rate: Learning rate for the Adam fine-tuning optimizer.
            filters: Conv1D filter count (must match pre-trained architecture).
            lstm_units: LSTM hidden unit count.
            dropout_rate: Dropout rate for regularisation.
        """
        if not TF_AVAILABLE:
            raise ImportError(
                "TensorFlow is required for SWaTTransferLearner. "
                "Install with: pip install tensorflow==2.15.0"
            )

        self.n_swat_features = n_swat_features
        self.window_size = window_size
        self.pretrained_model_path = pretrained_model_path
        self.freeze_cnn_blocks = freeze_cnn_blocks
        self.learning_rate = learning_rate
        self.filters = filters
        self.lstm_units = lstm_units
        self.dropout_rate = dropout_rate
        self.input_shape: Tuple[int, int] = (window_size, n_swat_features)

    def build_transfer_model(self) -> "tf.keras.Model":
        """Builds the SWaT transfer model.

        If a pre-trained model path is provided, attempts to load the NSL-KDD
        model and adapt it. Otherwise builds a fresh CNN-LSTM for SWaT.

        Returns:
            A compiled tf.keras.Model ready for fine-tuning.
        """
        if self.pretrained_model_path and os.path.exists(self.pretrained_model_path):
            return self._build_from_pretrained()
        else:
            if self.pretrained_model_path:
                print(
                    f"[SWaTTransfer] Pre-trained model not found at "
                    f"'{self.pretrained_model_path}'. Building fresh SWaT model."
                )
            return self._build_fresh_swat_model()

    def _build_fresh_swat_model(self) -> "tf.keras.Model":
        """Builds a new CNN-LSTM tuned for the SWaT time-series structure."""
        inputs = tf.keras.Input(shape=self.input_shape, name="swat_input")

        # Conv Block 1 - local feature extraction
        x = tf.keras.layers.Conv1D(
            self.filters, kernel_size=3, activation="relu", padding="same",
            name="conv1"
        )(inputs)
        x = tf.keras.layers.BatchNormalization(name="bn1")(x)
        x = tf.keras.layers.MaxPooling1D(pool_size=2, padding="same", name="pool1")(x)
        x = tf.keras.layers.Dropout(self.dropout_rate, name="drop1")(x)

        # Conv Block 2 - higher-level patterns
        x = tf.keras.layers.Conv1D(
            self.filters * 2, kernel_size=3, activation="relu", padding="same",
            name="conv2"
        )(x)
        x = tf.keras.layers.BatchNormalization(name="bn2")(x)
        x = tf.keras.layers.MaxPooling1D(pool_size=2, padding="same", name="pool2")(x)
        x = tf.keras.layers.Dropout(self.dropout_rate, name="drop2")(x)

        # LSTM - temporal dependencies between sensor readings
        x = tf.keras.layers.LSTM(
            self.lstm_units, return_sequences=False, name="lstm_swat"
        )(x)
        x = tf.keras.layers.Dropout(self.dropout_rate, name="drop_lstm")(x)

        # SWaT classification head
        x = tf.keras.layers.Dense(128, activation="relu", name="dense_head")(x)
        x = tf.keras.layers.Dropout(0.2, name="drop_head")(x)
        outputs = tf.keras.layers.Dense(1, activation="sigmoid", name="swat_output")(x)

        model = tf.keras.Model(inputs=inputs, outputs=outputs, name="CNN_LSTM_SWaT")
        model.compile(
            optimizer=tf.keras.optimizers.Adam(learning_rate=self.learning_rate),
            loss="binary_crossentropy",
            metrics=["accuracy"],
        )
        return model

    def _build_from_pretrained(self) -> "tf.keras.Model":
        """Loads NSL-KDD weights and adapts to SWaT input dimensions.

        Strategy: Since NSL-KDD has 41 features and SWaT has 51, we cannot
        directly reuse input weights. Instead we:
          1. Load the saved model to extract Conv/LSTM architecture
          2. Build a new SWaT-input model with the same layer structure
          3. Copy Conv and LSTM weights where dimensions allow
          4. Freeze the CNN blocks and train only the new head
        """
        print(f"[SWaTTransfer] Loading pre-trained weights from: {self.pretrained_model_path}")
        try:
            nslkdd_model = tf.keras.models.load_model(self.pretrained_model_path)
        except Exception as exc:
            print(f"[SWaTTransfer] Failed to load pretrained model: {exc}. Building fresh.")
            return self._build_fresh_swat_model()

        # Build the new SWaT-compatible model
        swat_model = self._build_fresh_swat_model()

        # Copy compatible weights (Conv1D, BatchNorm layers - these learn
        # channel patterns that generalise across domains)
        weight_transfer_count = 0
        for layer in nslkdd_model.layers:
            try:
                swat_layer = swat_model.get_layer(layer.name)
                swat_weights = swat_layer.get_weights()
                src_weights = layer.get_weights()
                if swat_weights and src_weights:
                    # Only copy if shapes match exactly (Conv/BN layers often do)
                    if all(s.shape == t.shape for s, t in zip(src_weights, swat_weights)):
                        swat_layer.set_weights(src_weights)
                        weight_transfer_count += 1
            except ValueError:
                # Layer not found in SWaT model (e.g., input, output layers)
                pass

        print(f"[SWaTTransfer] Transferred weights from {weight_transfer_count} layers.")

        # Freeze CNN blocks if requested
        if self.freeze_cnn_blocks:
            frozen = 0
            for layer in swat_model.layers:
                if layer.name in ("conv1", "bn1", "pool1", "conv2", "bn2", "pool2"):
                    layer.trainable = False
                    frozen += 1
            print(f"[SWaTTransfer] Frozen {frozen} CNN layers. Training LSTM + head only.")

        swat_model.compile(
            optimizer=tf.keras.optimizers.Adam(learning_rate=self.learning_rate),
            loss="binary_crossentropy",
            metrics=["accuracy"],
        )
        return swat_model

    def fine_tune(
        self,
        model: "tf.keras.Model",
        X_train: np.ndarray,
        y_train: np.ndarray,
        X_val: np.ndarray,
        y_val: np.ndarray,
        epochs: int = 50,
        batch_size: int = 256,
        patience: int = 10,
        save_path: Optional[str] = None,
        class_weight_override: Optional[Dict[int, float]] = None,
    ) -> "tf.keras.callbacks.History":
        """Fine-tunes the model on SWaT data.

        Uses a larger batch size (256) suitable for SWaT's large dataset size,
        and class weighting to handle the imbalance (SWaT ~12% attacks).

        Args:
            model: Built transfer model (from build_transfer_model()).
            X_train: Training windows, shape (n, window_size, n_features).
            y_train: Training labels, shape (n,).
            X_val: Validation windows.
            y_val: Validation labels.
            epochs: Max training epochs.
            batch_size: Batch size (256 works well for SWaT).
            patience: Early stopping patience.
            save_path: If provided, saves best model checkpoint here.
            class_weight_override: Custom class weight dict. If None, computes
                automatically from y_train distribution.

        Returns:
            Keras training history.
        """
        # Compute class weights for the severe imbalance in SWaT
        if class_weight_override:
            class_weights = class_weight_override
        else:
            n_normal = int(np.sum(y_train == 0))
            n_attack = int(np.sum(y_train == 1))
            total = len(y_train)
            class_weights = {
                0: total / (2.0 * max(n_normal, 1)),
                1: total / (2.0 * max(n_attack, 1)),
            }
            print(
                f"[SWaTTransfer] Class weights: Normal={class_weights[0]:.3f}, "
                f"Attack={class_weights[1]:.3f} "
                f"(from {n_normal:,}/{n_attack:,} distribution)"
            )

        callbacks = [
            tf.keras.callbacks.EarlyStopping(
                monitor="val_loss",
                patience=patience,
                restore_best_weights=True,
                verbose=1,
            ),
            tf.keras.callbacks.ReduceLROnPlateau(
                monitor="val_loss",
                factor=0.5,
                patience=5,
                min_lr=1e-6,
                verbose=1,
            ),
        ]

        if save_path:
            os.makedirs(os.path.dirname(os.path.abspath(save_path)), exist_ok=True)
            callbacks.append(
                tf.keras.callbacks.ModelCheckpoint(
                    filepath=save_path,
                    monitor="val_loss",
                    save_best_only=True,
                    verbose=1,
                )
            )

        # Reshape labels for binary sigmoid output
        y_train_bin = y_train.reshape(-1, 1)
        y_val_bin = y_val.reshape(-1, 1)

        history = model.fit(
            X_train,
            y_train_bin,
            validation_data=(X_val, y_val_bin),
            epochs=epochs,
            batch_size=batch_size,
            callbacks=callbacks,
            class_weight=class_weights,
            verbose=1,
        )
        return history

    def evaluate(
        self,
        model: "tf.keras.Model",
        X_test: np.ndarray,
        y_test: np.ndarray,
        threshold: float = 0.5,
    ) -> Dict[str, Any]:
        """Evaluates model on the SWaT test set and returns a metrics dict.

        Args:
            model: Trained SWaT model.
            X_test: Test windows, shape (n, window_size, n_features).
            y_test: True binary labels.
            threshold: Decision threshold for binary classification.

        Returns:
            Dict with accuracy, precision, recall, f1, auc_roc, confusion matrix.
        """
        import time

        t0 = time.perf_counter()
        y_prob = model.predict(X_test, batch_size=512, verbose=0).flatten()
        latency_ms = (time.perf_counter() - t0) / len(X_test) * 1000

        y_pred = (y_prob >= threshold).astype(int)

        results: Dict[str, Any] = {
            "n_samples": len(y_test),
            "threshold": threshold,
            "latency_per_sample_ms": round(latency_ms, 4),
        }

        if SKLEARN_METRICS:
            results["f1_binary"] = round(float(f1_score(y_test, y_pred, zero_division=0)), 4)
            results["f1_macro"] = round(float(f1_score(y_test, y_pred, average="macro", zero_division=0)), 4)
            results["auc_roc"] = round(float(roc_auc_score(y_test, y_prob)), 4)
            results["confusion_matrix"] = confusion_matrix(y_test, y_pred).tolist()
            report = classification_report(
                y_test, y_pred, target_names=["Normal", "Attack"],
                output_dict=True, zero_division=0,
            )
            results["per_class"] = report
            results["accuracy"] = round(float(report["accuracy"]), 4)
            results["precision_attack"] = round(float(report["Attack"]["precision"]), 4)
            results["recall_attack"] = round(float(report["Attack"]["recall"]), 4)

        print(
            f"\n[SWaTTransfer] Evaluation Results:\n"
            f"  Accuracy:     {results.get('accuracy', 'N/A')}\n"
            f"  F1 (Attack):  {results.get('f1_binary', 'N/A')}\n"
            f"  F1 (Macro):   {results.get('f1_macro', 'N/A')}\n"
            f"  AUC-ROC:      {results.get('auc_roc', 'N/A')}\n"
            f"  Latency/sample: {results['latency_per_sample_ms']:.4f}ms\n"
        )
        return results

    def find_optimal_threshold(
        self,
        model: "tf.keras.Model",
        X_val: np.ndarray,
        y_val: np.ndarray,
        thresholds: Optional[np.ndarray] = None,
    ) -> Tuple[float, float]:
        """Finds the decision threshold maximising F1 score on a validation set.

        SWaT's high class imbalance means 0.5 is often suboptimal. This grid
        searches thresholds to maximise the binary F1 score for Attack detection.

        Args:
            model: Trained model.
            X_val: Validation windows.
            y_val: True validation labels.
            thresholds: Array of thresholds to test. Defaults to [0.1 … 0.9].

        Returns:
            (best_threshold, best_f1) tuple.
        """
        if thresholds is None:
            thresholds = np.arange(0.1, 0.95, 0.05)

        y_prob = model.predict(X_val, batch_size=512, verbose=0).flatten()

        best_f1 = 0.0
        best_thresh = 0.5
        for thresh in thresholds:
            y_pred = (y_prob >= thresh).astype(int)
            if SKLEARN_METRICS:
                f1 = float(f1_score(y_val, y_pred, zero_division=0))
            else:
                tp = int(np.sum((y_pred == 1) & (y_val == 1)))
                fp = int(np.sum((y_pred == 1) & (y_val == 0)))
                fn = int(np.sum((y_pred == 0) & (y_val == 1)))
                prec = tp / max(tp + fp, 1)
                rec = tp / max(tp + fn, 1)
                f1 = 2 * prec * rec / max(prec + rec, 1e-8)

            if f1 > best_f1:
                best_f1 = f1
                best_thresh = float(thresh)

        print(f"[SWaTTransfer] Optimal threshold: {best_thresh:.2f} -> F1={best_f1:.4f}")
        return best_thresh, best_f1
