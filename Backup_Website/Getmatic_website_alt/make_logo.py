"""
Erstellt das GeTMatic-Logo als PNG.
Layout: "GeTMatic | Automatisierungstechnik" auf #4A4A4A-Hintergrund.
"""

import urllib.request
import os
import sys
from PIL import Image, ImageDraw, ImageFont

# --- Schrift herunterladen (Barlow Condensed ExtraBold 800) ---
FONT_URL = "https://fonts.gstatic.com/s/barlowcondensed/v12/HTxwL3I-JCGChYJ8VI-L6OO_au7B6xTT3w.ttf"
FONT_CLAIM_URL = "https://fonts.gstatic.com/s/barlow/v12/7cHqv4kjgoGqM7E3b8s8yn4.ttf"
FONT_PATH = os.path.join(os.path.dirname(__file__), "_barlow_condensed_800.ttf")
FONT_CLAIM_PATH = os.path.join(os.path.dirname(__file__), "_barlow_400.ttf")

def download_font(url, path):
    if not os.path.exists(path):
        print(f"Lade Schrift herunter: {url}")
        urllib.request.urlretrieve(url, path)
        print("  OK")
    else:
        print(f"Schrift vorhanden: {path}")

download_font(FONT_URL, FONT_PATH)
download_font(FONT_CLAIM_URL, FONT_CLAIM_PATH)

# --- Farben ---
BG         = (74,  74,  74)          # #4A4A4A  Header-Hintergrund
WHITE      = (255, 255, 255, 255)
TEAL       = (90,  215, 215, 255)    # #5AD7D7  GeT-Farbe
CLAIM_COL  = (255, 255, 255, 140)    # rgba(255,255,255,0.55)
SEP_COL    = (255, 255, 255, 51)     # rgba(255,255,255,0.2)

# --- Skalierung (2× für Schärfe) ---
SCALE      = 2
FONT_SIZE  = int(2 * 16 * SCALE)     # 2rem ≈ 32px → ×2 = 64px
CLAIM_SIZE = int(0.65 * 16 * SCALE)  # 0.65rem ≈ 10.4px → ×2 ≈ 21px
PAD_X      = 32 * SCALE
PAD_Y      = 20 * SCALE
GAP        = 10 * SCALE              # gap zwischen logo-word und logo-claim
SEP_PAD    = 10 * SCALE              # padding-left des Claims (= border-left spacing)

fnt_logo  = ImageFont.truetype(FONT_PATH,       FONT_SIZE)
fnt_claim = ImageFont.truetype(FONT_CLAIM_PATH, CLAIM_SIZE)

# --- Textmaße bestimmen ---
dummy = Image.new("RGBA", (1, 1))
d = ImageDraw.Draw(dummy)

# "GeT" und "Matic" werden inline gezeichnet (kein Zeilenumbruch)
bb_get   = d.textbbox((0, 0), "GeT",   font=fnt_logo,  language="de")
bb_matic = d.textbbox((0, 0), "Matic", font=fnt_logo,  language="de")
bb_claim = d.textbbox((0, 0), "AUTOMATISIERUNGSTECHNIK", font=fnt_claim, language="de")

w_get   = bb_get[2]   - bb_get[0]
w_matic = bb_matic[2] - bb_matic[0]
h_logo  = max(bb_get[3] - bb_get[1], bb_matic[3] - bb_matic[1])

w_claim = bb_claim[2] - bb_claim[0]
h_claim = bb_claim[3] - bb_claim[1]

# Separator: 1px Linie, volle Logo-Höhe
SEP_W = 1 * SCALE

# Gesamtbreite: PAD + GeT + Matic + GAP + SEP_PAD + SEP + SEP_PAD + claim + PAD
total_w = PAD_X + w_get + w_matic + GAP + SEP_PAD + SEP_W + SEP_PAD + w_claim + PAD_X
total_h = PAD_Y + h_logo + PAD_Y

img = Image.new("RGBA", (total_w, total_h), BG + (255,))
d   = ImageDraw.Draw(img)

# Vertikale Mittelpositionen (baseline-Ausrichtung → gleiche Baseline)
y_logo  = PAD_Y
# Claim vertikal zentriert zur Logo-Textzeile
y_claim = PAD_Y + (h_logo - h_claim) // 2

x = PAD_X

# "GeT" in Teal
d.text((x, y_logo), "GeT", font=fnt_logo, fill=TEAL)
x += w_get

# "Matic" in Weiß (kein Leerzeichen, direkt angehängt)
d.text((x, y_logo), "Matic", font=fnt_logo, fill=WHITE)
x += w_matic + GAP

# Separator-Linie
d.line([(x + SEP_PAD, PAD_Y),
        (x + SEP_PAD, PAD_Y + h_logo)],
       fill=SEP_COL, width=SEP_W)
x += SEP_PAD + SEP_W + SEP_PAD

# "AUTOMATISIERUNGSTECHNIK" in gedämpftem Weiß, zentriert
d.text((x, y_claim), "AUTOMATISIERUNGSTECHNIK", font=fnt_claim, fill=CLAIM_COL)

# Auf halbe Größe skalieren → scharfe Ausgabe (Retina-Downscale)
out_w = total_w // SCALE
out_h = total_h // SCALE
final = img.resize((out_w, out_h), Image.LANCZOS).convert("RGBA")

out_path = os.path.join(os.path.dirname(__file__), "getmatic_logo.png")
final.save(out_path, "PNG")
print(f"\nLogo gespeichert: {out_path}  ({out_w}×{out_h} px)")
