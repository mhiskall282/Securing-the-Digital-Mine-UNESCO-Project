"""Verify Colab notebook structure and required cells."""
import json
import sys
import os

nb_path = "notebooks/00_colab_setup_and_train.ipynb"
if not os.path.exists(nb_path):
    print(f"FAIL: {nb_path} not found")
    sys.exit(1)

with open(nb_path, encoding="utf-8") as f:
    nb = json.load(f)

cells = nb.get("cells", [])
print(f"Total cells: {len(cells)}")

for i, cell in enumerate(cells):
    ctype = cell.get("cell_type", "unknown")
    source = "".join(cell.get("source", []))[:80].replace("\n", " ")
    print(f"  Cell {i+1} ({ctype}): {source}")

# Check required cells exist
sources = ["".join(c.get("source", [])) for c in cells]
checks = {
    "GPU check": any("nvidia-smi" in s for s in sources),
    "git clone": any("git clone" in s for s in sources),
    "NSL-KDD download": any("KDDTrain" in s for s in sources),
    "BWOA run": any("BinaryWhaleOptimizer" in s or "bwoa" in s.lower() for s in sources),
    "CNN-LSTM v4 training": any("build_cnn_lstm_v4" in s for s in sources),
    "quantization": any("TFLiteConverter" in s or "tflite" in s.lower() for s in sources),
    "download artifacts": any("files.download" in s for s in sources),
}

print("\nRequired content checks:")
all_pass = True
for check, result in checks.items():
    status = "PASS" if result else "FAIL"
    if not result:
        all_pass = False
    print(f"  {status}: {check}")

if all_pass:
    print("\nCOLAB NOTEBOOK: ALL CHECKS PASSED")
    sys.exit(0)
else:
    print("\nCOLAB NOTEBOOK: SOME CHECKS FAILED - fix missing cells")
    sys.exit(1)
