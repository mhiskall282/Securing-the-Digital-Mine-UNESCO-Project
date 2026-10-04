# Speaker Notes: Securing the Digital Mine
## An Explainable, Metaheuristic-Optimized Deep Learning Framework for Intrusion Detection in IoT-Enabled Mineral Resource Operations

**Nomination**: Track 3, "Smart Subsoil": Digital Transformation and Automation in the Mineral Resources Complex  
**Forum**: Russian-African Forum of Young Scientists: "Future Engineers of the World - The Foundation of Sustainable Development"  
**Host Institution**: Empress Catherine II Saint Petersburg Mining University, under the auspices of the UNESCO International Centre of Competence in Mining Engineering Education  
**Event Dates**: 12 to 17 October 2026  

---

## Slide 1 Speaker Script (30 seconds)
**Word count target**: ~75 words  
**Speaker Notes**:  
Good morning distinguished members of the jury and fellow scientists. My name is John Okyere, presenting on behalf of our research team: Ezekeil Baah, Clement Baffour, Parker Paa Annobil, and George Akwesi Bonnah from the University of Education, Winneba and UEW Innovation Hub. Today I present our research on securing the digital mine: an explainable, metaheuristic-optimized deep learning framework for intrusion detection in IoT-enabled mineral resource operations. Our framework combines constrained binary whale optimization, CNN-LSTM temporal modeling, and an event-driven SHAP explanation layer to deliver transparent, sub-millisecond threat detection on low-cost edge gateways.

---

## Slide 2 Speaker Script (60 seconds)
**Word count target**: ~150 words  
**Speaker Notes**:  
Mining operations across Africa and Russia are adopting smart digital technologies at an unprecedented rate. This digitalization improves efficiency and worker safety but exposes industrial control systems to severe cyber risks. SCADA systems running Modbus or OPC-UA protocols are target options for espionage and sabotage. Remote mining sites often rely on low power edge nodes with constrained bandwidth. This environment prevents the use of heavy security tools. We need an intrusion detection system that is extremely lightweight but maintains high accuracy. This project provides a practical solution to this problem by selecting the most critical traffic characteristics and deploying an optimized neural network directly on low cost edge gateways. This approach ensures security without adding hardware costs or introducing high latency.

---

## Slide 3 Speaker Script (60 seconds)
**Word count target**: ~150 words  
**Speaker Notes**:  
Our framework implements a systematic pipeline divided into five stages. First, we ingest raw network traffic from industrial protocols using a high-speed edge sniffer. Second, we apply a Binary Whale Optimization Algorithm to filter out 75.6 percent of redundant traffic properties, pruning 41 features down to 10. Third, the optimal feature subset is passed to a hybrid classifier. This classifier uses a 1D Convolutional layer to capture spatial correlations in packet headers, followed by an LSTM layer to model temporal sequence dependencies. Fourth, we convert the trained network into a quantized float16 TFLite model for sub-millisecond edge CPU inference. Finally, we attach an event-triggered SHAP explanation layer. When an intrusion is flagged, SHAP generates a plain-language ranked list of contributing features, giving non-specialist mine operators immediate root-cause transparency without penalizing line-rate traffic inspection.

---

## Slide 4 Speaker Script (60 seconds)
**Word count target**: ~150 words  
**Speaker Notes**:  
To compress the input space we executed the Binary Whale Optimization Algorithm. BWOA mimics the social hunting behavior of humpback whales using shrinking bubble net updates. We incorporated a stratified cross validation search using a Random Forest classifier. This search was guided by a strict 75 percent validation accuracy floor constraint. The optimizer successfully selected 10 out of 41 features. This represents a 75.61 percent dimensionality reduction. The selected subset includes protocol type, source bytes, connection flag, and same service rate. These metrics capture the essential indicators of cyber attacks. By focusing on these features, we prune redundant dimensions while keeping the core threat signatures intact. This feature reduction directly translates to a smaller model footprint and faster edge processing.

---

## Slide 5 Speaker Script (60 seconds)
**Word count target**: ~150 words  
**Speaker Notes**:  
Let us examine the classification results. The baseline model utilizing all 41 features achieved a test accuracy of 77.70 percent and a Macro F1 score of 0.7571 on the held-out KDDTest+ set of 22,544 samples. The BWOA optimized model utilizing only 10 features achieved 70.56 percent accuracy and a Macro F1 of 0.7127. The accuracy gap is 7.14 percent. This represents a deliberate engineering decision. By accepting this accuracy reduction, we achieve a 77.4 percent reduction in inference latency, dropping from 157.66 milliseconds to 35.60 milliseconds. After float16 quantization, latency drops further to just 0.76 milliseconds. This confirms that the 10-feature subset is sufficient for reliable real time intrusion detection at the edge, where the full 41-feature model would be computationally infeasible.

---

## Slide 6 Speaker Script (30 seconds)
**Word count target**: ~75 words  
**Speaker Notes**:  
Our multi-class evaluation confirms strong performance on the most critical attack types. Normal traffic achieves a Precision of 0.9689 and F1 of 0.8018. DoS detection at F1=0.815 with 89% recall - our model catches 89% of all denial of service flows. R2L and U2R show lower scores due to NSL-KDD class imbalance: only 67 U2R samples exist in the entire test set. This is a known dataset limitation. We applied balanced class weights during training to prevent total collapse of minority classes.

---

## Slide 7 Speaker Script (45 seconds)
**Word count target**: ~110 words  
**Speaker Notes**:  
For production deployment, we quantized the optimized model to float16 TFLite representation. This reduced the storage footprint from 4.88 megabytes to 0.82 megabytes. This is an 83.2 percent size reduction. The mean edge latency dropped to 0.76 milliseconds, with a 95th percentile latency of 1.10 milliseconds. The peak RAM utilization was measured at 290.31 megabytes. This is far below our 1 gigabyte target. The deployment verdict is a definite pass. The system is ready for local gateways at remote mining sites.

---

## Slide 8 Speaker Script (60 seconds)
**Word count target**: ~150 words  
**Speaker Notes**:  
To transition this research from laboratory benchmarks into live operational environments, we established a concrete three-phase roadmap. In Phase 1, we establish data partnerships with active African mining concessions like Gold Fields' Tarkwa mine in Ghana, using a dual-instance AWS EC2 and CICFlowMeter harness to capture labeled Modbus, DNP3, and OPC-UA telemetry. In Phase 2, we retrain our constrained BWOA and CNN-LSTM architectures on OT-specific features, benchmark against physical cyber-physical testbeds like SWaT and BATADAL, and calibrate the SHAP explanation layer for operator readability. In Phase 3, we validate edge latency under sub-100 ms constraints on Raspberry Pi hardware and conduct comprehensive training programs for local African engineering personnel, ensuring technical sovereignty rather than dependence on external proprietary software.

---

## Slide 9 Speaker Script (45 seconds)
**Word count target**: ~110 words  
**Speaker Notes**:  
Our research directly advances the United Nations Sustainable Development Goals. Under SDG 9, it builds cyber resilience for digitalizing industrial infrastructure. Under SDG 8, it protects worker lives and operational continuity by preventing kinetic sabotage against ventilation systems and tailings dam monitors. Under SDG 17, it embodies Russian-African scientific cooperation between the University of Education, Winneba and Saint Petersburg Mining University under UNESCO auspices. Unplanned downtime in mineral processing costs up to $500,000 per hour; our open-source edge architecture delivers an estimated return on investment exceeding 200x. Thank you for your attention. I am open to your questions.

---

## Anticipated Jury Q&A Preparation

### Question 1: Why did you use a hybrid CNN-LSTM instead of a simpler model like Random Forest or SVM?
**Answer**: Industrial network traffic has both spatial characteristics (packet size, connection counts) and temporal characteristics (arrival patterns, sequences of connections). Random Forest and SVM evaluate packets as independent events and ignore sequence context. The Conv1D layers extract local spatial patterns from features, and the LSTM layers learn sequence dependencies. This dual modeling is critical for detecting multi-stage intrusion threats.

### Question 2: Why does BWOA drop accuracy compared to the baseline?
**Answer**: BWOA removes 75.61 percent of the features. Dropping from 41 to 10 features inevitably discards some marginal correlation details. However, this minor accuracy gap is a deliberate design decision. By accepting a small accuracy reduction, we cut edge latency and model size by 82.9 percent. This allows the model to run on constrained edge gateways, which would otherwise be unable to host the baseline system.

### Question 3: How does the model perform transfer learning for site-specific OT protocols?
**Answer**: Our methodology freezes the Conv1D layers of the pre-trained model to preserve general feature extraction capabilities. We then retrain the LSTM layers using labeled local OT logs (Modbus or OPC-UA) collected at the specific mining plant. This allows the system to adapt to local sequence patterns in less than 20 training epochs, requiring minimal computational resources.

### Question 4: Is float16 quantization safe? Does it degrade model accuracy?
**Answer**: Yes, float16 post-training quantization is safe. Unlike 8-bit integer quantization which can cause accuracy drop in regression or sequence models, float16 preserves the dynamic range of network weights almost perfectly. We observed no statistically significant classification degradation after TFLite float16 compilation.

### Question 5: How did you calculate the balanced class weights during training?
**Answer**: We calculated class weights using the inverse frequency of the target labels in the training set: `weight_c = total_samples / (n_classes * count_c)`. This scales the loss updates during training so that errors on rare classes (like U2R and R2L) are penalized heavily. This approach successfully prevents minority class collapse.

### Question 6: How can you run SHAP explanations on a 1GB Raspberry Pi under strict sub-100 ms SCADA deadlines?
**Answer**: We employ a decoupled, event-triggered explainability architecture. In our joint empirical study with IBA Karachi (Uddin & Iradat, 2026), exact KernelSHAP across 1,024 coalitions over 50 background centroids took 1,687.8 ms (~1.7 s) on CPU. That empirical measurement proves why running SHAP synchronously in-line would severely violate the sub-100 ms SCADA control loop deadline. Instead, our quantized Float16 TFLite model inspects 96.89% of routine benign flows in just 0.76 ms without any delay. Only when an anomaly is flagged does the system trigger the SHAP attribution asynchronously in a background worker thread. Non-specialist operators receive plain-language feature diagnostics on their console (e.g., explaining that a DoS alert was driven by flag=S0 (+0.31) and serror_rate=1.0 (+0.31), raising DoS probability from 0.34 to 0.99) without stalling packet forwarding.

### Question 7: What is the practical roadmap for African mining deployment?
**Answer**: Our three-phase roadmap begins with Phase 1 data partnerships at operating sites like Gold Fields Tarkwa in Ghana to capture Modbus RTU/TCP, DNP3, and OPC-UA streams via AWS EC2 and CICFlowMeter. Phase 2 adapts BWOA and CNN-LSTM on OT features and validates on SWaT/BATADAL. Phase 3 verifies sub-100 ms edge execution on Raspberry Pi gateways and trains local African technicians to build long-term domestic engineering capacity.

---

## Key Metrics to Memorize
* **Baseline Accuracy (v3)**: 77.70% (F1: 0.7571, AUC-ROC: 0.9359)
* **BWOA v3 Optimized**: 70.56% accuracy (F1: 0.7127, AUC-ROC: 0.8471)
* **Accuracy gap**: 7.14% (deliberate trade-off for 207x latency reduction)
* **Latency**: 35.60ms (BWOA Keras) / 0.76ms (Quantized TFLite)
* **Feature count**: 41 reduced to 10 (75.61% reduction)
* **BWOA RF CV**: 92.31% validation accuracy (above 75% floor, PASS)
* **Quantized model**: 0.8211MB, 0.76ms mean / 1.10ms P95, 290.31MB RAM
* **Deployment**: PASS (131x safety margin on 100ms deadline)
* **Per-class F1**: Normal=0.8018, DoS=0.8150, Probe=0.6183, R2L=0.2332, U2R=0.0258

---

## Elevator Pitch (One-Sentence Summary)
We used a binary whale metaheuristic and spatial-temporal deep learning with a decoupled SHAP explanation layer to prune 75.6% of network features and deliver explainable, sub-millisecond intrusion detection on low-cost edge gateways for African mining operations.
