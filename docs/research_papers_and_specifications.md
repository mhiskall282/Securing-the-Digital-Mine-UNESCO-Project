# Academic Research Papers & Engineering Specifications Index

This directory documents the formal scholarly and engineering deliverables produced for the research project:
**"Securing the Digital Mine: A Metaheuristic-Optimized Deep Learning Framework for Intrusion Detection in IoT-Enabled Mineral Resource Operations"**
*Presented at the Russian-African Forum of Young Scientists (UNESCO Project) - Track 3: Smart Subsoil.*

---

## 📚 Complete Document Deliverables

| Deliverable | File Path | Format & Extent | Standards & Description |
| :--- | :--- | :--- | :--- |
| **Full Research Paper** | [`research/full_research_paper.docx`](../research/full_research_paper.docx) | Word DOCX (~11,000 words, 35 pages) | Full 5-chapter Design Science Research manuscript: 12pt Times New Roman, 1.5 line spacing, XML table borders, APA 7th citations, formal mathematical equations, and Appendices A-H. |
| **Technical Report** | [`research/technical_report.docx`](../research/technical_report.docx) | Word DOCX | Comprehensive architecture deep-dive, step-by-step Raspberry Pi and AWS EC2 deployment runbooks, and Appendices A-E. |
| **Product Requirements Document (PRD)** | [`research/PRD.docx`](../research/PRD.docx) | Word DOCX | Product vision, target user personas (SCADA engineer, SOC analyst, mine manager), functional (FR-01 to FR-08) and non-functional requirements (NFR-01 to NFR-07), and release roadmap. |
| **Software Requirements Specification (SRS)** | [`research/SRS.docx`](../research/SRS.docx) | Word DOCX (IEEE 830-1998) | Formal IEEE 830 specification covering external interfaces, system features, and automated verification test gates (unit, integration, dry-run, UAT). |
| **Poster Presentation (Word Document)** | [`research/poster_presentation.docx`](../research/poster_presentation.docx) | Large Format DOCX | Large-format structured poster layout in `.docx` with styled card containers, high-res diagrams, and readable 2-meter typography. |
| **Poster Presentation (PDF)** | [`research/poster_presentation.pdf`](../research/poster_presentation.pdf) | High-Res PDF (300 DPI) | Print-ready A0 portrait poster for conference display. |
| **Editable Poster (PPTX)** | [`research/poster_presentation.pptx`](../research/poster_presentation.pptx) | Editable PowerPoint | Fully customizable A0 portrait presentation slides. |
| **Presentation Slide Deck** | [`research/DigitalMine_Presentation (1).pdf`](../research/DigitalMine_Presentation%20(1).pdf) | Conference Slide Deck (PDF) | Official slide deck for the UNESCO Russian-African Forum 2026. |
| **Formal Abstract** | [`research/Abstract_DigitalMine_Final (2).pdf`](../research/Abstract_DigitalMine_Final%20(2).pdf) | Conference Abstract (PDF) | Official abstract approved for the forum proceedings. |

---

## 🏛️ Design Science Research (DSR) Chapter Alignment

The research paper strictly adheres to the 5-chapter Design Science Research guidelines (`research/Design Science projects.pdf`):

```
+-----------------------------------------------------------------------------------------------+
|                        DESIGN SCIENCE RESEARCH (DSR) LIFECYCLE MAPPING                         |
+------------------------------------+----------------------------------------------------------+
| DSR Lifecycle Phase                | Report Chapter & Artifact Coverage                       |
+------------------------------------+----------------------------------------------------------+
| 1. Problem Identification          | Chapter 1: Introduction (Mining 4.0, OT Air-Gap Loss)    |
| 2. Define Objectives of a Solution | Chapter 1: Introduction (Sub-1ms Latency, Edge Fit)      |
| 3. Knowledge Base / Lit Review     | Chapter 2: Literature Review (Existing IDS, BWOA, ICS DL)|
| 4. Design & Architecture           | Chapter 3: Methodology (4-Layer Arch, ER, UML, Wireframes)|
| 5. Development & Implementation    | Chapter 4: Development (Python/TFLite, Float16, CLI)     |
| 6. Demonstration                   | Chapter 4: Demonstration (Live Sniffer Operator Workflow)|
| 7. Empirical Evaluation            | Chapter 4: Evaluation (NSL-KDD, SWaT, Pi 4B Latency, UAT)|
| 8. Communication                   | Chapter 5 & Forum (UNESCO Russian-African Proceedings)   |
+------------------------------------+----------------------------------------------------------+
```

---

## 📐 Mathematical Formulations Rendered

### 1. Shrinking Encircling Mechanism
$$\vec{D} = |\vec{C} \odot \vec{X}^*(t) - \vec{X}(t)| \quad (1)$$
$$\vec{X}_{cont}(t+1) = \vec{X}^*(t) - \vec{A} \odot \vec{D} \quad (2)$$
where $\vec{A} = 2a\vec{r}_1 - a$, $\vec{C} = 2\vec{r}_2$, and $a$ linearly decreases from 2 to 0 over iterations.

### 2. Spiral Bubble-Net Foraging
$$\vec{X}_{cont}(t+1) = \vec{D}' \cdot \exp(bl) \cdot \cos(2\pi l) + \vec{X}^*(t) \quad (3)$$
where $\vec{D}' = |\vec{X}^*(t) - \vec{X}(t)|$, $b=1.0$, and $l \sim \mathcal{U}(-1, 1)$.

### 3. V-Shaped Binary Transfer Function
$$V(x_d) = \left| \frac{x_d}{\sqrt{1 + x_d^2}} \right| \quad (4)$$
$$X_d(t+1) = \begin{cases} 1 - X_d(t) & \text{if } \text{rand}() < V(x_d) \\ X_d(t) & \text{otherwise} \end{cases} \quad (5)$$

### 4. Constrained Multi-Objective Fitness Function with Accuracy Floor
$$\text{Fitness}(\vec{X}) = \alpha \cdot (1 - \text{Accuracy}(\vec{X})) + (1 - \alpha) \cdot \left( \frac{|\vec{X}|}{D} \right) + \text{Penalty}(\vec{X}) \quad (6)$$
where $\alpha = 0.3$ (70% weight to error minimization), $|\vec{X}|$ is the selected feature count ($D=41$), and $\text{Penalty}(\vec{X}) = 1.0$ if $\text{Accuracy}(\vec{X}) < 0.75$ or $|\vec{X}| < 10$.

---

## 📊 Complete Figures and Tables Reference

### High-Resolution Diagrams (`research/figures/`)
1. [`research/figures/dsr_framework.png`](../research/figures/dsr_framework.png) - Six-Stage Design Science Research Process Framework.
2. [`research/figures/system_architecture.png`](../research/figures/system_architecture.png) - Four-Layer End-to-End System Architecture.
3. [`research/figures/mining_scada_flowchart.png`](../research/figures/mining_scada_flowchart.png) - Cyber-Physical Mineral Processing SCADA Circuit & Edge Defense Boundary.
4. [`research/figures/er_diagram.png`](../research/figures/er_diagram.png) - Relational Entity-Relationship (ER) Database Schema.
5. [`research/figures/uml_use_case.png`](../research/figures/uml_use_case.png) - UML Use Case Model.
6. [`research/figures/uml_class_diagram.png`](../research/figures/uml_class_diagram.png) - UML Class Diagram.
7. [`research/figures/uml_activity_diagram.png`](../research/figures/uml_activity_diagram.png) - UML Activity Diagram with Swimlanes.
8. [`research/figures/uml_sequence_diagram.png`](../research/figures/uml_sequence_diagram.png) - UML Sequence Diagram.
9. [`research/figures/dashboard_wireframe.png`](../research/figures/dashboard_wireframe.png) - Real-Time Multi-Tenant SCADA Monitoring Console Wireframe.
10. [`research/figures/bwoa_convergence.png`](../research/figures/bwoa_convergence.png) - BWOA Optimization Convergence History.
11. [`research/figures/feature_importance.png`](../research/figures/feature_importance.png) - Gini Importance Ranking of 10 Selected Features.
12. [`research/figures/cnn_lstm_architecture.png`](../research/figures/cnn_lstm_architecture.png) - Spatial-Temporal CNN-LSTM Neural Network Flowchart.
13. [`research/figures/training_curves.png`](../research/figures/training_curves.png) - Training Loss and Accuracy Convergence Curves.
14. [`research/figures/confusion_matrix.png`](../research/figures/confusion_matrix.png) - Confusion Matrix on Held-Out NSL-KDD Test Set (22,544 samples).
15. [`research/figures/roc_auc_curves.png`](../research/figures/roc_auc_curves.png) - Multi-Class ROC Curves across All 5 Attack Classes.
16. [`research/figures/latency_comparison_barchart.png`](../research/figures/latency_comparison_barchart.png) - Single-Sample Inference Latency vs SCADA Limit (<100ms).

### CSV Benchmark Tables (`research/tables/`)
* `research/tables/table1_baseline_vs_bwoa.csv` - Baseline vs BWOA CNN-LSTM Performance.
* `research/tables/table2_per_class_metrics.csv` - Per-Class Precision, Recall, and F1 Metrics.
* `research/tables/table3_bwoa_selected_features.csv` - Detailed Breakdown of the 10 Selected Features.
* `research/tables/table4_edge_hardware_latency.csv` - Latency, RAM, and Power on Raspberry Pi 4B, Pi 5, and AWS EC2.
* `research/tables/table5_transfer_learning_swat.csv` - SWaT Transfer Learning Benchmark.
* `research/tables/table6_economic_roi_breakdown.csv` - Financial Risk Mitigation & Downtime ROI.
* `research/tables/table7_user_acceptance_testing.csv` - Quantitative Likert UAT Evaluation Scores.
