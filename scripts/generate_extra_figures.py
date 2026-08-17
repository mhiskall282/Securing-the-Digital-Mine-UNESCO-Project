"""Generate additional UML and Interface Wireframe figures for the 35-page DSR paper."""
import os
import matplotlib.pyplot as plt
import matplotlib.patches as patches
import numpy as np

os.makedirs("research/figures", exist_ok=True)

plt.rcParams['font.sans-serif'] = 'Arial'
plt.rcParams['font.family'] = 'sans-serif'

UNESCO_BLUE = "#00529B"
DARK_NAVY = "#0B1D3A"
CYAN = "#00A3E0"
GOLD = "#D4AF37"
EMERALD = "#10B981"
BG_LIGHT = "#F8FAFC"
BORDER_COLOR = "#CBD5E1"

# -------------------------------------------------------------
# 1. UML Activity Diagram
# -------------------------------------------------------------
fig, ax = plt.subplots(figsize=(10, 7.5), dpi=300)
ax.set_facecolor(BG_LIGHT)
fig.patch.set_facecolor('white')

# Swimlanes
ax.axvline(3.3, color='#94A3B8', linestyle='--', lw=1.5)
ax.axvline(6.6, color='#94A3B8', linestyle='--', lw=1.5)

ax.text(1.65, 7.1, "Edge Sniffer Client\n(CLI Agent)", ha='center', weight='bold', color=DARK_NAVY, fontsize=10)
ax.text(4.95, 7.1, "Inference API Service\n(FastAPI / TFLite)", ha='center', weight='bold', color=DARK_NAVY, fontsize=10)
ax.text(8.25, 7.1, "Operator Dashboard\n(Laravel Livewire)", ha='center', weight='bold', color=DARK_NAVY, fontsize=10)

# Start Node
start_circle = patches.Circle((1.65, 6.4), 0.15, fc=DARK_NAVY, ec="none")
ax.add_patch(start_circle)

# Activities
activities = [
    ("Hook Network Adapter\n& Promiscuous Sniffing", 1.65, 5.5, "#1E3A8A"),
    ("Extract 10 BWOA\nOptimized Features", 1.65, 4.3, "#1E3A8A"),
    ("Construct & Transmit\nJSON Telemetry Payload", 1.65, 3.1, "#1E3A8A"),
    ("Validate Device Token\n& Ingest Flow Vector", 4.95, 3.1, "#0369A1"),
    ("Execute Float16\nCNN-LSTM Inference", 4.95, 1.9, "#0369A1"),
    ("Evaluate Threat Score\n& Class Probability", 4.95, 0.7, "#0369A1"),
    ("Broadcast Livewire Alert\n& Trigger Visual Alarm", 8.25, 0.7, "#047857"),
    ("Execute Mitigation\n(Isolate PLC Subnet)", 8.25, 1.9, "#B91C1C")
]

for title, x, y, col in activities:
    rect = patches.FancyBboxPatch((x - 1.3, y - 0.35), 2.6, 0.7, boxstyle="round,pad=0.08", fc=col, ec="none")
    ax.add_patch(rect)
    ax.text(x, y, title, ha='center', va='center', color='white', weight='bold', fontsize=8)

# Decision Diamond
diamond = patches.Polygon([[8.25, 3.5], [8.95, 3.1], [8.25, 2.7], [7.55, 3.1]], fc="#F59E0B", ec=DARK_NAVY, lw=1.5)
ax.add_patch(diamond)
ax.text(8.25, 3.1, "Attack\nDetected?", ha='center', va='center', weight='bold', fontsize=7.5, color=DARK_NAVY)

# Arrows
ax.annotate('', xy=(1.65, 5.85), xytext=(1.65, 6.25), arrowprops=dict(arrowstyle="-|>", lw=1.5, color='#475569'))
ax.annotate('', xy=(1.65, 4.65), xytext=(1.65, 5.15), arrowprops=dict(arrowstyle="-|>", lw=1.5, color='#475569'))
ax.annotate('', xy=(1.65, 3.45), xytext=(1.65, 3.95), arrowprops=dict(arrowstyle="-|>", lw=1.5, color='#475569'))
ax.annotate('', xy=(3.65, 3.1), xytext=(2.95, 3.1), arrowprops=dict(arrowstyle="-|>", lw=1.5, color='#475569'))
ax.annotate('', xy=(4.95, 2.25), xytext=(4.95, 2.75), arrowprops=dict(arrowstyle="-|>", lw=1.5, color='#475569'))
ax.annotate('', xy=(4.95, 1.05), xytext=(4.95, 1.55), arrowprops=dict(arrowstyle="-|>", lw=1.5, color='#475569'))
ax.annotate('', xy=(6.95, 0.7), xytext=(6.25, 0.7), arrowprops=dict(arrowstyle="-|>", lw=1.5, color='#475569'))
ax.annotate('', xy=(8.25, 2.7), xytext=(8.25, 1.05), arrowprops=dict(arrowstyle="-|>", lw=1.5, color='#475569'))
ax.annotate('', xy=(8.25, 2.25), xytext=(8.25, 2.7), arrowprops=dict(arrowstyle="-|>", lw=1.5, color='#B91C1C'))
ax.text(8.4, 2.45, "Yes", weight='bold', color='#B91C1C', fontsize=8)

ax.set_xlim(0, 10.0)
ax.set_ylim(0, 7.5)
ax.axis('off')
plt.title("UML Activity Diagram: End-to-End Autonomous Threat Detection & Response Lifecycle", fontsize=11, weight='bold', pad=12, color=DARK_NAVY)
plt.tight_layout()
plt.savefig("research/figures/uml_activity_diagram.png", bbox_inches='tight')
plt.close()

# -------------------------------------------------------------
# 2. Interface Wireframe: Real-Time SaaS Dashboard
# -------------------------------------------------------------
fig, ax = plt.subplots(figsize=(11, 6.5), dpi=300)
ax.set_facecolor("#0F172A")
fig.patch.set_facecolor('#0B132B')

# Navigation Bar
nav = patches.Rectangle((0.2, 5.8), 10.6, 0.6, fc="#1E293B", ec="none")
ax.add_patch(nav)
ax.text(0.5, 6.1, "⛏️ SECURING THE DIGITAL MINE | OT SCADA Threat Console", color="#38BDF8", weight='bold', fontsize=10, va='center')
ax.text(8.5, 6.1, "🟢 Edge Gateway: ONLINE (0.76ms)", color="#10B981", weight='bold', fontsize=8.5, va='center')

# Left Panel: Live Flow Stream
card1 = patches.FancyBboxPatch((0.2, 0.4), 6.5, 5.2, boxstyle="round,pad=0.08", fc="#1E293B", ec="#334155", lw=1.2)
ax.add_patch(card1)
ax.text(0.4, 5.3, "LIVE TELEMETRY FLOW STREAM (Modbus / SCADA)", color="#F1F5F9", weight='bold', fontsize=9.5)

flows = [
    ("[04:12:01] eth0 -> IP 192.168.10.45:502  | serror: 0.00 | same_srv: 1.00 -> Normal (Conf: 99.4%)", "#10B981"),
    ("[04:12:03] eth0 -> IP 192.168.10.46:502  | serror: 0.00 | same_srv: 1.00 -> Normal (Conf: 98.9%)", "#10B981"),
    ("[04:12:05] eth0 -> IP 192.168.10.99:502  | serror: 0.95 | diff_srv: 0.90 -> DoS Attack (Conf: 96.2%)", "#EF4444"),
    ("[04:12:07] eth0 -> IP 192.168.10.102:80  | dst_host_diff: 0.85          -> Probe (Conf: 88.5%)", "#F59E0B"),
    ("[04:12:09] eth0 -> IP 192.168.10.45:502  | serror: 0.00 | same_srv: 1.00 -> Normal (Conf: 99.1%)", "#10B981"),
    ("[04:12:11] eth0 -> IP 192.168.10.55:502  | serror: 0.00 | same_srv: 1.00 -> Normal (Conf: 99.7%)", "#10B981"),
    ("[04:12:13] eth0 -> IP 192.168.10.77:21   | hot: 3 | su_attempted: 1      -> U2R Root Escalation", "#EF4444")
]
for idx, (f_text, f_col) in enumerate(flows):
    ax.text(0.4, 4.7 - idx * 0.55, f_text, color=f_col, fontsize=7.5, family='monospace')

# Right Panel: Security Gauges & Threat Matrix
card2 = patches.FancyBboxPatch((7.0, 0.4), 3.8, 5.2, boxstyle="round,pad=0.08", fc="#1E293B", ec="#334155", lw=1.2)
ax.add_patch(card2)
ax.text(7.2, 5.3, "SCADA DEFENSE METRICS", color="#F1F5F9", weight='bold', fontsize=9.5)

metrics = [
    ("Inference Latency", "0.76 ms", "Target: < 100 ms", "#10B981"),
    ("Model Memory Size", "0.82 MB", "Quantized Float16", "#38BDF8"),
    ("Pruned Feature Count", "10 / 41", "75.61% Compression", "#A855F7"),
    ("Mining Concession", "Tarkwa Mine, GH", "SAG Mill Subnet B", "#F59E0B")
]
for idx, (m_title, m_val, m_sub, m_col) in enumerate(metrics):
    y_m = 4.4 - idx * 1.05
    bx = patches.FancyBboxPatch((7.2, y_m - 0.25), 3.4, 0.85, boxstyle="round,pad=0.05", fc="#0F172A", ec=m_col, lw=1.0)
    ax.add_patch(bx)
    ax.text(7.4, y_m + 0.35, m_title, color="#94A3B8", fontsize=7.5)
    ax.text(7.4, y_m, m_val, color=m_col, weight='bold', fontsize=12)
    ax.text(9.0, y_m, m_sub, color="#CBD5E1", fontsize=7, va='center')

ax.set_xlim(0, 11.0)
ax.set_ylim(0, 6.7)
ax.axis('off')
plt.title("Interface Design: Real-Time Multi-Tenant Mining SCADA Monitoring Dashboard", fontsize=11, weight='bold', pad=12, color='white')
plt.tight_layout()
plt.savefig("research/figures/dashboard_wireframe.png", bbox_inches='tight')
plt.close()

# -------------------------------------------------------------
# 3. Mining SCADA Process Flowchart
# -------------------------------------------------------------
fig, ax = plt.subplots(figsize=(11, 5.5), dpi=300)
ax.set_facecolor(BG_LIGHT)
fig.patch.set_facecolor('white')

units = [
    ("1. Primary Crushing\n& Ore Feed", "Coarse ore jaw crushers\n(Modbus RTU motor drives)", 1.2, 3.2, "#1E3A8A"),
    ("2. SAG / Ball Milling\nCircuit", "Grinding slurry density &\npower load controllers", 3.8, 3.2, "#1E3A8A"),
    ("3. Froth Flotation\nCells", "pH, air injection rate &\nreagent metering valves", 6.4, 3.2, "#1E3A8A"),
    ("4. Tailings Storage\nFacility (TSF)", "Pore pressure piezometers\n& decant return pumps", 9.0, 3.2, "#1E3A8A")
]

for title, desc, x, y, col in units:
    rect = patches.FancyBboxPatch((x - 1.1, y - 0.8), 2.2, 1.6, boxstyle="round,pad=0.08", fc=col, ec="none")
    ax.add_patch(rect)
    ax.text(x, y + 0.4, title, ha='center', va='center', color='white', weight='bold', fontsize=8.5)
    ax.text(x, y - 0.25, desc, ha='center', va='center', color='#E2E8F0', fontsize=7.5)
    
    if x < 8.0:
        ax.annotate('', xy=(x + 1.6, y), xytext=(x + 1.1, y),
                    arrowprops=dict(arrowstyle="-|>", lw=2, color='#475569', mutation_scale=12))

# Defensive Sensor Line
shield = patches.FancyBboxPatch((0.5, 0.6), 9.6, 1.2, boxstyle="round,pad=0.08", fc="#065F46", ec="#10B981", lw=1.5)
ax.add_patch(shield)
ax.text(5.3, 1.4, "BWOA + CNN-LSTM Edge IDS Defense Perimeter (Sub-100ms Autonomous Interception)", ha='center', color=GOLD, weight='bold', fontsize=9.5)
ax.text(5.3, 0.9, "Intercepts Unauthorized Modbus Setpoint Tampering, Malicious Coil Forcing, and Volumetric DoS Floods in 0.76ms", ha='center', color='white', fontsize=8)

for x in [1.2, 3.8, 6.4, 9.0]:
    ax.annotate('', xy=(x, 1.8), xytext=(x, 2.4),
                arrowprops=dict(arrowstyle="<->", lw=1.5, color='#10B981', linestyle=':'))

ax.set_xlim(0, 10.6)
ax.set_ylim(0.2, 5.0)
ax.axis('off')
plt.title("Cyber-Physical Mineral Processing SCADA Circuit & Edge Defense Boundary", fontsize=11, weight='bold', pad=12, color=DARK_NAVY)
plt.tight_layout()
plt.savefig("research/figures/mining_scada_flowchart.png", bbox_inches='tight')
plt.close()

print("All extra research figures generated successfully!")
