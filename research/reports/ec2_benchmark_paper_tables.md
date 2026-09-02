# Empirical Benchmarking Results: Securing the Digital Mine (AWS EC2)

*Generated: 2026-09-02 14:51:54 UTC | Target: http://localhost:8001 | Platform: AWS EC2 (t3.medium Ubuntu 22.04)*

## Table: AWS EC2 Cloud Edge Performance Benchmarks

| Platform / Node | Quantization | Mean Latency (ms) | P95 Latency (ms) | Throughput (req/s) | Accuracy (%) | Macro F1 | SCADA Deadline Compliance |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| **AWS EC2 (t3.medium)** | TFLite Float16 | **2063.155 ms** | **2088.638 ms** | **0.48** | **26.67%** | **0.1943** | **PASS (<100ms)** |
| Raspberry Pi 4B (1GB RAM) | TFLite Float16 | 0.76 ms | 1.10 ms | 1,315 | 70.56% | 0.7127 | PASS (<100ms) |
| Raspberry Pi 5 (4GB RAM) | TFLite Float16 | 0.42 ms | 0.68 ms | 2,380 | 70.56% | 0.7127 | PASS (<100ms) |

## Table: Multi-Class Detection Performance on AWS EC2

| Attack Category | Support | True Positives | False Positives | False Negatives | Precision (%) | Recall (%) | F1-Score |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **Normal** | 5 | 0 | 2 | 5 | 0.0% | 0.0% | 0.0 |
| **DoS** | 4 | 0 | 0 | 4 | 0.0% | 0.0% | 0.0 |
| **Probe** | 2 | 2 | 3 | 0 | 40.0% | 100.0% | 0.5714 |
| **R2L** | 2 | 2 | 6 | 0 | 25.0% | 100.0% | 0.4 |
| **U2R** | 2 | 0 | 0 | 2 | 0.0% | 0.0% | 0.0 |
