# Aktuelle Daten — Website getmatic

---

## Aktueller Stand

- Website ist online unter **getmatic.de**
- Laufende Optimierung — DE/EN Sprachumschalter ergänzt, Einsatzbereiche erweitert
- **2026-06-21:** Hero-Slider komplett überarbeitet (4 Slides, alle leichtgewichtige SVGs statt schwerer/veralteter GIFs), Impressum bekam Sprachumschalter + komplette EN-Übersetzung, Canonical-Tags ergänzt (Google zeigte fälschlich `glander-led.de` als Domain — Ursache: fehlendes Canonical-Tag)
- **2026-06-22:** Eigene Unterseiten für alle 6 Einsatzbereiche umgesetzt (Plan: `plans/2026-06-22-einsatzbereiche-unterseiten.md`, Status: Implementiert). 5 Branchenseiten (`einsatzbereich-*.html`) + eigene Produktseite `docucontrol.html` mit prominentem Autarkie-Block (kein Eingriff in Maschine/Kundennetzwerk, Recherche-Basis: DocuPi-3000-Konzept aus `claude-workspace-docupi`). Jede Anlagen-Karte (Bild + Text) und der Hero-Slide-2-Button verlinken auf die jeweilige Unterseite. Neuer CSS-Abschnitt `EINSATZBEREICH-DETAIL` in `style.css`.
- **2026-06-22, bestätigt behoben:** Hero-Slider-Bug war ein Re-Upload-Rückstand (alte Live-Dateien) — nach Upload der 6 slider-relevanten Dateien (`index.html`, `style.css`, `Systeme.svg`, `dokumentation.svg`, `medizin-sterilisation.svg`, `antriebstechnik.svg`) funktioniert der Slider live korrekt (auto + manuelle Pfeile).
- **2026-06-22, bestätigt behoben:** Nach dem ersten Upload der neuen Unterseiten wiederholte sich der Seiteninhalt endlos nach unten. Ursache: 4 Dateien (`einsatzbereich-transport.html`, `-papier.html`, `-lebensmittel.html`, `-medizin.html`) wurden beim FTP-Upload **8-fach aneinandergehängt** (vermutlich Resume-Modus statt Überschreiben) — bestätigt per `curl`-Abgleich Dateigröße live vs. lokal. Nach Löschen + saubererem Re-Upload (ohne Resume) behoben. **Lerneffekt:** Bei künftigen Uploads auf 1blu/FileZilla nicht „Fortsetzen", sondern Überschreiben/normalen Upload verwenden, sonst droht erneute Duplizierung.

---

## Tech-Stack

- **Typ:** Statische Website — reines HTML / CSS / JavaScript (kein Framework, kein CMS)
- **Hosting:** 1blu — Pfad auf Server: `www/getmatic/`
- **Deployment:** FTP — 1blu Online-Manager oder FileZilla (empfohlen für Bulk-Upload)
- **Fonts:** Lokal eingebunden (kein Google Fonts mehr — DSGVO-konform)
  - `_barlow_condensed_800.ttf` — Barlow Condensed, alle Gewichte
  - `_barlow_400.ttf` — Barlow, alle Gewichte
- **Brand-Farben** (aus `style.css`):
  - Teal: `#209D9D` / Light: `#5AD7D7` / Dark: `#0F8C8C`
  - Header-Grau: `#4A4A4A`
  - Hintergrund: `#E0DCDC`
  - Text: `#5C5B5B`

---

## Struktur `reference/Getmatic_website/`

Aktuelle Arbeitsdateien — diese werden auf den Server hochgeladen:
- `index.html` — Hauptseite (DE/EN, 6 Einsatzbereiche, 4 Hero-Slides)
- `style.css` — Alle Styles inkl. Sprachumschalter, lokale Fonts
- `impressum.html` — jetzt mit Sprachumschalter + vollständiger EN-Übersetzung + Canonical-Tag
- `Systeme.svg` — Hero-Slide 1 (Steuerungssysteme)
- `dokumentation.svg` — Hero-Slide 2 (DocuControl) + Anlagen-Kachel
- `medizin-sterilisation.svg` — NEU (2026-06-21): Hero-Slide 3 (Medizin & Sterilisation, dichter/dunkler Stil)
- `antriebstechnik.svg` — NEU (2026-06-21): Hero-Slide 4 (SPS Step7/TIA Portal + Antriebstechnik)
- `sterilisation.svg` — weiterhin für die Anlagen-Kachel (nicht mehr im Hero)
- `transportanlage.svg`, `papierindustrie.svg`, `kartonverpackung.svg`, `getraenkeverpackung.svg` — Anlagen-Kacheln
- `_barlow_condensed_800.ttf`, `_barlow_400.ttf`
- `getmatic_logo.png`, `getmatic_logo_transparent.png` (auch Favicon)
- Entfernt aus dem Hero: `pic01.gif`, `motor.gif` (2,9 MB!), `visutech.gif` — durch SVGs ersetzt (Performance + Optik)
- **NEU (2026-06-22):** Unterseiten zu den Einsatzbereichen, DE/EN, eigene SEO-Metadaten pro Seite:
  - `einsatzbereich-transport.html` — Transport & Verpackung
  - `einsatzbereich-papier.html` — Papierindustrie
  - `einsatzbereich-lebensmittel.html` — Lebensmittelindustrie
  - `einsatzbereich-getraenke.html` — Getränkeverpackung
  - `einsatzbereich-medizin.html` — Medizin & Sterilisation (verlinkt zusätzlich auf `docucontrol.html`)
  - `docucontrol.html` — eigene Produktseite für DocuControl (statt Branchenkarte 06 „Dokumentation"), mit Autarkie-Vorteile-Block

## Backup-Ordner

`Backup_Website/` enthält zwei ältere Versionen:
- `Getmatic_website_DE` — reine DE-Version
- `Getmatic_website_DE_EN` — Zwischenstand mit DE/EN

---

## Offene Aufgaben / nächste Schritte

- [x] Re-Upload nach Hero-Slider-Umbau — erledigt und live bestätigt (2026-06-22)
- [x] Re-Upload nach Einsatzbereiche-Unterseiten — erledigt und live bestätigt (2026-06-22), inkl. Behebung der 8-fachen Datei-Duplizierung bei 4 Unterseiten
- [ ] Verifizieren, ob `glander-led.de` dem User gehört → ggf. 301-Redirect auf `getmatic.de` einrichten (Canonical-Tag allein reicht u.U. nicht)
- [ ] **`getmatic-website`-Skill erstellen** — Plan liegt vor: `plans/2026-06-02-website-creator-skills-aufbauen.md`
- [ ] Fehlende Barlow-Font-Gewichte beschaffen (Condensed 400/600/700, Regular 300/500) für sauberes Rendering
- [ ] Security Headers auf Server konfigurieren (`X-Frame-Options`, `Content-Security-Policy`)

---

## Deployment-Hinweise

- 1blu Online-Manager: SVG-Dateien **einzeln** hochladen (Bulk-Upload überspringt Dateien stillschweigend)
- Für mehrere Dateien: **FileZilla** verwenden
- Linux-Server: Groß-/Kleinschreibung beachten (`Systeme.svg` → großes S)

---

_Wird laufend aktualisiert — veraltete Einträge entfernen._
