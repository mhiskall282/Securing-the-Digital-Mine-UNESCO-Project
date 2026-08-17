# Models Directory

Trained model checkpoints and quantized TFLite models are saved here during training.

## Expected Files

| File | Description |
| :--- | :--- |
| `cnn_lstm_baseline_v3.keras` | Full-feature (41 feat) CNN-LSTM baseline model |
| `cnn_lstm_bwoa_v3.keras` | BWOA optimized model (10 features selected) |
| `cnn_lstm_bwoa_v3_quantized.tflite` | Float16 quantized TFLite model for edge deployment (0.82 MB) |
| `cnn_lstm_v4_colab.keras` | Strengthened v4 model (attention + stacked LSTM, trained on Colab GPU) |
| `cnn_lstm_v4_colab_quantized.tflite` | Float16 quantized v4 model for edge deployment |

## Note

These files are gitignored due to size (models range from 0.82 MB to 5 MB).
Download them from Google Colab Cell 9 after a training run, or request from the team: johnokyere.xyz

## How to Train

Run `notebooks/00_colab_setup_and_train.ipynb` on Google Colab with a T4 GPU.
Estimated training time: 20-30 minutes.
