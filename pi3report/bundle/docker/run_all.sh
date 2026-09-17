#!/usr/bin/env bash
set -uo pipefail
OUT=/output
cd /opt/unesco-project

echo "=== ENVIRONMENT ===" | tee $OUT/00_environment.log
date >> $OUT/00_environment.log
python --version >> $OUT/00_environment.log 2>&1
nproc >> $OUT/00_environment.log
cat /proc/meminfo | head -3 >> $OUT/00_environment.log
pip freeze >> $OUT/00_environment.log 2>&1
echo "tflite_runtime install log:" >> $OUT/00_environment.log
cat tflite_runtime_install.log >> $OUT/00_environment.log 2>&1 || echo "(no failure log - install succeeded)" >> $OUT/00_environment.log

echo "=== [1] validate_pi_deployment.sh ===" 
chmod +x scripts/validate_pi_deployment.sh
./scripts/validate_pi_deployment.sh > $OUT/01_validate_pi_deployment.log 2>&1
echo "exit code: $?" >> $OUT/01_validate_pi_deployment.log

echo "=== [2] full unittest discover (raw) ==="
python -m unittest discover -s tests -v > $OUT/02_unittest_full.log 2>&1
echo "exit code: $?" >> $OUT/02_unittest_full.log

echo "=== [3] model file sizes ==="
ls -la models/*.keras models/*.h5 models/*.tflite > $OUT/03_model_sizes.log 2>&1
du -h models/*.keras models/*.h5 models/*.tflite >> $OUT/03_model_sizes.log 2>&1

echo "=== [4] tflite_runtime sanity check ==="
python -c "import tflite_runtime.interpreter as tflite; print('tflite_runtime import OK, version module:', tflite)" > $OUT/04_tflite_runtime_check.log 2>&1

echo "=== [5] edge benchmark test ==="
python -m unittest tests.test_edge_benchmark -v > $OUT/05_test_edge_benchmark.log 2>&1
echo "exit code: $?" >> $OUT/05_test_edge_benchmark.log

echo "=== [6] scripts/benchmark_and_export.py (if runnable standalone) ==="
timeout 120 python scripts/benchmark_and_export.py --help > $OUT/06_benchmark_and_export_help.log 2>&1
echo "exit code: $?" >> $OUT/06_benchmark_and_export_help.log

echo "ALL STEPS COMPLETE"
