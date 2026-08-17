# AWS EC2 Cloud Deployment Guide: Securing the Digital Mine

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
| `503` from `/api/analyze` | TFLite model file not present on EC2 | Complete Section 8 below and restart the service. |

---

## 8. Load the Real TFLite Model on EC2

The API service reads model paths from environment variables. Set these in the systemd unit file so the service finds the correct files after deployment:

```bash
# Edit the systemd unit file
sudo nano /etc/systemd/system/mine-sec-api.service
```

Add the following `Environment=` lines in the `[Service]` block:

```ini
[Service]
Environment="MODEL_PATH=/opt/unesco-project/models/cnn_lstm_bwoa_v3_quantized.tflite"
Environment="FEATURE_MASK_PATH=/opt/unesco-project/data/features/nslkdd_bwoa_mask_v3.npy"
Environment="SCALER_PATH=/opt/unesco-project/data/processed/scaler.pkl"
```

Reload and restart:

```bash
sudo systemctl daemon-reload
sudo systemctl restart mine-sec-api.service

# Verify model is loaded:
curl http://localhost:8001/api/health
# Expected: {"status": "healthy", "model_ready": true, ...}
```

---

## 9. SSL / HTTPS Setup with Let's Encrypt (Auto-Renewal)

```bash
# Install Certbot and the Nginx plugin
sudo apt install certbot python3-certbot-nginx -y

# Obtain and install a certificate (replace with your domain)
sudo certbot --nginx -d yourdomain.com

# Test auto-renewal
sudo certbot renew --dry-run

# Add automatic renewal to root crontab
sudo crontab -e
# Add this line:
# 0 12 * * * /usr/bin/certbot renew --quiet
```

---

## 10. Monitoring and Alerting

### Check API response time
Create `/home/ubuntu/curl-format.txt`:
```
     time_total: %{time_total}s\n
```
Then:
```bash
curl -w "@/home/ubuntu/curl-format.txt" -o /dev/null -s http://localhost:8001/api/health
```

### Monitor real-time inference logs
```bash
# Follow live journal output for the API service
sudo journalctl -u mine-sec-api.service -f --since "1 hour ago"
```

### Set up downtime alerting via cron (every 5 minutes)
```bash
crontab -e
# Add the following line (replace with your Slack webhook URL):
*/5 * * * * curl -sf http://localhost:8001/api/health || curl -X POST https://hooks.slack.com/services/YOUR/WEBHOOK/URL -d '{"text":"Mine-Sec API is DOWN on EC2"}'
```

### Prometheus-ready health check endpoint
The `/api/health` endpoint returns JSON with `"status": "healthy"` -- compatible with most uptime monitoring tools (UptimeRobot, Grafana, CloudWatch synthetics).

---

## 11. API Rate Limiting with Nginx

Protect the public API from abuse and excessive traffic by adding rate limiting to the Nginx configuration. Edit `scripts/nginx_ec2.conf` or `/etc/nginx/sites-available/mine-sec`:

```nginx
# Define a rate limit zone: 100 requests/second per IP, stored in 10MB shared memory
limit_req_zone $binary_remote_addr zone=api:10m rate=100r/s;

server {
    listen 80;
    server_name yourdomain.com;

    location /api/ {
        # Allow bursts up to 200 requests; nodelay prevents queuing
        limit_req zone=api burst=200 nodelay;
        proxy_pass http://127.0.0.1:8001;
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
    }

    location / {
        proxy_pass http://127.0.0.1:8001;
    }
}
```

Reload Nginx after editing:
```bash
sudo nginx -t && sudo systemctl reload nginx
```

