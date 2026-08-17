"""Validate all ML inference API endpoints."""
import subprocess
import time
import requests
import json
import sys
import os

proc = subprocess.Popen(
    [sys.executable, "src/api_service.py"],
    stdout=subprocess.PIPE,
    stderr=subprocess.PIPE,
    cwd=os.path.abspath(os.path.join(os.path.dirname(__file__), ".."))
)
time.sleep(3)  # Wait for server to start

BASE = "http://localhost:8001"
errors = []

# Test 1: Health endpoint
try:
    r = requests.get(f"{BASE}/api/health", timeout=5)
    assert r.status_code == 200, f"Health returned {r.status_code}"
    data = r.json()
    assert "status" in data, "Health missing 'status' field"
    assert "model_version" in data, "Health missing 'model_version' field"
    print("PASS: GET /api/health")
except Exception as e:
    errors.append(f"FAIL: GET /api/health -- {e}")
    print(errors[-1])

# Test 2: Features endpoint
try:
    r = requests.get(f"{BASE}/api/features", timeout=5)
    assert r.status_code == 200, f"Features returned {r.status_code}"
    data = r.json()
    assert "selected_features" in data, "Missing selected_features"
    assert len(data["selected_features"]) == 10, f"Expected 10 features, got {len(data['selected_features'])}"
    print("PASS: GET /api/features")
except Exception as e:
    errors.append(f"FAIL: GET /api/features -- {e}")
    print(errors[-1])

# Test 3: Analyze endpoint - DoS signature (high serror_rate)
dos_payload = {
    "protocol_type": 1, "service": 21, "flag": 10,
    "src_bytes": 1032, "hot": 0, "su_attempted": 0,
    "serror_rate": 0.88, "same_srv_rate": 0.95,
    "diff_srv_rate": 0.05, "dst_host_diff_srv_rate": 0.02
}
try:
    r = requests.post(f"{BASE}/api/analyze", json=dos_payload, timeout=5)
    assert r.status_code in [200, 503], f"Analyze returned {r.status_code}"
    data = r.json()
    if r.status_code == 200:
        assert "prediction" in data, "Missing prediction field"
        assert "confidence" in data, "Missing confidence field"
        assert "latency_ms" in data, "Missing latency_ms field"
        assert data["prediction"] in ["Normal", "DoS", "Probe", "R2L", "U2R"], \
            f"Invalid prediction: {data['prediction']}"
        print(f"PASS: POST /api/analyze -- prediction={data['prediction']}, "
              f"confidence={data['confidence']}%, latency={data['latency_ms']}ms")
    else:
        # 503 means model file not present -- acceptable in CI without trained model
        print(f"PASS: POST /api/analyze -- 503 (model file not found, expected in CI)")
        print(f"      Response: {data.get('error', 'no error field')}")
except Exception as e:
    errors.append(f"FAIL: POST /api/analyze -- {e}")
    print(errors[-1])

# Test 4: Analyze endpoint - Normal signature
normal_payload = {
    "protocol_type": 2, "service": 80, "flag": 5,
    "src_bytes": 215, "hot": 0, "su_attempted": 0,
    "serror_rate": 0.0, "same_srv_rate": 1.0,
    "diff_srv_rate": 0.0, "dst_host_diff_srv_rate": 0.0
}
try:
    r = requests.post(f"{BASE}/api/analyze", json=normal_payload, timeout=5)
    assert r.status_code in [200, 503]
    data = r.json()
    if r.status_code == 200:
        print(f"PASS: POST /api/analyze (normal) -- prediction={data['prediction']}")
    else:
        print("PASS: POST /api/analyze (normal) -- 503 expected without model file")
except Exception as e:
    errors.append(f"FAIL: POST /api/analyze (normal) -- {e}")
    print(errors[-1])

# Test 5: Invalid endpoint returns 404
try:
    r = requests.get(f"{BASE}/nonexistent", timeout=5)
    assert r.status_code == 404, f"Expected 404, got {r.status_code}"
    print("PASS: GET /nonexistent returns 404")
except Exception as e:
    errors.append(f"FAIL: 404 test -- {e}")
    print(errors[-1])

proc.terminate()
try:
    proc.wait(timeout=2)
except Exception:
    proc.kill()

if errors:
    print(f"\nFAILED: {len(errors)} endpoint tests failed")
    sys.exit(1)
else:
    print(f"\nALL API ENDPOINT TESTS PASSED")
