#!/usr/bin/env bash
# ==============================================================================
# Securing the Digital Mine - AWS EC2 Production Deployment Script
# Target OS: Ubuntu 22.04 LTS / Ubuntu 24.04 LTS (AWS EC2)
# Service: FastAPI Python ML Inference Server (Port 8001 -> Nginx 80/443)
# ==============================================================================

set -e

echo "======================================================================"
echo " Starting Securing the Digital Mine - AWS EC2 Deployment"
echo "======================================================================"

# 1. Update system packages
echo "[1/7] Updating system packages & installing prerequisites..."
sudo apt-get update -y
sudo apt-get install -y python3 python3-pip python3-venv nginx git curl ufw

# 2. Setup project directory
PROJECT_DIR="/opt/unesco-project"
echo "[2/7] Synchronizing repository files to ${PROJECT_DIR}..."
sudo mkdir -p "${PROJECT_DIR}"
sudo cp -r . "${PROJECT_DIR}/"
sudo chown -R $USER:$USER "${PROJECT_DIR}"

cd "${PROJECT_DIR}"
mkdir -p research/reports research/tables

# 3. Create Python Virtual Environment & Install Dependencies
echo "[3/7] Configuring Python virtual environment & dependencies..."
if [ ! -d "venv" ]; then
    python3 -m venv venv
fi

source venv/bin/activate
pip install --upgrade pip
pip install -r requirements.txt

# 4. Install & Register Systemd Service Unit
echo "[4/7] Installing Systemd Service (mine-sec-api.service)..."
sudo cp scripts/mine-sec-api.service /etc/systemd/system/mine-sec-api.service
sudo systemctl daemon-reload
sudo systemctl enable mine-sec-api.service
sudo systemctl restart mine-sec-api.service

# 5. Configure Nginx Reverse Proxy
echo "[5/7] Configuring Nginx reverse proxy..."
sudo cp scripts/nginx_ec2.conf /etc/nginx/sites-available/mine-sec.conf
sudo ln -sf /etc/nginx/sites-available/mine-sec.conf /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

sudo nginx -t
sudo systemctl restart nginx

# 6. Configure UFW Firewall & Port Ingress
echo "[6/7] Configuring UFW Firewall..."
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable

echo "======================================================================"
echo " Service Health Check Verification:"
curl -s http://localhost/api/health || curl -s http://localhost:8001/api/health
echo ""

# 7. Automated Empirical Benchmark & Academic Report Export
echo "[7/7] Running Automated Empirical Benchmark & Exporting Research Reports..."
sleep 2

# Detect Public IP
PUBLIC_IP=$(curl -s http://checkip.amazonaws.com || curl -s https://api.ipify.org || echo "51.21.219.29")

python3 scripts/benchmark_and_export.py --url http://127.0.0.1:8001 --samples 100 --output-dir research/reports

echo ""
echo "======================================================================"
echo " AWS EC2 Deployment & Empirical Research Benchmark Complete!"
echo "======================================================================"
echo " Direct Download Links for Research Paper Writing:"
echo "   Excel Workbook (.xlsx): http://${PUBLIC_IP}/api/export/results.xlsx"
echo "   CSV Inferences (.csv):  http://${PUBLIC_IP}/api/export/results.csv"
echo "   Summary Metrics (.csv): http://${PUBLIC_IP}/api/export/summary.csv"
echo "   Paper Tables (.md):     http://${PUBLIC_IP}/api/export/report.md"
echo "   LaTeX Tables (.tex):    http://${PUBLIC_IP}/api/export/tables.tex"
echo ""
echo " On-Instance File Locations:"
echo "   /opt/unesco-project/research/reports/ec2_benchmark_complete_results.xlsx"
echo "   /opt/unesco-project/research/reports/ec2_benchmark_detailed_inferences.csv"
echo "   /opt/unesco-project/research/reports/ec2_benchmark_summary.csv"
echo "   /opt/unesco-project/research/reports/ec2_benchmark_paper_tables.md"
echo "   /opt/unesco-project/research/reports/ec2_benchmark_tables.tex"
echo "======================================================================"
