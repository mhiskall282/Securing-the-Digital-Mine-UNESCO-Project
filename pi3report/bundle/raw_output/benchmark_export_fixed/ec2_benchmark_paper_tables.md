# Empirical Benchmarking Results: Securing the Digital Mine (AWS EC2)

*Generated: 2026-09-16 13:39:25 UTC | Target: http://localhost:8001 | Platform: AWS EC2 (t3.medium Ubuntu 22.04)*

## Table: AWS EC2 Cloud Edge Performance Benchmarks

| Platform / Node | Quantization | Mean Latency (ms) | P95 Latency (ms) | Throughput (req/s) | Accuracy (%) | Macro F1 | SCADA Deadline Compliance |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| **AWS EC2 (t3.medium)** | TFLite Float16 | **2.677 ms** | **3.47 ms** | **364.44** | **57.0%** | **0.5667** | **PASS (<100ms)** |
| Raspberry Pi 4B (1GB RAM) | TFLite Float16 | 0.76 ms | 1.10 ms | 1,315 | 70.56% | 0.7127 | PASS (<100ms) |
| Raspberry Pi 5 (4GB RAM) | TFLite Float16 | 0.42 ms | 0.68 ms | 2,380 | 70.56% | 0.7127 | PASS (<100ms) |

## Table: Multi-Class Detection Performance on AWS EC2

| Attack Category | Support | True Positives | False Positives | False Negatives | Precision (%) | Recall (%) | F1-Score |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **Normal** | 58 | 29 | 29 | 29 | 50.0% | 50.0% | 0.5 |
| **DoS** | 58 | 29 | 0 | 29 | 100.0% | 50.0% | 0.6667 |
| **Probe** | 28 | 28 | 0 | 0 | 100.0% | 100.0% | 1.0 |
| **R2L** | 28 | 28 | 28 | 0 | 50.0% | 100.0% | 0.6667 |
| **U2R** | 28 | 0 | 29 | 28 | 0.0% | 0.0% | 0.0 |
