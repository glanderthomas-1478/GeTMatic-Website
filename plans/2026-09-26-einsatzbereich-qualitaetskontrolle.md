# Plan: Neuer Einsatzbereich „Qualitätskontrolle“ (Kamera, Bildauswertung, Ausschleusung)

**Erstellt:** 2026-09-26
**Status:** Implementiert (2026-09-26) — Nachtrag: Produktname „VisionControl“, Seite als `visioncontrol.html` (analog `docucontrol.html`)
**Anforderung:** Neuer Reiter unter „Anlagen“ für kamerabasierte Qualitätskontrolle mit Auswertung und Ausschleusung — branchenübergreifend, eigene Unterseite, SVG-Grafik.

---

## Überblick

### Was dieser Plan erreicht

Die Website bekommt einen fünften Einsatzbereich „Qualitätskontrolle“ mit eigener Unterseite `visioncontrol.html` (DE/EN), Eintrag im „Anlagen“-Dropdown auf allen Seiten, Kachel 05 auf der Startseite, Sitemap-Eintrag und einer neuen schematischen SVG-Grafik (Kamera über Förderband → Auswertung → Ausschleuser).

### Warum das wichtig ist

Kamerabasierte Prüfung mit automatischer Ausschleusung ist ein eigenständiges, branchenübergreifendes Leistungsangebot (Industrie, Pharma, Medizin) und passt zur Strategie „Leistungsangebot klar kommunizieren“. Es knüpft an die bereits auf `docucontrol.html` erwähnte „Bildauswertung für Prozesse“ an und macht diese Kompetenz sichtbar und auffindbar (SEO).

---

## Aktueller Zustand

### Relevante bestehende Struktur

- `reference/Getmatic_website/einsatzbereich-papier.html` — Vorlage: Back-Link, `.einsatz-hero` (SVG via `--img`), `.einsatz-header` (Label + H1), `.einsatz-content` (3 Absätze), `.einsatz-vorteile` (4 Kacheln), `.einsatz-cta`, Inline-i18n-Objekt `de`/`en` am Seitenende, Präfix-Keys (`pap-*`)
- `index.html` — Anlagen-Sektion `#anlagen`, 4 `<article class="anlagen-item">` (01 Getränke `anl4-*`, 02 Doku `anl6-*`, 03 Medizin `anl5-*`, 04 Papier `anl2-*`); Zickzack per `.anlagen-item:nth-child(even)` in `style.css` (automatisch, kein Extra-CSS). Stats-Kachel `data-count="4"` „Einsatzbereiche“ (Z. 194)
- Dropdown `.nav-dropdown-menu` mit Keys `nav-drop-*` auf 7 Seiten: `index.html`, `docucontrol.html`, `einsatzbereich-getraenke.html`, `-medizin.html`, `-papier.html`, `impressum.html`, `datenschutz.html` (Link „Alle“: auf index `#anlagen`, sonst `index.html#anlagen`)
- `sitemap.xml` — 7 URLs
- Kachel-SVGs: `viewBox="0 0 800 400"`, dunkler Hallen-Hintergrund mit Verläufen, Teal-Akzente (`#26a69a`/`#00796b`), `role="img"` + `aria-label`
- `docucontrol.html` — Kachel `doc-proc3` „Bildauswertung für Prozesse“ (allgemein gehalten)

### Lücken oder Probleme, die adressiert werden

- Qualitätskontrolle per Kamera ist nirgends als Leistung dargestellt
- Keine Unterseite/Keywords für Suchbegriffe wie „Kamera Qualitätskontrolle“, „Bildverarbeitung Ausschleusung“

---

## Vorgeschlagene Änderungen

### Zusammenfassung der Änderungen

- Neue Unterseite `visioncontrol.html` nach Muster Papier-Seite, Präfix `qk-*`
- Neue SVG `qualitaetskontrolle.svg`
- Dropdown-Eintrag „Qualitätskontrolle“ auf allen 7 Seiten + neue Seite (Key `nav-drop-qualitaet`)
- Kachel 05 auf `index.html` (Keys `anl7-*`), Stats „Einsatzbereiche“ 4 → 5
- `sitemap.xml` um neue URL ergänzt
- Querverweis-Box auf der neuen Seite zu `docucontrol.html` (Dokumentation der Prüfergebnisse)
- `context/current-data.md` aktualisieren

### Neue Dateien erstellen

| Dateipfad | Zweck |
| --- | --- |
| `reference/Getmatic_website/visioncontrol.html` | Unterseite Qualitätskontrolle, DE/EN, SEO-Meta |
| `reference/Getmatic_website/qualitaetskontrolle.svg` | Schematische Grafik für Hero der Unterseite + Startseiten-Kachel |

### Zu ändernde Dateien

| Dateipfad | Änderungen |
| --- | --- |
| `reference/Getmatic_website/index.html` | Kachel 05 nach Papier, Dropdown-Eintrag, i18n DE/EN (`anl7-*`, `nav-drop-qualitaet`), Stats `data-count` 4→5 |
| `docucontrol.html`, `einsatzbereich-getraenke.html`, `einsatzbereich-medizin.html`, `einsatzbereich-papier.html`, `impressum.html`, `datenschutz.html` | Dropdown-Eintrag + i18n-Key DE/EN |
| `reference/Getmatic_website/sitemap.xml` | Neue URL, priority 0.6, monthly |
| `context/current-data.md` | Eintrag + Upload-Liste |

### Zu löschende Dateien (falls vorhanden)

Keine.

---

## Design-Entscheidungen

### Getroffene Schlüsselentscheidungen

1. **Dateiname `visioncontrol.html`**: konsistent mit `einsatzbereich-*`-Schema, ohne Umlaut (URL-sicher).
2. **i18n-Präfix `qk-*`, Startseiten-Keys `anl7-*`**: `anl1`/`anl3` gehörten zu gelöschten Rubriken (Transport/Lebensmittel) — neue Nummer vermeidet Verwechslung mit Altständen.
3. **Position im Dropdown/Kachel: an letzter Stelle (05)**: bestehende Reihenfolge (Getränke als Haupteinnahmequelle vorn) bleibt unangetastet.
4. **Branchenübergreifende Formulierung**: laut Nutzer; Beispiele allgemein (Verpackungen, Etiketten/Aufdrucke, Maßhaltigkeit, Vollständigkeit) statt konkreter Kundenprojekte.
5. **Keine erfundenen Zahlen, Kamera-Hersteller oder Referenzen**: nur Funktionsbeschreibung (Erfassen → Auswerten → Ausschleusen → Dokumentieren).
6. **Querverweis zu DocuControl**: bestehende `.einsatz-crosslink`/`med-crosslink`-Optik wiederverwenden (wie auf Medizin-Seite), kein neues CSS.
7. **Kein neues CSS**: alle Bausteine existieren bereits.

### Betrachtete Alternativen

- Nur Kachel + Dropdown ohne Unterseite — vom Nutzer verworfen.
- Als Unterabschnitt auf `einsatzbereich-getraenke.html` — verworfen, da branchenübergreifend.
- Foto/Video statt SVG — kein Material vorhanden, SVG gewählt (Nutzerentscheid).

### Offene Fragen (falls vorhanden)

Geklärt (2026-09-26): 1) Titel „Qualitätskontrolle“ ✔ 2) keine konkrete Technik nennen — neutral „Industriekameras“ + „Anbindung an die SPS“ ✔ 3) Stats „Einsatzbereiche“ 4 → 5 ✔

---

## Schritt-für-Schritt-Aufgaben

### Schritt 1: SVG-Grafik erstellen

Schematische Illustration im Stil der bestehenden Kachel-SVGs.

**Aktionen:**

- `viewBox="0 0 800 400"`, `role="img"`, `aria-label="Kamerabasierte Qualitätskontrolle mit Ausschleusung"`
- Dunkler Hallen-Hintergrund (Verlauf, kühles Blau-Grau passend zur neuen Palette `#1C2426`/`#101415`), Teal-Akzente `#209D9D`/`#5AD7D7`
- Motiv: Förderband von links nach rechts mit 4–5 Produkten (neutrale Boxen), Kamera an Portal über dem Band mit Sichtkegel (halbtransparent Teal), ein Produkt rot markiert (fehlerhaft), dahinter pneumatischer Ausschleuser/Weiche, der das rote Teil seitlich in einen Behälter schiebt; kleines Monitor-/Auswertungsfenster mit Häkchen/Kreuz-Symbolen
- Kein Text in der Grafik (sprachneutral)
- Dateigröße < 25 KB

**Betroffene Dateien:**

- `reference/Getmatic_website/qualitaetskontrolle.svg`

---

### Schritt 2: Unterseite erstellen

`einsatzbereich-papier.html` kopieren und anpassen.

**Aktionen:**

- Head: `description`, `keywords` (getmatic, GeTMatic, Qualitätskontrolle, Kamera-Inspektion, Bildverarbeitung, Bildauswertung, Ausschleusung, Inline-Prüfung, SPS, TIA Portal, Krefeld), `canonical` → `https://www.getmatic.de/visioncontrol.html`, OG-Tags, `<title>Qualitätskontrolle – GeTMatic Automatisierungstechnik</title>`, `og:image` `qualitaetskontrolle.svg`
- Hero: `--img: url('qualitaetskontrolle.svg')`
- Label „Einsatzbereich“, H1 „Qualitätskontrolle“
- 3 Absätze (`qk-p1..3`), Inhalt:
  - p1: Problem — fehlerhafte Teile früh erkennen, bevor sie weiterverarbeitet/ausgeliefert werden; manuelle Sichtprüfung stößt bei hohen Taktraten an Grenzen
  - p2: Lösung — Industriekameras erfassen jedes Teil im laufenden Prozess, Bildauswertung prüft Merkmale (z. B. Vollständigkeit, Position, Maßhaltigkeit, Aufdruck/Etikett, Beschädigungen), Ergebnis geht direkt an die SPS
  - p3: Ausschleusung — fehlerhafte Teile werden automatisch und taktgenau aus dem Produktstrom ausgeschleust, ohne die Linie anzuhalten; Prüfergebnisse nachvollziehbar erfasst
- 4 Vorteil-Kacheln (`qk-v1..4-h/-p`): Kamera-Inspektion / Bildauswertung / Automatische Ausschleusung / Nachvollziehbare Ergebnisse
- Crosslink-Box zu `docucontrol.html` (Stil wie `med-crosslink` auf Medizin-Seite): „Prüfergebnisse und Bilder lassen sich mit DocuControl lückenlos dokumentieren.“
- CTA „Projekt anfragen“ (`qk-cta`)
- i18n-Objekt: gemeinsame Keys (Nav, Footer, `einsatz-back`) von Papier-Seite übernehmen, `pap-*`/`tra-*` durch `qk-*` ersetzen, vollständige EN-Übersetzung
- Dropdown inkl. neuem Eintrag (siehe Schritt 3)

**Betroffene Dateien:**

- `reference/Getmatic_website/visioncontrol.html`

---

### Schritt 3: Dropdown auf allen Seiten ergänzen

**Aktionen:**

- In allen 8 Seiten nach `<li><a href="einsatzbereich-papier.html" data-i18n="nav-drop-papier">Papierindustrie</a></li>` einfügen:
  `<li><a href="visioncontrol.html" data-i18n="nav-drop-qualitaet">Qualitätskontrolle</a></li>`
- i18n DE: `'nav-drop-qualitaet': 'Qualitätskontrolle',` nach `nav-drop-papier`; EN: `'Quality Inspection'`
- Einrückung/Ausrichtung der Key-Spalten wie Bestand

**Betroffene Dateien:**

- `index.html`, `docucontrol.html`, `einsatzbereich-getraenke.html`, `einsatzbereich-medizin.html`, `einsatzbereich-papier.html`, `impressum.html`, `datenschutz.html`, `visioncontrol.html`

---

### Schritt 4: Startseite — Kachel 05 + Stats

**Aktionen:**

- Nach Papier-Artikel neues `<article class="anlagen-item animate-in" style="--d:.5s">` mit `anlagen-img` → `qualitaetskontrolle.svg`, `aria-label`, `anlagen-num` „05“, `anl7-h` „Qualitätskontrolle“, `anl7-p` (ca. 2 Sätze: Kamera-Inspektion im laufenden Prozess, automatische Auswertung und Ausschleusung fehlerhafter Teile — branchenübergreifend), `anl7-link`
- i18n DE/EN für `anl7-h/-p/-link`
- Stats `data-count="4"` → `"5"` (falls Offene Frage 3 bestätigt)
- Zickzack greift automatisch (5. Element = ungerade = Bild links)

**Betroffene Dateien:**

- `reference/Getmatic_website/index.html`

---

### Schritt 5: Sitemap

**Aktionen:**

- Nach Getränke-Eintrag: `<url><loc>https://www.getmatic.de/visioncontrol.html</loc><changefreq>monthly</changefreq><priority>0.6</priority></url>` (gleiche Einrückung)

**Betroffene Dateien:**

- `reference/Getmatic_website/sitemap.xml`

---

### Schritt 6: Testen

**Aktionen:**

- Lokaler Server (`python -m http.server 8765` in `reference/Getmatic_website/`), Playwright:
  - Alle 8 Seiten: Dropdown enthält 5 Bereiche, Link auf neue Seite funktioniert (200)
  - Neue Seite Desktop 1440×900 + Mobile 390×844: kein horizontaler Overflow, keine Konsolenfehler, Hero-SVG sichtbar
  - DE/EN-Umschaltung auf neuer Seite und Startseite (alle `qk-*`/`anl7-*`/`nav-drop-qualitaet` übersetzt, keine leeren Elemente)
  - Startseite: 5 Kacheln, Zickzack korrekt, Stats zählt bis 5
- SVG einzeln im Browser öffnen, optisch prüfen (Screenshot)
- `sitemap.xml` well-formed (Python `xml.etree` parse)

---

### Schritt 7: Dokumentation

**Aktionen:**

- `context/current-data.md`: Eintrag mit Datum, neue Dateien, Keys, Upload-Liste (`visioncontrol.html`, `qualitaetskontrolle.svg`, `sitemap.xml`, `index.html` + alle 6 weiteren HTML-Seiten)
- Struktur-Abschnitt „Struktur `reference/Getmatic_website/`“ um neue Seite/SVG ergänzen
- Plan-Status auf „Implementiert“ setzen

**Betroffene Dateien:**

- `context/current-data.md`, dieser Plan

---

## Verbindungen & Abhängigkeiten

### Dateien, die diesen Bereich referenzieren

- Alle 8 HTML-Seiten (Dropdown), `index.html` (Kachel, Stats), `sitemap.xml`
- `docucontrol.html` (thematisch „Bildauswertung für Prozesse“ — keine Änderung nötig, optional später Rück-Link)

### Nötige Updates für Konsistenz

- `context/current-data.md` (Stand + Upload-Liste)
- `CLAUDE.md` — keine Änderung nötig (Struktur bleibt gleich)

### Auswirkungen auf bestehende Workflows

- Größerer Upload-Batch (alle HTML-Seiten wegen Dropdown) — ohnehin Teil des anstehenden Gesamt-Uploads (erst bearingcontrol.de, dann getmatic.de)
- Nach Live-Gang: neue URL in Google Search Console zur Indexierung einreichen

---

## Validierungs-Checkliste

- [ ] `qualitaetskontrolle.svg` rendert korrekt, < 25 KB, kein Text
- [ ] `visioncontrol.html` lädt ohne Konsolenfehler, Desktop + Mobile ohne Overflow
- [ ] DE/EN vollständig auf neuer Seite und Startseite
- [ ] Dropdown auf allen 8 Seiten mit 5 Einträgen, alle Links 200
- [ ] Startseite zeigt Kachel 05 im Zickzack, Stats = 5
- [ ] `sitemap.xml` valide, 8 URLs
- [ ] Keine erfundenen Zahlen/Referenzen im Text
- [ ] `context/current-data.md` aktualisiert

---

## Erfolgskriterien

1. Neuer Bereich „Qualitätskontrolle“ ist von jeder Seite über das Anlagen-Dropdown und von der Startseite per Kachel erreichbar.
2. Unterseite erklärt Erfassen → Auswerten → Ausschleusen verständlich in DE und EN, im Look der übrigen Einsatzbereich-Seiten.
3. Playwright-Tests auf allen Seiten ohne Fehler/Overflow.

---

## Notizen

- Sobald echtes Foto-/Videomaterial einer Prüfstation existiert, kann es analog zur Getränke-Seite als stabilisiertes 16:9-Video mit Cyan-Rahmen (`.prozess-video--rahmen`) ergänzt werden.
- Optional später: Rück-Link von `docucontrol.html` (`doc-proc3`) auf die neue Seite.
