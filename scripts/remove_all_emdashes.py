"""Scan all documents in research/ and scripts/ and replace all em-dashes with standard academic punctuation."""

import os
import re
import docx

def purge_emdashes_from_text(text: str) -> str:
    # Replace em-dashes (\u2014) with appropriate punctuation
    # Pattern: word—word -> word: word or word, word depending on context
    # Let's replace ' — ' or '—' with ' - ' or ', '
    text = text.replace("—", " - ")
    # Replace double or triple dashes used inline in text (not standalone ---)
    # Standalone --- lines should be preserved for markdown horizontal rules
    lines = text.split("\n")
    cleaned_lines = []
    for line in lines:
        if line.strip() == "---":
            cleaned_lines.append(line)
        else:
            # Replace inline '---' with ' - '
            cleaned_line = line.replace("---", " - ")
            cleaned_lines.append(cleaned_line)
    
    res = "\n".join(cleaned_lines)
    # clean up any accidental double spaces created
    res = re.sub(r' +', ' ', res)
    res = res.replace(" - - ", " - ")
    return res

def process_file(file_path: str):
    if not os.path.exists(file_path):
        return
    with open(file_path, "r", encoding="utf-8") as f:
        content = f.read()
    
    orig_em = content.count("—")
    cleaned = purge_emdashes_from_text(content)
    
    with open(file_path, "w", encoding="utf-8") as f:
        f.write(cleaned)
    print(f"Processed {file_path}: removed {orig_em} em-dashes.")

def process_docx(docx_path: str):
    if not os.path.exists(docx_path):
        return
    doc = docx.Document(docx_path)
    em_count = 0
    for p in doc.paragraphs:
        if "—" in p.text:
            em_count += p.text.count("—")
            p.text = p.text.replace("—", " - ")
    for t in doc.tables:
        for row in t.rows:
            for cell in row.cells:
                if "—" in cell.text:
                    em_count += cell.text.count("—")
                    cell.text = cell.text.replace("—", " - ")
    doc.save(docx_path)
    print(f"Processed {docx_path}: removed {em_count} em-dashes.")

if __name__ == "__main__":
    text_files = [
        "research/ieee_paper.tex",
        "research/IEEE_Paper_Submission_Manuscript.md",
        "research/conference_presentation.md",
        "scratch/full_paper_extracted.md"
    ]
    for tf in text_files:
        process_file(tf)
    process_docx("research/full_research_paper.docx")
