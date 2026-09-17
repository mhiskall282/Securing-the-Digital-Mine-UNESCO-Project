#!/usr/bin/env bash
set -uo pipefail
OUT=/output
cd /opt/unesco-project

echo "=== [A] starting api_service.py ==="
PORT=8001 python src/api_service.py > $OUT/A_server_stdout.log 2>&1 &
SRV_PID=$!
echo "server pid: $SRV_PID" > $OUT/B_server_info.log

# wait for server to come up (warmup can take a moment)
for i in $(seq 1 30); do
  if curl -s -o /dev/null http://localhost:8001/api/health; then
    echo "server up after ${i}s" >> $OUT/B_server_info.log
    break
  fi
  sleep 1
done

echo "=== [C] health check ===" > $OUT/C_health.json
curl -s http://localhost:8001/api/health >> $OUT/C_health.json

echo "" >> $OUT/C_health.json
echo "=== [D] features ===" > $OUT/D_features.json
curl -s http://localhost:8001/api/features >> $OUT/D_features.json

echo "=== [E] analyze - documented DoS sample payload ===" > $OUT/E_analyze_dos_sample.json
curl -s -X POST http://localhost:8001/api/analyze \
  -H "Content-Type: application/json" \
  -d '{
    "protocol_type": 1,
    "service": 21,
    "flag": 10,
    "src_bytes": 1032,
    "hot": 0,
    "su_attempted": 0,
    "serror_rate": 0.88,
    "same_srv_rate": 0.95,
    "diff_srv_rate": 0.05,
    "dst_host_diff_srv_rate": 0.02
  }' >> $OUT/E_analyze_dos_sample.json

echo "" >> $OUT/E_analyze_dos_sample.json
echo "=== [F] analyze - documented normal-ish sample payload (string categoricals) ===" > $OUT/F_analyze_normal_sample.json
curl -s -X POST http://localhost:8001/api/analyze \
  -H "Content-Type: application/json" \
  -d '{"protocol_type": "tcp", "service": "http", "flag": "SF", "src_bytes": 1024, "hot": 0, "su_attempted": 0, "serror_rate": 0.85, "same_srv_rate": 0.15, "diff_srv_rate": 0.0, "dst_host_diff_srv_rate": 0.0}' \
  >> $OUT/F_analyze_normal_sample.json

echo "=== [G] resident memory of server process (RSS) ===" > $OUT/G_server_rss.log
grep VmRSS /proc/$SRV_PID/status >> $OUT/G_server_rss.log
ps -o pid,rss,vsz,cmd -p $SRV_PID >> $OUT/G_server_rss.log

echo "=== [H] malformed-input robustness checks ===" > $OUT/H_robustness.log
echo "-- no Content-Length / empty body --" >> $OUT/H_robustness.log
curl -s -o /dev/null -w "HTTP %{http_code}\n" -X POST http://localhost:8001/api/analyze >> $OUT/H_robustness.log
echo "-- non-JSON body --" >> $OUT/H_robustness.log
curl -s -X POST http://localhost:8001/api/analyze -H "Content-Type: application/json" -d 'not json at all' >> $OUT/H_robustness.log
echo "" >> $OUT/H_robustness.log
echo "-- JSON array instead of object --" >> $OUT/H_robustness.log
curl -s -X POST http://localhost:8001/api/analyze -H "Content-Type: application/json" -d '[1,2,3]' >> $OUT/H_robustness.log
echo "" >> $OUT/H_robustness.log
echo "-- large payload (5MB) latency/behavior --" >> $OUT/H_robustness.log
python -c "import json; print(json.dumps({'protocol_type':'tcp','padding':'A'*5000000}))" > /tmp/big_payload.json
time curl -s -o /dev/null -w "HTTP %{http_code} in %{time_total}s\n" -X POST http://localhost:8001/api/analyze -H "Content-Type: application/json" --data @/tmp/big_payload.json >> $OUT/H_robustness.log 2>&1

echo "=== [I] real benchmark via scripts/benchmark_and_export.py (200 samples) ==="
mkdir -p $OUT/benchmark_export
timeout 180 python scripts/benchmark_and_export.py --url http://localhost:8001 --samples 200 --output-dir $OUT/benchmark_export > $OUT/I_benchmark_run.log 2>&1
echo "exit code: $?" >> $OUT/I_benchmark_run.log

kill $SRV_PID 2>/dev/null
echo "ALL API/BENCH STEPS COMPLETE"
