# Empirical Benchmarking Results: Securing the Digital Mine (AWS EC2)

*Generated: 2026-09-02 15:18:55 UTC | Target: http://127.0.0.1:8001 | Platform: AWS EC2 (t3.medium Ubuntu 22.04)*

## Table: AWS EC2 Cloud Edge Performance Benchmarks

| Platform / Node | Quantization | Mean Latency (ms) | P95 Latency (ms) | Throughput (req/s) | Accuracy (%) | Macro F1 | SCADA Deadline Compliance |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| **AWS EC2 (t3.medium)** | TFLite Float16 | **1.574 ms** | **1.712 ms** | **617.13** | **0.0%** | **0.0** | **PASS (<100ms)** |
| Raspberry Pi 4B (1GB RAM) | TFLite Float16 | 0.76 ms | 1.10 ms | 1,315 | 70.56% | 0.7127 | PASS (<100ms) |
| Raspberry Pi 5 (4GB RAM) | TFLite Float16 | 0.42 ms | 0.68 ms | 2,380 | 70.56% | 0.7127 | PASS (<100ms) |

## Table: Multi-Class Detection Performance on AWS EC2

| Attack Category | Support | True Positives | False Positives | False Negatives | Precision (%) | Recall (%) | F1-Score |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **Normal** | 0 | 0 | 0 | 0 | 0.0% | 0.0% | 0.0 |
| **DoS** | 0 | 0 | 0 | 0 | 0.0% | 0.0% | 0.0 |
| **Probe** | 0 | 0 | 0 | 0 | 0.0% | 0.0% | 0.0 |
| **R2L** | 0 | 0 | 0 | 0 | 0.0% | 0.0% | 0.0 |
| **U2R** | 0 | 0 | 0 | 0 | 0.0% | 0.0% | 0.0 |
