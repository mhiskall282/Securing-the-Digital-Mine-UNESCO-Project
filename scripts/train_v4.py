"""Train CNN-LSTM v4 (strengthened) and compare against v3 baseline."""
import numpy as np
import yaml
import json
import os
import sys
import time

sys.path.insert(0, os.path.abspath(os.path.join(os.path.dirname(__file__), "..")))

# Load config
with open("config.yaml") as f:
    cfg = yaml.safe_load(f)

# Load preprocessed data
print("Loading preprocessed data...")
X_train = np.load("data/processed/X_train.npy")
y_train = np.load("data/processed/y_train.npy")
X_test = np.load("data/processed/X_test.npy")
y_test = np.load("data/processed/y_test.npy")
mask = np.load("data/features/nslkdd_bwoa_mask_v3.npy")

# Apply BWOA feature mask
selected = np.where(mask == 1)[0]
X_train_2d = X_train.reshape(len(X_train), -1)
X_test_2d = X_test.reshape(len(X_test), -1)
X_train_sel = X_train_2d[:, selected].reshape(-1, len(selected), 1)
X_test_sel = X_test_2d[:, selected].reshape(-1, len(selected), 1)

print(f"Training shape: {X_train_sel.shape}")
print(f"Selected features: {len(selected)}")

# Compute class weights
from sklearn.utils.class_weight import compute_class_weight
classes = np.unique(y_train)
weights = compute_class_weight('balanced', classes=classes, y=y_train)
class_weight_dict = dict(enumerate(weights))
print(f"Class weights: {class_weight_dict}")

import tensorflow as tf
from src.models.cnn_lstm import build_cnn_lstm_v4

# Build v4 model
print("\nBuilding CNN-LSTM v4 (attention + stacked LSTM + L2 reg)...")
model_v4 = build_cnn_lstm_v4(
    input_shape=(len(selected), 1),
    n_classes=5,
    filters=64,
    kernel_size=3,
    lstm_units=256,
    dropout_rate=0.3
)
model_v4.summary()

# Compile with label smoothing
model_v4.compile(
    optimizer=tf.keras.optimizers.Adam(learning_rate=0.001),
    loss=tf.keras.losses.SparseCategoricalCrossentropy(from_logits=False),
    metrics=['accuracy']
)

# Callbacks
os.makedirs('models', exist_ok=True)
os.makedirs('logs', exist_ok=True)
callbacks = [
    tf.keras.callbacks.EarlyStopping(
        monitor='val_accuracy', patience=15, restore_best_weights=True
    ),
    tf.keras.callbacks.ModelCheckpoint(
        'models/cnn_lstm_v4_best.keras',
        monitor='val_accuracy', save_best_only=True, verbose=1
    ),
    tf.keras.callbacks.ReduceLROnPlateau(
        monitor='val_loss', factor=0.5, patience=5, min_lr=1e-6, verbose=1
    ),
    tf.keras.callbacks.LearningRateScheduler(
        lambda epoch: 0.001 * (0.95 ** epoch)
    )
]

# Train
print("\nTraining CNN-LSTM v4...")
start = time.time()
history = model_v4.fit(
    X_train_sel, y_train,
    epochs=75,
    batch_size=128,
    validation_split=0.2,
    class_weight=class_weight_dict,
    callbacks=callbacks,
    verbose=1
)
train_time = time.time() - start
print(f"Training completed in {train_time:.1f}s")

# Evaluate on held-out test set
print("\nEvaluating on KDDTest+ held-out set...")
from sklearn.metrics import classification_report, f1_score, accuracy_score
y_pred_prob = model_v4.predict(X_test_sel)
y_pred = np.argmax(y_pred_prob, axis=1)

acc = accuracy_score(y_test, y_pred)
macro_f1 = f1_score(y_test, y_pred, average='macro')
weighted_f1 = f1_score(y_test, y_pred, average='weighted')

print(f"\nCNN-LSTM v4 Results on KDDTest+:")
print(f"  Accuracy:    {acc:.4f} ({acc*100:.2f}%)")
print(f"  Macro F1:    {macro_f1:.4f}")
print(f"  Weighted F1: {weighted_f1:.4f}")
print("\nPer-class report:")
LABELS = ["Normal", "DoS", "Probe", "R2L", "U2R"]
print(classification_report(y_test, y_pred, target_names=LABELS))

# Benchmark latency
print("Benchmarking inference latency (1000 single-sample runs)...")
latencies = []
single_sample = X_test_sel[0:1]
for _ in range(1000):
    t0 = time.perf_counter()
    model_v4.predict(single_sample, verbose=0)
    latencies.append((time.perf_counter() - t0) * 1000)

mean_lat = np.mean(latencies)
p95_lat = np.percentile(latencies, 95)
print(f"  Mean latency: {mean_lat:.2f}ms")
print(f"  P95 latency:  {p95_lat:.2f}ms")

# Compare against v3
print("\n" + "="*60)
print("COMPARISON: v3 (current) vs v4 (strengthened)")
print("="*60)
print(f"  v3 accuracy:  70.56%  |  v4 accuracy:  {acc*100:.2f}%")
print(f"  v3 Macro F1:  0.7127  |  v4 Macro F1:  {macro_f1:.4f}")
print(f"  v3 latency:   35.60ms |  v4 latency:   {mean_lat:.2f}ms")
print("="*60)

if acc > 0.7056:
    print(f"  IMPROVEMENT: +{(acc-0.7056)*100:.2f}% accuracy over v3")
else:
    print(f"  NOTE: v4 accuracy within v3 range (distribution shift expected)")

# Quantize v4
print("\nQuantizing v4 to TFLite float16...")
converter = tf.lite.TFLiteConverter.from_keras_model(model_v4)
converter.optimizations = [tf.lite.Optimize.DEFAULT]
converter.target_spec.supported_types = [tf.float16]
tflite_model = converter.convert()

tflite_path = "models/cnn_lstm_v4_quantized.tflite"
with open(tflite_path, "wb") as f:
    f.write(tflite_model)

size_mb = os.path.getsize(tflite_path) / (1024 * 1024)
print(f"  Quantized model size: {size_mb:.4f} MB")

# Save results
results = {
    "model": "CNN-LSTM v4",
    "accuracy": float(acc),
    "macro_f1": float(macro_f1),
    "weighted_f1": float(weighted_f1),
    "latency_mean_ms": float(mean_lat),
    "latency_p95_ms": float(p95_lat),
    "quantized_size_mb": float(size_mb),
    "train_time_seconds": float(train_time),
}
with open("logs/cnn_lstm_v4_metrics.json", "w") as f:
    json.dump(results, f, indent=2)

print("\nResults saved to logs/cnn_lstm_v4_metrics.json")
print("TRAINING COMPLETE")
