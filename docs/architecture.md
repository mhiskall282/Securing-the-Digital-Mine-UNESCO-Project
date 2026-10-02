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
flowchart TD
    subgraph Tier1["Tier 1: High-Speed Ingestion Layer"]
        SPAN["SCADA Switch SPAN / Mirror Port"]
        SNIFF["Passive Libpcap Sniffer Daemon (promiscuous mode)"]
        SPAN --> SNIFF
    end

    subgraph Tier2["Tier 2: Metaheuristic Optimization Layer"]
        RAW["Raw Telemetry Vector (41 Dimensions)"]
        BWOA["BWOA Feature Pruner (10 Optimal Features Retained)"]
        MASK["75.61% Dimensionality Reduction (0.05 ms)"]
        SNIFF --> RAW
        RAW --> BWOA
        BWOA --> MASK
    end

    subgraph Tier3["Tier 3: Spatial-Temporal Inference Layer"]
        CONV["1D CNN Layer (64 Filters, k=3, Spatial Packet Correlation)"]
        LSTM["LSTM Layer (64 Units, Temporal Sequence Transitions)"]
        TFLITE["TFLite Float16 Engine (0.82 MB, 0.76 ms Inference)"]
        MASK --> CONV
        CONV --> LSTM
        LSTM --> TFLITE
    end

    subgraph Tier4["Tier 4: Supervisory & Explainability Layer"]
        DECISION{"Alert Gate"}
        TFLITE --> DECISION
        DECISION -- "Normal Flow (96.89% Precision)" --> LOG["Local Baseline Buffer (<0.8 ms)"]
        DECISION -- "Intrusion Flagged" --> SHAP["Decoupled SHAP Engine (Async Thread)"]
        SHAP --> UI["FastAPI + Livewire SCADA Console (Plain-Language Reasons)"]
        UI --> ISO["Automated Subnet Isolation & Technician Alert"]
    end

    classDef t1 fill:#e1f5fe,stroke:#0288d1,stroke-width:1px;
    classDef t2 fill:#fff8e1,stroke:#f57f17,stroke-width:1px;
    classDef t3 fill:#e8f5e9,stroke:#388e3c,stroke-width:1px;
    classDef t4 fill:#fce4ec,stroke:#c2185b,stroke-width:1px;

    class SPAN,SNIFF t1;
    class RAW,BWOA,MASK t2;
    class CONV,LSTM,TFLITE t3;
    class DECISION,LOG,SHAP,UI,ISO t4;
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
        SHAP->>Operator: Plain-Language Diagnostic Alert<br/>"DoS Attack: src_bytes (+0.42), serror_rate (+0.28)"
        Operator->>Operator: Verify Telemetry and Authorize Subnet Isolation
    end
```

---

## 3. Cyber-Physical Mine Topology & Defense Boundary

The diagram below maps the physical mineral extraction circuit to the operational technology network and edge intrusion detection boundary:

```mermaid
flowchart TD
    subgraph MiningCircuit["Level 0: Physical Mineral Extraction Circuit"]
        direction TB
        CRUSH["Primary Jaw Crusher (Coarse Ore Breakage)"]
        MILL["15 MW Semi-Autogenous Grinding (SAG) Mill"]
        FLOAT["Froth Flotation & Cyanide Leaching Tanks"]
        TSF["Tailings Storage Facility (Piezometers & Level Sensors)"]
        VENT["Underground Ventilation-on-Demand Airflow Fans"]
    end

    subgraph ControlLevel["Level 1: Industrial Automation & Control"]
        direction TB
        PLC1["Crusher PLC"]
        PLC2["SAG Mill Bearing & Cooling Loop PLC"]
        PLC3["Flotation Reagent Dosing PLC"]
        PLC4["Tailings Dam Hydrostatic Monitor RTU"]
        PLC5["Shaft Airflow & Gas Scrubber PLC"]
        
        CRUSH <--> PLC1
        MILL <--> PLC2
        FLOAT <--> PLC3
        TSF <--> PLC4
        VENT <--> PLC5
    end

    subgraph NetworkLevel["Level 2: OT Industrial Network"]
        SW["Industrial Ethernet Core Switch (Modbus TCP, DNP3, OPC-UA)"]
        PLC1 <--> SW
        PLC2 <--> SW
        PLC3 <--> SW
        PLC4 <--> SW
        PLC5 <--> SW
    end

    subgraph EdgeDefense["Edge Cybersecurity Boundary (Substation Gateways)"]
        SPAN["Switch Mirror / SPAN Port"]
        PI["Raspberry Pi 4B Edge IDS (0.76 ms Float16 TFLite)"]
        DB["Local SQLite Offline Alert Buffer"]
        SW --> SPAN
        SPAN --> PI
        PI <--> DB
    end

    subgraph Level3["Level 3: Mine Operations Center (SOC / SCADA)"]
        HMI["Central SCADA HMI Screen"]
        DASH["FastAPI + Livewire Threat Dashboard"]
        SHAP_VIEW["Plain-Language SHAP Operator Diagnosis"]
        PI --> DASH
        DASH --> HMI
        DASH --> SHAP_VIEW
    end

    classDef circuit fill:#f0f4c3,stroke:#9e9d24,stroke-width:1px;
    classDef plc fill:#fff9c4,stroke:#fbc02d,stroke-width:1px;
    classDef net fill:#bbdefb,stroke:#1976d2,stroke-width:1px;
    classDef edge fill:#c8e6c9,stroke:#388e3c,stroke-width:1px;
    classDef scada fill:#ffcdd2,stroke:#d32f2f,stroke-width:1px;

    class CRUSH,MILL,FLOAT,TSF,VENT circuit;
    class PLC1,PLC2,PLC3,PLC4,PLC5 plc;
    class SW net;
    class SPAN,PI,DB edge;
    class HMI,DASH,SHAP_VIEW scada;
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
