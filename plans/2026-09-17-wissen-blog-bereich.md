# Plan: Wissen-/Blog-Bereich (Guide-Format nach Vaultwarden-Vorbild)

**Erstellt:** 2026-09-17
**Status:** Entwurf
**Anforderung:** Neuen Blog-/Wissensbereich auf getmatic.de aufbauen, im Format-Vorbild von https://shattered.io/de/vaultwarden-einrichten/ (Inhaltsverzeichnis, Lesezeit, Autor-Box, Schritt-für-Schritt-Struktur, Code-/Vergleichsblöcke, Sidebar mit Themen/neueste Beiträge) — inhaltlich passend zu getmatic (SPS/TIA Portal, Antriebstechnik, Pharma-/Medizin-Dokumentation), nicht zum Vaultwarden-Thema.

---

## Überblick

### Was dieser Plan erreicht

Ein neuer, wiederverwendbarer Seitentyp „Wissen"-Artikel wird etabliert: eine Übersichtsseite (`wissen.html`) plus ein erster, vollständiger Beispielartikel im Long-Form-Guide-Format (Inhaltsverzeichnis, Lesezeit, Autor-Box, Schritt-Struktur, Vergleichstabelle, FAQ). Das CSS-Pattern (`WISSEN`-Sektion in `style.css`) ist so gebaut, dass künftige Artikel einfach als weitere `wissen-<slug>.html`-Dateien nach demselben Muster ergänzt werden können, ohne dass CSS erneut angefasst werden muss.

### Warum das wichtig ist

Passt zu Strategie-Priorität 2 und 3 (`context/strategy.md`): Leistungsangebot klar kommunizieren und Vertrauen aufbauen. Long-Form-Fachartikel sind laut Vaultwarden-Vorbild ein bewährtes Content-Marketing-Format, das organischen Google-Traffic zu Nischenthemen (SPS, Chargendokumentation, Antriebstechnik) anzieht — ein Kanal, den die reinen Leistungsseiten (`einsatzbereich-*.html`) nicht abdecken, weil sie auf Konversion statt auf Themenrecherche ausgelegt sind.

---

## Aktueller Zustand

### Relevante bestehende Struktur

- Statische HTML/CSS/JS-Website, kein CMS. Jede Seite ist eine eigenständige `.html`-Datei mit eingebettetem `<script>`-Block für DE/EN-Übersetzung (`data-i18n`-Attribute + `translations`-Objekt, `localStorage`-basiert) — siehe z. B. `reference/Getmatic_website/einsatzbereich-medizin.html`.
- Gemeinsames Kopf-/Fußzeilen-Markup wird in **jeder** HTML-Datei dupliziert (kein Include-Mechanismus). Aktuell 8 Seiten mit identischem Nav-/Footer-Grundgerüst: `index.html`, `impressum.html`, `datenschutz.html`, `docucontrol.html`, `einsatzbereich-{medizin,transport,papier,getraenke}.html`.
- Haupt-Nav (`.main-nav`) aktuell: Leistungen, Anlagen, Kontakt, Impressum, Datenschutz. Footer-Nav (`.footer-nav`) analog (ohne "Anlagen"/"Kontakt", siehe Bestand).
- CSS-Design-Tokens in `style.css` (Zeilen 24–58): Farben `--teal`/`--teal-light`/`--teal-dark`/`--gray-header`/`--gray-dark`/`--text`/`--text-light`/`--text-dark`/`--bg-white`/`--bg-card`, Spacing `--space-1` (8px) bis `--space-7` (96px), `--font-head` (Barlow Condensed), `--font-body` (Barlow), `--radius` (4px), `--shadow`/`--shadow-lg`, `--transition`.
- Bestehende, wiederverwendbare Seiten-Muster:
  - `.imprint-page`/`.legal-section` (Impressum/Datenschutz) — nummerierte Abschnitte, gut für rechtliche/strukturierte Texte.
  - `.einsatz-page`/`.einsatz-hero`/`.einsatz-content`/`.einsatz-vorteile`/`.einsatz-crosslink` (Einsatzbereich-Unterseiten) — Hero-Bild, Fließtext, Vorteile-Kacheln, Cross-Link-Kasten.
  - `.einsatz-screens`/`.einsatz-screen`/`.screen-frame` (docucontrol.html) — Zweispalten-Wechsel-Layout Bild/Text, inkl. Browser-Mockup-Rahmen für Screenshots.
  - `animate-in` + `IntersectionObserver` — Scroll-Einblendung, in jedem Seiten-Script dupliziert, respektiert `prefers-reduced-motion`.
- `sitemap.xml` listet aktuell 8 URLs mit `changefreq`/`priority`. `robots.txt` erlaubt alles außer `/intern/` (keine Änderung nötig).
- Kein bestehendes Mehrspalten-Layout mit Sidebar, kein Inhaltsverzeichnis-Pattern, keine Code-Block-Typografie, keine Autor-Box, keine "Lesezeit"-Anzeige irgendwo auf der Seite — dies ist der erste Einsatz all dieser Elemente.

### Lücken oder Probleme, die adressiert werden

- Es gibt keinen Content-Typ, der auf Themenrecherche/organischen Blog-Traffic ausgelegt ist — alle bestehenden Seiten sind Produkt-/Leistungsseiten mit Konversionsfokus.
- Kein etabliertes CSS-Pattern für Long-Form-Artikel-Typografie (Inhaltsverzeichnis, Code-Blöcke, Vergleichstabellen, Autor-Box) — muss neu geschaffen werden.

---

## Vorgeschlagene Änderungen

### Zusammenfassung der Änderungen

- Neue CSS-Sektion `WISSEN` in `style.css`: Übersichtsseiten-Layout (Artikel-Kacheln) + Artikel-Layout (zweispaltig: Content + Sidebar, mobil einspaltig) mit allen Typografie-Bausteinen (TOC, Meta-Zeile/Lesezeit, Autor-Box, Code-Block, Vergleichstabelle, Sidebar-Karte, Abschluss-CTA).
- Neue Seite `wissen.html` — Übersicht/Hub, listet Artikel als Kacheln (analog zum Anlagen-Kachel-Pattern auf `index.html`).
- Neuer erster Artikel `wissen-chargendokumentation-en-iso-17665.html` — vollständiger Guide im Zielformat, Thema passend zu getmatic (siehe Design-Entscheidungen).
- Neuer Nav-Punkt „Wissen" in `.main-nav` und `.footer-nav` auf **allen 8 bestehenden Seiten** plus den 2 neuen Seiten selbst (10 Dateien insgesamt), Position zwischen „Kontakt" und „Impressum".
- `sitemap.xml`: zwei neue `<url>`-Einträge (`wissen.html`, `wissen-chargendokumentation-en-iso-17665.html`).
- `context/current-data.md`: neuer datierter Eintrag + Upload-Checkliste-Punkt.

### Neue Dateien erstellen

| Dateipfad | Zweck |
| --- | --- |
| `reference/Getmatic_website/wissen.html` | Wissen-Übersichtsseite: Hub mit Artikel-Kachel(n), Einstiegspunkt für den neuen Bereich |
| `reference/Getmatic_website/wissen-chargendokumentation-en-iso-17665.html` | Erster Guide-Artikel im Zielformat (TOC, Lesezeit, Autor-Box, Schritte, Vergleichstabelle, FAQ) |

### Zu ändernde Dateien

| Dateipfad | Änderungen |
| --- | --- |
| `reference/Getmatic_website/style.css` | Neue Sektion `WISSEN` ergänzen (siehe Schritt 3) |
| `reference/Getmatic_website/index.html` | Nav-Punkt „Wissen" in Header + Footer + i18n-Objekt (DE/EN) ergänzen |
| `reference/Getmatic_website/impressum.html` | dito |
| `reference/Getmatic_website/datenschutz.html` | dito |
| `reference/Getmatic_website/docucontrol.html` | dito |
| `reference/Getmatic_website/einsatzbereich-medizin.html` | dito |
| `reference/Getmatic_website/einsatzbereich-transport.html` | dito |
| `reference/Getmatic_website/einsatzbereich-papier.html` | dito |
| `reference/Getmatic_website/einsatzbereich-getraenke.html` | dito |
| `reference/Getmatic_website/sitemap.xml` | 2 neue `<url>`-Einträge für die neuen Seiten |
| `context/current-data.md` | Neuer datierter Eintrag + „Noch hochzuladen"-Punkt |

### Zu löschende Dateien

Keine.

---

## Design-Entscheidungen

### Getroffene Schlüsselentscheidungen

1. **Deutscher Name „Wissen" statt „Blog"**: Passt zum bestehenden Muster deutscher URL-/Nav-Begriffe (`impressum.html`, `datenschutz.html`, „Leistungen", „Anlagen") und klingt für die B2B-Zielgruppe (Techniker/Entscheider in Industrie/Pharma) fachlich-seriöser als „Blog". Nav-Label DE: „Wissen", EN: „Guides".
2. **Hub-Seite (`wissen.html`) + Einzelartikel als separate Dateien**, keine Kategorie-/Tag-Unterseiten: Bei nur einem Artikel zum Start wäre ein Kategoriesystem Überengineering. Die Kachel-Struktur auf `wissen.html` ist aber so gebaut (Grid, wiederholbare Karte), dass weitere Artikel einfach als zusätzliche Kacheln + Dateien ergänzt werden.
3. **Erster Artikel-Thema: „Chargendokumentation für Sterilisationsprozesse: Anforderungen nach EN ISO 17665"** — statt eines generischen Automatisierungs-Themas. Begründung: knüpft inhaltlich direkt an `einsatzbereich-medizin.html` und `docucontrol.html` an (natürliche Cross-Links in beide Richtungen), zielt auf eine konkrete, suchvolumenstarke Nische (Pharma/Medizin-Entscheider, die nach Normanforderungen suchen), und lässt sich im Vaultwarden-Stil (Warum wichtig → Anforderungen → Schritt-für-Schritt-Ansatz → Vergleich manuell/automatisiert → Fehler → FAQ) sauber aufbauen, ohne interne DocuControl-Implementierungsdetails preiszugeben. **Das ist eine inhaltliche/strategische Entscheidung — siehe „Offene Fragen" unten, falls ein anderes Thema bevorzugt wird.**
4. **Kein JS-ScrollSpy für das Inhaltsverzeichnis**: Reine Anchor-Link-Liste (`<a href="#abschnitt">`) ohne aktives Hervorheben beim Scrollen. Begründung: Die Website nutzt bewusst minimales, robustes Vanilla-JS (nur Nav-Toggle, IntersectionObserver für Einblend-Animation, i18n-Switch); ein ScrollSpy wäre zusätzliche Komplexität ohne Kernnutzen für den MVP und erhöht das Risiko neuer Bugs in der Mobile-Navigation.
5. **Lesezeit als statischer Text, nicht per JS berechnet**: Konsistent mit dem Rest der Seite (kein dynamischer Content), einmalig beim Schreiben geschätzt (ca. 200 Wörter/Minute) und im HTML hart hinterlegt.
6. **Autor-Box mit CSS-Initialen-Avatar („TG") statt Foto**: Kein Foto-Asset aktuell vorbereitet; Initialen-Kreis in Teal ist konsistent mit dem bestehenden Logo-Farbschema und vermeidet eine zusätzliche Asset-Abhängigkeit vor Launch. Kann später leicht durch ein `<img>` ersetzt werden.
7. **Sidebar „Weitere Themen"/„Neueste Beiträge" bereits jetzt im Markup**, auch wenn zum Start nur 1 Artikel existiert: Zeigt vorerst nur einen Link zurück zur Übersicht (`wissen.html`) plus einen Platzhalter-Hinweis „Weitere Guides folgen". Struktur ist so gebaut, dass künftige Artikel einfach als zusätzliche `<li>`-Einträge ergänzt werden, ohne Layout-Änderung.
8. **BlogPosting-Schema (JSON-LD) auf der Artikelseite**, analog zum bestehenden `LocalBusiness`-Schema auf `index.html`: Verbessert die Chance auf Rich-Snippets in der Google-Suche (Autor, Datum), geringer Zusatzaufwand.
9. **Vergleichstabelle statt Code-Block als zentrales Struktur-Element im ersten Artikel**: Das Thema (Chargendokumentation) ist nicht code-lastig; eine Tabelle „Manuelle Dokumentation vs. automatisiert (DocuControl)" transportiert den Kernnutzen besser. Die `.artikel-code`-CSS-Klasse wird trotzdem mit angelegt (für künftige, technischere Artikel, z. B. SCL/TIA-Portal-Snippets), aber im ersten Artikel nicht zwingend verwendet.
10. **Nav-Punkt-Position „Wissen" zwischen „Kontakt" und „Impressum"**: Hält die inhaltlichen Nav-Punkte (Leistungen/Anlagen/Kontakt/Wissen) vor den rechtlichen (Impressum/Datenschutz) zusammen — konsistent mit der bestehenden Reihenfolge, bei der Impressum/Datenschutz bereits als letzte, rechtliche Gruppe stehen.

### Betrachtete Alternativen

- **Gemeinsames Header/Footer-Include-System (z. B. per JS `fetch()` oder Build-Step) einführen**, um die 10-fache Duplizierung bei jedem Nav-Update zu vermeiden: Verworfen für diesen Plan — wäre eine grundlegende Architektur-Änderung der gesamten (kein Framework, kein Build-Step) Website, betrifft alle bestehenden Seiten und ist deutlich größer als der angeforderte Blog-Bereich. Bleibt als mögliche spätere, separate Initiative (siehe Notizen).
- **Artikel-Content aus einer zentralen JSON/Markdown-Datei rendern**: Verworfen, da kein Build-Step/JS-Rendering-Layer existiert und die Website bewusst auf reinem statischem HTML pro Seite basiert (Konsistenz mit allen bisherigen Seiten).
- **Kategorien/Tags von Anfang an**: Verworfen als Überengineering bei einem Artikel zum Start (siehe Design-Entscheidung 2).
- **Homepage-Teaser-Sektion für „Wissen" auf `index.html`** (analog zur Anlagen-Sektion): Erwogen, aber bewusst **nicht** Teil dieses Plans — reine Nav-Verlinkung reicht für den Start, da der Bereich primär über Google-Suche (nicht Homepage-Klicks) gefunden werden soll. Siehe Notizen für mögliche spätere Ergänzung.

### Offene Fragen

1. **Thema des ersten Artikels**: Vorschlag in Design-Entscheidung 3 (Chargendokumentation/EN ISO 17665). Falls ein anderes Thema bevorzugt wird (z. B. „TIA Portal S7 Grundlagen", „Frequenzumrichter parametrieren", „Raspberry Pi in der Industrieautomatisierung"), bitte vor `/implement` in diesem Plan-Dokument ändern (Abschnitt „Schritt 5" unten enthält die volle Artikelgliederung, die dann entsprechend angepasst werden müsste).

---

## Schritt-für-Schritt-Aufgaben

### Schritt 1: CSS-Grundlagen recherchiert (bereits erledigt in dieser Planungsphase)

Bestehende Tokens/Patterns sind bekannt (siehe „Aktueller Zustand"). Kein zusätzlicher Recherche-Schritt bei `/implement` nötig.

---

### Schritt 2: Nav-Punkt „Wissen" auf allen 8 bestehenden Seiten ergänzen

In jeder der 8 Dateien (`index.html`, `impressum.html`, `datenschutz.html`, `docucontrol.html`, `einsatzbereich-medizin.html`, `einsatzbereich-transport.html`, `einsatzbereich-papier.html`, `einsatzbereich-getraenke.html`) an drei Stellen ergänzen:

**Aktionen:**

- Im `<nav class="main-nav">`-Block, **vor** dem Impressum-Link:
  ```html
  <li><a href="wissen.html" data-i18n="nav-wissen">Wissen</a></li>
  ```
  (Bei `index.html` zeigen die anderen Nav-Links auf Anker `#leistungen` etc.; bei den Unterseiten auf `index.html#leistungen`. Der neue Link ist auf allen Seiten identisch `wissen.html`, unabhängig vom Seitentyp.)
- Im `<nav class="footer-nav">`-Block, **vor** dem Impressum-Link, analog:
  ```html
  <a href="wissen.html" data-i18n="nav-wissen">Wissen</a>
  ```
- Im `translations`-Objekt jeder Seite, sowohl im `de`- als auch im `en`-Block, **direkt nach** dem `'nav-kontakt'`-Eintrag (bzw. an äquivalenter Stelle bei Seiten ohne `nav-kontakt`, z. B. Impressum/Datenschutz — dort nach `'nav-anlagen'`/vor `'nav-impressum'`):
  ```js
  'nav-wissen': 'Wissen',   // im de-Block
  ```
  ```js
  'nav-wissen': 'Guides',  // im en-Block
  ```
- Bei `impressum.html` und `datenschutz.html`: dort ist der Impressum- bzw. Datenschutz-Link im aktiven Zustand mit `style="color:#fff"` markiert — der neue „Wissen"-Link bekommt **keinen** Active-Style (nicht die aktuelle Seite).

**Betroffene Dateien:**

- `reference/Getmatic_website/index.html`
- `reference/Getmatic_website/impressum.html`
- `reference/Getmatic_website/datenschutz.html`
- `reference/Getmatic_website/docucontrol.html`
- `reference/Getmatic_website/einsatzbereich-medizin.html`
- `reference/Getmatic_website/einsatzbereich-transport.html`
- `reference/Getmatic_website/einsatzbereich-papier.html`
- `reference/Getmatic_website/einsatzbereich-getraenke.html`

---

### Schritt 3: Neue CSS-Sektion `WISSEN` in `style.css` anlegen

Am Ende der Datei (nach der letzten bestehenden Sektion) einen neuen, klar abgegrenzten Block einfügen, im Stil der bestehenden Abschnittskommentare (`/* ===== ... ===== */`).

**Aktionen:**

Folgende Regeln ergänzen (Werte konsistent mit bestehenden Tokens):

```css
/* =========================================
   WISSEN (Blog-/Guide-Bereich)
   ========================================= */

/* --- Übersichtsseite --- */
.wissen-page {
  padding: var(--space-6) 0 var(--space-7);
}

.wissen-header {
  max-width: 720px;
  margin-bottom: var(--space-5);
}

.wissen-header h1 {
  font-family: var(--font-head);
  font-size: clamp(2.2rem, 5vw, 3.5rem);
  font-weight: 800;
  color: var(--text-dark);
  letter-spacing: -1px;
}

.wissen-header p {
  font-size: 1.05rem;
  color: var(--text);
  line-height: 1.7;
  margin-top: var(--space-2);
}

.wissen-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
  gap: var(--space-4);
}

.wissen-card {
  display: block;
  background: var(--bg-white);
  border: 1px solid rgba(0,0,0,0.08);
  border-top: 3px solid var(--teal);
  border-radius: var(--radius);
  padding: var(--space-4);
  transition: box-shadow var(--transition), transform var(--transition);
}
.wissen-card:hover {
  box-shadow: var(--shadow);
  transform: translateY(-2px);
}

.wissen-card-label {
  font-size: 0.7rem;
  font-weight: 500;
  color: var(--teal);
  letter-spacing: 2px;
  text-transform: uppercase;
  margin-bottom: var(--space-1);
}

.wissen-card h3 {
  font-family: var(--font-head);
  font-size: 1.3rem;
  font-weight: 700;
  color: var(--text-dark);
  margin-bottom: var(--space-1);
  line-height: 1.2;
}

.wissen-card p {
  font-size: 0.9rem;
  color: var(--text-light);
  line-height: 1.6;
}

.wissen-card-meta {
  margin-top: var(--space-2);
  font-size: 0.78rem;
  color: var(--text-light);
}

/* --- Artikelseite: Layout --- */
.artikel-page {
  padding: var(--space-6) 0 var(--space-7);
}

.artikel-layout {
  display: grid;
  grid-template-columns: minmax(0, 1fr) 280px;
  gap: var(--space-5);
  align-items: start;
}

@media (max-width: 860px) {
  .artikel-layout {
    grid-template-columns: 1fr;
  }
}

.artikel-label {
  font-family: var(--font-body);
  font-size: 0.72rem;
  font-weight: 500;
  color: var(--teal);
  letter-spacing: 3px;
  text-transform: uppercase;
  margin-bottom: var(--space-1);
}

.artikel-header h1 {
  font-family: var(--font-head);
  font-size: clamp(2rem, 4.5vw, 3rem);
  font-weight: 800;
  color: var(--text-dark);
  letter-spacing: -1px;
  line-height: 1.15;
  margin-bottom: var(--space-2);
}

.artikel-meta {
  display: flex;
  flex-wrap: wrap;
  gap: var(--space-2);
  font-size: 0.85rem;
  color: var(--text-light);
  margin-bottom: var(--space-4);
  padding-bottom: var(--space-3);
  border-bottom: 1px solid rgba(0,0,0,0.08);
}

.artikel-meta span { display: inline-flex; align-items: center; gap: 6px; }

/* --- Inhaltsverzeichnis --- */
.artikel-toc {
  background: var(--bg-card);
  border: 1px solid rgba(0,0,0,0.07);
  border-radius: var(--radius);
  padding: var(--space-3);
  margin-bottom: var(--space-4);
}

.artikel-toc-label {
  font-size: 0.72rem;
  font-weight: 600;
  color: var(--teal);
  letter-spacing: 2px;
  text-transform: uppercase;
  margin-bottom: var(--space-2);
}

.artikel-toc ol {
  list-style: none;
  counter-reset: toc;
}

.artikel-toc li {
  counter-increment: toc;
  margin-bottom: 8px;
  font-size: 0.88rem;
}

.artikel-toc li a {
  color: var(--text);
  transition: color var(--transition);
}
.artikel-toc li a::before {
  content: counter(toc) ". ";
  color: var(--teal);
  font-weight: 700;
}
.artikel-toc li a:hover { color: var(--teal); }

/* --- Artikel-Fließtext --- */
.artikel-content h2 {
  font-family: var(--font-head);
  font-size: 1.6rem;
  font-weight: 800;
  color: var(--text-dark);
  margin: var(--space-5) 0 var(--space-2);
  scroll-margin-top: var(--space-3);
}

.artikel-content h3 {
  font-family: var(--font-head);
  font-size: 1.2rem;
  font-weight: 700;
  color: var(--text-dark);
  margin: var(--space-4) 0 var(--space-1);
}

.artikel-content p {
  font-size: 1rem;
  color: var(--text);
  line-height: 1.8;
  margin-bottom: var(--space-3);
}

.artikel-content ul,
.artikel-content ol {
  margin: 0 0 var(--space-3) var(--space-3);
  color: var(--text);
  line-height: 1.8;
}

.artikel-content li { margin-bottom: 6px; }

/* --- Code-Block (für künftige technischere Artikel) --- */
.artikel-code {
  background: var(--gray-dark);
  color: #e6e6e6;
  border-radius: var(--radius);
  padding: var(--space-3);
  font-family: 'Courier New', monospace;
  font-size: 0.85rem;
  line-height: 1.6;
  overflow-x: auto;
  margin-bottom: var(--space-3);
}

/* --- Vergleichstabelle --- */
.artikel-table-wrap {
  overflow-x: auto;
  margin-bottom: var(--space-4);
}

.artikel-table {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.9rem;
}

.artikel-table th,
.artikel-table td {
  text-align: left;
  padding: var(--space-2);
  border-bottom: 1px solid rgba(0,0,0,0.08);
}

.artikel-table th {
  font-family: var(--font-head);
  font-weight: 700;
  color: var(--text-dark);
  background: var(--bg-card);
}

.artikel-table td { color: var(--text); }

/* --- Autor-Box --- */
.artikel-author {
  display: flex;
  align-items: center;
  gap: var(--space-2);
  background: var(--bg-card);
  border-radius: var(--radius);
  padding: var(--space-3);
  margin: var(--space-5) 0;
}

.artikel-author-avatar {
  flex-shrink: 0;
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: var(--teal);
  color: #fff;
  font-family: var(--font-head);
  font-weight: 800;
  font-size: 1.1rem;
  display: flex;
  align-items: center;
  justify-content: center;
}

.artikel-author-name {
  font-family: var(--font-head);
  font-weight: 700;
  color: var(--text-dark);
}

.artikel-author-role {
  font-size: 0.82rem;
  color: var(--text-light);
}

/* --- Sidebar --- */
.artikel-sidebar-card {
  background: var(--bg-card);
  border: 1px solid rgba(0,0,0,0.07);
  border-radius: var(--radius);
  padding: var(--space-3);
  margin-bottom: var(--space-3);
}

.artikel-sidebar-label {
  font-size: 0.72rem;
  font-weight: 600;
  color: var(--teal);
  letter-spacing: 2px;
  text-transform: uppercase;
  margin-bottom: var(--space-2);
}

.artikel-sidebar-card ul { list-style: none; }
.artikel-sidebar-card li { margin-bottom: 8px; font-size: 0.88rem; }
.artikel-sidebar-card a { color: var(--text); transition: color var(--transition); }
.artikel-sidebar-card a:hover { color: var(--teal); }

.artikel-sidebar-hint {
  font-size: 0.82rem;
  color: var(--text-light);
  font-style: italic;
}

/* --- FAQ --- */
.artikel-faq-item {
  margin-bottom: var(--space-3);
  padding-bottom: var(--space-3);
  border-bottom: 1px solid rgba(0,0,0,0.07);
}
.artikel-faq-item:last-child { border-bottom: none; }
.artikel-faq-item h3 {
  font-family: var(--font-head);
  font-size: 1.05rem;
  font-weight: 700;
  color: var(--text-dark);
  margin-bottom: 6px;
}
.artikel-faq-item p {
  font-size: 0.92rem;
  color: var(--text);
  line-height: 1.7;
}
```

Auf `prefers-reduced-motion` muss nicht gesondert eingegangen werden — die neuen Elemente nutzen kein zusätzliches Bewegungs-/Animations-CSS über das bestehende `.animate-in`-Muster hinaus.

**Betroffene Dateien:**

- `reference/Getmatic_website/style.css`

---

### Schritt 4: `wissen.html` (Übersichtsseite) erstellen

Struktur exakt nach dem Muster von `einsatzbereich-medizin.html` (Meta-Tags, Header, Footer, i18n-Script), aber mit `<main class="wissen-page">` statt `<main class="einsatz-page">`.

**Aktionen:**

- Kopf-Metadaten: `<title>Wissen – GeTMatic Automatisierungstechnik</title>`, `description` ("Fachartikel und Praxis-Guides zu SPS-Programmierung, Automatisierung und Chargendokumentation von GeTMatic."), `canonical` auf `https://www.getmatic.de/wissen.html`, `og:title`/`og:description` analog, `og:image` auf `getmatic_logo.png`.
- Header/Footer 1:1 wie bei den anderen Seiten übernehmen, **inklusive** des in Schritt 2 ergänzten „Wissen"-Nav-Punkts (auf dieser Seite selbst zusätzlich mit `style="color:#fff"` als aktiver Zustand markiert, analog zum Muster bei Impressum/Datenschutz).
- Im `<main>`:
  ```html
  <main class="wissen-page" id="main-content">
    <div class="container">
      <div class="wissen-header animate-in">
        <div class="einsatz-label" data-i18n="wissen-label">Wissen</div>
        <h1 data-i18n="wissen-h1">Fachartikel &amp; Praxis-Guides</h1>
        <p data-i18n="wissen-intro">Hintergrundwissen zu SPS-Programmierung, Automatisierung und normkonformer Prozessdokumentation aus der Praxis von GeTMatic.</p>
      </div>
      <div class="wissen-grid">
        <a href="wissen-chargendokumentation-en-iso-17665.html" class="wissen-card animate-in" style="--d:.1s">
          <div class="wissen-card-label" data-i18n="wissen-card1-label">Pharma &amp; Medizin</div>
          <h3 data-i18n="wissen-card1-h">Chargendokumentation für Sterilisationsprozesse: Anforderungen nach EN ISO 17665</h3>
          <p data-i18n="wissen-card1-p">Was bei der normkonformen Dokumentation von Dampf-Autoklaven zu beachten ist – und wo manuelle Prozesse an ihre Grenzen stoßen.</p>
          <div class="wissen-card-meta" data-i18n="wissen-card1-meta">8 Min. Lesezeit</div>
        </a>
      </div>
    </div>
  </main>
  ```
- Footer + Script-Block 1:1 aus `einsatzbereich-medizin.html` übernehmen (Nav-Toggle, IntersectionObserver, i18n-Mechanik identisch), `translations`-Objekt um die auf dieser Seite verwendeten Keys ergänzen (`nav-wissen`, `wissen-label`, `wissen-h1`, `wissen-intro`, `wissen-card1-label`, `wissen-card1-h`, `wissen-card1-p`, `wissen-card1-meta`, plus alle Standard-Keys `skip-link`/`nav-*`/`foot-start`) — DE und EN.
- EN-Übersetzung `wissen-h1`: "Articles &amp; Practical Guides", `wissen-intro`: "Background knowledge on PLC programming, automation, and standards-compliant process documentation from GeTMatic's daily practice.", `wissen-card1-h`: "Batch Documentation for Sterilization Processes: Requirements under EN ISO 17665", `wissen-card1-p`: "What standards-compliant documentation of steam autoclaves requires — and where manual processes reach their limits.", `wissen-card1-meta`: "8 min read".

**Betroffene Dateien:**

- `reference/Getmatic_website/wissen.html` (neu)

---

### Schritt 5: Ersten Artikel `wissen-chargendokumentation-en-iso-17665.html` erstellen

Vollständiger Guide-Artikel im Zielformat. Gliederung (dient als verbindliche Struktur für `/implement` — Fließtext-Inhalte werden beim Implementieren in fachlich korrektem, für getmatic passendem Deutsch/Englisch verfasst, orientiert an den bereits vorhandenen Fakten aus `einsatzbereich-medizin.html` und `docucontrol.html`, ohne Erfindung neuer technischer Behauptungen):

**Aktionen:**

- Kopf-Metadaten: `<title>Chargendokumentation für Sterilisationsprozesse: EN ISO 17665 – GeTMatic</title>`, passende `description`/`keywords` (u. a. "EN ISO 17665", "Chargendokumentation Autoklav", "DocuControl"), `canonical` auf die neue URL, `og:*`-Tags, plus zusätzliches JSON-LD `BlogPosting`-Schema:
  ```html
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "BlogPosting",
    "headline": "Chargendokumentation für Sterilisationsprozesse: Anforderungen nach EN ISO 17665",
    "author": { "@type": "Person", "name": "Thomas Glander" },
    "publisher": { "@type": "Organization", "name": "GeTMatic Automatisierungstechnik", "logo": { "@type": "ImageObject", "url": "https://www.getmatic.de/getmatic_logo.png" } },
    "datePublished": "2026-09-17",
    "dateModified": "2026-09-17",
    "mainEntityOfPage": "https://www.getmatic.de/wissen-chargendokumentation-en-iso-17665.html"
  }
  </script>
  ```
- Header/Footer identisch zu den anderen Seiten (inkl. Wissen-Nav-Punkt, nicht aktiv markiert, da man sich auf einer Artikel- nicht der Übersichtsseite befindet).
- `<main class="artikel-page">` mit `<div class="artikel-layout">` (zwei Kinder: `<article>` links, `<aside>` rechts):
  - **Artikel-Header** (`.artikel-label` „Wissen · Pharma & Medizin", `<h1>`, `.artikel-meta` mit drei `<span>`s: Datum „17. September 2026", Lesezeit „8 Min. Lesezeit", Zurück-Link `<a href="wissen.html">← Zur Übersicht</a>`).
  - **Autor-Box** (`.artikel-author`: Initialen-Avatar „TG", Name „Thomas Glander", Rolle „Inhaber, GeTMatic Automatisierungstechnik").
  - **`.artikel-content`** mit folgenden `<h2>`-Abschnitten (je mit `id`-Attribut für die TOC-Anker):
    1. `#warum-wichtig` — Warum Chargendokumentation in Sterilisationsprozessen kritisch ist (Patientensicherheit, Nachweispflicht, Rückverfolgbarkeit).
    2. `#anforderungen-eniso17665` — Kernanforderungen der Norm EN ISO 17665 an dokumentierte Sterilisationszyklen (Druck, Temperatur, Zykluszeit, Nachweisbarkeit über den gesamten Prozess).
    3. `#manuell-vs-automatisiert` — Vergleichstabelle (`.artikel-table`) mit Spalten „Kriterium" / „Manuelle Dokumentation" / „Automatisierte Dokumentation (z. B. DocuControl)": Zeilen zu Nachvollziehbarkeit, Fehleranfälligkeit, Aufwand, Archivierung, Auditierbarkeit.
    4. `#typische-fehler` — 3–4 typische Stolperfallen bei rein manueller/papierbasierter Dokumentation (unvollständige Einträge, Medienbrüche, verzögerte Erfassung).
    5. `#loesungsansatz` — Wie eine automatisierte, autarke Lösung (Cross-Link zu `docucontrol.html`) diese Anforderungen adressiert, **ohne** Eingriff in Maschine/Klinik-Netzwerk (Fakt bereits aus `docucontrol.html`/`einsatzbereich-medizin.html` übernehmen, nicht neu erfinden).
    6. `#faq` — 3–4 FAQ-Einträge (`.artikel-faq-item`), z. B. „Ist eine automatisierte Dokumentation für jede Anlagengröße sinnvoll?", „Wie lange müssen Chargendaten aufbewahrt werden?", „Lässt sich eine bestehende Anlage nachrüsten?".
  - **Abschluss-CTA**: `<a href="index.html#kontakt" class="einsatz-cta animate-in" data-i18n="art1-cta">Projekt anfragen</a>` (bestehende `.einsatz-cta`-Klasse wiederverwenden, kein neues CSS nötig).
  - **`<aside>`** mit zwei `.artikel-sidebar-card`-Blöcken:
    - Inhaltsverzeichnis (`.artikel-toc`, `<ol>` mit 6 Ankern zu den obigen `<h2>`-IDs) — **oberhalb** des Fließtexts im DOM einfügen (vor `.artikel-content`, aber per Grid rechts positioniert) ODER als erstes Kind im `<aside>`, je nachdem was im finalen Markup natürlicher liegt; wichtig ist nur, dass es visuell in der rechten Spalte erscheint.
    - „Weitere Themen"-Karte (`.artikel-sidebar-card`): Link zurück zu `wissen.html` plus `.artikel-sidebar-hint`-Text „Weitere Guides folgen in Kürze.".
- Footer + Script-Block: identisches Grundgerüst (Nav-Toggle, IntersectionObserver, i18n). `translations`-Objekt enthält alle Standard-Keys plus artikelspezifische Keys für jeden Text-/Überschriften-Baustein (Konvention: Prefix `art1-`, z. B. `art1-h1`, `art1-meta-date`, `art1-meta-read`, `art1-toc-h1` … `art1-toc-h6`, `art1-sec1-h` … `art1-sec6-h` + zugehörige `-p`-Body-Texte, `art1-table-*`, `art1-faq1-h`/`-p` … `art1-faq4-h`/`-p`, `art1-cta`) — DE und EN vollständig, wie bei allen bisherigen Seiten.
- Cross-Link-Konsistenz: In `einsatzbereich-medizin.html` und `docucontrol.html` **nicht zwingend** ein Rück-Link auf den neuen Artikel ergänzen (kein harter Bestandteil dieses Plans, siehe Notizen) — der Artikel selbst verlinkt aktiv auf `docucontrol.html` und `einsatzbereich-medizin.html`/`index.html#kontakt`.

**Betroffene Dateien:**

- `reference/Getmatic_website/wissen-chargendokumentation-en-iso-17665.html` (neu)

---

### Schritt 6: `sitemap.xml` ergänzen

**Aktionen:**

- Vor dem schließenden `</urlset>` zwei neue Einträge ergänzen:
  ```xml
  <url>
    <loc>https://www.getmatic.de/wissen.html</loc>
    <changefreq>monthly</changefreq>
    <priority>0.6</priority>
  </url>
  <url>
    <loc>https://www.getmatic.de/wissen-chargendokumentation-en-iso-17665.html</loc>
    <changefreq>yearly</changefreq>
    <priority>0.6</priority>
  </url>
  ```

**Betroffene Dateien:**

- `reference/Getmatic_website/sitemap.xml`

---

### Schritt 7: Lokal testen

**Aktionen:**

- Lokalen Server aus `reference/Getmatic_website/` starten (`http-server -p 8080 -o "wissen.html"`, ggf. `MSYS_NO_PATHCONV=1` voranstellen, siehe bekannte Git-Bash-Falle in `context/current-data.md`).
- Playwright-Check auf Desktop- (z. B. 1440×900) und Mobile-Viewport (390×844):
  - Keine Konsolenfehler auf `wissen.html` und dem neuen Artikel.
  - `wissen.html`: Kachel-Link führt korrekt zum Artikel.
  - Artikel: TOC-Links springen zu den korrekten `id`-Ankern; `.artikel-layout` wird auf Desktop zweispaltig, auf Mobile einspaltig dargestellt; kein horizontaler Overflow (`document.documentElement.scrollWidth === window.innerWidth`).
  - „Wissen"-Nav-Punkt ist auf **allen 10 Seiten** vorhanden und verlinkt korrekt auf `wissen.html`.
  - DE/EN-Sprachumschalter auf `wissen.html` und dem Artikel: alle Texte wechseln korrekt, keine sichtbaren `data-i18n`-Rohkeys.
  - Regressions-Check auf 2–3 der geänderten Bestandsseiten (z. B. `index.html`, `docucontrol.html`): bestehende Nav-/Footer-Funktion weiterhin unverändert, kein Layout-Bruch durch den zusätzlichen Nav-Punkt (insbesondere mobile Hamburger-Nav).
- Screenshots der Übersichts- und Artikelseite (Desktop + Mobile) sichten.

**Betroffene Dateien:**

- Keine (Testschritt).

---

### Schritt 8: `context/current-data.md` aktualisieren

**Aktionen:**

- Neuen datierten Eintrag unter „Aktueller Stand" ergänzen, der den neuen Bereich, die neuen Dateien und den Upload-Status dokumentiert.
- Neuen Punkt unter „Offene Aufgaben / nächste Schritte" ergänzen: Re-Upload für `wissen.html`, `wissen-chargendokumentation-en-iso-17665.html`, `style.css`, plus alle 8 geänderten Bestandsseiten, `sitemap.xml`.
- Im Abschnitt „Struktur `reference/Getmatic_website/`" den neuen Seitentyp kurz ergänzen (analog zu den bisherigen Einträgen für Einsatzbereich-Unterseiten).

**Betroffene Dateien:**

- `context/current-data.md`

---

## Verbindungen & Abhängigkeiten

### Dateien, die diesen Bereich referenzieren

- Alle 8 bestehenden HTML-Seiten (Nav/Footer-Link, siehe Schritt 2).
- `sitemap.xml` (siehe Schritt 6).
- `docucontrol.html`/`einsatzbereich-medizin.html` werden vom neuen Artikel aus verlinkt (Cross-Link „Lösungsansatz"-Abschnitt), erhalten aber selbst keinen Pflicht-Rücklink in diesem Plan.

### Nötige Updates für Konsistenz

- `context/current-data.md` (Schritt 8) — einzige laufend gepflegte Statusdokumentation.
- Kein `CLAUDE.md`-Update nötig: Die Workspace-Struktur (Ordner/Commands) ändert sich nicht, nur der Website-Content innerhalb der bestehenden Struktur `reference/Getmatic_website/`.

### Auswirkungen auf bestehende Workflows

- Etabliert ein wiederverwendbares Seiten-/CSS-Pattern (`WISSEN`-Sektion) für künftige Artikel — weitere Guides lassen sich danach ohne erneute CSS-Arbeit als zusätzliche `wissen-<slug>.html`-Datei + Kachel auf `wissen.html` ergänzen.
- Erhöht die Duplizierungslast bei künftigen Nav-Änderungen (jetzt 10 statt 8 Dateien mit identischem Header/Footer) — bereits bestehendes, bekanntes Muster dieser Website, keine neue Problemklasse.
- Keine Auswirkung auf den internen Mitarbeiterbereich (`intern/`) oder bestehende Deployment-Workflows.

---

## Validierungs-Checkliste

- [ ] `wissen.html` existiert, zeigt Hub mit mindestens einer Artikel-Kachel
- [ ] `wissen-chargendokumentation-en-iso-17665.html` existiert, enthält alle 6 inhaltlichen Abschnitte + FAQ + funktionierendes Inhaltsverzeichnis
- [ ] Neue CSS-Sektion `WISSEN` in `style.css` vorhanden, keine bestehenden Regeln überschrieben/verändert
- [ ] „Wissen"-Nav-Punkt auf allen 10 Seiten (8 bestehende + 2 neue) vorhanden, Header **und** Footer, DE **und** EN
- [ ] `sitemap.xml` enthält beide neuen URLs
- [ ] Lokaler Playwright-Test: keine Konsolenfehler, kein horizontaler Overflow (Desktop + Mobile), TOC-Anker funktionieren, DE/EN-Umschaltung korrekt auf beiden neuen Seiten
- [ ] Regressionscheck: bestehende Seiten (Nav, Hamburger-Menü, i18n) funktionieren nach der Nav-Ergänzung weiterhin unverändert
- [ ] `context/current-data.md` enthält neuen Eintrag + Upload-Checkliste-Punkt

---

## Erfolgskriterien

Die Implementierung ist abgeschlossen, wenn:

1. `wissen.html` und der erste Artikel lokal fehlerfrei laufen, im bestehenden Design (Farben/Fonts/Spacing) konsistent wirken und DE/EN vollständig unterstützen.
2. Das Inhaltsverzeichnis, die Lesezeit-/Autor-Meta-Zeile, die Vergleichstabelle und die Sidebar wie im Vaultwarden-Vorbild funktional vorhanden sind (ohne 1:1-Kopie des Themas).
3. Der neue Bereich über die Hauptnavigation von jeder Seite aus erreichbar ist und in der Sitemap gelistet ist.
4. `context/current-data.md` den Stand korrekt dokumentiert, inkl. offenem Upload-Status.

---

## Notizen

- **Thema des ersten Artikels ist eine vorgeschlagene, aber änderbare Entscheidung** (siehe „Offene Fragen") — vor `/implement` prüfen, ob das Thema so passt.
- Perspektivisch sinnvolle, aber bewusst nicht in diesem Plan enthaltene Folgeschritte:
  - Homepage-Teaser-Sektion für „Wissen" auf `index.html` (eigener Plan, falls gewünscht).
  - Header/Footer-Include-Mechanismus, um die inzwischen 10-fache Duplizierung bei künftigen Nav-Änderungen zu reduzieren (größere Architektur-Entscheidung, separat zu planen).
  - Rück-Links von `docucontrol.html`/`einsatzbereich-medizin.html` auf den neuen Artikel (kleine Ergänzung, jederzeit nachrüstbar).
  - Weitere Artikel-Themen (TIA Portal/Step7-Grundlagen, Frequenzumrichter/Antriebstechnik, Raspberry Pi in der Industrie) — jeweils als eigene `wissen-<slug>.html`-Datei nach demselben CSS-Pattern.
- Deployment-Reihenfolge beim späteren Live-Upload (nicht Teil dieses Plans): alle 10 geänderten/neuen HTML-Dateien + `style.css` + `sitemap.xml` — wie immer **Überschreiben statt Resume** beim FTP-Upload verwenden (bekannte 1blu/FileZilla-Falle).
