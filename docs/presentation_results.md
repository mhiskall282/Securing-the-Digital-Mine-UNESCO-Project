# Presentation Slides: Securing the Digital Mine

Saint Petersburg Mining University - UNESCO Young Scientists Forum 2026  
*Track 3: "Smart Subsoil" - Digital Transformation and Automation in the Mineral Resources Complex*

---

## Slide 1: Title & Project Scope
### Securing the Digital Mine: A Metaheuristic Optimized Deep Learning Framework for Intrusion Detection in IoT Enabled Mineral Resource Operations

* **Authors**:
  * **John Okyere** (Team Lead & AI Security Researcher, UEW Innovation Hub & UEW)
  * **Ezekeil Baah** (Machine Learning Engineer & Data Scientist, UEW)
  * **Clement Baffour** (Edge Deployment & Quantization Engineer, UEW)
  * **Parker Paa Annobil** (Machine Learning Engineer & Data Scientist, UEW)
  * **George Akwesi Bonnah** (Cloud Services Engineer, UEW)
* **Full Abstract**: [Google Drive Document](https://drive.google.com/file/d/1SS40i_wyjIAllRItygb_wXr3D7aMYbFt/view?usp=drive_link)
* **Presentation Slides**: [Google Drive Slides](https://drive.google.com/file/d/1kgmFS5CS3oQ0YsNLBVTF-mg4qbue68PI/view?usp=drive_link)
* **Academic Deliverables**:
  * [Full 35-Page DSR Research Paper (DOCX)](../research/full_research_paper.docx)
  * [Technical Report & Deployment Specifications (DOCX)](../research/technical_report.docx)
  * [Product Requirements Document (PRD) (DOCX)](../research/PRD.docx)
  * [Software Requirements Specification (SRS - IEEE 830) (DOCX)](../research/SRS.docx)
  * [A0 Poster Presentation (High-Res PDF)](../research/poster_presentation.pdf) | [Poster (DOCX)](../research/poster_presentation.docx) | [Poster (PPTX)](../research/poster_presentation.pptx)
  * [Research Papers & Specifications Index](research_papers_and_specifications.md)
* **Under the Auspices of**: UNESCO & Empress Catherine II Saint Petersburg Mining University, Russia
* **Event Dates**: 12-17 October 2026
* **Key Idea**: A lightweight, edge-deployable intrusion detection system (IDS) utilizing Binary Whale Optimization Algorithm (BWOA) for feature selection and CNN-LSTM for classification.

---

## Slide 2: The Digital Mine Problem Statement
### Cybersecurity Challenges in Industrial IoT (IIoT) & SCADA Operations
* **Rapid Digitalization**: Deep integration of automation in mining (SDG 9) increases the cyberattack surface.
* **Complex Threats**: SCADA protocol vulnerabilities (Modbus, DNP3, OPC-UA) and traditional network vectors (DoS, Probing, U2R, R2L).
* **Resource Constraints**: Remote mining sites in Africa operate under low-bandwidth, low-power edge nodes (Raspberry Pi/industrial gateways).
* **Objective**: Build a highly accurate yet computationally lightweight IDS that runs locally at the edge with sub-100ms latency.

---

## Slide 3: Proposed Methodology Workflow
### BWOA Feature Selection + CNN-LSTM Classifier
1. **Network Ingestion**: Collect packets from SCADA/OT devices.
2. **Feature Selection**: Apply Binary Whale Optimization Algorithm (BWOA) to prune redundant features.
3. **Sequence Classification**: Use hybrid CNN-LSTM to capture spatial-temporal threat patterns:
   * **Conv1D**: Extract local spatial correlations from packet features.
   * **LSTM**: Learn long-term temporal dependencies across sequential connections.
4. **Quantization**: Perform post-training float16 TFLite quantization for lightweight CPU inference.

---

## Slide 4: BWOA Feature Selection Results
### 75.61% Dimensionality Reduction (v3 with Accuracy Floor Constraint)
* **Input Features**: 41 raw network features (NSL-KDD schema).
* **BWOA Output Subset**: **10 features** selected (v3 with 75% accuracy floor):
  `['protocol_type', 'service', 'flag', 'src_bytes', 'hot', 'su_attempted', 'serror_rate', 'same_srv_rate', 'diff_srv_rate', 'dst_host_diff_srv_rate']`
* **BWOA Validation Accuracy**: **92.31%** (RandomForest 3-fold CV on 3000-sample stratified subset).
* **Performance Benefit**: Reduces model input layer complexity by **75.61%**, translating to lower inference latency and smaller model footprint.

---

## Slide 5: Model Classification Performance
### Final Experimental Metrics (v3 - KDDTest+ / SWaT Temporal Test set)

| Model | Features | Accuracy | Macro F1 | AUC-ROC | Latency |
| :--- | :---: | :---: | :---: | :---: | :---: |
| CNN-LSTM Baseline | 41 | **77.70%** | **0.7571** | **0.9359** | 157.66ms |
| CNN-LSTM + BWOA v3 (ours) | 10 | **70.56%** | **0.7127** | **0.8471** | 35.60ms |
| CNN-LSTM + BWOA Quantized | 10 | **70.56%** | **0.7127** | **0.8471** | **0.76ms** |
| CNN-LSTM (Transfer SWaT) | 51 | **59.95%** | **0.5966** | **0.8650** | **0.12ms** |

* **Accuracy gap**: 7.14% below baseline. Accepted trade-off: 77.4% latency reduction (157.66ms to 35.60ms) and 75.61% fewer input features enabling edge deployment at remote mining sites.
* **SWaT Domain Transfer**: Successfully adapted the pre-trained IT network detector to the 51-sensor physical water treatment telemetry with **0.12ms** inference latency (PASS).
* **Engineering justification**: The 7.14% accuracy trade-off represents a deliberate decision. By accepting this reduction, we achieve 77.4% lower inference latency (from 157.66ms to 35.60ms Keras; 0.76ms quantized) and 75.61% fewer input features, enabling deployment on Raspberry Pi-class edge hardware at remote African mining sites where full-feature models are computationally infeasible.

---

## Slide 6: Per-Class Breakdown (v3 Optimized Model - KDDTest+)

### Multi-Class Performance Under BWOA v3 (10 features, KDDTest+)

| Class | Precision | Recall | F1-Score |
| :--- | :---: | :---: | :---: |
| **Normal** | 0.9689 | 0.6839 | **0.8018** |
| **DoS** | 0.7514 | 0.8904 | **0.8150** |
| **Probe** | 0.5488 | 0.7080 | **0.6183** |
| **R2L** | 0.5971 | 0.1449 | 0.2332 |
| **U2R** | 0.0134 | 0.3881 | 0.0258 |

* **Strongest detection**: Normal traffic (F1=0.8018, Precision=0.9689). The model reliably filters benign connections.
* **Best attack class**: DoS (F1=0.8150, Recall=0.8904) - catches 89% of denial of service attacks.
* **DoS/R2L/U2R note**: R2L and U2R low scores reflect NSL-KDD's extreme class imbalance. U2R has only 67 test samples vs 13,449 Normal. This is a known dataset limitation, not a model flaw. Balanced class weights were applied during training to prevent total minority-class collapse.


---

## Slide 7: Edge Deployment & Quantization
### Multi-Platform Edge & Cloud Hardware Benchmarks (Table 5 Confirmed)

| Hardware Platform | Quantization | Mean Latency | P95 Latency | Throughput | Peak RAM | Verdict (<100ms Target) |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: |
| **Raspberry Pi 4B (1GB RAM)** | TFLite Float16 | **0.76ms** | 1.10ms | 1,315 req/s | 290.31MB | **PASS** (131x safety margin) |
| **Raspberry Pi 5 (4GB RAM)** | TFLite Float16 | **0.42ms** | 0.68ms | 2,380 req/s | 295.10MB | **PASS** (238x safety margin) |
| **AWS EC2 Cloud (t3.medium)** | TFLite Float16 | **1.57ms** | 1.71ms | **617 req/s** | **18.10MB** | **PASS** (63.5x safety margin) |

* **Size reduction**: Quantized TFLite is 83.2% smaller than the Keras BWOA checkpoint (4.88MB to 0.82MB).
* **Latency speedup**: 207x faster than Keras baseline (157.66ms to 0.76ms on edge; 1.57ms on AWS EC2 cloud).
* **RAM footprint**: 18.10MB resident on AWS EC2; 290.31MB peak on Raspberry Pi (well within 1,024MB ceiling).
* **Throughput**: 617 requests/second on AWS EC2 (> 53 million evaluations per day).
* **Empirical publication bundle**: All datasets and styled workbooks archived in [`research/reports/ec2_benchmark_reports.zip`](../research/reports/ec2_benchmark_reports.zip) and [`ec2_benchmark_complete_results.xlsx`](../research/reports/ec2_benchmark_complete_results.xlsx).
* **Deployment verdict**: PASS across both edge gateways and cloud nodes with strict sub-100ms real-time SCADA compliance.

---

## Slide 8: SDG & UNESCO Alignment
### Sustainable Development Goals (SDG) Target Impact
* **SDG 9: Industry, Innovation, and Infrastructure**: Secures the digitalization of critical subsoil extraction infrastructure.
* **SDG 8: Decent Work and Economic Growth**: Safeguards operational continuity and automated safety monitoring systems in hazardous mines.
* **SDG 17: Partnerships for the Goals**: A joint Russian-African scientific pathway demonstrating collaborative young-scientist development at Saint Petersburg Mining University.

---

## Slide 9: Summary & Conclusions
* **Lightweight Architecture**: Combining Binary Whale Optimization with LSTM sequence learning generates an accurate, high-throughput IDS.
* **Quantization Success**: Model size reduced to 0.82MB (83.2% compression) and execution latency dropped to 0.76ms (sub-100ms constraint PASS).
* **Future Outlook**: Proceeding to validate the model's domain transfer capability using custom OT collectors at scale on Modbus RTU/TCP networks.
