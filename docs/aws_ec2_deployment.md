# AWS EC2 Cloud Deployment Guide — Securing the Digital Mine

This guide provides end-to-end instructions for deploying the **Securing the Digital Mine** Python ML Inference Microservice (`src/api_service.py`) and OT Sniffer Daemon on Amazon Web Services (AWS EC2).

---

## 1. Architectural System Overview

The AWS EC2 Cloud Deployment hosts the primary high-throughput inference pipeline:

```mermaid
flowchart TD
    subgraph Edge Layer (SCADA / Mining Site)
        A["Industrial OT Packets (Modbus/DNP3/OPC-UA)"] --> B["unesco-mine-sec-cli / Sniffer Daemon"]
        B --> C["BWOA Feature Extractor (10 Selected)"]
    end

    subgraph AWS Cloud Layer (EC2 Instance)
        C -- "HTTP/HTTPS (Port 80/443)" --> D["Nginx Reverse Proxy"]
        D -- "Proxy Pass (Port 8001)" --> E["FastAPI ML Inference Service (mine-sec-api.service)"]
        E --> F["TFLite Float16 CNN-LSTM Classifier"]
        F --> E
        E --> G["JSON Inference Response (Prediction, Confidence, Latency)"]
    end
```

---

## 2. AWS EC2 Instance Provisioning

### Recommended Hardware Specifications
* **AMI**: Ubuntu 22.04 LTS (HVM), SSD Volume Type (`ami-0c7217cdde317cfec` or region equivalent)
* **Instance Type**: `t3.medium` (2 vCPU, 4 GiB RAM) or `t3.large` for high-concurrency SCADA traffic
* **Storage**: 20 GB General Purpose SSD (gp3)

### AWS Security Group Configuration
In the AWS Management Console under **EC2 Security Groups**, create a security group `mine-sec-ec2-sg` with the following inbound rules:

| Type | Protocol | Port Range | Source | Description |
| :--- | :--- | :--- | :--- | :--- |
| **SSH** | TCP | 22 | `My IP` (e.g. `203.0.113.4/32`) | Secure Administrative SSH Access |
| **HTTP** | TCP | 80 | `0.0.0.0/0` | Public Ingress / Dashboard Ingress |
| **HTTPS** | TCP | 443 | `0.0.0.0/0` | Secure SSL Ingress |
| **Custom TCP** | TCP | 8001 | `VPC Subnet` / `172.31.0.0/16` | Internal API Microservice Ingress (Optional) |

---

## 3. Automated 1-Command Deployment

Once connected to your EC2 instance via SSH:

```bash
# 1. Clone the repository
git clone https://github.com/mhiskall282/unesco-project.git
cd unesco-project

# 2. Make deployment script executable
chmod +x scripts/deploy_ec2.sh

# 3. Execute automated provisioning
./scripts/deploy_ec2.sh
```

### What `deploy_ec2.sh` Performs Automatically:
1. Installs Python 3.11, `pip`, `virtualenv`, `nginx`, and `ufw` firewall tools.
2. Copies repository source code to `/opt/unesco-project`.
3. Creates a Python virtual environment (`/opt/unesco-project/venv`) and installs `requirements.txt`.
4. Registers and starts the `mine-sec-api.service` daemon under `systemd`.
5. Configures Nginx as a production reverse proxy mapping port 80/443 to `http://127.0.0.1:8001`.
6. Enables UFW firewall rules for SSH and Nginx traffic.

---

## 4. Systemd Process Management & Lifecycle Commands

The microservice runs continuously as a background system service managed by `systemd`.

### Service Management Commands:
```bash
# Check service status
sudo systemctl status mine-sec-api.service

# Restart the service
sudo systemctl restart mine-sec-api.service

# Stop the service
sudo systemctl stop mine-sec-api.service

# View real-time application logs
sudo journalctl -u mine-sec-api.service -f
```

---

## 5. Verification & Endpoint Testing

### Health Check Evaluation
Run the health check endpoint:
```bash
curl -i http://localhost/api/health
```
**Expected Response (`200 OK`)**:
```json
{
  "status": "healthy",
  "model_version": "v3.0.0-quantized",
  "framework": "TFLite Float16"
}
```

### Anomaly Inference Evaluation
Send a sample BWOA feature telemetry payload:
```bash
curl -X POST http://localhost/api/analyze \
  -H "Content-Type: application/json" \
  -d '{
    "protocol_type": "tcp",
    "service": "http",
    "flag": "SF",
    "src_bytes": 1024,
    "hot": 0,
    "su_attempted": 0,
    "serror_rate": 0.85,
    "same_srv_rate": 0.15,
    "diff_srv_rate": 0.0,
    "dst_host_diff_srv_rate": 0.0
  }'
```

**Expected JSON Output**:
```json
{
  "prediction": "DoS",
  "confidence": 97.42,
  "features_triggered": [
    "high_serror_rate"
  ],
  "latency_ms": 0.7642
}
```

---

## 6. SSL / TLS Certificate Setup (Let's Encrypt / Certbot)

To secure the EC2 API endpoint with HTTPS using a custom domain:

```bash
# Install Certbot and Nginx plugin
sudo apt-get install -y certbot python3-certbot-nginx

# Request and install SSL certificate
sudo certbot --nginx -d your-api-domain.com

# Auto-renewal verification
sudo certbot renew --dry-run
```

---

## 7. Troubleshooting & Logs

| Symptom | Cause | Resolution |
| :--- | :--- | :--- |
| `Connection refused (502 Bad Gateway)` | `api_service.py` process stopped or crashed | Run `sudo systemctl restart mine-sec-api` and inspect `journalctl -u mine-sec-api.service -n 50`. |
| `Timeout / No response from public IP` | AWS Security Group inbound port 80/443 blocked | Check EC2 Security Group rules; ensure `0.0.0.0/0` is allowed for HTTP (port 80). |
| `Permission denied` on `deploy_ec2.sh` | Missing execute permission | Run `chmod +x scripts/deploy_ec2.sh`. |
