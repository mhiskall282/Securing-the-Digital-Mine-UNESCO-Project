"""Verify all internal links in README.md point to existing files."""
import re
import os
import sys
import urllib.parse

with open("README.md", encoding="utf-8") as f:
    content = f.read()

# Match markdown links [text](path) allowing balanced or escaped parentheses
# We can find matches using regex or token scanning
pattern = r'\[([^\]]+)\]\(((?:[^()\s]|\([^\s()]*\))+)\)'
links = re.findall(pattern, content)
errors = []

for text, href in links:
    # Skip external URLs and anchors
    if href.startswith('http') or href.startswith('#') or href.startswith('mailto:'):
        continue
    # Clean anchor and decode URL
    path = href.split('#')[0]
    path = urllib.parse.unquote(path)
    if path and not os.path.exists(path):
        errors.append(f"BROKEN: [{text}]({href}) -- file not found: {path}")

if errors:
    print(f"Found {len(errors)} broken internal links in README.md:")
    for e in errors:
        print(f"  {e}")
    sys.exit(1)
else:
    total = len([h for _, h in links if not h.startswith('http') and not h.startswith('#') and not h.startswith('mailto:') and h])
    print(f"All {total} internal README links verified. PASS")
    sys.exit(0)
