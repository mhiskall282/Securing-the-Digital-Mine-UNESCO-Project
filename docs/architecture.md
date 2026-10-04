# System Architecture and Methodology: Securing the Digital Mine
## An Explainable, Metaheuristic-Optimized Deep Learning Framework for Intrusion Detection in IoT-Enabled Mineral Resource Operations

**Nomination**: Track 3, "Smart Subsoil": Digital Transformation and Automation in the Mineral Resources Complex  
**Forum**: Russian-African Forum of Young Scientists: "Future Engineers of the World - The Foundation of Sustainable Development", Empress Catherine II Saint Petersburg Mining University, under the auspices of the UNESCO International Centre of Competence in Mining Engineering Education.  

---

## Executive Summary & Scientific Proposal

### Problem Statement
African mining operations are digitalizing quickly through IoT sensor networks, SCADA systems, and cloud-connected digital twins (e.g., Gold Fields' Tarkwa mine in Ghana), yet cybersecurity for these operational technology (OT) environments lags behind conventional IT (Alanazi, Mahmood, & Chowdhury, 2022). Industrial control protocols (Modbus RTU/TCP, DNP3, OPC-UA) lack authentication, encryption, or sequence verification. Compromising these networks allows adversaries to manipulate PLCs, halt milling production ($25,000 to $50,000/hr downtime), or trigger catastrophic kinetic disasters such as tailings dam overflows or ventilation failure in underground shafts (African Mining Market, 2024; IT-Online, 2026).

### Main Scientific Hypothesis
A Binary Whale Optimization Algorithm (BWOA) combined with a spatial-temporal CNN-LSTM deep learning detector, validated on generic network traffic, can be systematically retrained and adapted to the structural characteristics of mining IoT and SCADA traffic (deterministic polling intervals, industrial protocol fields, and narrow attack classes; Kheddar et al., 2023). Attaching a decoupled SHAP (SHapley Additive exPlanations) layer provides non-specialist control room operators with transparent, plain-language root-cause reasons for each flagged alert without delaying line-rate packet evaluation (Oyedotun, Oise, & Ozobialu, 2025).

---

## 1. 4-Tier Edge Defense Architecture

The framework is organized into four decoupled tiers, ensuring zero inline network latency and offline survivability:

```mermaid
flowchart LR
    subgraph Tier1["Tier 1: Line Ingestion"]
        direction TB
        SPAN["📡 <b>SCADA Mirror Port</b><br/>Zero In-Line Latency"]
        SNIFF["📥 <b>Libpcap Daemon</b><br/>Promiscuous Packet Capture"]
        SPAN --> SNIFF
    end

    subgraph Tier2["Tier 2: BWOA Optimization"]
        direction TB
        RAW["📊 <b>Raw Vector</b><br/>41 Telemetry Dimensions"]
        BWOA["⚡ <b>BWOA Pruner</b><br/>10 High-Value Features"]
        MASK["🎯 <b>75.61% Reduction</b><br/>Executed in <0.05 ms"]
        RAW --> BWOA --> MASK
    end

    subgraph Tier3["Tier 3: Spatial-Temporal Engine"]
        direction TB
        CONV["🔬 <b>1D CNN Layer</b><br/>Spatial Packet Correlation"]
        LSTM["⏱️ <b>LSTM Units (64)</b><br/>Temporal Sequence Memory"]
        TFLITE["🧠 <b>Float16 TFLite</b><br/>0.76 ms / 0.82 MB Size"]
        CONV --> LSTM --> TFLITE
    end

    subgraph Tier4["Tier 4: Supervisory & SHAP"]
        direction TB
        GATE{"Alert Gate"}
        PASS["✅ <b>Normal Baseline</b><br/>Logged (<0.8 ms)"]
        SHAP["🔍 <b>Decoupled SHAP</b><br/>Async Explainability"]
        UI["🖥️ <b>SCADA Console</b><br/>Plain-Language Alerts"]
        GATE -- "Normal" --> PASS
        GATE -- "Intrusion" --> SHAP --> UI
    end

    SNIFF ==> RAW
    MASK ==> CONV
    TFLITE ==> GATE

    classDef t1 fill:#1e293b,stroke:#38bdf8,stroke-width:2px,color:#f8fafc;
    classDef t2 fill:#1e293b,stroke:#f59e0b,stroke-width:2px,color:#f8fafc;
    classDef t3 fill:#1e293b,stroke:#10b981,stroke-width:2px,color:#f8fafc;
    classDef t4 fill:#1e293b,stroke:#ef4444,stroke-width:2px,color:#f8fafc;

    class SPAN,SNIFF t1;
    class RAW,BWOA,MASK t2;
    class CONV,LSTM,TFLITE t3;
    class GATE,PASS,SHAP,UI t4;
```

---

## 2. Decoupled Explainable AI (SHAP) Lifecycle

In mineral extraction operations, deep learning predictions must be explainable so that shift supervisors can verify alerts before shutting down heavy machinery. To respect the sub-100 ms SCADA control loop deadline, SHAP is executed asynchronously on flagged events:

```mermaid
sequenceDiagram
    autonumber
    participant Switch as Industrial Switch SPAN Port
    participant Sniffer as Libpcap Sniffer Daemon
    participant BWOA as BWOA Feature Pruner
    participant Engine as TFLite Float16 Engine
    participant SHAP as Decoupled SHAP Engine
    participant Operator as Mine SCADA Operator Console

    Switch->>Sniffer: Line-rate SCADA frames (Modbus, DNP3, OPC-UA)
    Sniffer->>BWOA: 41 Raw Connection Fields
    Note over BWOA: Prune 31 Redundant Dimensions<br/>Extract 10 High-Importance Features
    BWOA->>Engine: 10-Feature Vector
    Note over Engine: Spatial Conv1D + Temporal LSTM<br/>Latency: 0.76 ms (Sub-100ms PASS)
    Engine-->>Sniffer: Probability Distribution & Classification

    alt Benign Operational Flow
        Engine->>Operator: Status: Normal (Zero Line-Rate Overhead)
    else Intrusive Anomaly Flagged (e.g. DoS / Modbus Function Abuse)
        Engine->>SHAP: Trigger Async Attribution (Event-Driven)
        Note over SHAP: Calculate Shapley Values for 10 Features<br/>Rank Top Root-Cause Indicators
        SHAP->>Operator: Plain-Language Diagnostic Alert<br/>"DoS Attack: flag=S0 (+0.31), serror_rate=1.0 (+0.31)"
        Operator->>Operator: Verify Telemetry and Authorize Subnet Isolation
    end
```

### Empirical SHAP Benchmarking & Architectural Parameters
*(Joint study with IBA Karachi; Uddin & Iradat 2026, AI4DEMONS'26)*

1. **Exact Attribution Computation**: Evaluated via KernelSHAP with full exact coalition enumeration ($2^{10} = 1,024$ subsets) over 50 k-means background centroids from KDDTrain+.
2. **Latency Justification**: Exact KernelSHAP requires **1,687.8 ms** per explanation on a laptop CPU. Inline execution would stall packet forwarding. Decoupled asynchronous invocation preserves the **0.76 ms** TFLite inference latency for 96.89% of benign network flows.
3. **Class Driver Mapping**:
   - **DoS**: Driven by connection-state flags (`flag=S0`, SHAP +0.308) and SYN error surges (`serror_rate=1.0`, SHAP +0.311), raising DoS probability from baseline 0.34 to 0.99.
   - **Probe**: Driven by `dst_host_diff_srv_rate` (SHAP 0.285) and `diff_srv_rate` (SHAP 0.241), capturing horizontal register scans.
   - **R2L / U2R**: Driven by `service` (SHAP 0.342) and `hot` indicators (SHAP 0.385), detecting unauthorized command injection.
4. **Exposed Microservice Endpoints**:
   - `GET /api/shap/summary`: Returns empirical attribution metrics and BWOA vs TreeSHAP comparisons.
### 2.1 Comparative Analysis & Empirical Defense: BWOA vs. SHAP Feature Selection

A central defense proof point for academic jury review is explaining why BWOA was chosen for feature selection over SHAP feature importance or filter methods:

| Feature Selection Strategy | Method Category | CNN-LSTM v4 Acc (%) | CNN-LSTM v4 Macro F1 | Random Forest Acc (%) | Normal F1 | DoS F1 | Primary Role in System |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :--- |
| **All-41 Features** | Uncompressed Baseline | 73.80% | 0.5380 | 75.50% | 0.8120 | 0.8250 | Baseline upper bound |
| **BWOA-10 (Deployed)** | Swarm Metaheuristic | 71.51% | 0.5113 | **76.20%** | **0.8522** | 0.8101 | **Global offline feature reducer (0.82 MB edge model)** |
| **SHAP-10 (IBA Study)** | Game-Theoretic Attrib | **73.50%** | **0.5408** | 74.11% | 0.7765 | **0.8179** | **Local post-hoc anomaly explainer (1.68s out-of-band)** |
| **MI-10** | Mutual Information Filter | 72.10% | 0.5100 | 74.90% | 0.7804 | 0.8654 | Univariate filter control |
| **Random-10 (Mean)** | Stochastic Control | 68.20% | 0.4560 | 71.49% | 0.7712 | 0.7974 | Random baseline |

#### Visual Proof Points & Architectural Artifacts:
* **Feature Selection Paradigm Comparison**: [`figures/fig_feature_selection_comparison.png`](../figures/fig_feature_selection_comparison.png) - Multi-panel evaluation demonstrating 67.9% Jaccard concordance on core features (`protocol_type`, `service`, `src_bytes`, `dst_host_diff_srv_rate`).
* **KernelSHAP Multi-Class Heatmap**: [`figures/fig_shap_class_attributions.png`](../figures/fig_shap_class_attributions.png) - Quantifies exact per-feature contributions across all 5 classes.
* **Operator Diagnostic Waterfall**: [`figures/fig_shap_waterfall_defense.png`](../figures/fig_shap_waterfall_defense.png) - Illustrates how an alert moves from prior $E[f(x)]=0.360$ to $f(x)=0.992$ via $+0.311$ `serror_rate` and $+0.308$ `flag=S0`.
* **Hardware Latency vs SCADA Budget**: [`figures/fig_latency_breakdown_scada.png`](../figures/fig_latency_breakdown_scada.png) - Visualizes all 4 hardware tiers strictly satisfying the 100 ms control loop threshold.

---

## 3. Cyber-Physical Mine Topology & Defense Boundary

The diagram below maps the physical mineral extraction circuit to the operational technology network and edge intrusion detection boundary:

```mermaid
flowchart LR
    subgraph MiningCircuit["Level 0: Mine Plant Circuit"]
        direction TB
        CRUSH["🪨 <b>Primary Crusher</b><br/>Coarse Ore Breakage"]
        MILL["⚙️ <b>SAG Grinding Mill</b><br/>15 MW Dual Drive Motor"]
        FLOAT["🧪 <b>Flotation Tanks</b><br/>Reagents & Cyanide Leaching"]
        TSF["💧 <b>Tailings Dam (TSF)</b><br/>Piezometers & Hydrostatic"]
        VENT["💨 <b>Ventilation Fans</b><br/>Mine Shaft Airflow VOD"]
    end

    subgraph ControlLevel["Level 1: Industrial Automation"]
        direction TB
        PLC1["Crusher PLC"]
        PLC2["SAG Mill Cooling PLC"]
        PLC3["Reagent Dosing PLC"]
        PLC4["Tailings RTU"]
        PLC5["Ventilation PLC"]
        CRUSH <--> PLC1
        MILL <--> PLC2
        FLOAT <--> PLC3
        TSF <--> PLC4
        VENT <--> PLC5
    end

    subgraph NetworkLevel["Level 2: OT Network"]
        direction TB
        SW["🔀 <b>Industrial Switch</b><br/>Modbus, DNP3, OPC-UA"]
        SPAN["📡 <b>Mirror / SPAN Port</b><br/>Line-Rate Promiscuous Feed"]
        PLC1 & PLC2 & PLC3 & PLC4 & PLC5 <--> SW
        SW --> SPAN
    end

    subgraph EdgeDefense["Edge IDS Defense (Pi 4B)"]
        direction TB
        PI["🧠 <b>TFLite Float16 IDS</b><br/>0.76 ms / Sub-100ms PASS"]
        DB["💾 <b>SQLite FIFO Buffer</b><br/>Offline Resilient Storage"]
        SPAN ==> PI
        PI <--> DB
    end

    subgraph Level3["Level 3: Mine SOC / Control Center"]
        direction TB
        DASH["🖥️ <b>Livewire Threat Screen</b><br/>Real-Time Telemetry Feed"]
        SHAP_VIEW["🔍 <b>Plain-Language SHAP</b><br/>Root-Cause Diagnostic Alert"]
        PI ==> DASH --> SHAP_VIEW
    end

    classDef c0 fill:#1e293b,stroke:#38bdf8,stroke-width:2px,color:#f8fafc;
    classDef c1 fill:#1e293b,stroke:#f59e0b,stroke-width:2px,color:#f8fafc;
    classDef c2 fill:#1e293b,stroke:#6366f1,stroke-width:2px,color:#f8fafc;
    classDef c3 fill:#1e293b,stroke:#10b981,stroke-width:2px,color:#f8fafc;
    classDef c4 fill:#1e293b,stroke:#ef4444,stroke-width:2px,color:#f8fafc;

    class CRUSH,MILL,FLOAT,TSF,VENT c0;
    class PLC1,PLC2,PLC3,PLC4,PLC5 c1;
    class SW,SPAN c2;
    class PI,DB c3;
    class DASH,SHAP_VIEW c4;
```

---

## 4. Three-Phase Implementation Roadmap

The research roadmap transitions the framework from academic benchmarks into live operational environments across Africa:

```mermaid
flowchart LR
    subgraph Phase1["Phase 1: Data Partnership & OT Capture"]
        direction TB
        P1A["Active Concessions (e.g., Gold Fields Tarkwa, Ghana)"]
        P1B["2-Instance AWS EC2 + CICFlowMeter Pipeline"]
        P1C["Capture Modbus, DNP3, OPC-UA & Sensor Streams"]
        P1D["Labeled African Mining OT Benchmark Corpus"]
        P1A --> P1B --> P1C --> P1D
    end

    subgraph Phase2["Phase 2: Model Adaptation & Explainability"]
        direction TB
        P2A["Retrain Constrained BWOA Feature Selection"]
        P2B["Fine-tune Spatial-Temporal CNN-LSTM on OT Data"]
        P2C["Cross-Validation on SWaT & BATADAL SCADA"]
        P2D["Calibrate SHAP Operator Diagnostic Explanations"]
        P2A --> P2B --> P2C --> P2D
    end

    subgraph Phase3["Phase 3: Deployment Readiness & Localization"]
        direction TB
        P3A["Sub-100 ms Profiling on 1GB RAM Raspberry Pi"]
        P3B["Float16 Model Quantization (0.82 MB Footprint)"]
        P3C["Offline Survivability & Local SQLite Buffering"]
        P3D["Indigenous Training: Local Mine Cybersecurity Staff"]
        P3A --> P3B --> P3C --> P3D
    end

    Phase1 --> Phase2 --> Phase3
```

### Phase 1: Data Partnership & Ingestion
Capture real-world OT traffic at pilot mining concessions (e.g., Gold Fields Tarkwa mine, Ghana) and academic testbeds. Deploy a dual-instance AWS EC2 and CICFlowMeter setup to generate labeled benign and attack traffic for industrial SCADA protocols (Modbus RTU/TCP, DNP3, OPC-UA) and IoT sensor telemetry.

### Phase 2: Model Adaptation & Explainability
Retrain constrained BWOA and spatial-temporal CNN-LSTM models directly on domain-specific OT feature sets (Kheddar et al., 2023). Cross-validate on the physical 51-sensor SWaT and BATADAL cyber-physical benchmarks. Attach the SHAP explanation layer to generate plain-language feature attributions for non-specialist operators (Oyedotun et al., 2025).

### Phase 3: Deployment Readiness & Localization
Profile latency and compute envelopes under strict edge constraints, guaranteeing sub-100 ms response times on Raspberry Pi hardware (with decoupled SHAP execution). Train local mine technicians and cybersecurity personnel to build sovereign African technical capacity rather than transferring opaque foreign tools.

---

## 5. Alignment with United Nations Sustainable Development Goals

| SDG | Goal Target | Project Contribution |
| :--- | :--- | :--- |
| **SDG 9** | Industry, Innovation, and Infrastructure | Strengthens cybersecurity resilience and sovereignty for digitalizing African mining infrastructure. |
| **SDG 8** | Decent Work and Economic Growth | Safeguards worker lives and operational continuity by shielding ventilation grids and tailings dam monitors from kinetic cyber sabotage. |
| **SDG 17** | Partnerships for the Goals | Bilateral young-scientist research pathway uniting the University of Education, Winneba (Ghana) and Empress Catherine II Saint Petersburg Mining University (Russia). |
