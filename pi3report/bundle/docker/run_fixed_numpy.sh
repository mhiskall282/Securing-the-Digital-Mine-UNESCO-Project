#!/usr/bin/env bash
set -uo pipefail
OUT=/output
cd /opt/unesco-project

echo "=== [J] downgrading numpy to a tflite_runtime-2.14.0-compatible ABI (numpy<2) ===" > $OUT/J_numpy_pin_install.log
pip install --no-cache-dir "numpy<2" >> $OUT/J_numpy_pin_install.log 2>&1
python -c "import numpy; print('numpy version now:', numpy.__version__)" >> $OUT/J_numpy_pin_install.log 2>&1

echo "=== [K] sanity check: does tflite interpreter construct now? ==="
python -c "
import tflite_runtime.interpreter as tflite
interp = tflite.Interpreter(model_path='models/cnn_lstm_quantized_float16_v3.tflite')
interp.allocate_tensors()
print('Interpreter constructed and tensors allocated OK')
print(interp.get_input_details())
" > $OUT/K_tflite_sanity_after_pin.log 2>&1

PORT=8001 python src/api_service.py > $OUT/L_server_stdout_after_pin.log 2>&1 &
SRV_PID=$!
for i in $(seq 1 30); do
  if curl -s -o /dev/null http://localhost:8001/api/health; then
    break
  fi
  sleep 1
done

curl -s -X POST http://localhost:8001/api/analyze \
  -H "Content-Type: application/json" \
  -d '{"protocol_type": 1, "service": 21, "flag": 10, "src_bytes": 1032, "hot": 0, "su_attempted": 0, "serror_rate": 0.88, "same_srv_rate": 0.95, "diff_srv_rate": 0.05, "dst_host_diff_srv_rate": 0.02}' \
  > $OUT/M_analyze_after_pin.json

grep VmRSS /proc/$SRV_PID/status > $OUT/N_server_rss_after_pin.log
ps -o pid,rss,vsz,cmd -p $SRV_PID >> $OUT/N_server_rss_after_pin.log

mkdir -p $OUT/benchmark_export_fixed
timeout 180 python scripts/benchmark_and_export.py --url http://localhost:8001 --samples 200 --output-dir $OUT/benchmark_export_fixed > $OUT/O_benchmark_run_after_pin.log 2>&1
echo "exit code: $?" >> $OUT/O_benchmark_run_after_pin.log

kill $SRV_PID 2>/dev/null
echo "ALL FIXED-NUMPY STEPS COMPLETE"
