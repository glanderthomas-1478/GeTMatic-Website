# Plan: Rubrik „Schnittstelle“ ersetzt „Papierindustrie“

**Erstellt:** 2026-11-05
**Status:** Implementiert (2026-11-05), lokal getestet, noch nicht hochgeladen; `.htaccess`-Redirect nach Upload auf bearingcontrol.de prüfen
**Anforderung:** Einsatzbereich Papierindustrie entfernen; neue Rubrik „Schnittstelle“ zeigt eigene Systeme, die sich mit Bestandsanlagen verbinden, diese unterstützen, verbessern und um neue Funktionen erweitern.

---

## Überblick

### Was dieser Plan erreicht

Die Rubrik 05 auf der Website wird ausgetauscht: Statt „Papierindustrie“ gibt es „Schnittstelle“ mit eigener Unterseite (DE/EN), neuer SVG-Grafik, Startseiten-Kachel, Menüeintrag auf allen Seiten und Eintrag im Kontaktformular. Die Papier-Seite wird gelöscht, ihre alte Adresse per 301 umgeleitet.

### Warum das wichtig ist

Die Rubrik stellt das eigene Systemgeschäft heraus: Kunden müssen keine neue Anlage kaufen, sondern bestehende werden erweitert. Das passt zur Priorität „Leistungsangebot klar kommunizieren“ und verbindet DocuControl und VisionControl unter einer gemeinsamen Aussage.

---

## Aktueller Zustand

### Relevante bestehende Struktur

- `reference/Getmatic_website/einsatzbereich-papier.html`: Abschnitte Papier (`pap-*`) und „Transport & Verpackung“ (`tra-*`), Vorlage für den Seitenaufbau
- `reference/Getmatic_website/visioncontrol.html`: zweite Vorlage, Hinweis-Kasten `qk-crosslink`, Modifier `.einsatz-page--breit`
- `index.html`: Dropdown Z. 87, Kachel 05 Z. 382–387 (`anl2-*`), i18n DE Z. 523/561–563, EN Z. 650/688–690, Formular-Optionen ab Z. 448 (`kf-opt-*`)
- Dropdown-Eintrag `nav-drop-papier` + 2 i18n-Zeilen auf: `datenschutz.html`, `docucontrol.html`, `einsatzbereich-getraenke.html`, `einsatzbereich-medizin.html`, `impressum.html`, `visioncontrol.html`
- `sitemap.xml`: Papier-Eintrag
- `.htaccess`: 301-Redirects der entfernten Rubriken Lebensmittel und Transport auf die Startseite
- `kontakt.php`: Konstante `ANLIEGEN`
- Stats-Kachel „Einsatzbereiche“: 5 (bleibt 5)
- `docucontrol.html` belegt: DocuControl wird passiv zwischen HMI-Panel und Drucker geschaltet, die validierte Steuerung bleibt unverändert

### Lücken oder Probleme, die adressiert werden

- Papierindustrie soll nicht mehr angeboten werden
- Das Angebot „eigene Systeme für Bestandsanlagen“ ist nirgends als eigene Leistung sichtbar

---

## Vorgeschlagene Änderungen

### Zusammenfassung der Änderungen

- Neue Unterseite `schnittstelle.html` und Grafik `schnittstelle.svg`
- Papier-Seite und `papierindustrie.svg` löschen, 301-Redirect ergänzen
- Menü auf allen Seiten, Startseiten-Kachel 05, Sitemap und Formular umstellen

### Neue Dateien erstellen

| Dateipfad | Zweck |
| --- | --- |
| `reference/Getmatic_website/schnittstelle.html` | Unterseite Schnittstelle, DE/EN, SEO-Meta, i18n-Präfix `sst-*` |
| `reference/Getmatic_website/schnittstelle.svg` | Grafik für Unterseiten-Hero und Startseiten-Kachel |

### Zu ändernde Dateien

| Dateipfad | Änderungen |
| --- | --- |
| `index.html` | Dropdown, Kachel 05 (`anl8-*`), i18n DE/EN, Formular-Option |
| `datenschutz.html`, `docucontrol.html`, `einsatzbereich-getraenke.html`, `einsatzbereich-medizin.html`, `impressum.html`, `visioncontrol.html` | Dropdown-Eintrag + i18n DE/EN |
| `kontakt.php` | `ANLIEGEN` um `schnittstelle` ergänzen |
| `sitemap.xml` | Papier-URL durch neue URL ersetzen (priority 0.6, monthly) |
| `.htaccess` | 301 `einsatzbereich-papier.html` → Startseite |
| `context/current-data.md` | Eintrag, Struktur-Abschnitt, Upload-Liste |

### Zu löschende Dateien (falls vorhanden)

| Dateipfad | Begründung |
| --- | --- |
| `reference/Getmatic_website/einsatzbereich-papier.html` | Rubrik entfällt, letzte Version bleibt in der Git-Historie |
| `reference/Getmatic_website/papierindustrie.svg` | Wird danach nirgends mehr verwendet |

Auf dem Server beim Upload ebenfalls löschen: beide Dateien, zusätzlich zur bestehenden Löschliste.

---

## Design-Entscheidungen

### Getroffene Schlüsselentscheidungen

1. **Dateiname `schnittstelle.html`**: ohne Umlaut, kein `einsatzbereich-`-Präfix, weil es um eigene Systeme geht (analog `docucontrol.html`, `visioncontrol.html`).
2. **Name**: DE „Schnittstelle“, EN „Interface“, wie vom Nutzer festgelegt.
3. **Position**: Kachel und Menü an Stelle 05 der Papierindustrie. Stats bleiben 5, Zickzack bleibt automatisch.
4. **Redirect auf die Startseite**: gleiches Vorgehen wie bei Lebensmittel und Transport. Die neue Seite behandelt ein anderes Thema und wäre kein passender Ersatz für Papier-Suchanfragen.
5. **Kein neues CSS**: alle Bausteine existieren (`.einsatz-*`, `.einsatz-page--breit`, Crosslink-Kasten).
6. **Keine erfundenen Zahlen, Referenzen oder Systeme**: nur Aussagen des Nutzers und bereits auf der Website belegte Fakten.
7. **Keine pauschale Aussage „ohne Eingriff“**: Sie ist nur für DocuControl belegt und würde neben „neue Funktionen implementieren“ falsch verstanden werden. Pauschal nur mit Bestätigung des Nutzers (Offene Frage 2).

### Betrachtete Alternativen

- „Transport & Verpackung“ auf die neue Seite übernehmen: verworfen. Die Inhalte beschreiben Förderlogik als Dienstleistung, die neue Seite aber eigene Systeme für Bestandsanlagen.
- Nur Eintrag im Formular ohne eigene Seite: verworfen, weil die Rubrik die Systeme zeigen soll.

### Offene Fragen (falls vorhanden)

Geklärt (2026-11-05):

1. **Systeme:** nur DocuControl und VisionControl.
2. **Eingriff in die Steuerung:** nicht beantwortet. Der Text enthält deshalb keine pauschale Aussage, sondern nennt je System nur das auf der Website Belegte (DocuControl passiv, VisionControl an der SPS).
3. **„Transport & Verpackung“:** entfällt ersatzlos, Inhalt bleibt in der Git-Historie.
4. **Praxisbeispiel:** keines angegeben, Text bleibt allgemein. Kann später ergänzt werden.

---

## Schritt-für-Schritt-Aufgaben

### Schritt 1: SVG-Grafik erstellen

**Aktionen:**

- `viewBox="0 0 800 400"`, `role="img"`, `aria-label="Eigenes System verbunden mit einer Bestandsanlage"`, kein Text in der Grafik, unter 25 KB
- Stil wie `qualitaetskontrolle.svg`: dunkler Hallen-Hintergrund, Teal-Akzente `#209D9D`/`#5AD7D7`
- Motiv: links Bestandsanlage mit Schaltschrank in gedämpften Farben, rechts kompaktes eigenes System mit Display in Teal, dazwischen deutlich sichtbare Datenverbindung

**Betroffene Dateien:**

- `reference/Getmatic_website/schnittstelle.svg`

---

### Schritt 2: Unterseite erstellen

`visioncontrol.html` als Vorlage kopieren und anpassen. Alle Texte DE/EN.

**Aktionen:**

- Head: `title` „Schnittstelle – GeTMatic Automatisierungstechnik“, `description`, `keywords` (getmatic, GeTMatic, Bestandsanlagen, Nachrüstung, Anlagenerweiterung, DocuControl, VisionControl, Krefeld), `canonical`, OG-Tags, `og:image`
- Label „Einsatzbereich“, H1 „Schnittstelle“, Hero `schnittstelle.svg`
- Textentwurf (nach Klärung der offenen Fragen finalisieren):
  - `sst-p1`: Bestehende Anlagen laufen oft zuverlässig, ihnen fehlen aber einzelne Funktionen. Eine neue Anlage ist dafür selten nötig.
  - `sst-p2`: Unsere eigenen Systeme verbinden sich mit Ihrer Bestandsanlage und unterstützen sie im laufenden Betrieb. Ergänzung je nach Offener Frage 2.
  - `sst-p3`: So verbessern wir bestehende Abläufe und ergänzen neue Funktionen, die die Anlage bisher nicht bietet, zum Beispiel lückenlose Chargendokumentation mit DocuControl oder Kamera-Prüfung mit VisionControl.
- Kacheln (`sst-v1..4-h/-p`): Verbinden / Unterstützen / Verbessern / Neue Funktionen
- Hinweis-Kasten (`sst-crosslink`) mit Links auf `docucontrol.html` und `visioncontrol.html`
- CTA „Projekt anfragen“ (`sst-cta`) auf `index.html#kontakt`
- Vollständiges Menü + i18n-Objekt, `qk-*` durch `sst-*` ersetzen

**Betroffene Dateien:**

- `reference/Getmatic_website/schnittstelle.html`

---

### Schritt 3: Menü auf allen Seiten umstellen

**Aktionen:**

- Eintrag ersetzen durch `<li><a href="schnittstelle.html" data-i18n="nav-drop-schnittstelle">Schnittstelle</a></li>`
- i18n `nav-drop-papier` ersetzen durch `nav-drop-schnittstelle`: DE „Schnittstelle“, EN „Interface“

**Betroffene Dateien:**

- `index.html`, `datenschutz.html`, `docucontrol.html`, `einsatzbereich-getraenke.html`, `einsatzbereich-medizin.html`, `impressum.html`, `visioncontrol.html`

---

### Schritt 4: Startseite und Formular

**Aktionen:**

- Kachel 05: Bild, Links und `aria-label` auf die neue Seite und Grafik, Keys `anl2-*` → `anl8-*`, Text ca. 2 Sätze: eigene Systeme für Bestandsanlagen, die sich verbinden, unterstützen, verbessern und neue Funktionen ergänzen
- i18n DE/EN entsprechend ersetzen
- Formular-Option `schnittstelle` vor `docucontrol` ergänzen (`kf-opt-schnittstelle`, DE/EN), in `kontakt.php` in `ANLIEGEN` an gleicher Stelle
- „Retrofit & Legacy-Integration“ bleibt als Thema bestehen

**Betroffene Dateien:**

- `index.html`, `kontakt.php`

---

### Schritt 5: Papier-Seite entfernen, Sitemap und Redirect

**Aktionen:**

- `git rm` für `einsatzbereich-papier.html` und `papierindustrie.svg`
- `.htaccess`: nach dem Transport-Redirect ergänzen: `RewriteRule ^einsatzbereich-papier\.html$ / [R=301,L]`, mit Kommentar im bestehenden Stil
- `sitemap.xml`: Papier-Eintrag durch `schnittstelle.html` ersetzen

**Betroffene Dateien:**

- `.htaccess`, `sitemap.xml`, gelöschte Dateien

---

### Schritt 6: Testen

**Aktionen:**

- Playwright, Desktop 1440×900 + Mobile 390×844: alle 8 Seiten mit identischem Dropdown, alle Links 200, kein Overflow, keine Konsolenfehler, DE/EN vollständig
- Keine Reste: Grep nach `papier` in `*.html`, `sitemap.xml` (Treffer nur noch in `.htaccess` erlaubt)
- Formular: Test mit portablem PHP + SMTP-Stub, Betreff enthält „Schnittstelle“
- SVG im Browser prüfen, `sitemap.xml` parsen
- `.htaccess` ist lokal nicht testbar: Redirect nach Upload auf bearingcontrol.de mit `curl -I` prüfen

---

### Schritt 7: Dokumentation

**Aktionen:**

- `context/current-data.md`: Eintrag mit Datum, Struktur-Abschnitt, Upload- und Löschliste
- Plan-Status auf „Implementiert“ setzen

**Betroffene Dateien:**

- `context/current-data.md`, dieser Plan

---

## Verbindungen & Abhängigkeiten

### Dateien, die diesen Bereich referenzieren

- 7 HTML-Seiten (Dropdown), `index.html` (Kachel, Formular), `kontakt.php`, `sitemap.xml`, `.htaccess`

### Nötige Updates für Konsistenz

- `context/current-data.md`
- `CLAUDE.md`: keine Änderung nötig

### Auswirkungen auf bestehende Workflows

- Teil des anstehenden Gesamt-Uploads: erst bearingcontrol.de, dann getmatic.de
- Nach Live-Gang: neue URL in der Search Console einreichen, `einsatzbereich-papier.html` und `papierindustrie.svg` auf dem Server löschen

---

## Validierungs-Checkliste

- [ ] `schnittstelle.svg` rendert korrekt, unter 25 KB, kein Text
- [ ] `schnittstelle.html` ohne Fehler und Overflow, DE/EN vollständig
- [ ] Dropdown auf allen 8 Seiten identisch, alle Links 200
- [ ] Startseite: 5 Kacheln, Zickzack korrekt, Stats = 5
- [ ] Formular sendet Thema „Schnittstelle“
- [ ] Keine Papier-Reste außer dem Redirect
- [ ] `sitemap.xml` valide, 8 URLs
- [ ] Keine erfundenen Systeme, Zahlen oder Referenzen
- [ ] `context/current-data.md` aktualisiert

---

## Erfolgskriterien

1. „Schnittstelle“ ist von jeder Seite und von der Startseite erreichbar, Papierindustrie ist überall entfernt.
2. Die Seite erklärt verständlich in DE und EN: eigene Systeme verbinden sich mit Bestandsanlagen, unterstützen und verbessern sie und ergänzen neue Funktionen.
3. Alle lokalen Tests laufen ohne Fehler, die alte Papier-Adresse leitet auf der Testumgebung per 301 um.

---

## Notizen

- Echte Fotos oder Videos eines angeschlossenen Systems können später die Grafik ergänzen.
- Das Impressum (HRB, Gesellschafter) ist ein eigenes Thema und nicht Teil dieses Plans.
