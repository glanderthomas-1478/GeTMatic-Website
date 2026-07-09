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
- **2026-07-07:** Sektion „Das Gerät" mit 4 freigestellten Hardwarefotos auf `docucontrol.html` erstellt (rembg/onnxruntime, Details siehe Git-Historie) — **noch am selben Tag wieder entfernt** und durch das 3D-Modell (siehe unten) ersetzt, auf Nutzerwunsch. Die 4 Foto-Dateien (`docucontrol-hardware-*.png`) sowie die CSS-Klassen `.einsatz-hardware`/`.einsatz-hardware-item` wurden aus `reference/Getmatic_website/` bzw. `style.css` wieder entfernt. Die freigestellten Rohbilder bleiben als Referenz in `reference/Bilder Docucontrol/` erhalten.
- **2026-07-07/08:** Schematisches, drehbares 3D-Modell des DocuControl-Geräts auf `docucontrol.html` (echtes Three.js-3D, kein Fotoscan — für einen echten Fotoscan reicht die Datenlage der Fotos nicht: zu wenige Aufnahmen, glänzendes Gehäuse killt Photogrammetrie-Feature-Matching, kein Rundum-Coverage). Erste Version: Bildschirm-Quader + Recheneinheit-Box + einfacher Standfuß-Balken aus Grundformen. Neue Dateien: `docucontrol-3d.js`, `vendor/three/three.module.min.js` (670&nbsp;KB, minifiziert) + `vendor/three/OrbitControls.js` — **lokal gehostet statt CDN-Link**, damit kein Live-Request an einen Drittanbieter beim Seitenaufruf entsteht (DSGVO, gleiches Prinzip wie bei den lokalen Fonts). Einbindung per `<script type="importmap">` + `<script type="module">` in `docucontrol.html`, CSS-Klassen `.docu-3d-wrap`/`.docu-3d-canvas` in `style.css`.
- **2026-07-08 (überarbeitet):** 3D-Modell anhand dreier echter Produktfotos des Monitors (`reference/Monitor bilder/` — Front-¾, Rückseite, Seitenansicht) deutlich detailgetreuer nachgebaut. Neue Geometrie in `docucontrol-3d.js`: Bildschirm-Panel als zwei Boxen (schlankes Frontpanel + dickeres Elektronik-Deck unten hinten angesetzt) statt einheitlichem Quader — ergibt den charakteristischen Keil-Querschnitt aus dem Seitenfoto; Rückseite mit Lüftungsrippen-Textur (Canvas-generiert); kleine Lautsprecher-Lüftungsschlitze unten rechts auf der Frontblende; Standfuß komplett neu als Z-förmiger Knickarm (per `ExtrudeGeometry` aus 2D-Profil) + Schwenkgelenk (Zylinder + zwei Schraubenköpfe) + keilförmige Fußplatte mit Einkerbung (ebenfalls `ExtrudeGeometry`) statt einfachem geraden Balken — entspricht jetzt sehr nah der Doppelgelenk-Stand-Silhouette aus den Referenzfotos. Materialien aufgehellt (vorher zu dunkel/detaillos: `bodyMat` 0x14161a → 0x33363b, Ambient-Licht 0.55 → 0.85, Key-Light 1.4 → 1.9) — Form ist jetzt klar erkennbar statt Silhouette. **Lerneffekt für künftige Extrude-Geometrien:** `ExtrudeGeometry` extrudiert entlang der lokalen Z-Achse; nach Rotation zur Achsen-Umlegung führt manuelles `.translate()` zur Zentrierung leicht zu Vorzeichenfehlern (führte hier kurzzeitig zu einem massiv falsch positionierten/übergroß wirkenden Modell) — robuster: Geometrie nach dem Bauen explizit zentrieren (Bounding-Box-basiert) statt Offsets von Hand zu berechnen. Getestet mit lokal installiertem Playwright/Chromium + lokalem `http-server` (nur lokale Test-Tools, nicht Teil der Website) — mehrere Rotationswinkel (Front/Seite/Rück) sowie Mobile-Viewport per Screenshot verifiziert, keine Konsolenfehler. **Vom Nutzer im echten Browser bestätigt** (2026-07-08, dreht sich korrekt). Danach vom Nutzer gemeldeter Bug: „Halbkugel auf dem Display" — Ursache war das Schwenkgelenk (Zylinder), das in festen Weltkoordinaten statt als Kind der `device`-Gruppe platziert war und dadurch aus bestimmten Blickwinkeln vor statt hinter dem Bildschirm erschien. Fix: Gelenk + Schraubenköpfe sind jetzt Kinder von `device` in lokalen Koordinaten (hinter dem Panel, `z = -slabD/2 - 0.55`) — bleiben dadurch bei jeder Kamera-Rotation zuverlässig vom Gehäuse verdeckt. Vom Nutzer bestätigt behoben (2026-07-08). **Noch offen:** auf den Server hochladen — `docucontrol.html`, `style.css`, `docucontrol-3d.js`, kompletter `vendor/`-Ordner (muss als Unterordner neben `docucontrol.html` liegen).
- **Stolperstein beim lokalen Testen (2026-07-08):** Nutzer öffnete die Seite zunächst wiederholt direkt als `file:///C:/...` statt über den lokalen Server — dabei blockiert Chrome das Nachladen von `docucontrol-3d.js` als ES-Modul per CORS (Same-Origin-Regel gilt auch für `file://`), Ergebnis: komplett weißer Bildschirm ohne 3D-Modell, auch in einem frischen Inkognito-Fenster (vermutlich Adressleisten-Autovervollständigung auf eine alte `file://`-URL). Zuverlässiger Fix: lokalen Server mit Auto-Öffnen-Flag starten (`http-server -p <port> -o "docucontrol.html"`), das öffnet den Standardbrowser garantiert mit der korrekten `http://127.0.0.1:<port>/...`-Adresse. **Git-Bash-Falle dabei:** `-o /docucontrol.html` (mit führendem Slash) wird von MSYS/Git-Bash automatisch in einen Windows-Pfad umgewandelt (z. B. `/C:/Program Files/Git/docucontrol.html` → 404) — Fix: `MSYS_NO_PATHCONV=1` voranstellen oder Pfad ohne führenden Slash übergeben.
- **2026-07-09:** DocuControl hat sich weiterentwickelt — dokumentiert inzwischen nicht mehr nur Autoklaven-/Sterilisationsvorgänge, sondern auch die Abfüllung von Sauerstoffflaschen (Druckgasflaschen). Umsetzung (Plan: `plans/2026-07-09-docucontrol-individuelle-loesungen-druckflaschen.md`, im Verlauf korrigiert — siehe unten):
  - Label "Eigenentwicklung" überall zu **„Individuelle Lösungen"** (DE) / „Custom Solutions" (EN) umbenannt — `index.html` Hero-Tag, `docucontrol.html` Produktseiten-Label
  - `doc-intro` (docucontrol.html) und die Kachel-Beschreibung „Dokumentation" (`anl6-p` auf index.html) erweitert: erwähnen jetzt Sterilisation **und** Druckgasflaschen-Abfüllung
  - **Korrektur nach Nutzer-Feedback:** Ursprünglich als eigener, siebter Einsatzbereich mit eigener Unterseite umgesetzt (`einsatzbereich-druckflaschen.html` + `druckflaschen.svg` + Kachel „07") — das war nicht gewünscht. Stattdessen auf `docucontrol.html` selbst ein neuer Abschnitt **„Dokumentierte Prozesse"** ergänzt (zwischen Einleitung und den 4 Vorteile-Kacheln), mit zwei Punkten: Sterilisation im Dampf-Autoklav / Abfüllung von Sauerstoff- und Druckgasflaschen. Die separate Unterseite, ihre SVG-Illustration, die Startseiten-Kachel „07" und der Sitemap-Eintrag wurden wieder entfernt.
  - Lokal mit `http-server` + Playwright getestet (Scroll-Simulation, DE/EN, Konsolenfehler-Check) — keine Fehler, alle Inhalte korrekt. **Noch nicht live hochgeladen.**
  - **Zusätzlich (2026-07-09):** Layout des 3D-Modells an das Muster der darunterliegenden Praxis-Punkte (Live-Monitor, Chargenübersicht, Dateiverwaltung) angeglichen — jetzt zweispaltig wie die anderen `.einsatz-screen`-Blöcke, aber gespiegelt (`.docu-3d-row`, `direction:rtl`): 3D-Modell rechts, neuer Beschreibungstext „Das Gerät" links daneben. Canvas-Höhe an die schmalere Spalte angepasst (`clamp(240px, 24vw, 380px)` statt vorher `clamp(280px, 42vw, 460px)`), Mobile-Fallback (einspaltig) ergänzt. Lokal per Playwright auf Desktop- und Mobile-Viewport verifiziert (kein horizontaler Overflow, `scrollWidth === innerWidth`).

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
  - **NEU (2026-07-07):** `docucontrol-3d.js` + `vendor/three/` (lokal gehostetes Three.js + OrbitControls) — schematisches, drehbares 3D-Modell auf `docucontrol.html` (Logo vorne + hinten)
  - **NEU (2026-07-09):** Abschnitt „Dokumentierte Prozesse" auf `docucontrol.html` — beschreibt beide von DocuControl abgedeckten Prozesstypen (Sterilisation Autoklav, Abfüllung Sauerstoffflaschen)
- **NEU (2026-07-08):** `sitemap.xml`, `robots.txt` (sperrt `/intern/` aus der Indexierung aus), `google5b934c778739615e.html` (Search-Console-Verifizierung) — alle drei auf Root-Ebene neben `index.html`
- **NEU (2026-07-08):** `index.html` Footer — GeTMatic-Schriftzug ist jetzt unsichtbarer Link zu `intern/login.php`

## Interner Mitarbeiterbereich (`intern/`)

**NEU (2026-07-08):** Erste serverseitige Funktion der Website — bisher war alles statisches HTML/CSS/JS. Login-geschützter Bereich für mehrere individuelle Mitarbeiterkonten zum Hoch-/Runterladen/Löschen von Dateien bis 20&nbsp;GB pro Datei (Chunked Upload). Nicht öffentlich beworben, kein Nav-Eintrag, nur per direktem Link erreichbar. Plan: `plans/2026-07-08-interner-mitarbeiterbereich-login-dateien.md`.

- **Ordner:** `reference/Getmatic_website/intern/` — `login.php`, `index.php` (Dashboard), `upload_chunk.php`/`upload_finalize.php` (Chunked Upload), `download.php` (mit HTTP-Range/Resume), `delete.php`, `logout.php`, `includes/` (auth.php, db.php, functions.php), `assets/` (portal.css, chunked-upload.js), `sql/` (schema.sql, add_user.php.example)
- **Tech:** PHP-Sessions (bcrypt-Passwörter, Rate-Limiting nach 5 Fehlversuchen/15 Min.), MySQL (Tabellen `intern_users`, `intern_files`, `intern_login_attempts`, `intern_upload_sessions`), Chunked Upload (8-MB-Häppchen) für Dateien bis 20 GB, Whitelist erlaubter Dateiendungen + echter MIME-Check, `files/` und `tmp_uploads/` per `.htaccess` komplett gegen Direktzugriff gesperrt (Downloads laufen ausschließlich über `download.php`).
- **Gemeinsamer Dateipool** — alle eingeloggten Mitarbeiter sehen alle Dateien und dürfen auch alle löschen (bewusste Vereinfachung für kleinen, vertrauten Kreis).
- **Wichtig — abweichender Deployment-Workflow:** `config.php` (echte DB-Zugangsdaten), die Datenbank selbst, hochgeladene Dateien in `files/`/`tmp_uploads/` sind **nie** im Repo/lokalen Ordner mit echten Werten und werden **nicht** im normalen Re-Upload-Workflow mit hochgeladen (`.gitignore` schützt davor). Reihenfolge beim Deployment:
  1. `intern/sql/schema.sql` einmalig im 1blu-Datenbank-Tool (phpMyAdmin) ausführen
  2. `intern/`-Ordner komplett per FTP hochladen
  3. Auf dem Server (oder lokal, nicht committen): `config.example.php` nach `config.php` kopieren, echte DB-Zugangsdaten + einen eigenen `ADD_USER_SETUP_KEY`-Zufallswert eintragen
  4. `sql/add_user.php.example` nach `add_user.php` kopieren, im Browser mit `?key=<ADD_USER_SETUP_KEY>` aufrufen, erstes Konto **„TG"** anlegen (Passwort wurde dem Assistenten mündlich in der Session mitgeteilt, bewusst nirgends in einer Repo-Datei dokumentiert)
  5. `add_user.php` danach wieder vom Server löschen
  6. Login unter `https://www.getmatic.de/intern/login.php` testen
- **Status: live und bestätigt funktionsfähig (2026-07-08).** Deployment gemeinsam mit dem User durchgeführt (kein direkter Serverzugriff für den Assistenten — alle Uploads/DB-Aktionen musste der User selbst ausführen, Anleitung + Fehlersuche per Chat). Account „TG" angelegt, Login, Dashboard und Chunked-Upload live getestet und bestätigt.
- **1blu-DB-Zugangsdaten für dieses Paket** (falls für andere Anwendungen auf demselben Hosting relevant): Host ist **nicht** `localhost`, sondern `mysql38.1blu.de`; Datenbank-Benutzername unterscheidet sich vom Datenbanknamen (Format `s148263_...` vs. `db148263x...`) — im 1blu-Kundencenter unter der jeweiligen Datenbank („Verwaltung"-Ansicht) nachzuschauen, nicht raten.
- **Beim Live-Deployment gefundene und behobene Bugs** (gute Referenz für künftige PHP-Bereiche auf dieser Website):
  1. `enforce_https()` als PHP-Redirect (`header('Location: ...')`) verursachte auf 1blu einen fehlerhaften Response (`ERR_HTTP_RESPONSE_CODE_FAILURE`) — vermutlich Proxy-/Header-Konflikt. Fix: HTTPS-Erzwingung in `intern/.htaccess` per `mod_rewrite` verlagert (zuverlässiger), PHP-Funktion macht jetzt nur noch ein Log statt Redirect.
  2. `sql/add_user.php.example` hatte `../`-Pfade, die nur stimmten, solange die Datei in `sql/` bleibt — beim bestimmungsgemäßen Verschieben nach `intern/add_user.php` liefen die `require_once`-Pfade ins Leere (500-Fehler). Fix: Pfade auf den finalen Speicherort `intern/` umgestellt.
  3. `finfo_close()` ist seit **PHP 8.5** deprecated (finfo-Objekte werden automatisch freigegeben) — die Deprecation-Warnung verunreinigte die JSON-Antwort von `upload_finalize.php` und ließ den Chunked-Upload im Frontend fehlschlagen, obwohl die Datei serverseitig korrekt gespeichert wurde. Fix: Aufruf entfernt.
  4. **Bekanntes 1blu/FileZilla-Problem erneut aufgetreten:** `includes/functions.php` wurde beim Hochladen im Resume-Modus doppelt aneinandergehängt (`Parse error: unexpected token "<"` an der Stelle, wo die Datei eigentlich endet) — exakt dasselbe Muster wie beim Einsatzbereiche-Upload am 2026-06-22. Immer **Überschreiben statt Fortsetzen/Resume** beim FTP-Upload verwenden.
  5. `logout.php` hatte kein `exit;` nach dem Redirect-Header und keinen Fallback — robuster gemacht (`headers_sent()`-Check + HTML-Fallback mit sichtbarem Link).
  6. Login-Logo (`getmatic_logo_transparent.png`) zeigte im weißen Login-Feld nur „GeT" statt dem vollen Schriftzug — Ursache: das Bild ist für dunkle Hintergründe gebaut (heller Teil „Matic" ist weiß, unsichtbar auf Weiß, nur im echten Seiten-Header mit dunklem Hintergrund vollständig lesbar). Fix: Logo in `login.php` jetzt in einer anklickbaren Schaltfläche mit dunklem Hintergrund (`--gray-header`, wie der echte Header) + grauem Rahmen, führt zurück zu `index.html`. Neue CSS-Klasse `.portal-logo-link` in `portal.css`. **Bestätigt funktionsfähig nach Upload.**
- Debug-Hilfsmittel (`ini_set('display_errors', '1')`) wurden nach der Fehlersuche aus allen Dateien wieder entfernt.
- `add_user.php` wurde nach der Konto-Anlage vom Server gelöscht (vom User bestätigt). **Achtung `.gitignore`:** Regel zeigte zunächst noch auf den alten Pfad `sql/add_user.php` statt auf den tatsächlichen Ziel-Pfad `intern/add_user.php` — beim Shutdown korrigiert (sonst wäre das ausgefüllte Skript fast committet worden).
- **NEU (2026-07-08):** Zugang zum Login jetzt auch von der Startseite aus — der „GeTMatic"-Schriftzug im Footer (`index.html`, Klasse `.footer-logo`) ist zum unsichtbaren Link auf `intern/login.php` geworden (optisch komplett unverändert, kein Text/Hinweis, nur per Klick auffindbar). CSS-Ergänzung: `text-decoration: none` in `.footer-logo`. **Noch zu bestätigen ob live:** `index.html`, `style.css` hochgeladen?

---

## SEO / Google-Sichtbarkeit (2026-07-08)

- **`glander-led.de` weiterhin ungeklärt, ob der 301-Redirect (`.htaccess`) hochgeladen wurde** — 1blu-Domainübersicht bestätigt: `www.getmatic.de` UND `www.glander-led.de` zeigen beide direkt auf `/www/getmatic` (kein Alias-Mechanismus, echte Doppel-Domain auf denselben Webspace). Der vorbereitete Redirect in `reference/Getmatic_website/.htaccess` ist die richtige Lösung, sofern hochgeladen — Root-`.htaccess`-Inhalt beim Live-Check am 2026-07-08 korrekt bestätigt. **Sauberere Alternative weiterhin offen:** im 1blu-Kundencenter prüfen, ob `glander-led.de` auf „Weiterleitung" statt „Verzeichnis" umgestellt werden kann.
- **Google Search Console neu eingerichtet** (Property `https://www.getmatic.de`, Verifizierung per HTML-Datei `google5b934c778739615e.html` — liegt jetzt auch lokal in `reference/Getmatic_website/`, vermutlich durch denselben Hintergrund-Sync-Mechanismus wie unten beschrieben).
- **`sitemap.xml`** (alle 8 Seiten, inkl. `docucontrol.html`) und **`robots.txt`** (erlaubt alles außer `/intern/`, verlinkt auf die Sitemap) neu angelegt in `reference/Getmatic_website/`. Bei Google eingereicht.
- **`docucontrol.html`-Indexierung über „Indexierung beantragen" zweimal mit „404" abgelehnt**, obwohl Live-Check per `curl` (sowohl mit Googlebot- als auch normalem User-Agent) zuverlässig `200 OK` liefert, `robots.txt`/`sitemap.xml` ebenfalls korrekt erreichbar, kein IPv6-Eintrag (also keine IPv6-Fehlkonfiguration als Ursache). Ursache nicht abschließend geklärt — vermutlich ein gecachtes/veraltetes Ergebnis in Googles Live-Test-Tool. **Empfehlung an User:** einfach etwas Zeit verstreichen lassen (Stunden bis Tage) und über den „Seitenindexierung"-Bericht in Search Console verfolgen; die eingereichte Sitemap sollte die Seite ohnehin im normalen Crawling-Rhythmus erfassen, auch ohne manuellen Button.

---

## ⚠️ Beobachtung: Ungeklärte Datei-Synchronisation (2026-07-08)

Beim Shutdown-Check festgestellt: `Backup_Website/Getmatic_website/` (ohne `_DE`/`_DE_EN`-Suffix, in `CLAUDE.md`/dieser Datei bisher nicht dokumentiert) enthielt nach Session-Ende **identische** Kopien von `docucontrol.html`/`style.css` wie `reference/Getmatic_website/` — inklusive aller Änderungen aus dieser Session, obwohl zu keinem Zeitpunkt gezielt dorthin geschrieben wurde. Zeitstempel passen exakt zu den Bearbeitungszeiten in `reference/Getmatic_website/`. Das deutet auf einen aktiven Hintergrund-Sync-/Spiegelungsprozess auf diesem Rechner hin (evtl. Dropbox oder ähnliches — im Browser des Users war ein Dropbox-Lesezeichen sichtbar), der `reference/Getmatic_website/` in `Backup_Website/Getmatic_website/` spiegelt. Vermutlich auch verantwortlich für andere in dieser Session beobachtete Anomalien (z. B. `config.php` sprang zwischenzeitlich unerklärlich auf den Platzhalter-Inhalt zurück, `add_user.php.example` wurde zwischenzeitlich zu `add_user.php` umbenannt vorgefunden).

**Nicht automatisch bereinigt** (unklare Absicht, siehe Sicherheitsrichtlinie „unfamiliar state investigieren statt löschen") — `Backup_Website/Getmatic_website/`-Änderungen wurden beim Shutdown-Commit bewusst **nicht mit committet**. Empfehlung an User: bei Gelegenheit prüfen, was genau diesen Ordner synchronisiert, und ob das gewünscht ist — falls nicht, könnte er versehentlich einen vermeintlichen „alten Stand" überschreiben.

**⚠️ Eskaliert (2026-07-09) — echtes Sicherheitsrisiko gefunden:** `Backup_Website/Getmatic_website/` (ohne Suffix) ist jetzt komplett von der Platte verschwunden (`git status` zeigt alle Dateien darin als gelöscht). Stattdessen sind zwei neue, bisher undokumentierte Ordner aufgetaucht: `Backup_Website/Getmatic_website_mit_Login/` und `Backup_Website/Getmatic_website_ohne Login/` (Name mit Leerzeichen). Der „mit_Login"-Ordner enthält eine **vollständige Kopie von `reference/Getmatic_website/intern/`, inklusive `intern/config.php` mit echten, unverschlüsselten DB-Zugangsdaten und dem `ADD_USER_SETUP_KEY`** — diese Kopie liegt **außerhalb** des durch `.gitignore` geschützten Pfads (`.gitignore` schützt nur `reference/Getmatic_website/intern/config.php`, nicht den Spiegel-Pfad unter `Backup_Website/`). Zusätzlich lag dort auch wieder ein ausgefülltes `intern/sql/add_user.php` (sollte nach Konto-Anlage gelöscht bleiben). Diese Dateien wurden **nicht committet** (Backup_Website/ komplett von Staging ausgeschlossen) und als Sofortmaßnahme wurde `.gitignore` um eine pfadunabhängige Regel für `**/config.php`-Dateien unter `intern/` ergänzt, damit ein versehentliches Commit dieser Kopie in Zukunft technisch ausgeschlossen ist. **Empfehlung an User (dringend):** 1) Prüfen, welches Tool diese Ordner erzeugt (Dropbox-Konfliktkopien? Ein Sync-Client, der bei Konflikten umbenennt statt überschreibt?). 2) Da die DB-Zugangsdaten jetzt an einer zweiten, unkontrollierten Stelle auf der Festplatte liegen: erwägen, das DB-Passwort bei 1blu zu ändern, falls dieser Ordner z. B. auch mit einem Cloud-Dienst synchronisiert wird. 3) Die verwaisten `Backup_Website/*`-Ordner grundsätzlich manuell aufräumen/klären, sobald die Ursache bekannt ist.

---

## Backup-Ordner

`Backup_Website/` enthält mehrere ältere Versionen:
- `Getmatic_website_DE` — reine DE-Version
- `Getmatic_website_DE_EN` — Zwischenstand mit DE/EN
- `Getmatic_website_alt`, `Getmatic_website_aalt` — weitere ältere Zwischenstände (Herkunft/Zweck nicht dokumentiert, unangetastet gelassen)
- `Getmatic_website` (ohne Suffix) — seit 2026-07-09 komplett von der Platte verschwunden, siehe Warnhinweis oben
- `Getmatic_website_mit_Login`, `Getmatic_website_ohne Login` — **NEU (2026-07-09)**, offenbar Nachfolger des verschwundenen `Getmatic_website`-Ordners, **enthält ungeschützte echte Zugangsdaten** — siehe eskalierten Warnhinweis oben

---

## Offene Aufgaben / nächste Schritte

- [x] Re-Upload nach Hero-Slider-Umbau — erledigt und live bestätigt (2026-06-22)
- [x] Re-Upload nach Einsatzbereiche-Unterseiten — erledigt und live bestätigt (2026-06-22), inkl. Behebung der 8-fachen Datei-Duplizierung bei 4 Unterseiten
- [x] Re-Upload nach DocuControl-Screenshot-Sektion (2026-06-25) — `docucontrol.html`, `style.css`, 3 PNGs — live bestätigt (2026-06-26)
- [ ] Re-Upload für 3D-Modell (2026-07-07/08, überarbeitet 2026-07-08) — `docucontrol.html`, `style.css`, `docucontrol-3d.js`, kompletter `vendor/`-Ordner. Lokal im Testserver geprüft, vom Nutzer im echten Browser freigegeben (inkl. Halbkugel-Bugfix). **Noch nicht live hochgeladen.** Die 4 Hardware-Foto-PNGs (`docucontrol-hardware-*.png`) sind **hinfällig** — Sektion wurde noch am selben Tag durch das 3D-Modell ersetzt, brauchen keinen Upload.
- [x] Interner Mitarbeiterbereich (2026-07-08) — deployt, TG-Account angelegt, Login/Dashboard/Upload live bestätigt, `add_user.php` gelöscht
- [x] `.htaccess` (301-Redirect glander-led.de → getmatic.de) hochgeladen — Inhalt beim Live-Check am 2026-07-08 auf dem Server bestätigt korrekt
- [ ] Im 1blu-Kundencenter prüfen: `glander-led.de` zeigt laut Domain-Übersicht direkt auf `/www/getmatic` (kein Alias, echte Doppel-Domain) — eine saubere Domain-Weiterleitung wäre robuster als der `.htaccess`-Redirect, aber nicht zwingend, falls Redirect zuverlässig greift
- [ ] `sitemap.xml` + `robots.txt` hochladen nach `www/getmatic/` (falls noch nicht geschehen) — beide neu am 2026-07-08 angelegt
- [ ] `index.html` + `style.css` hochladen für den neuen unsichtbaren Footer-Login-Link (2026-07-08) — Upload-Status nicht explizit bestätigt
- [ ] Google Search Console: „Seitenindexierung"-Bericht in den nächsten Tagen prüfen, ob `docucontrol.html` von selbst indexiert wird (manuelle „Indexierung beantragen" lief zweimal auf 404, obwohl Seite serverseitig nachweislich erreichbar ist — vermutlich Google-seitiges Cache-Problem, siehe Abschnitt „SEO / Google-Sichtbarkeit" oben)
- [ ] Ursache der Hintergrund-Datei-Synchronisation (`Backup_Website/Getmatic_website/`) klären — siehe Warnhinweis oben
- [ ] **`getmatic-website`-Skill erstellen** — Plan liegt vor: `plans/2026-06-02-website-creator-skills-aufbauen.md`
- [ ] Fehlende Barlow-Font-Gewichte beschaffen (Condensed 400/600/700, Regular 300/500) für sauberes Rendering
- [ ] Security Headers auf Server konfigurieren (`X-Frame-Options`, `Content-Security-Policy`)
- [ ] Re-Upload für "Individuelle Lösungen"-Umbenennung + neuen Abschnitt „Dokumentierte Prozesse" auf docucontrol.html (2026-07-09) — betroffene Dateien: `index.html`, `docucontrol.html`. Lokal getestet (Playwright, DE/EN, keine Konsolenfehler), **noch nicht live hochgeladen**.

---

## Deployment-Hinweise

- 1blu Online-Manager: SVG-Dateien **einzeln** hochladen (Bulk-Upload überspringt Dateien stillschweigend)
- Für mehrere Dateien: **FileZilla** verwenden
- Linux-Server: Groß-/Kleinschreibung beachten (`Systeme.svg` → großes S)

---

_Wird laufend aktualisiert — veraltete Einträge entfernen._
