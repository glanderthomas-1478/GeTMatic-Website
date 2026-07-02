# Aktuelle Daten — Website getmatic

---

## Aktueller Stand

- Website ist online unter **getmatic.de**
- Laufende Optimierung — DE/EN Sprachumschalter ergänzt, Einsatzbereiche erweitert
- **2026-06-21:** Hero-Slider komplett überarbeitet (4 Slides, alle leichtgewichtige SVGs statt schwerer/veralteter GIFs), Impressum bekam Sprachumschalter + komplette EN-Übersetzung, Canonical-Tags ergänzt (Google zeigte fälschlich `glander-led.de` als Domain — Ursache: fehlendes Canonical-Tag)
- **2026-06-22:** Eigene Unterseiten für alle 6 Einsatzbereiche umgesetzt (Plan: `plans/2026-06-22-einsatzbereiche-unterseiten.md`, Status: Implementiert). 5 Branchenseiten (`einsatzbereich-*.html`) + eigene Produktseite `docucontrol.html` mit prominentem Autarkie-Block (kein Eingriff in Maschine/Kundennetzwerk, Recherche-Basis: DocuPi-3000-Konzept aus `claude-workspace-docupi`). Jede Anlagen-Karte (Bild + Text) und der Hero-Slide-2-Button verlinken auf die jeweilige Unterseite. Neuer CSS-Abschnitt `EINSATZBEREICH-DETAIL` in `style.css`.
- **2026-06-22, bestätigt behoben:** Hero-Slider-Bug war ein Re-Upload-Rückstand (alte Live-Dateien) — nach Upload der 6 slider-relevanten Dateien (`index.html`, `style.css`, `Systeme.svg`, `dokumentation.svg`, `medizin-sterilisation.svg`, `antriebstechnik.svg`) funktioniert der Slider live korrekt (auto + manuelle Pfeile).
- **2026-06-22, bestätigt behoben:** Nach dem ersten Upload der neuen Unterseiten wiederholte sich der Seiteninhalt endlos nach unten. Ursache: 4 Dateien (`einsatzbereich-transport.html`, `-papier.html`, `-lebensmittel.html`, `-medizin.html`) wurden beim FTP-Upload **8-fach aneinandergehängt** (vermutlich Resume-Modus statt Überschreiben) — bestätigt per `curl`-Abgleich Dateigröße live vs. lokal. Nach Löschen + saubererem Re-Upload (ohne Resume) behoben. **Lerneffekt:** Bei künftigen Uploads auf 1blu/FileZilla nicht „Fortsetzen", sondern Überschreiben/normalen Upload verwenden, sonst droht erneute Duplizierung.
- **2026-06-25:** Aufräumen `reference/Getmatic_website/` — 24 ungenutzte Alt-Assets gelöscht (~24 MB: alte GIFs/PNGs von 2019, Template-Reste in `images/` von 2012). Abgleich per Grep aller `*.html`+`style.css` gegen Dateibestand.
- **2026-06-25:** DocuControl-Unterseite (`docucontrol.html`) um Abschnitt „DocuControl in der Praxis" erweitert — 3 Screenshots (Live-Monitor, Chargenübersicht, Dateiverwaltung, Quelle: `reference/Screen_DcoCuntrol/`) mit CSS-Browser-Mockup-Frame (Titelleiste, Schatten, abgerundete Ecken) statt roher Screenshot-Kanten. Neue CSS-Klassen `.einsatz-screens`/`.einsatz-screen`/`.screen-frame` in `style.css`. DE/EN übersetzt. Live bestätigt (2026-06-26).
- **2026-06-26:** Live-Monitor-Screenshot durch animiertes TCP-Terminal ersetzt — grün auf schwarz, Monospace, Zeile für Zeile (620ms Takt), Rolling-Buffer (20 sichtbare Zeilen, ältere scrollen nach oben raus), Loop mit 4,5s Pause. Daten: echter Sterilisations-Chargenprotokoll (Autoklav, Steri-Nr. 12345). Neue CSS-Klassen `.tcp-screen`/`.tcp-terminal`/`.tcp-pre`/`.tcp-cursor`. Live bestätigt (2026-06-26).
- **2026-06-26:** SEO-Optimierung aller 7 HTML-Seiten — `getmatic`/`GeTMatic` in alle Keywords + Descriptions eingetragen, `DocuControl` in `docucontrol.html` und `einsatzbereich-medizin.html` verstärkt. `og:image` in `index.html` von gelöschter `images/pic01.gif` auf `getmatic_logo.png` korrigiert. JSON-LD Schema um `logo` und `image` ergänzt. `apple-touch-icon` hinzugefügt. **Noch hochzuladen:** alle 7 HTML-Dateien (inkl. index.html).
- **2026-07-02:** Ursache für „Google zeigt glander-led.de statt getmatic.de" gefunden: `glander-led.de` liegt im selben 1blu-Webspace (bestätigt per curl) — über HTTP liefert die Domain den vollen getmatic-Inhalt inkl. korrektem Canonical-Tag, über HTTPS dagegen die 1blu-Standard-Platzhalterseite (kein eigenes Zertifikat). Canonical-Tag allein reicht nicht, weil Google die Domain trotzdem crawlen/indexieren kann. **Fix:** `.htaccess` mit 301-Redirect (`glander-led.de` → `https://www.getmatic.de/`) angelegt in `reference/Getmatic_website/.htaccess`. **Noch offen:** Datei auf den Server hochladen (Pfad `www/getmatic/`) und zusätzlich im 1blu-Kundencenter prüfen, ob dort eine saubere Domain-Weiterleitung statt Alias auf denselben Webspace möglich ist.

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
  - `docucontrol.html` — eigene Produktseite für DocuControl (statt Branchenkarte 06 „Dokumentation"), mit Autarkie-Vorteile-Block + Screenshot-Sektion (2026-06-25)
  - `docucontrol-livemonitor.png`, `docucontrol-chargen.png`, `docucontrol-dateien.png` — Software-Screenshots für die Praxis-Sektion auf `docucontrol.html`

## Backup-Ordner

`Backup_Website/` enthält zwei ältere Versionen:
- `Getmatic_website_DE` — reine DE-Version
- `Getmatic_website_DE_EN` — Zwischenstand mit DE/EN

---

## Offene Aufgaben / nächste Schritte

- [x] Re-Upload nach Hero-Slider-Umbau — erledigt und live bestätigt (2026-06-22)
- [x] Re-Upload nach Einsatzbereiche-Unterseiten — erledigt und live bestätigt (2026-06-22), inkl. Behebung der 8-fachen Datei-Duplizierung bei 4 Unterseiten
- [x] Re-Upload nach DocuControl-Screenshot-Sektion (2026-06-25) — `docucontrol.html`, `style.css`, 3 PNGs — live bestätigt (2026-06-26)
- [ ] `.htaccess` (mit 301-Redirect glander-led.de → getmatic.de) hochladen nach `www/getmatic/` — Datei liegt bereit in `reference/Getmatic_website/.htaccess`
- [ ] Im 1blu-Kundencenter prüfen: ist `glander-led.de` als reine Domain-Weiterleitung eingerichtet oder als Alias auf denselben Webspace? Sauberer wäre eine echte Weiterleitung auf Domain-Ebene statt .htaccess-Redirect
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
