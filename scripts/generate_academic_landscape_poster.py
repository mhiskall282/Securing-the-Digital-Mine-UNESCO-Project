"""Generate an academic landscape scientific conference poster matching the clean 3-column reference design.
Dimensions: 48.0 x 32.0 inches (Landscape).
Top vibrant header banner + clean 3-column white canvas + prominent charts + crisp typography.
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
from pptx.enum.text import PP_ALIGN, MSO_ANCHOR
from pptx.enum.shapes import MSO_SHAPE

from docx_styler import clean_text, set_table_borders, set_cell_background, set_cell_margins

def create_academic_landscape_poster_pptx():
    prs = Presentation()
    # 48 x 30 inches (Standard 16:10 Academic Landscape Conference Poster)
    prs.slide_width = Inches(48.0)
    prs.slide_height = Inches(30.0)

    blank_layout = prs.slide_layouts[6]
    slide = prs.slides.add_slide(blank_layout)

    # Color Palette matching UNESCO & Modern Academic Poster Design
    BANNER_BLUE = RGBColor(0, 82, 155)       # #00529B (Vibrant UNESCO Blue)
    DARK_NAVY = RGBColor(11, 29, 58)         # #0B1D3A
    CYAN_ACCENT = RGBColor(0, 163, 224)      # #00A3E0
    GOLD_ACCENT = RGBColor(255, 215, 0)      # #FFD700 (Vibrant Gold)
    HEADING_COLOR = RGBColor(0, 82, 155)    # #00529B (Section Titles)
    TEXT_DARK = RGBColor(30, 41, 59)         # #1E293B (Body Text)
    TEXT_MUTED = RGBColor(100, 116, 139)     # #64748B
    WHITE = RGBColor(255, 255, 255)
    EMERALD = RGBColor(16, 185, 129)

    # 1. Pure White Slide Canvas
    canvas = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, Inches(48.0), Inches(30.0))
    canvas.fill.solid()
    canvas.fill.fore_color.rgb = WHITE
    canvas.line.fill.background()

    # 2. Top Vibrant Header Banner (Height: 5.5 inches)
    banner = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, 0, Inches(48.0), Inches(5.5))
    banner.fill.solid()
    banner.fill.fore_color.rgb = BANNER_BLUE
    banner.line.fill.background()

    # Banner Bottom Gold Accent Strip
    strip = slide.shapes.add_shape(MSO_SHAPE.RECTANGLE, 0, Inches(5.4), Inches(48.0), Inches(0.12))
    strip.fill.solid()
    strip.fill.fore_color.rgb = GOLD_ACCENT
    strip.line.fill.background()

    # Header Left Institution Text (UND / UEW Style)
    tb_inst_l = slide.shapes.add_textbox(Inches(1.0), Inches(0.6), Inches(6.0), Inches(4.2))
    tf_il = tb_inst_l.text_frame
    p_il1 = tf_il.paragraphs[0]
    r_il1 = p_il1.add_run()
    r_il1.text = "UEW"
    r_il1.font.name = "Arial"
    r_il1.font.size = Pt(38)
    r_il1.font.bold = True
    r_il1.font.color.rgb = WHITE

    p_il2 = tf_il.add_paragraph()
    r_il2 = p_il2.add_run()
    r_il2.text = "UNIVERSITY OF EDUCATION,\nWINNEBA, GHANA\n& KAYABA LABS"
    r_il2.font.name = "Arial"
    r_il2.font.size = Pt(13)
    r_il2.font.bold = True
    r_il2.font.color.rgb = CYAN_ACCENT

    # Header Right Forum Details
    tb_inst_r = slide.shapes.add_textbox(Inches(41.0), Inches(0.6), Inches(6.0), Inches(4.2))
    tf_ir = tb_inst_r.text_frame
    tf_ir.word_wrap = True
    p_ir1 = tf_ir.paragraphs[0]
    p_ir1.alignment = PP_ALIGN.RIGHT
    r_ir1 = p_ir1.add_run()
    r_ir1.text = "UNESCO"
    r_ir1.font.name = "Arial"
    r_ir1.font.size = Pt(36)
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
    tb_center = slide.shapes.add_textbox(Inches(7.2), Inches(0.4), Inches(33.6), Inches(4.8))
    tf_c = tb_center.text_frame
    tf_c.word_wrap = True

    p_title = tf_c.paragraphs[0]
    p_title.alignment = PP_ALIGN.CENTER
    r_t = p_title.add_run()
    r_t.text = "Securing the Digital Mine with a Metaheuristic-Optimized\nDeep Learning Adaptive System"
    r_t.font.name = "Arial"
    r_t.font.size = Pt(36)
    r_t.font.bold = True
    r_t.font.color.rgb = WHITE

    p_auth = tf_c.add_paragraph()
    p_auth.alignment = PP_ALIGN.CENTER
    p_auth.space_before = Pt(8)
    r_a = p_auth.add_run()
    r_a.text = "John Okyere, Ezekeil Baah, Clement Baffour, Parker Paa Annobil, George Akwesi Bonnah"
    r_a.font.name = "Arial"
    r_a.font.size = Pt(18)
    r_a.font.bold = True
    r_a.font.color.rgb = GOLD_ACCENT

    p_aff = tf_c.add_paragraph()
    p_aff.alignment = PP_ALIGN.CENTER
    r_aff = p_aff.add_run()
    r_aff.text = "Department of ICT, University of Education, Winneba & Kayaba Labs | Saint Petersburg, Russia 2026"
    r_aff.font.name = "Arial"
    r_aff.font.size = Pt(15)
    r_aff.font.color.rgb = RGBColor(226, 232, 240)

    # -------------------------------------------------------------
    # 3-COLUMN CONTENT CANVAS
    # -------------------------------------------------------------
    # Column Parameters:
    col_w = Inches(14.4)
    c1_left = Inches(1.2)
    c2_left = Inches(16.8)
    c3_left = Inches(32.4)
    top_y = Inches(6.0)

    # Helper function for Section Headings
    def add_section_header(slide_obj, left, top, title_text):
        tb = slide_obj.shapes.add_textbox(left, top, col_w, Inches(0.8))
        tf = tb.text_frame
        p = tf.paragraphs[0]
        r = p.add_run()
        r.text = title_text
        r.font.name = "Arial"
        r.font.size = Pt(26)
        r.font.bold = True
        r.font.color.rgb = HEADING_COLOR
        return top + Inches(0.8)

    # =============================================================
    # COLUMN 1: INTRODUCTION & METHOD
    # =============================================================
    # 1. Introduction Section
    y1 = add_section_header(slide, c1_left, top_y, "Introduction")
    tb_intro = slide.shapes.add_textbox(c1_left, y1, col_w, Inches(5.8))
    tf_intro = tb_intro.text_frame
    tf_intro.word_wrap = True

    intro_bullets = [
        "Mining 4.0 & Smart Subsoil: African and Russian mineral extraction complexes are rapidly integrating IoT sensors, SCADA telemetry, and automated milling fleets to maximize ore recovery.",
        "The Air-Gap Myth: Connecting OT networks to enterprise analytics exposes unauthenticated Modbus RTU/TCP and DNP3 industrial protocols to hostile zero-day cyber intrusions.",
        "High Downtime Costs: Unplanned mining downtime costs between USD $50,000 and $500,000 per hour; cyber-physical disruption of ventilation or dewatering directly endangers human life.",
        "Failure of IT-Centric IDS: Enterprise security tools analyze 41+ features taking 150+ ms, directly violating the 20 to 50 millisecond control loop deadlines of industrial PLCs."
    ]
    for b in intro_bullets:
        p = tf_intro.add_paragraph()
        p.space_after = Pt(8)
        p.font.size = Pt(14.5)
        p.font.name = "Arial"
        p.font.color.rgb = TEXT_DARK
        r = p.add_run()
        r.text = "• " + b

    # 2. Method Section
    y1_m = y1 + Inches(6.0)
    add_section_header(slide, c1_left, y1_m, "Method")
    tb_meth = slide.shapes.add_textbox(c1_left, y1_m + Inches(0.8), col_w, Inches(13.5))
    tf_meth = tb_meth.text_frame
    tf_meth.word_wrap = True

    p_m_intro = tf_meth.paragraphs[0]
    p_m_intro.space_after = Pt(8)
    r_mi = p_m_intro.add_run()
    r_mi.text = "Our approach is built around four interconnected modules designed to operate in real-time on edge gateways:"
    r_mi.font.name = "Arial"
    r_mi.font.size = Pt(14.5)
    r_mi.font.bold = True
    r_mi.font.color.rgb = TEXT_DARK

    method_modules = [
        ("Data Ingestion & Extraction: ", "Promiscuously captures raw network frames from SCADA interfaces at line speed using @mhiskall282/unesco-mine-sec-cli, parsing IP/TCP/Modbus packet headers without dropping packets."),
        ("BWOA Feature Pruning: ", "Applies Binary Whale Optimization with a V-shaped transfer function and a 75% accuracy floor constraint, pruning input dimensions by 75.61% (from 41 down to 10 vital features)."),
        ("Spatial-Temporal Classifier: ", "Combines a 1D Convolutional Neural Network (Conv1D) layer for packet spatial feature extraction with Long Short-Term Memory (LSTM) cells for temporal state tracking."),
        ("Float16 Edge Quantization: ", "Applies post-training Float16 weight quantization, compressing model memory footprint to 0.82 MB (83.2% compression) for sub-millisecond execution.")
    ]
    for m_title, m_desc in method_modules:
        p = tf_meth.add_paragraph()
        p.space_after = Pt(10)
        r1 = p.add_run()
        r1.text = "• " + m_title
        r1.font.bold = True
        r1.font.size = Pt(14)
        r1.font.name = "Arial"
        r1.font.color.rgb = BANNER_BLUE
        r2 = p.add_run()
        r2.text = m_desc
        r2.font.size = Pt(14)
        r2.font.name = "Arial"
        r2.font.color.rgb = TEXT_DARK

    # Bottom Left Decorative Diagonal Accent Stripes
    for i in range(7):
        accent_stripe = slide.shapes.add_shape(
            MSO_SHAPE.PARALLELOGRAM,
            Inches(1.2 + i * 0.9), Inches(27.8), Inches(0.6), Inches(1.4)
        )
        accent_stripe.fill.solid()
        accent_stripe.fill.fore_color.rgb = BANNER_BLUE if i % 2 == 0 else CYAN_ACCENT
        accent_stripe.line.fill.background()

    # =============================================================
    # COLUMN 2: SYSTEM ARCHITECTURE & ANALYSIS
    # =============================================================
    # 1. Pipeline Diagram Top
    tb_pipe_lbl = slide.shapes.add_textbox(c2_left, top_y, col_w, Inches(1.0))
    tf_pl = tb_pipe_lbl.text_frame
    tf_pl.word_wrap = True
    p_pl = tf_pl.paragraphs[0]
    r_pl = p_pl.add_run()
    r_pl.text = "• Real-Time Edge Telemetry to Deep Learning Inference Pipeline:"
    r_pl.font.name = "Arial"
    r_pl.font.size = Pt(15)
    r_pl.font.bold = True
    r_pl.font.color.rgb = TEXT_DARK

    if os.path.exists("research/figures/system_architecture.png"):
        slide.shapes.add_picture(
            "research/figures/system_architecture.png",
            c2_left, top_y + Inches(0.8), width=col_w
        )

    # 2. Analysis Section
    y2_a = top_y + Inches(8.8)
    add_section_header(slide, c2_left, y2_a, "Analysis")
    tb_ana = slide.shapes.add_textbox(c2_left, y2_a + Inches(0.8), col_w, Inches(6.0))
    tf_ana = tb_ana.text_frame
    tf_ana.word_wrap = True

    ana_bullets = [
        ("BWOA Convergence: ", "BWOA optimization reached minimal fitness at iteration 23/100, maintaining 92.31% Random Forest cross-validation accuracy on the selected 10-feature subset."),
        ("Significant Dimension Reduction: ", "Pruned 31 uninformative features, reducing bandwidth transmission overhead by 75.61% across low-bandwidth satellite links in African extraction sites."),
        ("Key Discriminative Signals: ", "Selected features capture connection state (`service`, `flag`), volume asymmetry for DoS (`src_bytes`), privilege escalation (`hot`, `su_attempted`), and error rates (`serror_rate`, `same_srv_rate`).")
    ]
    for a_title, a_desc in ana_bullets:
        p = tf_ana.add_paragraph()
        p.space_after = Pt(8)
        r1 = p.add_run()
        r1.text = "• " + a_title
        r1.font.bold = True
        r1.font.size = Pt(14)
        r1.font.name = "Arial"
        r1.font.color.rgb = BANNER_BLUE
        r2 = p.add_run()
        r2.text = a_desc
        r2.font.size = Pt(14)
        r2.font.name = "Arial"
        r2.font.color.rgb = TEXT_DARK

    # Embedded Chart 1: BWOA Convergence
    if os.path.exists("research/figures/bwoa_convergence.png"):
        slide.shapes.add_picture(
            "research/figures/bwoa_convergence.png",
            c2_left, y2_a + Inches(7.0), width=col_w
        )

    # =============================================================
    # COLUMN 3: PERFORMANCE, CONCLUSIONS & REFERENCES
    # =============================================================
    # 1. Performance Section
    tb_perf_lbl = slide.shapes.add_textbox(c3_left, top_y, col_w, Inches(1.2))
    tf_perfl = tb_perf_lbl.text_frame
    tf_perfl.word_wrap = True
    p_perfl = tf_perfl.paragraphs[0]
    r_pfl = p_perfl.add_run()
    r_pfl.text = "• Objective latency and multi-class benchmark evaluation on physical edge hardware (Raspberry Pi 4B, 1GB RAM):"
    r_pfl.font.name = "Arial"
    r_pfl.font.size = Pt(14.5)
    r_pfl.font.bold = True
    r_pfl.font.color.rgb = TEXT_DARK

    # Embedded Chart 2: Latency Bar Chart
    if os.path.exists("research/figures/latency_comparison_barchart.png"):
        slide.shapes.add_picture(
            "research/figures/latency_comparison_barchart.png",
            c3_left, top_y + Inches(1.2), width=col_w
        )

    # 2. Conclusions Section
    y3_c = top_y + Inches(9.2)
    add_section_header(slide, c3_left, y3_c, "Conclusions")
    tb_conc = slide.shapes.add_textbox(c3_left, y3_c + Inches(0.8), col_w, Inches(7.5))
    tf_conc = tb_conc.text_frame
    tf_conc.word_wrap = True

    conc_bullets = [
        ("Sub-Millisecond Edge Real-Time: ", "Executes single-sample inference in 0.76 milliseconds on a Raspberry Pi 4B (207x faster than baseline), easily satisfying the sub-100ms industrial SCADA control loop deadline."),
        ("High Detection Fidelity: ", "Delivers 70.56% multi-class accuracy, 96.89% precision on benign telemetry (preventing false alarms), and 89.04% recall on denial-of-service intrusions."),
        ("Economic & Life Safety Impact: ", "Provides 200x to 300x industrial ROI by preventing USD $50,000 to $500,000/hr downtime while safeguarding miner lives from toxic gas or ventilation manipulation (SDG 8 & 9)."),
        ("Open-Source Ecosystem: ", "Fully open-source CLI agent (@mhiskall282/unesco-mine-sec-cli) and 75/75 automated unit test suites ensure complete reproducibility.")
    ]
    for c_title, c_desc in conc_bullets:
        p = tf_conc.add_paragraph()
        p.space_after = Pt(8)
        r1 = p.add_run()
        r1.text = "• " + c_title
        r1.font.bold = True
        r1.font.size = Pt(14)
        r1.font.name = "Arial"
        r1.font.color.rgb = EMERALD
        r2 = p.add_run()
        r2.text = c_desc
        r2.font.size = Pt(14)
        r2.font.name = "Arial"
        r2.font.color.rgb = TEXT_DARK

    # 3. References Section (Bottom Right)
    y3_r = y3_c + Inches(8.6)
    add_section_header(slide, c3_left, y3_r, "References")
    tb_refs = slide.shapes.add_textbox(c3_left, y3_r + Inches(0.7), col_w, Inches(4.5))
    tf_refs = tb_refs.text_frame
    tf_refs.word_wrap = True

    refs = [
        "1. Alanazi, M., et al. (2022). SCADA vulnerabilities and attacks: A review. Computers & Security, 125, 103028.",
        "2. Almomani, O., et al. (2025). Cyberattack detection for SCADA in industrial IoT. Symmetry, 17(4), 480.",
        "3. Kheddar, H., et al. (2023). Deep transfer learning for intrusion detection in ICS. JNCA, 220, 103747.",
        "4. Mirjalili, S., & Lewis, A. (2016). The whale optimization algorithm. Advances in Eng. Software, 95, 51-67.",
        "5. Minerals Commission of Ghana. (2024). Digital telemetry and cybersecurity compliance guidelines."
    ]
    for ref_text in refs:
        p = tf_refs.add_paragraph()
        p.space_after = Pt(3)
        r = p.add_run()
        r.text = ref_text
        r.font.name = "Arial"
        r.font.size = Pt(10)
        r.font.color.rgb = TEXT_MUTED

    output_pptx = "research/poster_presentation.pptx"
    prs.save(output_pptx)
    print(f"Academic Landscape Poster (PPTX) saved successfully to {output_pptx}!")

def create_academic_landscape_poster_docx():
    doc = Document()
    # Landscape format in docx (17 x 11 inches)
    for section in doc.sections:
        section.top_margin = DInches(0.5)
        section.bottom_margin = DInches(0.5)
        section.left_margin = DInches(0.6)
        section.right_margin = DInches(0.6)
        section.page_width = DInches(17.0)
        section.page_height = DInches(11.0)

    # Top Vibrant Header Banner
    tbl_hdr = doc.add_table(rows=1, cols=3)
    tbl_hdr.alignment = WD_TABLE_ALIGNMENT.CENTER
    tbl_hdr.autofit = False
    tbl_hdr.rows[0].cells[0].width = DInches(2.5)
    tbl_hdr.rows[0].cells[1].width = DInches(10.8)
    tbl_hdr.rows[0].cells[2].width = DInches(2.5)

    for cell in tbl_hdr.rows[0].cells:
        set_cell_background(cell, "00529B") # UNESCO Blue
        set_cell_margins(cell, top=140, bottom=140, left=140, right=140)
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
    r_ct.font.size = DPt(18)
    r_ct.font.bold = True
    r_ct.font.color.rgb = DRGBColor(255, 255, 255)
    r_ca = p_c.add_run("John Okyere, Ezekeil Baah, Clement Baffour, Parker Paa Annobil, George Akwesi Bonnah\n")
    r_ca.font.name = 'Arial'
    r_ca.font.size = DPt(11)
    r_ca.font.bold = True
    r_ca.font.color.rgb = DRGBColor(255, 215, 0)
    r_caff = p_c.add_run("Department of ICT, University of Education, Winneba & Kayaba Labs | Saint Petersburg Mining University 2026")
    r_caff.font.name = 'Arial'
    r_caff.font.size = DPt(9.5)
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

    doc.add_paragraph().paragraph_format.space_after = DPt(8)

    # 3-Column Content Table
    tbl_cols = doc.add_table(rows=1, cols=3)
    tbl_cols.alignment = WD_TABLE_ALIGNMENT.CENTER
    tbl_cols.autofit = False
    tbl_cols.rows[0].cells[0].width = DInches(5.2)
    tbl_cols.rows[0].cells[1].width = DInches(5.2)
    tbl_cols.rows[0].cells[2].width = DInches(5.2)

    for cell in tbl_cols.rows[0].cells:
        set_cell_background(cell, "FFFFFF")
        set_cell_margins(cell, top=100, bottom=100, left=120, right=120)
    set_table_borders(tbl_cols, color="CBD5E1", sz="4", val="single")

    # Helper for adding formatted sections inside a cell
    def add_col_section(cell, title, bullets, image_path=None, image_w=4.8):
        p_t = cell.add_paragraph() if cell.paragraphs[0].text else cell.paragraphs[0]
        p_t.paragraph_format.space_before = DPt(6)
        p_t.paragraph_format.space_after = DPt(3)
        rt = p_t.add_run(clean_text(title))
        rt.font.name = 'Arial'
        rt.font.size = DPt(14)
        rt.font.bold = True
        rt.font.color.rgb = DRGBColor(0, 82, 155)

        for b in bullets:
            pb = cell.add_paragraph()
            pb.paragraph_format.space_after = DPt(4)
            pb.paragraph_format.line_spacing = 1.15
            rb = pb.add_run("• " + clean_text(b))
            rb.font.name = 'Arial'
            rb.font.size = DPt(9.5)
            rb.font.color.rgb = DRGBColor(30, 41, 59)

        if image_path and os.path.exists(image_path):
            p_img = cell.add_paragraph()
            p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p_img.paragraph_format.space_before = DPt(4)
            p_img.paragraph_format.space_after = DPt(4)
            p_img.add_run().add_picture(image_path, width=DInches(image_w))

    # Populate Column 1 (Left): Introduction & Method
    c1 = tbl_cols.rows[0].cells[0]
    add_col_section(c1, "Introduction", [
        "Mining 4.0: African/Russian mines deploy IoT & SCADA telemetry to maximize ore yield.",
        "Air-Gap Collapse: Connecting OT to cloud twins exposes Modbus/DNP3 protocols to zero-day attacks.",
        "Downtime Costs: Unplanned downtime costs USD $50k-$500k/hr; gas/ventilation disruption endangers lives.",
        "IT IDS Mismatch: Traditional tools take 150+ ms, violating the 20-50ms SCADA control loop deadline."
    ])
    add_col_section(c1, "Method", [
        "Data Ingestion: Promiscuously captures SCADA packets via @mhiskall282/unesco-mine-sec-cli.",
        "BWOA Feature Pruning: Prunes 41 features down to 10 vital signals (75.61% reduction) with 75% accuracy floor.",
        "Spatial-Temporal Classifier: Conv1D spatial filters + LSTM temporal memory units.",
        "Float16 Quantization: Compresses model to 0.82 MB for sub-millisecond execution on 1GB RAM edge gateways."
    ])

    # Populate Column 2 (Center): Architecture & Analysis
    c2 = tbl_cols.rows[0].cells[1]
    add_col_section(c2, "Architecture & Pipeline", [
        "Decoupled 4-layer edge architecture ensures sub-millisecond real-time threat evaluation:"
    ], image_path="research/figures/system_architecture.png", image_w=4.8)

    add_col_section(c2, "Analysis & Convergence", [
        "BWOA convergence reached at iteration 23/100, maintaining 92.31% RF cross-validation accuracy.",
        "Selected features: protocol_type, service, flag, src_bytes, hot, su_attempted, serror_rate, same_srv_rate, diff_srv_rate, dst_host_diff_srv_rate."
    ], image_path="research/figures/bwoa_convergence.png", image_w=4.8)

    # Populate Column 3 (Right): Evaluation, Conclusions & References
    c3 = tbl_cols.rows[0].cells[2]
    add_col_section(c3, "Evaluation & Latency", [
        "Single-sample inference latency benchmarked across hardware platforms vs 100ms SCADA ceiling:"
    ], image_path="research/figures/latency_comparison_barchart.png", image_w=4.8)

    add_col_section(c3, "Conclusions", [
        "0.76ms Edge Latency: 207x faster than baseline on Raspberry Pi 4B (1GB RAM), passing SCADA constraints.",
        "High Detection Fidelity: 70.56% multi-class accuracy, 96.89% benign precision, 89.04% DoS recall.",
        "Industrial ROI: 200x-300x return averting $50k-$500k/hr outages while protecting miner lives (SDG 8 & 9).",
        "Open-Source CLI: Package @mhiskall282/unesco-mine-sec-cli published on GitHub Packages."
    ])

    add_col_section(c3, "References", [
        "1. Alanazi, M., et al. (2022). SCADA vulnerabilities & attacks. Computers & Security, 125, 103028.",
        "2. Almomani, O., et al. (2025). SCADA intrusion detection in IIoT. Symmetry, 17(4), 480.",
        "3. Mirjalili, S., & Lewis, A. (2016). Whale optimization algorithm. Adv. Eng. Software, 95, 51-67.",
        "4. Minerals Commission of Ghana. (2024). Digital telemetry & cybersecurity compliance policy."
    ])

    output_docx = "research/poster_presentation.docx"
    doc.save(output_docx)
    print(f"Academic Landscape Poster (DOCX) saved successfully to {output_docx}!")

if __name__ == "__main__":
    create_academic_landscape_poster_pptx()
    create_academic_landscape_poster_docx()
