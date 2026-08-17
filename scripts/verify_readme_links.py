"""Verify all internal links in README.md point to existing files."""
import re
import os
import sys

with open("README.md", encoding="utf-8") as f:
    content = f.read()

# Find all markdown links: [text](path)
links = re.findall(r'\[([^\]]+)\]\(([^)]+)\)', content)
errors = []

for text, href in links:
    # Skip external URLs and anchors
    if href.startswith('http') or href.startswith('#'):
        continue
    # Normalize path
    path = href.split('#')[0]  # Remove anchor
    if path and not os.path.exists(path):
        errors.append(f"BROKEN: [{text}]({href}) -- file not found: {path}")

if errors:
    print(f"Found {len(errors)} broken internal links in README.md:")
    for e in errors:
        print(f"  {e}")
    sys.exit(1)
else:
    total = len([h for _, h in links if not h.startswith('http') and not h.startswith('#') and h])
    print(f"All {total} internal README links verified. PASS")
    sys.exit(0)
