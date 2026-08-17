# Logs Directory

Experiment logs are written here by `ExperimentLogger` during training runs.

## Expected Files

| File | Description |
| :--- | :--- |
| `baseline_v3_metrics.json` | Full-feature (41 feat) CNN-LSTM baseline classification metrics |
| `bwoa_v3_metrics.json` | BWOA optimized (10 feat) CNN-LSTM classification metrics |
| `edge_benchmark_report_v3.json` | TFLite float16 latency and RAM benchmarks on edge hardware |
| `experiment_summary.md` | Human-readable summary of all v3 experiment results |

## Note

These files are gitignored due to their large binary content and experiment-run specificity.
Download them from Google Colab after a training run, or request from the team: johnokyere.xyz
