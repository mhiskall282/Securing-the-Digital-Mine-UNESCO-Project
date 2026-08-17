"""ML Inference Microservice -- Securing the Digital Mine.

Loads the quantized TFLite CNN-LSTM model at startup and exposes three endpoints:
    GET  /api/health    -- liveness check
    POST /api/analyze   -- run TFLite inference on a feature dict
    GET  /api/features  -- return the 10 BWOA-selected feature names and indices

MODEL_PATH, FEATURE_MASK_PATH, and SCALER_PATH can be overridden via environment
variables so the same binary works on both Raspberry Pi and EC2.
"""

import json
import time
import os
import numpy as np
from http.server import HTTPServer, BaseHTTPRequestHandler

# ---------------------------------------------------------------------------
# Path configuration (override via environment variables)
# ---------------------------------------------------------------------------
MODEL_PATH = os.environ.get(
    "MODEL_PATH",
    os.path.join(os.path.dirname(__file__), "../models/cnn_lstm_bwoa_v3_quantized.tflite"),
)
FEATURE_MASK_PATH = os.environ.get(
    "FEATURE_MASK_PATH",
    os.path.join(os.path.dirname(__file__), "../data/features/nslkdd_bwoa_mask_v3.npy"),
)
SCALER_PATH = os.environ.get(
    "SCALER_PATH",
    os.path.join(os.path.dirname(__file__), "../data/processed/scaler.pkl"),
)

# ---------------------------------------------------------------------------
# Class labels matching the model output order
# ---------------------------------------------------------------------------
CLASS_LABELS = ["Normal", "DoS", "Probe", "R2L", "U2R"]

# ---------------------------------------------------------------------------
# All 41 NSL-KDD feature names in the canonical column order
# ---------------------------------------------------------------------------
ALL_FEATURES = [
    "duration", "protocol_type", "service", "flag", "src_bytes", "dst_bytes",
    "land", "wrong_fragment", "urgent", "hot", "num_failed_logins", "logged_in",
    "num_compromised", "root_shell", "su_attempted", "num_root", "num_file_creations",
    "num_shells", "num_access_files", "num_outbound_cmds", "is_host_login",
    "is_guest_login", "count", "srv_count", "serror_rate", "srv_serror_rate",
    "rerror_rate", "srv_rerror_rate", "same_srv_rate", "diff_srv_rate",
    "srv_diff_host_rate", "dst_host_count", "dst_host_srv_count",
    "dst_host_same_srv_rate", "dst_host_diff_srv_rate", "dst_host_same_src_port_rate",
    "dst_host_srv_diff_host_rate", "dst_host_serror_rate", "dst_host_srv_serror_rate",
    "dst_host_rerror_rate", "dst_host_srv_rerror_rate",
]

# ---------------------------------------------------------------------------
# BWOA v3 selected features (10 of 41)
# ---------------------------------------------------------------------------
SELECTED_FEATURES = [
    "protocol_type", "service", "flag", "src_bytes", "hot", "su_attempted",
    "serror_rate", "same_srv_rate", "diff_srv_rate", "dst_host_diff_srv_rate",
]
SELECTED_INDICES = [ALL_FEATURES.index(f) for f in SELECTED_FEATURES]

# ---------------------------------------------------------------------------
# Lazy-loaded singletons
# ---------------------------------------------------------------------------
INTERPRETER = None
SCALER = None
FEATURE_MASK = None


def _load_interpreter():
    """Load TFLite interpreter, preferring tflite_runtime over full TF."""
    model_path = os.path.abspath(MODEL_PATH)
    if not os.path.exists(model_path):
        raise FileNotFoundError(
            f"TFLite model not found at '{model_path}'. "
            "Set the MODEL_PATH environment variable or place the model file at "
            "models/cnn_lstm_bwoa_v3_quantized.tflite. "
            "Run the Colab notebook (notebooks/00_colab_setup_and_train.ipynb) to train "
            "and download the quantized model."
        )
    try:
        import tflite_runtime.interpreter as tflite
        interpreter = tflite.Interpreter(model_path=model_path)
    except ImportError:
        import tensorflow as tf
        interpreter = tf.lite.Interpreter(model_path=model_path)
    interpreter.allocate_tensors()
    return interpreter


def _load_scaler():
    """Load the sklearn StandardScaler if available."""
    import pickle
    scaler_path = os.path.abspath(SCALER_PATH)
    if os.path.exists(scaler_path):
        with open(scaler_path, "rb") as fh:
            return pickle.load(fh)
    return None


def _get_model():
    """Return (interpreter, scaler, feature_mask), initializing on first call."""
    global INTERPRETER, SCALER, FEATURE_MASK
    if INTERPRETER is None:
        INTERPRETER = _load_interpreter()
        SCALER = _load_scaler()
        mask_path = os.path.abspath(FEATURE_MASK_PATH)
        if os.path.exists(mask_path):
            FEATURE_MASK = np.load(mask_path)
    return INTERPRETER, SCALER, FEATURE_MASK


def run_inference(features_dict: dict) -> dict:
    """Run TFLite inference on a dict of NSL-KDD feature values.

    Args:
        features_dict: Mapping of feature name to numeric value. Missing features
            default to 0.0.

    Returns:
        Dict with prediction, confidence, class_probabilities, features_used,
        latency_ms, and model_version.
    """
    interpreter, scaler, _ = _get_model()

    # Build full 41-feature vector; missing features default to 0.0
    raw = np.array(
        [float(features_dict.get(f, 0.0)) for f in ALL_FEATURES],
        dtype=np.float32,
    )

    # Apply scaler if one was saved during training
    if scaler is not None:
        raw = scaler.transform(raw.reshape(1, -1)).flatten().astype(np.float32)

    # Select BWOA features and reshape to (1, n_selected, 1) for CNN-LSTM
    selected = raw[SELECTED_INDICES].reshape(1, len(SELECTED_INDICES), 1).astype(np.float32)

    # Run inference
    input_details = interpreter.get_input_details()
    output_details = interpreter.get_output_details()

    interpreter.set_tensor(input_details[0]["index"], selected)

    t_start = time.perf_counter()
    interpreter.invoke()
    latency_ms = (time.perf_counter() - t_start) * 1000

    output = interpreter.get_tensor(output_details[0]["index"])[0]
    pred_idx = int(np.argmax(output))
    confidence = float(output[pred_idx])

    return {
        "prediction": CLASS_LABELS[pred_idx],
        "confidence": round(confidence * 100, 2),
        "class_probabilities": {
            CLASS_LABELS[i]: round(float(output[i]) * 100, 2)
            for i in range(len(CLASS_LABELS))
        },
        "features_used": SELECTED_FEATURES,
        "latency_ms": round(latency_ms, 4),
        "model_version": "v3.0.0-tflite-quantized",
    }


# ---------------------------------------------------------------------------
# HTTP request handler
# ---------------------------------------------------------------------------

class ModelInferenceHandler(BaseHTTPRequestHandler):
    """Handles HTTP requests for the ML inference microservice."""

    def log_message(self, format, *args):
        # Suppress per-request log noise; errors are still raised
        return

    # ------------------------------------------------------------------
    # GET endpoints
    # ------------------------------------------------------------------
    def do_GET(self):
        if self.path in ("/api/health", "/"):
            self._handle_health()
        elif self.path == "/api/features":
            self._handle_features()
        else:
            self._send_json({"error": "Not found"}, status=404)

    def _handle_health(self):
        """Return service liveness and model version."""
        model_ready = os.path.exists(os.path.abspath(MODEL_PATH))
        response = {
            "status": "healthy",
            "model_ready": model_ready,
            "model_version": "v3.0.0-tflite-quantized",
            "framework": "TFLite Float16",
            "model_path": os.path.abspath(MODEL_PATH),
        }
        self._send_json(response, status=200)

    def _handle_features(self):
        """Return the 10 BWOA-selected feature names and their indices in ALL_FEATURES."""
        response = {
            "selected_features": SELECTED_FEATURES,
            "selected_indices": SELECTED_INDICES,
            "n_selected": len(SELECTED_FEATURES),
            "n_total": len(ALL_FEATURES),
            "reduction_pct": round((1 - len(SELECTED_FEATURES) / len(ALL_FEATURES)) * 100, 2),
        }
        self._send_json(response, status=200)

    # ------------------------------------------------------------------
    # POST endpoints
    # ------------------------------------------------------------------
    def do_POST(self):
        if self.path == "/api/analyze":
            self._handle_analyze()
        else:
            self._send_json({"error": "Not found"}, status=404)

    def _handle_analyze(self):
        """Parse the request body and run TFLite inference."""
        content_length = int(self.headers.get("Content-Length", 0))
        raw_body = self.rfile.read(content_length)

        try:
            payload = json.loads(raw_body.decode("utf-8"))
        except (json.JSONDecodeError, UnicodeDecodeError) as exc:
            self._send_json({"error": f"Invalid JSON body: {exc}"}, status=400)
            return

        if not isinstance(payload, dict):
            self._send_json({"error": "Request body must be a JSON object"}, status=400)
            return

        try:
            result = run_inference(payload)
        except FileNotFoundError as exc:
            self._send_json({"error": str(exc)}, status=503)
            return
        except Exception as exc:
            self._send_json({"error": f"Inference error: {exc}"}, status=500)
            return

        self._send_json(result, status=200)

    # ------------------------------------------------------------------
    # Helper
    # ------------------------------------------------------------------
    def _send_json(self, data: dict, status: int = 200):
        body = json.dumps(data).encode("utf-8")
        self.send_response(status)
        self.send_header("Content-Type", "application/json")
        self.send_header("Content-Length", str(len(body)))
        self.end_headers()
        self.wfile.write(body)


# ---------------------------------------------------------------------------
# Entry point
# ---------------------------------------------------------------------------

def run(server_class=HTTPServer, handler_class=ModelInferenceHandler):
    port = int(os.environ.get("PORT", 8001))
    host = os.environ.get("HOST", "0.0.0.0")
    server_address = (host, port)
    httpd = server_class(server_address, handler_class)
    model_path = os.path.abspath(MODEL_PATH)
    print(f"Securing the Digital Mine -- Inference Server")
    print(f"Listening on http://{host}:{port}")
    print(f"MODEL_PATH: {model_path}")
    print(f"Model file exists: {os.path.exists(model_path)}")
    try:
        httpd.serve_forever()
    except KeyboardInterrupt:
        pass
    httpd.server_close()


if __name__ == "__main__":
    run()
