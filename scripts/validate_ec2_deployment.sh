#!/usr/bin/env bash
# Validates AWS EC2 deployment readiness
set -e

echo "============================================================"
echo " AWS EC2 Deployment Validation (Dry Run)"
echo "============================================================"

ERRORS=0

PYTHON_BIN="python"
if ! command -v python &> /dev/null; then
    if command -v python3 &> /dev/null; then
        PYTHON_BIN="python3"
    fi
fi

check() {
    if [ "$2" = "PASS" ]; then echo "  PASS: $1"
    else echo "  FAIL: $1 -- $2"; ERRORS=$((ERRORS+1)); fi
}

# Check all required files for EC2 deployment
echo ""
echo "[1/6] Required EC2 deployment files..."
for f in scripts/deploy_ec2.sh scripts/mine-sec-api.service \
          scripts/nginx_ec2.conf src/api_service.py; do
    [ -f "$f" ] && check "$f exists" "PASS" || check "$f exists" "MISSING"
done

# Check systemd service file has real env vars
echo ""
echo "[2/6] Checking systemd service configuration..."
if grep -q "MODEL_PATH\|FEATURE_MASK_PATH" scripts/mine-sec-api.service 2>/dev/null; then
    check "mine-sec-api.service has MODEL_PATH env var" "PASS"
else
    check "mine-sec-api.service has MODEL_PATH env var" "MISSING - add Environment=MODEL_PATH=..."
fi

# Check nginx config has proxy_pass
echo ""
echo "[3/6] Checking Nginx configuration..."
if grep -q "proxy_pass.*8001" scripts/nginx_ec2.conf 2>/dev/null; then
    check "nginx_ec2.conf proxies to port 8001" "PASS"
else
    check "nginx_ec2.conf proxies to port 8001" "MISSING proxy_pass directive"
fi

# Check api_service.py PORT env var
echo ""
echo "[4/6] Checking API service port configuration..."
if grep -q 'os.environ.get.*PORT.*8001' src/api_service.py; then
    check "api_service.py reads PORT from env var" "PASS"
else
    check "api_service.py reads PORT from env var" "MISSING"
fi

# Check render.yaml
echo ""
echo "[5/6] Checking render.yaml blueprint..."
if [ -f "render.yaml" ]; then
    if grep -q "api-service-prod" render.yaml && grep -q "minesec-dashboard-prod" render.yaml; then
        check "render.yaml has both services defined" "PASS"
    else
        check "render.yaml service definitions" "INCOMPLETE"
    fi
else
    check "render.yaml exists" "MISSING"
fi

# API syntax check
echo ""
echo "[6/6] Python syntax check on deployment-critical files..."
for f in src/api_service.py src/sniffer_daemon.py; do
    $PYTHON_BIN -m py_compile "$f" 2>/dev/null && check "py_compile $f" "PASS" || check "py_compile $f" "SYNTAX ERROR"
done

echo ""
echo "============================================================"
echo " EC2 Validation: $ERRORS errors found"
[ "$ERRORS" -eq 0 ] && echo " STATUS: READY" || echo " STATUS: NOT READY"
echo "============================================================"
exit $ERRORS
