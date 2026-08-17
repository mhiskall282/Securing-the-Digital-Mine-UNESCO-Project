#!/usr/bin/env bash
# ==============================================================================
# Securing the Digital Mine - Raspberry Pi Edge Gateway Deployment Script
# Target OS: Raspberry Pi OS 64-bit (Debian Bullseye / Bookworm) on Pi 4 / Pi 5
# Component: TFLite Quantized Edge Classifier & unesco-mine-sec-cli Sniffer Agent
# ==============================================================================

set -e

echo "======================================================================"
echo " Starting Securing the Digital Mine - Raspberry Pi Edge Deployment"
echo "======================================================================"

# 1. Update System & Install Hardware Dependencies
echo "[1/6] Installing Raspberry Pi system dependencies & libpcap..."
sudo apt-get update -y
sudo apt-get install -y python3-pip python3-dev python3-venv nodejs npm libpcap-dev git curl ufw

# 2. Configure Industrial Network NIC Promiscuous Mode (SPAN / TAP Mirror Port)
echo "[2/6] Configuring network interface promiscuous mode..."
PRIMARY_NIC=$(ip route show default | awk '/default/ {print $5}' | head -n 1)
MONITOR_NIC="eth1"

if ip link show "$MONITOR_NIC" > /dev/null 2>&1; then
    echo "Enabling promiscuous mode on secondary SCADA interface: ${MONITOR_NIC}..."
    sudo ip link set "$MONITOR_NIC" promisc on
else
    echo "Secondary interface ${MONITOR_NIC} not detected. Enabling promiscuous mode on primary: ${PRIMARY_NIC}..."
    sudo ip link set "$PRIMARY_NIC" promisc on
fi

# 3. Setup Project Directory
PROJECT_DIR="/opt/unesco-project"
echo "[3/6] Setting up project directory at ${PROJECT_DIR}..."

if [ ! -d "${PROJECT_DIR}" ]; then
    sudo mkdir -p "${PROJECT_DIR}"
    sudo cp -r . "${PROJECT_DIR}/"
    sudo chown -R $USER:$USER "${PROJECT_DIR}"
fi

cd "${PROJECT_DIR}"

# 4. Install Python TFLite Runtime & Dependencies
echo "[4/6] Installing Python TFLite runtime dependencies..."
if [ ! -d "venv" ]; then
    python3 -m venv venv
fi

source venv/bin/activate
pip install --upgrade pip
pip install -r requirements.txt || true
pip install requests

# 5. Build & Install Global CLI Packet Scanner Agent (unesco-mine-sec-cli)
echo "[5/6] Building and installing unesco-mine-sec-cli agent..."
if [ -d "npm-packet-scanner" ]; then
    cd npm-packet-scanner
    npm install
    sudo npm install -g ./
    cd "${PROJECT_DIR}"
fi

# 6. Install & Register Systemd Edge Daemon Service
echo "[6/6] Registering Systemd Edge Agent Service (mine-sec-agent.service)..."
sudo cp scripts/mine-sec-agent.service /etc/systemd/system/mine-sec-agent.service
sudo systemctl daemon-reload
sudo systemctl enable mine-sec-agent.service
sudo systemctl restart mine-sec-agent.service

echo "======================================================================"
echo " Raspberry Pi Edge Deployment Complete!"
echo " Service Status:"
sudo systemctl status mine-sec-agent.service --no-pager | head -n 12
echo ""
echo " Global CLI Agent installed: unesco-mine-sec-cli"
echo " Run 'unesco-mine-sec-cli' interactively or use 'sudo systemctl status mine-sec-agent'."
echo "======================================================================"
