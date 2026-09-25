# Plan: Website-Redesign Iteration 2 — modernisierte Farbpalette, mehr Wow-Effekt, mehr ehrlicher Inhalt

**Erstellt:** 2026-09-17
**Status:** Implementiert
**Anforderung:** Nutzer-Feedback nach Ansicht von Iteration 1 („überzeugt mich noch nicht"): Farbpalette wirkt altbacken, zu wenig Wow-Effekt, Inhalt überzeugt noch nicht — Teal bleibt Kernfarbe, Fonts/Logo/Nav-Struktur bleiben unverändert.

---

## Überblick

### Was dieser Plan erreicht

Baut auf der bereits implementierten (aber noch nicht live hochgeladenen) Iteration 1 auf (`plans/2026-09-17-website-redesign-navigation-inhalt.md`) und adressiert konkretes Nutzer-Feedback: (1) eine kühlere, kontrastreichere Farbpalette rund um das unveränderte Teal-Markenzeichen, (2) ein spürbar mutigerer erster Eindruck (Hero, neue dunkle Stats-Sektion, Mikro-Interaktionen an Buttons/Karten) und mehr strukturelle Abwechslung zwischen den Sektionen (Hell/Dunkel-Rhythmus statt durchgehend ähnlicher heller Abschnitte), (3) zusätzlicher, wahrheitsgemäßer Inhalt (Kennzahlen-Leiste mit echten, zählbaren Fakten + neue Prozess-Sektion „Unser Vorgehen") ohne erfundene Referenzen oder Kennzahlen.

### Warum das wichtig ist

Der erste optische Eindruck entscheidet bei einem B2B-Entscheider in Industrie/Pharma/Medizin mit, ob die Website als kompetent und aktuell wahrgenommen wird (`context/strategy.md`, Priorität 3: Vertrauen). Eine als „altbacken" wahrgenommene Optik untergräbt genau dieses Ziel, unabhängig davon, wie gut der fachliche Inhalt ist. Diese Iteration behebt das gezielt, ohne die Wiedererkennbarkeit (Teal, Logo, Schriften) aufzugeben.

---

## Aktueller Zustand

### Relevante bestehende Struktur

- Iteration 1 ist umgesetzt (siehe `plans/2026-09-17-website-redesign-navigation-inhalt.md`, Status „Implementiert", noch nicht live): Dropdown-Nav, 5. Leistungs-Karte, „Warum GeTMatic"-Sektion (aktuell **hell**, teal-getönter Verlauf), 3 zusätzliche Absätze, DocuControl-FAQ, Hero-Zoom, Anlagen-Zickzack, Leistungen-Hover-Polish.
- Aktuelle Farb-Tokens in `style.css` `:root` (Zeilen 23–39): `--teal:#209D9D` / `--teal-light:#5AD7D7` / `--teal-dark:#0F8C8C` (Kernfarben, **bleiben unverändert**), `--gray-header:#4A4A4A` (Header, Kontakt-CTA-Hintergrund), `--gray-dark:#2C2B2B` (Footer, mobile Nav, Dropdown-Hintergrund), `--bg:#E0DCDC` (warmes Beige-Grau, Seitenhintergrund/Anlagen-Sektion), `--bg-card:#F5F3F3`, `--text`/`--text-light`/`--text-dark` (Grautöne).
- Aktueller Seitenaufbau `index.html`: Hero → Leistungen (weiß) → Warum GeTMatic (hell, teal-getönt) → Anlagen (`--bg`) → Kontakt-CTA (dunkel, `--gray-header`) → Footer (`--gray-dark`). Fünf von sechs Abschnitten sind hell/mittelhell — wenig Hell-Dunkel-Rhythmus, dadurch wirkt die Seite trotz Iteration 1 optisch gleichförmig.
- Bestehendes CSS-Pattern für Zahlen-/Nummern-Typografie bereits etabliert (`.anlagen-num`, `.warum-num`), IntersectionObserver bereits für `.animate-in` im Einsatz — beides wiederverwendbar für eine neue Zähl-Animation.
- `reference/Getmatic_website/impressum.html` Footer zeigt `&copy; 2019...` — Hinweis auf ein mögliches Gründungsjahr, aber **nicht bestätigt als Marketing-Fact** (Copyright-Jahr ≠ zwangsläufig Gründungsjahr).
- Bereits sicher zählbare, wahrheitsgemäße Fakten direkt aus der bestehenden Seite ableitbar: **5 Einsatzbereiche** (Getränkeverbundkarton, Dokumentation, Medizin & Sterilisation, Papierindustrie, Transport) und **3 Branchen** (Industrie, Pharma, Medizin — mehrfach auf der Seite so benannt, u. a. `context/business-info.md`).

### Lücken oder Probleme, die adressiert werden

- Der Seitenhintergrund (`--bg:#E0DCDC`) und der Header-Grauton (`--gray-header:#4A4A4A`) sind warm/flach und erzeugen den vom Nutzer benannten „altbackenen" Eindruck (typisch für ältere Corporate-Templates).
- Kein Hell-Dunkel-Rhythmus zwischen den Sektionen — reduziert visuelle Spannung/Wow-Effekt.
- Keine Mikro-Interaktionen über Scroll-Fade-in und den (in Iteration 1 neuen) Hero-Zoom hinaus.
- Keine Kennzahlen-/Vertrauens-Leiste mit echten Zahlen, obwohl mehrere sofort verfügbar sind (5 Einsatzbereiche, 3 Branchen).
- Kein Abschnitt, der den tatsächlichen Arbeitsablauf mit einem Kunden beschreibt — Warum-GeTMatic-Punkt 3 erwähnt „Von der Planung bis zur Inbetriebnahme" nur als Ein-Zeiler, ohne eigenen Abschnitt.

---

## Vorgeschlagene Änderungen

### Zusammenfassung der Änderungen

- **Farbpalette modernisiert** (nur `:root`-Tokens in `style.css`, propagiert automatisch auf alle 8 Seiten): kühlerer, dunklerer Header/Footer-Ton, kühlerer/cleanerer Seitenhintergrund, neuer Akzentton (Industrie-Amber) für punktuelle Highlights, neuer „Glow"-Schatten-Token. Teal/Teal-Light/Teal-Dark bleiben exakt wie bisher.
- **Neue dunkle Kennzahlen-Leiste** direkt unter dem Hero auf `index.html` (5 Einsatzbereiche, 3 Branchen, SPS-Systembandbreite), mit Zähl-Animation beim Einscrollen.
- **Neue Sektion „Unser Vorgehen"** (4-Schritte-Prozess mit Verbindungslinie) zwischen Leistungen und Warum-GeTMatic auf `index.html`.
- **„Warum GeTMatic"-Sektion auf dunkel umgestellt** (Farbwechsel, kein Markup-Umbau) für Hell-Dunkel-Rhythmus.
- **Hero mutiger:** größere Höhe/Typografie, Kompetenz-Pills im ersten Slide, stärkerer teal-getönter Overlay-Glow.
- **Mikro-Interaktionen:** Shine-Sweep-Hover auf allen Primär-Buttons (`.btn-primary`, `.einsatz-cta`, `.kontakt-link`), farbiger Glow-Schatten beim Hover auf Leistungen-Karten und Anlagen-Kacheln.
- `context/current-data.md`: neuer datierter Eintrag + Upload-Checkliste-Punkt.

### Neue Dateien erstellen

Keine.

### Zu ändernde Dateien

| Dateipfad | Änderungen |
| --- | --- |
| `reference/Getmatic_website/style.css` | Farb-Tokens, neue Sektionen (Stats-Band, Vorgehen), Warum-Band dunkel, Hero-Erweiterungen, Mikro-Interaktionen, Responsive-Anpassungen (siehe Schritte 1–7) |
| `reference/Getmatic_website/index.html` | Neue Stats-Band-Sektion, neue Vorgehen-Sektion, Hero-Pills (Slide 1), Zähl-Animation-Script, neue i18n-Keys (DE/EN) |
| `context/current-data.md` | Neuer datierter Eintrag + „Noch hochzuladen"-Punkt |

Alle übrigen 7 HTML-Seiten benötigen **keine** Änderungen in diesem Plan — die Farbmodernisierung wirkt allein über die zentralen CSS-Tokens, die neuen Inhaltsabschnitte (Stats-Band, Vorgehen) sind bewusst nur für die Startseite vorgesehen (Design-Entscheidung 6).

### Zu löschende Dateien

Keine.

---

## Design-Entscheidungen

### Getroffene Schlüsselentscheidungen

1. **Teal/Teal-Light/Teal-Dark bleiben exakt unverändert**: Nutzer-Vorgabe „Teal bleibt Kernfarbe" — Logo und Wiedererkennung dürfen nicht angetastet werden. Nur das *Umfeld* (Hintergrund, Header/Footer-Grauton) wird modernisiert.
2. **Neuer Akzentton „Industrie-Amber" (`--accent:#FF8A3D`)**: Ergänzt Teal um einen warmen Kontrastpunkt für Kennzahlen und punktuelle Highlights (Stats-Zahlen, Warum-Nummern). Bewusst *sparsam* eingesetzt (nur Zahlen/Nummern-Typografie in zwei Sektionen), damit Teal weiterhin die dominante Markenfarbe bleibt und nichts wie ein Zufallsfund wirkt. Amber/Signalorange passt thematisch zu Industrie/Automatisierung (Signalfarbe an Maschinen/Schaltschränken) — ein passender, nicht willkürlicher Farbbezug.
3. **Kühlerer, dunklerer Header/Footer-Ton statt flachem Mittelgrau**: `--gray-header` von `#4A4A4A` auf `#1C2426`, `--gray-dark` von `#2C2B2B` auf `#101415`. Erzeugt sofort einen moderneren, „premium" wirkenden Kontrast (dunkle Flächen mit hellem/teal-farbenem Text sind ein etabliertes Muster moderner Tech-/Industrieseiten) und behebt gleichzeitig den „2015er-Template"-Eindruck des bisherigen flachen Mittelgraus.
4. **Kühlerer, hellerer Seitenhintergrund statt warmem Beige-Grau**: `--bg` von `#E0DCDC` auf `#EDF2F1`, `--bg-card` von `#F5F3F3` auf `#F5F8F7`. Entfernt den „staubigen"/datierten Unterton, ohne den Kontrast zu Textfarben zu verändern (Text-Tokens bleiben unverändert, funktionieren auf beiden Hintergründen gleich gut).
5. **„Warum GeTMatic" wird zur dunklen Sektion**: Reine Farbumstellung (keine Markup-Änderung nötig, da bereits vollständig Token-basiert), erzeugt zusammen mit der neuen Stats-Band einen Hell-Dunkel-Rhythmus über die Seite (Hero[dunkel] → Stats[dunkel] → Leistungen[hell] → Vorgehen[hell] → Warum[dunkel] → Anlagen[hell] → Kontakt[dunkel] → Footer[am dunkelsten]) statt der bisherigen, fast durchgehend hellen Abfolge.
6. **Neue Inhaltsabschnitte (Stats-Band, Vorgehen) nur auf `index.html`**: Die Startseite ist der Haupteinstiegspunkt und trägt bereits alle bisherigen „mehr Inhalt"-Ergänzungen aus Iteration 1 (Warum-Sektion, 5. Leistung). Eine Ausweitung auf alle 8 Seiten wäre unverhältnismäßig zum Nutzen und würde die Wartungslast (aktuell schon dupliziertes Header/Footer-Markup) unnötig erhöhen.
7. **Kennzahlen-Leiste nutzt ausschließlich bereits verifizierte, zählbare Fakten**: 5 Einsatzbereiche und 3 Branchen sind direkt aus der bestehenden Anlagen-Sektion bzw. `context/business-info.md` ableitbar — keine Schätzung, keine Erfindung. Ein mögliches viertes „seit 20XX"-Kachel wird bewusst **nicht** standardmäßig ergänzt, da das Gründungsjahr nicht zweifelsfrei bestätigt ist (siehe Offene Fragen).
8. **Keine Cursor-Tracking-/Magnet-Button-Mikrointeraktion mit JavaScript**: Ein „Magnet-Button"-Effekt (Button folgt leicht dem Mauszeiger) wäre zusätzlicher, nicht-trivialer JS-Code je Seite. Stattdessen ein reiner CSS-Shine-Sweep-Hover (Lichtstreifen läuft beim Hover über den Button) — liefert einen ähnlichen „wow"-Effekt, bleibt aber im Rahmen der dokumentierten Minimal-JS-Philosophie der Website und ist ohne Fehlerrisiko re-implementierbar.
9. **Angled/schräge Sektions-Trenner (Clip-Path) werden NICHT umgesetzt**: Erwogen für zusätzlichen „Wow"-Effekt, aber verworfen — Clip-Path auf ganzen Sektionen birgt reales Risiko, bei Textumbruch/Mobile-Reflow echten Inhalt abzuschneiden, und hätte in Iteration 1 (wo bereits zwei CSS-Spezifitätsbugs erst durch Tests gefunden wurden) ein unverhältnismäßig hohes Risiko für optisches Gewinn-Risiko-Verhältnis. Der Hell-Dunkel-Rhythmus (Entscheidung 5) liefert einen Großteil des gewünschten Effekts risikofrei.
10. **Zähl-Animation der Stats-Zahlen per Vanilla-JS mit `requestAnimationFrame`**: Konsistent mit dem bereits vorhandenen IntersectionObserver-Muster der Seite, keine neue Bibliothek, respektiert `prefers-reduced-motion` (Zahl erscheint dort sofort ohne Animation).

### Betrachtete Alternativen

- **Komplett neue Markenfarbe statt Akzentton-Ergänzung**: Verworfen — Nutzer-Vorgabe ist ausdrücklich „Teal bleibt Kernfarbe".
- **Neue Inhaltsabschnitte auf allen 8 Seiten**: Verworfen zugunsten von „nur Startseite" (siehe Entscheidung 6) — Aufwand/Nutzen-Verhältnis, Wartungslast.
- **Erfundenes Gründungsjahr/erfundene Kundenzahl für die Stats-Band**: Kategorisch ausgeschlossen — verstößt gegen den bereits in Iteration 1 etablierten Ehrlichkeits-Grundsatz.
- **Dark-Mode-Umschalter für die ganze Seite**: Deutlich größerer Scope (jede Komponente bräuchte ein zweites Farbschema, State-Persistenz analog zum Sprachumschalter) — nicht das, was der Nutzer mit „Wow-Effekt" angefragt hat; die punktuelle Hell-Dunkel-Rhythmisierung einzelner Sektionen erreicht den gewünschten Effekt mit einem Bruchteil des Aufwands.

### Offene Fragen

Keine mehr offen. **Geklärt (2026-09-17):** Nutzer hat bestätigt, dass 2019 das korrekte Jahr ist, seit dem getmatic aktiv ist. Die Stats-Band bekommt daher standardmäßig die 4. Kachel „seit 2019" (siehe Schritt 3 — die dort als optional beschriebene 4. Kachel ist jetzt fester Bestandteil der Implementierung, nicht mehr bedingt).

---

## Schritt-für-Schritt-Aufgaben

### Schritt 1: Farb-Tokens modernisieren

**Aktionen:**

In `style.css`, im `:root`-Block, folgende Werte ändern (nur Werte, keine Variablennamen):

```css
:root {
  /* Brand */
  --teal:        #209D9D;   /* unverändert */
  --teal-light:  #5AD7D7;   /* unverändert */
  --teal-dark:   #0F8C8C;   /* unverändert */
  --gray-header: #1C2426;   /* war #4A4A4A */
  --gray-dark:   #101415;   /* war #2C2B2B */

  /* Neuer Akzent */
  --accent:      #FF8A3D;
  --accent-dark: #DB6A1E;

  /* Hintergrund */
  --bg:          #EDF2F1;   /* war #E0DCDC */
  --bg-card:     #F5F8F7;   /* war #F5F3F3 */
  --bg-white:    #FFFFFF;   /* unverändert */

  /* Text */
  --text:        #5C5B5B;   /* unverändert */
  --text-light:  #8C8B8B;   /* unverändert */
  --text-dark:   #2C2B2B;   /* unverändert */

  ...

  /* Sonstiges */
  --radius:     4px;
  --shadow:     0 2px 16px rgba(0,0,0,0.08);
  --shadow-lg:  0 8px 40px rgba(0,0,0,0.12);
  --shadow-glow: 0 16px 44px rgba(32,157,157,0.28);   /* NEU */
  --transition: 0.25s ease;
  --max-w:      1140px;
}
```

Da alle 8 Seiten `style.css` zentral einbinden, wirkt diese Änderung sofort überall — Header, Footer, mobile Nav, Dropdown-Hintergrund, Seitenhintergrund (`.anlagen`, `body`), Kontakt-CTA-Hintergrund passen sich automatisch an, ohne dass eine einzige HTML-Datei angefasst werden muss.

**Betroffene Dateien:**

- `reference/Getmatic_website/style.css`

---

### Schritt 2: „Warum GeTMatic" auf dunkles Farbschema umstellen

**Aktionen:**

In `style.css`, Abschnitt `WARUM GETMATIC`, folgende Regeln ändern/ergänzen:

```css
.warum-band {
  padding: var(--space-6) 0;
  background: linear-gradient(160deg, var(--gray-header) 0%, var(--gray-dark) 100%);
}

.warum-band .section-label { color: var(--teal-light); }
.warum-band .section-title { color: #fff; }

.warum-num { color: var(--accent); }

.warum-item h3 { color: #fff; }

.warum-item p { color: rgba(255,255,255,0.65); }
```

Diese Regeln stehen zusätzlich zu (nicht anstelle von) den bestehenden `.warum-*`-Regeln aus Iteration 1 und überschreiben nur Farbwerte. Kein HTML-Markup auf `index.html` muss geändert werden, da die Sektion bereits vollständig über CSS-Klassen gesteuert wird.

**Betroffene Dateien:**

- `reference/Getmatic_website/style.css`

---

### Schritt 3: Neue Kennzahlen-Leiste („Stats-Band") auf `index.html`

**Aktionen:**

In `style.css`, neuer Abschnitt (z. B. direkt nach `HERO`, vor `SECTION GEMEINSAM`):

```css
/* =========================================
   STATS-BAND
   ========================================= */
.stats-band {
  background: var(--gray-header);
  padding: var(--space-5) 0;
}

.stats-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: var(--space-4);
  text-align: center;
}

.stat-num,
.stat-num-text {
  font-family: var(--font-head);
  font-weight: 800;
  color: var(--accent);
  line-height: 1;
}
.stat-num { font-size: clamp(2.4rem, 5vw, 3.4rem); }
.stat-num-text { font-size: clamp(1.3rem, 3vw, 1.7rem); }

.stat-label {
  margin-top: 8px;
  font-size: 0.8rem;
  font-weight: 500;
  letter-spacing: 1.5px;
  text-transform: uppercase;
  color: rgba(255,255,255,0.6);
}
```

In `index.html`, direkt nach dem schließenden `</section>` des Hero-Bereichs und vor `<section class="leistungen" ...>` einfügen:

```html
<!-- Stats-Band -->
<section class="stats-band">
  <div class="container">
    <div class="stats-grid">
      <div class="stat-tile animate-in" style="--d:.1s">
        <div class="stat-num" data-count="5">0</div>
        <div class="stat-label" data-i18n="stat1-label">Einsatzbereiche</div>
      </div>
      <div class="stat-tile animate-in" style="--d:.2s">
        <div class="stat-num" data-count="3">0</div>
        <div class="stat-label" data-i18n="stat2-label">Branchen</div>
      </div>
      <div class="stat-tile animate-in" style="--d:.3s">
        <div class="stat-num-text" data-i18n="stat3-num">S7-300&nbsp;&ndash;&nbsp;S7-1500</div>
        <div class="stat-label" data-i18n="stat3-label">SPS-Systeme</div>
      </div>
      <div class="stat-tile animate-in" style="--d:.4s">
        <div class="stat-num-text" data-i18n="stat4-num">seit 2019</div>
        <div class="stat-label" data-i18n="stat4-label">Am Markt</div>
      </div>
    </div>
  </div>
</section>
```

Im `translations`-Objekt von `index.html`, DE-Block nach `'kont-p'` ergänzen:
```js
'stat1-label': 'Einsatzbereiche',
'stat2-label': 'Branchen',
'stat3-num':   'S7-300&nbsp;&ndash;&nbsp;S7-1500',
'stat3-label': 'SPS-Systeme',
'stat4-num':   'seit 2019',
'stat4-label': 'Am Markt',
```
EN-Block analog:
```js
'stat1-label': 'Applications',
'stat2-label': 'Industries',
'stat3-num':   'S7-300&nbsp;&ndash;&nbsp;S7-1500',
'stat3-label': 'PLC Systems',
'stat4-num':   'since 2019',
'stat4-label': 'In Business',
```

Am Ende des bestehenden `<script>`-Blocks von `index.html` (nach der bestehenden „Scroll animations"-IntersectionObserver-Sektion) folgendes Zähl-Animation-Script ergänzen:

```js
// =========================================
// Stats-Band Zähl-Animation
// =========================================
var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
var statObserver = new IntersectionObserver(function(entries) {
  entries.forEach(function(entry) {
    if (!entry.isIntersecting) return;
    var el = entry.target;
    var target = parseInt(el.getAttribute('data-count'), 10);
    if (prefersReducedMotion) {
      el.textContent = target;
      statObserver.unobserve(el);
      return;
    }
    var duration = 900;
    var startTime = null;
    function step(ts) {
      if (!startTime) startTime = ts;
      var progress = Math.min((ts - startTime) / duration, 1);
      el.textContent = Math.floor(progress * target);
      if (progress < 1) requestAnimationFrame(step);
      else el.textContent = target;
    }
    requestAnimationFrame(step);
    statObserver.unobserve(el);
  });
}, { threshold: 0.5 });
document.querySelectorAll('.stat-num').forEach(function(el) { statObserver.observe(el); });
```

**Betroffene Dateien:**

- `reference/Getmatic_website/index.html`
- `reference/Getmatic_website/style.css`

---

### Schritt 4: Neue Sektion „Unser Vorgehen" auf `index.html`

**Aktionen:**

In `style.css`, neuer Abschnitt (nach `LEISTUNGEN`, vor `WARUM GETMATIC`):

```css
/* =========================================
   UNSER VORGEHEN
   ========================================= */
.vorgehen {
  padding: var(--space-7) 0;
  background: var(--bg);
}

.vorgehen-steps {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: var(--space-4);
  position: relative;
}

.vorgehen-steps::before {
  content: '';
  position: absolute;
  top: 28px;
  left: 8%;
  right: 8%;
  height: 2px;
  background: linear-gradient(90deg, var(--teal) 0%, rgba(32,157,157,0.15) 100%);
  z-index: 0;
}

.vorgehen-step { position: relative; z-index: 1; }

.vorgehen-num {
  width: 56px;
  height: 56px;
  border-radius: 50%;
  background: var(--bg-white);
  border: 2px solid var(--teal);
  color: var(--teal);
  font-family: var(--font-head);
  font-weight: 800;
  font-size: 1.3rem;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-bottom: var(--space-2);
  box-shadow: var(--shadow);
}

.vorgehen-step h3 {
  font-family: var(--font-head);
  font-size: 1.1rem;
  font-weight: 700;
  color: var(--text-dark);
  margin-bottom: 6px;
}

.vorgehen-step p {
  font-size: 0.88rem;
  color: var(--text-light);
  line-height: 1.6;
}
```

Im bestehenden `@media (max-width: 768px)`-Block ergänzen:
```css
.vorgehen-steps { grid-template-columns: 1fr; gap: var(--space-3); }
.vorgehen-steps::before { display: none; }
```

In `index.html`, nach dem schließenden `</section>` der Leistungen-Sektion und vor `<section class="warum-band">` einfügen:

```html
<!-- Unser Vorgehen -->
<section class="vorgehen">
  <div class="container">
    <div class="section-label animate-in" data-i18n="vorg-label">So arbeiten wir</div>
    <h2 class="section-title animate-in" style="--d:.05s" data-i18n="vorg-title">Unser Vorgehen</h2>
    <div class="vorgehen-steps">
      <div class="vorgehen-step animate-in" style="--d:.1s">
        <div class="vorgehen-num">01</div>
        <h3 data-i18n="vorg1-h">Anfrage &amp; Erstgespräch</h3>
        <p data-i18n="vorg1-p">Sie schildern Ihr Automatisierungsvorhaben — telefonisch, per E-Mail oder vor Ort.</p>
      </div>
      <div class="vorgehen-step animate-in" style="--d:.2s">
        <div class="vorgehen-num">02</div>
        <h3 data-i18n="vorg2-h">Analyse &amp; Konzept</h3>
        <p data-i18n="vorg2-p">Prüfung der bestehenden Anlage bzw. Anforderungen, Erarbeitung eines passenden Steuerungs- oder Dokumentationskonzepts.</p>
      </div>
      <div class="vorgehen-step animate-in" style="--d:.3s">
        <div class="vorgehen-num">03</div>
        <h3 data-i18n="vorg3-h">Programmierung &amp; Umsetzung</h3>
        <p data-i18n="vorg3-p">Realisierung mit Step 7, TIA Portal oder als individuelle Embedded-Lösung — laufend mit Ihnen abgestimmt.</p>
      </div>
      <div class="vorgehen-step animate-in" style="--d:.4s">
        <div class="vorgehen-num">04</div>
        <h3 data-i18n="vorg4-h">Inbetriebnahme &amp; Support</h3>
        <p data-i18n="vorg4-p">Test, Inbetriebnahme vor Ort und Ansprechbarkeit auch nach Projektabschluss.</p>
      </div>
    </div>
  </div>
</section>
```

Im `translations`-Objekt von `index.html`, DE-Block nach den neuen `stat3-label`-Keys aus Schritt 3 ergänzen:
```js
'vorg-label': 'So arbeiten wir',
'vorg-title': 'Unser Vorgehen',
'vorg1-h': 'Anfrage &amp; Erstgespräch',
'vorg1-p': 'Sie schildern Ihr Automatisierungsvorhaben — telefonisch, per E-Mail oder vor Ort.',
'vorg2-h': 'Analyse &amp; Konzept',
'vorg2-p': 'Prüfung der bestehenden Anlage bzw. Anforderungen, Erarbeitung eines passenden Steuerungs- oder Dokumentationskonzepts.',
'vorg3-h': 'Programmierung &amp; Umsetzung',
'vorg3-p': 'Realisierung mit Step 7, TIA Portal oder als individuelle Embedded-Lösung — laufend mit Ihnen abgestimmt.',
'vorg4-h': 'Inbetriebnahme &amp; Support',
'vorg4-p': 'Test, Inbetriebnahme vor Ort und Ansprechbarkeit auch nach Projektabschluss.',
```
EN-Block analog:
```js
'vorg-label': 'How We Work',
'vorg-title': 'Our Process',
'vorg1-h': 'Inquiry &amp; Initial Discussion',
'vorg1-p': 'You describe your automation project — by phone, email, or on site.',
'vorg2-h': 'Analysis &amp; Concept',
'vorg2-p': 'Assessment of the existing system or requirements, development of a suitable control or documentation concept.',
'vorg3-h': 'Programming &amp; Implementation',
'vorg3-p': 'Realization with Step 7, TIA Portal, or as a custom embedded solution — coordinated with you throughout.',
'vorg4-h': 'Commissioning &amp; Support',
'vorg4-p': 'Testing, on-site commissioning, and availability even after project completion.',
```

**Betroffene Dateien:**

- `reference/Getmatic_website/index.html`
- `reference/Getmatic_website/style.css`

---

### Schritt 5: Hero mutiger gestalten

**Aktionen:**

In `style.css`, Abschnitt `HERO`:
- `.hero-gallery` Regel: `height: clamp(380px, 60vh, 620px);` ersetzen durch `height: clamp(460px, 68vh, 720px);`
- `.hero-content h1` Regel: `font-size: clamp(2.4rem, 6vw, 4.5rem);` ersetzen durch `font-size: clamp(2.6rem, 6.5vw, 5.2rem);`
- `.hero-overlay` Regel ersetzen durch:
  ```css
  .hero-overlay {
    position: absolute;
    inset: 0;
    background:
      radial-gradient(ellipse at 75% 15%, rgba(32,157,157,0.35) 0%, rgba(32,157,157,0) 55%),
      linear-gradient(105deg,
        rgba(16,20,21,0.86) 0%,
        rgba(16,20,21,0.55) 45%,
        rgba(16,20,21,0.15) 100%
      );
  }
  ```
- Neue Regeln für die Kompetenz-Pills ergänzen:
  ```css
  .hero-pills {
    display: flex;
    flex-wrap: wrap;
    gap: 8px;
    margin-bottom: var(--space-4);
  }
  .hero-pill {
    font-family: var(--font-body);
    font-size: 0.78rem;
    font-weight: 600;
    letter-spacing: 0.3px;
    color: #fff;
    background: rgba(32,157,157,0.22);
    border: 1px solid rgba(90,215,215,0.4);
    backdrop-filter: blur(4px);
    padding: 6px 14px;
    border-radius: 999px;
  }
  ```
- Im bestehenden `@media (max-width: 768px)`-Block, Abschnitt „Hero", ergänzen: `.hero-gallery { height: clamp(420px, 62vh, 560px); }` (überschreibt die vergrößerte Basis-Höhe für mittlere Bildschirme, bevor die 480px-Regel für sehr kleine Screens greift).

In `index.html`, im **ersten** Hero-Slide (`Systeme.svg`) nach dem `.hero-sub`-Absatz und vor dem `.btn-primary`-Link einfügen:
```html
            <div class="hero-pills">
              <span class="hero-pill">SPS</span>
              <span class="hero-pill">Antriebstechnik</span>
              <span class="hero-pill">TIA Portal</span>
              <span class="hero-pill">Raspberry Pi</span>
            </div>
```
Die übrigen 4 Slides bleiben unverändert (Pills nur im Intro-Slide, siehe Design-Entscheidung — vermeidet Wiederholung auf jedem Slide).

**Betroffene Dateien:**

- `reference/Getmatic_website/index.html`
- `reference/Getmatic_website/style.css`

---

### Schritt 6: Mikro-Interaktionen — Shine-Sweep-Buttons + Glow-Schatten auf Karten

**Aktionen:**

In `style.css` folgende Regeln ergänzen (z. B. direkt vor dem `ANIMATIONEN`-Abschnitt):

```css
/* =========================================
   MIKRO-INTERAKTIONEN
   ========================================= */
.btn-primary,
.einsatz-cta,
.kontakt-link {
  position: relative;
  overflow: hidden;
}
.btn-primary::after,
.einsatz-cta::after,
.kontakt-link::after {
  content: '';
  position: absolute;
  top: 0;
  left: -60%;
  width: 40%;
  height: 100%;
  background: linear-gradient(120deg, transparent, rgba(255,255,255,0.35), transparent);
  transform: skewX(-20deg);
  transition: left 0.55s ease;
  pointer-events: none;
}
.btn-primary:hover::after,
.einsatz-cta:hover::after,
.kontakt-link:hover::after {
  left: 130%;
}
```

Bestehende Hover-Schatten auf Glow-Token umstellen:
- `.leistung-card:hover` Regel: `box-shadow: var(--shadow-lg);` ersetzen durch `box-shadow: var(--shadow-glow);`
- `.anlagen-item:hover` Regel: `box-shadow: var(--shadow-lg);` ersetzen durch `box-shadow: var(--shadow-glow);`

Im bestehenden `@media (prefers-reduced-motion: reduce)`-Block ergänzen:
```css
.btn-primary::after,
.einsatz-cta::after,
.kontakt-link::after {
  transition: none;
}
```

**Betroffene Dateien:**

- `reference/Getmatic_website/style.css`

---

### Schritt 7: Lokal testen

**Aktionen:**

- Lokalen Server aus `reference/Getmatic_website/` starten (`http-server -p 8080 -o "index.html"`, ggf. `MSYS_NO_PATHCONV=1` voranstellen).
- Playwright-Check auf Desktop (1440×900) und Mobile (390×844):
  - Alle 8 Seiten: keine Konsolenfehler, kein horizontaler Overflow (`document.documentElement.scrollWidth === window.innerWidth`) — **insbesondere erneut auf CSS-Spezifitätskonflikte prüfen**, analog zu den zwei in Iteration 1 gefundenen Bugs (Dropdown-Menü/`prefers-reduced-motion`), da diese Iteration ebenfalls mehrere neue, ineinandergreifende Selektoren einführt.
  - `index.html`: Stats-Band sichtbar direkt unter dem Hero, Zahlen zählen beim Einscrollen sichtbar von 0 auf 5/3 hoch; bei `prefers-reduced-motion: reduce` erscheinen die Zielwerte sofort ohne Zählanimation (per `page.evaluate` `getComputedStyle`/direkte `textContent`-Prüfung unmittelbar nach Intersection, nicht erst nach 900ms).
  - `index.html`: „Unser Vorgehen"-Sektion zeigt 4 Schritte mit Verbindungslinie auf Desktop, Linie ausgeblendet + einspaltig auf Mobile.
  - `index.html`: „Warum GeTMatic" zeigt jetzt dunklen Hintergrund, weißer/teal-farbener Text gut lesbar (Kontrast-Stichprobe).
  - `index.html`: Hero zeigt die 4 Kompetenz-Pills nur im ersten Slide, größere Höhe/Typografie, radialer Teal-Glow im Overlay sichtbar.
  - Hover-Test (Desktop): Shine-Sweep-Effekt auf `.btn-primary`, `.einsatz-cta`, `.kontakt-link` läuft beim Hover sichtbar durch; Leistungen-Karten und Anlagen-Kacheln zeigen beim Hover den neuen teal-getönten Glow-Schatten statt des alten neutralen Schattens.
  - DE/EN-Umschaltung auf `index.html`: alle neuen Keys (`stat*`, `vorg*`) korrekt übersetzt, keine sichtbaren Rohkeys.
  - Regressionscheck: Iteration-1-Elemente (Dropdown-Nav, 5. Leistungs-Karte, Anlagen-Zickzack, DocuControl-FAQ) funktionieren weiterhin unverändert auf allen 8 Seiten, insbesondere nach der Farb-Token-Änderung (Kontrast/Sichtbarkeit von Texten auf den jetzt dunkleren Header-/Footer-/Dropdown-Flächen prüfen).
- Screenshots von Hero, Stats-Band, Vorgehen-Sektion und dunklem Warum-Bereich sichten.

**Betroffene Dateien:**

- Keine (Testschritt).

---

### Schritt 8: `context/current-data.md` aktualisieren

**Aktionen:**

- Neuen datierten Eintrag unter „Aktueller Stand" ergänzen: Farbpalette modernisiert (Werte + Begründung kurz), neue Stats-Band + Vorgehen-Sektion, Warum-Band auf dunkel umgestellt, Hero-Erweiterungen, Mikro-Interaktionen — mit Hinweis auf offene Bestätigung des Gründungsjahres.
- Neuen Punkt unter „Offene Aufgaben / nächste Schritte" ergänzen: Re-Upload für `index.html` + `style.css` (Iteration 2), idealerweise gebündelt mit dem noch ausstehenden Iteration-1-Upload.

**Betroffene Dateien:**

- `context/current-data.md`

---

## Verbindungen & Abhängigkeiten

### Dateien, die diesen Bereich referenzieren

- Alle 8 HTML-Seiten binden `style.css` ein — die Farb-Token-Änderung (Schritt 1) wirkt automatisch überall, ohne dass diese Dateien einzeln angefasst werden müssen.
- Baut direkt auf `plans/2026-09-17-website-redesign-navigation-inhalt.md` auf (Dropdown-Nav, Warum-Sektion, Anlagen-Zickzack, Leistungen-Karten — alle aus Iteration 1 werden hier nur farblich/interaktiv erweitert, nicht strukturell verändert).

### Nötige Updates für Konsistenz

- `context/current-data.md` (Schritt 8).
- Kein `CLAUDE.md`-Update nötig: Workspace-Struktur ändert sich nicht.

### Auswirkungen auf bestehende Workflows

- Keine Auswirkung auf den internen Mitarbeiterbereich (`intern/`) oder bestehende Deployment-Workflows.
- Da Iteration 1 noch nicht live hochgeladen wurde, sollten Iteration 1 und Iteration 2 idealerweise **gemeinsam** in einem Re-Upload-Batch hochgeladen werden (beide betreffen dieselben Dateien: alle 8 HTML-Seiten + `style.css`).

---

## Validierungs-Checkliste

- [ ] Neue Farb-Tokens in `:root` gesetzt, Teal/Teal-Light/Teal-Dark unverändert
- [ ] „Warum GeTMatic" zeigt dunklen Hintergrund mit gut lesbarem hellem Text
- [ ] Stats-Band sichtbar unter dem Hero, Zahlen zählen beim Scroll hoch, respektiert `prefers-reduced-motion`
- [ ] „Unser Vorgehen"-Sektion mit 4 Schritten + Verbindungslinie (Desktop) / einspaltig ohne Linie (Mobile)
- [ ] Hero zeigt Kompetenz-Pills (nur Slide 1), größere Typografie/Höhe, Teal-Glow im Overlay
- [ ] Shine-Sweep-Hover auf allen Primär-Buttons, Glow-Schatten auf Leistungen-Karten und Anlagen-Kacheln
- [ ] Alle neuen i18n-Keys vollständig in DE **und** EN, keine sichtbaren Rohkeys
- [ ] Lokaler Playwright-Test auf allen 8 Seiten: keine Konsolenfehler, kein horizontaler Overflow, kein neuer CSS-Spezifitätsbug
- [ ] `context/current-data.md` enthält neuen Eintrag + Upload-Checkliste-Punkt

---

## Erfolgskriterien

Die Implementierung ist abgeschlossen, wenn:

1. Die Seite auf den ersten Blick moderner/kontrastreicher wirkt (kühlere, dunklere Header-/Footer-Töne, cleanerer Hintergrund) — bei gleichbleibendem Teal-Markenbild.
2. Die Startseite einen klaren Hell-Dunkel-Rhythmus zeigt und mit Stats-Band, Vorgehen-Sektion und den Hero-Erweiterungen spürbar mehr visuelle Vielfalt und „Wow-Effekt" bietet als vor dieser Iteration.
3. Alle neuen Inhalte (Kennzahlen, Prozess-Schritte) ausschließlich auf bereits verifizierten Fakten beruhen — keine erfundenen Zahlen oder Referenzen.
4. Alle Mikro-Interaktionen funktionieren, respektieren `prefers-reduced-motion`, und es treten keine neuen CSS-Spezifitäts- oder Overflow-Bugs auf (verifiziert per Playwright, analog zur Testmethodik aus Iteration 1).

---

## Notizen

- Diese Iteration ist bewusst als **Aufsatz auf Iteration 1** konzipiert, nicht als Ersatz — beide Pläne zusammen ergeben den vollständigen Redesign-Umfang, der beim nächsten Re-Upload gemeinsam live gehen sollte.
- Perspektivisch mögliche, aber bewusst nicht in diesem Plan enthaltene Folgeschritte:
  - Angled/schräge Sektions-Trenner per Clip-Path (siehe Design-Entscheidung 9) — falls der Nutzer nach dieser Iteration weiterhin mehr „Wow" wünscht, als risikoärmere Einzelmaßnahme in einer separaten, kleinen Iteration umsetzbar.
  - Eigenes Farbschema/Foto für eine Autoren-/Über-mich-Box (aktuell nur Initialen-Avatar im separaten Wissen-Plan-Entwurf vorgesehen, nicht Teil dieses Plans).
- Deployment-Hinweis (nicht Teil dieses Plans): Beim späteren Live-Upload wie immer **Überschreiben statt Resume** verwenden (bekannte 1blu/FileZilla-Falle).

---

## Implementierungsnotizen

**Implementiert:** 2026-09-17

### Zusammenfassung

Alle 8 Schritte umgesetzt: Farb-Tokens modernisiert (`:root` in `style.css`, wirkt automatisch auf allen 8 Seiten), „Warum GeTMatic" auf dunkles Farbschema umgestellt, neue Stats-Band mit Zähl-Animation (5 Einsatzbereiche, 3 Branchen, SPS-Systembandbreite, „seit 2019" — vom Nutzer bestätigt), neue Sektion „Unser Vorgehen" (4 Prozess-Schritte), Hero vergrößert + Kompetenz-Pills + stärkerer Overlay-Glow, Shine-Sweep-Hover auf Buttons + Glow-Schatten auf Karten. Lokal mit Playwright getestet: alle 8 Seiten Desktop (1440×900) + Mobile (390×844), kein horizontaler Overflow, keine Konsolenfehler, Stats-Werte korrekt, Vorgehen-Schritte korrekt, Warum-Dark-Theme-Farben verifiziert (Hintergrund-Gradient, weißer Text, Amber-Nummern), Hero-Pills vorhanden, Shine-Sweep-Hover funktional, Glow-Schatten korrekt (teal-getönt), `prefers-reduced-motion` deaktiviert Shine-Sweep-Transition und zeigt Stats-Werte sofort ohne Zählanimation. `context/current-data.md` mit datiertem Eintrag + Upload-Checkliste-Punkt aktualisiert.

### Abweichungen vom Plan

Die „Unser Vorgehen"-CSS-Sektion wurde im Stylesheet zwischen den bestehenden CSS-Blöcken `ANLAGEN` und `WARUM GETMATIC` eingefügt statt exakt „nach LEISTUNGEN" (wie im Plan-Wortlaut) — inhaltlich unerheblich, da CSS-Selektoren unabhängig von ihrer Position im Stylesheet wirken (keine Spezifitätskonflikte mit den neuen Klassen) und die HTML-Reihenfolge der Sektionen exakt wie geplant ist (Hero → Stats → Leistungen → Vorgehen → Warum → Anlagen → Kontakt).

### Aufgetretene Probleme

Keine — anders als bei Iteration 1 wurden diesmal keine CSS-Spezifitätsbugs oder Overflow-Probleme gefunden (Playwright-Tests liefen beim ersten Durchlauf vollständig grün).
