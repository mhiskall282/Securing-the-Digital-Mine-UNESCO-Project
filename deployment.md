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

## 3. AWS EC2 Cloud Production Deployment (1-Command Automation)

For deploying the Python FastAPI Model Inference Server (`src/api_service.py`) and OT Sniffer Daemon on an AWS EC2 instance:

### Recommended Instance & Setup:
- **AMI**: Ubuntu 22.04 LTS (HVM)
- **Instance Sizing**: `t3.medium` (2 vCPU, 4 GiB RAM)
- **Security Group**: Inbound TCP ports `22` (SSH), `80` (HTTP), `443` (HTTPS).

### One-Command Deployment:
Connect to your EC2 instance via SSH and execute:
```bash
git clone https://github.com/mhiskall282/unesco-project.git
cd unesco-project
chmod +x scripts/deploy_ec2.sh
./scripts/deploy_ec2.sh
```

For comprehensive step-by-step instructions, Nginx SSL configuration, and systemd service management, see the dedicated [AWS EC2 Deployment Guide](file:///c:/Users/user/Desktop/unesco-project/docs/aws_ec2_deployment.md).

---

## 4. Raspberry Pi 4/5 & Industrial Edge Deployment

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

## 5. Local & Containerized Environment Setup (SQLite Resilience)

### Automated Database Initialization & Resilience
When executing locally or inside a Docker container (e.g. Render production instance):
- **Missing DB File**: `AppServiceProvider.php` automatically detects missing SQLite database files and creates `database/database.sqlite` on disk.
- **Auto-Migration & Seeding**: `AppServiceProvider.php` inspects `Schema::hasTable('users')`. If tables are absent, it programmatically invokes `php artisan migrate --force` and `php artisan db:seed --force` prior to servicing requests.
- **Container Permissions**: The container startup script in `Dockerfile` enforces permissions (`chmod -R 777 storage bootstrap/cache database`).

### Default Seeded Administrative Credentials
When deploying with SQLite or executing database seeds:
- **Admin User**: `admin@npontu.local` | Password: `password`
- **Lead User**: `lead@npontu.local` | Password: `password`
- **Agent User**: `agent@npontu.local` | Password: `password`

---

## 6. Local Developer Manual Setup

For local testing on Windows/Linux:

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

3. **Provision Database**:
   ```bash
   php artisan migrate --force
   php artisan db:seed --force
   php artisan key:generate
   ```

4. **Compile Assets & Clear Cache**:
   ```bash
   npm run build
   php artisan view:clear
   ```

5. **Start Services**:
   - **FastAPI Inference Server**: `python src/api_service.py`
   - **Python Sniffer Daemon**: `python src/sniffer_daemon.py`
   - **Laravel Web Dashboard**: `php artisan serve --port=8000`

6. **Access Dashboard**:
   Open `http://localhost:8000` in your web browser.
