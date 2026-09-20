# Conference Presentation: Securing the Digital Mine
## A Metaheuristic-Optimized Deep Learning Framework for Edge Intrusion Detection in Industrial Mining IoT

**Venue**: Russian-African Forum-Contest of Young Scientists / UNESCO International Centre of Competence in Mining Engineering Education  
**Track**: Track 3: Smart Subsoil - Digital Transformation and Automation in Mineral Resources  
**Presentation Timing**: 15 Minutes (14 Slides) + 5 Minutes Q&A  
**Presenters**: John Okyere (Lead Author), Ezekeil Baah, Clement Baffour, Parker Paa Annobil, George Akwesi Bonnah  
*University of Education, Winneba (UEW), Ghana | UEW Innovation Hub*

---

## Slide 1: Title & Institutional Context (0:00 - 1:00)

### Slide Visual Content
- **Title**: Securing the Digital Mine: A Metaheuristic-Optimized Deep Learning Framework for Edge Intrusion Detection in Industrial Mining IoT
- **Authors**: John Okyere, Ezekeil Baah, Clement Baffour, Parker Paa Annobil, George Akwesi Bonnah
- **Affiliation**: Cyber-Physical Systems Research Group, Department of ICT & UEW Innovation Hub, University of Education, Winneba, Ghana
- **Conference Banner**: UNESCO Russian-African Forum-Contest of Young Scientists, Empress Catherine II Saint Petersburg Mining University
- **Core Callout**: *75.6% Feature Pruning | 0.76 ms Edge Inference | 207x Latency Acceleration on 1GB RAM ARM Gateways*

### Speaker Notes (130 words | ~1 min)
> "Distinguished session chairs, esteemed colleagues, and fellow researchers from across the African continent and the Russian Federation. I am John Okyere, representing the University of Education, Winneba and the UEW Innovation Hub in Ghana.
> 
> Today, our team presents our Design Science Research artifact: **'Securing the Digital Mine.'** As mineral resource extraction transitions to the Smart Subsoil paradigm - deploying hundreds of thousands of Industrial IoT telemetry sensors and automated SCADA systems - we face an urgent cybersecurity challenge.
> 
> In this talk, we present an edge-native intrusion detection framework that combines a constrained Binary Whale Optimization Algorithm with a spatial-temporal CNN-LSTM neural network and Float16 quantization, achieving sub-millisecond threat detection on 1GB RAM hardware. Let us examine why traditional IT security models fail in mineral extraction."

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

## Slide 5: Architectural Blueprint: 4-Tier Edge Defense (4:00 - 5:15)

### Slide Visual Content
- **Diagram**: 4-Tier End-to-End System Flow
  - **Tier 1 (Ingestion)**: Libpcap edge sniffer (`@mhiskall282/unesco-mine-sec-cli`) capturing bi-directional packets at line speed.
  - **Tier 2 (Optimization)**: BWOA feature pruner dropping 75.6% of fields (41 -> 10 features).
  - **Tier 3 (Inference)**: TFLite Float16 spatial-temporal engine executing in 0.76 ms.
  - **Tier 4 (Supervision)**: FastAPI microservice + Livewire SCADA monitoring console.
- **Key Metric**: Fully self-contained edge execution with zero external cloud dependency.

### Speaker Notes (130 words | ~1 min 15 sec)
> "To solve this, we engineered a decoupled four-tier architecture.
> 
> At Tier 1, an asynchronous edge sniffer daemon binds to switch mirror ports, capturing SCADA packets at line speed without introducing in-line latency.
> 
> At Tier 2, our metaheuristic optimization layer immediately prunes incoming telemetry, discarding 31 redundant attributes and extracting exactly 10 high-value features.
> 
> At Tier 3, a lightweight spatial-temporal neural network - compressed via post-training Float16 quantization - evaluates the flow in 0.76 milliseconds on a single ARM core.
> 
> Finally, at Tier 4, localized predictions and confidence scores are streamed to a control room dashboard via a high-speed FastAPI microservice. The entire pipeline operates 100% offline, guaranteeing autonomous defense even if satellite backhaul is completely severed."

---

## Slide 6: Metaheuristic Feature Pruning: Constrained BWOA (5:15 - 6:30)

### Slide Visual Content
- **Binary Whale Optimization Algorithm (BWOA)**:
  - Models humpback whale bubble-net foraging in discrete binary space $\{0, 1\}^{41}$.
  - Shrinking encircling ($|A| < 1$) + logarithmic spiral updating ($p \ge 0.5$) + random exploration ($|A| \ge 1$).
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

## Slide 7: Physical Meaning of Selected Features (6:30 - 7:45)

### Slide Visual Content
- **The Top 10 BWOA-Selected Features & Gini Importance**:
  1. `src_bytes` (0.2451): Captures volumetric buffer floods and DoS bursts.
  2. `service` (0.1982): Maps connection type to Modbus/DNP3 industrial ports.
  3. `flag` (0.1420): Connection state tracking (SYN/FIN/RST anomalies).
  4. `serror_rate` (0.1185): SYN error percentage - primary DoS signature.
  5. `same_srv_rate` (0.0894): Detects repeated polling abuse of specific registers.
  6. `diff_srv_rate` (0.0652): Reconnaissance port sweeps across PLCs.
  7. `dst_host_diff_srv_rate` (0.0521): Host-level scanning of substation devices.
  8. `protocol_type` (0.0412): TCP vs UDP vs ICMP segregation.
  9. `hot` (0.0278): Unauthorized access to sensitive system directories.
  10. `su_attempted` (0.0205): Escalation to root privileges on engineering workstations.

### Speaker Notes (135 words | ~1 min 15 sec)
> "What is remarkable about our BWOA optimizer is that it did not select features arbitrarily. Every single chosen feature corresponds directly to physical cyber-physical attack vectors.
> 
> The top feature - `src_bytes`, with a Gini importance of 0.2451 - detects volumetric Denial-of-Service floods attempting to blind operator displays.
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

## Slide 11: Operational Trade-Off & Pareto Optimality (11:30 - 12:30)

### Slide Visual Content
- **The Core Trade-off**:
  - Baseline (41 features): 77.70% accuracy @ 157.66 ms latency
  - BWOA Quantized (10 features): 70.56% accuracy @ 0.76 ms latency
  - Delta: -7.14% theoretical accuracy for a **207x real-time speedup**.
- **Pareto Optimality Argument**:
  - A 77.7% model that takes 157 ms cannot be deployed in SCADA (0% real protection).
  - A 70.56% model operating in 0.76 ms delivers continuous, actionable real-time security.
- **User Acceptance Testing (UAT)**:
  - 5 industrial specialists scored the system **4.85 / 5.00 overall operational utility**.

### Speaker Notes (135 words | ~1 min)
> "A rigorous reviewer will ask: 'Why accept a 7% decrease in overall accuracy, from 77.7% to 70.5%?'
> 
> In industrial systems engineering, this trade-off is completely Pareto-optimal.
> A model requiring 157 milliseconds cannot run in real time. It cannot be deployed on a 50-millisecond control loop. Its theoretical 77.7% accuracy provides exactly zero real-world protection.
> 
> In contrast, our 70.56% model runs in 0.76 milliseconds. It evaluates every single packet at line speed.
> Furthermore, the accuracy delta is concentrated in extreme minority classes like User-to-Root, where the benchmark contains only 52 training examples. On the attacks that matter most to plant survival - DoS and normal baseline filtering - our model provides enterprise-grade reliability."

---

## Slide 12: Formal Answers to Research Questions (12:30 - 13:30)

### Slide Visual Content
- **Empirical Grounding of RQ1-RQ4**:
  - **RQ1 Answer (Pruning)**: Constrained BWOA pruned 75.61% of dimensions (41 -> 10 features), maintaining 70.56% multi-class accuracy and 92.31% cross-validation accuracy.
  - **RQ2 Answer (Modeling)**: Spatial-temporal Conv1D-LSTM captured cross-packet dependencies and temporal states, delivering 96.89% normal precision and 89.04% DoS recall (0.8471 AUC-ROC).
  - **RQ3 Answer (Edge Quantization)**: Float16 quantization compressed model to 0.82 MB (83.2% reduction) and achieved 0.76 ms inference on 1GB RAM Pi 4B (207x faster, 131x under 100 ms deadline).
  - **RQ4 Answer (Generalization & ROI)**: Generalized to SWaT testbed (0.8650 AUC in 0.12 ms) and established 200x to 300x financial ROI while eliminating worker life-safety risks.

### Speaker Notes (135 words | ~1 min)
> "Returning to our four research questions, our empirical data provides unambiguous, definitive answers.
> 
> For RQ1: Constrained BWOA pruned 75.61% of features down to 10, retaining 70.56% test accuracy and 92.31% CV accuracy.
> 
> For RQ2: Conv1D-LSTM effectively decoupled spatial cross-feature maps from temporal sequence states, achieving 96.89% precision on benign traffic and 89.04% recall on DoS intrusions.
> 
> For RQ3: Float16 quantization compressed the network to 0.82 megabytes and clocked 0.76 milliseconds on a 1GB Raspberry Pi 4B, easily satisfying real-time PLC cyclic scan loops.
> 
> And for RQ4: Cross-domain transferability on the SWaT testbed reached an AUC-ROC of 0.8650 in 0.12 milliseconds, while economic modeling confirmed over 200x return on investment in preventing costly downtime."

---

## Slide 13: Economic ROI, Human Safety & UN SDGs (13:30 - 14:15)

### Slide Visual Content
- **Economic ROI in Mineral Processing**:
  - Autonomous Haulage Truck ($12.5k/hr outage): **200x ROI**
  - Crusher & Milling SCADA ($25k/hr outage): **300x ROI**
  - Ventilation Safety Grid ($50k/hr outage): **260x ROI + Worker Life Safety**
- **UN Sustainable Development Goals**:
  - **SDG 9**: Resilient industrial infrastructure for developing economies.
  - **SDG 8**: Worker safety and life preservation in underground mines.
  - **SDG 17**: Bilateral UNESCO scientific collaboration between Ghana and Russia.

### Speaker Notes (125 words | ~45 sec)
> "The socio-economic significance of this work directly addresses the United Nations Sustainable Development Goals.
> 
> From an economic standpoint, unplanned downtime in mining costs between $50,000 and $500,000 per hour. Deploying an open-source IDS on a $45 edge gateway yields an estimated return on investment exceeding **200x**.
> 
> But more importantly, this is about human lives. Underground miners depend on automated ventilation-on-demand and toxic gas scrubbers. Intercepting cyber intrusions before they tamper with ventilation controls prevents fatal asphyxiation disasters.
> 
> Under the auspices of UNESCO and Empress Catherine II Saint Petersburg Mining University, this research exemplifies true scientific partnership - building sovereign engineering capacity for the African mining sector."

---

## Slide 14: Conclusion & Open-Source Artifacts (14:15 - 15:00)

### Slide Visual Content
- **Summary of Achievements**:
  - 75.61% feature dimensionality reduction via constrained BWOA.
  - 0.76 ms edge latency on 1GB RAM Raspberry Pi 4B (207x faster than baseline).
  - 0.82 MB model size under Float16 quantization (83.2% compression).
  - 96.89% normal precision, 89.04% DoS recall, sub-100 ms SCADA compliant.
- **Production Artifacts**:
  - Open-Source Repo: `github.com/mhiskall282/Securing-the-Digital-Mine-UNESCO-Project`
  - Global Sniffer CLI: `@mhiskall282/unesco-mine-sec-cli` (GitHub Packages)
  - Full IEEE Manuscript & Verified Test Suite (75/75 Pass)
- **Closing**: *"Securing the foundation of tomorrow's digital mines."*

### Speaker Notes (125 words | ~45 sec)
> "In conclusion, 'Securing the Digital Mine' demonstrates that intelligent metaheuristic feature pruning and deep learning quantization can solve the real-time latency dilemma in industrial IoT.
> 
> By shrinking our feature set from 41 to 10 and quantizing to Float16, we achieved a 207-fold speedup, delivering sub-millisecond threat detection on low-power, 1GB RAM edge gateways.
> 
> All our artifacts - the sniffer CLI, the FastAPI inference engine, the BWOA optimizer, and the 75 automated unit tests - are publicly available on GitHub under an open-source license for global mining operators and academic researchers.
> 
> We extend our sincere gratitude to UNESCO and Saint Petersburg Mining University for championing young scientists in mineral resources. Thank you, and we welcome your questions."

---

## Comprehensive Q&A Defense Cheat-Sheet (Post-Presentation)

### Question 1: "Why did you use NSL-KDD instead of a purely industrial OT dataset like TON_IoT or CIC-IDS?"
**Answer**:
> "NSL-KDD was chosen as the baseline because it is the most rigorously studied and reproducible intrusion benchmark in the literature, enabling direct mathematical comparison of our BWOA feature selection against existing state-of-the-art papers. However, recognizing the IT-centric nature of NSL-KDD, we explicitly performed **transfer evaluation on the SWaT physical industrial SCADA testbed**, which features 51 real physical water treatment sensor channels under 36 kinetic attack scenarios. Furthermore, as detailed in Appendix M of our paper, Phase 1 field PCAP capture is underway at Gold Fields Tarkwa in Ghana to produce a dedicated, open-source mining Modbus dataset."

### Question 2: "How do you explain the 7.14% drop in overall accuracy from the baseline?"
**Answer**:
> "The 7.14% drop is a deliberate, Pareto-optimal engineering compromise. The 77.70% baseline model requires 157.66 milliseconds to evaluate a single packet. In an industrial mineral processing plant where PLCs scan every 20 to 50 milliseconds, that baseline model is mathematically impossible to deploy - it would drop 95% of incoming packets. Our 70.56% model operates in 0.76 milliseconds, processing over 1,300 packets per second. Crucially, on benign operational traffic, precision remains at 96.89%, and on DoS attacks, recall is 89.04%. The drop is concentrated solely in minority classes like U2R where only 52 training samples exist."

### Question 3: "Does Float16 quantization cause numerical instability or gradient underflow?"
**Answer**:
> "We applied post-training quantization (PTQ) rather than quantization-aware training. Because quantization is performed *after* model weights have converged, there is zero risk of gradient underflow. Float16 provides 5 bits of exponent and 10 bits of mantissa, offering a dynamic range of $6.1 \times 10^{-5}$ to $65,504$, which is more than sufficient for the bounded activations of normalized network flow attributes and tanh/sigmoid outputs. Our empirical results confirm that Float16 quantization caused exactly zero degradation in accuracy or Macro F1 compared to unquantized weights."

### Question 4: "What happens if the edge gateway loses internet connectivity?"
**Answer**:
> "The system is architected as an edge-native, zero-cloud dependency artifact. The sniffer daemon, the BWOA pruning mask, the compiled TFLite runtime, and the local SQLite alert buffer reside entirely within the local Raspberry Pi gateway. If satellite or cellular connectivity fails, the edge node continues to evaluate traffic at line speed, store encrypted forensic audit logs, and trigger local relay contacts."

### Question 5: "How does BWOA compare to Particle Swarm Optimization (PSO) or Genetic Algorithms (GA)?"
**Answer**:
> "WOA possesses a unique dual-phase mathematical mechanism: shrinking encircling for exploitation and logarithmic spiral updating for exploration, governed by the linearly decaying coefficient vector $A$. Unlike standard PSO, which is prone to premature convergence in high-dimensional feature spaces, or GAs, which require computationally expensive crossover and mutation operations across large populations, our constrained BWOA with adaptive alpha decay converged in just 23 iterations, saving significant compute during retraining cycles."
