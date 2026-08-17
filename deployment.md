# Deployment Overview - Securing the Digital Mine

This repository supports two production deployment targets. Full step-by-step instructions, systemd service configuration, TFLite model transfer, SSL setup, and troubleshooting guides are available in the dedicated documents below.

## Edge Deployment (Raspberry Pi 4/5)

For deploying the TFLite float16 quantized CNN-LSTM classifier directly on resource-constrained industrial gateways at mine sites:

**[docs/raspberry_pi_deployment.md](docs/raspberry_pi_deployment.md)**

Covers: prerequisites checklist, hardware setup, SPAN/TAP promiscuous mode, 1-command deployment script, execution modes (CLI / cron / systemd daemon), performance benchmarks, model transfer via `scp`, curl-based inference verification, hardware watchdog timer, and live alert forwarding to AWS EC2.

## Cloud Deployment (AWS EC2)

For deploying the Python inference microservice and Nginx reverse proxy on Amazon Web Services:

**[docs/aws_ec2_deployment.md](docs/aws_ec2_deployment.md)**

Covers: EC2 instance provisioning, security group rules, 1-command deployment script, systemd service management, endpoint verification, SSL/HTTPS with Let's Encrypt, TFLite environment variable configuration, monitoring and alerting, and Nginx rate limiting.
