# Plan: Website-Verschönerung — Dropdown-Navigation, mehr Inhalt, visuelles Redesign

**Erstellt:** 2026-09-17
**Status:** Implementiert
**Anforderung:** Dropdown-Untermenü für „Anlagen" in der Hauptnavigation, inhaltlicher Ausbau bestehender Seiten (Startseite, 3 Einsatzbereich-Unterseiten, DocuControl) und spürbares visuelles Redesign einzelner Bereiche (Hero, Anlagen-Kacheln, Leistungen-Karten) — Grundfarben und Fonts bleiben unverändert.

---

## Überblick

### Was dieser Plan erreicht

Die Website bekommt drei zusammenhängende Verbesserungen: (1) eine Dropdown-Navigation unter „Anlagen", die alle 5 Einsatzbereiche direkt zugänglich macht statt nur per Anker-Sprung zur Startseite, (2) mehr fachlichen Inhalt auf der Startseite (neue Leistung, neue Vertrauens-Sektion) und den drei am wenigsten ausgebauten Einsatzbereich-Seiten sowie einen kleinen FAQ-Block auf DocuControl, (3) ein spürbar aufgewertetes visuelles Erscheinungsbild bei Hero-Slider, Anlagen-Kacheln und Leistungen-Karten — ohne die bestehende Teal/Grau-Farbpalette oder die Schriften (Barlow/Barlow Condensed) zu verändern.

### Warum das wichtig ist

Passt direkt auf die drei strategischen Prioritäten aus `context/strategy.md`: laufende Optimierung, klare Kommunikation des Leistungsangebots (inkl. Raspberry Pi/Embedded, das bisher keine eigene Leistungs-Kachel hat) und Vertrauensaufbau bei Entscheidern in Industrie/Pharma/Medizin. Die Dropdown-Navigation verkürzt den Weg zu den Branchenseiten von „scrollen auf der Startseite" auf „ein Klick von jeder Seite aus" — das senkt Absprungrate und erleichtert die Orientierung für Erstbesucher, die über eine Unterseite (z. B. per Google) einsteigen.

---

## Aktueller Zustand

### Relevante bestehende Struktur

- 8 HTML-Seiten mit identisch dupliziertem Header/Footer-Markup (kein Include-Mechanismus): `index.html`, `impressum.html`, `datenschutz.html`, `docucontrol.html`, `einsatzbereich-{medizin,transport,papier,getraenke}.html`.
- Hauptnavigation aktuell flach: Leistungen (Anker), Anlagen (Anker), Kontakt (Anker), Impressum, Datenschutz — auf Unterseiten zeigen die Anker auf `index.html#...`.
- `index.html` Anlagen-Sektion listet die 5 Einsatzbereiche in dieser Reihenfolge (bestätigt in `index.html`, Stand nach Umstrukturierung 2026-09-16): 01 Getränkeverbundkarton (`einsatzbereich-getraenke.html`), 02 Dokumentation (`docucontrol.html`), 03 Medizin & Sterilisation (`einsatzbereich-medizin.html`), 04 Papierindustrie (`einsatzbereich-papier.html`), 05 Transport (`einsatzbereich-transport.html`).
- CSS-Design-Tokens in `style.css` (Zeilen 23–60): Farben `--teal`/`--teal-light`/`--teal-dark`/`--gray-header`/`--gray-dark`/`--text`/`--text-light`/`--text-dark`/`--bg`/`--bg-card`/`--bg-white`, Spacing `--space-1`(8px) bis `--space-7`(96px), `--font-head`(Barlow Condensed)/`--font-body`(Barlow), `--radius`, `--shadow`/`--shadow-lg`, `--transition`.
- Bestehendes, bereits bewährtes CSS-Muster für alternierende Zweispalten-Layouts: `.einsatz-screen:nth-child(even) { direction: rtl; } .einsatz-screen:nth-child(even) .screen-text { direction: ltr; }` (docucontrol.html) — dasselbe Prinzip wird für die Anlagen-Kacheln wiederverwendet (siehe Schritt 5).
- Mobile Navigation: `.main-nav` wird bei ≤768px zu einem Hamburger-Dropdown (`.nav-toggle`), reines CSS + minimales JS (`nav.classList.toggle('open')`), keine verschachtelten Untermenüs bisher vorhanden.
- Leistungen-Sektion (`index.html`) hat aktuell 4 Karten: SPS & Steuerung, Antriebstechnik, Visualisierung, Komplettlösungen. Raspberry Pi/Embedded wird in `context/business-info.md` als Kernleistung genannt, taucht aber auf der Website bisher nur indirekt (DocuControl „Individuelle Lösungen") auf.
- Einsatzbereich-Seiten `medizin`/`transport`/`papier` haben je 2 Absätze `.einsatz-content` + 4 `.einsatz-vorteil`-Karten. `einsatzbereich-getraenke.html` wurde am 2026-09-16 bereits stark ausgebaut (Prozess-Sektion mit 4 Videos) und `docucontrol.html` ist mit Intro, „Dokumentierte Prozesse", 4 Vorteilen, 3D-Modell und 3 Praxis-Screens bereits die inhaltsreichste Unterseite.
- Übersetzungsschlüssel-Präfixe je Seite (bestätigt durch Lesen der Dateien): `med-*` (Medizin), `tra-*` (Transport), `pap-*` (Papier), `doc-*` (DocuControl), `anl1-h` bis `anl6-h`/`-p`/`-link` (Anlagen-Kacheln auf `index.html`), `leis1-h` bis `leis4-h`/`-p` (Leistungen auf `index.html`).
- Aktuell uncommittete Änderungen im Repo (Datenschutz-Nav-Rollout vom 2026-09-16/17) zeigen das etablierte Muster, wie ein neuer Übersetzungsschlüssel + Nav-Link konsistent auf allen 8 Seiten ergänzt wird (Referenz für Schritt 2).

### Lücken oder Probleme, die adressiert werden

- „Anlagen" ist nur ein Anker-Link zur Startseite — von einer Unterseite aus muss man erst zur Startseite navigieren und dann scrollen, um zu einem anderen Einsatzbereich zu wechseln.
- Raspberry Pi/Embedded-Systeme sind eine dokumentierte Kernleistung (`context/business-info.md`), tauchen aber nicht als eigene Leistungs-Kachel auf der Startseite auf.
- Es gibt keine dedizierte Vertrauens-/„Warum GeTMatic"-Sektion, obwohl Vertrauensaufbau explizit als strategische Priorität dokumentiert ist (`context/strategy.md`, Priorität 3).
- Die Einsatzbereich-Seiten Medizin, Transport und Papier sind inhaltlich vergleichsweise knapp (nur 2 Absätze Fließtext) im Vergleich zu DocuControl und der frisch ausgebauten Getränke-Seite.
- Hero-Slider, Anlagen-Kacheln und Leistungen-Karten sind funktional und sauber, aber optisch seit längerem unverändert — kein Bewegungs-/Layout-Element hebt sie visuell hervor (abgesehen vom bereits vorhandenen Fade-in beim Scrollen).

---

## Vorgeschlagene Änderungen

### Zusammenfassung der Änderungen

- **Navigation:** Neues CSS-only Dropdown-Untermenü unter „Anlagen" (Hover/Focus auf Desktop, immer sichtbar eingerückt in der mobilen Hamburger-Nav) mit Links zu allen 5 Einsatzbereichen + „Alle Einsatzbereiche". Kein JavaScript nötig. Auf allen 8 Seiten identisch ergänzt.
- **Inhalt Startseite:** 5. Leistungs-Karte „Raspberry Pi & Embedded-Systeme"; neue Sektion „Warum GeTMatic" (4 Vertrauens-Punkte) zwischen Leistungen und Anlagen.
- **Inhalt Unterseiten:** Je ein zusätzlicher, fachlich fundierter Absatz auf `einsatzbereich-medizin.html`, `-transport.html`, `-papier.html`; neuer kompakter FAQ-Block (3 Fragen) auf `docucontrol.html`, basierend auf bereits auf der Seite etablierten Fakten (keine neuen, unbestätigten Behauptungen).
- **Redesign Hero:** Sanfter Ken-Burns-Zoom-Effekt auf dem Hintergrundbild jedes aktiven Slides (via neuer `.hero-slide-bg`-Ebene) + dezenter, animierter Scroll-Hinweis-Pfeil unten im Hero (nur Desktop, respektiert `prefers-reduced-motion`).
- **Redesign Anlagen-Kacheln:** Zickzack-Layout (Bild/Text alternierend links/rechts, wiederverwendet das bestehende `direction:rtl`-Muster), größere Bildfläche, kräftigerer Hover.
- **Redesign Leistungen-Karten:** Icon-Kreis mit sanftem Verlauf, füllt sich beim Hover mit Teal (Icon wird weiß) — mehr visuelle Reaktion ohne Strukturänderung.
- `context/current-data.md`: neuer datierter Eintrag + Upload-Checkliste-Punkt.

### Neue Dateien erstellen

Keine — dieser Plan ändert ausschließlich bestehende Dateien.

### Zu ändernde Dateien

| Dateipfad | Änderungen |
| --- | --- |
| `reference/Getmatic_website/style.css` | Neue CSS-Regeln für Nav-Dropdown, Hero-Zoom/Scrollcue, Anlagen-Zickzack, Leistungen-Hover, Warum-Sektion, DocuControl-FAQ (siehe Schritte 3, 4, 5, 6, 7, 8) |
| `reference/Getmatic_website/index.html` | Dropdown-Nav, 5. Leistungs-Karte, „Warum GeTMatic"-Sektion, Hero-Markup-Anpassung (`.hero-slide-bg`), Scroll-Cue, neue i18n-Keys (DE/EN) |
| `reference/Getmatic_website/impressum.html` | Dropdown-Nav im Header, neue i18n-Keys (DE/EN) |
| `reference/Getmatic_website/datenschutz.html` | Dropdown-Nav im Header, neue i18n-Keys (DE/EN) |
| `reference/Getmatic_website/docucontrol.html` | Dropdown-Nav im Header, neuer FAQ-Block, neue i18n-Keys (DE/EN) |
| `reference/Getmatic_website/einsatzbereich-medizin.html` | Dropdown-Nav im Header, zusätzlicher Absatz `med-p3`, neue i18n-Keys (DE/EN) |
| `reference/Getmatic_website/einsatzbereich-transport.html` | Dropdown-Nav im Header, zusätzlicher Absatz `tra-p3`, neue i18n-Keys (DE/EN) |
| `reference/Getmatic_website/einsatzbereich-papier.html` | Dropdown-Nav im Header, zusätzlicher Absatz `pap-p3`, neue i18n-Keys (DE/EN) |
| `reference/Getmatic_website/einsatzbereich-getraenke.html` | Nur Dropdown-Nav im Header, neue i18n-Keys (DE/EN) — kein zusätzlicher Fließtext (siehe Design-Entscheidung 4) |
| `context/current-data.md` | Neuer datierter Eintrag + „Noch hochzuladen"-Punkt |

### Zu löschende Dateien

Keine.

---

## Design-Entscheidungen

### Getroffene Schlüsselentscheidungen

1. **Dropdown rein per CSS (`:hover`/`:focus-within`), kein JavaScript**: Passt zur dokumentierten Philosophie der Website („bewusst minimales, robustes Vanilla-JS", siehe `plans/2026-09-17-wissen-blog-bereich.md`). Auf Desktop öffnet sich das Menü bei Hover oder Tastatur-Fokus (barrierefrei via `:focus-within`), auf Mobile (≤768px, Hamburger-Menü offen) wird das Untermenü per Media Query permanent statisch/eingerückt angezeigt — kein Toggle-JS nötig, keine neuen Bugs im mobilen Nav-Toggle-Mechanismus möglich.
2. **Nur „Anlagen" bekommt ein Dropdown**, „Leistungen" bleibt ein einfacher Anker-Link: „Leistungen" ist eine einzelne Seiten-Sektion ohne Unterseiten, ein Dropdown wäre dort ohne Nutzen. Das CSS-Pattern (`.nav-dropdown`/`.nav-dropdown-menu`) ist aber generisch benannt und lässt sich später 1:1 für einen möglichen „Wissen"-Dropdown wiederverwenden (siehe `plans/2026-09-17-wissen-blog-bereich.md`, das bewusst nicht Teil dieses Plans ist).
3. **Keine erfundenen Referenzen/Case-Studies**: Die Anforderung „ggf. Referenzen/Case-Studies" wird bewusst **nicht** durch fiktive Kundenprojekte oder Zitate umgesetzt — das wäre inhaltlich unwahr und ein Vertrauensrisiko für ein Unternehmen, dessen Website gerade Vertrauen aufbauen soll. Stattdessen: eine generische, aber wahrheitsgemäße „Warum GeTMatic"-Sektion mit Werten, die bereits an anderer Stelle der Website belegt sind (Einzelunternehmer = direkter Ansprechpartner, Erfahrung in Pharma/Medizin/Industrie, „Individuelle Lösungen" statt Standard — letzteres ist bereits das etablierte Label für DocuControl). Der neue DocuControl-FAQ-Block leitet seine Antworten direkt aus bereits auf der Seite vorhandenen, bestätigten Fakten ab (keine neuen Behauptungen).
4. **`einsatzbereich-getraenke.html` bekommt keinen zusätzlichen Fließtext**: Diese Seite wurde am 2026-09-16 bereits umfassend ausgebaut (Prozess-Sektion mit 4 Videos, siehe `context/current-data.md`). Weiterer Text würde die Seite überladen statt sie zu verbessern. Sie bekommt trotzdem die Dropdown-Navigation, da diese seitenübergreifend konsistent sein muss.
5. **Zickzack-Layout für Anlagen-Kacheln wiederverwendet das bestehende `direction:rtl`-Trick-Pattern** (siehe `.einsatz-screen:nth-child(even)` in `style.css`): Bewährter, bereits im Code vorhandener Ansatz, erfordert keine HTML-Umstrukturierung (kein Vertauschen von Bild/Text im Markup nötig), nur CSS auf `:nth-child(even)`.
6. **Hero-Zoom erfordert eine neue innere `.hero-slide-bg`-Ebene statt Zoom direkt auf `.hero-slide`**: Würde man `transform: scale()` direkt auf `.hero-slide` anwenden, würden auch `.hero-overlay` und `.hero-content` (Text, Button) mitskaliert und dadurch unscharf/verzerrt wirken. Die Aufteilung in eine separate Hintergrund-Ebene hält Text und Button scharf und unbewegt, während nur das Bild sanft zoomt.
7. **Scroll-Cue-Pfeil nur auf Desktop (≥769px)**: Auf Mobile ist der Hero deutlich niedriger (`clamp(380px,60vh,620px)` bzw. weiter reduziert bei sehr kleinen Screens) und der Abstand zu den Dots wäre zu eng — Kollisionsrisiko mit den bereits vorhandenen Bildauswahl-Punkten.
8. **Alle neuen Bewegungs-Effekte (Hero-Zoom, Scroll-Cue-Bounce) respektieren `prefers-reduced-motion: reduce`** — konsistent mit der bereits bestehenden Regel im gleichnamigen Media-Query-Block in `style.css`.
9. **Neue Leistungs-Karte „Raspberry Pi & Embedded-Systeme" als 5. Karte statt Ersatz einer bestehenden**: `.leistungen-grid` nutzt bereits `repeat(auto-fit, minmax(240px, 1fr))` — eine 5. Karte bricht einfach in eine neue Zeile um, keine CSS-Anpassung am Grid nötig.

### Betrachtete Alternativen

- **JavaScript-basiertes Dropdown mit Klick-Toggle und `aria-expanded`-State**: Verworfen zugunsten der reinen CSS-Lösung — würde Änderungen am `<script>`-Block aller 8 Seiten erfordern (aktuell nur Nav-Toggle-Logik, keine Untermenü-Logik) und stünde im Widerspruch zur dokumentierten Minimal-JS-Philosophie der Website. Bei Bedarf jederzeit nachrüstbar, falls sich die CSS-Lösung in der Praxis als unzureichend erweist.
- **Erfundene Kundenreferenzen/Testimonials für „mehr Inhalt"**: Verworfen (siehe Design-Entscheidung 3) — Ehrlichkeit hat Vorrang vor zusätzlichem Content-Volumen.
- **Komplettes Hero-Redesign (z. B. Video-Hintergrund, neues Layout)**: Verworfen als zu groß für „spürbares Redesign einzelner Bereiche" — ein früherer Hero-Video-Plan wurde bereits verworfen (siehe `context/current-data.md`, Eintrag zum Hero-Video-Plan). Der Zoom-Effekt ist eine kleinere, risikoarme Verbesserung im bestehenden Rahmen.
- **FAQ-Block auch auf den Einsatzbereich-Seiten statt nur DocuControl**: Verworfen für diesen Plan, um den Umfang begrenzt zu halten — die drei Einsatzbereich-Seiten bekommen stattdessen je einen zusätzlichen Absatz (kleinere, einheitlichere Änderung). Kann als Folge-Plan ergänzt werden.

### Offene Fragen

Keine — alle Design-Entscheidungen sind getroffen. Der Nutzer sollte die neuen Fließtexte (Schritt 6, 7) vor dem Live-Upload wie gewohnt gegenlesen, da es sich um neu formulierte Fachtexte handelt (Standard-Review-Schritt, kein Blocker für `/implement`).

---

## Schritt-für-Schritt-Aufgaben

### Schritt 1: `style.css` — Neue CSS-Grundlage für Nav-Dropdown

Am Ende des bestehenden `HEADER`/`Nav`-Abschnitts (nach der `.nav-toggle span`-Regel, vor dem `HERO`-Abschnitt) folgende Regeln ergänzen:

**Aktionen:**

```css
/* Dropdown-Untermenü (z. B. "Anlagen") */
.nav-dropdown { position: relative; }

.nav-dropdown > a {
  display: flex;
  align-items: center;
  gap: 4px;
}
.nav-dropdown > a::after {
  content: '';
  width: 6px;
  height: 6px;
  border-right: 1.5px solid currentColor;
  border-bottom: 1.5px solid currentColor;
  transform: rotate(45deg);
  margin-top: -3px;
  opacity: 0.7;
}

.nav-dropdown-menu {
  position: absolute;
  top: 100%;
  left: 0;
  min-width: 240px;
  background: var(--gray-dark);
  border-radius: var(--radius);
  box-shadow: var(--shadow-lg);
  padding: 8px;
  margin-top: 4px;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-6px);
  transition: opacity var(--transition), transform var(--transition), visibility var(--transition);
  z-index: 110;
}

.nav-dropdown:hover .nav-dropdown-menu,
.nav-dropdown:focus-within .nav-dropdown-menu {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

.nav-dropdown-menu li + li { margin-top: 2px; }

.nav-dropdown-menu a {
  display: block;
  padding: 10px 14px;
  font-family: var(--font-body);
  font-size: 0.88rem;
  font-weight: 500;
  letter-spacing: 0.2px;
  text-transform: none;
  color: rgba(255,255,255,0.8);
  border-radius: calc(var(--radius) - 1px);
  transition: background var(--transition), color var(--transition);
  min-height: auto;
}
.nav-dropdown-menu a:hover {
  background: rgba(255,255,255,0.1);
  color: #fff;
}

.nav-dropdown-menu .nav-dropdown-all {
  margin-top: 6px;
  padding-top: 12px;
  border-top: 1px solid rgba(255,255,255,0.12);
  color: var(--teal-light);
  font-weight: 700;
}
```

Danach im bestehenden `@media (max-width: 768px)`-Block, im Abschnitt „Mobile Nav" (nach der Regel `.main-nav a { padding: 12px 16px; }`), ergänzen:

```css
.nav-dropdown > a::after { display: none; }
.nav-dropdown-menu {
  position: static;
  opacity: 1;
  visibility: visible;
  transform: none;
  box-shadow: none;
  background: transparent;
  padding: 4px 0 4px 20px;
  margin-top: 0;
}
.nav-dropdown-menu a { color: rgba(255,255,255,0.6); font-size: 0.85rem; }
```

**Betroffene Dateien:**

- `reference/Getmatic_website/style.css`

---

### Schritt 2: Dropdown-Markup + neue i18n-Keys auf allen 8 Seiten ergänzen

In jeder der 8 Dateien (`index.html`, `impressum.html`, `datenschutz.html`, `docucontrol.html`, `einsatzbereich-medizin.html`, `einsatzbereich-transport.html`, `einsatzbereich-papier.html`, `einsatzbereich-getraenke.html`) den bestehenden „Anlagen"-Eintrag im `<nav class="main-nav">`-Block ersetzen.

**Aktionen:**

- Bisheriges Markup (Beispiel für Unterseiten, `href="index.html#anlagen"`; auf `index.html` selbst ist der Top-Link-href `#anlagen`, das bleibt so):

  ```html
  <li><a href="index.html#anlagen" data-i18n="nav-anlagen">Anlagen</a></li>
  ```

  ersetzen durch:

  ```html
  <li class="nav-dropdown">
    <a href="index.html#anlagen" data-i18n="nav-anlagen">Anlagen</a>
    <ul class="nav-dropdown-menu">
      <li><a href="einsatzbereich-getraenke.html" data-i18n="nav-drop-getraenke">Getränkeverbundkarton</a></li>
      <li><a href="docucontrol.html" data-i18n="nav-drop-doku">Dokumentation</a></li>
      <li><a href="einsatzbereich-medizin.html" data-i18n="nav-drop-medizin">Medizin &amp; Sterilisation</a></li>
      <li><a href="einsatzbereich-papier.html" data-i18n="nav-drop-papier">Papierindustrie</a></li>
      <li><a href="einsatzbereich-transport.html" data-i18n="nav-drop-transport">Transport</a></li>
      <li><a href="index.html#anlagen" class="nav-dropdown-all" data-i18n="nav-drop-alle">Alle Einsatzbereiche &#8594;</a></li>
    </ul>
  </li>
  ```

  Auf `index.html` bleibt der href des Top-Links `#anlagen` (nicht `index.html#anlagen`), alle Submenu-Links bleiben identisch (verweisen auf die jeweiligen Dateien, auch von `index.html` aus).
- Im `translations`-Objekt jeder Datei, sowohl `de`- als auch `en`-Block, **direkt nach** dem `'nav-datenschutz'`-Eintrag ergänzen:

  DE:
  ```js
  'nav-drop-getraenke': 'Getränkeverbundkarton',
  'nav-drop-doku':      'Dokumentation',
  'nav-drop-medizin':   'Medizin & Sterilisation',
  'nav-drop-papier':    'Papierindustrie',
  'nav-drop-transport': 'Transport',
  'nav-drop-alle':      'Alle Einsatzbereiche &#8594;',
  ```

  EN:
  ```js
  'nav-drop-getraenke': 'Beverage Composite Carton',
  'nav-drop-doku':      'Documentation',
  'nav-drop-medizin':   'Medical & Sterilization',
  'nav-drop-papier':    'Paper Industry',
  'nav-drop-transport': 'Transport',
  'nav-drop-alle':      'All Applications &#8594;',
  ```
- Auf `docucontrol.html` zeigt der Dropdown-Eintrag „Dokumentation" auf die Seite selbst — das ist beabsichtigt und unkritisch (kein Sonderfall/Active-State nötig, analog dazu, dass „Anlagen" auf keiner Unterseite als aktiv markiert wird).
- Vor der Umsetzung `datenschutz.html` einmal lesen, um zu bestätigen, dass sie exakt demselben Header-/`translations`-Muster wie die übrigen Unterseiten folgt (wurde von einem Hintergrund-Agenten erstellt, siehe `context/current-data.md`, Eintrag „Vorfall: Subagent..."); falls Abweichungen bestehen, das Dropdown-Markup entsprechend anpassen statt blind zu kopieren.

**Betroffene Dateien:**

- Alle 8 in der Liste oben.

---

### Schritt 3: Hero-Redesign auf `index.html` — Zoom-Effekt + Scroll-Cue

**Aktionen:**

- In jedem der 5 `.hero-slide`-Blöcke das inline `style="--bg: url('...')"` vom `.hero-slide`-Div auf ein neues, als erstes Kind eingefügtes `<div class="hero-slide-bg" style="--bg: url('...')"></div>` verschieben. Beispiel (Slide 1):

  Vorher:
  ```html
  <div class="hero-slide active" style="--bg: url('Systeme.svg')">
    <div class="hero-overlay"></div>
    <div class="hero-content">...</div>
  </div>
  ```

  Nachher:
  ```html
  <div class="hero-slide active">
    <div class="hero-slide-bg" style="--bg: url('Systeme.svg')"></div>
    <div class="hero-overlay"></div>
    <div class="hero-content">...</div>
  </div>
  ```

  Diese Umstellung an allen 5 Slides durchführen (Systeme.svg, getraenkeverpackung.svg, dokumentation.svg, medizin-sterilisation.svg, antriebstechnik.svg), der innere Inhalt (`.hero-overlay`, `.hero-content` mit allen Texten/Buttons) bleibt unverändert.
- Nach `<div class="hero-dots" ...></div>` (innerhalb von `.hero-gallery`, nach dem `next`-Button) ergänzen:

  ```html
  <a href="#leistungen" class="hero-scrollcue" aria-label="Nach unten scrollen">
    <svg viewBox="0 0 24 24" fill="none" aria-hidden="true"><path d="M6 9l6 6 6-6" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
  </a>
  ```
- In `style.css` im `HERO`-Abschnitt:
  - In der bestehenden `.hero-slide`-Regel die drei Zeilen `background-image: var(--bg); background-size: cover; background-position: center;` entfernen und stattdessen `overflow: hidden;` ergänzen.
  - Neue Regeln für `.hero-slide-bg` und den Zoom ergänzen:
    ```css
    .hero-slide-bg {
      position: absolute;
      inset: -4%;
      background-image: var(--bg);
      background-size: cover;
      background-position: center;
      transform: scale(1);
    }
    .hero-slide.active .hero-slide-bg {
      animation: heroZoom 7s ease-out forwards;
    }
    @keyframes heroZoom {
      from { transform: scale(1); }
      to   { transform: scale(1.08); }
    }
    ```
  - Neue Regeln für den Scroll-Cue (z. B. direkt nach dem `.hero-dots`-Block):
    ```css
    .hero-scrollcue {
      position: absolute;
      bottom: 48px;
      left: 50%;
      transform: translateX(-50%);
      width: 32px;
      height: 32px;
      display: flex;
      align-items: center;
      justify-content: center;
      color: rgba(255,255,255,0.8);
      animation: heroScrollBounce 2s ease-in-out infinite;
      z-index: 10;
    }
    .hero-scrollcue svg { width: 22px; height: 22px; }
    .hero-scrollcue:hover { color: var(--teal-light); }
    @keyframes heroScrollBounce {
      0%, 100% { transform: translate(-50%, 0); }
      50%      { transform: translate(-50%, 8px); }
    }
    ```
  - Im bestehenden `@media (max-width: 768px)`-Block, Abschnitt „Hero", ergänzen: `.hero-scrollcue { display: none; }`
  - Im bestehenden `@media (prefers-reduced-motion: reduce)`-Block (bereits vorhanden, listet u. a. `.hero-slide { transition: none; }`) ergänzen: `.hero-slide-bg { animation: none; } .hero-scrollcue { animation: none; }`

**Betroffene Dateien:**

- `reference/Getmatic_website/index.html`
- `reference/Getmatic_website/style.css`

---

### Schritt 4: Leistungen-Redesign + 5. Karte „Raspberry Pi & Embedded-Systeme"

**Aktionen:**

- In `style.css`, `.leistung-icon`-Regel: `background: rgba(32,157,157,0.1);` ersetzen durch `background: linear-gradient(135deg, rgba(32,157,157,0.16), rgba(32,157,157,0.04)); transition: background var(--transition), color var(--transition);`
- Neue Regel ergänzen: `.leistung-card:hover .leistung-icon { background: var(--teal); color: #fff; }`
- In `index.html`, nach der 4. Leistungs-Karte („Komplettlösungen") eine 5. Karte in `.leistungen-grid` ergänzen:
  ```html
  <div class="leistung-card animate-in" style="--d:.5s">
    <div class="leistung-icon">
      <svg viewBox="0 0 48 48" fill="none" aria-hidden="true"><rect x="10" y="10" width="28" height="28" rx="2" stroke="currentColor" stroke-width="2"/><path d="M18 10V5M24 10V5M30 10V5M18 43v-5M24 43v-5M30 43v-5M10 18H5M10 24H5M10 30H5M43 18h-5M43 24h-5M43 30h-5" stroke="currentColor" stroke-width="1.7" stroke-linecap="round"/><circle cx="18" cy="18" r="2" fill="currentColor" opacity=".5"/><circle cx="30" cy="30" r="2" fill="currentColor" opacity=".5"/></svg>
    </div>
    <h3 data-i18n="leis5-h">Raspberry Pi &amp; Embedded-Systeme</h3>
    <p data-i18n="leis5-p">Individuelle Embedded-Lösungen auf Raspberry-Pi-Basis für Datenerfassung, Sondersteuerungen und autarke Dokumentationsgeräte wie DocuControl.</p>
  </div>
  ```
- Im `translations`-Objekt von `index.html`, DE-Block nach `'leis4-p'` ergänzen:
  ```js
  'leis5-h': 'Raspberry Pi &amp; Embedded-Systeme',
  'leis5-p': 'Individuelle Embedded-Lösungen auf Raspberry-Pi-Basis für Datenerfassung, Sondersteuerungen und autarke Dokumentationsgeräte wie DocuControl.',
  ```
  EN-Block nach `'leis4-p'`:
  ```js
  'leis5-h': 'Raspberry Pi &amp; Embedded Systems',
  'leis5-p': 'Custom Raspberry Pi-based embedded solutions for data acquisition, special-purpose controls, and self-contained documentation devices such as DocuControl.',
  ```

**Betroffene Dateien:**

- `reference/Getmatic_website/index.html`
- `reference/Getmatic_website/style.css`

---

### Schritt 5: Anlagen-Kacheln — Zickzack-Redesign

**Aktionen:**

- In `style.css`, Abschnitt `ANLAGEN`:
  - `.anlagen-item` Regel: `grid-template-columns: 280px 1fr;` ersetzen durch `grid-template-columns: minmax(260px, 38%) 1fr;`
  - `.anlagen-img` Regel: `height: 200px;` ersetzen durch `height: 260px;`
  - Neue Regeln ergänzen:
    ```css
    .anlagen-item:nth-child(even) { direction: rtl; }
    .anlagen-item:nth-child(even) .anlagen-body { direction: ltr; }
    ```
  - `.anlagen-item:hover` Regel: `transform: translateY(-2px);` ersetzen durch `transform: translateY(-4px);` (Konsistenz mit `.leistung-card:hover`)
- Im bestehenden `@media (max-width: 768px)`-Block sicherstellen, dass die vorhandene Regel `.anlagen-item { grid-template-columns: 1fr; }` weiterhin greift (sie steht bereits unter „Anlagen" im Responsive-Block) — zusätzlich ergänzen: `.anlagen-item:nth-child(even) { direction: ltr; }` damit auf Mobile keine vertauschte Lesereihenfolge entsteht. `.anlagen-img { height: 200px; }` (bestehende Mobile-Regel) bleibt wie gehabt — nicht die neuen 260px.

**Betroffene Dateien:**

- `reference/Getmatic_website/style.css`

---

### Schritt 6: Neue Sektion „Warum GeTMatic" auf `index.html`

**Aktionen:**

- In `style.css`, neuer Abschnitt (z. B. nach `ANLAGEN`, vor `KONTAKT CTA`):
  ```css
  /* =========================================
     WARUM GETMATIC
     ========================================= */
  .warum-band {
    padding: var(--space-6) 0;
    background: linear-gradient(180deg, rgba(32,157,157,0.07) 0%, rgba(32,157,157,0.015) 100%);
  }

  .warum-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
    gap: var(--space-4);
  }

  .warum-item {
    display: flex;
    gap: var(--space-3);
    align-items: flex-start;
  }

  .warum-num {
    font-family: var(--font-head);
    font-size: 1.6rem;
    font-weight: 800;
    color: var(--teal);
    flex-shrink: 0;
    line-height: 1.3;
  }

  .warum-item h3 {
    font-family: var(--font-head);
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 4px;
  }

  .warum-item p {
    font-size: 0.9rem;
    color: var(--text-light);
    line-height: 1.65;
  }
  ```
- In `index.html`, direkt nach dem schließenden `</section>` der Leistungen-Sektion und vor `<section class="anlagen" id="anlagen">` einfügen:
  ```html
  <!-- Warum GeTMatic -->
  <section class="warum-band">
    <div class="container">
      <div class="section-label animate-in" data-i18n="warum-label">Vertrauen &amp; Erfahrung</div>
      <h2 class="section-title animate-in" style="--d:.05s" data-i18n="warum-title">Warum GeTMatic</h2>
      <div class="warum-grid">
        <div class="warum-item animate-in" style="--d:.1s">
          <span class="warum-num">01</span>
          <div><h3 data-i18n="warum1-h">Direkter Ansprechpartner</h3>
          <p data-i18n="warum1-p">Als Einzelunternehmer sind Sie direkt mit mir im Kontakt — keine Warteschleifen, kein Wechsel der Ansprechperson.</p></div>
        </div>
        <div class="warum-item animate-in" style="--d:.2s">
          <span class="warum-num">02</span>
          <div><h3 data-i18n="warum2-h">Erfahrung in sensiblen Branchen</h3>
          <p data-i18n="warum2-p">Praxis in Industrie, Pharma und Medizintechnik — inklusive der dort geltenden Dokumentations- und Validierungsanforderungen.</p></div>
        </div>
        <div class="warum-item animate-in" style="--d:.3s">
          <span class="warum-num">03</span>
          <div><h3 data-i18n="warum3-h">Von der Planung bis zur Inbetriebnahme</h3>
          <p data-i18n="warum3-p">Ganzheitliche Begleitung Ihres Automatisierungsprojekts aus einer Hand.</p></div>
        </div>
        <div class="warum-item animate-in" style="--d:.4s">
          <span class="warum-num">04</span>
          <div><h3 data-i18n="warum4-h">Individuelle Lösungen</h3>
          <p data-i18n="warum4-p">Maßgeschneiderte Steuerungs- und Dokumentationslösungen wie DocuControl — statt Standard von der Stange.</p></div>
        </div>
      </div>
    </div>
  </section>
  ```
- Im `translations`-Objekt von `index.html`, DE-Block nach `'anl6-link'` (Ende des Anlagen-Blocks) ergänzen:
  ```js
  'warum-label': 'Vertrauen &amp; Erfahrung',
  'warum-title': 'Warum GeTMatic',
  'warum1-h': 'Direkter Ansprechpartner',
  'warum1-p': 'Als Einzelunternehmer sind Sie direkt mit mir im Kontakt — keine Warteschleifen, kein Wechsel der Ansprechperson.',
  'warum2-h': 'Erfahrung in sensiblen Branchen',
  'warum2-p': 'Praxis in Industrie, Pharma und Medizintechnik — inklusive der dort geltenden Dokumentations- und Validierungsanforderungen.',
  'warum3-h': 'Von der Planung bis zur Inbetriebnahme',
  'warum3-p': 'Ganzheitliche Begleitung Ihres Automatisierungsprojekts aus einer Hand.',
  'warum4-h': 'Individuelle Lösungen',
  'warum4-p': 'Maßgeschneiderte Steuerungs- und Dokumentationslösungen wie DocuControl — statt Standard von der Stange.',
  ```
  EN-Block analog, an gleicher Stelle im EN-Objekt:
  ```js
  'warum-label': 'Trust &amp; Experience',
  'warum-title': 'Why GeTMatic',
  'warum1-h': 'A Direct Point of Contact',
  'warum1-p': 'As a sole proprietor, you deal directly with me — no call queues, no changing contact person.',
  'warum2-h': 'Experience in Sensitive Industries',
  'warum2-p': 'Practical experience in industry, pharma and medical technology — including the documentation and validation requirements that apply there.',
  'warum3-h': 'From Planning to Commissioning',
  'warum3-p': 'End-to-end support for your automation project from a single source.',
  'warum4-h': 'Custom Solutions',
  'warum4-p': 'Tailored control and documentation solutions such as DocuControl — instead of off-the-shelf standard products.',
  ```

**Betroffene Dateien:**

- `reference/Getmatic_website/index.html`
- `reference/Getmatic_website/style.css`

---

### Schritt 7: Zusätzlicher Absatz auf Medizin-, Transport- und Papier-Unterseite

**Aktionen:**

- `einsatzbereich-medizin.html`: Im `.einsatz-content`-Block nach `med-p2` einen dritten Absatz ergänzen:
  ```html
  <p data-i18n="med-p3">Auch bei der Nachrüstung bestehender Sterilisationsanlagen prüfen wir vorab die vorhandene Sensorik und Aktorik, um die neue Steuerung möglichst wartungsarm und normkonform in den laufenden Klinik- oder Produktionsbetrieb zu integrieren.</p>
  ```
  Übersetzungs-Keys DE (nach `'med-p2'`): `'med-p3': 'Auch bei der Nachrüstung bestehender Sterilisationsanlagen prüfen wir vorab die vorhandene Sensorik und Aktorik, um die neue Steuerung möglichst wartungsarm und normkonform in den laufenden Klinik- oder Produktionsbetrieb zu integrieren.'`
  EN: `'med-p3': 'When retrofitting existing sterilization systems, we first assess the existing sensors and actuators to integrate the new control system into ongoing clinical or production operations with minimal maintenance and full standards compliance.'`
- `einsatzbereich-transport.html`: Nach `tra-p2` ergänzen:
  ```html
  <p data-i18n="tra-p3">Für einen unterbrechungsfreien Betrieb legen wir zudem Wert auf durchdachte Störungsdiagnose und Not-Halt-Konzepte, damit Stillstandszeiten im Störfall so kurz wie möglich ausfallen.</p>
  ```
  DE: `'tra-p3': 'Für einen unterbrechungsfreien Betrieb legen wir zudem Wert auf durchdachte Störungsdiagnose und Not-Halt-Konzepte, damit Stillstandszeiten im Störfall so kurz wie möglich ausfallen.'`
  EN: `'tra-p3': 'For uninterrupted operation, we also place great importance on well-designed fault diagnostics and emergency-stop concepts, so that downtime in the event of a fault is kept as short as possible.'`
- `einsatzbereich-papier.html`: Nach `pap-p2` ergänzen:
  ```html
  <p data-i18n="pap-p3">Ergänzend zur Antriebsverbund-Steuerung achten wir auf eine klare Diagnose- und Störmeldeanzeige direkt an der Anlage, damit das Bedienpersonal Abweichungen schnell erkennt und Stillstandszeiten minimiert werden.</p>
  ```
  DE: `'pap-p3': 'Ergänzend zur Antriebsverbund-Steuerung achten wir auf eine klare Diagnose- und Störmeldeanzeige direkt an der Anlage, damit das Bedienpersonal Abweichungen schnell erkennt und Stillstandszeiten minimiert werden.'`
  EN: `'pap-p3': 'In addition to the drive-coupled control system, we ensure clear diagnostics and fault indication directly on the machine, so operating staff can quickly identify deviations and minimize downtime.'`
- Jeweils Übersetzungs-Keys an der passenden Stelle im `translations`-Objekt (DE- und EN-Block) der jeweiligen Datei ergänzen, direkt nach dem entsprechenden `-p2`-Key.

**Betroffene Dateien:**

- `reference/Getmatic_website/einsatzbereich-medizin.html`
- `reference/Getmatic_website/einsatzbereich-transport.html`
- `reference/Getmatic_website/einsatzbereich-papier.html`

---

### Schritt 8: FAQ-Block auf `docucontrol.html`

**Aktionen:**

- In `style.css` neuen, generischen (seitenunabhängigen) FAQ-Baustein ergänzen (z. B. am Ende des `EINSATZBEREICH-DETAIL`-Abschnitts):
  ```css
  /* Generischer FAQ-Baustein (aktuell nur auf docucontrol.html verwendet) */
  .info-faq { margin-bottom: var(--space-6); }
  .info-faq-item {
    padding: var(--space-3) 0;
    border-bottom: 1px solid rgba(0,0,0,0.08);
  }
  .info-faq-item:last-child { border-bottom: none; }
  .info-faq-item h3 {
    font-family: var(--font-head);
    font-size: 1.05rem;
    font-weight: 700;
    color: var(--text-dark);
    margin-bottom: 6px;
  }
  .info-faq-item p {
    font-size: 0.92rem;
    color: var(--text);
    line-height: 1.7;
  }
  ```
- In `docucontrol.html`, nach dem letzten `.einsatz-screens`-Block und vor dem `einsatz-cta`-Link (`DocuControl anfragen`) einfügen:
  ```html
  <div class="einsatz-content animate-in" style="--d:.48s">
    <h2 style="font-family:var(--font-head);font-size:1.5rem;font-weight:700;color:var(--text-dark);margin-bottom:var(--space-2);" data-i18n="doc-faq-h">Häufige Fragen</h2>
    <div class="info-faq">
      <div class="info-faq-item">
        <h3 data-i18n="doc-faq1-h">Ist eine Nachrüstung an bestehenden Anlagen möglich?</h3>
        <p data-i18n="doc-faq1-p">Ja. DocuControl wird passiv zwischen HMI-Panel und Drucker angeschlossen und funktioniert unabhängig vom Alter oder Hersteller der Anlage.</p>
      </div>
      <div class="info-faq-item">
        <h3 data-i18n="doc-faq2-h">Werden personenbezogene Daten gespeichert?</h3>
        <p data-i18n="doc-faq2-p">Nein, ausschließlich Maschinendaten. Der Standardbetrieb läuft ohne ausgehende Internetverbindung — DSGVO-konform durch lokale Datenverarbeitung.</p>
      </div>
      <div class="info-faq-item">
        <h3 data-i18n="doc-faq3-h">Wie schnell ist DocuControl einsatzbereit?</h3>
        <p data-i18n="doc-faq3-p">Die Installation erfolgt am bestehenden Druckeranschluss und ist in der Regel in unter 30 Minuten abgeschlossen.</p>
      </div>
    </div>
  </div>
  ```
- Übersetzungs-Keys im `translations`-Objekt von `docucontrol.html`, DE-Block nach `'doc-s3-p'` ergänzen:
  ```js
  'doc-faq-h': 'Häufige Fragen',
  'doc-faq1-h': 'Ist eine Nachrüstung an bestehenden Anlagen möglich?',
  'doc-faq1-p': 'Ja. DocuControl wird passiv zwischen HMI-Panel und Drucker angeschlossen und funktioniert unabhängig vom Alter oder Hersteller der Anlage.',
  'doc-faq2-h': 'Werden personenbezogene Daten gespeichert?',
  'doc-faq2-p': 'Nein, ausschließlich Maschinendaten. Der Standardbetrieb läuft ohne ausgehende Internetverbindung — DSGVO-konform durch lokale Datenverarbeitung.',
  'doc-faq3-h': 'Wie schnell ist DocuControl einsatzbereit?',
  'doc-faq3-p': 'Die Installation erfolgt am bestehenden Druckeranschluss und ist in der Regel in unter 30 Minuten abgeschlossen.',
  ```
  EN-Block analog nach `'doc-s3-p'`:
  ```js
  'doc-faq-h': 'Frequently Asked Questions',
  'doc-faq1-h': 'Can it be retrofitted to existing systems?',
  'doc-faq1-p': 'Yes. DocuControl is passively connected between the HMI panel and the printer and works independently of the age or manufacturer of the system.',
  'doc-faq2-h': 'Is any personal data stored?',
  'doc-faq2-p': 'No, only machine data. Standard operation runs without an outgoing internet connection — GDPR-compliant through local data processing.',
  'doc-faq3-h': 'How quickly is DocuControl ready for use?',
  'doc-faq3-p': 'Installation takes place at the existing printer connection and is typically completed in under 30 minutes.',
  ```

**Betroffene Dateien:**

- `reference/Getmatic_website/docucontrol.html`
- `reference/Getmatic_website/style.css`

---

### Schritt 9: Lokal testen

**Aktionen:**

- Lokalen Server aus `reference/Getmatic_website/` starten (`http-server -p 8080 -o "index.html"`, ggf. `MSYS_NO_PATHCONV=1` voranstellen, siehe bekannte Git-Bash-Falle in `context/current-data.md`).
- Playwright-Check auf Desktop- (z. B. 1440×900) und Mobile-Viewport (390×844) für alle 8 Seiten:
  - Keine Konsolenfehler.
  - Nav-Dropdown „Anlagen": auf Desktop öffnet sich das Menü bei Hover **und** bei Tastatur-Fokus (Tab-Taste), alle 5 Links + „Alle Einsatzbereiche" funktionieren, führen zur richtigen Seite. Auf Mobile ist das Untermenü im geöffneten Hamburger-Menü immer sichtbar und eingerückt.
  - `index.html`: Hero-Zoom läuft sichtbar (Screenshot zu Beginn und nach ~3s vergleichen), Scroll-Cue-Pfeil sichtbar auf Desktop, ausgeblendet auf Mobile, kein Layout-Overflow durch `.hero-slide-bg`.
  - `index.html`: 5. Leistungs-Karte sichtbar, Hover färbt Icon-Kreis teal; „Warum GeTMatic"-Sektion zwischen Leistungen und Anlagen sichtbar, 4 Punkte lesbar.
  - `index.html`: Anlagen-Kacheln zeigen Zickzack-Layout (Bild abwechselnd links/rechts) auf Desktop, einspaltig (kein RTL-Bug, korrekte Lesereihenfolge) auf Mobile.
  - `einsatzbereich-medizin.html`, `-transport.html`, `-papier.html`: neuer dritter Absatz sichtbar und korrekt in DE/EN.
  - `docucontrol.html`: FAQ-Block sichtbar, 3 Fragen/Antworten korrekt in DE/EN.
  - DE/EN-Sprachumschalter auf allen 8 Seiten: keine sichtbaren rohen `data-i18n`-Keys, insbesondere die neuen Dropdown-, Warum-, FAQ- und Absatz-Keys.
  - `prefers-reduced-motion: reduce` simulieren (Playwright `emulateMedia`) und bestätigen, dass Hero-Zoom und Scroll-Cue-Bounce deaktiviert sind.
- Screenshots von Hero (Desktop+Mobile), Anlagen-Kacheln, Warum-Sektion und geöffnetem Nav-Dropdown sichten.

**Betroffene Dateien:**

- Keine (Testschritt).

---

### Schritt 10: `context/current-data.md` aktualisieren

**Aktionen:**

- Neuen datierten Eintrag unter „Aktueller Stand" ergänzen: Dropdown-Navigation, neue Inhalte (Raspberry-Pi-Karte, Warum-Sektion, 3 neue Absätze, DocuControl-FAQ), Hero-/Anlagen-/Leistungen-Redesign — mit Hinweis, dass alle Fließtexte vor Live-Upload vom Nutzer gegengelesen werden sollten.
- Neuen Punkt unter „Offene Aufgaben / nächste Schritte" ergänzen: Re-Upload für alle 8 HTML-Dateien + `style.css`.

**Betroffene Dateien:**

- `context/current-data.md`

---

## Verbindungen & Abhängigkeiten

### Dateien, die diesen Bereich referenzieren

- Alle 8 HTML-Seiten (identisches Header-/Footer-/Nav-Markup, siehe Schritt 2).
- `plans/2026-09-17-wissen-blog-bereich.md` (separater, nicht umgesetzter Plan) — das `.nav-dropdown`-CSS-Muster aus diesem Plan ist bewusst so generisch benannt, dass ein künftiger „Wissen"-Dropdown es wiederverwenden kann, ohne dass dieser Plan das voraussetzt.

### Nötige Updates für Konsistenz

- `context/current-data.md` (Schritt 10) — einzige laufend gepflegte Statusdokumentation.
- Kein `CLAUDE.md`-Update nötig: Workspace-Struktur (Ordner/Commands) ändert sich nicht, nur Website-Content/-Design innerhalb der bestehenden Struktur `reference/Getmatic_website/`.

### Auswirkungen auf bestehende Workflows

- Erhöht die Duplizierungslast bei künftigen Nav-Änderungen leicht (Dropdown-Markup + 6 neue i18n-Keys jetzt auf allen 8 Seiten dupliziert) — bereits bestehendes, bekanntes Muster dieser Website, keine neue Problemklasse.
- Keine Auswirkung auf den internen Mitarbeiterbereich (`intern/`) oder bestehende Deployment-Workflows.
- Das neue `.info-faq`-CSS ist bewusst seitenunabhängig benannt und kann künftig auf weiteren Seiten wiederverwendet werden.

---

## Validierungs-Checkliste

- [ ] Dropdown „Anlagen" auf allen 8 Seiten vorhanden, Desktop-Hover/Focus + Mobile-Inline-Darstellung funktionieren, alle 6 Links korrekt
- [ ] Neue i18n-Keys (Dropdown, Warum, FAQ, `-p3`-Absätze, `leis5-*`) auf allen betroffenen Seiten vollständig in DE **und** EN, keine sichtbaren Rohkeys
- [ ] Hero-Zoom + Scroll-Cue sichtbar auf Desktop, Scroll-Cue ausgeblendet auf Mobile, beide deaktiviert bei `prefers-reduced-motion: reduce`
- [ ] 5. Leistungs-Karte „Raspberry Pi & Embedded-Systeme" sichtbar, Hover-Effekt (Icon-Kreis füllt sich teal) funktioniert
- [ ] „Warum GeTMatic"-Sektion zwischen Leistungen und Anlagen sichtbar, 4 Punkte korrekt
- [ ] Anlagen-Kacheln zeigen Zickzack-Layout auf Desktop, korrekte (nicht vertauschte) Lesereihenfolge auf Mobile
- [ ] Neuer dritter Absatz auf Medizin-, Transport- und Papier-Seite sichtbar
- [ ] FAQ-Block auf `docucontrol.html` sichtbar, 3 Fragen/Antworten korrekt
- [ ] Lokaler Playwright-Test auf allen 8 Seiten: keine Konsolenfehler, kein horizontaler Overflow (Desktop + Mobile)
- [ ] `context/current-data.md` enthält neuen Eintrag + Upload-Checkliste-Punkt

---

## Erfolgskriterien

Die Implementierung ist abgeschlossen, wenn:

1. Von jeder der 8 Seiten aus alle 5 Einsatzbereiche über das Dropdown-Menü „Anlagen" in einem Klick erreichbar sind, ohne dass JavaScript für das Menü selbst nötig ist.
2. Die Startseite eine 5. Leistungs-Karte (Raspberry Pi/Embedded) und eine neue „Warum GeTMatic"-Sektion zeigt; die drei Einsatzbereich-Seiten Medizin/Transport/Papier sowie DocuControl spürbar mehr fachlichen Inhalt haben als zuvor — ohne erfundene Fakten oder Referenzen.
3. Hero, Anlagen-Kacheln und Leistungen-Karten optisch spürbar aufgewertet sind (Zoom-Effekt, Zickzack-Layout, Hover-Polish), ohne dass sich Farbpalette oder Schriftarten geändert haben.
4. Alle Änderungen lokal fehlerfrei laufen (keine Konsolenfehler, kein Overflow, DE/EN vollständig) und `context/current-data.md` den Stand korrekt dokumentiert, inkl. offenem Upload-Status.

---

## Notizen

- Alle neu verfassten Fließtexte (Schritt 6, 7, 8) sind fachlich plausible, aber neu formulierte Inhalte — wie bei jeder Content-Änderung dieser Website sollte der Nutzer sie vor dem Live-Upload kurz gegenlesen (Standard-Workflow, kein zusätzlicher Schritt).
- Perspektivisch mögliche, aber bewusst nicht in diesem Plan enthaltene Folgeschritte:
  - FAQ-Block (`.info-faq`) auch auf den Einsatzbereich-Seiten ergänzen.
  - „Wissen"-Dropdown nach Umsetzung von `plans/2026-09-17-wissen-blog-bereich.md` in dasselbe `.nav-dropdown`-Muster einhängen.
  - Homepage-Teaser für „Warum GeTMatic" ggf. um ein Foto/Porträt ergänzen, falls der Nutzer künftig ein Profilbild bereitstellen möchte (aktuell kein Foto-Asset vorbereitet, siehe auch Autor-Box-Entscheidung im Wissen-Plan).
- Deployment-Reihenfolge beim späteren Live-Upload (nicht Teil dieses Plans): alle 8 geänderten HTML-Dateien + `style.css` — wie immer **Überschreiben statt Resume** beim FTP-Upload verwenden (bekannte 1blu/FileZilla-Falle, siehe `context/current-data.md`).

---

## Implementierungsnotizen

**Implementiert:** 2026-09-17

### Zusammenfassung

Alle 10 Schritte umgesetzt: CSS-only Dropdown-Nav unter „Anlagen" auf allen 8 Seiten, 5. Leistungs-Karte + „Warum GeTMatic"-Sektion auf der Startseite, je ein zusätzlicher Absatz auf Medizin/Transport/Papier, FAQ-Block auf DocuControl, Hero-Zoom + Scroll-Cue, Anlagen-Zickzack-Layout, Leistungen-Hover-Polish. Lokal mit einem lokalen `http-server` + Playwright (Chromium) umfassend getestet: alle 8 Seiten auf Desktop (1440×900) und Mobile (390×844), DE/EN-Umschaltung, Dropdown-Verhalten (Desktop-Hover/Fokus, Mobile-Inline-Darstellung), Hero-Zoom-Fortschritt, `prefers-reduced-motion`-Verhalten, horizontaler Overflow, Konsolenfehler. `context/current-data.md` mit datiertem Eintrag + Upload-Checkliste-Punkt aktualisiert.

### Abweichungen vom Plan

Keine inhaltlichen Abweichungen — alle Texte, CSS-Werte und Markup-Strukturen entsprechen exakt der Plan-Spezifikation.

### Aufgetretene Probleme

Zwei CSS-Spezifitätsbugs beim Testen gefunden und behoben (nicht im ursprünglichen Plan vorhergesehen, da nur beim tatsächlichen Rendern sichtbar):

1. **Dropdown-Menü horizontal statt vertikal:** `.nav-dropdown-menu` (Spezifität einer Klasse) erbte `display: flex` von der bestehenden, spezifischeren Regel `.main-nav ul` (Klasse+Element) und reihte die 6 Einträge nebeneinander statt untereinander auf. Führte zusätzlich zu ca. 27px unsichtbarem horizontalem Seiten-Overflow (durch `body { overflow-x: hidden; }` nicht sichtbar, aber unsauber). Fix: neue Regel `.main-nav .nav-dropdown-menu { display: block; }` mit höherer Spezifität ergänzt.
2. **`prefers-reduced-motion` deaktivierte den Hero-Zoom nicht:** Die Override-Regel `.hero-slide-bg { animation: none; }` hatte niedrigere Spezifität als die Basisregel `.hero-slide.active .hero-slide-bg { animation: heroZoom ...; }` und wurde daher nie angewendet. Fix: Selektor auf `.hero-slide.active .hero-slide-bg { animation: none; }` angepasst (gleiche Spezifität, gewinnt durch spätere Position im Stylesheet).

Beide Fixes wurden nach dem Fund erneut mit Playwright verifiziert (Dropdown zeigt jetzt vertikale Liste, kein Overflow mehr; `prefers-reduced-motion`-Emulation zeigt `animationName: "none"` für den Hero-Hintergrund).
