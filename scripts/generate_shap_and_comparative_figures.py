#!/usr/bin/env python3
"""Generate publication-quality empirical figures for IEEE manuscript and defense.

Figures produced:
1. fig_feature_selection_comparison (.png, .pdf):
   Multi-panel empirical benchmark comparing All-41, BWOA-10, SHAP-10, MI-10, and Random-10.
2. fig_shap_class_attributions (.png, .pdf):
   Heatmap of per-class KernelSHAP feature attributions across 5 attack classes.
3. fig_latency_breakdown_scada (.png, .pdf):
   Hardware latency benchmark across physical tiers (Pi 3B, Pi 4B, Pi 5, AWS EC2) vs 100ms SCADA deadline.
4. fig_shap_waterfall_defense (.png, .pdf):
   Operator-facing plain-language SHAP waterfall explanation for a mining SCADA alert.
"""

import os
import sys
import numpy as np
import matplotlib.pyplot as plt
import matplotlib.patches as patches
import seaborn as sns

# Ensure target directories exist
os.makedirs("figures", exist_ok=True)
os.makedirs("research/figures", exist_ok=True)

# Publication styling matching academic-plotting guidelines
plt.rcParams.update({
    "font.family": "serif",
    "font.serif": ["Times New Roman", "DejaVu Serif", "Liberation Serif"],
    "font.size": 10,
    "axes.titlesize": 11,
    "axes.titleweight": "bold",
    "axes.labelsize": 10,
    "legend.fontsize": 8.5,
    "legend.frameon": False,
    "figure.dpi": 300,
    "savefig.dpi": 300,
    "savefig.bbox": "tight",
    "axes.spines.top": False,
    "axes.spines.right": False,
    "axes.grid": True,
    "grid.alpha": 0.15,
    "grid.linestyle": "-",
    "lines.linewidth": 1.8,
    "lines.markersize": 5,
})

# Curated Okabe-Ito / Ocean Dusk color palette
COLOR_BWOA = "#E76F51"     # Burnt Coral (Our Deployed Optimizer)
COLOR_SHAP = "#2A9D8F"     # Teal (IBA Karachi SHAP Selection)
COLOR_MI = "#E9C46A"       # Gold (Mutual Information)
COLOR_ALL = "#264653"      # Deep Navy (Full 41 Features)
COLOR_RAND = "#B0BEC5"     # Cool Gray (Random 10 Baseline)

# ===========================================================================
# 1. Feature Selection Multi-Panel Comparison (B2 & B3 CSV Benchmark Data)
# ===========================================================================
def plot_feature_selection_comparison():
    fig, (ax1, ax2) = plt.subplots(1, 2, figsize=(10, 4.2), dpi=300)

    # Panel A: Accuracy and Macro F1 on CNN-LSTM v4 & Classifiers
    methods = ["All-41", "BWOA-10 (Deployed)", "SHAP-10 (Empirical)", "MI-10", "Random-10 (Mean)"]
    acc_rf = [75.50, 76.20, 74.11, 74.90, 71.49]
    acc_xgb = [76.78, 73.30, 74.06, 76.00, 71.90]
    acc_cnn = [73.80, 71.51, 73.50, 72.10, 68.20]  # CNN-LSTM v4 benchmark

    x = np.arange(len(methods))
    width = 0.26

    bars1 = ax1.bar(x - width, acc_rf, width, label="Random Forest", color="#264653", alpha=0.9, edgecolor="white")
    bars2 = ax1.bar(x, acc_xgb, width, label="XGBoost", color="#2A9D8F", alpha=0.9, edgecolor="white")
    bars3 = ax1.bar(x + width, acc_cnn, width, label="CNN-LSTM v4 (Deep Learning)", color=COLOR_BWOA, alpha=0.95, edgecolor="white")

    # Annotate deployed method
    for b in [bars1[1], bars2[1], bars3[1]]:
        b.set_edgecolor("#B91C1C")
        b.set_linewidth(1.4)

    ax1.set_ylabel("Classification Accuracy (%)")
    ax1.set_title("(a) Accuracy across Feature Selection Strategies")
    ax1.set_xticks(x)
    ax1.set_xticklabels(["All-41\n(Full)", "BWOA-10\n(Deployed)", "SHAP-10\n(IBA Study)", "MI-10\n(Filter)", "Random-10\n(Control)"], fontsize=8.5)
    ax1.set_ylim(60, 82)
    ax1.axhline(70.0, color="#64748B", linestyle=":", lw=1.0, alpha=0.6, label="Acceptable Operational Baseline")
    ax1.legend(loc="lower left", fontsize=8)

    # Panel B: Per-Class F1 Trade-off on CNN-LSTM v4
    classes = ["Normal", "DoS", "Probe", "R2L", "U2R"]
    bwoa_f1 = [0.8522, 0.8101, 0.6473, 0.2131, 0.0336]
    shap_f1 = [0.7765, 0.8179, 0.6211, 0.4063, 0.0821]

    xc = np.arange(len(classes))
    w2 = 0.35

    ax2.bar(xc - w2/2, [v * 100 for v in bwoa_f1], w2, label="BWOA-10 (Optimized for Mining SCADA Volume)", color=COLOR_BWOA, edgecolor="white")
    ax2.bar(xc + w2/2, [v * 100 for v in shap_f1], w2, label="SHAP-10 (Enhanced Stealth Attack Sensitivity)", color=COLOR_SHAP, edgecolor="white")

    ax2.set_ylabel("F1-Score (%)")
    ax2.set_title("(b) Per-Class F1 Trade-off: BWOA vs SHAP")
    ax2.set_xticks(xc)
    ax2.set_xticklabels(classes, fontsize=9)
    ax2.set_ylim(0, 100)

    # Add value labels
    for i in range(len(classes)):
        ax2.text(xc[i] - w2/2, bwoa_f1[i]*100 + 1.5, f"{bwoa_f1[i]*100:.1f}", ha="center", fontsize=7.5, color="#1E293B", weight="bold")
        ax2.text(xc[i] + w2/2, shap_f1[i]*100 + 1.5, f"{shap_f1[i]*100:.1f}", ha="center", fontsize=7.5, color="#047857", weight="bold")

    ax2.legend(loc="upper right", fontsize=8)

    plt.tight_layout()
    for ext in ["png", "pdf"]:
        fig.savefig(f"figures/fig_feature_selection_comparison.{ext}", dpi=300)
        fig.savefig(f"research/figures/fig_feature_selection_comparison.{ext}", dpi=300)
    plt.close()
    print("[+] Generated: figures/fig_feature_selection_comparison.png/.pdf")


# ===========================================================================
# 2. KernelSHAP Per-Class Feature Attribution Heatmap (A1 CSV Data)
# ===========================================================================
def plot_shap_heatmap():
    features = [
        "flag", "service", "same_srv_rate", "dst_host_diff_srv_rate",
        "protocol_type", "serror_rate", "diff_srv_rate", "hot",
        "src_bytes", "su_attempted"
    ]
    classes = ["DoS", "Normal", "Probe", "R2L", "U2R"]

    # Values from experiments/shap_explainability/results/A1_shap_importance_per_class.csv
    data = np.array([
        [0.2432, 0.1617, 0.1627, 0.0707, 0.1269],  # flag
        [0.0346, 0.1627, 0.0643, 0.1220, 0.3257],  # service
        [0.1739, 0.0482, 0.0831, 0.0563, 0.0175],  # same_srv_rate
        [0.0396, 0.0130, 0.2106, 0.0486, 0.0361],  # dst_host_diff_srv_rate
        [0.0515, 0.0433, 0.0723, 0.0497, 0.0546],  # protocol_type
        [0.0622, 0.0539, 0.0667, 0.0418, 0.0245],  # serror_rate
        [0.0186, 0.0079, 0.1812, 0.0095, 0.0066],  # diff_srv_rate
        [0.0203, 0.0197, 0.0022, 0.0215, 0.1342],  # hot
        [0.0006, 0.0005, 0.0001, 0.0003, 0.0003],  # src_bytes
        [0.0001, 0.0001, 0.0003, 0.0002, 0.0003],  # su_attempted
    ])

    fig, ax = plt.subplots(figsize=(7.5, 4.8), dpi=300)
    sns.heatmap(
        data,
        annot=True,
        fmt=".3f",
        cmap="YlGnBu",
        xticklabels=classes,
        yticklabels=features,
        cbar_kws={"label": "Mean Absolute SHAP Value (|SHAP|)"},
        linewidths=1.0,
        linecolor="white",
        ax=ax,
        annot_kws={"size": 8.5, "weight": "bold"}
    )

    ax.set_title("KernelSHAP Multi-Class Feature Attribution Matrix (IBA Karachi Study)", pad=12)
    ax.set_xlabel("Industrial Attack Classification")
    ax.set_ylabel("BWOA Selected Network Telemetry Feature")

    plt.tight_layout()
    for ext in ["png", "pdf"]:
        fig.savefig(f"figures/fig_shap_class_attributions.{ext}", dpi=300)
        fig.savefig(f"research/figures/fig_shap_class_attributions.{ext}", dpi=300)
    plt.close()
    print("[+] Generated: figures/fig_shap_class_attributions.png/.pdf")


# ===========================================================================
# 3. Multi-Tier Hardware Latency & SCADA Deadline Margin (Table 5 + Pi 3B)
# ===========================================================================
def plot_hardware_latency_breakdown():
    fig, (ax1, ax2) = plt.subplots(1, 2, figsize=(10, 4.2), dpi=300, gridspec_kw={'width_ratios': [2, 1]})

    platforms = [
        "Raspberry Pi 3B\n(Cortex-A53, 1GB)",
        "Raspberry Pi 4B\n(Cortex-A72, 1GB)",
        "Raspberry Pi 5\n(Cortex-A76, 4GB)",
        "AWS EC2 Cloud\n(t3.medium Xeon)"
    ]

    mean_latencies = [32.53, 0.76, 0.42, 1.57]
    p95_latencies = [40.82, 1.10, 0.68, 1.71]
    peak_rams = [290.0, 290.31, 295.10, 18.10]

    y = np.arange(len(platforms))
    height = 0.32

    # Left Panel: Latency vs 100ms Ceiling
    rects1 = ax1.barh(y - height/2, mean_latencies, height, label="Mean Latency (ms)", color="#0284C7", edgecolor="white")
    rects2 = ax1.barh(y + height/2, p95_latencies, height, label="P95 Latency (ms)", color="#0369A1", edgecolor="white")

    ax1.axvline(100.0, color="#DC2626", linestyle="--", lw=1.8, label="SCADA Safety Ceiling (100 ms)")
    ax1.axvspan(100.0, 120.0, color="#FEE2E2", alpha=0.5)

    ax1.set_yticks(y)
    ax1.set_yticklabels(platforms, fontsize=8.5)
    ax1.invert_yaxis()
    ax1.set_xlabel("Inference Round-Trip Latency (ms)")
    ax1.set_xlim(0, 115)
    ax1.set_title("(a) Edge-to-Cloud Real-Time Inference vs SCADA Ceiling")
    ax1.legend(loc="upper right", fontsize=8)

    # Value annotations
    for i, (m, p) in enumerate(zip(mean_latencies, p95_latencies)):
        ax1.text(p + 2.0, y[i], f"P95: {p:.2f}ms\n(PASS)", va="center", fontsize=7.5, color="#0F172A", weight="bold")

    # Right Panel: Memory Footprint
    colors_ram = ["#10B981", "#10B981", "#10B981", "#3B82F6"]
    bars_ram = ax2.bar(range(len(platforms)), peak_rams, color=colors_ram, width=0.55, edgecolor="white")
    ax2.set_ylabel("Peak RAM Consumption (MB)")
    ax2.set_title("(b) Peak Memory Envelope")
    ax2.set_xticks(range(len(platforms)))
    ax2.set_xticklabels(["Pi 3B", "Pi 4B", "Pi 5", "EC2"], fontsize=8.5)
    ax2.set_ylim(0, 350)
    ax2.axhline(1024.0, color="#94A3B8", linestyle=":", lw=1.0)
    ax2.text(0.5, 315, "Device Limit: 1,024 MB", color="#64748B", fontsize=7.5, style="italic")

    for bar, val in zip(bars_ram, peak_rams):
        ax2.text(bar.get_x() + bar.get_width()/2, val + 6, f"{val:.1f}M", ha="center", fontsize=7.5, weight="bold")

    plt.tight_layout()
    for ext in ["png", "pdf"]:
        fig.savefig(f"figures/fig_latency_breakdown_scada.{ext}", dpi=300)
        fig.savefig(f"research/figures/fig_latency_breakdown_scada.{ext}", dpi=300)
    plt.close()
    print("[+] Generated: figures/fig_latency_breakdown_scada.png/.pdf")


# ===========================================================================
# 4. Operator SHAP Waterfall Alert Diagnostic (Defense Proof Point)
# ===========================================================================
def plot_shap_waterfall_defense():
    fig, ax = plt.subplots(figsize=(8.5, 4.4), dpi=300)

    steps = [
        "Prior Base Rate\nE[f(x)]",
        "serror_rate = 1.0\n(SYN Errors)",
        "flag = S0\n(Connection Reset)",
        "same_srv_rate = 1.0\n(Targeted Flooding)",
        "protocol = tcp\n(Raw Socket)",
        "Final Threat\nProbability f(x)"
    ]

    base = 0.360
    deltas = [base, 0.311, 0.308, 0.089, -0.076]
    final = 0.992

    # Running cumulative values
    running = [0.0]
    for d in deltas:
        running.append(running[-1] + d)

    y_pos = np.arange(len(steps))

    # Base step
    ax.barh(y_pos[0], base, height=0.55, color="#64748B", edgecolor="white", label="Empirical Prior")
    # Positive drivers
    ax.barh(y_pos[1], 0.311, left=base, height=0.55, color="#DC2626", edgecolor="white", label="Risk Increasing (+SHAP)")
    ax.barh(y_pos[2], 0.308, left=base + 0.311, height=0.55, color="#DC2626", edgecolor="white")
    ax.barh(y_pos[3], 0.089, left=base + 0.311 + 0.308, height=0.55, color="#DC2626", edgecolor="white")
    # Mitigating driver
    ax.barh(y_pos[4], 0.076, left=final, height=0.55, color="#10B981", edgecolor="white", label="Risk Mitigating (-SHAP)")
    # Final alert
    ax.barh(y_pos[5], final, height=0.55, color="#991B1B", edgecolor="white", label="Alert Threshold Exceeded (99.2%)")

    ax.axvline(0.50, color="#F59E0B", linestyle="--", lw=1.2, label="Anomaly Decision Threshold (0.50)")
    ax.axvline(0.85, color="#DC2626", linestyle=":", lw=1.2, label="Critical Alarm Threshold (0.85)")

    ax.set_yticks(y_pos)
    ax.set_yticklabels(steps, fontsize=8.5)
    ax.invert_yaxis()
    ax.set_xlabel("Attributed Probability Contribution to DoS Classification")
    ax.set_xlim(0, 1.1)
    ax.set_title("Operator SHAP Attribution Waterfall: Diagnostic Reasoning for Live DoS Intrusion Alert", pad=12)

    # Annotate deltas
    ax.text(base/2, y_pos[0], "0.360", va="center", ha="center", color="white", weight="bold", fontsize=7.5)
    ax.text(base + 0.311/2, y_pos[1], "+0.311", va="center", ha="center", color="white", weight="bold", fontsize=7.5)
    ax.text(base + 0.311 + 0.308/2, y_pos[2], "+0.308", va="center", ha="center", color="white", weight="bold", fontsize=7.5)
    ax.text(base + 0.311 + 0.308 + 0.089/2, y_pos[3], "+0.089", va="center", ha="center", color="white", weight="bold", fontsize=7.5)
    ax.text(final + 0.076/2, y_pos[4], "-0.076", va="center", ha="center", color="white", weight="bold", fontsize=7.5)
    ax.text(final - 0.08, y_pos[5], "0.992 (DoS Alert)", va="center", ha="center", color="white", weight="bold", fontsize=8)

    ax.legend(loc="lower right", fontsize=8)

    plt.tight_layout()
    for ext in ["png", "pdf"]:
        fig.savefig(f"figures/fig_shap_waterfall_defense.{ext}", dpi=300)
        fig.savefig(f"research/figures/fig_shap_waterfall_defense.{ext}", dpi=300)
    plt.close()
    print("[+] Generated: figures/fig_shap_waterfall_defense.png/.pdf")


if __name__ == "__main__":
    print("=" * 60)
    print("Generating Academic-Grade SHAP & Benchmark Figures...")
    print("=" * 60)
    plot_feature_selection_comparison()
    plot_shap_heatmap()
    plot_hardware_latency_breakdown()
    plot_shap_waterfall_defense()
    print("\nAll figures exported successfully to figures/ and research/figures/.")
