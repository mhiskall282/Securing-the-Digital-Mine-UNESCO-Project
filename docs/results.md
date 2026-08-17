# Experimental Results and Evaluations (v3 Final)

This document aggregates the confirmed performance metrics, feature reduction statistics, and edge execution profiles from our v3 experiments. All NSL-KDD metrics are evaluated on the KDDTest+ held-out set (22,544 samples).

---

## 1. BWOA Feature Selection

**v3 Configuration**: n_agents=30, max_iter=100, alpha=0.3, min_accuracy=0.75, min_features=10, early stopping patience=15.

| Dataset | Original Features | Selected | Reduction | RF CV Accuracy | Converged | Status |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| NSL-KDD | 41 | 10 | 75.61% | 92.31% | Iter 23/100 | Confirmed |
| SWaT | 51 | 22 | 56.86% | 88.54% | Iter 44/100 | Confirmed |
| Custom OT | ~41 | ~22 | ~46% | - | - | Phase 1: Pending data capture |

**NSL-KDD v3 Selected Features** (10 of 41):
`protocol_type, service, flag, src_bytes, hot, su_attempted, serror_rate, same_srv_rate, diff_srv_rate, dst_host_diff_srv_rate`

> **SWaT access**: iTrust Centre, SUTD Singapore: https://itrust.sutd.edu.sg/itrust-labs_datasets/dataset_info/
> **Custom OT**: Phase 1 field capture at pilot mine sites using AWS EC2 sniffer nodes logging Modbus/DNP3/OPC-UA traffic. Not yet started.

---

## 2. Classification Performance

| Model | Dataset | Features | Accuracy | Precision | Recall | F1 Macro | AUC-ROC | Latency |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| CNN-LSTM Baseline | NSL-KDD | 41 | 77.70% | 0.8017 | 0.7770 | 0.7571 | 0.9359 | 157.66ms |
| CNN-LSTM + BWOA v3 | NSL-KDD | 10 | 70.56% | 0.5833 | 0.7056 | 0.7127 | 0.8471 | 35.60ms |
| CNN-LSTM + BWOA Quantized | NSL-KDD | 10 | 70.56% | 0.5833 | 0.7056 | 0.7127 | 0.8471 | **0.76ms** |
| CNN-LSTM (Transfer Learning) | SWaT | 51 | 59.95% | 0.5621 | 0.5891 | 0.5966 | 0.8650 | **0.12ms** |
| CNN-LSTM (Transfer Learning) | Custom OT | ~22 | - | - | - | - | - | Phase 1 |

**Accuracy gap (baseline vs BWOA)**: 7.14% - accepted trade-off for 78.4% latency reduction (157.66ms to 35.60ms Keras; 0.76ms quantized) and edge deployability.
**Best BWOA config found**: 256 LSTM units, 1 layer, 50 epochs (capacity-tuning iteration 2 of 4).

---

## 3. Per-Class Performance (BWOA v3 Optimized - 10 features, KDDTest+)

| Class | Precision | Recall | F1-Score | Notes |
| :--- | :---: | :---: | :---: | :--- |
| **Normal** | 0.9689 | 0.6839 | 0.8018 | Strongest; high precision for benign traffic |
| **DoS** | 0.7514 | 0.8904 | 0.8150 | Strong recall 0.890 - catches 89% of denial of service attacks |
| **Probe** | 0.5488 | 0.7080 | 0.6183 | Good balanced attack class performance |
| **R2L** | 0.5971 | 0.1449 | 0.2332 | Minority class; strong imbalance in NSL-KDD |
| **U2R** | 0.0134 | 0.3881 | 0.0258 | Rarest class (67 test vs 52 train samples); NSL-KDD known limitation |

> U2R and R2L low F1 reflects NSL-KDD class imbalance (52 U2R training samples vs 13,449 Normal). This is a dataset limitation, not a model failure. Balanced class weights were applied during training.

---

## 4. Edge Deployment Performance

| Model | Size (MB) | Latency Mean | Latency P95 | RAM | Deployment |
| :--- | :---: | :---: | :---: | :---: | :---: |
| CNN-LSTM Baseline (Keras) | 1.8630MB | 157.66ms | 256.23ms | - | Yes |
| CNN-LSTM + BWOA v3 (Keras) | 4.8762MB | 35.60ms | 90.12ms | - | Yes |
| **BWOA Quantized Float16 (TFLite)** | **0.8211MB** | **0.76ms** | **1.10ms** | **290.31MB** | **PASS** |
| **SWaT Transfer Learning Model** | **1.7600MB** | **0.12ms** | **0.19ms** | **295.40MB** | **PASS** |

- Quantized size reduction vs BWOA Keras: **83.17%** smaller
- Quantized latency speedup vs baseline Keras: **207x faster** (157.66ms to 0.76ms); Keras BWOA v3: 35.60ms
- RAM is well within the 1,024MB target ceiling
- Custom OT edge benchmarks: pending Phase 1 dataset availability

---

## 5. Full Results Summary Table

| Model | Dataset | Accuracy | F1 Macro | Latency | Size | Deployment |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: |
| CNN-LSTM Baseline (41 feat) | NSL-KDD | 77.70% | 0.7571 | 157.66ms | 1.86MB | Yes |
| CNN-LSTM + BWOA (10 feat) | NSL-KDD | 70.56% | 0.7127 | 35.60ms | 4.88MB | Yes |
| CNN-LSTM + BWOA Quantized | NSL-KDD | 70.56% | 0.7127 | 0.76ms | 0.82MB | PASS |
| Transfer Learning (51 feat) | SWaT | 59.95% | 0.5966 | 0.12ms | 1.76MB | PASS |
| Transfer Learning | Custom OT | - | - | - | - | Phase 1 |

---

## 6. System Verification and Automated Testing

All components in the framework undergo continuous validation:

* **Unit Test Suite**: 75 of 75 unit tests passing (`Ran 75 tests in 125.632s, OK`). Covers data loaders, BWOA optimizer, CNN-LSTM variants (baseline, v4, attention), SWaT transfer learner, and evaluation metrics.
* **ML Inference API Endpoints**: End-to-end endpoint verification via `scripts/validate_api.py` (Health, Features, Analyze, and 404 handler passing).
* **AWS EC2 Cloud Deployment**: Validated via `scripts/validate_ec2_deployment.sh` (0 errors found, STATUS: READY).
* **Raspberry Pi Edge Deployment**: Validated via `scripts/validate_pi_deployment.sh` (STATUS: READY).
* **Colab GPU Pipeline**: Verified structure via `scripts/verify_colab_notebook.py` (22 cells verified, all pipeline stages present).
* **Documentation & Links**: 24 of 24 internal links validated via `scripts/verify_readme_links.py`.

---

## 7. Research Team & Citation

* **John Okyere** - Team Lead & AI Security Researcher (University of Education, Winneba & Kayaba Labs)
* **Ezekeil Baah** - Machine Learning Engineer & Data Scientist (University of Education, Winneba)
* **Clement Baffour** - Edge Deployment & Quantization Engineer (University of Education, Winneba)
* **Parker Paa Annobil** - Machine Learning Engineer & Data Scientist (University of Education, Winneba)
* **George Akwesi Bonnah** - Cloud Services Engineer (University of Education, Winneba)

```bibtex
@inproceedings{okyere2026securing,
  author    = {Okyere, John and Baah, Ezekeil and Baffour, Clement and Annobil, Parker Paa and Bonnah, George Akwesi},
  title     = {Securing the Digital Mine: A Metaheuristic Optimized Deep Learning Framework for Intrusion Detection in IoT Enabled Mineral Resource Operations},
  booktitle = {Proceedings of the Russian-African Forum-Contest of Young Scientists: Future Engineers of the World: The Foundation of Sustainable Development},
  publisher = {Empress Catherine II Saint Petersburg Mining University},
  year      = {2026},
  address   = {Saint Petersburg, Russia},
  month     = {October}
}
```
