"""CNN-LSTM hybrid deep learning classifier.

This module provides functions to construct the hybrid deep learning model
comprising 1D Convolutional layers (for spatial feature extraction) and
LSTM layers (for temporal sequence modeling).

Functions
---------
build_cnn_lstm       -- original v3 architecture (preserved for backward compatibility)
build_cnn_lstm_with_attention -- v3 with a self-attention layer after LSTM
build_cnn_lstm_v4    -- strengthened architecture with dual Conv blocks, stacked
                        LSTM, self-attention, L2 regularization, label smoothing,
                        and cosine-annealing learning rate
"""

from typing import Tuple
import tensorflow as tf
from tensorflow.keras.regularizers import l2


def build_cnn_lstm(
    input_shape: Tuple[int, int],
    n_classes: int,
    filters: int = 64,
    kernel_size: int = 3,
    lstm_units: int = 128,
    dropout_rate: float = 0.3,
    unroll: bool = False,
    num_lstm_layers: int = 1,
) -> tf.keras.Model:
    """Builds and compiles the CNN-LSTM hybrid neural network model.

    Args:
        input_shape: A tuple representing input shape (sequence_length, features).
        n_classes: Number of prediction targets. Use 2 for binary classification.
        filters: Number of filters for the Conv1D layers.
        kernel_size: Dimension of kernel window size in Conv1D.
        lstm_units: Size of hidden units in LSTM layers.
        dropout_rate: Ratio of node dropouts for regularization.
        unroll: Whether to unroll the LSTM layer for TFLite conversion.
        num_lstm_layers: Number of stacked LSTM layers.

    Returns:
        A compiled tf.keras.Model instance.
    """
    inputs = tf.keras.Input(shape=input_shape)

    # 1D Convolution for spatial feature extraction
    x = tf.keras.layers.Conv1D(
        filters=filters,
        kernel_size=kernel_size,
        activation="relu",
        padding="same",
    )(inputs)
    x = tf.keras.layers.BatchNormalization()(x)
    x = tf.keras.layers.MaxPooling1D(pool_size=2, padding="same")(x)
    x = tf.keras.layers.Dropout(dropout_rate)(x)

    # Secondary Convolutional block
    x = tf.keras.layers.Conv1D(
        filters=filters * 2,
        kernel_size=kernel_size,
        activation="relu",
        padding="same",
    )(x)
    x = tf.keras.layers.BatchNormalization()(x)
    x = tf.keras.layers.MaxPooling1D(pool_size=2, padding="same")(x)
    x = tf.keras.layers.Dropout(dropout_rate)(x)

    # LSTM for temporal sequence learning (unrolled to support TFLite without Select TF Ops)
    if num_lstm_layers > 1:
        for _ in range(num_lstm_layers - 1):
            x = tf.keras.layers.LSTM(units=lstm_units, return_sequences=True, unroll=unroll)(x)
            x = tf.keras.layers.Dropout(dropout_rate)(x)

    x = tf.keras.layers.LSTM(units=lstm_units, return_sequences=False, unroll=unroll)(x)
    x = tf.keras.layers.Dropout(dropout_rate)(x)

    # Classifier output
    if n_classes == 2 or n_classes == 1:
        # Binary classification
        outputs = tf.keras.layers.Dense(n_classes, activation="sigmoid")(x)
    else:
        # Multi-class classification
        outputs = tf.keras.layers.Dense(n_classes, activation="softmax")(x)

    model = tf.keras.Model(inputs=inputs, outputs=outputs, name="CNN_LSTM_IDS")
    return model


def build_cnn_lstm_with_attention(
    input_shape: Tuple[int, int],
    n_classes: int,
    filters: int = 64,
    kernel_size: int = 3,
    lstm_units: int = 128,
    dropout_rate: float = 0.3,
    unroll: bool = False,
    num_lstm_layers: int = 1,
) -> tf.keras.Model:
    """Builds CNN-LSTM with a self-attention layer after the final LSTM.

    The attention mechanism learns to weight the most informative timesteps
    before the global pooling reduces the sequence to a fixed vector.

    Args:
        input_shape: A tuple (sequence_length, features).
        n_classes: Number of output classes.
        filters: Number of Conv1D filters in the first block.
        kernel_size: Conv1D kernel size.
        lstm_units: LSTM hidden units.
        dropout_rate: Dropout probability.
        unroll: Unroll LSTM for TFLite compatibility.
        num_lstm_layers: Number of stacked LSTM layers.

    Returns:
        A compiled tf.keras.Model instance.
    """
    inputs = tf.keras.Input(shape=input_shape)

    x = tf.keras.layers.Conv1D(filters=filters, kernel_size=kernel_size,
                                activation="relu", padding="same")(inputs)
    x = tf.keras.layers.BatchNormalization()(x)
    x = tf.keras.layers.MaxPooling1D(pool_size=2, padding="same")(x)
    x = tf.keras.layers.Dropout(dropout_rate)(x)

    x = tf.keras.layers.Conv1D(filters=filters * 2, kernel_size=kernel_size,
                                activation="relu", padding="same")(x)
    x = tf.keras.layers.BatchNormalization()(x)
    x = tf.keras.layers.MaxPooling1D(pool_size=2, padding="same")(x)
    x = tf.keras.layers.Dropout(dropout_rate)(x)

    if num_lstm_layers > 1:
        for _ in range(num_lstm_layers - 1):
            x = tf.keras.layers.LSTM(units=lstm_units, return_sequences=True,
                                      unroll=unroll)(x)
            x = tf.keras.layers.Dropout(dropout_rate)(x)

    # Final LSTM must return sequences so attention can weight timesteps
    x_seq = tf.keras.layers.LSTM(units=lstm_units, return_sequences=True,
                                  unroll=unroll)(x)
    x_seq = tf.keras.layers.Dropout(dropout_rate)(x_seq)

    # Self-attention: learn importance weights over the time dimension
    attention_scores = tf.keras.layers.Dense(1, activation="tanh")(x_seq)
    attention_weights = tf.keras.layers.Softmax(axis=1)(attention_scores)
    x = tf.keras.layers.Multiply()([x_seq, attention_weights])
    x = tf.keras.layers.GlobalAveragePooling1D()(x)

    if n_classes <= 2:
        outputs = tf.keras.layers.Dense(n_classes, activation="sigmoid")(x)
    else:
        outputs = tf.keras.layers.Dense(n_classes, activation="softmax")(x)

    model = tf.keras.Model(inputs=inputs, outputs=outputs,
                           name="CNN_LSTM_Attention_IDS")
    return model


def build_cnn_lstm_v4(
    input_shape: Tuple[int, int],
    n_classes: int,
    filters: int = 64,
    kernel_size: int = 3,
    lstm_units: int = 128,
    dropout_rate: float = 0.3,
    unroll: bool = False,
    label_smoothing: float = 0.1,
    l2_strength: float = 1e-4,
    learning_rate: float = 1e-3,
) -> tf.keras.Model:
    """Strengthened CNN-LSTM v4 architecture targeting >80% accuracy on KDDTest+.

    Architecture improvements over v3:
      - Two Conv1D blocks (64 then 128 filters) each with BatchNorm
      - Stacked LSTM: first returns sequences for the attention layer,
        second collapses the sequence dimension
      - Self-attention layer between the two LSTM layers
      - L2 regularization on the Dense output layer
      - Label smoothing in the CategoricalCrossentropy loss
      - Cosine annealing learning rate schedule (set in the caller via callbacks)

    Args:
        input_shape: Tuple (sequence_length, features), e.g. (10, 1).
        n_classes: Number of output classes (5 for NSL-KDD).
        filters: Base filter count for Conv1D block 1 (block 2 uses filters*2).
        kernel_size: Conv1D kernel size.
        lstm_units: Hidden units in both LSTM layers.
        dropout_rate: Dropout probability applied after every major sub-layer.
        unroll: Unroll LSTM for TFLite-compatible export.
        label_smoothing: Label smoothing factor for the loss (default 0.1).
        l2_strength: L2 regularization weight on the Dense output kernel.
        learning_rate: Initial learning rate (used by Adam; pair with cosine schedule).

    Returns:
        A compiled tf.keras.Model ready for training.
    """
    inputs = tf.keras.Input(shape=input_shape)

    # ------------------------------------------------------------------
    # Convolutional block 1: filters=64
    # ------------------------------------------------------------------
    x = tf.keras.layers.Conv1D(filters=filters, kernel_size=kernel_size,
                                activation="relu", padding="same")(inputs)
    x = tf.keras.layers.BatchNormalization()(x)
    x = tf.keras.layers.MaxPooling1D(pool_size=2, padding="same")(x)
    x = tf.keras.layers.Dropout(dropout_rate)(x)

    # ------------------------------------------------------------------
    # Convolutional block 2: filters=128
    # ------------------------------------------------------------------
    x = tf.keras.layers.Conv1D(filters=filters * 2, kernel_size=kernel_size,
                                activation="relu", padding="same")(x)
    x = tf.keras.layers.BatchNormalization()(x)
    x = tf.keras.layers.MaxPooling1D(pool_size=2, padding="same")(x)
    x = tf.keras.layers.Dropout(dropout_rate)(x)

    # ------------------------------------------------------------------
    # LSTM 1: returns sequences for attention
    # ------------------------------------------------------------------
    x_seq = tf.keras.layers.LSTM(units=lstm_units, return_sequences=True,
                                  unroll=unroll)(x)
    x_seq = tf.keras.layers.Dropout(dropout_rate)(x_seq)

    # ------------------------------------------------------------------
    # Self-attention between the two LSTMs
    # ------------------------------------------------------------------
    attention_scores = tf.keras.layers.Dense(1, activation="tanh")(x_seq)
    attention_weights = tf.keras.layers.Softmax(axis=1)(attention_scores)
    x_attended = tf.keras.layers.Multiply()([x_seq, attention_weights])

    # ------------------------------------------------------------------
    # LSTM 2: collapses attended sequence to a fixed vector
    # ------------------------------------------------------------------
    x = tf.keras.layers.LSTM(units=lstm_units, return_sequences=False,
                              unroll=unroll)(x_attended)
    x = tf.keras.layers.Dropout(dropout_rate)(x)

    # ------------------------------------------------------------------
    # Classifier with L2 regularization on the kernel
    # ------------------------------------------------------------------
    if n_classes <= 2:
        outputs = tf.keras.layers.Dense(
            n_classes, activation="sigmoid",
            kernel_regularizer=l2(l2_strength),
        )(x)
        loss = tf.keras.losses.BinaryCrossentropy(label_smoothing=label_smoothing)
        metrics = ["accuracy", tf.keras.metrics.AUC(name="auc")]
    else:
        outputs = tf.keras.layers.Dense(
            n_classes, activation="softmax",
            kernel_regularizer=l2(l2_strength),
        )(x)
        loss = tf.keras.losses.CategoricalCrossentropy(
            label_smoothing=label_smoothing
        )
        metrics = [
            "accuracy",
            tf.keras.metrics.AUC(name="auc"),
            tf.keras.metrics.Precision(name="precision"),
            tf.keras.metrics.Recall(name="recall"),
        ]

    model = tf.keras.Model(inputs=inputs, outputs=outputs, name="CNN_LSTM_v4_IDS")

    optimizer = tf.keras.optimizers.Adam(learning_rate=learning_rate)
    model.compile(optimizer=optimizer, loss=loss, metrics=metrics)
    return model
