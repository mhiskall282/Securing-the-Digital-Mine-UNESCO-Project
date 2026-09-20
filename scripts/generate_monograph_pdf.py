"""Generate a complete PDF for the full research monograph."""

import os
from reportlab.lib.pagesizes import letter
from reportlab.lib import colors
from reportlab.lib.styles import getSampleStyleSheet, ParagraphStyle
from reportlab.platypus import (
    SimpleDocTemplate, Paragraph, Spacer, Table, TableStyle, PageBreak, HRFlowable
)
from reportlab.pdfgen import canvas

class MonographNumberedCanvas(canvas.Canvas):
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

    def draw_page_decorations(self, total_pages):
        self.saveState()
        self.setFont("Helvetica", 8)
        self.setFillColor(colors.HexColor("#666666"))
        
        # Header (pages > 1)
        if self._pageNumber > 1:
            self.drawString(54, 750, "UNESCO RUSSIAN-AFRICAN FORUM  |  DESIGN SCIENCE RESEARCH MONOGRAPH")
            self.setStrokeColor(colors.HexColor("#DDDDDD"))
            self.setLineWidth(0.5)
            self.line(54, 744, 558, 744)

        # Footer
        footer_text = f"Securing the Digital Mine: Full Research Monograph   |   Page {self._pageNumber} of {total_pages}"
        self.drawRightString(558, 36, footer_text)
        self.setStrokeColor(colors.HexColor("#DDDDDD"))
        self.setLineWidth(0.5)
        self.line(54, 46, 558, 46)
        
        self.restoreState()


def build_monograph_pdf(md_path: str, output_path: str):
    os.makedirs(os.path.dirname(output_path), exist_ok=True)
    doc = SimpleDocTemplate(
        output_path,
        pagesize=letter,
        leftMargin=54,
        rightMargin=54,
        topMargin=60,
        bottomMargin=56
    )

    styles = getSampleStyleSheet()
    c_navy = colors.HexColor("#0B2545")
    c_blue = colors.HexColor("#134074")
    c_dark = colors.HexColor("#222222")

    styles.add(ParagraphStyle('MonoTitle', fontName='Helvetica-Bold', fontSize=18, leading=22, textColor=c_navy, alignment=1, spaceAfter=10))
    styles.add(ParagraphStyle('MonoSubtitle', fontName='Helvetica', fontSize=10, leading=14, textColor=c_blue, alignment=1, spaceAfter=14))
    styles.add(ParagraphStyle('ChapterHead', fontName='Helvetica-Bold', fontSize=13, leading=17, textColor=c_navy, spaceBefore=16, spaceAfter=6, keepWithNext=True))
    styles.add(ParagraphStyle('SubHead', fontName='Helvetica-Bold', fontSize=10.5, leading=14, textColor=c_blue, spaceBefore=10, spaceAfter=4, keepWithNext=True))
    styles.add(ParagraphStyle('MonoBody', fontName='Helvetica', fontSize=9, leading=13, textColor=c_dark, spaceAfter=6, alignment=4))
    styles.add(ParagraphStyle('MonoBullet', fontName='Helvetica', fontSize=9, leading=13, textColor=c_dark, leftIndent=14, firstLineIndent=-10, spaceAfter=4))

    story = []

    with open(md_path, 'r', encoding='utf-8') as f:
        text = f.read()

    paragraphs = text.split('\n\n')

    # First paragraph is title
    if paragraphs:
        story.append(Paragraph(paragraphs[0].strip(), styles['MonoTitle']))
        story.append(HRFlowable(width="100%", thickness=1, color=c_navy, spaceBefore=0, spaceAfter=10))

    for p in paragraphs[1:]:
        p_str = p.strip()
        if not p_str:
            continue
        
        # Check headings
        if p_str.startswith("CHAPTER") or p_str.startswith("APPENDICES"):
            story.append(PageBreak())
            story.append(Paragraph(f"<b>{p_str}</b>", styles['ChapterHead']))
            story.append(HRFlowable(width="100%", thickness=0.5, color=c_blue, spaceBefore=0, spaceAfter=8))
        elif p_str.startswith("1.") or p_str.startswith("2.") or p_str.startswith("3.") or p_str.startswith("4.") or p_str.startswith("5.") or p_str.startswith("6.") or p_str.startswith("7.") or p_str.startswith("APPENDIX"):
            story.append(Paragraph(f"<b>{p_str}</b>", styles['SubHead']))
        elif p_str.startswith("• ") or p_str.startswith("* ") or (len(p_str) > 2 and p_str[0].isdigit() and p_str[1] == '.' and p_str[2] == ' '):
            story.append(Paragraph(p_str, styles['MonoBullet']))
        else:
            story.append(Paragraph(p_str, styles['MonoBody']))

    doc.build(story, canvasmaker=MonographNumberedCanvas)
    print(f"Generated monograph PDF at: {output_path}")

if __name__ == "__main__":
    md_file = os.path.abspath("scratch/full_paper_extracted.md")
    out_pdf = os.path.abspath("research/Full_Research_Monograph_Digital_Mine.pdf")
    build_monograph_pdf(md_file, out_pdf)
