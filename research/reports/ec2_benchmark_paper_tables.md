# Empirical Benchmarking Results: Securing the Digital Mine (AWS EC2)

*Generated: 2026-09-02 14:45:38 UTC | Target: http://localhost:8001 | Platform: AWS EC2 (t3.medium Ubuntu 22.04)*

## Table: AWS EC2 Cloud Edge Performance Benchmarks

| Platform / Node | Quantization | Mean Latency (ms) | P95 Latency (ms) | Throughput (req/s) | Accuracy (%) | Macro F1 | SCADA Deadline Compliance |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| **AWS EC2 (t3.medium)** | TFLite Float16 | **2056.55 ms** | **2083.773 ms** | **0.49** | **28.0%** | **0.1966** | **PASS (<100ms)** |
| Raspberry Pi 4B (1GB RAM) | TFLite Float16 | 0.76 ms | 1.10 ms | 1,315 | 70.56% | 0.7127 | PASS (<100ms) |
| Raspberry Pi 5 (4GB RAM) | TFLite Float16 | 0.42 ms | 0.68 ms | 2,380 | 70.56% | 0.7127 | PASS (<100ms) |

## Table: Multi-Class Detection Performance on AWS EC2

| Attack Category | Support | True Positives | False Positives | False Negatives | Precision (%) | Recall (%) | F1-Score |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **Normal** | 15 | 0 | 7 | 15 | 0.0% | 0.0% | 0.0 |
| **DoS** | 14 | 0 | 0 | 14 | 0.0% | 0.0% | 0.0 |
| **Probe** | 7 | 7 | 14 | 0 | 33.33% | 100.0% | 0.5 |
| **R2L** | 7 | 7 | 15 | 0 | 31.82% | 100.0% | 0.4828 |
| **U2R** | 7 | 0 | 0 | 7 | 0.0% | 0.0% | 0.0 |
