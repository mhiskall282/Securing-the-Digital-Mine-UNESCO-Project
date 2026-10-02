# Conference Presentation: Securing the Digital Mine
## An Explainable, Metaheuristic-Optimized Deep Learning Framework for Intrusion Detection in IoT-Enabled Mineral Resource Operations

**Nomination**: Track 3, "Smart Subsoil": Digital Transformation and Automation in the Mineral Resources Complex  
**Venue**: Russian-African Forum of Young Scientists: "Future Engineers of the World - The Foundation of Sustainable Development", Empress Catherine II Saint Petersburg Mining University, under the auspices of the UNESCO International Centre of Competence in Mining Engineering Education  
**Presentation Timing**: 15 Minutes (14 Slides) + 5 Minutes Q&A  
**Presenters**: John Okyere (Lead Author), Ezekeil Baah, Clement Baffour, Parker Paa Annobil, George Akwesi Bonnah  
*University of Education, Winneba (UEW), Ghana | UEW Innovation Hub*

---

## Slide 1: Title & Institutional Context (0:00 - 1:00)

### Slide Visual Content
- **Title**: Securing the Digital Mine: An Explainable, Metaheuristic-Optimized Deep Learning Framework for Intrusion Detection in IoT-Enabled Mineral Resource Operations
- **Track**: Track 3, "Smart Subsoil": Digital Transformation and Automation in the Mineral Resources Complex
- **Authors**: John Okyere, Ezekeil Baah, Clement Baffour, Parker Paa Annobil, George Akwesi Bonnah
- **Affiliation**: Cyber-Physical Systems Research Group, Department of ICT & UEW Innovation Hub, University of Education, Winneba, Ghana
- **Conference Banner**: Russian-African Forum of Young Scientists, Empress Catherine II Saint Petersburg Mining University
- **Core Callout**: *75.6% Feature Pruning | 0.76 ms Edge Inference | SHAP Explainability for Operators | Sub-100 ms SCADA Deadline Compliant*

### Speaker Notes (130 words | ~1 min)
> "Distinguished session chairs, esteemed colleagues, and fellow researchers from across the African continent and the Russian Federation. I am John Okyere, representing the University of Education, Winneba and the UEW Innovation Hub in Ghana.
> 
> Today, our team presents our research: **'Securing the Digital Mine: An Explainable, Metaheuristic-Optimized Deep Learning Framework for Intrusion Detection in IoT-Enabled Mineral Resource Operations.'**
> 
> As African and global mining complexes accelerate digitalization - deploying IoT sensor grids and SCADA networks at operations like Gold Fields' Tarkwa mine - cybersecurity for operational technology lags dangerously behind. In this presentation, we detail a lightweight, explainable architecture combining constrained Binary Whale Optimization, spatial-temporal CNN-LSTM modeling, and a SHAP explanation layer to deliver sub-millisecond, interpretable threat detection on low-cost edge gateways."

---

## Slide 2: The Industrial Mining Threat Landscape (1:00 - 2:00)

### Slide Visual Content
- **Domain Context**: Gold, platinum, nickel, and rare-earth extraction complexes in Ghana, South Africa, and Russia.
- **The Air-Gap Erosion**: Cloud production analytics, remote diagnostics, and vendor VPN tunnels have dismantled physical network isolation.
- **Protocol Vulnerabilities**: Modbus RTU/TCP, DNP3, and OPC-UA lack encryption, authentication, or sequence integrity.
- **Kinetic Cyber Consequences**:
  - *SAG Mills (15 MW motors)*: Overriding cooling loop valves causes catastrophic mechanical seizure ($25k-$50k/hr downtime).
  - *Tailings Storage Facilities (TSF)*: Spoofing piezometer pressure telemetry risks dam overtopping and toxic chemical spills.
  - *Underground Ventilation*: Falsifying airflow telemetry risks fatal gas buildup and asphyxiation.

### Speaker Notes (140 words | ~1 min)
> "In traditional corporate IT, cybersecurity prioritizes confidentiality. In operational mining environments, physical human safety and continuous availability dictate everything.
> 
> Mineral processing circuits operate heavy, continuous-duty machinery: semi-autogenous grinding mills, cyanide leaching tanks, and underground ventilation grids. Historically, these control loops relied on air-gapped isolation. Today, that air gap is gone.
> 
> The protocols governing these facilities - such as Modbus and DNP3 - were designed decades ago with zero security. They transmit telemetry in clear plaintext without cryptographic signatures. An attacker who gains access can inject legitimate-looking function codes that override pump valves or disable tailings dam monitoring. In our industry, cyber attacks do not just corrupt data - they cause kinetic destruction, environmental devastation, and loss of human life."

---

## Slide 3: The SCADA Real-Time Control Loop Dilemma (2:00 - 3:00)

### Slide Visual Content
- **The Timing Crisis**:
  - Industrial PLC Cyclic Scan Loop: **20 ms - 50 ms**
  - Unoptimized Deep Learning Inference (41 features): **157.66 ms** (FAILS DEADLINE)
  - Proposed Quantized BWOA Framework (10 features): **0.76 ms** (PASSES DEADLINE)
- **Four Core System Gaps**:
  1. *Signature Brittleness*: Snort/Suricata fail on zero-days and semantic protocol abuse (<15% recall).
  2. *High Dimensionality*: Enterprise IT benchmarks (41-80+ features) burden low-power microcontrollers.
  3. *Latency Overhead*: 157 ms creates buffer bloat and violates real-time safety bounds.
  4. *African Concession Constraints*: Solar-powered edge nodes, intermittent satellite links, low-cost hardware.

### Speaker Notes (135 words | ~1 min)
> "Why can't we simply deploy an off-the-shelf deep learning model from computer vision or enterprise IT?
> 
> The fundamental bottleneck is the **control loop scan cycle**. In mineral processing, Programmable Logic Controllers scan physical sensor inputs and execute control logic every 20 to 50 milliseconds.
> 
> If a security model requires 157 milliseconds to evaluate a single connection flow - which is typical for unoptimized 41-feature neural networks - it can evaluate fewer than 7 samples per second. It creates network buffer bloat and completely violates the safety deadline.
> 
> Furthermore, remote African mining sites operate under harsh field conditions: solar microgrids and intermittent satellite uplinks. We cannot rely on multi-gigabyte cloud models. We need an autonomous, sub-millisecond, sub-megabyte model that runs directly at the edge."

---

## Slide 4: Research Questions Guiding the DSR Artifact (3:00 - 4:00)

### Slide Visual Content
- **Methodological Framework**: Design Science Research (DSR) Paradigm (Peffers et al., 2007; Hevner et al., 2004)
- **Four Core Research Questions**:
  - **RQ1 (Dimensionality Optimization)**: Can a constrained BWOA with an adaptive alpha decay schedule and hard accuracy floor prune 75%+ of telemetry features while preserving multi-class threat discrimination?
  - **RQ2 (Spatial-Temporal Threat Modeling)**: How effectively does a hybrid Conv1D-LSTM architecture capture packet-level spatial patterns and temporal connection state transitions?
  - **RQ3 (Edge Real-Time Execution and Quantization)**: Can post-training Float16 quantization compress the neural model below 1.0 MB and achieve sub-millisecond (<1.0 ms) inference on 1GB RAM ARM hardware, satisfying the sub-100 ms SCADA deadline?
  - **RQ4 (Empirical Generalization and Economic ROI)**: How robustly does the framework generalize to physical SCADA testbeds (SWaT 51-sensor facility), and what is its financial and worker-safety ROI in mining concessions?

### Speaker Notes (135 words | ~1 min)
> "To guide our artifact design and empirical validation under the Design Science Research methodology, we formulated four explicit research questions.
> 
> First, RQ1 asks whether a constrained Binary Whale Optimization Algorithm can prune high-dimensional industrial telemetry by over 75% without sacrificing multi-class threat discrimination.
> 
> Second, RQ2 evaluates how effectively a hybrid 1D CNN and LSTM architecture can capture localized spatial correlations across packet headers and sequential state transitions across industrial control sessions.
> 
> Third, RQ3 explores whether post-training Float16 quantization can compress the network below 1 megabyte and achieve sub-millisecond inference on low-cost 1GB RAM ARM hardware to respect industrial control loop scan times.
> 
> And fourth, RQ4 investigates empirical transferability to physical cyber-physical testbeds like the 51-sensor SWaT plant, and evaluates the socio-economic return on investment for mining operators."

---

## Slide 5: Architectural Blueprint: 4-Tier Edge Defense with SHAP Explainability (4:00 - 5:15)

### Slide Visual Content
- **Diagram**: 4-Tier End-to-End System Flow
  - **Tier 1 (Ingestion)**: Libpcap edge sniffer (`@mhiskall282/unesco-mine-sec-cli`) capturing bi-directional packets at line speed.
  - **Tier 2 (Optimization)**: BWOA feature pruner dropping 75.6% of fields (41 -> 10 features).
  - **Tier 3 (Inference)**: TFLite Float16 spatial-temporal engine executing in 0.76 ms.
  - **Tier 4 (Supervision & Explainability)**: FastAPI microservice + SHAP explanation layer (Oyedotun et al., 2025) delivering plain-language diagnostics for flagged events to the Livewire SCADA console.
- **Key Metric**: Fully self-contained edge execution with decoupled, event-triggered explainability.

```mermaid
flowchart LR
    T1["Tier 1: Libpcap Sniffer<br/>(Mirror Port Line Rate)"] --> T2["Tier 2: BWOA Pruner<br/>(41 to 10 Features, 0.05ms)"]
    T2 --> T3["Tier 3: CNN-LSTM TFLite<br/>(Float16, 0.76ms Engine)"]
    T3 --> T4["Tier 4: Decoupled SHAP & UI<br/>(Plain-Language Alerts)"]
```

### Speaker Notes (130 words | ~1 min 15 sec)
> "To solve this, we engineered a decoupled four-tier architecture.
> 
> At Tier 1, an asynchronous edge sniffer daemon binds to switch mirror ports, capturing SCADA packets at line speed without introducing in-line latency.
> 
> At Tier 2, our metaheuristic optimization layer immediately prunes incoming telemetry, discarding 31 redundant attributes and extracting exactly 10 high-value features.
> 
> At Tier 3, a lightweight spatial-temporal neural network - compressed via post-training Float16 quantization - evaluates the flow in 0.76 milliseconds on a single ARM core.
> 
> Finally, at Tier 4, localized predictions stream to a control room dashboard. Critically, we attach an asynchronous SHAP explanation layer: every flagged alert provides non-specialist operators with a ranked list of contributing features in plain language, without burdening routine traffic evaluation."

---

## Slide 6: Metaheuristic Feature Pruning: Constrained BWOA (5:15 - 6:30)

### Slide Visual Content
- **Binary Whale Optimization Algorithm (BWOA)**:
  - Models humpback whale bubble-net foraging in discrete binary space $\{0, 1\}^{41}$ (Mirjalili & Lewis, 2016).
  - Shrinking encircling ($|A| < 1$) + logarithmic spiral updating ($p \ge 0.5$) + random exploration ($|A| \ge 1$).
  - Hybrid metaheuristic feature selection validated on industrial and sensor networks (Krishnaveni et al., 2025; Anand & Arul, 2024).
- **V-Shaped Transfer Function**:
  - $\mathcal{V}(v_d) = |v_d / \sqrt{1 + v_d^2}|$ treats positive and negative velocities symmetrically, completely preventing search stagnation.
- **Constrained Multi-Objective Fitness Function**:
  - $\mathcal{F}(\vec{X}) = \alpha(t) \cdot \text{Error}(\vec{X}) + (1 - \alpha(t)) \cdot (|\text{Selected}| / D) + \mathcal{P}(\vec{X})$
  - Adaptive alpha schedule: $\alpha = 0.5 \to 0.3$ over 50 iterations.
  - Hard Accuracy Floor: $\mathcal{P}(\vec{X}) = 1.0$ if $\text{Accuracy} < 75\%$ or $|\text{Selected}| < 10$.
- **Result**: Convergence at iteration 23; exactly 10 features selected (75.61% reduction).

### Speaker Notes (140 words | ~1 min 15 sec)
> "At Tier 2, our feature selection is powered by a constrained Binary Whale Optimization Algorithm.
> 
> Continuous optimization updates coordinates in real numbers. To discretize search into binary bit-masks without boundary saturation, we derived a V-shaped transfer function. Unlike traditional sigmoid functions which stagnate at negative velocities, our V-shaped function treats large positive and negative velocities symmetrically as strong signals to alter the feature state.
> 
> Furthermore, unconstrained metaheuristics often discard rare attack indicators to minimize feature counts. To prevent this, we formulated a multi-objective fitness function with an adaptive alpha decay schedule and a hard accuracy floor. If any candidate mask drops validation accuracy below 75%, it receives a severe penalty of 1.0, disqualifying it immediately.
> 
> Across 30 agents over 100 iterations, the swarm converged in 23 iterations, pruning 41 features down to exactly 10."

---

## Slide 7: Physical Feature Meaning & The SHAP Explanation Layer (6:30 - 7:45)

### Slide Visual Content
- **Top 10 BWOA-Selected Features & Physical Mining Vector**:
  - `src_bytes` (0.2451): Volumetric DoS bursts blinding operator consoles.
  - `service` (0.1982) & `flag` (0.1420): Modbus/DNP3 industrial ports & session anomalies.
  - `serror_rate` (0.1185) & `same_srv_rate` (0.0894): SYN error surges & cyclic register polling abuse.
  - `diff_srv_rate` (0.0652) & `dst_host_diff_srv_rate` (0.0521): Reconnaissance sweeps across PLCs.
  - `protocol_type` (0.0412), `hot` (0.0278), `su_attempted` (0.0205): Insider escalation & unauthorized access.
- **SHAP-Based Explanation Layer (Lundberg & Lee, 2017; Oyedotun et al., 2025)**:
  - Additive feature attribution: $g(z') = \phi_0 + \sum_{i=1}^M \phi_i z_i'$
  - **Decoupled Real-Time Architecture**: SHAP runs strictly on *flagged events*, preserving 0.76 ms line-rate inspection.
  - **Plain-Language Operator Reason**: Non-specialist operators receive plain English diagnostics with every alert (e.g., *'Alert: DoS flood detected. Main triggers: src_bytes (+0.42), serror_rate (+0.28). Recommended: check PLC cooling loop'*).

```mermaid
flowchart LR
    DEC{"Event Decision"}
    OK["✅ <b>Baseline Log</b><br/>Zero Overhead (0.76ms)"]
    SHAP["🔍 <b>Async SHAP Engine</b><br/>Additive Feature Attribution"]
    REASON["🖥️ <b>Plain-Language Reason</b><br/>'DoS: src_bytes (+0.42), serror_rate (+0.28)'"]

    DEC -- "Benign Flow (96.89% Prec)" --> OK
    DEC -- "Intrusion Flagged" --> SHAP --> REASON

    classDef d1 fill:#1e293b,stroke:#f59e0b,stroke-width:2px,color:#f8fafc;
    classDef d2 fill:#1e293b,stroke:#10b981,stroke-width:2px,color:#f8fafc;
    classDef d3 fill:#1e293b,stroke:#ef4444,stroke-width:2px,color:#f8fafc;

    class DEC d1;
    class OK d2;
    class SHAP,REASON d3;
```

### Speaker Notes (140 words | ~1 min 15 sec)
> "What is remarkable about our BWOA optimizer is that it did not select features arbitrarily. Every chosen feature corresponds directly to physical cyber-physical attack vectors in industrial control systems.
> 
> The top feature - `src_bytes`, with a Gini importance of 0.2451 - detects volumetric Denial-of-Service floods attempting to blind operator displays. Connection flags and error rates track industrial protocol states, while access flags detect root privilege escalation.
> 
> Crucially, deep learning models are often rejected by mine operators as untrustworthy 'black boxes'. To solve this, we incorporate a SHAP explanation layer based on Shapley values.
> 
> To protect our sub-100 ms real-time latency target, explanations are generated exclusively for flagged anomaly events, not routine traffic. When an intrusion is intercepted, the operator receives an intuitive, plain-language breakdown of the top contributing features, empowering local technicians to make immediate, confident operational decisions."
> 
> `service`, `flag`, and `serror_rate` track industrial protocol connection states, immediately flagging malformed TCP handshakes and Modbus session resets.
> 
> And critically, the optimizer preserved access flags like `hot` and `su_attempted`. Even though these represent subtle privilege escalations, our hard accuracy floor prevented the algorithm from dropping them, ensuring our model retains defenses against insider threats and root compromise."

---

## Slide 8: Spatial-Temporal Deep Learning & Float16 Quantization (7:45 - 9:00)

### Slide Visual Content
- **Hybrid Conv1D-LSTM Pipeline**:
  - **Conv1D Layer**: 64 filters, kernel size $k = 3$, ReLU activation. Extracts localized cross-feature correlations across packet flows.
  - **LSTM Layer**: 64 recurrent memory units. Six formal gating equations ($f_t, i_t, \tilde{c}_t, c_t, o_t, h_t$) track multi-second protocol state transitions.
  - **Softmax Layer**: Emits 5-class normalized threat probabilities.
- **Post-Training Float16 Quantization**:
  - Converts 32-bit floating point weights into 16-bit IEEE 754 half precision:
    $x_{\text{FP16}} = (-1)^s \cdot 2^{e - 15} \cdot (1 + m / 1024)$
  - Dynamic numerical range ($6.1 \times 10^{-5}$ to $65,504$) prevents gradient underflow or numeric clipping.
  - Compresses model binary from 4.88 MB to **0.82 MB** (83.2% compression).

### Speaker Notes (135 words | ~1 min 15 sec)
> "At Tier 3, our neural architecture combines spatial and temporal modeling.
> 
> First, a 1D Convolutional layer with 64 filters slides across input feature vectors, capturing localized spatial correlations between packet size, error rates, and protocol flags.
> 
> Next, an LSTM recurrent layer with 64 units processes the temporal sequence. Its forget, input, and output gates track connection state transitions across time, detecting slow-and-low reconnaissance scans that evade stateless packet filters.
> 
> To deploy this model onto edge hardware, we applied post-training Float16 quantization. Mapping 32-bit floats to 16-bit half-precision reduced the binary size by 83.2% - from 4.88 megabytes down to just 0.82 megabytes - while fitting entirely within the small L2 cache of embedded ARM processors."

---

## Slide 9: Edge Hardware Benchmarking & Latency Acceleration (9:00 - 10:15)

### Slide Visual Content
- **Physical Testbed Results Across Three Hardware Tiers**:
  - **Raspberry Pi 4B (1GB RAM, Cortex-A72 @ 1.5 GHz)**:
    - Baseline (41 feat, FP32): 157.66 ms latency | 1.86 MB
    - BWOA v3 (10 feat, FP32): 35.60 ms latency | 4.88 MB
    - **BWOA Quantized (10 feat, FP16)**: **0.76 ms latency** | **0.82 MB** (PASSES SCADA LOOP)
    - **Speedup**: **207x latency acceleration** over full-feature baseline.
  - **Raspberry Pi 5 (4GB RAM, Cortex-A76 @ 2.4 GHz)**:
    - **0.42 ms mean latency** | 0.68 ms P95 latency | 3.8 W power.
  - **AWS EC2 (t3.medium)**:
    - 1.57 ms mean latency | 617 requests/second sustained throughput.
- **Power Consumption**: 2.5 Watts - readily powered by a 10W solar panel and buffer battery.

### Speaker Notes (135 words | ~1 min 15 sec)
> "Let us examine our empirical hardware benchmarks. We tested the framework on physical edge hardware: a 1GB RAM Raspberry Pi 4B, a Raspberry Pi 5, and an AWS cloud instance.
> 
> On the Raspberry Pi 4B - representative of a $45 industrial substation edge gateway - the unoptimized 41-feature baseline required 157.66 milliseconds per sample.
> 
> With BWOA feature pruning, latency dropped to 35.60 milliseconds.
> 
> With Float16 quantization, inference latency plummeted to **0.76 milliseconds** - a 207-fold speedup.
> 
> Even at the 95th percentile, latency is just 1.10 milliseconds. This is over 40 times faster than the tightest 50-millisecond PLC scan cycle, operating at a meager 2.5 Watts of power."

---

## Slide 10: Multi-Class Detection Performance on KDDTest+ (10:15 - 11:30)

### Slide Visual Content
- **Held-Out KDDTest+ Evaluation (22,544 Samples)**:
  - **Normal Telemetry Precision**: **96.89%** (Preserves plant operational continuity)
  - **DoS Recall**: **89.04%** (Intercepts nearly 9 out of 10 volumetric flooding attacks)
  - **Probe Recall**: **70.80%** (Catches port scanning and lateral movement)
  - **Macro F1**: **0.7127** | **AUC-ROC**: **0.8471**
- **Transfer Learning on SWaT Physical SCADA**:
  - 51 physical water treatment sensors: **59.95% accuracy | 0.8650 AUC-ROC in 0.12 ms**.
- **Automated Verification Suite**: 75/75 unit tests passing across all pipeline modules.

### Speaker Notes (135 words | ~1 min 15 sec)
> "Let us examine classification performance on the held-out KDDTest+ benchmark of 22,544 samples.
> 
> In mineral extraction, false positives are toxic: shutting down a ball mill due to a false alarm costs $25,000 to $50,000 per hour. Our model delivers **96.89% precision on normal traffic** - virtually identical to the 97.12% baseline.
> 
> Concurrently, on Denial-of-Service attacks - the single biggest threat to industrial PLCs - we achieve **89.04% recall**, capturing 9 out of 10 volumetric attacks before they saturate controllers.
> 
> To test real-world transferability, we evaluated the framework on the 51-sensor physical Secure Water Treatment SCADA dataset, achieving an AUC-ROC of 0.8650 in 0.12 milliseconds. This proves our spatial-temporal features generalize to physical industrial processes."

---

## Slide 11: Three-Phase Implementation Roadmap & Pilot Deployments (11:30 - 12:30)

### Slide Visual Content
- **Structured 3-Phase Translation Pathway**:
  - **Phase 1 (Data Partnership & OT Capture)**:
    - Deploy pilot capture testbeds with mining and academic partners (e.g., Gold Fields Tarkwa, Ghana).
    - 2-instance AWS EC2 + CICFlowMeter harness generating labeled attack & benign traffic for Modbus TCP/RTU, DNP3, OPC-UA, and sensor telemetry.
  - **Phase 2 (Model Adaptation & Explainability)**:
    - Retrain constrained BWOA and CNN-LSTM on OT-specific protocol features.
    - Benchmark against SWaT (water treatment) and BATADAL (distribution) physical datasets.
    - Attach SHAP explanation layer giving non-specialist operators plain-language reasons with every alert.
  - **Phase 3 (Deployment Readiness & Localization)**:
    - Validate real-time sub-100 ms latency constraints on Raspberry Pi-class edge nodes.
    - Decoupled event-driven trigger: explanations generated strictly for flagged events.
    - Train local cybersecurity staff at partner sites to build indigenous African engineering capacity.

```mermaid
flowchart LR
    P1["Phase 1: Data Partnership<br/>(Gold Fields Tarkwa, Ghana)"] --> P2["Phase 2: Model Adaptation<br/>(SWaT / BATADAL + SHAP)"]
    P2 --> P3["Phase 3: Deployment Readiness<br/>(Sub-100ms Edge & Staff Training)"]
```

### Speaker Notes (140 words | ~1 min)
> "Our contribution extends beyond an isolated algorithm: we provide a concrete, literature-grounded three-phase implementation roadmap designed for resource-constrained African mining contexts.
> 
> In Phase 1, we partner with operating mines - such as Gold Fields' Tarkwa operation in Ghana - and academic testbeds, using our dual-instance AWS EC2 and CICFlowMeter architecture to capture labeled benign and attack traffic across industrial protocols like Modbus, DNP3, and OPC-UA.
> 
> In Phase 2, we retrain our BWOA feature selector and CNN-LSTM detector on these industrial protocol fields, benchmark against physical cyber-physical testbeds like SWaT and BATADAL, and attach the SHAP explanation layer.
> 
> In Phase 3, we validate sub-100 ms edge execution on Raspberry Pi hardware and train local mine cybersecurity personnel, ensuring the framework builds sovereign African technical capability rather than acting as a foreign black-box tool."

---

## Slide 12: Operational Trade-Off & Formal Research Questions (12:30 - 13:30)

### Slide Visual Content
- **The Core Engineering Trade-Off**:
  - Baseline (41 features): 77.70% accuracy @ 157.66 ms latency (fails real-time loop).
  - BWOA Quantized (10 features): 70.56% accuracy @ 0.76 ms latency (207x acceleration).
  - 96.89% normal precision, 89.04% DoS recall; delta concentrated in minority classes.
- **Empirical Grounding of RQ1-RQ4**:
  - **RQ1 (Pruning)**: BWOA pruned 75.61% of dimensions; converged in 23 iterations.
  - **RQ2 (Modeling)**: Conv1D-LSTM decoupled spatial cross-features from sequential temporal states.
  - **RQ3 (Edge Quantization)**: Float16 reduced model to 0.82 MB, running in 0.76 ms on 1GB RAM Pi 4B.
  - **RQ4 (Generalization & ROI)**: Generalized to SWaT SCADA (0.8650 AUC) with 200x-300x financial ROI.

### Speaker Notes (135 words | ~1 min)
> "In industrial engineering, this trade-off is completely Pareto-optimal.
> 
> A baseline model requiring 157 milliseconds cannot run in real time. It cannot be deployed on a 50-millisecond control loop; it would drop 95% of incoming traffic and provide zero real protection.
> 
> In contrast, our 70.56% model runs in 0.76 milliseconds, processing over 1,300 packets per second. On normal operational traffic, precision remains at 96.89%, and on DoS attacks, recall reaches 89.04%.
> 
> This definitively answers our research questions: BWOA successfully pruned 75.6% of dimensions; Conv1D-LSTM captured temporal-spatial states; Float16 quantization achieved sub-millisecond edge latency; and transferability to physical SCADA testbeds was empirically confirmed."

---

## Slide 13: Economic ROI, Human Safety & UN SDGs (13:30 - 14:15)

### Slide Visual Content
- **Economic ROI in Mineral Processing**:
  - Autonomous Haulage Truck ($12.5k/hr outage): **200x ROI**
  - Crusher & Milling SCADA ($25k/hr outage): **300x ROI**
  - Ventilation Safety Grid ($50k/hr outage): **260x ROI + Worker Life Safety**
- **Direct Alignment with UN Sustainable Development Goals**:
  - **SDG 9 (Industry, Innovation and Infrastructure)**: Strengthening cyber-resilience of digitalizing industrial infrastructure.
  - **SDG 8 (Decent Work and Economic Growth)**: Protecting operational continuity and underground worker safety from kinetic cyber-physical sabotage.
  - **SDG 17 (Partnerships for the Goals)**: Cross-continental scientific cooperation between African institutions and Empress Catherine II Saint Petersburg Mining University.

### Speaker Notes (125 words | ~45 sec)
> "The socio-economic significance of this work directly addresses the United Nations Sustainable Development Goals.
> 
> Economically, under SDG 9 and SDG 8, unplanned downtime in mineral extraction costs $50,000 to $500,000 per hour. Deploying an open-source, edge-native intrusion detector on a $45 gateway delivers an estimated return on investment exceeding 200x while protecting workers from catastrophic ventilation or tailings dam failures.
> 
> Environmentally, securing control loops prevents toxic chemical spills from unmonitored leaching tanks.
> 
> And under SDG 17, this project embodies true Russian-African scientific partnership, uniting the University of Education, Winneba with Saint Petersburg Mining University under the auspices of the UNESCO International Centre of Competence in Mining Engineering Education."

---

## Slide 14: Conclusion & Sovereign Technical Capacity (14:15 - 15:00)

### Slide Visual Content
- **Summary of Core Contributions**:
  - 75.61% feature dimensionality reduction via constrained BWOA (Mirjalili & Lewis, 2016).
  - 0.76 ms edge latency on 1GB RAM Raspberry Pi 4B (207x faster than baseline, sub-100 ms compliant).
  - 0.82 MB model size under Float16 quantization (83.2% compression).
  - Decoupled SHAP explainability layer providing transparent diagnostics for non-specialist operators.
  - Structured 3-Phase Roadmap from generic baseline to operational mine testbeds.
- **Production Artifacts & Open-Source Release**:
  - Open-Source Repo: `github.com/mhiskall282/Securing-the-Digital-Mine-UNESCO-Project`
  - Global Sniffer CLI: `@mhiskall282/unesco-mine-sec-cli` (GitHub Packages)
  - Full IEEE Manuscript & 77/77 Verified Automated Test Suite
- **Closing**: *"Building sovereign technical capacity to secure the digital mines of tomorrow."*

### Speaker Notes (125 words | ~45 sec)
> "In conclusion, 'Securing the Digital Mine' demonstrates that metaheuristic feature pruning, spatial-temporal deep learning, and explainable AI can resolve the edge latency and interpretability dilemmas in industrial IoT.
> 
> By shrinking telemetry from 41 to 10 features and quantizing to Float16, we achieved a 207-fold speedup, enabling sub-millisecond edge protection with plain-language SHAP diagnostics.
> 
> Most importantly, our framework is tailored for African operating realities: edge-first, offline-capable, and coupled with local technical training so that mining communities build indigenous capability rather than depending on proprietary foreign systems.
> 
> All software, models, and test harnesses are published under open-source licenses. We thank UNESCO and Saint Petersburg Mining University for this honor. Thank you, and we welcome your questions."

---

## Comprehensive Q&A Defense Cheat-Sheet (Post-Presentation)

### Question 1: "Why did you use NSL-KDD instead of a purely industrial OT dataset like TON_IoT or CIC-IDS?"
**Answer**:
> "NSL-KDD was chosen as the starting point because it is the most rigorously studied and reproducible intrusion benchmark in the literature, enabling direct mathematical comparison of our BWOA feature selection against existing published metaheuristic benchmarks (Krishnaveni et al., 2025; Anand & Arul, 2024). However, as Kheddar et al. (2023) emphasize, industrial OT networks exhibit unique structural dynamics: deterministic polling cycles, industrial protocol fields (Modbus, DNP3, OPC-UA), and narrower attack diversity. To address this, our research explicitly evaluates cross-domain transferability on the physical SWaT SCADA facility (0.8650 AUC in 0.12 ms), and establishes Phase 1 field PCAP capture at Gold Fields Tarkwa in Ghana to retrain BWOA and CNN-LSTM on live mining OT telemetry in Phase 2."

### Question 2: "How do you explain the 7.14% drop in overall accuracy from the baseline?"
**Answer**:
> "The 7.14% drop is a deliberate, Pareto-optimal engineering compromise. The 77.70% baseline model requires 157.66 milliseconds to evaluate a single packet. In an industrial mineral processing plant where PLCs scan every 20 to 50 milliseconds, that baseline model is mathematically impossible to deploy - it would drop 95% of incoming packets. Our 70.56% model operates in 0.76 milliseconds, processing over 1,300 packets per second. Crucially, on benign operational traffic, precision remains at 96.89%, and on DoS attacks, recall is 89.04%. The drop is concentrated solely in minority classes like U2R where only 52 training samples exist."

### Question 3: "Does Float16 quantization cause numerical instability or gradient underflow?"
**Answer**:
> "We applied post-training quantization (PTQ) rather than quantization-aware training. Because quantization is performed after model weights have converged, there is zero risk of gradient underflow. Float16 provides 5 bits of exponent and 10 bits of mantissa, offering a dynamic range of 6.1e-5 to 65,504, which is more than sufficient for the bounded activations of normalized network flow attributes and tanh/sigmoid outputs. Our empirical results confirm that Float16 quantization caused exactly zero degradation in accuracy or Macro F1 compared to unquantized weights."

### Question 4: "What happens if the edge gateway loses internet connectivity?"
**Answer**:
> "The system is architected as an edge-native, zero-cloud dependency artifact. The sniffer daemon, the BWOA pruning mask, the compiled TFLite runtime, and the local SQLite alert buffer reside entirely within the local Raspberry Pi gateway. If satellite or cellular connectivity fails, the edge node continues to evaluate traffic at line speed, store encrypted forensic audit logs, and trigger local relay contacts."

### Question 5: "How does BWOA compare to Particle Swarm Optimization (PSO) or Genetic Algorithms (GA)?"
**Answer**:
> "WOA possesses a unique dual-phase mathematical mechanism: shrinking encircling for exploitation and logarithmic spiral updating for exploration, governed by the linearly decaying coefficient vector A (Mirjalili & Lewis, 2016). Unlike standard PSO, which is prone to premature convergence in high-dimensional feature spaces, or GAs, which require computationally expensive crossover and mutation operations across large populations, our constrained BWOA with adaptive alpha decay converged in just 23 iterations, saving significant compute during retraining cycles."

### Question 6: "How do you run computationally intensive SHAP explanations on a 1GB Raspberry Pi without violating the sub-100 ms SCADA latency deadline?"
**Answer**:
> "We employ a decoupled, event-triggered explainability architecture, following principles established by Oyedotun et al. (2025). On routine, benign network traffic, SHAP is not executed at all - inference proceeds purely through the quantized TFLite engine in 0.76 milliseconds. Only when an anomaly or attack is flagged does the system trigger the SHAP attribution calculation asynchronously in a background thread. The resulting top feature contributions and plain-language operator reasons are populated onto the operator console within several hundred milliseconds of alert generation, ensuring that real-time packet inspection is never blocked or stalled while giving non-specialist operators immediate, interpretable root-cause visibility."

### Question 7: "What is your roadmap for validating this on live African mining operational technology?"
**Answer**:
> "We have formulated a literature-grounded three-phase roadmap. In Phase 1, we deploy our dual-instance AWS EC2 and CICFlowMeter data pipeline in collaboration with operational mining partners like Gold Fields Tarkwa in Ghana to capture labeled Modbus RTU/TCP, DNP3, and OPC-UA streams. In Phase 2, we retrain our constrained BWOA and CNN-LSTM architectures on these domain-specific industrial protocols, cross-validating on the SWaT and BATADAL datasets, and fine-tuning the SHAP explanation layer. In Phase 3, we test edge execution constraints on Raspberry Pi hardware and conduct hands-on training for local mine engineers and technicians, building long-term African technical sovereignty."
