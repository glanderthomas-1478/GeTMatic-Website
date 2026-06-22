# Plan: Eigene Unterseiten für die Einsatzbereiche

**Erstellt:** 2026-06-22
**Status:** Implementiert
**Anforderung:** Die 6 Karten in der Anlagen/Einsatzbereiche-Sektion auf `index.html` bekommen jeweils eine eigene Unterseite mit mehr Informationen (statt Hover/Akkordeon), DE/EN, SEO-optimiert.

---

## Überblick

### Was dieser Plan erreicht

Aus den 6 Kurzkarten in der Sektion „Unsere Einsatzbereiche" werden 6 verlinkte Unterseiten mit ausführlicherem Text, mehr Bildmaterial und eigener SEO-Optimierung (Title/Description/Canonical pro Branche). Die Startseite bleibt unverändert kompakt, verlinkt aber jede Karte auf „Mehr erfahren →".

### Warum das wichtig ist

Laut `context/strategy.md` ist „Leistungsangebot klar kommunizieren" eine Kernpriorität — Industrie/Pharma/Medizin-Entscheider sollen sich direkt angesprochen fühlen. Eigene Unterseiten erlauben es, pro Branche gezielt auf Suchbegriffe zu optimieren (z. B. „Automatisierung Papierindustrie Krefeld"), was eine Hover-Lösung nicht leisten kann, und schaffen mehr Raum für Vertrauenssignale (Detailtext, ggf. Referenzprojekte) — passend zur Priorität „Kontakt und Vertrauen".

---

## Aktueller Zustand

### Relevante bestehende Struktur

- `reference/Getmatic_website/index.html` — Anlagen-Sektion Zeilen 216–279, sechs `<article class="anlagen-item">`-Karten mit Bild, Nummer, Titel, Kurztext. i18n-Keys `anl1-h`/`anl1-p` … `anl6-h`/`anl6-p` im `<script>`-Block (DE ab Zeile 361, EN ab Zeile 417).
- `reference/Getmatic_website/impressum.html` — einzige existierende Unterseite. Liefert das Pattern für neue Unterseiten: identischer Header/Footer, eigener `<main>`-Bereich, eigener i18n-Block mit page-spezifischen Keys, eigenes `<meta name="robots">`/`<link rel="canonical">`.
- `reference/Getmatic_website/style.css` — Abschnitt `/* IMPRESSUM */` (Zeile 711 ff.) zeigt das Pattern für Unterseiten-Layout (`.imprint-page`, `.imprint-header`, `.imprint-card` …).
- Bestehende SVGs pro Branche bereits vorhanden: `transportanlage.svg`, `papierindustrie.svg`, `kartonverpackung.svg`, `getraenkeverpackung.svg`, `sterilisation.svg`, `dokumentation.svg`.
- `context/current-data.md` — Tech-Stack (statisches HTML/CSS/JS, 1blu-Hosting, FTP-Deployment), Liste aller Live-Dateien.

### Lücken oder Probleme, die adressiert werden

- Karten zeigen nur 1–2 Sätze Kurztext, keine Möglichkeit für mehr Tiefe (Referenzprojekte, technische Details, Vorteile).
- Keine SEO-Differenzierung pro Branche — aktuell rankt nur die Startseite mit einem generischen Title für alle Branchen gleichzeitig.
- DocuControl (Karte 06 „Dokumentation") ist eigentlich ein eigenständiges Produkt mit Hero-Slide, hat aber bisher keine eigene Landingpage — nur den Kurztext in der Karte.

---

## Vorgeschlagene Änderungen

### Zusammenfassung der Änderungen

- 6 neue HTML-Unterseiten anlegen, eine pro Einsatzbereich/Branche.
- Neuer CSS-Abschnitt `EINSATZBEREICH-DETAIL` in `style.css` (Hero-Bereich, Inhalts-Body, Zurück-Link, Vorteile-Liste, CTA), angelehnt an das Impressum-Pattern.
- `index.html`: jede Anlagen-Karte bekommt einen Link „Mehr erfahren →" zur jeweiligen Unterseite (ganze Karte klickbar machen, `<article>` → `<a class="anlagen-item">` oder Button am Kartenende — siehe Design-Entscheidungen).
- i18n-Keys für neue Unterseiten in jeweils eigenem `<script>`-Block (Pattern wie `impressum.html`), plus neuer Key `anlX-link` ("Mehr erfahren" / "Learn more") in `index.html`.
- `context/current-data.md` aktualisieren: neue Dateien in der Liste der Arbeitsdateien, neue Re-Upload-Checkliste.

### Neue Dateien erstellen

| Dateipfad | Zweck |
|---|---|
| `reference/Getmatic_website/einsatzbereich-transport.html` | Unterseite „Transport & Verpackung" |
| `reference/Getmatic_website/einsatzbereich-papier.html` | Unterseite „Papierindustrie" |
| `reference/Getmatic_website/einsatzbereich-lebensmittel.html` | Unterseite „Lebensmittelindustrie" |
| `reference/Getmatic_website/einsatzbereich-getraenke.html` | Unterseite „Getränkeverpackung" |
| `reference/Getmatic_website/einsatzbereich-medizin.html` | Unterseite „Medizin & Sterilisation" |
| `reference/Getmatic_website/docucontrol.html` | Produktseite „DocuControl" (statt generischer „Dokumentation"-Branchenseite, da es sich um das Eigenprodukt handelt) |

### Zu ändernde Dateien

| Dateipfad | Änderungen |
|---|---|
| `reference/Getmatic_website/index.html` | Anlagen-Karten 01–06 jeweils mit Link zur Unterseite versehen (`anl1-link` … `anl6-link` i18n-Keys ergänzen, "Mehr erfahren"/"Learn more"); Hero-Slide-2-Button (`hero2-btn` „DocuControl entdecken") von `#anlagen` auf `docucontrol.html` umbiegen |
| `reference/Getmatic_website/style.css` | Neuer Abschnitt für Unterseiten-Layout `.einsatz-page`, `.einsatz-hero`, `.einsatz-back`, `.einsatz-content`, `.einsatz-vorteile`; `.anlagen-item` Cursor/Hover an Klickbarkeit anpassen |
| `context/current-data.md` | Neue Dateien in Arbeitsdateien-Liste ergänzen, neue Re-Upload-Checkliste für diesen Umbau |

### Zu löschende Dateien

Keine.

---

## Design-Entscheidungen

### Getroffene Schlüsselentscheidungen

1. **Eigene Unterseiten statt Hover/Akkordeon**: User-Entscheidung — SEO-Wert pro Branche, zuverlässig auf Mobilgeräten (vs. Hover).
2. **DocuControl bekommt eigene Produktseite statt „Dokumentation"-Branchenseite**: DocuControl ist Eigenentwicklung von getmatic (siehe Hero-Slide 2, `context/current-data.md`), kein Branchen-Einsatzbereich wie die anderen 5. Eigene Seite passt besser zur Such-Intention („DocuControl Chargenprotokoll") und verbindet Hero-CTA „DocuControl entdecken" sinnvoll mit echtem Inhalt statt nur Anker zur Anlagen-Sektion.
3. **Ganze Karte bleibt zusätzlich per Klick verlinkt + expliziter „Mehr erfahren"-Link**: Karten sind aktuell schon hover-interaktiv (Bild-Zoom). Ein zusätzlicher Text-Link macht die Klickbarkeit für Nutzer eindeutig erkennbar (Accessibility, klare Erwartung "hier gibt's mehr").
4. **Unterseiten-Dateinamen mit Präfix `einsatzbereich-`**: Konsistent, leicht erweiterbar, klar von `impressum.html` unterscheidbar. Branchen-Slug statt durchnummeriert (`einsatzbereich-01.html`), damit URLs für SEO sprechende Slugs haben.
5. **Layout-Pattern von `impressum.html` übernehmen**: Header/Footer/Sprachumschalter/Mobile-Nav 1:1 wiederverwenden (kein neues Pattern erfinden), nur der `<main>`-Bereich unterscheidet sich. Reduziert Wartungsaufwand und Inkonsistenz-Risiko.
6. **Kein neues CMS/Templating**: Bleibt reines statisches HTML wie der Rest der Seite (Tech-Stack-Entscheidung in `context/current-data.md`) — jede Unterseite ist eine eigenständige `.html`-Datei mit dupliziertem Header/Footer, wie bei `impressum.html` bereits etabliert.

### Betrachtete Alternativen

- **Akkordeon auf der Startseite**: schneller umzusetzen, aber kein SEO-Mehrwert, begrenzter Platz für Inhalt — verworfen (User-Entscheidung).
- **Ein gemeinsames Template via JS-Include**: würde Duplizierung reduzieren, aber bricht mit dem aktuellen Tech-Stack-Prinzip „kein Framework, kein CMS" und erschwert FTP-Bulk-Deployment unnötig — verworfen.
- **DocuControl als 6. Branchenkarte beibehalten**: einfacher, aber verschenkt Potenzial einer echten Produktseite für das Eigenprodukt — verworfen zugunsten von `docucontrol.html`.

### Offene Fragen — geklärt durch Recherche

1. **Inhalt pro Unterseite**: User-Entscheidung — Texte recherchieren und plausibel erweitern (kein neuer Fakten-Input vom User). Branchentexte werden beim Implementieren fachlich vertieft (typische Maschinentypen, Prozessanforderungen, Normen), ohne erfundene Kundenreferenzen oder Zahlen, die nicht bereits auf der Website stehen.
2. **DocuControl-Inhalt**: Recherche im Projektverzeichnis `C:\Claude-workspaces\claude-workspace-docupi` (Konzeptpapier `outputs/docupi-3000_konzept_getmatic.md`) ergab zentrale, bisher auf der Website fehlende Fakten — siehe „Recherche-Ergebnis DocuControl" unten. Diese fließen in `docucontrol.html` ein.
3. **Bildmaterial**: Bestehende SVGs reichen für diesen Schritt aus, keine neuen Bilder nötig.

### Recherche-Ergebnis DocuControl (Quelle: `claude-workspace-docupi`)

Internes Projekt heißt `DocuPi-3000`, Website-Markenname bleibt **DocuControl** (bereits etabliert in Hero-Slide 2 und Anlagen-Karte 06) — auf der neuen Produktseite wird ausschließlich der Markenname verwendet, die technischen Fakten stammen aber aus dem DocuPi-3000-Konzept.

Zentraler USP, den der User explizit hervorgehoben haben möchte: **Autarkie — kein Eingriff in die Maschine, kein Eingriff ins Kundennetzwerk nötig.**

Kernfakten für die Produktseite:

- **Passive Zwischenschaltung**: DocuControl sitzt zwischen HMI-Panel und Drucker und liest die Chargenprotokolle direkt aus dem Druckdatenstrom (TCP/9100) — die Maschinensteuerung selbst wird nicht verändert, nicht einmal berührt.
- **Keine Eingriffe in validierte Systeme**: Sterilisator-Steuerungen sind validierte Systeme; Software-Updates oder Konfigurationsänderungen direkt an der Maschine sind regulatorisch/kommerziell meist ausgeschlossen. DocuControl braucht genau das nicht — reine externe Beobachtung des Druckdatenstroms.
- **Zwei physisch getrennte Netzwerkseiten, kein IP-Routing**: Maschinen-Seite (abgeschottet, nur Kommunikation mit der HMI) und Klinik-/Kunden-Seite (Web-Dashboard-Zugriff) sind strikt getrennt. DocuControl ist ein Endgerät, kein Router — zentrales Freigabekriterium für jede IT-Abteilung.
- **Drei Betriebsmodi, je nach Netzwerk-Politik des Kunden**:
  - *Integriert*: im Kunden-LAN eingebunden, Zugriff vom Arbeitsplatz-PC
  - *Hotspot*: eigenes WLAN, Zugriff per Tablet/Laptop ohne jede Netzwerk-Integration
  - *USB-Export*: Chargen werden auf USB-Stick exportiert — für IT-restriktive Umgebungen ganz ohne Netzwerkanbindung
- **Keine ausgehenden Internetverbindungen im Standardbetrieb** — Gerät funktioniert vollständig lokal/autark.
- **Keine personenbezogenen Daten**, ausschließlich Maschinendaten — DSGVO-konform durch Lokalität der Datenverarbeitung.
- **Installation unter 30 Minuten**: vorhandenen LAN-Printserver abklemmen, DocuControl an gleicher Stelle anschließen, fertig.
- Bereits bestehende Website-Fakten (weiterverwenden): EN ISO 17665-Konformität, PDF + Web-Dashboard, Datenspeicherung auf Gerät/USB/optional Netzwerk.

Diese Punkte — insbesondere Autarkie/kein Maschinen- und Netzwerk-Eingriff — sollen auf der Produktseite **prominent** (z. B. eigener Vorteile-Block ganz oben, nicht nur im Fließtext) stehen, da sie laut User der wichtigste Verkaufsgrund sind.

---

## Schritt-für-Schritt-Aufgaben

### Schritt 1: CSS-Grundlage für Unterseiten-Layout schaffen

Neuen Abschnitt in `style.css` nach dem `/* IMPRESSUM */`-Block ergänzen, analog aufgebaut: `.einsatz-page` (Padding wie `.imprint-page`), `.einsatz-hero` (großes Bild oben, ähnlich `--img`-Var-Pattern aus `.anlagen-img`), `.einsatz-back` (Link "← Zurück zur Übersicht"), `.einsatz-content` (Fließtext, ggf. `.einsatz-vorteile`-Liste mit Checkmark-Icons analog `.leistung-card`), `.einsatz-cta` (Wiederverwendung von `.kontakt-cta`-Pattern oder Verweis auf `#kontakt`-Sektion via Link auf `index.html#kontakt`).

**Aktionen:**
- Neue CSS-Klassen ergänzen, Farben/Spacing aus bestehenden `--teal`/`--space-*`-Variablen verwenden
- Responsive-Regeln in den bestehenden `@media (max-width: 768px)`-Block ergänzen

**Betroffene Dateien:**
- `reference/Getmatic_website/style.css`

---

### Schritt 2: Erste Unterseite als Referenz-Template bauen (`einsatzbereich-transport.html`)

Komplette Seite nach `impressum.html`-Pattern: identischer `<head>` (eigene `<title>`, `<meta description>`, `<link rel="canonical">` mit branchenspezifischem SEO-Text, `robots: index, follow` da diese Seiten anders als Impressum indexiert werden sollen), identischer Header/Footer/Sprachumschalter/Mobile-Nav-Code, eigener `<main class="einsatz-page">`.

Fachliche Vertiefung für Transport & Verpackung (Basis für Fließtext/Vorteile-Liste, Text-Basis `anl1-p`): Förderbänder, Sortieranlagen, Verpackungslinien; typische Herausforderungen: hohe Taktraten bei wechselnden Produktgrößen/-gewichten, Sensorik-Integration (Lichtschranken, Wäge-/Erkennungssysteme), Linienverbund mehrerer Anlagenabschnitte; SPS-Aufgaben: Step7/TIA-Programmierung der Förderlogik, Frequenzumrichter-Parametrierung für variable Bandgeschwindigkeiten, Schnittstellen zu vor-/nachgelagerten Anlagenteilen.
- `.einsatz-back`-Link zurück zu `index.html#anlagen`
- `.einsatz-hero` mit `transportanlage.svg`
- Titel + erweiterter Fließtext (Ausgangstext aus `anl1-p` plus vertiefende Absätze)
- Vorteile-Liste (3–4 Punkte: Was getmatic konkret bietet für diesen Bereich)
- CTA-Link zu `index.html#kontakt`
- Eigener i18n-`<script>`-Block mit `tra-*`-Keys (Präfix `tra` für transport, analog `imp-*`)

**Aktionen:**
- Datei erstellen, Header/Footer 1:1 aus `impressum.html` kopieren, Links auf `index.html#leistungen` etc. beibehalten
- `<main>`-Inhalt branchenspezifisch füllen
- i18n-Keys für DE/EN ergänzen (inkl. der gemeinsamen Keys `nav-*`, `foot-start`, `skip-link`)
- `<title>`, `<meta name="description">`, `<link rel="canonical">` setzen, z. B. Canonical: `https://www.getmatic.de/einsatzbereich-transport.html`

**Betroffene Dateien:**
- `reference/Getmatic_website/einsatzbereich-transport.html`

---

### Schritt 3: Verbleibende 4 Branchen-Unterseiten nach demselben Template bauen

Gleiches Vorgehen wie Schritt 2. Fachliche Vertiefung je Branche (Basis für Vorteile-Liste + Fließtext, beim Implementieren in vollständige Sätze ausformulieren — keine erfundenen Kundennamen/Zahlen, nur allgemeine Fachinhalte zu SPS-Automatisierung in der jeweiligen Branche):

- **`einsatzbereich-papier.html`** (Bild `papierindustrie.svg`, Text-Basis `anl2-p`): Rollenschneider, Querschneider, Kalander, Wickelmaschinen; typische Herausforderungen: Bahnspannungsregelung, Synchronisation mehrerer Achsen, Materialbruch-Erkennung; SPS-Aufgaben: Antriebsverbund-Steuerung (Step7/TIA), Rezeptverwaltung für unterschiedliche Papiersorten/Formate
- **`einsatzbereich-lebensmittel.html`** (Bild `kartonverpackung.svg`, Text-Basis `anl3-p`): Faltschachtel-Klebemaschinen, Verarbeitungsanlagen; Anforderungen: Hygienegerechte Steuerungstechnik, IP-Schutzklassen für Reinigungsprozesse, Rückverfolgbarkeit von Chargen, hohe Taktzahlen bei gleichzeitig hoher Prozesssicherheit
- **`einsatzbereich-getraenke.html`** (Bild `getraenkeverpackung.svg`, Text-Basis `anl4-p`): Aseptische Kartonverpackungslinien (Tetra-Pak-artig), Faltung/Siegel/Abpackung bis 18.000 Packungen/Stunde; Anforderungen: präzise Synchronisation von Servoantrieben, Sterilbereich-Überwachung, Anlagenverfügbarkeit/Linieneffizienz (OEE) als zentrale KPI
- **`einsatzbereich-medizin.html`** (Bild `sterilisation.svg`, Text-Basis `anl5-p`): Dampf-Autoklaven, Sterilisationsprozesse nach EN ISO 17665; Anforderungen: präzise Druck-/Temperatur-/Zykluszeitregelung, Validierungsanforderungen an die Steuerung, Prozesssicherheit als oberste Priorität (Patientensicherheit). Querverweis im Fließtext auf die DocuControl-Produktseite (`docucontrol.html`) als ergänzendes Dokumentationsangebot für genau diese Anlagen — Cross-Link einbauen, da hoher inhaltlicher Zusammenhang.

**Aktionen:**
- Je Datei: Template aus Schritt 2 kopieren, Bild/Titel/Text/i18n-Präfix/Canonical-URL austauschen
- Fließtext und Vorteile-Liste mit obigen Stichpunkten fachlich ausformulieren
- Eigene `<meta name="description">` pro Branche mit branchenspezifischen Suchbegriffen formulieren
- Bei `einsatzbereich-medizin.html`: Cross-Link-Hinweis/Button zu `docucontrol.html` ergänzen

**Betroffene Dateien:**
- `reference/Getmatic_website/einsatzbereich-papier.html`
- `reference/Getmatic_website/einsatzbereich-lebensmittel.html`
- `reference/Getmatic_website/einsatzbereich-getraenke.html`
- `reference/Getmatic_website/einsatzbereich-medizin.html`

---

### Schritt 4: DocuControl-Produktseite bauen (`docucontrol.html`)

Eigenes Template, leicht abweichend von den Branchenseiten — Fokus auf Produktmerkmale statt Branche. Inhalt basiert auf dem Recherche-Ergebnis oben (Quelle: DocuPi-3000-Konzeptpapier):

- Hero-ähnlicher Bereich mit `dokumentation.svg`
- **Autarkie-Vorteile-Block direkt nach dem Hero, vor dem Fließtext** (3–4 Kacheln im `.leistung-card`-Stil, da das laut User der wichtigste Punkt ist):
  1. „Kein Eingriff in die Maschine" — passive Zwischenschaltung zwischen HMI und Drucker, liest nur den Druckdatenstrom, validierte Steuerung bleibt unverändert
  2. „Kein Eingriff ins Kundennetzwerk" — zwei physisch getrennte Netzwerkseiten, kein IP-Routing, DocuControl ist Endgerät statt Router
  3. „Drei Betriebsmodi" — Integriert (Kunden-LAN), Hotspot (eigenes WLAN), USB-Export (ganz ohne Netzwerk) — passend zur IT-Politik jedes Kunden
  4. „Vollständig autark" — keine ausgehenden Internetverbindungen im Standardbetrieb, keine personenbezogenen Daten, DSGVO-konform durch lokale Datenverarbeitung
- Fließtext darunter: automatische Chargenprotokollierung in Echtzeit, EN ISO 17665-Konformität, PDF-Export mit Druck-/Temperaturkurven, Web-Dashboard, Datenspeicherung auf Gerät/USB/optional Netzwerk, Installation unter 30 Minuten (vorhandenen Printserver abklemmen, DocuControl anschließen)
- CTA zu `index.html#kontakt`
- i18n-Keys mit Präfix `doc-*`

**Aktionen:**
- Datei erstellen nach Pattern aus Schritt 2/3, aber mit Produkt- statt Branchen-Framing
- Autarkie-Vorteile-Block so platzieren, dass er ohne Scrollen oder direkt danach sichtbar ist
- `<title>`/`description`/Canonical auf `docucontrol.html` und DocuControl-spezifische Suchbegriffe ausrichten (z. B. „Chargendokumentation Sterilisator ohne Netzwerkeingriff", „DocuControl EN ISO 17665")
- Keine Erwähnung des internen Projektnamens „DocuPi-3000" auf der Website — nur „DocuControl" verwenden

**Betroffene Dateien:**
- `reference/Getmatic_website/docucontrol.html`

---

### Schritt 5: `index.html` verlinken

- Jede der 6 `<article class="anlagen-item">` bekommt einen `<a class="anlagen-link" href="...">`-Button am Ende von `.anlagen-body` mit i18n-Key `anlX-link` (Text "Mehr erfahren →" / "Learn more →")
- Karte 06 verlinkt auf `docucontrol.html` statt einer `einsatzbereich-dokumentation.html`
- Hero-Slide 2 Button (`hero2-btn`) `href="#anlagen"` → `href="docucontrol.html"`
- Neue i18n-Keys `anl1-link` … `anl6-link` in DE- und EN-Übersetzungsobjekt ergänzen

**Aktionen:**
- HTML-Edits in der Anlagen-Sektion (6× ein `<a>`-Tag ergänzen)
- `hero2-btn`-Link ändern
- i18n-Objekt um 6 neue Keys (DE + EN) erweitern

**Betroffene Dateien:**
- `reference/Getmatic_website/index.html`

---

### Schritt 6: Kontext-Dokumentation aktualisieren

- `context/current-data.md`: neue Dateien in der Arbeitsdateien-Liste ergänzen, neuen Eintrag in „Offene Aufgaben" für den Re-Upload aller 6 neuen Unterseiten + aktualisierter `index.html`/`style.css`
- Re-Upload-Checkliste: alle neuen `.html`-Dateien + `index.html` + `style.css` müssen gemeinsam per FTP hoch (bestehende Erfahrung: SVGs einzeln, mehrere Dateien via FileZilla, Linux-Server Groß-/Kleinschreibung beachten)

**Aktionen:**
- `current-data.md` editieren: neue Dateien, neue Checkliste, Datum des Updates

**Betroffene Dateien:**
- `context/current-data.md`

---

## Verbindungen & Abhängigkeiten

### Dateien, die diesen Bereich referenzieren

- `reference/Getmatic_website/index.html` (Hero-Slide 2, Anlagen-Sektion, Footer-Nav)
- `context/current-data.md` (Dateiliste, Re-Upload-Checkliste)

### Nötige Updates für Konsistenz

- Footer-Navigation (`footer-nav` in jeder neuen Unterseite) muss dieselben Links wie `index.html`/`impressum.html` enthalten
- `<link rel="canonical">` in jeder neuen Datei korrekt und eindeutig pro Seite
- `og:image`/structured data (`application/ld+json`) bleiben auf `index.html` beschränkt — keine Duplizierung des LocalBusiness-Schemas nötig auf Unterseiten (Branchenseiten brauchen kein eigenes Schema, höchstens optional `Service`-Schema — nicht in diesem Plan enthalten, da nicht angefordert)

### Auswirkungen auf bestehende Workflows

- Re-Upload-Checkliste in `context/current-data.md` wächst um 6 neue Dateien — beim nächsten FTP-Deployment müssen diese mit hochgeladen werden
- Bestehender Hero-Slider-Re-Upload (offener Punkt aus letzter Session) bleibt unabhängig davon offen und sollte vorher abgeschlossen/verifiziert werden

---

## Validierungs-Checkliste

- [ ] Alle 6 neuen `.html`-Dateien öffnen lokal im Browser fehlerfrei (kein gebrochenes Layout, Header/Footer identisch zu `index.html`/`impressum.html`)
- [ ] Sprachumschalter DE/EN funktioniert auf jeder neuen Unterseite
- [ ] Mobile-Nav (Hamburger) funktioniert auf jeder neuen Unterseite
- [ ] Jede Karte auf der Startseite verlinkt korrekt auf die zugehörige Unterseite
- [ ] Hero-Slide-2-Button verlinkt auf `docucontrol.html`
- [ ] Jede Unterseite hat eigenen `<title>`, `<meta description>`, `<link rel="canonical">`
- [ ] „Zurück zur Übersicht"-Link auf jeder Unterseite führt zu `index.html#anlagen`
- [ ] `context/current-data.md` aktualisiert (Dateiliste + Re-Upload-Checkliste)
- [ ] CLAUDE.md geprüft, ob Workspace-Struktur-Änderung dort dokumentiert werden muss (vermutlich nicht nötig, da reine Content-Erweiterung innerhalb bestehender Struktur)

## Erfolgskriterien

1. 6 neue, vollständige, fehlerfrei ladende Unterseiten existieren in `reference/Getmatic_website/`
2. Jede Anlagen-Karte und der DocuControl-Hero-Button verlinken korrekt
3. Jede Unterseite ist DE/EN-fähig und hat eigene SEO-Metadaten
4. `context/current-data.md` spiegelt den neuen Dateibestand und offene Re-Upload-Aufgabe wider

---

## Notizen

- Vor Implementierung sollten die drei offenen Fragen (Inhalt/Referenzprojekte, DocuControl-Details, zusätzliches Bildmaterial) mit dem User geklärt werden — sonst läuft `/implement` Gefahr, Inhalte zu erfinden, die nicht durch bestehenden Website-Content gedeckt sind.
- Der offene Punkt aus der letzten Session (Hero-Slider-Re-Upload, leere Slider-Flächen auf Live-Site) ist unabhängig von diesem Plan und sollte vorher verifiziert/behoben sein, damit der nächste Upload nicht zwei ungetestete Änderungen gleichzeitig auf den Server bringt.
- Mögliche Folge-Idee (nicht Teil dieses Plans): Referenzprojekte/Case-Studies pro Branche, sobald konkrete Kundenprojekte dafür freigegeben sind (siehe offene Frage in `context/strategy.md`).

---

## Implementierungsnotizen

**Implementiert:** 2026-06-22

### Zusammenfassung

Alle 6 Schritte wie geplant umgesetzt: CSS-Grundlage (`EINSATZBEREICH-DETAIL`-Abschnitt in `style.css`), 5 Branchen-Unterseiten (`einsatzbereich-transport.html`, `-papier.html`, `-lebensmittel.html`, `-getraenke.html`, `-medizin.html`) sowie die DocuControl-Produktseite (`docucontrol.html`) mit prominentem Autarkie-Vorteile-Block (kein Eingriff in Maschine/Kundennetzwerk, drei Betriebsmodi, vollständig autark — recherchiert aus `claude-workspace-docupi/outputs/docupi-3000_konzept_getmatic.md`). `index.html` verlinkt jede Anlagen-Karte ("Mehr erfahren →") und der DocuControl-Hero-Button auf die jeweilige Unterseite. `context/current-data.md` mit neuem Dateibestand und Re-Upload-Checkliste aktualisiert.

### Abweichungen vom Plan

Keine inhaltlichen Abweichungen. Kleinere Implementierungsdetails, die der Plan offenließ:
- Checkmark-/Symbol-Icons im Autarkie-Block der DocuControl-Seite frei gewählt (Schloss, durchgestrichener Kreis, Raster, Schild) statt generischer Häkchen, um die vier Punkte visuell unterscheidbar zu machen.
- Medizin-Seite: Cross-Link zu DocuControl als eigene Hervorhebungsbox (`.einsatz-crosslink`) umgesetzt statt nur als Inline-Link im Fließtext, für bessere Sichtbarkeit.

### Aufgetretene Probleme

Ein Tippfehler beim ersten Entwurf von `einsatzbereich-transport.html` (doppeltes `style`-Attribut auf dem `.einsatz-hero`-Element) wurde sofort nach dem Schreiben korrigiert. Lokale Struktur-Validierung (Tag-Zählung) für alle 6 neuen Dateien unauffällig.

### Offen für den User

- Lokale Sichtprüfung im Browser (Sprachumschalter, Mobile-Nav, Layout) noch nicht durch den User bestätigt — die drei Stichproben-Dateien (`docucontrol.html`, `einsatzbereich-medizin.html`, `index.html`) wurden zur Kontrolle automatisch im Standardbrowser geöffnet.
- Re-Upload aller geänderten/neuen Dateien per FTP steht noch aus (siehe aktualisierte Checkliste in `context/current-data.md`) — bewusst nicht automatisch hochgeladen, da Deployment ein bewusster, vom User auszuführender Schritt ist.
