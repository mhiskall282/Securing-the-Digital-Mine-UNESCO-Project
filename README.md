# Securing the Digital Mine

> A Metaheuristic-Optimized, Multi-Tenant Deep Learning Suite for Intrusion Detection in IoT SCADA Industrial Mining Networks.

[![License: MIT](https://img.shields.io/badge/License-MIT-emerald.svg)](LICENSE)
[![Vite](https://img.shields.io/badge/Vite-v7-purple.svg)](https://vitejs.dev)
[![Laravel](https://img.shields.io/badge/Laravel-v12-red.svg)](https://laravel.com)
[![Support Email](https://img.shields.io/badge/Support-hello%40johnokyere.xyz-blue.svg)](mailto:hello@johnokyere.xyz)

This repository hosts the **Securing the Digital Mine** intrusion detection system (IDS) suite. Built as a secure multi-tenant SaaS platform, it allows industrial mineral extraction organizations to sign up, deploy edge sensors, monitor traffic flows, and classify anomalies in real time.

---

## 1. System Pipeline & Telemetry Origin

```mermaid
flowchart TD
    subgraph Edge Layer (Raspberry Pi Node)
        A["Raw SPAN/Mirror Packets"] --> B["unesco-mine-sec-cli (Node)"]
        B --> C["BWOA Feature Pruner (10 Selected)"]
    end

    subgraph Central API Gateway (SaaS Dashboard)
        C -- "POST /api/external/analyze (Device Token)" --> D["ExternalApiController"]
        D --> E["FastAPI / Python Model Server (Port 8001)"]
        E --> F["CNN-LSTM Inference Evaluation"]
        F --> E
        E --> G["SQLite/Postgres Database Log"]
        G --> H["Livewire Live Monitor Feed (Port 8000)"]
    end
```

---

## 2. Core Architectural Pillars

### A. Binary Whale Optimization (BWOA)
Reduces processing complexity by pruning the default 41 NSL-KDD network variables down to **10 key features** (75.6% database footprint reduction). 
Continuous whale vector updates are mapped into binary search matrices using:
$$V(x) = \left| \frac{x}{\sqrt{1+x^2}} \right|$$

### B. Spatial-Temporal Deep Classification (CNN-LSTM)
- **1D CNN**: Analyzes spatial packet byte alignments.
- **LSTM**: Tracks temporal repetition and connection status histories over active packet windows.

### C. Enterprise Multi-Tenant Scoping
Dashboards, logs, and device keys are isolated strictly by `organization_id` inside the backend schemas.

---

## 3. Production Deployment (Render Blueprint)

This project features a `render.yaml` Blueprint file for automated SaaS deployments.

1. Create a **New Blueprint Instance** inside **Render.com**.
2. Connect this repository. Render will automatically provision:
   - A PostgreSQL Database cluster.
   - A private Python ML inference service (port 8001).
   - A public Laravel dashboard web service (port 8000).

---

## 4. Local Quick Start

### A. Run the Inference Service
```bash
python src/api_service.py
```

### B. Run the Local Sniffer Daemon
```bash
python src/sniffer_daemon.py
```

### C. Serve the Laravel Dashboard
```bash
cd dashboard
C:\xampp\php\php.exe artisan serve --port=8000
```

### D. Deploy the CLI Agent (npm, Yarn, or pnpm)
To stream network connection packets to your private tenant dashboard:
```bash
cd npm-packet-scanner

# Option A: npm
npm install && npm install -g ./

# Option B: Yarn
yarn install && yarn global add file:./

# Option C: pnpm
pnpm install && pnpm add -g ./

# Execute globally
unesco-mine-sec-cli
```

---

## 5. Contact & Support
For pilot inquiries, enterprise licensing, or technical assistance:
- **Email**: [hello@johnokyere.xyz](mailto:hello@johnokyere.xyz)
- **Author**: John Okyere
- **Track**: Track 3 - Smart Subsoil (Young Scientists Forum 2026)
