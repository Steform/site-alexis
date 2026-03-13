#!/usr/bin/env python3
"""
Extract dominant colors from maquette.png for design tokens.
Run: python extract-colors.py
Output: hex values for primary, secondary, backgrounds.
"""
from pathlib import Path

try:
    from PIL import Image
    import collections
except ImportError:
    print("Install: pip install Pillow")
    exit(1)

path = Path(__file__).parent / "maquette.png"
if not path.exists():
    print(f"Not found: {path}")
    exit(1)

img = Image.open(path)
img = img.convert("RGB")
pixels = list(img.getdata())

sampled = pixels[::15]
counter = collections.Counter(sampled)
1
def to_hex(rgb):
    return f"#{rgb[0]:02x}{rgb[1]:02x}{rgb[2]:02x}"

# Categorize by luminance
def luminance(r, g, b):
    return 0.299 * r + 0.587 * g + 0.114 * b

blues = []
lights = []
darks = []

for rgb, count in counter.most_common(50):
    r, g, b = rgb
    lum = luminance(r, g, b)
    # Blue-ish: B > R and B > G
    if b > r and b > g and count > 100:
        blues.append((to_hex(rgb), rgb, count))
    elif lum > 240:
        lights.append((to_hex(rgb), rgb, count))
    elif lum < 30:
        darks.append((to_hex(rgb), rgb, count))

print("=== MAQUETTE COLOR EXTRACTION ===\n")
print("Primary (blue tones):")
for h, rgb, c in blues[:5]:
    print(f"  {h}  RGB{rgb}  (count: {c})")
print("\nLight (backgrounds):")
for h, rgb, c in lights[:3]:
    print(f"  {h}  RGB{rgb}")
print("\nDark (text):")
for h, rgb, c in darks[:3]:
    print(f"  {h}  RGB{rgb}")
