# Plan: DocuControl-Label umbenennen & neuen Einsatzbereich "Abfüllung Druckflaschen" anlegen

**Erstellt:** 2026-07-09
**Status:** Umgesetzt mit Abweichung vom Plan (lokal getestet, Re-Upload noch offen)

> **Korrektur nach Implementierung:** Der Nutzer wollte keinen eigenen, siebten Einsatzbereich mit eigener Unterseite (wie in diesem Plan unter Schritt 3–7 beschrieben), sondern einen zusätzlichen Textabschnitt direkt auf `docucontrol.html`. Umgesetzt wurde stattdessen: ein neuer Abschnitt „Dokumentierte Prozesse" auf `docucontrol.html` (zwischen Einleitung und Vorteile-Kacheln) mit zwei Punkten (Sterilisation Autoklav / Abfüllung Sauerstoffflaschen). `einsatzbereich-druckflaschen.html`, `druckflaschen.svg`, die Kachel „07" und der Sitemap-Eintrag wurden erstellt und danach wieder entfernt. Schritte 3, 5 und 6 dieses Plans sind damit **nicht** die finale Umsetzung — siehe `context/current-data.md` (Eintrag 2026-07-09) für den tatsächlichen Stand.
**Anforderung:** "Eigenentwicklung" zu "Individuelle Lösungen" ändern + neue Einsatzbereich-Unterseite für die Abfüllung von Druckflaschen (Sauerstoffflaschen) mit DocuControl-Dokumentation erstellen.

---

## Überblick

### Was dieser Plan erreicht

DocuControl hat sich weiterentwickelt: Es dokumentiert nicht mehr nur Autoklaven-/Sterilisationsvorgänge, sondern jetzt auch die Abfüllung von Sauerstoffflaschen (Druckgasflaschen). Dieser Plan (1) benennt das Label "Eigenentwicklung" in "Individuelle Lösungen" um (passender, da DocuControl längst über eine reine Eigenentwicklung hinausgewachsen ist und projektspezifisch an neue Prozesse angepasst wird) und (2) legt eine siebte Einsatzbereich-Unterseite `einsatzbereich-druckflaschen.html` an, die den Abfüllprozess von Druckgasflaschen und dessen Dokumentation über DocuControl beschreibt.

### Warum das wichtig ist

Die Website muss das tatsächliche Leistungsangebot von getmatic aktuell und vollständig abbilden (strategy.md: "Leistungsangebot klar kommunizieren"). Ein neuer, real existierender Anwendungsfall von DocuControl fehlt aktuell komplett auf der Website — potenzielle Kunden aus dem Bereich Gaseabfüllung/Medizintechnik finden ihren Anwendungsfall sonst nicht.

---

## Aktueller Zustand

### Relevante bestehende Struktur

- `reference/Getmatic_website/index.html` — Hero-Slide 2 (`hero2-tag`: "Eigenentwicklung — DocuControl") und Anlagen-Sektion (`#anlagen`) mit 6 Karten (`anl1`–`anl6`), Karte 06 verlinkt auf `docucontrol.html`
- `reference/Getmatic_website/docucontrol.html` — Produktseite, Label `doc-label`: "Eigenentwicklung" (DE) / "In-House Product" (EN), Intro-Text `doc-intro` erwähnt bisher nur Sterilisationsprozesse
- `reference/Getmatic_website/einsatzbereich-medizin.html` — dient als 1:1-Vorlage für Struktur/Markup neuer Einsatzbereich-Seiten (Header, `.einsatz-hero`, `.einsatz-header`, `.einsatz-content`, `.einsatz-crosslink`, `.einsatz-vorteile`, `.einsatz-cta`, Footer, i18n-Skript-Block)
- `reference/Getmatic_website/sterilisation.svg` — Beispiel für Stil einer Einsatzbereich-Illustration (400×760 viewBox, Gradients, `filter#shadow`/`filter#glow`)
- `reference/Getmatic_website/sitemap.xml` — listet alle 7 aktuellen Seiten
- `reference/Getmatic_website/style.css` — Klassen `.einsatz-*`, `.anlagen-*` bereits vorhanden, keine neuen CSS-Klassen nötig

### Lücken oder Probleme, die adressiert werden

- Das Label "Eigenentwicklung" ist ungenau geworden — DocuControl wird zunehmend projektspezifisch angepasst, "Individuelle Lösungen" trifft es besser
- Der neue Anwendungsfall "Abfüllung Druckflaschen" (Sauerstoffflaschen) hat noch keine eigene Seite und ist auf der Startseite nicht als Einsatzbereich sichtbar
- `docucontrol.html`-Intro und die Kachel-Beschreibung (`anl6-p`) erwähnen ausschließlich Sterilisationsprozesse — nicht mehr vollständig, seit DocuControl auch Abfüllprozesse dokumentiert

---

## Vorgeschlagene Änderungen

### Zusammenfassung der Änderungen

- Label-Umbenennung "Eigenentwicklung" → "Individuelle Lösungen" in `index.html` (Hero-Tag) und `docucontrol.html` (`doc-label`), inkl. passender EN-Anpassung
- Textbroadening: `doc-intro` (docucontrol.html) und `anl6-p` (index.html, Kachel 06) erwähnen künftig sowohl Sterilisationsprozesse als auch Druckgasflaschen-Abfüllung
- Neue Datei `druckflaschen.svg` — Illustration im Stil der bestehenden Einsatzbereich-SVGs (Sauerstoffflaschen an einer Abfüllstation)
- Neue Datei `einsatzbereich-druckflaschen.html` — siebte Einsatzbereich-Seite nach dem Muster von `einsatzbereich-medizin.html`, mit Crosslink zu `docucontrol.html`
- Neue Anlagen-Karte "07" in `index.html` (`#anlagen`-Sektion), verlinkt auf die neue Seite
- Neuer Eintrag in `sitemap.xml`

### Neue Dateien erstellen

| Dateipfad | Zweck |
| --- | --- |
| `reference/Getmatic_website/druckflaschen.svg` | Hero-/Kachel-Illustration für den neuen Einsatzbereich (Sauerstoffflaschen-Abfüllstation), Stil analog zu `sterilisation.svg` |
| `reference/Getmatic_website/einsatzbereich-druckflaschen.html` | Neue Einsatzbereich-Unterseite "Abfüllung Druckflaschen", DE/EN, eigene SEO-Metadaten, Crosslink zu DocuControl |

### Zu ändernde Dateien

| Dateipfad | Änderungen |
| --- | --- |
| `reference/Getmatic_website/docucontrol.html` | `doc-label` DE "Eigenentwicklung" → "Individuelle Lösungen"; EN "In-House Product" → "Custom Solutions"; `doc-intro` DE+EN erweitern um Erwähnung der Druckgasflaschen-Abfüllung |
| `reference/Getmatic_website/index.html` | `hero2-tag` DE+EN umbenennen; `anl6-p` DE+EN erweitern (DocuControl dokumentiert jetzt auch Abfüllprozesse); neue Anlagen-Karte "07" (Markup + `anl7-h`/`anl7-p`/`anl7-link` DE+EN) in der `#anlagen`-Liste ergänzen, verlinkt auf `einsatzbereich-druckflaschen.html` und `druckflaschen.svg` |
| `reference/Getmatic_website/sitemap.xml` | Neuen `<url>`-Eintrag für `einsatzbereich-druckflaschen.html` ergänzen (priority 0.6, analog zu den anderen Einsatzbereich-Seiten) |
| `context/current-data.md` | Änderung dokumentieren (neuer Einsatzbereich, Label-Umbenennung, offene Aufgabe: Re-Upload) |

### Zu löschende Dateien (falls vorhanden)

Keine.

---

## Design-Entscheidungen

### Getroffene Schlüsselentscheidungen

1. **Neue Karte statt Umnummerierung:** Die neue Karte wird als "07" ans Ende der Liste angehängt (nach der DocuControl-Karte "06"), statt bestehende Karten 01–06 umzunummerieren. Grund: minimaler Diff, keine Kollisionsgefahr mit bestehenden i18n-Keys/Screenshots/Referenzen anderswo.
2. **EN-Label ebenfalls angepasst:** "In-House Product" wird zu "Custom Solutions", damit DE/EN inhaltlich konsistent bleiben, obwohl die Anforderung nur den deutschen Begriff nannte — sonst würden beide Sprachversionen auseinanderlaufen.
3. **Textbroadening von `doc-intro`/`anl6-p`:** Da der Nutzer explizit sagt, DocuControl dokumentiere jetzt auch Sauerstoffflaschen-Abfüllung, wird das auf der Produktseite selbst und in der Kachel-Beschreibung nachgezogen — sonst widerspricht sich die Website (Produktseite erwähnt nur Sterilisation, neue Unterseite verlinkt aber genau dorthin mit Abfüllbezug).
4. **Neue eigene SVG-Illustration statt Wiederverwendung:** Keine bestehende Grafik zeigt Gasflaschen/Abfüllung — wird neu erstellt, im gleichen visuellen Stil (Gradients, Bildformat 400×760) wie die bestehenden Einsatzbereich-SVGs, damit die Kachel-Reihe optisch einheitlich bleibt.
5. **Kein neuer CSS ­nötig:** Die Seite nutzt ausschließlich bestehende `.einsatz-*`-Klassen aus `style.css` (identisch zum Muster der 5 bestehenden Einsatzbereich-Seiten).

### Betrachtete Alternativen

- Bestehende Karte 05 ("Medizin & Sterilisation") um Druckflaschen erweitern statt eigener Seite — verworfen, da der Nutzer explizit einen *neuen, eigenen* Einsatzbereich wünscht und die Themen (Autoklav vs. Gasabfüllung) fachlich unterschiedlich genug sind, um getrennte Seiten zu rechtfertigen.
- Karte 06 (Dokumentation/DocuControl) direkt um Druckflaschen-Inhalt erweitern statt neue Karte 07 — verworfen, da Karte 06 das *Produkt* DocuControl repräsentiert, nicht einen *Einsatzbereich*; die neue Karte 07 repräsentiert den Einsatzbereich (Branche/Prozess), analog zu 01–05.

### Offene Fragen

- Exakter Seitentitel/Wortlaut: Plan verwendet "Abfüllung Druckflaschen" (H1) bzw. "Druckflaschen-Abfüllung" (Kachel-Titel) als Arbeitstitel, angelehnt an "Sauerstoffflaschen". Falls der Nutzer einen anderen Fachbegriff bevorzugt (z. B. "Druckgasflaschen-Abfüllung", "Sauerstoffabfüllung"), bei der Umsetzung entsprechend anpassen — inhaltlich unkritisch, reine Wortwahl.
- Technische Detailtiefe zum Abfüllprozess (z. B. genaue Druckwerte, Normen wie z. B. medizinischer Sauerstoff nach Ph. Eur. / DIN EN ISO 7396) liegt dem Assistenten nicht vor — Texte werden auf Basis des bestehenden Site-Tons (fachlich fundiert, aber nicht über-spezifisch) plausibel formuliert, ähnlich der bestehenden Seiten. Bei Bedarf nach Implementierung fachlich gegenlesen lassen.

---

## Schritt-für-Schritt-Aufgaben

### Schritt 1: Label "Eigenentwicklung" → "Individuelle Lösungen" umbenennen

**Aktionen:**

- In `docucontrol.html`: `'doc-label': 'Eigenentwicklung'` → `'doc-label': 'Individuelle Lösungen'` (DE-Übersetzungsobjekt) sowie das sichtbare `<div class="einsatz-label" data-i18n="doc-label">Eigenentwicklung</div>` im Markup anpassen
- In `docucontrol.html`: EN-Übersetzungsobjekt `'doc-label': 'In-House Product'` → `'doc-label': 'Custom Solutions'`
- In `index.html`: `'hero2-tag': 'Eigenentwicklung &mdash; DocuControl'` → `'hero2-tag': 'Individuelle Lösungen &mdash; DocuControl'` (DE) sowie das sichtbare `<p class="hero-tag" data-i18n="hero2-tag">Eigenentwicklung &mdash; DocuControl</p>` im Markup
- In `index.html`: EN-Pendant von `hero2-tag` suchen und analog auf "Custom Solutions — DocuControl" anpassen

**Betroffene Dateien:**

- `reference/Getmatic_website/docucontrol.html`
- `reference/Getmatic_website/index.html`

---

### Schritt 2: `doc-intro` und `anl6-p` um Druckflaschen-Abfüllung erweitern

**Aktionen:**

- In `docucontrol.html`, `doc-intro` (DE) neu formulieren, z. B.: "DocuControl zeichnet Chargen- und Prozessdaten vollautomatisch auf — von Sterilisationsprozessen im Dampf-Autoklav nach EN&nbsp;ISO&nbsp;17665 bis zur Abfüllung von Druckgasflaschen — und dokumentiert sie normkonform als durchsuchbare PDF-Chargendokumentation mit Druck- und Temperaturkurven sowie als Web-Dashboard. Der entscheidende Vorteil: <strong>DocuControl benötigt dafür weder einen Eingriff in die Maschine noch in Ihr Netzwerk.</strong>" — EN-Pendant analog übersetzen
- In `index.html`, `anl6-p` (DE) erweitern, z. B.: "Automatische Erfassung und Auswertung von Sterilisations- und Abfüllprozessen. Messwerte werden vollständig aufgezeichnet und normkonform als PDF aufbereitet — bereit zur Weitergabe an den Kunden und zur Archivierung." — EN-Pendant analog anpassen
- Sichtbare `<p data-i18n="doc-intro">…</p>` bzw. `<p data-i18n="anl6-p">…</p>` im jeweiligen Markup mit demselben DE-Text synchron halten (data-i18n überschreibt den Inhalt zwar zur Laufzeit, der Anfangszustand sollte trotzdem konsistent sein)

**Betroffene Dateien:**

- `reference/Getmatic_website/docucontrol.html`
- `reference/Getmatic_website/index.html`

---

### Schritt 3: Neue Illustration `druckflaschen.svg` erstellen

**Aktionen:**

- Neue SVG-Datei im Stil von `sterilisation.svg` anlegen: viewBox `0 0 400 760`, Hintergrund-Gradient, 2–3 stilisierte Druckgasflaschen (schlanke Zylinder mit Ventilkopf/Flaschenhals, in typischer Sauerstoffflaschen-Farbgebung — weiß/hellblau) an einer schematischen Abfüllstation/Manifold (Verteilerbalken mit Anschlussschläuchen), `role="img"` + `aria-label="Abfüllung Druckgasflaschen"`
- Gradients/Filter (`filter#shadow`, ggf. `filter#glow`) analog zu den bestehenden Illustrationen wiederverwenden, damit der visuelle Stil der Kachel-Reihe einheitlich bleibt

**Betroffene Dateien:**

- `reference/Getmatic_website/druckflaschen.svg` (neu)

---

### Schritt 4: Neue Seite `einsatzbereich-druckflaschen.html` erstellen

**Aktionen:**

- Datei `einsatzbereich-medizin.html` komplett als Vorlage kopieren und anpassen:
  - `<title>`, `meta description`, `meta keywords`, `og:title`, `og:description`, `og:image` (→ `druckflaschen.svg`), `canonical` auf `https://www.getmatic.de/einsatzbereich-druckflaschen.html` anpassen
  - `.einsatz-hero` `--img` auf `url('druckflaschen.svg')`, `aria-label` anpassen
  - `einsatz-label` bleibt "Einsatzbereich" (wie bei allen anderen Einsatzbereich-Seiten, NICHT "Individuelle Lösungen" — das Label gilt nur für die Produktseite `docucontrol.html`)
  - `<h1>` "Abfüllung Druckflaschen" (Arbeitstitel, siehe offene Frage)
  - Inhalts-Absätze (`p1`/`p2`, neue i18n-Keys z. B. `df-p1`/`df-p2`): Prozessbeschreibung — SPS-gesteuerte Abfüllung von Sauerstoffflaschen (medizinischer/industrieller Sauerstoff), Druck- und Füllstandsüberwachung, Sicherheitsanforderungen (Explosionsschutz, Grenzwertüberwachung), Step 7/TIA-Portal-Steuerung der Abfüllstationen
  - `einsatz-crosslink` (neuer Key `df-crosslink`): Verweis auf DocuControl, das den Abfüllprozess jetzt ebenfalls automatisch und normkonform dokumentiert — analog zum bestehenden Crosslink-Text auf `einsatzbereich-medizin.html`, aber auf Abfüllvorgänge statt Sterilisationschargen bezogen
  - 4 `einsatz-vorteil`-Kacheln (neue Keys `df-v1-h`/`df-v1-p` … `df-v4-h`/`df-v4-p`), z. B.: Druck-/Füllstandsüberwachung, Explosionsschutz-konforme Steuerung, Prozesssicherheit/Grenzwertkontrolle, Dokumentation über DocuControl
  - `einsatz-cta` (neuer Key `df-cta`): "Projekt anfragen" (wie bei den anderen Seiten)
  - Alle neuen i18n-Keys sowohl im `de`- als auch im `en`-Objekt des Übersetzungs-Scripts ergänzen (Namensschema `df-*`, analog zu `med-*` auf der Medizin-Seite)
  - Restliches Markup (Header, Nav, Footer, Sprach-Switch, Scroll-Animation-Script) unverändert aus der Vorlage übernehmen

**Betroffene Dateien:**

- `reference/Getmatic_website/einsatzbereich-druckflaschen.html` (neu)

---

### Schritt 5: Neue Anlagen-Karte "07" in `index.html` ergänzen

**Aktionen:**

- Nach der bestehenden Karte "06" (DocuControl/Dokumentation) in der `.anlagen-list` ein neues `<article class="anlagen-item animate-in" style="--d:.7s">` einfügen:
  - Bild: `<a href="einsatzbereich-druckflaschen.html" class="anlagen-img" style="--img: url('druckflaschen.svg')" role="img" aria-label="Abfüllung Druckgasflaschen Sauerstoffflaschen"></a>`
  - `<span class="anlagen-num">07</span>`
  - `<h3 data-i18n="anl7-h">Abfüllung Druckflaschen</h3>`
  - `<p data-i18n="anl7-p">…kurze Prozessbeschreibung + Dokumentation via DocuControl…</p>`
  - `<a href="einsatzbereich-druckflaschen.html" class="anlagen-link" data-i18n="anl7-link">Mehr erfahren &#8594;</a>`
- Neue i18n-Keys `anl7-h`/`anl7-p`/`anl7-link` in DE- und EN-Übersetzungsobjekt ergänzen (Schema analog zu `anl1`–`anl6`)

**Betroffene Dateien:**

- `reference/Getmatic_website/index.html`

---

### Schritt 6: `sitemap.xml` aktualisieren

**Aktionen:**

- Neuen `<url>`-Block für `https://www.getmatic.de/einsatzbereich-druckflaschen.html` ergänzen, `changefreq: monthly`, `priority: 0.6` (analog zu den anderen Einsatzbereich-Seiten)

**Betroffene Dateien:**

- `reference/Getmatic_website/sitemap.xml`

---

### Schritt 7: Lokale Prüfung

**Aktionen:**

- Lokalen Server starten (`http-server` o. ä., siehe Deployment-Hinweise in `current-data.md` bzgl. `file://`-CORS-Falle) und `index.html` sowie `einsatzbereich-druckflaschen.html` im Browser prüfen: Sprachumschalter DE/EN, neue Karte 07 sichtbar + verlinkt, neue Seite lädt korrekt inkl. SVG, Crosslink zu `docucontrol.html` funktioniert, `docucontrol.html`-Änderungen (Label + Intro) korrekt
- HTML-Struktur der neuen Seite gegen `einsatzbereich-medizin.html` gegenprüfen (keine übernommenen medizin-spezifischen Reste wie falsche i18n-Keys oder Restformulierungen)

**Betroffene Dateien:** keine (Validierung)

---

### Schritt 8: `context/current-data.md` aktualisieren

**Aktionen:**

- Neuen Eintrag mit heutigem Datum ergänzen: Label-Umbenennung + neuer Einsatzbereich "Abfüllung Druckflaschen", Liste der neuen/geänderten Dateien
- Unter "Offene Aufgaben / nächste Schritte" ergänzen: Re-Upload der geänderten/neuen Dateien (`index.html`, `docucontrol.html`, `sitemap.xml`, `druckflaschen.svg`, `einsatzbereich-druckflaschen.html`) noch offen
- Abschnitt "Struktur `reference/Getmatic_website/`" um die zwei neuen Dateien ergänzen

**Betroffene Dateien:**

- `context/current-data.md`

---

## Verbindungen & Abhängigkeiten

### Dateien, die diesen Bereich referenzieren

- `index.html` verlinkt auf alle Einsatzbereich-Seiten und auf `docucontrol.html`
- Jede bestehende `einsatzbereich-*.html` verlinkt zurück auf `index.html#anlagen`
- `sitemap.xml`/`robots.txt` referenzieren alle Seiten der Domain (robots.txt braucht keine Änderung — sperrt nur `/intern/`)

### Nötige Updates für Konsistenz

- `context/current-data.md` (Abschnitt "Struktur `reference/Getmatic_website/`" und "Offene Aufgaben") — siehe Schritt 8
- Kein Bedarf, `CLAUDE.md` anzupassen — Workspace-Struktur selbst ändert sich nicht

### Auswirkungen auf bestehende Workflows

- Kein Einfluss auf `intern/`-Bereich oder bestehende Commands
- Re-Upload-Workflow wie gehabt: **Überschreiben, nicht Fortsetzen/Resume** beim FTP-Upload (bekannte 1blu/FileZilla-Falle, siehe `current-data.md`)

---

## Validierungs-Checkliste

- [ ] "Eigenentwicklung" ist an beiden Stellen (index.html Hero-Tag, docucontrol.html Label) durch "Individuelle Lösungen" ersetzt (DE), EN-Pendant konsistent anpasst
- [ ] `doc-intro` und `anl6-p` erwähnen sowohl Sterilisation als auch Druckgasflaschen-Abfüllung (DE+EN)
- [ ] `druckflaschen.svg` existiert, lädt fehlerfrei, visueller Stil passt zu den anderen Einsatzbereich-Illustrationen
- [ ] `einsatzbereich-druckflaschen.html` existiert, folgt 1:1 dem Strukturmuster der bestehenden Einsatzbereich-Seiten, DE/EN vollständig übersetzt, eigene SEO-Metadaten gesetzt
- [ ] Neue Karte "07" auf `index.html` sichtbar, verlinkt korrekt auf neue Seite und SVG
- [ ] Crosslink von der neuen Seite zu `docucontrol.html` funktioniert und formuliert den Abfüllbezug korrekt
- [ ] `sitemap.xml` enthält neuen Eintrag
- [ ] Lokaler Test im Browser (DE/EN-Umschaltung, alle Links, Responsive/Mobile) ohne Konsolenfehler
- [ ] `context/current-data.md` aktualisiert (neuer Stand + offene Re-Upload-Aufgabe)

---

## Erfolgskriterien

1. Die Website nennt an keiner Stelle mehr "Eigenentwicklung" für DocuControl, stattdessen konsistent "Individuelle Lösungen" (DE) / "Custom Solutions" (EN)
2. Ein siebter Einsatzbereich "Abfüllung Druckflaschen" ist vollständig als eigene Unterseite umgesetzt, von der Startseite aus erreichbar und mit DocuControl verlinkt
3. DocuControl-Produktseite und Startseiten-Kachel spiegeln den erweiterten Funktionsumfang (Sterilisation **und** Druckgasflaschen-Abfüllung) korrekt wider
4. Alle Änderungen sind lokal getestet, aber **noch nicht live hochgeladen** (Re-Upload bleibt expliziter, separater nächster Schritt)

---

## Notizen

- Der eigentliche Live-Upload ist bewusst **nicht** Teil dieses Plans (kein direkter Serverzugriff für den Assistenten, siehe Muster beim internen Mitarbeiterbereich) — wird als offene Aufgabe in `current-data.md` vermerkt
- Fachliche Detailtiefe zum Sauerstoffflaschen-Abfüllprozess ist eine plausible Näherung auf Basis des bestehenden Site-Tons; bei Bedarf vom Nutzer fachlich gegenlesen lassen, da er als Elektrotechniker/Automatisierer die Prozessdetails am besten kennt
