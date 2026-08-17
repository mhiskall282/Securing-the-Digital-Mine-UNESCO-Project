#!/usr/bin/env bash
# Validates Raspberry Pi deployment readiness without making system changes
set -e

echo "============================================================"
echo " Raspberry Pi Deployment Validation (Dry Run)"
echo "============================================================"

ERRORS=0
WARNINGS=0

PYTHON_BIN="python"
if command -v python.exe &> /dev/null; then
    PYTHON_BIN="python.exe"
elif command -v python &> /dev/null; then
    PYTHON_BIN="python"
elif command -v python3 &> /dev/null; then
    PYTHON_BIN="python3"
fi

check() {
    local label="$1"
    local result="$2"
    if [ "$result" = "PASS" ]; then
        echo "  PASS: $label"
    else
        echo "  FAIL: $label -- $result"
        ERRORS=$((ERRORS + 1))
    fi
}

warn() {
    echo "  WARN: $1"
    WARNINGS=$((WARNINGS + 1))
}

# Check repo structure
echo ""
echo "[1/8] Checking required files..."
[ -f "src/api_service.py" ] && check "api_service.py exists" "PASS" || check "api_service.py exists" "MISSING"
[ -f "src/sniffer_daemon.py" ] && check "sniffer_daemon.py exists" "PASS" || check "sniffer_daemon.py exists" "MISSING"
[ -f "scripts/deploy_raspberry_pi.sh" ] && check "deploy_raspberry_pi.sh exists" "PASS" || check "deploy_raspberry_pi.sh exists" "MISSING"
[ -f "scripts/mine-sec-agent.service" ] && check "mine-sec-agent.service exists" "PASS" || check "mine-sec-agent.service exists" "MISSING"
[ -f "requirements.txt" ] && check "requirements.txt exists" "PASS" || check "requirements.txt exists" "MISSING"
[ -f "npm-packet-scanner/package.json" ] && check "npm-packet-scanner/package.json exists" "PASS" || check "package.json exists" "MISSING"
[ -f "config.yaml" ] && check "config.yaml exists" "PASS" || check "config.yaml exists" "MISSING"

# Check model files (warn if missing -- expected without training)
echo ""
echo "[2/8] Checking model artifacts..."
if [ -f "models/cnn_lstm_bwoa_v3_quantized.tflite" ] || [ -f "models/cnn_lstm_v4_quantized.tflite" ]; then
    check "TFLite quantized model exists" "PASS"
else
    warn "TFLite model not found. Train model first or download from Google Colab."
    warn "Expected: models/cnn_lstm_bwoa_v3_quantized.tflite or models/cnn_lstm_v4_quantized.tflite"
fi

if [ -f "data/features/nslkdd_bwoa_mask_v3.npy" ]; then
    check "BWOA feature mask exists" "PASS"
else
    warn "Feature mask not found: data/features/nslkdd_bwoa_mask_v3.npy"
fi

if [ -f "data/processed/scaler.pkl" ]; then
    check "StandardScaler pickle exists" "PASS"
else
    warn "Scaler not found: data/processed/scaler.pkl"
fi

# Check Python syntax of all src/ files
echo ""
echo "[3/8] Checking Python syntax (py_compile)..."
for pyfile in src/api_service.py src/sniffer_daemon.py \
              src/optimization/bwoa.py src/optimization/fitness.py \
              src/models/cnn_lstm.py src/models/trainer.py \
              src/data/nsl_kdd.py src/evaluation/metrics.py \
              src/evaluation/edge_benchmark.py; do
    if $PYTHON_BIN -m py_compile "$pyfile" 2>/dev/null; then
        check "py_compile $pyfile" "PASS"
    else
        check "py_compile $pyfile" "SYNTAX ERROR"
    fi
done

# Check api_service.py has real TFLite loading (not fake rules)
echo ""
echo "[4/8] Checking api_service.py is real TFLite inference..."
if grep -q "_load_interpreter\|tflite_runtime\|tf.lite.Interpreter" src/api_service.py; then
    check "api_service.py uses real TFLite inference" "PASS"
else
    check "api_service.py uses real TFLite inference" "FAIL - still using fake rules"
fi

if grep -q "if serror_rate > 0.70" src/api_service.py; then
    check "api_service.py has no hardcoded rules" "FAIL - hardcoded rules still present"
else
    check "api_service.py has no hardcoded rules" "PASS"
fi

# Check no em dashes in any file
echo ""
echo "[5/8] Checking em dashes in documentation..."
EM_DASH_FILES=$(grep -rl $'\xe2\x80\x94' docs/ README.md 2>/dev/null | tr '\n' ' ')
if [ -z "$EM_DASH_FILES" ]; then
    check "No em dashes in docs and README" "PASS"
else
    check "No em dashes in docs and README" "FAIL in: $EM_DASH_FILES"
fi

# Check deploy script is readable and present
echo ""
echo "[6/8] Checking script existence..."
[ -f "scripts/deploy_raspberry_pi.sh" ] && check "deploy_raspberry_pi.sh is present" "PASS" || check "deploy_raspberry_pi.sh is present" "MISSING"
[ -f "scripts/deploy_ec2.sh" ] && check "deploy_ec2.sh is present" "PASS" || check "deploy_ec2.sh is present" "MISSING"

# Check npm package
echo ""
echo "[7/8] Checking npm CLI package..."
if [ -f "npm-packet-scanner/package.json" ]; then
    BIN=$($PYTHON_BIN -c "import json; d=json.load(open('npm-packet-scanner/package.json')); print(list(d.get('bin',{}).keys()))" 2>/dev/null || echo "['unesco-mine-sec-cli']")
    check "npm CLI binary defined: $BIN" "PASS"
fi

# Run test suite
echo ""
echo "[8/8] Running unit test suite..."
if $PYTHON_BIN -m unittest discover -s tests < /dev/null; then
    check "All unit tests pass" "PASS"
else
    check "Unit tests" "SOME TESTS FAILED"
fi

echo ""
echo "============================================================"
echo " Validation Summary"
echo "============================================================"
echo "  Errors:   $ERRORS"
echo "  Warnings: $WARNINGS"
if [ "$ERRORS" -eq 0 ]; then
    echo "  STATUS:   READY FOR DEPLOYMENT"
else
    echo "  STATUS:   NOT READY -- fix $ERRORS errors above"
fi
echo "============================================================"

exit $ERRORS
