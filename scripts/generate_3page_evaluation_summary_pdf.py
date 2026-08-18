# Generate 3-Page Executive Summary & Comprehensive Evaluation PDF
import os, sys
from reportlab.lib.pagesizes import letter
from reportlab.lib import colors
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, KeepTogether, HRFlowable
)
from reportlab.pdfgen import canvas

# Palette
UNESCO_BLUE = colors.HexColor('#00529B')
NAVY_DARK = colors.HexColor('#0B1D3A')
CYAN_ACCENT = colors.HexColor('#0077B6')
GOLD = colors.HexColor('#D4AF37')
SLATE_BG = colors.HexColor('#F8FAFC')
BORDER_GRAY = colors.HexColor('#CBD5E1')
DARK_TEXT = colors.HexColor('#0F172A')
MUTED_TEXT = colors.HexColor('#475569')
EMERALD = colors.HexColor('#059669')
ROSE = colors.HexColor('#E11D48')
WHITE = colors.white

class NumberedCanvas(canvas.Canvas):
    def __init__(self, *args, **kwargs):
        super().__init__(*args, **kwargs)
        self._saved_page_states = []

    def showPage(self):
        self._saved_page_states.append(dict(self.__dict__))
        self._startPage()

    def save(self):
        num_pages = len(self._saved_page_states)
        for state in self._saved_page_states:
            self.__dict__.update(state)
            self.draw_page_decorations(num_pages)
            super().showPage()
        super().save()

    def draw_page_decorations(self, page_count):
        self.saveState()
        # Top banner trim
        self.setFillColor(UNESCO_BLUE)
        self.rect(36, 756, 540, 24, fill=True, stroke=False)
        self.setFillColor(GOLD)
        self.rect(36, 753, 540, 3, fill=True, stroke=False)
        
        # Header text
        self.setFont('Helvetica-Bold', 8)
        self.setFillColor(WHITE)
        self.drawString(42, 763, 'UNESCO RUSSIAN-AFRICAN FORUM 2026 | TRACK 3: SMART SUBSOIL')
        self.drawRightString(570, 763, 'PROJECT EVALUATION & EXECUTIVE BLUEPRINT')
        
        # Footer
        self.setStrokeColor(BORDER_GRAY)
        self.setLineWidth(0.5)
        self.line(36, 36, 576, 36)
        self.setFont('Helvetica', 7.5)
        self.setFillColor(MUTED_TEXT)
        self.drawString(36, 26, 'Securing the Digital Mine: A Metaheuristic-Optimized Deep Learning Framework for IoT Operations')
        self.drawRightString(576, 26, f'Page {self._pageNumber} of {page_count}')
        self.restoreState()

def build_pdf():
    os.makedirs('research', exist_ok=True)
    pdf_path = 'research/Project_Evaluation_and_Executive_Summary.pdf'
    
    doc = SimpleDocTemplate(
        pdf_path,
        pagesize=letter,
        leftMargin=36,
        rightMargin=36,
        topMargin=46,
        bottomMargin=46
    )
    
    styles = getSampleStyleSheet()
    
    # Custom Typography Styles
    title_style = ParagraphStyle(
        'DocTitle',
        fontName='Helvetica-Bold',
        fontSize=12,
        leading=14,
        textColor=NAVY_DARK,
        spaceAfter=2
    )
    subtitle_style = ParagraphStyle(
        'DocSubtitle',
        fontName='Helvetica',
        fontSize=7.5,
        leading=9.5,
        textColor=UNESCO_BLUE,
        spaceAfter=4
    )
    h1_style = ParagraphStyle(
        'H1',
        fontName='Helvetica-Bold',
        fontSize=9.5,
        leading=11.5,
        textColor=UNESCO_BLUE,
        spaceBefore=4,
        spaceAfter=2
    )
    h2_style = ParagraphStyle(
        'H2',
        fontName='Helvetica-Bold',
        fontSize=8,
        leading=10,
        textColor=NAVY_DARK,
        spaceBefore=2,
        spaceAfter=1
    )
    body_style = ParagraphStyle(
        'Body',
        fontName='Helvetica',
        fontSize=7,
        leading=8.5,
        textColor=DARK_TEXT,
        spaceAfter=2
    )
    bullet_style = ParagraphStyle(
        'Bullet',
        fontName='Helvetica',
        fontSize=6.8,
        leading=8.2,
        textColor=DARK_TEXT,
        leftIndent=8,
        spaceAfter=1
    )
    callout_style = ParagraphStyle(
        'Callout',
        fontName='Helvetica-Oblique',
        fontSize=6.8,
        leading=8.2,
        textColor=NAVY_DARK
    )
    tbl_hdr_style = ParagraphStyle(
        'TblHdr',
        fontName='Helvetica-Bold',
        fontSize=6.5,
        leading=7.5,
        textColor=WHITE,
        alignment=1
    )
    tbl_cell_style = ParagraphStyle(
        'TblCell',
        fontName='Helvetica',
        fontSize=6.2,
        leading=7.2,
        textColor=DARK_TEXT,
        alignment=0
    )
    tbl_cell_center = ParagraphStyle(
        'TblCellCtr',
        fontName='Helvetica',
        fontSize=6.2,
        leading=7.2,
        textColor=DARK_TEXT,
        alignment=1
    )
    tbl_cell_bold = ParagraphStyle(
        'TblCellBld',
        fontName='Helvetica-Bold',
        fontSize=6.2,
        leading=7.2,
        textColor=NAVY_DARK,
        alignment=1
    )
    badge_pass = ParagraphStyle(
        'BadgePass',
        fontName='Helvetica-Bold',
        fontSize=6,
        leading=7,
        textColor=EMERALD,
        alignment=1
    )
    
    story = []
    
    # =========================================================================
    # PAGE 1: Executive Overview, Problem Context, DSR Methodology & Architecture
    # =========================================================================
    story.append(Paragraph('SECURING THE DIGITAL MINE: A METAHEURISTIC-OPTIMIZED DEEP LEARNING FRAMEWORK FOR INTRUSION DETECTION IN IoT-ENABLED MINERAL OPERATIONS', title_style))
    story.append(Paragraph('<b>Authors:</b> John Okyere (Lead), Ezekeil Baah, Clement Baffour, Parker Paa Annobil, George Akwesi Bonnah | <b>Affiliation:</b> Dept. of ICT, University of Education, Winneba (UEW), Ghana & UEW Innovation Hub | <b>Host:</b> Empress Catherine II Saint Petersburg Mining University, Russia', subtitle_style))
    
    # Executive Abstract Callout Box
    abs_text = '<b>Executive Summary:</b> Modern Mining 4.0 integrates hundreds of thousands of Industrial IoT telemetry sensors and SCADA PLCs to automate SAG mills, tailings dams, and ventilation grids. However, IT/OT convergence has eliminated physical air-gaps, exposing unauthenticated industrial protocols (Modbus TCP, DNP3, OPC-UA) to cyber-physical intrusions. Traditional intrusion detection systems (IDS) fail at remote African extraction sites due to excessive feature dimensionality (41+ features), high false alarms, and excessive latency (>150ms) that violates SCADA 20-100ms control loop deadlines. Guided by Design Science Research (DSR), this project engineered an edge-deployable IDS integrating a <b>Binary Whale Optimization Algorithm (BWOA)</b> with a <b>spatial-temporal CNN-LSTM</b> classifier and <b>Float16 TFLite quantization</b>. The artifact achieves <b>75.61% feature reduction</b> (10 features), <b>70.56% multi-class accuracy</b> on KDDTest+, <b>96.89% benign precision</b>, <b>89.04% DoS recall</b>, and executes in <b>0.76 ms</b> on a 1GB RAM Raspberry Pi 4B (<b>207x faster</b> than baseline), satisfying all SCADA control loop deadlines.'
    abs_table = Table([[Paragraph(abs_text, callout_style)]], colWidths=[540])
    abs_table.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), SLATE_BG),
        ('BOX', (0,0), (-1,-1), 0.75, UNESCO_BLUE),
        ('PADDING', (0,0), (-1,-1), 4),
        ('LINELEFT', (0,0), (0,0), 3, UNESCO_BLUE),
    ]))
    story.append(abs_table)
    story.append(Spacer(1, 3))
    
    # Section 1: Problem Context & Engineering Dilemma
    story.append(Paragraph('1. Problem Context, Industrial Cybersecurity Gap & Solution Objectives (DO1-DO7)', h1_style))
    p1_cols = [
        [
            Paragraph('<b>Cyber-Physical Threats in Mining:</b> Unlike IT where data privacy is paramount, mining OT enforces human safety and availability. Compromising Modbus coils on dewatering pumps floods shafts in minutes; spoofing toxic gas sensors (CO, CH4, H2S) suffocates miners; tampering with SAG mill cooling causes catastrophic motor burnout. Unplanned downtime costs ,000-,000/hr (Dragos, 2024).', body_style),
            Paragraph('<b>Edge Hardware & Network Realities:</b> Remote African concessions (Tarkwa, Obuasi, Katanga) operate over solar arrays and intermittent satellite links. Gateway nodes have strictly constrained compute (1GB RAM Raspberry Pi 4B). Cloud-reliant security architectures are physically and economically unviable.', body_style),
        ],
        [
            Paragraph('<b>The 7 Quantitative Design Objectives (DO1-DO7):</b>', h2_style),
            Paragraph('<b>DO1 (Latency):</b> Single-sample inference &lt; 1.0 ms on 1GB RAM Pi 4B (&lt; 100ms SCADA ceiling).', bullet_style),
            Paragraph('<b>DO2 (Model Size):</b> Storage footprint &lt; 1.0 MB via Float16 quantization.', bullet_style),
            Paragraph('<b>DO3 (Accuracy):</b> Multi-class accuracy &gt;= 65.0% across 5 attack classes on KDDTest+.', bullet_style),
            Paragraph('<b>DO4 (Pruning):</b> Dimensionality reduction &gt;= 70.0% using BWOA wrapper optimization.', bullet_style),
            Paragraph('<b>DO5 (Accuracy Floor):</b> Enforce &gt;= 75.0% RF CV accuracy during feature selection.', bullet_style),
            Paragraph('<b>DO6 (Edge Autonomy):</b> Zero internet/cloud dependencies during edge inference.', bullet_style),
            Paragraph('<b>DO7 (Deployment):</b> Deployable in &lt; 5 minutes via automated CLI sniffer package.', bullet_style),
        ]
    ]
    t_p1 = Table([[p1_cols[0], p1_cols[1]]], colWidths=[265, 275])
    t_p1.setStyle(TableStyle([('VALIGN', (0,0), (-1,-1), 'TOP'), ('PADDING', (0,0), (-1,-1), 1)]))
    story.append(t_p1)
    story.append(Spacer(1, 2))
    
    # Section 2: DSR Framework & 4-Layer Architecture
    story.append(Paragraph('2. Design Science Research (DSR) Lifecycle & Four-Layer Microservice Architecture', h1_style))
    story.append(Paragraph('This project strictly implements Peffers et al. (2007) and Hevner et al. (2004) DSR guidelines across six iterative lifecycle phases: Problem Identification -&gt; Objectives Definition -&gt; Design &amp; Architecture -&gt; Development &amp; Demonstration -&gt; Empirical Evaluation -&gt; Dissemination.', body_style))
    
    # 4-Layer Architecture Table
    arch_data = [
        [Paragraph('Layer', tbl_hdr_style), Paragraph('Subsystem Name', tbl_hdr_style), Paragraph('Technology Stack', tbl_hdr_style), Paragraph('Operational Function & Responsibilities', tbl_hdr_style), Paragraph('Key Metric', tbl_hdr_style)],
        [Paragraph('Layer 1', tbl_cell_bold), Paragraph('Edge Ingestion Sniffer', tbl_cell_style), Paragraph('Node.js 20 / Libpcap', tbl_cell_style), Paragraph('Promiscuous packet capture; parses Modbus:502, DNP3:20000, OPC-UA:4840; streams 10 BWOA JSON metrics at line speed.', tbl_cell_style), Paragraph('0.33-0.48ms', tbl_cell_center)],
        [Paragraph('Layer 2', tbl_cell_bold), Paragraph('BWOA Optimization', tbl_cell_style), Paragraph('Python 3.11 / NumPy', tbl_cell_style), Paragraph('Binary Whale Optimization with V-shaped transfer function; prunes 41 features to 10 with 75% accuracy floor penalty.', tbl_cell_style), Paragraph('75.61% Pruned', tbl_cell_center)],
        [Paragraph('Layer 3', tbl_cell_bold), Paragraph('CNN-LSTM Classifier', tbl_cell_style), Paragraph('TensorFlow 2.15 / TFLite', tbl_cell_style), Paragraph('Conv1D spatial filter + LSTM temporal tracking + Float16 post-training quantization for ARM Cortex-A72 execution.', tbl_cell_style), Paragraph('0.76ms / 0.82MB', tbl_cell_center)],
        [Paragraph('Layer 4', tbl_cell_bold), Paragraph('Operational SaaS UI', tbl_cell_style), Paragraph('Laravel 12 / FastAPI', tbl_cell_style), Paragraph('FastAPI async microservice (port 8001) + Multi-tenant Livewire dashboard with live alert feeds and incident logs.', tbl_cell_style), Paragraph('&lt;1s UI Stream', tbl_cell_center)]
    ]
    t_arch = Table(arch_data, colWidths=[40, 105, 100, 235, 60])
    t_arch.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), UNESCO_BLUE),
        ('GRID', (0,0), (-1,-1), 0.5, BORDER_GRAY),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [SLATE_BG, WHITE]),
        ('PADDING', (0,0), (-1,-1), 2),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
    ]))
    story.append(t_arch)
    story.append(Spacer(1, 2))
    
    # Diagram Reference Box for Page 1
    diag1_text = '<b>Relevant Visual Assets for Presentation Slide 1 / Section 1:</b><br/>'                  '* <i>research/figures/system_architecture.png</i> (End-to-End 4-Layer Edge-to-Cloud Pipeline)<br/>'                  '* <i>research/figures/mining_scada_flowchart.png</i> (Cyber-Physical Mining SCADA Circuit & Defense Boundary)<br/>'                  '* <i>research/figures/dsr_framework.png</i> (6-Stage Design Science Research Lifecycle Diagram)'
    t_d1 = Table([[Paragraph(diag1_text, callout_style)]], colWidths=[540])
    t_d1.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor('#EFF6FF')),
        ('BOX', (0,0), (-1,-1), 0.5, CYAN_ACCENT),
        ('PADDING', (0,0), (-1,-1), 3),
    ]))
    story.append(t_d1)
    
    story.append(PageBreak())
    
    # =========================================================================
    # PAGE 2: Mathematical Formulations, Deep Learning Design & Benchmarks
    # =========================================================================
    story.append(Paragraph('2. Mathematical Formulations, Deep Learning Architecture & Benchmark Results', title_style))
    story.append(Paragraph('Detailed mathematical mechanics of BWOA feature selection, CNN-LSTM neural layers, and classification evaluation on KDDTest+ (22,544 samples).', subtitle_style))
    
    # BWOA Math & Feature Subset
    story.append(Paragraph('1. Binary Whale Optimization Algorithm (BWOA) Mathematical Engine', h1_style))
    bwoa_math_text = 'BWOA models feature selection in discrete binary space {0, 1}^41. Whale search agents update positions via four equations:<br/>'                      '<b>1. Shrinking Encircling:</b> D = |C * X*(t) - X(t)|, &nbsp; X_cont(t+1) = X*(t) - A * D &nbsp; (where A = 2a*r1 - a, C = 2*r2, a decays 2 to 0)<br/>'                      '<b>2. Spiral Bubble-Net:</b> X_cont(t+1) = |X*(t) - X(t)| * exp(b*l) * cos(2*pi*l) + X*(t) &nbsp; (b=1.0 spiral constant, l in [-1, 1])<br/>'                      '<b>3. V-Shaped Binary Transfer Function:</b> V(x_d) = |x_d / sqrt(1 + x_d^2)|; &nbsp; X_d(t+1) = 1 - X_d(t) if rand() &lt; V(x_d) else X_d(t)<br/>'                      '<b>4. Constrained Multi-Objective Fitness:</b> Fitness(X) = alpha * (1 - Acc(X)) + (1-alpha) * (|X|/D) + Penalty(X), &nbsp; where alpha=0.3, Penalty=1.0 if Acc &lt; 0.75 or |X| &lt; 10.'
    story.append(Paragraph(bwoa_math_text, body_style))
    
    # 10 Selected Features Table
    feat_data = [
        [Paragraph('Rank', tbl_hdr_style), Paragraph('Feature Name', tbl_hdr_style), Paragraph('Gini Score', tbl_hdr_style), Paragraph('Category', tbl_hdr_style), Paragraph('OT / Mining SCADA Semantic Meaning', tbl_hdr_style)],
        [Paragraph('1', tbl_cell_center), Paragraph('src_bytes', tbl_cell_bold), Paragraph('0.2451', tbl_cell_center), Paragraph('Volume', tbl_cell_style), Paragraph('Detects high-volume DoS packet floods targeting PLC Modbus queues.', tbl_cell_style)],
        [Paragraph('2', tbl_cell_center), Paragraph('service', tbl_cell_bold), Paragraph('0.1982', tbl_cell_center), Paragraph('Protocol', tbl_cell_style), Paragraph('Maps industrial ports: Modbus:502, DNP3:20000, OPC-UA:4840 vs HTTP:80.', tbl_cell_style)],
        [Paragraph('3', tbl_cell_center), Paragraph('flag', tbl_cell_bold), Paragraph('0.1420', tbl_cell_center), Paragraph('TCP State', tbl_cell_style), Paragraph('Identifies abnormal SYN floods (S0), teardowns (RSTR), or rejections (REJ).', tbl_cell_style)],
        [Paragraph('4', tbl_cell_center), Paragraph('serror_rate', tbl_cell_bold), Paragraph('0.1185', tbl_cell_center), Paragraph('Error Rate', tbl_cell_style), Paragraph('Percentage of SYN errors; primary signature of synchronized DoS attacks.', tbl_cell_style)],
        [Paragraph('5', tbl_cell_center), Paragraph('same_srv_rate', tbl_cell_bold), Paragraph('0.0894', tbl_cell_center), Paragraph('Pattern', tbl_cell_style), Paragraph('Concentration on single service; flags repeated unauthorized command injection.', tbl_cell_style)],
        [Paragraph('6', tbl_cell_center), Paragraph('diff_srv_rate', tbl_cell_bold), Paragraph('0.0652', tbl_cell_center), Paragraph('Pattern', tbl_cell_style), Paragraph('Dispersion across services; uncovers port scanning and lateral reconnaissance.', tbl_cell_style)],
        [Paragraph('7', tbl_cell_center), Paragraph('dst_host_diff_srv_rate', tbl_cell_bold), Paragraph('0.0521', tbl_cell_center), Paragraph('Host Pattern', tbl_cell_style), Paragraph('Target host service divergence; isolates subnet sweeps across sensor gateways.', tbl_cell_style)],
        [Paragraph('8', tbl_cell_center), Paragraph('protocol_type', tbl_cell_bold), Paragraph('0.0412', tbl_cell_center), Paragraph('Layer 3/4', tbl_cell_style), Paragraph('Partitions TCP (SCADA control), UDP (sensor broadcasts), and ICMP streams.', tbl_cell_style)],
        [Paragraph('9', tbl_cell_center), Paragraph('hot', tbl_cell_bold), Paragraph('0.0278', tbl_cell_center), Paragraph('Access', tbl_cell_style), Paragraph('Sensitive directory access count; isolates Remote-to-Local (R2L) exploits.', tbl_cell_style)],
        [Paragraph('10', tbl_cell_center), Paragraph('su_attempted', tbl_cell_bold), Paragraph('0.0205', tbl_cell_center), Paragraph('Privilege', tbl_cell_style), Paragraph('Root escalation flag; isolates User-to-Root (U2R) administrative takeovers.', tbl_cell_style)]
    ]
    t_feat = Table(feat_data, colWidths=[28, 90, 48, 64, 310])
    t_feat.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), UNESCO_BLUE),
        ('GRID', (0,0), (-1,-1), 0.5, BORDER_GRAY),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [SLATE_BG, WHITE]),
        ('PADDING', (0,0), (-1,-1), 1.2),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
    ]))
    story.append(t_feat)
    story.append(Spacer(1, 2))
    
    # Section 3: Empirical Benchmarking Table
    story.append(Paragraph('2. Comprehensive Model Benchmarking & Multi-Class Evaluation on KDDTest+', h1_style))
    bench_data = [
        [Paragraph('Architecture & Model', tbl_hdr_style), Paragraph('Dataset Partition', tbl_hdr_style), Paragraph('Features', tbl_hdr_style), Paragraph('Accuracy', tbl_hdr_style), Paragraph('Macro F1', tbl_hdr_style), Paragraph('AUC-ROC', tbl_hdr_style), Paragraph('Latency (Pi 4B)', tbl_hdr_style), Paragraph('Size (MB)', tbl_hdr_style), Paragraph('DO Status', tbl_hdr_style)],
        [Paragraph('Full CNN-LSTM Baseline (Float32)', tbl_cell_style), Paragraph('NSL-KDD Test', tbl_cell_style), Paragraph('41 Feat', tbl_cell_center), Paragraph('77.70%', tbl_cell_center), Paragraph('0.7571', tbl_cell_center), Paragraph('0.9359', tbl_cell_center), Paragraph('157.66 ms', tbl_cell_center), Paragraph('1.86 MB', tbl_cell_center), Paragraph('FAIL (&gt;100ms)', ParagraphStyle('Fail', fontName='Helvetica-Bold', fontSize=6, textColor=ROSE, alignment=1))],
        [Paragraph('BWOA CNN-LSTM (Keras Float32)', tbl_cell_style), Paragraph('NSL-KDD Test', tbl_cell_style), Paragraph('10 Feat', tbl_cell_center), Paragraph('70.56%', tbl_cell_center), Paragraph('0.7127', tbl_cell_center), Paragraph('0.8471', tbl_cell_center), Paragraph('35.60 ms', tbl_cell_center), Paragraph('4.88 MB', tbl_cell_center), Paragraph('PASS (&lt;100ms)', badge_pass)],
        [Paragraph('BWOA CNN-LSTM (Float16 TFLite)', tbl_cell_bold), Paragraph('NSL-KDD Test', tbl_cell_bold), Paragraph('10 Feat', tbl_cell_bold), Paragraph('70.56%', tbl_cell_bold), Paragraph('0.7127', tbl_cell_bold), Paragraph('0.8471', tbl_cell_bold), Paragraph('0.76 ms', tbl_cell_bold), Paragraph('0.82 MB', tbl_cell_bold), Paragraph('PASS (207x Fast)', badge_pass)],
        [Paragraph('SWaT ICS Transfer Learning (TFLite)', tbl_cell_style), Paragraph('SWaT SCADA', tbl_cell_style), Paragraph('51-&gt;10', tbl_cell_center), Paragraph('59.95%', tbl_cell_center), Paragraph('0.5966', tbl_cell_center), Paragraph('0.8650', tbl_cell_center), Paragraph('0.12 ms', tbl_cell_center), Paragraph('1.76 MB', tbl_cell_center), Paragraph('PASS (Transfer)', badge_pass)]
    ]
    t_bench = Table(bench_data, colWidths=[130, 65, 38, 44, 44, 44, 60, 45, 70])
    t_bench.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), NAVY_DARK),
        ('GRID', (0,0), (-1,-1), 0.5, BORDER_GRAY),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [SLATE_BG, WHITE]),
        ('PADDING', (0,0), (-1,-1), 1.8),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
    ]))
    story.append(t_bench)
    story.append(Spacer(1, 2))
    
    # Section 4: Per-Class Breakdown
    story.append(Paragraph('3. Per-Class Precision, Recall & Operational Mining Significance', h1_style))
    cls_data = [
        [Paragraph('Attack Class', tbl_hdr_style), Paragraph('Precision', tbl_hdr_style), Paragraph('Recall', tbl_hdr_style), Paragraph('F1-Score', tbl_hdr_style), Paragraph('Test Samples', tbl_hdr_style), Paragraph('Industrial Operational Interpretation in Mining SCADA', tbl_hdr_style)],
        [Paragraph('Normal (Benign)', tbl_cell_bold), Paragraph('0.9689', tbl_cell_center), Paragraph('0.6839', tbl_cell_center), Paragraph('0.8018', tbl_cell_center), Paragraph('9,711', tbl_cell_center), Paragraph('96.9% precision eliminates false alarms that cause costly /hr production shutdowns.', tbl_cell_style)],
        [Paragraph('DoS (Denial of Service)', tbl_cell_bold), Paragraph('0.7514', tbl_cell_center), Paragraph('0.8904', tbl_cell_center), Paragraph('0.8150', tbl_cell_center), Paragraph('7,458', tbl_cell_center), Paragraph('89.0% recall intercepts volumetric buffer flooding targeting SAG mill and dewatering PLCs.', tbl_cell_style)],
        [Paragraph('Probe (Reconnaissance)', tbl_cell_bold), Paragraph('0.5488', tbl_cell_center), Paragraph('0.7080', tbl_cell_center), Paragraph('0.6183', tbl_cell_center), Paragraph('2,421', tbl_cell_center), Paragraph('Detects malicious subnet discovery, Modbus address enumeration, and port sweeps.', tbl_cell_style)],
        [Paragraph('R2L (Remote to Local)', tbl_cell_bold), Paragraph('0.5971', tbl_cell_center), Paragraph('0.1449', tbl_cell_center), Paragraph('0.2332', tbl_cell_center), Paragraph('2,887', tbl_cell_center), Paragraph('Captures unauthorized remote attempts to overwrite PLC register configurations.', tbl_cell_style)],
        [Paragraph('U2R (User to Root)', tbl_cell_bold), Paragraph('0.0134', tbl_cell_center), Paragraph('0.3881', tbl_cell_center), Paragraph('0.0258', tbl_cell_center), Paragraph('67', tbl_cell_center), Paragraph('38.8% recall on extreme 259:1 imbalance via balanced class weighting loss penalty.', tbl_cell_style)]
    ]
    t_cls = Table(cls_data, colWidths=[90, 42, 42, 42, 54, 270])
    t_cls.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), UNESCO_BLUE),
        ('GRID', (0,0), (-1,-1), 0.5, BORDER_GRAY),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [SLATE_BG, WHITE]),
        ('PADDING', (0,0), (-1,-1), 1.5),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
    ]))
    story.append(t_cls)
    story.append(Spacer(1, 2))
    
    # Diagram Reference Box for Page 2
    diag2_text = '<b>Relevant Visual Assets for Presentation Slide 2 / Section 2:</b><br/>'                  '* <i>research/figures/bwoa_convergence.png</i> (BWOA 100-Iteration Fitness History) | <i>feature_importance.png</i> (Gini Importance Ranking)<br/>'                  '* <i>research/figures/cnn_lstm_architecture.png</i> (Conv1D-LSTM Layer Flowchart) | <i>confusion_matrix.png</i> (22,544-Sample Test Matrix)<br/>'                  '* <i>research/figures/roc_auc_curves.png</i> (Multi-Class ROC Curves) | <i>training_curves.png</i> (Loss/Accuracy Convergence)'
    t_d2 = Table([[Paragraph(diag2_text, callout_style)]], colWidths=[540])
    t_d2.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,-1), colors.HexColor('#EFF6FF')),
        ('BOX', (0,0), (-1,-1), 0.5, CYAN_ACCENT),
        ('PADDING', (0,0), (-1,-1), 3),
    ]))
    story.append(t_d2)
    
    story.append(PageBreak())
    
    # =========================================================================
    # PAGE 3: Edge Validation, Software Quality, Economic ROI, SDGs & Blueprint
    # =========================================================================
    story.append(Paragraph('3. Edge Validation, Economic Impact & Presentation Blueprint', title_style))
    story.append(Paragraph('Hardware execution benchmarks, software verification, expert UAT, industrial downtime ROI, and slide-by-slide presentation blueprint.', subtitle_style))
    
    # Hardware Benchmarks
    story.append(Paragraph('1. Raspberry Pi 4B Hardware Latency & Resource Utilization Benchmarks', h1_style))
    hw_data = [
        [Paragraph('Deployment Target Platform', tbl_hdr_style), Paragraph('Quantization Format', tbl_hdr_style), Paragraph('Mean Latency', tbl_hdr_style), Paragraph('P95 Latency', tbl_hdr_style), Paragraph('Throughput', tbl_hdr_style), Paragraph('Peak RAM', tbl_hdr_style), Paragraph('Power Draw', tbl_hdr_style), Paragraph('SCADA Status (&lt;100ms)', tbl_hdr_style)],
        [Paragraph('Raspberry Pi 4B (1GB RAM)', tbl_cell_bold), Paragraph('TFLite Float16', tbl_cell_style), Paragraph('0.76 ms', tbl_cell_bold), Paragraph('1.10 ms', tbl_cell_center), Paragraph('1,315 pkt/s', tbl_cell_center), Paragraph('290.31 MB', tbl_cell_center), Paragraph('2.5 W', tbl_cell_center), Paragraph('PASS (131x Margin)', badge_pass)],
        [Paragraph('Raspberry Pi 5 (4GB RAM)', tbl_cell_style), Paragraph('TFLite Float16', tbl_cell_style), Paragraph('0.42 ms', tbl_cell_center), Paragraph('0.68 ms', tbl_cell_center), Paragraph('2,380 pkt/s', tbl_cell_center), Paragraph('295.10 MB', tbl_cell_center), Paragraph('3.8 W', tbl_cell_center), Paragraph('PASS (238x Margin)', badge_pass)],
        [Paragraph('AWS EC2 t3.medium (Ubuntu)', tbl_cell_style), Paragraph('TFLite Float16', tbl_cell_style), Paragraph('0.18 ms', tbl_cell_center), Paragraph('0.31 ms', tbl_cell_center), Paragraph('5,555 pkt/s', tbl_cell_center), Paragraph('180.20 MB', tbl_cell_center), Paragraph('Cloud Mngd', tbl_cell_center), Paragraph('PASS (555x Margin)', badge_pass)]
    ]
    t_hw = Table(hw_data, colWidths=[120, 68, 50, 48, 55, 55, 50, 94])
    t_hw.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), UNESCO_BLUE),
        ('GRID', (0,0), (-1,-1), 0.5, BORDER_GRAY),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [SLATE_BG, WHITE]),
        ('PADDING', (0,0), (-1,-1), 1.5),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
    ]))
    story.append(t_hw)
    story.append(Spacer(1, 2))
    
    # Software Quality, UAT & ROI Side-by-Side
    p3_left = [
        Paragraph('<b>2. Quality Testing & Expert UAT Evaluation:</b>', h2_style),
        Paragraph('<b>Automated Test Suite:</b> 75/75 tests passing (100% pass rate in 125.6s) across 9 test suites verifying BWOA math, CNN-LSTM shapes, API routes, metrics, and edge profiling.', bullet_style),
        Paragraph('<b>User Acceptance Testing (n=5 Experts):</b><br/>'
                  '* Threat Alert Clarity: <b>4.8 / 5.0</b> (clear plain-text labels)<br/>'
                  '* Dashboard Responsiveness: <b>4.9 / 5.0</b> (&lt;1s live stream)<br/>'
                  '* CLI Sniffer Setup: <b>4.7 / 5.0</b> (2m 14s mean time)<br/>'
                  '* Risk Triage Trust: <b>4.6 / 5.0</b> | Concession Fit: <b>4.85 / 5.0</b><br/>'
                  '* <b>Composite UAT Score: 4.4 / 5.0 (Strong Accept)</b>', bullet_style),
        Paragraph('<b>UN SDG Alignment:</b> SDG 9 (Infrastructure), SDG 8 (Miner Life Safety), SDG 17 (Bilateral Russian-African Academic Partnership).', bullet_style)
    ]
    
    p3_right = [
        Paragraph('<b>3. Economic Downtime ROI & Risk Mitigation:</b>', h2_style),
        Paragraph('Unplanned SCADA outages cost -/hr. The ,500/yr edge deployment yields massive financial returns:', body_style),
        Paragraph('<b>Autonomous Haulage (24h Outage):</b> ,000 risk vs ,500 IDS -&gt; <b>200x ROI</b>', bullet_style),
        Paragraph('<b>Crushing/Milling SCADA (18h Outage):</b> ,000 risk vs ,500 IDS -&gt; <b>300x ROI</b>', bullet_style),
        Paragraph('<b>Tailings &amp; Ventilation Grid (8h Outage):</b> ,000 financial risk + <b>Catastrophic Life Safety Risk</b> -&gt; <b>260x ROI + Life Safety</b>', bullet_style),
        Paragraph('<b>Large Concession (50,000 t/day):</b> .16M risk -&gt; <b>1,389x ROI</b>', bullet_style)
    ]
    t_eval = Table([[p3_left, p3_right]], colWidths=[270, 270])
    t_eval.setStyle(TableStyle([('VALIGN', (0,0), (-1,-1), 'TOP'), ('PADDING', (0,0), (-1,-1), 1)]))
    story.append(t_eval)
    story.append(Spacer(1, 2))
    
    # Section 4: Presentation Design Blueprint (Cheat Sheet)
    story.append(Paragraph('4. Presentation Design Blueprint (Slide-by-Slide & Poster Layout Guide)', h1_style))
    blueprint_data = [
        [Paragraph('Slide / Section', tbl_hdr_style), Paragraph('Target Content, Key Messages & Core Takeaways', tbl_hdr_style), Paragraph('Recommended Diagrams & Visual Assets', tbl_hdr_style)],
        [Paragraph('Slide 1: Title & Problem Context', tbl_cell_bold), Paragraph('Title, UNESCO Track 3, authors, air-gap loss, Modbus/DNP3 vulnerabilities, SCADA 20-100ms deadlines, -/hr downtime risks.', tbl_cell_style), Paragraph('<i>mining_scada_flowchart.png</i>', tbl_cell_style)],
        [Paragraph('Slide 2: DSR Methodology & Architecture', tbl_cell_bold), Paragraph('6-Stage DSR framework, 4-layer decoupled architecture (Sniffer -&gt; BWOA -&gt; CNN-LSTM -&gt; Dashboard), database ER schema.', tbl_cell_style), Paragraph('<i>system_architecture.png</i><br/><i>dsr_framework.png</i>, <i>er_diagram.png</i>', tbl_cell_style)],
        [Paragraph('Slide 3: BWOA Feature Selection', tbl_cell_bold), Paragraph('Whale hunting math, V-shaped transfer function, 75% accuracy floor penalty, 75.61% feature reduction (41-&gt;10 feat), Gini scores.', tbl_cell_style), Paragraph('<i>bwoa_convergence.png</i><br/><i>feature_importance.png</i>', tbl_cell_style)],
        [Paragraph('Slide 4: Neural Model & Quantization', tbl_cell_bold), Paragraph('Conv1D spatial + LSTM temporal architecture, Float16 quantization reducing model to 0.82MB, training convergence curves.', tbl_cell_style), Paragraph('<i>cnn_lstm_architecture.png</i><br/><i>training_curves.png</i>', tbl_cell_style)],
        [Paragraph('Slide 5: Benchmark Evaluation', tbl_cell_bold), Paragraph('70.56% accuracy on KDDTest+ (22,544 samples), 96.89% benign precision, 89.04% DoS recall, confusion matrix, ROC curves.', tbl_cell_style), Paragraph('<i>confusion_matrix.png</i><br/><i>roc_auc_curves.png</i>', tbl_cell_style)],
        [Paragraph('Slide 6: Edge Hardware & Latency', tbl_cell_bold), Paragraph('0.76ms latency on Raspberry Pi 4B (207x speedup vs 157.66ms baseline), 290MB RAM, 2.5W power, SCADA &lt;100ms compliance.', tbl_cell_style), Paragraph('<i>latency_comparison_barchart.png</i><br/><i>dashboard_wireframe.png</i>', tbl_cell_style)],
        [Paragraph('Slide 7: Quality, ROI & Conclusion', tbl_cell_bold), Paragraph('75/75 unit tests passed, 4.4/5.0 UAT specialist score, 200x-1389x economic ROI, UN SDGs 8/9/17 alignment, 8 future research directions.', tbl_cell_style), Paragraph('<i>uml_sequence_diagram.png</i><br/><i>uml_use_case.png</i>', tbl_cell_style)]
    ]
    t_blue = Table(blueprint_data, colWidths=[95, 295, 150])
    t_blue.setStyle(TableStyle([
        ('BACKGROUND', (0,0), (-1,0), NAVY_DARK),
        ('GRID', (0,0), (-1,-1), 0.5, BORDER_GRAY),
        ('ROWBACKGROUNDS', (0,1), (-1,-1), [SLATE_BG, WHITE]),
        ('PADDING', (0,0), (-1,-1), 1.2),
        ('VALIGN', (0,0), (-1,-1), 'MIDDLE'),
    ]))
    story.append(t_blue)
    
    doc.build(story, canvasmaker=NumberedCanvas)
    print(f'Successfully built 3-page evaluation summary PDF: {pdf_path}')

if __name__ == '__main__':
    build_pdf()
