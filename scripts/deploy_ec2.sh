#!/usr/bin/env bash
# ==============================================================================
# Securing the Digital Mine — AWS EC2 Production Deployment Script
# Target OS: Ubuntu 22.04 LTS / Ubuntu 24.04 LTS (AWS EC2)
# Service: FastAPI Python ML Inference Server (Port 8001 -> Nginx 80/443)
# ==============================================================================

set -e

echo "======================================================================"
echo " Starting Securing the Digital Mine — AWS EC2 Deployment"
echo "======================================================================"

# 1. Update system packages
echo "[1/6] Updating system packages & installing prerequisites..."
sudo apt-get update -y
sudo apt-get install -y python3 python3-pip python3-venv nginx git curl ufw

# 2. Setup project directory
PROJECT_DIR="/opt/unesco-project"
echo "[2/6] Setting up project directory at ${PROJECT_DIR}..."

if [ ! -d "${PROJECT_DIR}" ]; then
    echo "Copying repository files to ${PROJECT_DIR}..."
    sudo mkdir -p "${PROJECT_DIR}"
    sudo cp -r . "${PROJECT_DIR}/"
    sudo chown -R $USER:$USER "${PROJECT_DIR}"
fi

cd "${PROJECT_DIR}"

# 3. Create Python Virtual Environment & Install Dependencies
echo "[3/6] Configuring Python virtual environment..."
if [ ! -d "venv" ]; then
    python3 -m venv venv
fi

source venv/bin/activate
pip install --upgrade pip
pip install -r requirements.txt

# 4. Install & Register Systemd Service Unit
echo "[4/6] Installing Systemd Service (mine-sec-api.service)..."
sudo cp scripts/mine-sec-api.service /etc/systemd/system/mine-sec-api.service
sudo systemctl daemon-reload
sudo systemctl enable mine-sec-api.service
sudo systemctl restart mine-sec-api.service

# 5. Configure Nginx Reverse Proxy
echo "[5/6] Configuring Nginx reverse proxy..."
sudo cp scripts/nginx_ec2.conf /etc/nginx/sites-available/mine-sec.conf
sudo ln -sf /etc/nginx/sites-available/mine-sec.conf /etc/nginx/sites-enabled/
sudo rm -f /etc/nginx/sites-enabled/default

sudo nginx -t
sudo systemctl restart nginx

# 6. Configure UFW Firewall & Port Ingress
echo "[6/6] Configuring UFW Firewall..."
sudo ufw allow OpenSSH
sudo ufw allow 'Nginx Full'
sudo ufw --force enable

echo "======================================================================"
echo " AWS EC2 Deployment Complete!"
echo " Service Status:"
sudo systemctl status mine-sec-api.service --no-pager | head -n 12
echo ""
echo " Health Check Verification:"
curl -s http://localhost/api/health || curl -s http://localhost:8001/api/health
echo ""
echo "======================================================================"
