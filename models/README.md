# Trained Models & Quantized Edge Weights

This directory contains the trained deep learning checkpoints, metaheuristic-optimized weights, and quantized TensorFlow Lite (TFLite) binaries for the **Securing the Digital Mine** intrusion detection system.

---

## 📦 Model Artifacts Catalog

| Model File | Format | File Size | Input Dim | Test Accuracy | Macro F1 | Edge Latency (Pi 4B) | Description |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :--- |
| [`cnn_lstm_bwoa_v3_quantized.tflite`](cnn_lstm_bwoa_v3_quantized.tflite) | TFLite Float16 | **0.82 MB** | 10 Feat | **70.56%** | **0.7127** | **0.76 ms** | **Production Deployment Binary**: Canonical TFLite model referenced in AWS EC2 & Raspberry Pi systemd services. |
| [`cnn_lstm_quantized_float16_v3.tflite`](cnn_lstm_quantized_float16_v3.tflite) | TFLite Float16 | **0.82 MB** | 10 Feat | **70.56%** | **0.7127** | **0.76 ms** | **Production Edge Model**: BWOA 10-feature Float16 model for Raspberry Pi & ARM gateways. |
| [`cnn_lstm_quantized_float16.tflite`](cnn_lstm_quantized_float16.tflite) | TFLite Float16 | **0.82 MB** | 10 Feat | 70.56% | 0.7127 | 0.76 ms | Primary quantized deployment binary. |
| [`cnn_lstm_quantized_float16_v2.tflite`](cnn_lstm_quantized_float16_v2.tflite) | TFLite Float16 | **0.33 MB** | 10 Feat | 68.90% | 0.6840 | 0.45 ms | Ultra-compact edge variant for low-memory microcontrollers. |
| [`cnn_lstm_bwoa_v3.keras`](cnn_lstm_bwoa_v3.keras) | Keras (HDF5/Zip) | **5.11 MB** | 10 Feat | **70.56%** | **0.7127** | 35.60 ms (Keras) | Full-precision Keras model trained on 10 BWOA features (256 LSTM units, Conv1D 64 filters). |
| [`cnn_lstm_bwoa_optimized.keras`](cnn_lstm_bwoa_optimized.keras) | Keras | **1.96 MB** | 10 Feat | 70.56% | 0.7127 | 35.60 ms | BWOA-selected feature model checkpoint. |
| [`cnn_lstm_bwoa_v2.keras`](cnn_lstm_bwoa_v2.keras) | Keras | **1.95 MB** | 10 Feat | 68.90% | 0.6840 | 34.10 ms | BWOA iteration 2 checkpoint. |
| [`cnn_lstm_baseline_full.keras`](cnn_lstm_baseline_full.keras) | Keras | **2.04 MB** | 41 Feat | 77.70% | 0.7571 | 157.66 ms | Baseline full-feature model (41 NSL-KDD attributes). |
| [`cnn_lstm_baseline_v3.keras`](cnn_lstm_baseline_v3.keras) | Keras | **1.95 MB** | 41 Feat | 77.70% | 0.7571 | 157.66 ms | Baseline comparison checkpoint v3. |
| [`cnn_lstm_nslkdd_baseline.keras`](cnn_lstm_nslkdd_baseline.keras) | Keras | **1.96 MB** | 41 Feat | 77.70% | 0.7571 | 157.66 ms | Initial NSL-KDD benchmark baseline. |
| [`best_cnn_lstm.h5`](best_cnn_lstm.h5) | Keras HDF5 | **1.95 MB** | 10 Feat | 70.56% | 0.7127 | 35.60 ms | Best validation checkpoint weights. |
| [`cnn_lstm_v4_best.keras`](cnn_lstm_v4_best.keras) | Keras | **11.44 MB** | 10 Feat | 72.10% | 0.7250 | 42.10 ms | Multi-head attention extended architecture. |

---

## 🚀 How to Load and Run Inference

### 1. Running the Quantized TFLite Edge Model (Python)

```python
import numpy as np
import tensorflow as tf

# Load the 0.82 MB Float16 quantized model
interpreter = tf.lite.Interpreter(model_path="models/cnn_lstm_quantized_float16_v3.tflite")
interpreter.allocate_tensors()

input_details = interpreter.get_input_details()
output_details = interpreter.get_output_details()

# Example: 10 BWOA-selected features for a single network flow
# Features: [src_bytes, service, flag, serror_rate, same_srv_rate, diff_srv_rate, dst_host_diff_srv_rate, protocol_type, hot, su_attempted]
sample_flow = np.zeros((1, 10, 1), dtype=np.float32)

interpreter.set_tensor(input_details[0]['index'], sample_flow)
interpreter.invoke()

# 5-class prediction: [Normal, DoS, Probe, R2L, U2R]
probabilities = interpreter.get_tensor(output_details[0]['index'])
class_labels = ["Normal", "DoS", "Probe", "R2L", "U2R"]
predicted_class = class_labels[np.argmax(probabilities)]
confidence = float(np.max(probabilities)) * 100

print(f"Prediction: {predicted_class} ({confidence:.2f}% confidence)")
```

### 2. Loading Full Keras Checkpoints

```python
import tensorflow as tf

# Load full precision Keras model
model = tf.keras.models.load_model("models/cnn_lstm_bwoa_v3.keras")
model.summary()
```

---

## 🛠️ Retraining & Quantization Pipeline

To retrain and re-quantize these models from scratch:
1. **Google Colab GPU Pipeline**: Run [`notebooks/00_colab_setup_and_train.ipynb`](../notebooks/00_colab_setup_and_train.ipynb).
2. **Local / Cloud Training**: Run `python scripts/train_v4.py`.
3. **Edge Benchmark**: Run `python src/benchmarks/edge_benchmark.py`.
