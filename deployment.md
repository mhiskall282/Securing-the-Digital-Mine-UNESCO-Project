# Production & Edge Deployment Guide - MineSec SaaS & IoT Suite

This guide provides comprehensive, step-by-step instructions for deploying the **MineSec Multi-Tenant Intrusion Detection System (IDS)** across cloud platforms (Render.com, AWS) and resource-constrained edge gateways (Raspberry Pi 4/5).

---

## 1. Architectural System Overview

The system consists of 4 decoupled, production-ready modules:

1. **Central SaaS Dashboard** (`dashboard/`): Laravel 12 + Livewire 3 real-time web portal with multi-tenancy, threat mitigation playbooks, and account administration.
2. **FastAPI Deep Model Server** (`src/api_service.py`): Python microservice running quantized Float16 CNN-LSTM neural network inference on port 8001.
3. **Embedded Sniffer Daemon** (`src/sniffer_daemon.py`): Real-time network interface listener supporting continuous monitoring and intermittent cron execution (`--cron` / `--once`).
4. **Lightweight NPM Scanner CLI** (`npm-packet-scanner/`): Portable Node.js CLI tool (`unesco-mine-sec-cli`) with BWOA 10-feature pruning.

---

## 2. Render.com PaaS Cloud Production Deployment (1-Click Blueprint)

Deploy the central infrastructure automatically using Render's Infrastructure-as-Code Blueprint (`render.yaml`).

### Deployment Steps:

1. **Fork or Push Code to GitHub**:
   Ensure your repository contains the updated `render.yaml` blueprint at the root directory.

2. **Log in to Render.com**:
   Go to [dashboard.render.com](https://dashboard.render.com) and navigate to **Blueprints**.

3. **Connect Repository**:
   Click **New Blueprint Instance**, select your GitHub repository, and click **Connect**.

4. **Automated Resource Provisioning**:
   Render automatically provisions:
   - **PostgreSQL Database** (`minesec-db`): Managed database storing tenant accounts, devices, and live network flows.
   - **Python Inference Service** (`api-service`): Runs `python src/api_service.py` on port 8001.
   - **Laravel Dashboard Service** (`minesec-dashboard`): Builds Vite assets (`npm run build`), executes migrations (`php artisan migrate --force`), and serves the web portal.

5. **Verify Central Web Gateway**:
   Once provisioning completes, open your assigned HTTPS domain (e.g., `https://minesec-dashboard.onrender.com`).

---

## 3. Raspberry Pi 4/5 & Industrial Edge Deployment

Deploying in low-power SCADA extraction zones or mine shafts requires running the BWOA scanner on Raspberry Pi gateways.

### Step 1: Raspberry Pi OS Dependencies
Run the following commands on the Raspberry Pi terminal:
```bash
sudo apt-get update && sudo apt-get install -y python3-pip python3-dev nodejs npm
pip3 install tflite-runtime requests
```

### Step 2: Industrial Network Mirroring (SPAN/TAP Port)
1. Connect the Raspberry Pi secondary network interface card (NIC) to the mirror port (SPAN) of the industrial SCADA switch.
2. Ensure passive promiscuous mode is enabled:
   ```bash
   sudo ip link set eth1 promisc on
   ```

### Step 3: Install & Launch the Global CLI Scanner Client
Install the packet scanner package globally:
```bash
cd npm-packet-scanner
npm install -g ./
```

Launch the interactive scanner CLI:
```bash
unesco-mine-sec-cli
```
Input your central dashboard URL (`https://minesec-dashboard.onrender.com`), select `eth1`, and enter your Device Node API key generated from the dashboard.

---

## 4. Intermittent Cron Job Sniffer Setup

For battery-powered or bandwidth-restricted remote nodes, run the sniffer daemon in intermittent cron mode:

### Running an Intermittent Pass:
```bash
python src/sniffer_daemon.py --cron
```
*Executes a 5-sample flow evaluation burst into the database and exits cleanly.*

### Scheduling via Linux Crontab:
Edit the crontab table:
```bash
crontab -e
```
Add an entry to run every 15 minutes:
```cron
*/15 * * * * cd /home/pi/unesco-project && python3 src/sniffer_daemon.py --cron >> /var/log/sniffer_cron.log 2>&1
```

---

## 5. Local Developer Environment Setup (SQLite)

For local development and testing on Windows/Linux:

1. **Initialize Environment**:
   ```bash
   cd dashboard
   copy .env.example .env
   ```

2. **Install PHP & Node Dependencies**:
   ```bash
   composer install
   npm install
   ```

3. **Provision SQLite Database**:
   ```bash
   powershell -Command "New-Item -ItemType File -Path 'database/database.sqlite' -Force"
   php artisan migrate:fresh --force
   php artisan key:generate
   ```

4. **Compile Production Assets**:
   ```bash
   npm run build
   php artisan view:clear
   ```

5. **Start Local Servers**:
   - **FastAPI Model Server**: `python src/api_service.py`
   - **Python Sniffer Daemon**: `python src/sniffer_daemon.py`
   - **Laravel Web Dashboard**: `php artisan serve --port=8000`

6. **Access Dashboard**:
   Open `http://localhost:8000` in your web browser.
