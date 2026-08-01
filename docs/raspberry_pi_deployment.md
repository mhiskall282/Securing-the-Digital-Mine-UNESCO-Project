# Raspberry Pi Edge Deployment Guide — Securing the Digital Mine

This guide provides step-by-step instructions for deploying the **Securing the Digital Mine** Edge Classifier, BWOA Feature Pruner, and Packet Sniffer Agent (`unesco-mine-sec-cli` / `src/sniffer_daemon.py`) on resource-constrained industrial edge gateways (Raspberry Pi 4B / Pi 5).

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

### Mode A: Global Interactive CLI Agent (`unesco-mine-sec-cli`)
Launch the interactive terminal UI to stream connection telemetry to your central dashboard:

```bash
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
