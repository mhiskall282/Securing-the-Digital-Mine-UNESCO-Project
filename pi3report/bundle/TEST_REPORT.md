# Test Report — Securing the Digital Mine (UNESCO Project)

Repo tested: https://github.com/mhiskall282/Securing-the-Digital-Mine-UNESCO-Project
Commit: shallow clone of `main`, cloned 2026-09-16
Tested by: automated test pass, requested by prince@khaya.ai

## Methodology

No physical Raspberry Pi was available for this pass, so the edge classifier and API
service were tested in a Docker container resource-capped to `--memory=1g --cpus=4`,
approximating a Raspberry Pi 3 (quad-core, 1GB RAM). Caveats that matter for reading
these numbers:

- **CPU architecture**: the container runs on the host's x86_64 CPU, not the real
  ARM Cortex-A53 (Pi 3) or Cortex-A72 (Pi 4, what the README's numbers were measured
  on). Absolute latency numbers below are not a substitute for an on-device run —
  they're useful for relative comparison and for catching functional bugs, not for
  citing as final Pi performance figures.
- **Dependency set**: installed exactly `requirements.txt` plus `tflite-runtime`
  (the lightweight runtime the deployment docs specify for the edge path), not the
  full `tensorflow` package — matching what a real Pi 3/4 deployment would actually
  install.
- **No physical Pi, no SCADA network, no real NSL-KDD/SWaT/BATADAL datasets**: the
  repo doesn't ship the raw datasets (`data/raw/` is empty in the clone), so anything
  depending on them uses the codebase's own mock/synthetic generators. The
  `dashboard/` (Laravel web app) was out of scope for this pass.

## Findings

### 1. CRITICAL — Every inference request fails when installed exactly as documented

`requirements.txt` pins `numpy>=1.24.0` with no upper bound. On a fresh install today,
pip resolves that to NumPy 2.4.6. `tflite-runtime==2.14.0` (installed via
`pip install tflite-runtime`, exactly as `docs/raspberry_pi_deployment.md`'s
troubleshooting table instructs) was compiled against the NumPy 1.x ABI. The two are
incompatible: constructing the TFLite interpreter throws
`AttributeError: _ARRAY_API not found`, and every `/api/analyze` call returns:

```json
{"error": "Inference error: <built-in method CreateWrapperFromFile of PyCapsule object at 0x...> returned a result with an exception set"}
```

`/api/health` still reports `"status": "healthy", "model_ready": true"` because that
endpoint only checks whether the model *file* exists on disk, not whether the
interpreter can load it — so a health check alone would not catch this.

Confirmed the root cause by pinning `numpy<2` (installs 1.26.4) in the same container:
the interpreter then constructs and inference works normally (see Finding 4). This is
reproducible from a clean container every time, not an environment fluke.

Raw evidence: `raw_output/A_server_stdout.log`, `raw_output/E_analyze_dos_sample.json`,
`raw_output/F_analyze_normal_sample.json`, `raw_output/J_numpy_pin_install.log`,
`raw_output/K_tflite_sanity_after_pin.log`.

### 2. HIGH — The benchmark/reporting tool doesn't check for failed requests before declaring PASS

`scripts/benchmark_and_export.py` was run against the broken (pre-fix) server for 200
samples. Every single request returned HTTP 500 with `predicted_class: ERROR`, and the
script's own summary shows `successful_requests: 0` and `overall_accuracy_pct: 0.0` —
yet it still printed:

```
Real-Time Verdict:        PASS (Sub-100ms Deadline Compliant)
SCADA Real-Time Margin:   100.0% compliant with <100ms deadline
```

and wrote that verdict into `research/tables/table5_edge_deployment_benchmarks.csv` —
the actual paper table — along with the formatted XLSX/Markdown/LaTeX exports. The
pass/fail verdict is computed purely from response latency, with no gate on HTTP
status or classification success. A broken model or environment produces a
publication-ready "PASS" report, not a loud failure.

Raw evidence: `raw_output/I_benchmark_run.log`,
`raw_output/benchmark_export/ec2_benchmark_summary.csv`,
`raw_output/benchmark_export/ec2_benchmark_detailed_inferences.csv`.

### 3. MEDIUM — `edge_benchmark.py` (and its test) hard-require full TensorFlow, unlike the API service

`src/evaluation/edge_benchmark.py` does `import tensorflow as tf` unconditionally.
`src/api_service.py`, by contrast, tries `tflite_runtime` first and only falls back to
`tensorflow` if that import fails — the correct pattern for an edge deployment that
isn't expected to carry full TensorFlow. Because of this inconsistency,
`tests/test_edge_benchmark.py` and `tests/test_cnn_lstm.py` (2 of 69 tests) fail to
even import in an environment that only has the lightweight runtime installed — which
is exactly the environment a real Pi 3/4 deployment would have. The test whose entire
purpose is validating edge performance can't run on an edge-equivalent environment.

Raw evidence: `raw_output/02_unittest_full.log` (lines showing both `ModuleNotFoundError`).

### 4. Verified / working numbers (after pinning `numpy<2` to work around Finding 1)

| Metric | README claim (Pi 4B, ARM Cortex-A72) | Observed here (x86_64 container, 1GB/4-core cap) |
|---|---|---|
| Quantized model size | 0.82 MB | 0.82 MB (861,036 bytes) — **matches exactly** |
| Baseline model size | 4.88 MB | 4.88 MB (5,113,098 bytes, `cnn_lstm_bwoa_v3.keras`) — **matches exactly** |
| Size reduction | 83.1% | 83.16% — **matches** |
| Mean inference latency | 0.76 ms | 0.51 ms (raw TFLite invoke, server-side) |
| P95 latency | 1.10 ms | 1.00 ms |
| RAM footprint (quantized, idle) | 290.31 MB | 151 MB RSS after warmup + 1 request |
| SCADA <100ms deadline | PASS | PASS (100% of 200 requests under 100ms) |

Latency and RAM numbers are lower here than the README's Pi 4 figures, which is
expected given this ran on the host's x86_64 CPU rather than real ARM silicon and
isn't a like-for-like comparison — included for completeness, not as a contradiction
of the README.

The model-size and reduction-percentage claims are hardware-independent (they're just
file sizes) and are confirmed exactly.

### 5. Accuracy on the benchmark tool's synthetic sample set: 57.0% (Macro F1 0.5667)

With inference actually working, `scripts/benchmark_and_export.py --samples 200`
reported 57.0% overall accuracy. This is **not** a reproduction of the README/paper's
NSL-KDD test-set accuracy claim — the raw NSL-KDD dataset isn't in this repo clone, so
this benchmark script generates its own synthetic per-class feature samples rather
than evaluating against `KDDTest+.txt`. Per-class breakdown:

| Class | Precision | Recall | F1 |
|---|---|---|---|
| Normal | 50.0% | 50.0% | 0.50 |
| DoS | 100.0% | 50.0% | 0.67 |
| Probe | 100.0% | 100.0% | 1.00 |
| R2L | 50.0% | 100.0% | 0.67 |
| U2R | 0.0% | 0.0% | 0.00 |

U2R scored 0 across the board on this synthetic set — worth a look, though U2R is
also the rarest/hardest class in real NSL-KDD data generally, so this may just reflect
how the synthetic generator constructs U2R-labeled samples rather than a model defect.
Flagging as an observation, not a confirmed bug.

Raw evidence: `raw_output/benchmark_export_fixed/ec2_benchmark_summary.csv`,
`raw_output/benchmark_export_fixed/ec2_benchmark_per_class.csv`,
`raw_output/benchmark_export_fixed/ec2_benchmark_detailed_inferences.csv`.

### 6. Unit test suite: 67 / 69 pass

`python -m unittest discover -s tests` — 67 passed, 2 errored (both are Finding 3,
the TensorFlow import issue, not real test failures). Covers BWOA optimizer, fitness
function, NSL-KDD/SWaT/BATADAL loaders, metrics, and the API service's own unit tests.

Raw evidence: `raw_output/02_unittest_full.log`, `raw_output/01_validate_pi_deployment.log`.

### 7. `api_service.py` — light code/robustness review

- No authentication on any endpoint (`/api/health`, `/api/features`, `/api/analyze`,
  `/api/export/*`), and every response sends `Access-Control-Allow-Origin: *`. Given
  this service is documented to run with a NIC bridged onto the SCADA mirror port
  network, anything that can reach the Pi's management interface can query model
  internals and run inference for free, with no auth layer at this hop (the docs'
  bearer-token auth only appears on the *forwarding-to-EC2* path, not this local API).
- `/api/analyze` reads `Content-Length` bytes with no upper bound before parsing JSON.
  A 5MB body was handled fine in testing (no crash, no hang) but there's no explicit
  cap in the code — worth keeping in mind for a device sitting on operational
  infrastructure.
- `/api/health` exposes the full absolute filesystem path to the loaded model
  (`model_path`) in an unauthenticated response — minor internal-layout disclosure.
- Malformed-input handling (empty body, non-JSON, JSON array instead of object) is
  handled cleanly with proper 400 responses — no crashes observed.
- No shell injection, `eval`/`exec`, or hardcoded secrets found in `src/`, `scripts/`,
  or `npm-packet-scanner/` (grep-based scan, not a full audit).
- `npm-packet-scanner`'s default `--url` points to a third-party-hosted SaaS endpoint
  (`minesec-dashboard-prod.onrender.com`) rather than a local/self-hosted default —
  worth knowing if raw SCADA flow data is sensitive, since a default run without
  `--url` overridden streams classified flow data off-site.

## Scope not covered in this pass

- `dashboard/` (Laravel/Livewire web app) — not exercised.
- Real Raspberry Pi hardware — no physical device was reachable at the time of testing.
- Training pipeline / BWOA optimization against the real NSL-KDD/SWaT/BATADAL datasets
  — those datasets aren't in the repo and weren't downloaded for this pass; only the
  codebase's own mock generators were exercised (via the existing unit tests).
- Load/concurrency testing beyond the 200-request sequential benchmark.

## Raw data included in this bundle

See `raw_output/` for every log, JSON response, and CSV/XLSX/LaTeX export referenced
above, plus `docker/` for the exact Dockerfile and scripts used to reproduce this run.
