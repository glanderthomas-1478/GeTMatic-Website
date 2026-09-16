# Plan: Werbevideo als Hero-Video auf der Getränkeverpackung-Unterseite

**Erstellt:** 2026-09-16
**Status:** Entwurf
**Anforderung:** Vorhandenes Werbevideo (`reference/Video/werbevideo_neuschnitt_electro_house.mp4`) für's Web aufbereiten und als stummes Autoplay-Loop-Hero-Video auf `einsatzbereich-getraenke.html` einbauen — ersetzt die aktuelle statische SVG-Hero-Grafik.

---

## Überblick

### Was dieser Plan erreicht

Der aktuell statische SVG-Hero-Banner auf der Getränkeverpackung-Unterseite wird durch ein echtes Produktionsvideo (Nahaufnahmen einer laufenden Kartonverpackungslinie) ersetzt. Das Video läuft stumm, automatisch und in Endlosschleife — wie ein bewegtes Foto, ohne Tonspur-Rechtefrage. Das Rohvideo (11,4 MB, mit Musikspur) wird dafür komprimiert, von Audio befreit und lokal gehostet.

### Warum das wichtig ist

Getränkeverpackung ist laut User eine Haupteinnahmequelle von getmatic. Echtes Bewegtbild einer laufenden Anlage schafft mehr Vertrauen und Kompetenzeindruck bei der Zielgruppe (Entscheider/Techniker in der Industrie) als eine Illustration. Dies ist Schritt 1 einer größeren Hervorhebungs-Initiative für diese Rubrik — weitere Schritte (Startseiten-Kachel, Hero-Slide) sind bewusst nicht Teil dieses Plans und werden separat geplant.

---

## Aktueller Zustand

### Relevante bestehende Struktur

- `reference/Video/werbevideo_neuschnitt_electro_house.mp4` — Rohvideo, vom User abgelegt. 46,2 s, 480×480 px (quadratisch), H.264/AAC, 11,4 MB, mit Musikspur ("electro house"-Schnitt). Inhalt: Nahaufnahmen einer laufenden Kartonverpackungslinie (Falten/Bedrucken von Kartonmaterial in Bewegung) — inhaltlich passend zur Rubrik.
- `reference/Getmatic_website/einsatzbereich-getraenke.html` — Zielseite. Hero-Bereich aktuell:
  ```html
  <div class="einsatz-hero animate-in" style="--img: url('getraenkeverpackung.svg'); --d:.1s" role="img" aria-label="Getränkeverpackung Tetra Pak Produktion"></div>
  ```
- `reference/Getmatic_website/style.css`:
  - `.einsatz-hero` (Zeile 845–853): feste Höhe (`clamp(220px, 32vw, 360px)`), `border-radius`, `background-image: var(--img)`, `box-shadow`. Kein `position`/`overflow` gesetzt.
  - `.hero-video` (Zeile 250–257): bereits vorhandene, aktuell **ungenutzte** Klasse — `position: absolute; inset: 0; width: 100%; height: 100%; object-fit: cover; pointer-events: none;`. Exakt das Positionierungs-/Skalierungsverhalten, das ein Video innerhalb eines Hero-Containers braucht.
  - `@media (prefers-reduced-motion: reduce)` existiert bereits (Zeile 701) für die `animate-in`-Scroll-Animationen.
- `reference/Getmatic_website/getraenkeverpackung.svg` — wird weiterhin auf der Startseiten-Kachel (`index.html`, Anlagen-Item 04) und im `og:image`-Meta-Tag von `einsatzbereich-getraenke.html` verwendet. **Bleibt unverändert und wird nicht gelöscht.**
- Kein bestehendes `<video>`-Element irgendwo auf der Website aktiv im Einsatz — dies ist der erste produktive Einsatz von Video-Content.

### Lücken oder Probleme, die adressiert werden

- Die statische SVG-Illustration im Hero wirkt weniger überzeugend als echtes Anlagen-Bewegtbild für eine Kernumsatz-Rubrik.
- Das Rohvideo ist in aktueller Form (11,4 MB, mit Musikspur, quadratisch) nicht web-tauglich: zu groß für einen Hero-Loop, Musikspur wirft Autoplay-/Rechtefragen auf, kein Poster-Fallback für die Ladezeit.

---

## Vorgeschlagene Änderungen

### Zusammenfassung der Änderungen

- Rohvideo mit ffmpeg verarbeiten: Audiospur vollständig entfernen, mit `libx264`/CRF 27 neu kodieren, `faststart` für progressives Laden.
- Ein Vorschaubild (Poster-Frame) aus dem Video extrahieren für die Ladephase.
- Beide Dateien nach `reference/Getmatic_website/` legen (Upload-bereiter Ordner, wie alle anderen Web-Assets).
- `.einsatz-hero`-Hero-Block in `einsatzbereich-getraenke.html` von SVG-Hintergrund-Div auf `<video>`-Element (wiederverwendet die bestehende `.hero-video`-Klasse) umstellen.
- `.einsatz-hero` in `style.css` um `position: relative; overflow: hidden;` ergänzen, damit das absolut positionierte Video sauber im abgerundeten Hero-Rahmen sitzt.
- Kleines Inline-Script ergänzen, das bei `prefers-reduced-motion: reduce` das Video pausiert (Poster bleibt sichtbar) statt es abspielen zu lassen.
- Lokal mit `http-server` + Playwright testen (Desktop + Mobile, Autoplay, kein Overflow, Konsolenfehler).
- `context/current-data.md` mit einem datierten Eintrag + neuem "Noch hochzuladen"-Punkt aktualisieren.

### Neue Dateien erstellen

| Dateipfad | Zweck |
| --- | --- |
| `reference/Getmatic_website/getraenke-produktion.mp4` | Web-optimierte, tonlose Version des Werbevideos — wird als Hero-Video eingebunden |
| `reference/Getmatic_website/getraenke-produktion-poster.jpg` | Einzelbild aus dem Video (Poster-Attribut), zeigt echten Produktionsinhalt statt der SVG-Illustration während des Ladens |

### Zu ändernde Dateien

| Dateipfad | Änderungen |
| --- | --- |
| `reference/Getmatic_website/einsatzbereich-getraenke.html` | Hero-Div von `--img`-Hintergrundbild auf `<video class="hero-video">`-Kind-Element umstellen; kleines Inline-Script für `prefers-reduced-motion` ergänzen |
| `reference/Getmatic_website/style.css` | `.einsatz-hero`-Regel (Zeile ~845) um `position: relative; overflow: hidden;` ergänzen |
| `context/current-data.md` | Neuer datierter Eintrag zur Video-Integration + neuer Punkt in "Offene Aufgaben / nächste Schritte" (Upload-Status) |

### Zu löschende Dateien

Keine. Das Rohvideo in `reference/Video/` bleibt als Quellmaterial erhalten. `getraenkeverpackung.svg` bleibt für Startseiten-Kachel und OG-Meta-Tag im Einsatz.

---

## Design-Entscheidungen

### Getroffene Schlüsselentscheidungen

1. **Audiospur komplett entfernen (nicht nur `muted`-Attribut client-seitig)**: Physisch aus der Datei entfernt statt nur stummgeschaltet — spart zusätzlich Dateigröße und macht die Musik-Lizenzfrage für die öffentliche Website gegenstandslos, da keine Tonspur mehr ausgeliefert wird. (Vom User bereits so entschieden: "stumm, automatisch, in Schleife".)
2. **CRF 27, `libx264`, `preset slow`**: Testkodierung ergab **~2,0 MB** für die vollen 46 s (gegenüber 11,4 MB Original) bei visuell verlustarmer Qualität für ein Hero-Hintergrundvideo. Kein Trimmen auf einen kürzeren Ausschnitt nötig — bei dieser Dateigröße ist die volle 46-s-Sequenz problemlos web-tauglich und zeigt mehr Abwechslung als ein kurzer Loop.
3. **Bestehende `.hero-video`-CSS-Klasse wiederverwenden statt neuer Klasse**: Sie enthält bereits exakt das benötigte Positionierungs-/Skalierungsverhalten (`position:absolute; inset:0; object-fit:cover`), war aber bisher ungenutzt. Minimiert den CSS-Diff.
4. **Poster-Frame statt Wiederverwendung der SVG-Illustration**: Ein echtes Video-Standbild passt visuell nahtloser zum nachfolgenden Video als die Icon-artige SVG-Grafik und vermeidet einen sichtbaren "Bildwechsel"-Ruck beim Start der Wiedergabe.
5. **`prefers-reduced-motion`-Handling per JS**: Das HTML-`autoplay`-Attribut lässt sich nicht per CSS bedingt deaktivieren. Kleines Script prüft `matchMedia('(prefers-reduced-motion: reduce)')` und pausiert das Video sofort nach dem Laden, falls aktiv — Poster bleibt sichtbar. Konsistent mit der bereits vorhandenen `prefers-reduced-motion`-Behandlung der Scroll-Animationen im selben Script-Block.
6. **`playsinline` + `muted` + `autoplay` als HTML-Attribute (nicht nur per JS gesetzt)**: Erforderlich, damit Autoplay zuverlässig auf iOS Safari funktioniert.
7. **Lokale Datei, kein externer Hosting-Dienst (z. B. YouTube-Embed)**: Konsistent mit der bestehenden Praxis der Website (lokale Fonts, lokal gehostetes Three.js) — kein Drittanbieter-Request, keine DSGVO-Frage.

### Betrachtete Alternativen

- **Video zusätzlich auf der Startseiten-Kachel einbauen**: Vom User explizit auf später verschoben — nicht Teil dieses Plans.
- **Video auf einen kürzeren Ausschnitt (z. B. 10–15 s) trimmen**: Verworfen, da die Kompression allein bereits auf ~2 MB kommt — Trimmen würde zusätzliche Schnitt-Entscheidungen erfordern (welcher Ausschnitt?), ohne einen relevanten Performance-Vorteil zu bringen.
- **Video mit Ton + Klick-zum-Abspielen**: Vom User abgelehnt zugunsten der einfacheren, rechtlich unproblematischen Stumm-Loop-Variante.
- **WebM zusätzlich zu MP4 als Format-Fallback anbieten**: Verworfen als Überengineering für dieses kleine Feature — H.264/MP4 wird von allen relevant genutzten Browsern nativ unterstützt, ein zweites Encoding erhöht nur Wartungsaufwand ohne spürbaren Nutzen hier.

### Offene Fragen

Keine — alle notwendigen Entscheidungen (Platzierung, Audio-Handling, Scope) wurden vom User vor Planerstellung getroffen.

---

## Schritt-für-Schritt-Aufgaben

### Schritt 1: Video komprimieren und Audiospur entfernen

Das Rohvideo mit ffmpeg neu kodieren: Audiospur entfernen, `libx264`/CRF 27/`preset slow`, `+faststart` für progressives Laden im Browser, `yuv420p`-Pixelformat für maximale Browser-Kompatibilität.

**Aktionen:**

- Ausführen:
  ```bash
  ffmpeg -i "reference/Video/werbevideo_neuschnitt_electro_house.mp4" \
    -an -c:v libx264 -preset slow -crf 27 -pix_fmt yuv420p -movflags +faststart \
    "reference/Getmatic_website/getraenke-produktion.mp4"
  ```
- Ergebnisgröße prüfen (`ls -la`) — Zielkorridor ca. 1,5–2,5 MB. Falls deutlich darüber, CRF schrittweise auf 29–30 erhöhen und Bildqualität per Sichtprüfung (Frame-Extraktion) gegenprüfen.

**Betroffene Dateien:**

- `reference/Getmatic_website/getraenke-produktion.mp4` (neu)

---

### Schritt 2: Poster-Frame extrahieren

Ein repräsentatives Einzelbild aus dem komprimierten Video als JPG extrahieren, das während des Ladens angezeigt wird.

**Aktionen:**

- Einen visuell klaren Frame wählen (z. B. bei Sekunde 2–3, dort zeigte die Stichprobe eine gut erkennbare Maschinen-Nahaufnahme ohne Bewegungsunschärfe).
- Ausführen:
  ```bash
  ffmpeg -ss 00:00:02 -i "reference/Getmatic_website/getraenke-produktion.mp4" \
    -frames:v 1 -q:v 3 "reference/Getmatic_website/getraenke-produktion-poster.jpg"
  ```
- Bild sichten (Read-Tool) und bei Bedarf einen anderen Zeitpunkt wählen, falls der Frame unscharf oder wenig aussagekräftig ist.

**Betroffene Dateien:**

- `reference/Getmatic_website/getraenke-produktion-poster.jpg` (neu)

---

### Schritt 3: CSS anpassen — `.einsatz-hero` positionierungsfähig machen

`.einsatz-hero` braucht `position: relative` und `overflow: hidden`, damit das absolut positionierte `.hero-video`-Kind-Element korrekt geclippt und in den abgerundeten Rahmen eingepasst wird.

**Aktionen:**

- In `reference/Getmatic_website/style.css`, Regel `.einsatz-hero` (aktuell Zeile ~845–853), folgende zwei Deklarationen ergänzen:
  ```css
  .einsatz-hero {
    position: relative;
    overflow: hidden;
    height: clamp(220px, 32vw, 360px);
    border-radius: var(--radius);
    background-image: var(--img);
    background-size: cover;
    background-position: center;
    margin-bottom: var(--space-5);
    box-shadow: var(--shadow);
  }
  ```
- Keine Auswirkung auf andere Seiten, die `.einsatz-hero` mit SVG-Hintergrund nutzen (Transport, Papier, Lebensmittel, Medizin) — `position`/`overflow` ändern dort nichts sichtbar, da sie keine absolut positionierten Kind-Elemente enthalten.

**Betroffene Dateien:**

- `reference/Getmatic_website/style.css`

---

### Schritt 4: Hero-Markup in `einsatzbereich-getraenke.html` umstellen

Das bestehende Hero-Div von einem reinen Hintergrundbild-Div auf ein Container-Div mit `<video>`-Kind-Element umstellen.

**Aktionen:**

- Ersetzen:
  ```html
  <div class="einsatz-hero animate-in" style="--img: url('getraenkeverpackung.svg'); --d:.1s" role="img" aria-label="Getränkeverpackung Tetra Pak Produktion"></div>
  ```
  durch:
  ```html
  <div class="einsatz-hero animate-in" style="--d:.1s">
    <video class="hero-video" autoplay muted loop playsinline preload="auto"
           poster="getraenke-produktion-poster.jpg"
           aria-label="Getränkeverpackung Tetra Pak Produktion – laufende Kartonverpackungslinie">
      <source src="getraenke-produktion.mp4" type="video/mp4">
    </video>
  </div>
  ```
- `role="img"` entfällt, da `<video>` mit `aria-label` bereits eine eigene Zugänglichkeits-Semantik hat.

**Betroffene Dateien:**

- `reference/Getmatic_website/einsatzbereich-getraenke.html`

---

### Schritt 5: `prefers-reduced-motion`-Handling ergänzen

Im bestehenden `<script>`-Block der Seite (dort, wo bereits der `IntersectionObserver` für `.animate-in` registriert wird) eine kurze Ergänzung einfügen, die das Video bei aktivierter Bewegungsreduzierung pausiert.

**Aktionen:**

- Nach der bestehenden Zeile
  ```js
  document.querySelectorAll('.animate-in').forEach(function(el) { observer.observe(el); });
  ```
  ergänzen:
  ```js
  var heroVideo = document.querySelector('.einsatz-hero video');
  if (heroVideo && window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
    heroVideo.removeAttribute('autoplay');
    heroVideo.pause();
  }
  ```

**Betroffene Dateien:**

- `reference/Getmatic_website/einsatzbereich-getraenke.html`

---

### Schritt 6: Lokal testen

Mit lokalem Server + Playwright verifizieren, dass alles funktioniert, bevor der User live hochlädt.

**Aktionen:**

- Lokalen Server aus `reference/Getmatic_website/` starten (z. B. `http-server -p 8080 -o "einsatzbereich-getraenke.html"`, `MSYS_NO_PATHCONV=1` voranstellen falls Git Bash den Pfad verändert — siehe bekannte Falle in `context/current-data.md`, 2026-07-08).
- Playwright-Check auf Desktop-Viewport (z. B. 1440×900):
  - Kein Konsolenfehler.
  - `document.querySelector('.einsatz-hero video').paused === false` nach kurzer Wartezeit (Autoplay funktioniert).
  - `document.documentElement.scrollWidth === window.innerWidth` (kein horizontaler Overflow).
  - Screenshot des Hero-Bereichs sichten — Video füllt den abgerundeten Rahmen sauber, kein Verzerren/Überstehen.
- Playwright-Check auf Mobile-Viewport (z. B. 390×844): dieselben Prüfungen, zusätzlich Sichtprüfung, dass der Bildausschnitt (quadratisches Quellmaterial in schmalerem Container) noch sinnvoll aussieht.
- Prüfen, ob `prefers-reduced-motion: reduce` (Playwright: `page.emulateMedia({ reducedMotion: 'reduce' })`) das Video zuverlässig pausiert.
- DE/EN-Sprachumschalter kurz testen — unabhängig vom Video, aber Regressionscheck, da derselbe Script-Block bearbeitet wurde.

**Betroffene Dateien:**

- Keine (Testschritt, nur lokale Verifikation)

---

### Schritt 7: `context/current-data.md` aktualisieren

Neuen datierten Eintrag ergänzen (Konvention der Datei: chronologische Einträge unter "Aktueller Stand", plus Punkt in "Offene Aufgaben / nächste Schritte").

**Aktionen:**

- Neuen Eintrag unter "Aktueller Stand" ergänzen, z. B.:
  ```
  - **2026-09-16:** Werbevideo (`reference/Video/werbevideo_neuschnitt_electro_house.mp4`, Rohmaterial vom User) als Hero-Video auf `einsatzbereich-getraenke.html` eingebaut — ersetzt die statische SVG-Illustration. Für Web aufbereitet: Audiospur entfernt, komprimiert (`libx264`, CRF 27, faststart) von 11,4 MB auf ca. <Zielgröße einsetzen> MB, Poster-Frame extrahiert. Stumm, Autoplay, Loop, `playsinline`; pausiert bei `prefers-reduced-motion: reduce`. Neue Dateien: `getraenke-produktion.mp4`, `getraenke-produktion-poster.jpg`. Erster produktiver Video-Einsatz auf der Website. Lokal getestet (Playwright, Desktop/Mobile, Autoplay, reduced-motion, keine Konsolenfehler). **Noch nicht live hochgeladen.** Schritt 1 einer größeren Hervorhebungs-Initiative für die Rubrik Getränkeverpackung (Haupteinnahmequelle) — Startseiten-Sichtbarkeit (Kachel-Position/Hero-Slide) folgt in einem separaten Plan.
  ```
- Neuen Punkt unter "Offene Aufgaben / nächste Schritte" ergänzen:
  ```
  - [ ] Re-Upload für Getränkeverpackung-Hero-Video (2026-09-16) — betroffene Dateien: `einsatzbereich-getraenke.html`, `style.css`, `getraenke-produktion.mp4`, `getraenke-produktion-poster.jpg`. Lokal getestet, **noch nicht live hochgeladen.**
  ```

**Betroffene Dateien:**

- `context/current-data.md`

---

## Verbindungen & Abhängigkeiten

### Dateien, die diesen Bereich referenzieren

- `reference/Getmatic_website/index.html` — verlinkt auf `einsatzbereich-getraenke.html` (Anlagen-Kachel 04) und nutzt weiterhin `getraenkeverpackung.svg` als Kachel-Bild. Nicht Teil dieses Plans, keine Änderung nötig.
- `reference/Getmatic_website/sitemap.xml` — führt `einsatzbereich-getraenke.html` bereits auf, keine Änderung nötig (URL bleibt gleich, nur der Seiteninhalt ändert sich).

### Nötige Updates für Konsistenz

- `context/current-data.md` (siehe Schritt 7) — einzige Dokumentations-Datei, die laufend über Content-Änderungen Buch führt.
- Kein Update an `CLAUDE.md` nötig — keine strukturelle Workspace-Änderung, nur ein Content-/Asset-Update innerhalb der bestehenden Struktur.

### Auswirkungen auf bestehende Workflows

- Keine Auswirkung auf andere Seiten oder Commands. Die CSS-Änderung an `.einsatz-hero` ist additiv (zwei neue Deklarationen) und verändert das Erscheinungsbild der anderen fünf Einsatzbereich-Unterseiten nicht.
- Erstmaliger Einsatz von Video-Content auf der Website — etabliert ein Muster (`.hero-video`-Klasse, Poster-Fallback, `prefers-reduced-motion`-Handling), das bei künftigen Video-Integrationen (z. B. Startseiten-Hero-Slide, s. Notizen) wiederverwendet werden kann.

---

## Validierungs-Checkliste

- [ ] `getraenke-produktion.mp4` existiert in `reference/Getmatic_website/`, ist tonlos, Dateigröße im Zielkorridor (~1,5–2,5 MB)
- [ ] `getraenke-produktion-poster.jpg` existiert, zeigt einen scharfen, aussagekräftigen Frame
- [ ] `.einsatz-hero` in `style.css` hat `position: relative; overflow: hidden;` ergänzt, restliche Deklarationen unverändert
- [ ] `einsatzbereich-getraenke.html`: Hero-Div enthält `<video class="hero-video">` mit `autoplay muted loop playsinline`, korrektem `poster`- und `source src`-Pfad
- [ ] `prefers-reduced-motion`-Script-Ergänzung vorhanden und funktioniert (Playwright-Test mit `reducedMotion: 'reduce'`)
- [ ] Lokaler Test: Video spielt automatisch, kein Ton, läuft in Schleife, kein horizontaler Overflow auf Desktop und Mobile, keine Konsolenfehler
- [ ] DE/EN-Sprachumschalter funktioniert weiterhin (Regressionscheck)
- [ ] `context/current-data.md` enthält neuen Eintrag + Upload-To-do-Punkt mit tatsächlicher finaler Dateigröße

---

## Erfolgskriterien

Die Implementierung ist abgeschlossen, wenn:

1. Die Getränkeverpackung-Unterseite lokal ein stummes, automatisch abspielendes Loop-Video im Hero-Bereich zeigt statt der SVG-Illustration.
2. Die Video-Datei ist ohne Audiospur, komprimiert auf ein für einen Hero-Loop angemessenes Dateigewicht (deutlich unter dem 11,4-MB-Original).
3. Kein Konsolenfehler, kein horizontaler Overflow, funktionierendes `prefers-reduced-motion`-Verhalten — verifiziert per Playwright auf Desktop- und Mobile-Viewport.
4. `context/current-data.md` dokumentiert die Änderung inkl. offenem Upload-Status.

---

## Notizen

- Die weitergehende Hervorhebung der Rubrik Getränkeverpackung (Startseiten-Kachel-Position/-Größe oder ein eigener Hero-Slide auf `index.html`) war Teil der ursprünglichen User-Anfrage, wurde aber bewusst auf einen späteren, separaten Plan verschoben (User-Entscheidung während der Anforderungsklärung).
- Falls sich das mit diesem Plan etablierte Video-Pattern (`.hero-video`-Klasse, Poster, reduced-motion-Handling) bewährt, kann es 1:1 für einen künftigen Video-Hero-Slide auf der Startseite oder für andere Einsatzbereich-Unterseiten wiederverwendet werden.
- Deployment-Reihenfolge beim späteren Live-Upload (nicht Teil dieses Plans, nur Hinweis für später): `einsatzbereich-getraenke.html`, `style.css`, `getraenke-produktion.mp4`, `getraenke-produktion-poster.jpg` — wie immer **überschreiben statt Resume** verwenden (bekannte 1blu/FileZilla-Falle, siehe `context/current-data.md`).
