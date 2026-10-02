# SECURING THE DIGITAL MINE: AN EXPLAINABLE, METAHEURISTIC-OPTIMIZED DEEP LEARNING FRAMEWORK FOR INTRUSION DETECTION IN IOT-ENABLED MINERAL RESOURCE OPERATIONS

**Nomination**: Track 3, "Smart Subsoil": Digital Transformation and Automation in the Mineral Resources Complex  
**Forum**: Russian-African Forum of Young Scientists: "Future Engineers of the World - The Foundation of Sustainable Development"  
**Host Institution**: Empress Catherine II Saint Petersburg Mining University, Saint Petersburg, Russia  
**Under the Auspices of**: UNESCO International Centre of Competence in Mining Engineering Education  
**Authors**: John Okyere (Lead Author), Ezekeil Baah, Clement Baffour, Parker Paa Annobil, George Akwesi Bonnah  
**Affiliation**: Cyber-Physical Systems Research Group, Department of Information and Communication Technology & UEW Innovation Hub, University of Education, Winneba (UEW), Ghana  
**Correspondence**: hello@johnokyere.xyz | **Repository**: https://github.com/mhiskall282/Securing-the-Digital-Mine-UNESCO-Project  

---

## Abstract

African mining operations are digitalizing quickly through IoT sensor networks, SCADA systems, and cloud-connected digital twins, yet cybersecurity for these operational technology (OT) environments lags behind conventional IT (Alanazi, Mahmood, & Chowdhury, 2022). This work adapts a Binary Whale Optimization Algorithm (BWOA) and CNN-LSTM intrusion detector, validated on generic network traffic, to mining IoT and SCADA traffic. A SHAP-based explanation layer shows which traffic features triggered each alert, giving non-specialist operators a reason with every detection (Oyedotun, Oise, & Ozobialu, 2025). A three-phase roadmap, data partnerships, and links to the UN Sustainable Development Goals are outlined.

**Keywords**: intrusion detection; explainable AI; SHAP; Whale Optimization Algorithm; CNN-LSTM; Industrial IoT; SCADA; mining digitalization; Africa

---

## Introduction

Mining and mineral processing across Africa, including Ghana, are going digital. IoT sensors monitor equipment and ore bodies, AI-assisted predictive maintenance is deployed at sites such as Gold Fields' Tarkwa mine, and digital twins support ecological and operational risk management (Nigerian Mineral Exchange, 2025; IT-Online, 2026). The same connectivity widens the attack surface of facilities that were once isolated. Industry reporting shows that digitalization has exposed the mining sector to attacks capable of manipulating programmable logic controllers, halting production, or endangering workers (African Mining Market, 2024). Meanwhile, most intrusion detection research and benchmark datasets target conventional IT and cloud traffic rather than the protocols, polling intervals, and sensor-driven signatures of OT and Industrial IoT (IIoT) environments (Kheddar, Himeur, & Awad, 2023). For operators working under limited connectivity and infrastructure (IT-Online, 2026), this gap matters. The project asks how a detection approach validated on conventional traffic can be adapted to African mining infrastructure, and how its alerts can be made understandable to the people who must act on them.

---

## Methods

The starting point is a BWOA feature-selection stage combined with a CNN-LSTM classifier, validated on the NSL-KDD benchmark. The Whale Optimization Algorithm (Mirjalili & Lewis, 2016) mimics humpback bubble-net hunting and has since been widely applied to intrusion detection feature selection. Hybrid WOA approaches paired with CNN, LSTM, or ensemble classifiers report accuracy above 96 percent on NSL-KDD and related benchmarks (Krishnaveni et al., 2025; Anand & Arul, 2024). CNN-LSTM captures both packet-level patterns and temporal dynamics and has been applied to SCADA and IIoT detection (Almomani, Akour, & Habeb, 2025). Kheddar et al. (2023) identify the structural differences between IT and OT traffic that any adaptation must handle: deterministic polling cycles, industrial protocol fields, and narrower attack-class diversity. We add a SHAP explanation layer (Lundberg & Lee, 2017) so that every alert lists the features that pushed the model toward its decision, following recent work on explainable anomaly detection in SCADA and DCS environments (Oyedotun et al., 2025). The novelty is not a new algorithm but a concrete, literature-grounded adaptation pathway, with built-in explainability, for under-resourced African mining contexts where labeled OT attack data is scarce.

---

## Results: Three-Phase Framework

A three-phase framework is proposed:

* **Phase 1 (Data Partnership)**: Capture OT traffic at pilot mining sites or testbeds with industry and academic partners, using a two-instance AWS EC2 and CICFlowMeter setup to generate labeled attack and benign traffic for SCADA protocols (Modbus, DNP3, OPC-UA) and IoT sensor streams.
* **Phase 2 (Model Adaptation and Explainability)**: Retrain BWOA and the CNN-LSTM on OT-specific features, validate against the SWaT and BATADAL benchmarks, and attach the SHAP layer so each alert carries a ranked list of contributing features in plain language.
* **Phase 3 (Deployment Readiness)**: Test latency and computational footprint under edge constraints, targeting sub-100 ms detection on Raspberry Pi-class hardware. To protect that target, explanations are generated only for flagged events, not for all traffic.

```mermaid
flowchart LR
    subgraph P1["Phase 1: OT Data Partnerships"]
        direction TB
        A1["⛏️ <b>Pilot Mining Concessions</b><br/>Tarkwa gold basin, Ghana & testbeds"]
        A2["☁️ <b>Dual AWS EC2 Pipeline</b><br/>CICFlowMeter packet feature labeling"]
        A3["📡 <b>Capture SCADA Traffic</b><br/>Modbus RTU/TCP, DNP3, OPC-UA, MQTT"]
        A1 --> A2 --> A3
    end

    subgraph P2["Phase 2: Adaptation & SHAP"]
        direction TB
        B1["⚡ <b>Retrain BWOA Pruner</b><br/>Extract minimal OT feature vector"]
        B2["🔬 <b>Cross-Validate on SCADA</b><br/>Benchmarked on 51-sensor SWaT & BATADAL"]
        B3["🔍 <b>Attach Decoupled SHAP</b><br/>Plain-language diagnostic root cause"]
        B1 --> B2 --> B3
    end

    subgraph P3["Phase 3: Edge Deployment"]
        direction TB
        C1["⏱️ <b>Sub-100ms Edge Validation</b><br/>0.76ms Float16 on Raspberry Pi 4B"]
        C2["💾 <b>Offline SQLite Buffer</b><br/>Resilient store-and-forward telemetry"]
        C3["🎓 <b>Local Capacity Building</b><br/>Train sovereign African mine workforce"]
        C1 --> C2 --> C3
    end

    A3 ==> B1
    B3 ==> C1

    classDef p1 fill:#1e293b,stroke:#38bdf8,stroke-width:2px,color:#f8fafc;
    classDef p2 fill:#1e293b,stroke:#f59e0b,stroke-width:2px,color:#f8fafc;
    classDef p3 fill:#1e293b,stroke:#10b981,stroke-width:2px,color:#f8fafc;

    class A1,A2,A3 p1;
    class B1,B2,B3 p2;
    class C1,C2,C3 p3;
```

*(Note: Live baseline BWOA feature selection, CNN-LSTM classification, Float16 quantization, and edge latency benchmarks on Raspberry Pi 4B/5 and AWS EC2 are confirmed in repository documentation; full empirical OT retraining and production SHAP inference evaluation are scheduled for Phase 2 implementation).*

---

## Discussion

The project aligns with three UN Sustainable Development Goals:
* **SDG 9 (Industry, Innovation and Infrastructure)**: Strengthening the resilience of digitalizing industrial infrastructure.
* **SDG 8 (Decent Work and Economic Growth)**: Protecting operational continuity and worker safety (African Mining Market, 2024).
* **SDG 17 (Partnerships for the Goals)**: The proposed Russian-African data sharing and research pathway embodies cross-continental scientific cooperation.

Economically, undetected intrusions into industrial control systems risk production halts and costly equipment damage. Environmentally, compromised controls can cause unmonitored ecological harm from mining and processing. Socially, securing these systems protects workers and communities that depend on mining, especially as digitalization outpaces cybersecurity investment (IT-Online, 2026). The team brings direct, validated experience in metaheuristic-optimized deep learning for intrusion detection.

---

## Conclusion

As African and Russian mining operations digitalize, OT and IIoT security must receive the same urgency as conventional IT. The target audience is mining operators, industrial cybersecurity teams, and policymakers shaping digital infrastructure strategy on both continents. The framework is built for African conditions: edge-first deployment, offline-capable detection, and explained alerts that non-specialist operators can act on without an analyst. The Phase 3 localization component trains local cybersecurity staff at partner sites, so the solution builds African technical capacity rather than simply transferring a foreign tool. Its practical value is a resource-conscious starting point for securing the digital mine, treating cybersecurity as part of digitalization strategy, not an afterthought.

---

## References

1. African Mining Market. (2024, April 8). Cybersecurity concerns mount in mining arena. https://africanminingmarket.com/cybersecurity-concerns-mount-in-mining-arena/18217/
2. Alanazi, M., Mahmood, A., & Chowdhury, M. J. M. (2022). SCADA vulnerabilities and attacks: A review of the state-of-the-art and open issues. *Computers & Security*, 125, 103028. https://doi.org/10.1016/j.cose.2022.103028
3. Almomani, O., Akour, I., & Habeb, A. (2025). Symmetrical resilience: Detection of cyberattacks for SCADA systems used in IIoT in big data environments. *Symmetry*, 17(4), 480. https://doi.org/10.3390/sym17040480
4. Anand, M., & Arul, U. (2024). Whale optimization algorithm enhanced long short-term memory classifier with novel wrapped feature selection for intrusion detection. *Cryptography*, 8(4), 73. https://doi.org/10.3390/cryptography8040073
5. IT-Online. (2026, February 16). Digital innovations reshape the future of mining in Africa. https://it-online.co.za/2026/02/16/digital-innovations-reshape-the-future-of-mining-in-africa/
6. Kheddar, H., Himeur, Y., & Awad, A. I. (2023). Deep transfer learning for intrusion detection in industrial control networks: A comprehensive review. *Journal of Network and Computer Applications*. https://doi.org/10.48550/arXiv.2304.10550
7. Krishnaveni, S., Chen, T. M., Sivamohan, S., & Subbiah, S. (2025). Optimizing feature selection in imbalanced intrusion detection systems using hybrid metaheuristic algorithms for wireless sensor networks. *Cluster Computing*, 28, 5248. https://doi.org/10.1007/s10586-025-05248-6
8. Lundberg, S. M., & Lee, S.-I. (2017). A unified approach to interpreting model predictions. *Advances in Neural Information Processing Systems*, 30, 4765-4774. https://arxiv.org/abs/1705.07874
9. Mirjalili, S., & Lewis, A. (2016). The whale optimization algorithm. *Advances in Engineering Software*, 95, 51-67. https://doi.org/10.1016/j.advengsoft.2016.01.008
10. Nigerian Mineral Exchange. (2025, May 5). Smart mines, bigger profits: How IoT and big data are transforming Nigeria's mining efficiency. https://nigerianmineralexchange.com/smart-mines-bigger-profits-how-iot-and-big-data-are-transforming-nigerias-mining-efficiency/
11. Oyedotun, S. A., Oise, G. P., & Ozobialu, C. E. (2025). Towards intelligent cybersecurity in SCADA and DCS environments: Anomaly detection using multimodal deep learning and explainable AI. *Journal of Scientific Research and Reviews*, 2(1), 20-31.
12. United Nations. (2015). Transforming our world: The 2030 agenda for sustainable development (A/RES/70/1). https://sdgs.un.org/2030agenda
