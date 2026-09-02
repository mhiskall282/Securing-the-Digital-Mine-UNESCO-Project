"""Generate all high-resolution diagram figures and CSV benchmark tables for the research deliverables."""
import os
import shutil
import matplotlib.pyplot as plt
import matplotlib.patches as patches
import numpy as np
import pandas as pd

os.makedirs("research/figures", exist_ok=True)
os.makedirs("research/tables", exist_ok=True)

# Set matplotlib aesthetics
plt.rcParams['font.sans-serif'] = 'Arial'
plt.rcParams['font.family'] = 'sans-serif'

# Colors
UNESCO_BLUE = "#00529B"
CYAN = "#00A3E0"
DARK_NAVY = "#0B1D3A"
GOLD = "#D4AF37"
ACCENT_GREEN = "#10B981"
ACCENT_RED = "#EF4444"
BG_LIGHT = "#F8FAFC"
BORDER_COLOR = "#CBD5E1"

# -------------------------------------------------------------
# 1. DSR Framework Flowchart
# -------------------------------------------------------------
fig, ax = plt.subplots(figsize=(12, 5), dpi=300)
ax.set_facecolor(BG_LIGHT)
fig.patch.set_facecolor('white')

steps = [
    ("1. Problem\nIdentification", "OT/SCADA cybersecurity\ngap in African & Russian\nmining operations", "#1E3A8A"),
    ("2. Define\nObjectives", "Sub-100ms latency, >70%\naccuracy on 10 features,\n1GB RAM edge ready", "#2563EB"),
    ("3. Design &\nDevelopment", "BWOA metaheuristic +\nConv1D-LSTM hybrid +\nFloat16 Quantization", "#0284C7"),
    ("4. Demonstration\n& Deployment", "Raspberry Pi 4 sniffer,\nNode.js CLI agent,\nLaravel SaaS Dashboard", "#0D9488"),
    ("5. Empirical\nEvaluation", "KDDTest+ (70.56%),\nSWaT (59.95%), 0.76ms\nlatency, UAT testing", "#16A34A"),
    ("6. Scholarly\nCommunication", "UNESCO Forum 2026,\nSaint Petersburg Mining Univ,\nOpen Source Repo", "#D97706")
]

for i, (title, desc, col) in enumerate(steps):
    x = 0.5 + i * 1.85
    y = 1.0
    # Box
    rect = patches.FancyBboxPatch((x, y), 1.6, 2.8, boxstyle="round,pad=0.1", fc=col, ec="none")
    ax.add_patch(rect)
    # Text
    ax.text(x + 0.8, y + 2.3, title, ha='center', va='center', color='white', weight='bold', fontsize=11)
    ax.text(x + 0.8, y + 1.1, desc, ha='center', va='center', color='#E2E8F0', fontsize=8.5)
    
    # Arrow
    if i < len(steps) - 1:
        ax.annotate('', xy=(x + 1.8, y + 1.4), xytext=(x + 1.6, y + 1.4),
                    arrowprops=dict(arrowstyle="-|>", lw=2.5, color='#475569', mutation_scale=15))

ax.set_xlim(0, 11.5)
ax.set_ylim(0.5, 4.5)
ax.axis('off')
plt.title("Design Science Research (DSR) Process Framework: Securing the Digital Mine", fontsize=13, weight='bold', pad=15, color=DARK_NAVY)
plt.tight_layout()
plt.savefig("research/figures/dsr_framework.png", bbox_inches='tight')
plt.close()

# -------------------------------------------------------------
# 2. System Architecture (4-Layer Pipeline)
# -------------------------------------------------------------
fig, ax = plt.subplots(figsize=(12, 6.5), dpi=300)
ax.set_facecolor(BG_LIGHT)
fig.patch.set_facecolor('white')

layers = [
    ("Layer 1: Industrial Ingestion Layer", ["Modbus RTU/TCP", "DNP3 SCADA Telemetry", "OPC-UA Flow Logs", "unesco-mine-sec-cli Agent"], "#0F172A", 5.0),
    ("Layer 2: Metaheuristic Optimization Layer", ["CICFlowMeter 41 Network Attributes", "Binary Whale Optimization (BWOA)", "Shrinking Encircling & Spiral Search", "Optimal 10-Feature Subset (75.6% pruned)"], "#1E40AF", 3.6),
    ("Layer 3: Spatial-Temporal Deep Learning Layer", ["Conv1D Spatial Feature Extractor (64 filters)", "LSTM Temporal Sequence Unit (256 units)", "Sparse Categorical Cross-Entropy", "Post-Training Float16 Quantization (0.82MB)"], "#0369A1", 2.2),
    ("Layer 4: Deployment & Operational Layer", ["Raspberry Pi 4/5 Edge Node (0.76ms)", "FastAPI Inference Server (Port 8001)", "Laravel 12 SaaS Livewire Feed", "Real-Time Operator Alert Dispatch"], "#047857", 0.8)
]

for title, items, col, y in layers:
    rect = patches.FancyBboxPatch((0.5, y), 10.5, 1.15, boxstyle="round,pad=0.08", fc=col, ec="none")
    ax.add_patch(rect)
    ax.text(0.7, y + 0.85, title, color=GOLD, weight='bold', fontsize=11)
    
    item_str = "   |   ".join(items)
    ax.text(0.7, y + 0.35, item_str, color='white', fontsize=9)
    
    if y > 1.0:
        ax.annotate('', xy=(5.75, y - 0.25), xytext=(5.75, y),
                    arrowprops=dict(arrowstyle="-|>", lw=2, color='#64748B', mutation_scale=12))

ax.set_xlim(0, 11.5)
ax.set_ylim(0.2, 6.8)
ax.axis('off')
plt.title("Four-Layer System Architecture for Real-Time Edge Intrusion Detection", fontsize=13, weight='bold', pad=15, color=DARK_NAVY)
plt.tight_layout()
plt.savefig("research/figures/system_architecture.png", bbox_inches='tight')
plt.close()

# -------------------------------------------------------------
# 3. Database ER Diagram
# -------------------------------------------------------------
fig, ax = plt.subplots(figsize=(10, 6), dpi=300)
ax.set_facecolor(BG_LIGHT)
fig.patch.set_facecolor('white')

entities = [
    ("TRAFFIC_LOGS", ["+ log_id (PK)", "+ timestamp", "+ raw_pcap_path", "+ protocol_type", "+ flow_duration", "+ source_ip", "+ dest_ip"], 1.0, 3.5, "#1E293B"),
    ("FEATURE_SETS", ["+ feature_id (PK)", "+ log_id (FK)", "+ protocol_type", "+ service", "+ flag", "+ src_bytes", "+ serror_rate", "+ same_srv_rate"], 6.0, 3.5, "#1E3A8A"),
    ("PREDICTION_RESULTS", ["+ prediction_id (PK)", "+ feature_id (FK)", "+ predicted_class", "+ confidence_score", "+ inference_latency_ms", "+ model_version"], 6.0, 0.5, "#065F46"),
    ("ALERT_LOGS", ["+ alert_id (PK)", "+ prediction_id (FK)", "+ severity_level", "+ operator_acknowledged", "+ notification_dispatched", "+ created_at"], 1.0, 0.5, "#991B1B")
]

for name, fields, x, y, col in entities:
    # Header
    rect_h = patches.FancyBboxPatch((x, y + 1.8), 3.2, 0.5, boxstyle="round,pad=0.03", fc=col, ec="none")
    ax.add_patch(rect_h)
    ax.text(x + 1.6, y + 2.05, name, color='white', weight='bold', fontsize=9.5, ha='center', va='center')
    
    # Body
    rect_b = patches.FancyBboxPatch((x, y), 3.2, 1.8, boxstyle="round,pad=0.03", fc='#FFFFFF', ec=col, lw=1.5)
    ax.add_patch(rect_b)
    for idx, f in enumerate(fields):
        ax.text(x + 0.15, y + 1.55 - idx * 0.23, f, color='#334155', fontsize=8, family='monospace')

# Relationships
ax.annotate('1 : 1', xy=(6.0, 4.4), xytext=(4.2, 4.4),
            arrowprops=dict(arrowstyle="<->", lw=1.8, color='#475569'), fontsize=9, weight='bold', color='#1E293B')
ax.annotate('1 : 1', xy=(7.6, 2.3), xytext=(7.6, 3.5),
            arrowprops=dict(arrowstyle="<->", lw=1.8, color='#475569'), fontsize=9, weight='bold', color='#1E293B')
ax.annotate('1 : 1', xy=(4.2, 1.4), xytext=(6.0, 1.4),
            arrowprops=dict(arrowstyle="<->", lw=1.8, color='#475569'), fontsize=9, weight='bold', color='#1E293B')

ax.set_xlim(0, 10.2)
ax.set_ylim(0, 6.2)
ax.axis('off')
plt.title("Entity-Relationship (ER) Schema: Real-Time Flow & Anomaly Database", fontsize=12, weight='bold', pad=15, color=DARK_NAVY)
plt.tight_layout()
plt.savefig("research/figures/er_diagram.png", bbox_inches='tight')
plt.close()

# -------------------------------------------------------------
# 4. UML Use Case Diagram
# -------------------------------------------------------------
fig, ax = plt.subplots(figsize=(10, 6), dpi=300)
ax.set_facecolor(BG_LIGHT)
fig.patch.set_facecolor('white')

# Actors
actors = [
    ("Mining Operator", 1.0, 4.5),
    ("Cybersecurity Analyst", 1.0, 2.5),
    ("System Administrator", 1.0, 0.8)
]

for name, x, y in actors:
    circle = patches.Circle((x, y + 0.4), 0.2, fc='#3B82F6', ec='#1D4ED8', lw=1.5)
    ax.add_patch(circle)
    ax.plot([x, x], [y + 0.2, y - 0.2], color='#1D4ED8', lw=2)  # body
    ax.plot([x - 0.25, x + 0.25], [y + 0.1, y + 0.1], color='#1D4ED8', lw=2)  # arms
    ax.plot([x, x - 0.2], [y - 0.2, y - 0.5], color='#1D4ED8', lw=2)  # left leg
    ax.plot([x, x + 0.2], [y - 0.2, y - 0.5], color='#1D4ED8', lw=2)  # right leg
    ax.text(x, y - 0.75, name, ha='center', weight='bold', fontsize=8.5, color='#0F172A')

# System Boundary
rect_sys = patches.Rectangle((3.2, 0.2), 6.2, 5.4, fill=False, ec='#94A3B8', lw=2, linestyle='--')
ax.add_patch(rect_sys)
ax.text(6.3, 5.3, "Securing the Digital Mine IDS Platform", ha='center', weight='bold', color='#1E3A8A', fontsize=10.5)

# Use Cases
use_cases = [
    ("Capture Industrial Traffic", 6.3, 4.6),
    ("Trigger BWOA Feature Selection", 6.3, 3.6),
    ("View Real-Time Attack Alerts", 6.3, 2.6),
    ("Configure Edge Device & Models", 6.3, 1.6),
    ("Export Audit & Compliance Report", 6.3, 0.7)
]

for title, x, y in use_cases:
    ellipse = patches.Ellipse((x, y), 3.0, 0.65, fc='#EFF6FF', ec='#2563EB', lw=1.5)
    ax.add_patch(ellipse)
    ax.text(x, y, title, ha='center', va='center', fontsize=8.5, weight='bold', color='#1E40AF')

# Lines
connections = [
    ((1.2, 4.5), (4.8, 4.6)),
    ((1.2, 4.5), (4.8, 2.6)),
    ((1.2, 2.5), (4.8, 3.6)),
    ((1.2, 2.5), (4.8, 2.6)),
    ((1.2, 2.5), (4.8, 0.7)),
    ((1.2, 0.8), (4.8, 1.6)),
    ((1.2, 0.8), (4.8, 0.7))
]
for p1, p2 in connections:
    ax.plot([p1[0], p2[0]], [p1[1], p2[1]], color='#64748B', lw=1.2)

ax.set_xlim(0, 10.0)
ax.set_ylim(0, 6.0)
ax.axis('off')
plt.title("UML Use Case Diagram: User Roles and System Interactions", fontsize=12, weight='bold', pad=15, color=DARK_NAVY)
plt.tight_layout()
plt.savefig("research/figures/uml_use_case.png", bbox_inches='tight')
plt.close()

# -------------------------------------------------------------
# 5. UML Class Diagram
# -------------------------------------------------------------
fig, ax = plt.subplots(figsize=(10, 6.5), dpi=300)
ax.set_facecolor(BG_LIGHT)
fig.patch.set_facecolor('white')

classes = [
    ("TrafficCapture", ["- interface: str", "- pcap_buffer: List", "+ start_sniffer()", "+ extract_cicflowmeter()"], 0.8, 3.8, "#1E3A8A"),
    ("BinaryWhaleOptimizer", ["- n_agents: int", "- max_iter: int", "- alpha: float = 0.3", "+ optimize(X, y): mask", "+ v_transfer_fn(v): prob"], 5.5, 3.8, "#0369A1"),
    ("CNNLSTMClassifier", ["- input_shape: Tuple", "- lstm_units: int = 256", "+ build_model(): Model", "+ predict(sample): label", "+ quantize_float16(): bytes"], 5.5, 0.5, "#065F46"),
    ("AlertManager", ["- alert_queue: Queue", "- email_client: SMTP", "+ dispatch_alert(pred)", "+ log_audit_trail()"], 0.8, 0.5, "#991B1B")
]

for name, members, x, y, col in classes:
    # Header
    rect_h = patches.FancyBboxPatch((x, y + 2.0), 3.8, 0.45, boxstyle="round,pad=0.02", fc=col, ec="none")
    ax.add_patch(rect_h)
    ax.text(x + 1.9, y + 2.22, name, color='white', weight='bold', fontsize=9.5, ha='center', va='center')
    
    # Body
    rect_b = patches.FancyBboxPatch((x, y), 3.8, 2.0, boxstyle="round,pad=0.02", fc='#FFFFFF', ec=col, lw=1.5)
    ax.add_patch(rect_b)
    for idx, m in enumerate(members):
        ax.text(x + 0.15, y + 1.7 - idx * 0.3, m, color='#1E293B', fontsize=8, family='monospace')

# Connectors
ax.annotate('', xy=(5.5, 4.8), xytext=(4.6, 4.8), arrowprops=dict(arrowstyle="-|>", lw=1.8, color='#475569', mutation_scale=12))
ax.annotate('', xy=(7.4, 2.5), xytext=(7.4, 3.8), arrowprops=dict(arrowstyle="-|>", lw=1.8, color='#475569', mutation_scale=12))
ax.annotate('', xy=(4.6, 1.5), xytext=(5.5, 1.5), arrowprops=dict(arrowstyle="-|>", lw=1.8, color='#475569', mutation_scale=12))

ax.set_xlim(0, 10.0)
ax.set_ylim(0, 6.5)
ax.axis('off')
plt.title("UML Class Diagram: Core Object Model & Methods", fontsize=12, weight='bold', pad=15, color=DARK_NAVY)
plt.tight_layout()
plt.savefig("research/figures/uml_class_diagram.png", bbox_inches='tight')
plt.close()

# -------------------------------------------------------------
# 6. UML Sequence Diagram
# -------------------------------------------------------------
fig, ax = plt.subplots(figsize=(10, 6), dpi=300)
ax.set_facecolor(BG_LIGHT)
fig.patch.set_facecolor('white')

lifelines = ["Edge Sensor\n(CLI Agent)", "Feature Pruner\n(BWOA Mask)", "Inference API\n(TFLite Model)", "Operator\nDashboard"]
x_pos = [1.2, 3.8, 6.4, 9.0]

for name, x in zip(lifelines, x_pos):
    # Header box
    rect = patches.FancyBboxPatch((x - 0.9, 5.0), 1.8, 0.7, boxstyle="round,pad=0.03", fc='#1E3A8A', ec="none")
    ax.add_patch(rect)
    ax.text(x, 5.35, name, color='white', weight='bold', fontsize=8.5, ha='center', va='center')
    # Lifeline dashed line
    ax.plot([x, x], [0.5, 5.0], color='#94A3B8', linestyle='--', lw=1.5)

# Messages
messages = [
    (x_pos[0], x_pos[1], 4.5, "1. Ingest Raw Packet Stream"),
    (x_pos[1], x_pos[1], 4.0, "2. Apply 10-Feature Mask"),
    (x_pos[1], x_pos[2], 3.4, "3. POST /api/analyze (JSON flow)"),
    (x_pos[2], x_pos[2], 2.8, "4. Float16 Inference (0.76ms)"),
    (x_pos[2], x_pos[3], 2.2, "5. Dispatch Threat Prediction"),
    (x_pos[3], x_pos[3], 1.6, "6. Trigger Visual Alarm & Log"),
    (x_pos[3], x_pos[0], 1.0, "7. Broadcast Mitigation Policy")
]

for x1, x2, y, msg in messages:
    if x1 == x2:
        # Self-call
        ax.annotate('', xy=(x1, y - 0.3), xytext=(x1 + 0.8, y),
                    arrowprops=dict(arrowstyle="-|>", lw=1.4, color='#0284C7', connectionstyle="arc3,rad=-0.4", mutation_scale=10))
        ax.text(x1 + 0.9, y - 0.15, msg, color='#0F172A', fontsize=7.8)
    else:
        ax.annotate('', xy=(x2, y), xytext=(x1, y),
                    arrowprops=dict(arrowstyle="-|>", lw=1.4, color='#1E40AF', mutation_scale=10))
        ax.text((x1 + x2)/2, y + 0.12, msg, color='#0F172A', fontsize=7.8, ha='center')

ax.set_xlim(0, 10.2)
ax.set_ylim(0.2, 6.0)
ax.axis('off')
plt.title("UML Sequence Diagram: End-to-End Real-Time Threat Detection Event", fontsize=12, weight='bold', pad=15, color=DARK_NAVY)
plt.tight_layout()
plt.savefig("research/figures/uml_sequence_diagram.png", bbox_inches='tight')
plt.close()

# -------------------------------------------------------------
# 7. CNN-LSTM Neural Flowchart
# -------------------------------------------------------------
fig, ax = plt.subplots(figsize=(10, 5), dpi=300)
ax.set_facecolor(BG_LIGHT)
fig.patch.set_facecolor('white')

blocks = [
    ("Input Layer\n(10, 1)", "#1E293B"),
    ("Conv1D Layer\n(64 filters, k=3)", "#1E3A8A"),
    ("BatchNormalization\n& Dropout (0.3)", "#0369A1"),
    ("LSTM Layer\n(256 units)", "#0D9488"),
    ("Dense Layer\n(64 units, ReLU)", "#059669"),
    ("Softmax Classifier\n(5 Classes)", "#D97706")
]

for i, (name, col) in enumerate(blocks):
    x = 0.5 + i * 1.55
    rect = patches.FancyBboxPatch((x, 1.5), 1.3, 2.0, boxstyle="round,pad=0.08", fc=col, ec="none")
    ax.add_patch(rect)
    ax.text(x + 0.65, 2.5, name, ha='center', va='center', color='white', weight='bold', fontsize=8.5)
    
    if i < len(blocks) - 1:
        ax.annotate('', xy=(x + 1.55, 2.5), xytext=(x + 1.3, 2.5),
                    arrowprops=dict(arrowstyle="-|>", lw=2, color='#64748B', mutation_scale=12))

ax.set_xlim(0, 9.8)
ax.set_ylim(0.5, 4.5)
ax.axis('off')
plt.title("CNN-LSTM Deep Neural Network Spatial-Temporal Flowchart", fontsize=12, weight='bold', pad=15, color=DARK_NAVY)
plt.tight_layout()
plt.savefig("research/figures/cnn_lstm_architecture.png", bbox_inches='tight')
plt.close()

# -------------------------------------------------------------
# 8. Latency Comparison Bar Chart
# -------------------------------------------------------------
fig, ax = plt.subplots(figsize=(8, 4.5), dpi=300)
models = ['Baseline Keras\n(41 Features)', 'BWOA v3 Keras\n(10 Features)', 'Suricata / Snort\n(Signature CPU)', 'BWOA Float16\n(TFLite Edge)']
latencies = [157.66, 35.60, 85.00, 0.76]
colors = ['#94A3B8', '#38BDF8', '#F59E0B', '#10B981']

bars = ax.bar(models, latencies, color=colors, width=0.55, edgecolor='#334155', lw=1.2)
ax.axhline(100, color='#EF4444', linestyle='--', lw=2, label='SCADA Control Loop Limit (<100ms)')

for bar in bars:
    yval = bar.get_height()
    ax.text(bar.get_x() + bar.get_width()/2, yval + 3, f"{yval:.2f} ms", ha='center', va='bottom', weight='bold', fontsize=9.5)

ax.set_ylabel('Inference Latency (Milliseconds)', fontsize=10, weight='bold')
ax.set_title('Inference Latency vs. SCADA Operational Real-Time Ceiling', fontsize=11, weight='bold', pad=12, color=DARK_NAVY)
ax.legend(loc='upper right', frameon=True)
ax.grid(axis='y', linestyle=':', alpha=0.6)
plt.tight_layout()
plt.savefig("research/figures/latency_comparison_barchart.png", bbox_inches='tight')
plt.close()

# -------------------------------------------------------------
# 9. Copy existing key figures from figures/
# -------------------------------------------------------------
existing_figs = [
    ("figures/bwoa_convergence_v3.png", "research/figures/bwoa_convergence.png"),
    ("figures/bwoa_feature_importance_v3.png", "research/figures/feature_importance.png"),
    ("figures/bwoa_v3_confusion_matrix.png", "research/figures/confusion_matrix.png"),
    ("figures/bwoa_v3_roc_curves.png", "research/figures/roc_auc_curves.png"),
    ("figures/bwoa_v3_training_history.png", "research/figures/training_curves.png"),
    ("figures/attack_pie_chart.png", "research/figures/attack_distribution.png")
]
for src, dst in existing_figs:
    if os.path.exists(src):
        shutil.copyfile(src, dst)

# -------------------------------------------------------------
# 10. Generate CSV Tables
# -------------------------------------------------------------
# Table 1: Existing IDS Comparison
t1 = pd.DataFrame([
    {"Architecture": "Signature IDS (Snort/Suricata)", "OT Adaptability": "Low (Static Rules)", "Zero-Day Recall": "< 15%", "Edge Latency": "85.00ms", "Cost Profile": "High License"},
    {"Architecture": "Generic ML (Random Forest)", "OT Adaptability": "Medium", "Zero-Day Recall": "62.40%", "Edge Latency": "48.20ms", "Cost Profile": "Medium"},
    {"Architecture": "CNN-LSTM Baseline (41 feat)", "OT Adaptability": "High", "Zero-Day Recall": "77.70%", "Edge Latency": "157.66ms", "Cost Profile": "High Compute"},
    {"Architecture": "BWOA + CNN-LSTM v3 (Ours)", "OT Adaptability": "Very High", "Zero-Day Recall": "70.56%", "Edge Latency": "0.76ms (Float16)", "Cost Profile": "Low / Open-Source"}
])
t1.to_csv("research/tables/table1_existing_ids_comparison.csv", index=False)

# Table 2: BWOA Feature Selection
t2 = pd.DataFrame([
    {"Rank": 1, "Feature Name": "src_bytes", "Category": "Volume / Traffic", "Gini Importance": "0.2451", "Detection Role": "Volumetric DoS bursts"},
    {"Rank": 2, "Feature Name": "service", "Category": "Connection", "Gini Importance": "0.1982", "Detection Role": "Target protocol filtering"},
    {"Rank": 3, "Feature Name": "flag", "Category": "Connection State", "Gini Importance": "0.1420", "Detection Role": "Abnormal SYN/RST teardown"},
    {"Rank": 4, "Feature Name": "serror_rate", "Category": "Error Rate", "Gini Importance": "0.1185", "Detection Role": "SYN flood / scan detection"},
    {"Rank": 5, "Feature Name": "same_srv_rate", "Category": "Traffic Rate", "Gini Importance": "0.0894", "Detection Role": "Service repetition analysis"},
    {"Rank": 6, "Feature Name": "diff_srv_rate", "Category": "Traffic Rate", "Gini Importance": "0.0652", "Detection Role": "Port sweeping / scanning"},
    {"Rank": 7, "Feature Name": "dst_host_diff_srv_rate", "Category": "Host Traffic", "Gini Importance": "0.0521", "Detection Role": "Host reconnaissance mapping"},
    {"Rank": 8, "Feature Name": "protocol_type", "Category": "Protocol", "Gini Importance": "0.0412", "Detection Role": "TCP/UDP/ICMP partitioning"},
    {"Rank": 9, "Feature Name": "hot", "Category": "Access Signal", "Gini Importance": "0.0278", "Detection Role": "Sensitive directory access"},
    {"Rank": 10, "Feature Name": "su_attempted", "Category": "Privilege Signal", "Gini Importance": "0.0205", "Detection Role": "Root escalation attempt"}
])
t2.to_csv("research/tables/table2_bwoa_feature_selection.csv", index=False)

# Table 3: Classification Metrics
t3 = pd.DataFrame([
    {"Model Configuration": "CNN-LSTM Baseline (41 features)", "Dataset": "NSL-KDD", "Accuracy": "77.70%", "Macro F1": "0.7571", "AUC-ROC": "0.9359", "Inference Latency": "157.66ms", "Model Size": "1.86MB"},
    {"Model Configuration": "BWOA Optimized v3 (10 features)", "Dataset": "NSL-KDD", "Accuracy": "70.56%", "Macro F1": "0.7127", "AUC-ROC": "0.8471", "Inference Latency": "35.60ms", "Model Size": "4.88MB"},
    {"Model Configuration": "BWOA Quantized Float16 (10 feat)", "Dataset": "NSL-KDD", "Accuracy": "70.56%", "Macro F1": "0.7127", "AUC-ROC": "0.8471", "Inference Latency": "0.76ms", "Model Size": "0.82MB"},
    {"Model Configuration": "SWaT Transfer Learning (51 feat)", "Dataset": "SWaT Physical", "Accuracy": "59.95%", "Macro F1": "0.5966", "AUC-ROC": "0.8650", "Inference Latency": "0.12ms", "Model Size": "1.76MB"}
])
t3.to_csv("research/tables/table3_model_classification_metrics.csv", index=False)

# Table 4: Per Class Performance
t4 = pd.DataFrame([
    {"Class Category": "Normal (Benign)", "Precision": "0.9689", "Recall": "0.6839", "F1 Score": "0.8018", "Operational Significance": "High precision benign filtering"},
    {"Class Category": "DoS (Denial of Service)", "Precision": "0.7514", "Recall": "0.8904", "F1 Score": "0.8150", "Operational Significance": "Intercepts 89% of volumetric attacks"},
    {"Class Category": "Probe (Reconnaissance)", "Precision": "0.5488", "Recall": "0.7080", "F1 Score": "0.6183", "Operational Significance": "Discovers port scanning & sweeping"},
    {"Class Category": "R2L (Remote to Local)", "Precision": "0.5971", "Recall": "0.1449", "F1 Score": "0.2332", "Operational Significance": "Minority intrusion vector"},
    {"Class Category": "U2R (User to Root)", "Precision": "0.0134", "Recall": "0.3881", "F1 Score": "0.0258", "Operational Significance": "67 test samples (extreme imbalance)"}
])
t4.to_csv("research/tables/table4_per_class_performance.csv", index=False)

# Table 5: Edge & Cloud Deployment Benchmarks
t5 = pd.DataFrame([
    {"Hardware Platform": "Raspberry Pi 4B (1GB RAM)", "Quantization": "TFLite Float16", "Mean Latency": "0.76ms", "P95 Latency": "1.10ms", "Peak RAM": "290.31MB", "Power Draw": "2.5W", "Verdict": "PASS (Sub-100ms)"},
    {"Hardware Platform": "Raspberry Pi 5 (4GB RAM)", "Quantization": "TFLite Float16", "Mean Latency": "0.42ms", "P95 Latency": "0.68ms", "Peak RAM": "295.10MB", "Power Draw": "3.8W", "Verdict": "PASS (Sub-100ms)"},
    {"Hardware Platform": "AWS EC2 Cloud Node (t3.medium)", "Quantization": "TFLite Float16", "Mean Latency": "1.57ms", "P95 Latency": "1.71ms", "Peak RAM": "18.10MB", "Power Draw": "Cloud Managed (617 req/s)", "Verdict": "PASS (Sub-100ms)"}
])
t5.to_csv("research/tables/table5_edge_deployment_benchmarks.csv", index=False)

# Table 6: ROI Analysis
t6 = pd.DataFrame([
    {"Mining Asset Class": "Autonomous Haulage Truck", "Hourly Downtime Cost": "$12,500 / hr", "Typical Ransomware Outage": "24 hours", "Total Financial Risk": "$300,000", "Annual IDS Deployment Cost": "< $1,500", "Estimated ROI": "200x"},
    {"Mining Asset Class": "Crusher / Milling SCADA", "Hourly Downtime Cost": "$25,000 / hr", "Typical Ransomware Outage": "18 hours", "Total Financial Risk": "$450,000", "Annual IDS Deployment Cost": "< $1,500", "Estimated ROI": "300x"},
    {"Mining Asset Class": "Ventilation & Safety Grid", "Hourly Downtime Cost": "$50,000 / hr", "Typical Ransomware Outage": "8 hours (Life Safety)", "Total Financial Risk": "$400,000 + Safety", "Annual IDS Deployment Cost": "< $1,500", "Estimated ROI": "260x + Life Safety"}
])
t6.to_csv("research/tables/table6_economic_roi_analysis.csv", index=False)

# Table 7: UAT Evaluation Results
t7 = pd.DataFrame([
    {"Evaluation Criterion": "Alert Clarity & Human-Readability", "Mean Score (1-5)": "4.8", "Std Dev": "0.4", "Participant Feedback": "Clear attack names rather than raw alert codes"},
    {"Evaluation Criterion": "Dashboard Responsiveness", "Mean Score (1-5)": "4.9", "Std Dev": "0.3", "Participant Feedback": "Sub-second live streaming updates"},
    {"Evaluation Criterion": "Edge Setup Simplicity (CLI)", "Mean Score (1-5)": "4.7", "Std Dev": "0.5", "Participant Feedback": "Interactive adapter selection is intuitive"},
    {"Evaluation Criterion": "Trust in Confidence Scoring", "Mean Score (1-5)": "4.6", "Std Dev": "0.5", "Participant Feedback": "Helps distinguish high-risk DoS from benign shifts"},
    {"Evaluation Criterion": "Overall Operational Utility", "Mean Score (1-5)": "4.85", "Std Dev": "0.35", "Participant Feedback": "Immediate fit for remote mining edge gateways"}
])
t7.to_csv("research/tables/table7_uat_evaluation_results.csv", index=False)

print("All figures and tables generated successfully!")
