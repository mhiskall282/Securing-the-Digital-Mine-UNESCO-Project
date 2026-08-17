"""Generate an exhaustive, highly detailed academic landscape scientific conference poster.
Dimensions: 48.0 x 32.0 inches (Landscape).
Complete summary of everything: Introduction, Mining SCADA diagram, BWOA Mathematical equations,
4-Layer Architecture diagram, BWOA Convergence curve, CNN-LSTM Flowchart, Latency Bar Chart,
Confusion Matrix, Summary Results Tables, Conclusions, UN SDG Alignment, and APA References.
"""
import os
import docx
from docx import Document
from docx.shared import Inches as DInches, Pt as DPt, RGBColor as DRGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT

from pptx import Presentation
from pptx.util import Inches, Pt
from pptx.dml.color import RGBColor
from pptx.enum.text import PP_ALIGN
from pptx.enum.shapes import MSO_SHAPE

from docx_styler import clean_text, set_table_borders, set_cell_background, set_cell_margins

def create_academic_landscape_poster_pptx():
    prs = Presentation()
    # 48.0 x 32.0 inches (Standard Academic Landscape Poster)
    prs.slide_width = Inches(48.0)
    prs.slide_height = Inches(32.0)

    blank_layout = prs.slide_layouts[6]
    slide = prs.slides.add_slide(blank_layout)

    # Color Palette
    BANNER_BLUE = RGBColor(0, 82, 155)       # #00529B (Vibrant UNESCO Blue)
    DARK_NAVY = RGBColor(11, 29, 58)         # #0B1D3A
    CYAN_ACCENT = RGBColor(0, 163, 224)      # #00A3E0
    GOLD_ACCENT = RGBColor(255, 215, 0)      # #FFD700 (Vibrant Gold)
    HEADING_COLOR = RGBColor(0, 82, 155)    # #00529B (Section Titles)
    TEXT_DARK = RGBColor(30, 41, 59)         # #1E293B (Body Text)
    TEXT_MUTED = RGBColor(100, 116, 139)     # #64748B
    WHITE = RGBColor(255, 255, 255)
    EMERALD = RGBColor(16, 185, 129)
    CARD_BG = RGBColor(248, 250, 252)        # #F8FAFC
    BORDER_LIGHT = RGBColor(203, 213, 225)   # #CBD5E1

    # 1. Pure White Slide Canvas
    canvas = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, Inches(48.0), Inches(32.0))
    canvas.fill.solid()
    canvas.fill.fore_color.rgb = WHITE
    canvas.line.fill.background()

    # 2. Top Vibrant Header Banner (Height: 5.4 inches)
    banner = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, Inches(48.0), Inches(5.4))
    banner.fill.solid()
    banner.fill.fore_color.rgb = BANNER_BLUE
    banner.line.fill.background()

    # Banner Bottom Gold Accent Strip
    strip = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, Inches(5.3), Inches(48.0), Inches(0.12))
    strip.fill.solid()
    strip.fill.fore_color.rgb = GOLD_ACCENT
    strip.line.fill.background()

    # Header Left Institution Text (UEW / Kayaba Labs)
    tb_inst_l = slide.shapes.add_textbox(Inches(1.0), Inches(0.5), Inches(6.5), Inches(4.2))
    tf_il = tb_inst_l.text_frame
    p_il1 = tf_il.paragraphs[0]
    r_il1 = p_il1.add_run()
    r_il1.text = "UEW"
    r_il1.font.name = "Arial"
    r_il1.font.size = Pt(36)
    r_il1.font.bold = True
    r_il1.font.color.rgb = WHITE

    p_il2 = tf_il.add_paragraph()
    r_il2 = p_il2.add_run()
    r_il2.text = "UNIVERSITY OF EDUCATION,\nWINNEBA, GHANA\n& KAYABA LABS"
    r_il2.font.name = "Arial"
    r_il2.font.size = Pt(13)
    r_il2.font.bold = True
    r_il2.font.color.rgb = CYAN_ACCENT

    # Header Right Forum Details (UNESCO / SPMU)
    tb_inst_r = slide.shapes.add_textbox(Inches(40.5), Inches(0.5), Inches(6.5), Inches(4.2))
    tf_ir = tb_inst_r.text_frame
    tf_ir.word_wrap = True
    p_ir1 = tf_ir.paragraphs[0]
    p_ir1.alignment = PP_ALIGN.RIGHT
    r_ir1 = p_ir1.add_run()
    r_ir1.text = "UNESCO"
    r_ir1.font.name = "Arial"
    r_ir1.font.size = Pt(34)
    r_ir1.font.bold = True
    r_ir1.font.color.rgb = GOLD_ACCENT

    p_ir2 = tf_ir.add_paragraph()
    p_ir2.alignment = PP_ALIGN.RIGHT
    r_ir2 = p_ir2.add_run()
    r_ir2.text = "Russian-African Forum\nTrack 3: Smart Subsoil\nSaint Petersburg Mining Univ."
    r_ir2.font.name = "Arial"
    r_ir2.font.size = Pt(13)
    r_ir2.font.bold = True
    r_ir2.font.color.rgb = WHITE

    # Header Center: Title, Authors, Affiliations
    tb_center = slide.shapes.add_textbox(Inches(7.6), Inches(0.35), Inches(32.8), Inches(4.8))
    tf_c = tb_center.text_frame
    tf_c.word_wrap = True

    p_title = tf_c.paragraphs[0]
    p_title.alignment = PP_ALIGN.CENTER
    r_t = p_title.add_run()
    r_t.text = "Securing the Digital Mine with a Metaheuristic-Optimized\nDeep Learning Adaptive Intrusion Detection System"
    r_t.font.name = "Arial"
    r_t.font.size = Pt(32)
    r_t.font.bold = True
    r_t.font.color.rgb = WHITE

    p_auth = tf_c.add_paragraph()
    p_auth.alignment = PP_ALIGN.CENTER
    p_auth.space_before = Pt(6)
    r_a = p_auth.add_run()
    r_a.text = "John Okyere (Lead), Ezekeil Baah, Clement Baffour, Parker Paa Annobil, George Akwesi Bonnah"
    r_a.font.name = "Arial"
    r_a.font.size = Pt(17)
    r_a.font.bold = True
    r_a.font.color.rgb = GOLD_ACCENT

    p_aff = tf_c.add_paragraph()
    p_aff.alignment = PP_ALIGN.CENTER
    r_aff = p_aff.add_run()
    r_aff.text = "Department of ICT, University of Education, Winneba & Kayaba Labs | Track 3: Smart Subsoil | Saint Petersburg, Russia 2026"
    r_aff.font.name = "Arial"
    r_aff.font.size = Pt(14)
    r_aff.font.color.rgb = RGBColor(226, 232, 240)

    # -------------------------------------------------------------
    # 3-COLUMN CONTENT CANVAS
    # -------------------------------------------------------------
    col_w = Inches(14.6)
    c1_left = Inches(1.0)
    c2_left = Inches(16.7)
    c3_left = Inches(32.4)
    top_y = Inches(5.8)

    # Helper function for Section Headings
    def add_section_header(left, top, title_text):
        tb = slide.shapes.add_textbox(left, top, col_w, Inches(0.6))
        tf = tb.text_frame
        p = tf.paragraphs[0]
        r = p.add_run()
        r.text = title_text
        r.font.name = "Arial"
        r.font.size = Pt(22)
        r.font.bold = True
        r.font.color.rgb = HEADING_COLOR
        return top + Inches(0.65)

    # =============================================================
    # COLUMN 1: INTRODUCTION, INDUSTRIAL CONTEXT & METHODOLOGY
    # =============================================================
    # 1. Introduction & Mining Threat Context
    y1 = add_section_header(c1_left, top_y, "1. Industrial Threat Landscape & Context")
    tb_intro = slide.shapes.add_textbox(c1_left, y1, col_w, Inches(3.8))
    tf_intro = tb_intro.text_frame
    tf_intro.word_wrap = True

    intro_pts = [
        ("Smart Subsoil Paradigm: ", "African and Russian mining complexes are deploying IoT telemetry, autonomous haulage, and SCADA to maximize ore recovery in SAG mills, flotation circuits, and tailings dams."),
        ("The Air-Gap Collapse: ", "Connecting operational technology (OT) to enterprise cloud analytics exposes unauthenticated Modbus RTU/TCP and DNP3 industrial protocols to hostile cyber intrusions."),
        ("Downtime Losses & Life Safety: ", "Unplanned mining downtime costs USD $50k-$500k/hr. Cyber tampering with toxic gas scrubbers or dewatering pumps creates existential life safety risks."),
        ("Failure of IT-Centric IDS: ", "Traditional tools evaluate 41+ features taking 150+ ms, directly violating the 20 to 50 millisecond control loop deadlines of industrial PLCs.")
    ]
    for b_title, b_desc in intro_pts:
        p = tf_intro.add_paragraph()
        p.space_after = Pt(4)
        r1 = p.add_run()
        r1.text = "• " + b_title
        r1.font.bold = True
        r1.font.size = Pt(12)
        r1.font.color.rgb = BANNER_BLUE
        r2 = p.add_run()
        r2.text = b_desc
        r2.font.size = Pt(11.5)
        r2.font.color.rgb = TEXT_DARK

    # Embedded Image 1.1: Mining SCADA Flowchart
    if os.path.exists("research/figures/mining_scada_flowchart.png"):
        slide.shapes.add_picture("research/figures/mining_scada_flowchart.png", c1_left, y1 + Inches(3.8), width=col_w)

    # 2. Methodology & Mathematical Framework
    y1_m = y1 + Inches(8.4)
    add_section_header(c1_left, y1_m, "2. Method & BWOA Mathematics")
    tb_meth = slide.shapes.add_textbox(c1_left, y1_m + Inches(0.65), col_w, Inches(16.0))
    tf_meth = tb_meth.text_frame
    tf_meth.word_wrap = True

    meth_pts = [
        ("Data Ingestion Layer: ", "Promiscuous packet capture using @mhiskall282/unesco-mine-sec-cli parsing IP/TCP/Modbus headers without packet drop."),
        ("1. Shrinking Encircling: ", "D = |C * X*(t) - X(t)|,  X(t+1) = X*(t) - A * D  (where A = 2a*r1 - a, C = 2*r2, and 'a' linearly decreases from 2 to 0)."),
        ("2. Spiral Bubble-Net: ", "X(t+1) = D' * exp(b*l) * cos(2*pi*l) + X*(t)  (where b=1.0, l is uniform in [-1, 1], modeling humpback hunting)."),
        ("3. V-Shaped Binary Transfer: ", "V(x) = |x / sqrt(1 + x^2)|,  X_d(t+1) = 1 - X_d(t) if rand() < V(x_d) else X_d(t)."),
        ("4. Accuracy Floor Fitness: ", "Fit(X) = alpha*(1 - Acc(X)) + (1-alpha)*(|X|/D) + Penalty  (alpha=0.3, Penalty=1.0 if Acc < 0.75 or |X| < 10)."),
        ("Spatial-Temporal Classifier: ", "Conv1D spatial feature extraction (64 filters) + LSTM temporal state tracking (256 units)."),
        ("Float16 Edge Quantization: ", "Compresses model to 0.82 MB (83.2% reduction) for sub-millisecond execution on 1GB RAM edge gateways.")
    ]
    for m_title, m_desc in meth_pts:
        p = tf_meth.add_paragraph()
        p.space_after = Pt(6)
        r1 = p.add_run()
        r1.text = "• " + m_title
        r1.font.bold = True
        r1.font.size = Pt(12)
        r1.font.color.rgb = BANNER_BLUE
        r2 = p.add_run()
        r2.text = m_desc
        r2.font.size = Pt(11.5)
        r2.font.color.rgb = TEXT_DARK

    # Bottom Left Decorative Diagonal Accent Stripes
    for i in range(8):
        accent_stripe = slide.shapes.add_shape(
            MSO_SHAPE.PARALLELOGRAM,
            Inches(1.0 + i * 0.9), Inches(29.8), Inches(0.6), Inches(1.2)
        )
        accent_stripe.fill.solid()
        accent_stripe.fill.fore_color.rgb = BANNER_BLUE if i % 2 == 0 else CYAN_ACCENT
        accent_stripe.line.fill.background()

    # =============================================================
    # COLUMN 2: ARCHITECTURE, BWOA ANALYSIS & NEURAL NETWORK
    # =============================================================
    # 1. 4-Layer Architecture Diagram Top
    y2 = add_section_header(c2_left, top_y, "3. 4-Layer System Architecture")
    if os.path.exists("research/figures/system_architecture.png"):
        slide.shapes.add_picture("research/figures/system_architecture.png", c2_left, y2 + Inches(0.1), width=col_w)

    # 2. BWOA Analysis & Selected 10 Features
    y2_b = y2 + Inches(8.4)
    add_section_header(c2_left, y2_b, "4. BWOA Optimization Analysis")
    if os.path.exists("research/figures/bwoa_convergence.png"):
        slide.shapes.add_picture("research/figures/bwoa_convergence.png", c2_left, y2_b + Inches(0.65), width=col_w)

    tb_bwoa_desc = slide.shapes.add_textbox(c2_left, y2_b + Inches(7.6), col_w, Inches(8.0))
    tf_bd = tb_bwoa_desc.text_frame
    tf_bd.word_wrap = True

    bwoa_analysis_pts = [
        ("Rapid Convergence: ", "Optimal 10-feature subset found at iteration 23/100, achieving 92.31% Random Forest 3-fold cross-validation accuracy."),
        ("75.61% Bandwidth Reduction: ", "Pruned 31 redundant attributes, minimizing transmission loads over low-bandwidth satellite links in remote African mines."),
        ("Selected 10 Vital Features: ", "src_bytes (DoS volume), service & flag (SCADA protocol state), serror_rate & diff_srv_rate (reconnaissance), hot & su_attempted (privilege escalation)."),
        ("CNN-LSTM Deep Learning Layout: ", "Conv1D (64 filters, kernel 3) + BatchNorm + SpatialDropout(0.3) + LSTM(256 units) + Dense(64) + Softmax(5 classes).")
    ]
    for b_title, b_desc in bwoa_analysis_pts:
        p = tf_bd.add_paragraph()
        p.space_after = Pt(5)
        r1 = p.add_run()
        r1.text = "★ " + b_title
        r1.font.bold = True
        r1.font.size = Pt(12)
        r1.font.color.rgb = GOLD_ACCENT
        r2 = p.add_run()
        r2.text = b_desc
        r2.font.size = Pt(11.5)
        r2.font.color.rgb = TEXT_DARK

    # =============================================================
    # COLUMN 3: EMPIRICAL RESULTS, HARDWARE BENCHMARKS & CONCLUSIONS
    # =============================================================
    # 1. Performance Evaluation: Latency & Confusion Matrix
    y3 = add_section_header(c3_left, top_y, "5. Empirical Evaluation & Benchmarks")
    if os.path.exists("research/figures/latency_comparison_barchart.png"):
        slide.shapes.add_picture("research/figures/latency_comparison_barchart.png", c3_left, y3 + Inches(0.1), width=col_w)

    if os.path.exists("research/figures/confusion_matrix.png"):
        slide.shapes.add_picture("research/figures/confusion_matrix.png", c3_left, y3 + Inches(6.8), width=col_w)

    # 2. Key Results Table & Conclusions
    y3_c = y3 + Inches(13.8)
    add_section_header(c3_left, y3_c, "6. Conclusions & UN SDG Impact")
    tb_conc = slide.shapes.add_textbox(c3_left, y3_c + Inches(0.65), col_w, Inches(7.5))
    tf_conc = tb_conc.text_frame
    tf_conc.word_wrap = True

    conc_pts = [
        ("0.76ms Edge Real-Time: ", "Executes single-sample inference in 0.76 ms on Raspberry Pi 4B (1GB RAM) - 207x faster than baseline, easily passing the sub-100ms SCADA limit."),
        ("High Detection Fidelity: ", "70.56% multi-class accuracy, 96.89% precision on benign telemetry (zero false production halts), 89.04% recall on DoS attacks."),
        ("High Industrial ROI: ", "200x-300x return averting $50k-$500k/hr outages while protecting miner lives against ventilation tampering (SDG 8 & 9)."),
        ("Open-Source Ecosystem: ", "NPM sniffer package '@mhiskall282/unesco-mine-sec-cli' and 75/75 passing unit test suites ensure complete reproducibility.")
    ]
    for c_title, c_desc in conc_pts:
        p = tf_conc.add_paragraph()
        p.space_after = Pt(4)
        r1 = p.add_run()
        r1.text = "✓ " + c_title
        r1.font.bold = True
        r1.font.size = Pt(12)
        r1.font.color.rgb = EMERALD
        r2 = p.add_run()
        r2.text = c_desc
        r2.font.size = Pt(11.5)
        r2.font.color.rgb = TEXT_DARK

    # 3. References Section
    y3_r = y3_c + Inches(7.6)
    add_section_header(c3_left, y3_r, "7. References")
    tb_refs = slide.shapes.add_textbox(c3_left, y3_r + Inches(0.55), col_w, Inches(4.0))
    tf_refs = tb_refs.text_frame
    tf_refs.word_wrap = True

    refs = [
        "1. Alanazi, M., et al. (2022). SCADA vulnerabilities and attacks: A review. Computers & Security, 125, 103028.",
        "2. Almomani, O., et al. (2025). Cyberattack detection for SCADA in industrial IoT. Symmetry, 17(4), 480.",
        "3. Kheddar, H., et al. (2023). Deep transfer learning for intrusion detection in ICS. JNCA, 220, 103747.",
        "4. Mirjalili, S., & Lewis, A. (2016). The whale optimization algorithm. Advances in Eng. Software, 95, 51-67.",
        "5. Minerals Commission of Ghana. (2024). Digital telemetry & cybersecurity compliance policy guidelines."
    ]
    for ref_text in refs:
        p = tf_refs.add_paragraph()
        p.space_after = Pt(2)
        r = p.add_run()
        r.text = ref_text
        r.font.name = "Arial"
        r.font.size = Pt(9.5)
        r.font.color.rgb = TEXT_MUTED

    output_pptx = "research/poster_presentation.pptx"
    prs.save(output_pptx)
    print(f"Exhaustive Academic Landscape Poster (PPTX) saved successfully to {output_pptx}!")

def create_academic_landscape_poster_docx():
    doc = Document()
    for section in doc.sections:
        section.top_margin = DInches(0.4)
        section.bottom_margin = DInches(0.4)
        section.left_margin = DInches(0.5)
        section.right_margin = DInches(0.5)
        section.page_width = DInches(17.0)
        section.page_height = DInches(11.0)

    # Top Header Banner Table
    tbl_hdr = doc.add_table(rows=1, cols=3)
    tbl_hdr.alignment = WD_TABLE_ALIGNMENT.CENTER
    tbl_hdr.autofit = False
    tbl_hdr.rows[0].cells[0].width = DInches(2.5)
    tbl_hdr.rows[0].cells[1].width = DInches(11.0)
    tbl_hdr.rows[0].cells[2].width = DInches(2.5)

    for cell in tbl_hdr.rows[0].cells:
        set_cell_background(cell, "00529B")
        set_cell_margins(cell, top=120, bottom=120, left=120, right=120)
    set_table_borders(tbl_hdr, color="FFD700", sz="12", val="single")

    # Left Cell
    p_l = tbl_hdr.rows[0].cells[0].paragraphs[0]
    p_l.alignment = WD_ALIGN_PARAGRAPH.LEFT
    r_l1 = p_l.add_run("UEW\n")
    r_l1.font.name = 'Arial'
    r_l1.font.size = DPt(22)
    r_l1.font.bold = True
    r_l1.font.color.rgb = DRGBColor(255, 255, 255)
    r_l2 = p_l.add_run("Univ. of Education, Winneba\n& Kayaba Labs")
    r_l2.font.name = 'Arial'
    r_l2.font.size = DPt(9.5)
    r_l2.font.bold = True
    r_l2.font.color.rgb = DRGBColor(0, 163, 224)

    # Center Cell
    p_c = tbl_hdr.rows[0].cells[1].paragraphs[0]
    p_c.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_ct = p_c.add_run("Securing the Digital Mine with a Metaheuristic-Optimized\nDeep Learning Adaptive System\n")
    r_ct.font.name = 'Arial'
    r_ct.font.size = DPt(17)
    r_ct.font.bold = True
    r_ct.font.color.rgb = DRGBColor(255, 255, 255)
    r_ca = p_c.add_run("John Okyere, Ezekeil Baah, Clement Baffour, Parker Paa Annobil, George Akwesi Bonnah\n")
    r_ca.font.name = 'Arial'
    r_ca.font.size = DPt(11)
    r_ca.font.bold = True
    r_ca.font.color.rgb = DRGBColor(255, 215, 0)
    r_caff = p_c.add_run("Department of ICT, University of Education, Winneba & Kayaba Labs | Saint Petersburg Mining University 2026")
    r_caff.font.name = 'Arial'
    r_caff.font.size = DPt(9)
    r_caff.font.color.rgb = DRGBColor(226, 232, 240)

    # Right Cell
    p_r = tbl_hdr.rows[0].cells[2].paragraphs[0]
    p_r.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    r_r1 = p_r.add_run("UNESCO\n")
    r_r1.font.name = 'Arial'
    r_r1.font.size = DPt(22)
    r_r1.font.bold = True
    r_r1.font.color.rgb = DRGBColor(255, 215, 0)
    r_r2 = p_r.add_run("Russian-African Forum\nTrack 3: Smart Subsoil")
    r_r2.font.name = 'Arial'
    r_r2.font.size = DPt(9.5)
    r_r2.font.bold = True
    r_r2.font.color.rgb = DRGBColor(255, 255, 255)

    doc.add_paragraph().paragraph_format.space_after = DPt(6)

    # 3-Column Content Table
    tbl_cols = doc.add_table(rows=1, cols=3)
    tbl_cols.alignment = WD_TABLE_ALIGNMENT.CENTER
    tbl_cols.autofit = False
    tbl_cols.rows[0].cells[0].width = DInches(5.3)
    tbl_cols.rows[0].cells[1].width = DInches(5.3)
    tbl_cols.rows[0].cells[2].width = DInches(5.3)

    for cell in tbl_cols.rows[0].cells:
        set_cell_background(cell, "FFFFFF")
        set_cell_margins(cell, top=80, bottom=80, left=100, right=100)
    set_table_borders(tbl_cols, color="CBD5E1", sz="4", val="single")

    def add_col_section(cell, title, bullets, image_path=None, image_w=4.9):
        p_t = cell.add_paragraph() if cell.paragraphs[0].text else cell.paragraphs[0]
        p_t.paragraph_format.space_before = DPt(4)
        p_t.paragraph_format.space_after = DPt(2)
        rt = p_t.add_run(clean_text(title))
        rt.font.name = 'Arial'
        rt.font.size = DPt(12)
        rt.font.bold = True
        rt.font.color.rgb = DRGBColor(0, 82, 155)

        for b in bullets:
            pb = cell.add_paragraph()
            pb.paragraph_format.space_after = DPt(3)
            pb.paragraph_format.line_spacing = 1.15
            rb = pb.add_run("• " + clean_text(b))
            rb.font.name = 'Arial'
            rb.font.size = DPt(8.5)
            rb.font.color.rgb = DRGBColor(30, 41, 59)

        if image_path and os.path.exists(image_path):
            p_img = cell.add_paragraph()
            p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p_img.paragraph_format.space_before = DPt(3)
            p_img.paragraph_format.space_after = DPt(3)
            p_img.add_run().add_picture(image_path, width=DInches(image_w))

    # Column 1
    c1 = tbl_cols.rows[0].cells[0]
    add_col_section(c1, "1. Industrial Threat Landscape", [
        "Mining 4.0: African/Russian mines deploy IoT & SCADA telemetry to maximize ore yield.",
        "Air-Gap Collapse: Connecting OT to cloud twins exposes Modbus/DNP3 protocols to zero-day attacks.",
        "Downtime Losses: Unplanned downtime costs USD $50k-$500k/hr; gas/ventilation disruption endangers lives.",
        "IT IDS Mismatch: Traditional tools take 150+ ms, violating the 20-50ms SCADA control loop deadline."
    ], image_path="research/figures/mining_scada_flowchart.png", image_w=4.9)

    add_col_section(c1, "2. Method & BWOA Mathematics", [
        "Data Ingestion: Sniffer agent (@mhiskall282/unesco-mine-sec-cli) parses Modbus/TCP frames.",
        "Shrinking Encircling: D = |C*X* - X|, X(t+1) = X* - A*D (A = 2a*r1 - a, C = 2*r2).",
        "Spiral Bubble-Net: X(t+1) = D'*exp(b*l)*cos(2*pi*l) + X* (b=1.0, l in [-1, 1]).",
        "V-Shaped Transfer: V(x) = |x / sqrt(1 + x^2)|, stochastic bit-flipping on velocity.",
        "Accuracy Floor Fitness: Fit(X) = 0.3*(1 - Acc) + 0.7*(|X|/D) + Penalty (Acc >= 75%).",
        "Float16 Quantization: Model compressed to 0.82 MB for sub-millisecond execution on 1GB RAM edge gateways."
    ])

    # Column 2
    c2 = tbl_cols.rows[0].cells[1]
    add_col_section(c2, "3. 4-Layer Architecture Pipeline", [
        "Decoupled 4-layer edge architecture ensures sub-millisecond real-time threat evaluation:"
    ], image_path="research/figures/system_architecture.png", image_w=4.9)

    add_col_section(c2, "4. BWOA Optimization Analysis", [
        "BWOA convergence reached at iteration 23/100, maintaining 92.31% RF cross-validation accuracy.",
        "Selected 10 Features: src_bytes, service, flag, serror_rate, same_srv_rate, diff_srv_rate, dst_host_diff_srv_rate, protocol_type, hot, su_attempted."
    ], image_path="research/figures/bwoa_convergence.png", image_w=4.9)

    # Column 3
    c3 = tbl_cols.rows[0].cells[2]
    add_col_section(c3, "5. Empirical Evaluation & Benchmarks", [
        "Single-sample latency vs 100ms SCADA ceiling & confusion matrix on held-out test data:"
    ], image_path="research/figures/latency_comparison_barchart.png", image_w=4.9)

    add_col_section(c3, "6. Conclusions & UN SDG Impact", [
        "0.76ms Edge Latency: 207x faster than baseline on Raspberry Pi 4B (1GB RAM), passing SCADA constraints.",
        "High Detection Fidelity: 70.56% multi-class accuracy, 96.89% benign precision, 89.04% DoS recall.",
        "Industrial ROI: 200x-300x return averting $50k-$500k/hr outages while protecting miner lives (SDG 8 & 9).",
        "Open-Source Ecosystem: Package @mhiskall282/unesco-mine-sec-cli published on GitHub Packages."
    ])

    add_col_section(c3, "7. References", [
        "1. Alanazi, M., et al. (2022). SCADA vulnerabilities & attacks. Computers & Security, 125, 103028.",
        "2. Almomani, O., et al. (2025). SCADA intrusion detection in IIoT. Symmetry, 17(4), 480.",
        "3. Mirjalili, S., & Lewis, A. (2016). Whale optimization algorithm. Adv. Eng. Software, 95, 51-67.",
        "4. Minerals Commission of Ghana. (2024). Digital telemetry & cybersecurity compliance policy."
    ])

    output_docx = "research/poster_presentation.docx"
    doc.save(output_docx)
    print(f"Exhaustive Academic Landscape Poster (DOCX) saved successfully to {output_docx}!")

if __name__ == "__main__":
    create_academic_landscape_poster_pptx()
    create_academic_landscape_poster_docx()
