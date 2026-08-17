# Raspberry Pi Edge Deployment Guide - Securing the Digital Mine

This guide provides step-by-step instructions for deploying the **Securing the Digital Mine** Edge Classifier, BWOA Feature Pruner, and Packet Sniffer Agent (`unesco-mine-sec-cli` / `src/sniffer_daemon.py`) on resource-constrained industrial edge gateways (Raspberry Pi 4B / Pi 5).

---

## 0. Prerequisites Checklist

Complete all items before proceeding to Section 1.

- [ ] Raspberry Pi 4B (4GB/8GB RAM) or Pi 5 running 64-bit Raspberry Pi OS (Debian 12 Bookworm)
- [ ] Static IP address assigned to the Pi on the local OT network (or DHCP reservation)
- [ ] SSH access confirmed: `ssh pi@<PI_IP>` works from your development machine
- [ ] Secondary NIC (`eth1`) physically connected to the SCADA switch SPAN/TAP mirror port
- [ ] Python 3.11+ available: `python3 --version`
- [ ] `libpcap-dev` installed: `sudo apt install libpcap-dev -y`
- [ ] At least 500 MB free on the SD card: `df -h /`
- [ ] Trained quantized model available on your development machine:
  - `models/cnn_lstm_bwoa_v3_quantized.tflite` (0.82 MB)
  - `data/features/nslkdd_bwoa_mask_v3.npy`
  - `data/processed/scaler.pkl`
  - (Run `notebooks/00_colab_setup_and_train.ipynb` on Google Colab if you do not have these yet)

---

## 1. Architectural Overview

The Raspberry Pi Edge Gateway operates directly at low-power SCADA extraction zones or mine shafts, capturing real-time OT network telemetry and executing local TFLite inference or streaming reduced feature vectors:

```mermaid
flowchart TD
    subgraph Industrial OT Network (SCADA Switch)
        A["Raw Modbus / DNP3 / OPC-UA Traffic"] --> B["SPAN / Mirror Port (eth1 / eth0)"]
    end

    subgraph Raspberry Pi Edge Gateway (Pi 4 / Pi 5)
        B --> C["Passive Promiscuous Sniffer Daemon"]
        C --> D["BWOA Feature Pruner (10 Selected Features)"]
        D --> E{"Inference Execution"}
        E -- "Local Low-Latency Inference" --> F["Quantized TFLite Float16 Model (0.76ms)"]
        E -- "Stream Telemetry to Cloud/SaaS" --> G["unesco-mine-sec-cli REST Stream"]
    end

    G -- "POST /api/external/analyze" --> H["Central Dashboard / AWS EC2 Microservice"]
```

---

## 2. Hardware & OS Specifications

### Recommended Edge Gateway Specs
* **Hardware**: Raspberry Pi 4B (4GB/8GB RAM) or Raspberry Pi 5
* **OS**: Raspberry Pi OS 64-bit (Debian 11 Bullseye / Debian 12 Bookworm)
* **Storage**: 16 GB Class 10 MicroSD Card or NVMe SSD HAT
* **Network Interfaces**:
  - `eth0`: Management network (SSH, internet access, SaaS API streaming)
  - `eth1` (USB Ethernet Dongle / HAT): Secondary interface connected to SCADA SPAN/TAP mirror port.

---

## 3. Industrial SPAN/TAP Mirror Port Configuration

To passively sniff SCADA network traffic without interfering with active control flows:

1. Connect `eth1` to the mirror port (SPAN) of the industrial Ethernet switch.
2. Enable promiscuous mode:
   ```bash
   sudo ip link set eth1 promisc on
   ```
3. Verify promiscuous flag (`PROMISC`):
   ```bash
   ip link show eth1
   ```

---

## 4. Automated 1-Command Edge Deployment

Connect to your Raspberry Pi via SSH or terminal and run:

```bash
# 1. Clone the repository
git clone https://github.com/mhiskall282/unesco-project.git
cd unesco-project

# 2. Make deployment script executable
chmod +x scripts/deploy_raspberry_pi.sh

# 3. Execute automated provisioning
./scripts/deploy_raspberry_pi.sh
```

### What `deploy_raspberry_pi.sh` Performs Automatically:
1. Installs Python 3, `pip`, `nodejs`, `npm`, `libpcap-dev`, and network tools.
2. Automatically sets promiscuous mode on secondary/primary network interfaces.
3. Builds and installs `unesco-mine-sec-cli` globally.
4. Registers and enables `mine-sec-agent.service` under `systemd` to run the daemon continuously on boot.

---

## 5. Execution Modes

### Mode A: Global Interactive CLI Agent (`@mhiskall282/unesco-mine-sec-cli`)
Package Registry: [GitHub Packages](https://github.com/mhiskall282/Securing-the-Digital-Mine-UNESCO-Project/pkgs/npm/unesco-mine-sec-cli)

Install and launch the interactive terminal UI to stream connection telemetry to your central dashboard:

```bash
# 1. Point @mhiskall282 scope to GitHub Packages registry
npm config set @mhiskall282:registry https://npm.pkg.github.com

# 2. Run directly via npx or install globally
npx @mhiskall282/unesco-mine-sec-cli

# Or install globally
npm install -g @mhiskall282/unesco-mine-sec-cli
unesco-mine-sec-cli
```

### Mode B: Intermittent Low-Power Cron Pass (`--cron`)
For solar/battery-powered remote extraction sites or bandwidth-restricted nodes:

```bash
# Run a single 5-flow evaluation pass
python src/sniffer_daemon.py --cron
```

#### Crontab Schedule (Every 15 Minutes):
```bash
crontab -e
```
Add the cron rule:
```cron
*/15 * * * * cd /opt/unesco-project && /opt/unesco-project/venv/bin/python src/sniffer_daemon.py --cron >> /var/log/sniffer_cron.log 2>&1
```

### Mode C: Continuous Systemd Background Daemon
Manage the edge daemon service:

```bash
# Check service status
sudo systemctl status mine-sec-agent.service

# Restart service
sudo systemctl restart mine-sec-agent.service

# Inspect live logs
sudo journalctl -u mine-sec-agent.service -f
```

---

## 6. Performance & Edge Benchmarks

Evaluating on KDDTest+ / SWaT datasets on Raspberry Pi 4B (1.5GHz ARM Cortex-A72):

| Metric | Baseline (Keras) | Quantized Float16 (TFLite) | Unit | Status |
| :--- | :---: | :---: | :---: | :---: |
| **Model Size** | 4.88 MB | **0.82 MB** | Megabytes | **83.1% Reduction** |
| **Mean Latency** | 82.32 ms | **0.76 ms** | Milliseconds | **108x Speedup** |
| **P95 Latency** | 182.55 ms | **1.10 ms** | Milliseconds | **PASS (< 100ms)** |
| **RAM Footprint** | ~650 MB | **290.31 MB** | Megabytes | **PASS (< 1024MB)** |

---

## 7. Troubleshooting & Diagnostics

| Problem | Cause | Solution |
| :--- | :--- | :--- |
| `Permission denied (raw socket)` | Sniffer requires root privileges for `libpcap` | Run daemon with `sudo` or execute via systemd service (`User=root`). |
| `No network flows captured` | Network interface not in promiscuous mode or wrong NIC selected | Run `sudo ip link set eth1 promisc on` and verify with `tcpdump -i eth1 -c 5`. |
| `tflite-runtime import error` | Python wheel mismatch on ARM64 | Run `pip install tflite-runtime` or use precompiled TFLite wheels for Raspberry Pi OS 64-bit. |
| `503 Service Unavailable` from `/api/analyze` | TFLite model file missing | Complete Section 8 (model transfer) or set `MODEL_PATH` env var. |

---

## 8. Model Transfer to Raspberry Pi

After training on Google Colab (or locally), transfer the quantized model and supporting files from your development machine to the Pi:

```bash
# From your development machine, transfer the quantized model:
scp models/cnn_lstm_bwoa_v3_quantized.tflite pi@<PI_IP>:/opt/unesco-project/models/
scp data/features/nslkdd_bwoa_mask_v3.npy pi@<PI_IP>:/opt/unesco-project/data/features/
scp data/processed/scaler.pkl pi@<PI_IP>:/opt/unesco-project/data/processed/

# Verify transfer on Pi:
ssh pi@<PI_IP> "ls -lh /opt/unesco-project/models/*.tflite"
```

Expected output:
```
-rw-r--r-- 1 pi pi 841K Aug 17 10:22 /opt/unesco-project/models/cnn_lstm_bwoa_v3_quantized.tflite
```

After transfer, restart the API service so it loads the new model:
```bash
sudo systemctl restart mine-sec-api.service
```

---

## 9. Verify Inference is Working

### Health check
```bash
curl http://localhost:8001/api/health
```
Expected response:
```json
{"status": "healthy", "model_ready": true, "model_version": "v3.0.0-tflite-quantized", "framework": "TFLite Float16"}
```

### Feature list
```bash
curl http://localhost:8001/api/features
```

### Test with a simulated DoS flow (high serror_rate)
```bash
curl -X POST http://localhost:8001/api/analyze \
  -H "Content-Type: application/json" \
  -d '{
    "protocol_type": 1,
    "service": 21,
    "flag": 10,
    "src_bytes": 1032,
    "hot": 0,
    "su_attempted": 0,
    "serror_rate": 0.88,
    "same_srv_rate": 0.95,
    "diff_srv_rate": 0.05,
    "dst_host_diff_srv_rate": 0.02
  }'
```
Expected response:
```json
{"prediction": "DoS", "confidence": ~96, "latency_ms": ~0.76, "model_version": "v3.0.0-tflite-quantized"}
```

---

## 10. Hardware Watchdog Timer

Enable the Raspberry Pi hardware watchdog to auto-reboot the device within 15 seconds if the CPU hangs or the kernel freezes:

```bash
# Enable hardware watchdog in firmware config
echo 'dtparam=watchdog=on' | sudo tee -a /boot/config.txt

# Install and configure the watchdog daemon
sudo apt install watchdog -y
sudo sed -i 's/#watchdog-device/watchdog-device/' /etc/watchdog.conf
sudo sed -i 's/#max-load-1/max-load-1/' /etc/watchdog.conf

# Enable and start
sudo systemctl enable watchdog
sudo systemctl start watchdog

# Verify
sudo systemctl status watchdog
```

The Pi will auto-reboot within 15 seconds if the CPU freezes. The `mine-sec-api.service` is configured with `Restart=always` so it recovers automatically after the reboot.

---

## 11. Forward Alerts to AWS EC2

Any flow classified as non-Normal by the TFLite model can be forwarded in real time to a central AWS EC2 endpoint for logging, dashboarding, and aggregated threat intelligence.

### Option A: Inline forwarding from `sniffer_daemon.py`

Add the following snippet inside your `sniffer_daemon.py` alert handler (wherever a prediction is written to the database):

```python
import json, subprocess, tempfile, os

def forward_alert_to_ec2(alert_payload: dict, ec2_domain: str, device_token: str):
    """Forward a non-Normal inference result to the central EC2 dashboard."""
    with tempfile.NamedTemporaryFile(mode='w', suffix='.json', delete=False) as tmp:
        json.dump(alert_payload, tmp)
        tmp_path = tmp.name
    subprocess.Popen([
        "curl", "-s", "-X", "POST",
        f"https://{ec2_domain}/api/analyze",
        "-H", f"Authorization: Bearer {device_token}",
        "-H", "Content-Type: application/json",
        "-d", f"@{tmp_path}",
    ])
```

### Option B: Cron-based batch forwarding

Save the latest alert to `/tmp/latest_alert.json` inside your daemon, then schedule:

```bash
# /etc/cron.d/mine-sec-alert-forward
# Forward the latest alert every minute if it is non-Normal
* * * * * pi [ -f /tmp/latest_alert.json ] && \
  curl -s -X POST https://<EC2_DOMAIN>/api/analyze \
    -H "Authorization: Bearer <DEVICE_TOKEN>" \
    -H "Content-Type: application/json" \
    -d @/tmp/latest_alert.json
```

### Monitor forwarding logs via systemd journal

```bash
# Real-time alert stream from the Pi edge agent
sudo journalctl -u mine-sec-agent.service -f --since "1 hour ago"

# Filter only non-Normal predictions
sudo journalctl -u mine-sec-agent.service --since "1 hour ago" | grep -v '"prediction": "Normal"'
```

