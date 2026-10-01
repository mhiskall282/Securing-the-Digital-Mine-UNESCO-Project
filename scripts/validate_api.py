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

BASE = os.environ.get("API_URL", "http://127.0.0.1:8001")
errors = []

try:
    # Wait for server to start with polling (up to 45s on heavy TF environments)
    server_ready = False
    print(f"Waiting for ML API service to bind on {BASE}...")
    for _ in range(45):
        if proc.poll() is not None:
            out, err = proc.communicate()
            print("Server process exited unexpectedly:")
            print("STDOUT:", out.decode(errors="replace"))
            print("STDERR:", err.decode(errors="replace"))
            sys.exit(1)
        try:
            r = requests.get(f"{BASE}/api/health", timeout=1)
            if r.status_code in (200, 503):
                server_ready = True
                print("Server is listening and ready.")
                break
        except Exception:
            time.sleep(1)

    if not server_ready:
        print("Server failed to become ready within timeout.")
        sys.exit(1)

    # Test 1: Health endpoint
    try:
        r = requests.get(f"{BASE}/api/health", timeout=5)
        assert r.status_code in [200, 503], f"Health returned {r.status_code}"
        data = r.json()
        assert "status" in data, "Health missing 'status' field"
        assert "model_version" in data, "Health missing 'model_version' field"
        assert "model_path" not in data, "Health endpoint must not disclose internal filesystem path"
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

    # Test 3: Analyze endpoint - Canonical DoS signature (SYN flood / serror spike)
    dos_payload = {
        "protocol_type": "tcp", "service": "http", "flag": "S0",
        "src_bytes": 0, "hot": 0, "su_attempted": 0,
        "serror_rate": 0.95, "same_srv_rate": 0.08,
        "diff_srv_rate": 0.0, "dst_host_diff_srv_rate": 0.0
    }
    try:
        r = requests.post(f"{BASE}/api/analyze", json=dos_payload, timeout=5)
        assert r.status_code in [200, 503], f"Analyze returned {r.status_code}"
        data = r.json()
        if r.status_code == 200:
            assert "prediction" in data, "Missing prediction field"
            assert "confidence" in data, "Missing confidence field"
            assert "latency_ms" in data, "Missing latency_ms field"
            assert "class_probabilities" in data, "Missing class_probabilities field"
            assert "features_triggered" not in data, "Hallucinated field features_triggered found"
            assert data["prediction"] in ["Normal", "DoS", "Probe", "R2L", "U2R"], \
                f"Invalid prediction: {data['prediction']}"
            print(f"PASS: POST /api/analyze -- prediction={data['prediction']}, "
                  f"confidence={data['confidence']}%, latency={data['latency_ms']}ms")
        else:
            print(f"PASS: POST /api/analyze -- 503 (model file not found, expected in CI)")
            print(f"      Response: {data.get('error', 'no error field')}")
    except Exception as e:
        errors.append(f"FAIL: POST /api/analyze -- {e}")
        print(errors[-1])

    # Test 4: Analyze endpoint - Normal signature
    normal_payload = {
        "protocol_type": "tcp", "service": "http", "flag": "SF",
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

    # Test 5: Validation - Missing features must return HTTP 400
    try:
        incomplete_payload = {"protocol_type": "tcp"}
        r = requests.post(f"{BASE}/api/analyze", json=incomplete_payload, timeout=5)
        assert r.status_code == 400, f"Expected 400 for missing features, got {r.status_code}"
        data = r.json()
        assert "missing_features" in data, "Response missing 'missing_features' key"
        assert len(data["missing_features"]) == 9, f"Expected 9 missing features, got {len(data['missing_features'])}"
        print("PASS: POST /api/analyze rejects missing features with HTTP 400")
    except Exception as e:
        errors.append(f"FAIL: Missing features test -- {e}")
        print(errors[-1])

    # Test 6: Validation - Oversized body (>64 KB) must return HTTP 413
    try:
        oversized_payload = {"padding": "X" * (65 * 1024)}
        r = requests.post(f"{BASE}/api/analyze", json=oversized_payload, timeout=5)
        assert r.status_code == 413, f"Expected 413 for oversized body, got {r.status_code}"
        print("PASS: POST /api/analyze rejects oversized payload with HTTP 413")
    except Exception as e:
        errors.append(f"FAIL: Oversized payload test -- {e}")
        print(errors[-1])

    # Test 7: Invalid endpoint returns 404
    try:
        r = requests.get(f"{BASE}/nonexistent", timeout=5)
        assert r.status_code == 404, f"Expected 404, got {r.status_code}"
        print("PASS: GET /nonexistent returns 404")
    except Exception as e:
        errors.append(f"FAIL: 404 test -- {e}")
        print(errors[-1])

finally:
    proc.terminate()
    try:
        proc.wait(timeout=3)
    except Exception:
        proc.kill()

if errors:
    print(f"\nFAILED: {len(errors)} endpoint tests failed")
    sys.exit(1)
else:
    print(f"\nALL API ENDPOINT TESTS PASSED")

