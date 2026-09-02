# Experiment Reproduction Guide

This guide details step-by-step instructions to set up the environment, prepare raw data files, execute the feature selection/training scripts, and replicate our research results.

---

## Step 1: Environment Setup
Ensure Python 3.11 or higher is installed. Build a clean virtual environment and install dependencies:

```bash
# Create a virtual environment
python -m venv venv

# Activate on Windows
venv\Scripts\activate

# Install requirements
pip install -r requirements.txt
```

---

## Step 2: Data Preparation
Obtain the required datasets and place them under raw data directories:

1. **NSL-KDD**:
   The loader automatically attempts downloads. If offline, place `KDDTrain+.txt` and `KDDTest+.txt` into `data/raw/`.
2. **SWaT**:
   Request files from SUTD Singapore and place `swat.csv` into `data/raw/`.
3. **BATADAL**:
   Download challenge files and place `batadal.csv` into `data/raw/`.

---

## Step 3: Running BWOA Feature Selection
To find the optimal feature subsets, run notebook `02_bwoa_feature_selection.ipynb` or execute custom optimization scripts:

```python
from src.data.nsl_kdd import NSLKDDLoader
from src.optimization.bwoa import BinaryWhaleOptimizer
from src.optimization.fitness import FeatureFitnessEvaluator

loader = NSLKDDLoader()
df = loader.load("data/raw/KDDTrain+.txt")
X, y = loader.preprocess(df)

# Use a stratified 3000-sample subset for fitness evaluations
from sklearn.model_selection import train_test_split
_, X_subset, _, y_subset = train_test_split(X, y, test_size=3000, stratify=y, random_state=42)

# v3: alpha=0.3 (70% accuracy weight), min_accuracy=0.75 floor, min_features=10
evaluator = FeatureFitnessEvaluator(alpha=0.3, min_accuracy=0.75, min_features=10)
optimizer = BinaryWhaleOptimizer(
    n_agents=30,
    n_features=X_subset.shape[1],
    max_iter=100,
    fitness_fn=evaluator.calculate_fitness,
    minimum_features=10
)
# patience=15 triggers early stopping when fitness stops improving
best_mask, history = optimizer.optimize(X_subset, y_subset, X_subset, y_subset, patience=15)
```

The resulting feature mask is saved to `data/features/nslkdd_bwoa_mask.npy`.

---

## Step 4: Training the CNN-LSTM Baseline Model
Train the deep learning model on the masked feature subset:

1. Load the binary mask from `data/features/nslkdd_bwoa_mask.npy`.
2. Apply the mask to features and reshape the input to 3D sequences: `(samples, 1, features)`.
3. Call `build_cnn_lstm()` to compile the classifier.
4. Call `ModelTrainer.train()` to run backpropagation.

Outputs (accuracy, loss curves) and checkpoints are written to `figures/` and `models/` folders.

---

## Step 5: Running OT Traffic Domain Adaptation
For transfer learning adaptations to OT mining networks:

1. Capture live or simulated flow logs and map columns via `align_features_to_nslkdd()`.
2. Load the pre-trained baseline classifier model.
3. Freeze the trainable weights of the spatial extractor blocks (Conv1D and BatchNormalization).
4. Train the LSTM sequence layer and Dense output layers on target site flow statistics.

---

## Step 6: Edge Deployment Benchmarking
To profile model memory footprint and inference latency on local targets:

1. Load the saved keras checkpoint.
2. Initialize `EdgeBenchmark` and measure prediction response times.
3. Convert Keras weights to quantized `.tflite` format.
4. Execute `EdgeBenchmark.check_deployment_readiness()` to evaluate edge criteria.

---

## Step 7: Cloud & Edge Empirical Benchmarking & Academic Results Export

To collect empirical performance measurements from the live AWS EC2 or edge deployment and generate publication-ready tables and workbooks for research papers:

```bash
# 1. Run empirical benchmark against live EC2 (or local edge node)
python scripts/benchmark_and_export.py --url http://51.21.219.29 --samples 100

# 2. Results are automatically exported to research/reports/:
#    - ec2_benchmark_complete_results.xlsx   (Multi-sheet Excel workbook with Executive Summary, Latencies, Confusion Matrix)
#    - ec2_benchmark_detailed_inferences.csv  (Row-by-row packet feature and prediction logs)
#    - ec2_benchmark_summary.csv              (Aggregated latency percentiles and throughput)
#    - ec2_benchmark_per_class.csv            (Precision, Recall, and F1 per attack class)
#    - ec2_benchmark_paper_tables.md          (Markdown tables formatted for report drafts)
#    - ec2_benchmark_tables.tex               (LaTeX table snippets for IEEE/Springer manuscripts)

# 3. Download directly from web browser:
#    http://51.21.219.29/api/export/results.xlsx
#    http://51.21.219.29/api/export/results.csv
```
