# Plan: Interner Mitarbeiterbereich mit Login und Datei-Up-/Download

**Erstellt:** 2026-07-08
**Status:** Implementiert
**Anforderung:** Ein Login-geschützter interner Bereich (`intern/`) auf der getmatic-Website, in dem mehrere individuelle Mitarbeiterkonten Dateien — auch sehr große, bis 20 GB pro Datei — hoch- und wieder runterladen können, inklusive Löschfunktion. Nicht öffentlich beworben, nur per direktem Link erreichbar.

---

## Überblick

### Was dieser Plan erreicht

Ein neuer, passwortgeschützter Bereich `intern/` auf der Website, erreichbar nur per direktem Link (kein Eintrag in der Hauptnavigation). Mehrere Mitarbeiter loggen sich mit individuellem Benutzernamen/Passwort über ein Login-Fenster ein und landen auf einer gemeinsamen Dateiübersicht: hochladen (auch sehr große Dateien bis 20 GB, per Chunked Upload mit Fortschrittsanzeige), herunterladen, löschen.

### Warum das wichtig ist

Dies ist die **erste serverseitige Funktion** der bisher rein statischen Website (HTML/CSS/JS, siehe `context/current-data.md`) und damit ein Technologiesprung, kein normales Content-Update. Weil getmatic auch Pharma-/Medizin-Kunden bedient (siehe `context/business-info.md`), muss dieser Bereich von Anfang an mit echter, seriöser Absicherung gebaut werden. Die 20-GB-Anforderung (z. B. für große Projektdateien, Backups, CAD-Exporte) bedeutet zusätzlich, dass ein normales Formular-Upload technisch nicht reicht — es braucht ein Chunked-Upload-Verfahren, das Shared-Hosting-Limits pro Request umgeht.

---

## Aktueller Zustand

### Relevante bestehende Struktur

- `reference/Getmatic_website/` — komplette Website als statische Dateien, kein Backend, kein PHP bisher im Einsatz.
- `reference/Getmatic_website/.htaccess` — existiert bereits (301-Redirect `glander-led.de` → `getmatic.de`, siehe `context/current-data.md`, Stand 2026-07-02, **noch nicht hochgeladen**). Dieser Plan muss mit dieser Datei zusammen funktionieren, nicht sie ersetzen.
- `reference/Getmatic_website/impressum.html` — Muster für eigenständige Unterseiten (eigener Header/Footer), dient als visuelle Vorlage (Farben/Typografie) für die Login-Seite — der interne Bereich ist aber bewusst **nicht** ins DE/EN-i18n-System eingebunden (interne Mitarbeiterfunktion, kein Publikumscontent).
- `style.css` — CSS-Variablen (`--teal`, `--teal-dark`, `--bg-card`, `--radius`, `--shadow`, `--space-*`, `--text-dark`, `--text-light`) und bestehende Button-/Card-Patterns (`.einsatz-cta`, `.einsatz-vorteil`) als Basis für neue Formular-/Dashboard-Styles.
- `.gitignore` — aktuell sehr minimal (`.DS_Store`, `__pycache__/`, `*.pyc`, `*.log`, `*.tmp`, `*.bak`, `.claude/settings.local.json`). Muss um Geheimnisse/Laufzeitdaten des internen Bereichs ergänzt werden.
- Hosting: 1blu, Pfad `www/getmatic/`, Deployment per FTP (FileZilla empfohlen für mehrere Dateien, siehe `context/current-data.md`). PHP ist verfügbar, **MySQL-Datenbank ist laut User vorhanden** (offene Frage aus dem ersten Entwurf damit geklärt).
- Keine Datenbank bisher im Einsatz, kein bestehendes PHP-Pattern im Repo.

### Lücken oder Probleme, die adressiert werden

- Es gibt keinerlei serverseitige Logik, keine Authentifizierung, keine Datei-Upload-Möglichkeit auf der Website.
- Es existiert noch kein Muster im Projekt für: PHP-Konfiguration, Datenbank-Zugangsdaten-Handling, Session-Sicherheit, Datei-Validierung — dieser Plan legt das Fundament dafür.
- Sicherheitsrelevant auf Shared Hosting: unvalidierte Uploads sind ein klassisches Einfallstor (z. B. Hochladen einer `.php`-Datei, die dann ausführbar im Webspace liegt). Muss von Anfang an sauber verhindert werden.
- **Große Dateien (bis 20 GB) lassen sich nicht per normalem `<form>`-Upload übertragen** — Shared-Hosting-PHP-Konfigurationen begrenzen `upload_max_filesize`/`post_max_size`/`max_execution_time` typischerweise auf wenige hundert MB bzw. wenige Minuten pro Request. Das braucht Chunked Upload (Datei wird im Browser in kleine Teile zerlegt, jeder Teil ist ein eigener, kurzer Request).

---

## Vorgeschlagene Änderungen

### Zusammenfassung der Änderungen

- Neuer Unterordner `intern/` mit eigenem PHP-Login (Sessions, gehashte Passwörter, mehrere individuelle Konten aus einer Datenbank-Tabelle).
- Login-Seite (`intern/login.php`) mit Login-Fenster (Formular oben/mittig auf der Seite, im getmatic-Design).
- Dashboard (`intern/index.php`, nur mit gültiger Session erreichbar): Datei-Upload (mit Fortschrittsbalken, Chunked Upload), gemeinsame Dateiliste aller Mitarbeiter mit Download- und Löschen-Links.
- Chunked-Upload-Mechanismus: Client zerlegt die Datei in ~8-MB-Teile (`chunked-upload.js`), lädt sie sequenziell über `upload_chunk.php` hoch, `upload_finalize.php` setzt die Datei zusammen, validiert sie vollständig und verschiebt sie in den geschützten Ablageordner.
- Datei-Download nur über ein geprüftes PHP-Skript (`download.php`), niemals direkter Zugriff auf die gespeicherten Dateien; mit HTTP-Range-Unterstützung, damit auch sehr große Downloads bei Verbindungsabbruch fortgesetzt werden können.
- Löschfunktion (`delete.php`) — jeder eingeloggte Mitarbeiter darf jede Datei im gemeinsamen Pool löschen (siehe Design-Entscheidung 5).
- Upload-Validierung: Whitelist erlaubter Dateiendungen, echte MIME-Typ-Prüfung, zufällig generierte Speichernamen, Prüfung auf freien Speicherplatz vor dem Abspeichern.
- `.htaccess` in den Ablage-/Temp-Ordnern blockiert jeden direkten HTTP-Zugriff und jede PHP-Ausführung dort.
- Neue MySQL-Tabellen `intern_users`, `intern_files`, `intern_login_attempts`, `intern_upload_sessions` (Namenspräfix `intern_`, um Kollisionen zu vermeiden).
- Login-Rate-Limiting gegen Brute-Force (Sperre nach mehreren Fehlversuchen).
- `.gitignore`-Erweiterung, damit echte Zugangsdaten und hochgeladene/temporäre Dateien nie ins Git-Repo gelangen.
- `context/current-data.md` um den neuen Bereich, verbleibende offene Punkte und Deployment-Hinweise ergänzt.

### Neue Dateien erstellen

| Dateipfad | Zweck |
|---|---|
| `reference/Getmatic_website/intern/login.php` | Login-Seite mit Formular (Benutzername/Passwort), verarbeitet POST, startet Session |
| `reference/Getmatic_website/intern/logout.php` | Zerstört Session, leitet zu `login.php` um |
| `reference/Getmatic_website/intern/index.php` | Dashboard: Upload-Formular (mit Chunked-Upload-JS) + gemeinsame Dateiliste mit Download-/Löschen-Links |
| `reference/Getmatic_website/intern/upload_chunk.php` | Nimmt einen einzelnen Datei-Chunk entgegen, hängt ihn an die Assembly-Datei der laufenden Upload-Session an |
| `reference/Getmatic_website/intern/upload_finalize.php` | Wird nach dem letzten Chunk aufgerufen: prüft Gesamtgröße, validiert die fertige Datei (Whitelist/MIME), verschiebt sie in `files/`, legt DB-Eintrag an, räumt Temp-Daten auf |
| `reference/Getmatic_website/intern/download.php` | Prüft Session, liefert eine Datei per `id`-Parameter kontrolliert aus, mit HTTP-Range-Unterstützung für Resume bei Abbruch |
| `reference/Getmatic_website/intern/delete.php` | Prüft Session + CSRF-Token, löscht Datei aus `files/` und den zugehörigen DB-Eintrag |
| `reference/Getmatic_website/intern/includes/auth.php` | Session-Start mit sicheren Cookie-Einstellungen, `require_login()`, Login-/Logout-Hilfsfunktionen, Rate-Limiting-Check, CSRF-Hilfsfunktionen |
| `reference/Getmatic_website/intern/includes/db.php` | PDO-MySQL-Verbindung, liest Zugangsdaten aus `config.php` |
| `reference/Getmatic_website/intern/includes/functions.php` | Datei-Validierung (Whitelist, MIME-Check), sichere Speichernamen-Generierung, freien Speicherplatz prüfen, Hilfsfunktion für lesbare Dateigrößen |
| `reference/Getmatic_website/intern/config.example.php` | Platzhalter-Konfiguration (DB-Host/Name/User/Passwort als Dummys) — **wird committet**, dient als Vorlage |
| `reference/Getmatic_website/intern/.htaccess` | `Options -Indexes`, Schutz von `includes/` und `config.php` vor direktem Browser-Zugriff |
| `reference/Getmatic_website/intern/files/.htaccess` | `Require all denied` + `php_flag engine off` — blockiert jeden Direktzugriff und jede Skriptausführung im Ablageordner |
| `reference/Getmatic_website/intern/tmp_uploads/.htaccess` | Gleicher Schutz wie `files/.htaccess`, für die noch nicht fertiggestellten Chunk-Uploads |
| `reference/Getmatic_website/intern/assets/portal.css` | Eigenes Stylesheet für Login-Formular + Dashboard + Fortschrittsbalken (nutzt bestehende CSS-Variablen aus `style.css`) |
| `reference/Getmatic_website/intern/assets/chunked-upload.js` | Vanilla-JS: zerlegt die gewählte Datei in Chunks, lädt sie sequenziell hoch, zeigt Fortschritt, ruft `upload_finalize.php` auf, wiederholt fehlgeschlagene Chunks automatisch (Retry) |
| `reference/Getmatic_website/intern/sql/schema.sql` | SQL-Skript zum einmaligen Anlegen der vier Tabellen (im 1blu-Datenbank-Tool auszuführen) |
| `reference/Getmatic_website/intern/sql/add_user.php.example` | Vorlage für ein Hilfsskript, um neue Mitarbeiterkonten mit gehashtem Passwort anzulegen |

### Zu ändernde Dateien

| Dateipfad | Änderungen |
|---|---|
| `.gitignore` | Ergänzen: `reference/Getmatic_website/intern/config.php`, `reference/Getmatic_website/intern/files/*` (außer `.htaccess`), `reference/Getmatic_website/intern/tmp_uploads/*` (außer `.htaccess`), `reference/Getmatic_website/intern/sql/add_user.php` (ausgefüllte Version) |
| `context/current-data.md` | Neuer Abschnitt „Interner Mitarbeiterbereich (`intern/`)" mit Struktur, verbleibenden offenen Punkten, abweichendem Deployment-Workflow |

### Zu löschende Dateien

Keine.

---

## Design-Entscheidungen

### Getroffene Schlüsselentscheidungen

1. **PHP-Session-Login statt HTTP-Basic-Auth**: Gewünscht ist ein gestaltetes „Login-Fenster", kein nativer Browser-Dialog. `.htaccess` wird trotzdem eingesetzt, aber nur um `files/` und `tmp_uploads/` komplett vor direktem HTTP-Zugriff zu sperren.
2. **MySQL für Benutzerkonten und Dateiregister**: Bestätigt vorhanden. Eine relationale Struktur macht „gemeinsamer Pool, wer hat was hochgeladen" und die Verwaltung mehrerer Konten sauber.
3. **Chunked Upload für große Dateien (bis 20 GB)**: Einzelne Chunks von ~8 MB werden nacheinander per `fetch()` an `upload_chunk.php` gesendet und dort an eine pro Upload-Session eindeutige Assembly-Datei in `tmp_uploads/` angehängt. Das umgeht `upload_max_filesize`/`post_max_size`/`max_execution_time`-Limits, die für einzelne Requests gelten, ohne dass an der 1blu-PHP-Konfiguration etwas geändert werden muss. Nach dem letzten Chunk prüft `upload_finalize.php` die Gesamtgröße, validiert die komplette Datei und verschiebt sie erst dann nach `files/`.
4. **Downloads mit HTTP-Range-Unterstützung**: Bei Dateien im GB-Bereich ist ein Verbindungsabbruch ohne Fortsetzungsmöglichkeit sehr ärgerlich. `download.php` wertet `Range`-Header aus und liefert bei Bedarf `206 Partial Content`, sodass Browser (und Download-Manager) unterbrochene große Downloads fortsetzen können, statt von vorn zu beginnen.
5. **Löschrecht: jeder eingeloggte Mitarbeiter darf jede Datei löschen** — Annahme, weil kein Rollen-/Admin-Konzept angefragt wurde und es sich um einen kleinen, vertrauten internen Kreis handelt (passend zu „gemeinsamer Pool"). **Wenn das nicht gewünscht ist** (z. B. nur eigene Dateien löschbar, oder nur ein Admin-Konto darf löschen), bitte vor der Umsetzung Bescheid geben — kleine Änderung in `delete.php`.
6. **Speicherplatz-Prüfung vor jedem finalisierten Upload**: `upload_finalize.php` prüft mit `disk_free_space()`, ob nach dem Verschieben der Datei noch ausreichend Puffer auf dem Webspace bleibt, und bricht kontrolliert mit Fehlermeldung ab statt den Server vollzumüllen. Ersetzt keine echte Kontingent-Prüfung (siehe Offene Fragen).
7. **Hochgeladene Dateien werden nie direkt per URL ausgeliefert**: Alle Downloads laufen über `download.php` (Session-Prüfung + Range-Support). `files/` ist zusätzlich per `.htaccess` (`Require all denied`) komplett gesperrt.
8. **Zufällige Speichernamen statt Original-Dateinamen im Dateisystem**: Verhindert Path-Traversal- und Überschreibungs-Angriffe. Der Original-Dateiname wird nur in der Datenbank gespeichert und beim Download über `Content-Disposition` wiederhergestellt.
9. **Whitelist statt Blacklist für erlaubte Dateiendungen**: Nur explizit erlaubte, unkritische Endungen (Vorschlag: `pdf, doc, docx, xls, xlsx, ppt, pptx, jpg, jpeg, png, gif, zip, csv, txt, dwg, step, stp, mp4`; bei Bedarf beim Implementieren erweitern). `.php`, `.phtml`, `.html`, `.js`, `.exe`, `.sh` etc. werden immer abgelehnt, auch zusätzlich per `.htaccess` in `files/` geblockt (mehrschichtige Verteidigung).
10. **Passwörter ausschließlich mit `password_hash()`/`password_verify()` (bcrypt)**: kein Eigenbau-Hashing, kein Klartext-Passwort in Datenbank, Log oder Repo.
11. **`config.php` mit echten Zugangsdaten wird nie committet**: Nur `config.example.php` mit Platzhaltern liegt im Repo. Die echte `config.php` wird direkt auf dem Server angelegt.
12. **Das erste Mitarbeiterkonto (Benutzername „TG") wird bei der Implementierung direkt in der Datenbank angelegt, das zugehörige Passwort steht bewusst nicht in diesem Plan-Dokument** (Plan-Dateien werden versioniert/committet) — es wurde dem Assistenten in der Konversation mitgeteilt und wird beim Ausführen von `/implement` verwendet, ohne es in eine Datei zu schreiben, die ins Repository gelangt.
13. **Kein Eintrag in der Hauptnavigation**: Entspricht der Vorgabe „nicht öffentlich beworben, nur intern/direkt per Link erreichbar" — Bequemlichkeit, keine Sicherheitsmaßnahme, die eigentliche Absicherung ist Login + serverseitige Prüfung.
14. **Login-Rate-Limiting**: Nach 5 Fehlversuchen für denselben Benutzernamen 15 Minuten Sperre.
15. **HTTPS-Pflicht**: `index.php`/`login.php`/`upload_chunk.php`/`download.php` erzwingen HTTPS, Session-Cookie mit `Secure`-Flag.
16. **Kein DE/EN-i18n**: interner Werkzeugbereich, kein Übersetzungsaufwand nötig.

### Betrachtete Alternativen

- **Normales `<form>`-Upload ohne Chunking**: Für die geforderten bis zu 20 GB pro Datei technisch nicht machbar auf Shared Hosting (Request-Limits) — verworfen zugunsten Chunked Upload.
- **Fertige Chunked-Upload-Bibliothek (z. B. Resumable.js/tus)**: `tus` bräuchte einen eigenen Server-Prozess (`tusd`, i. d. R. Go-Binary) — auf 1blu-Shared-Hosting nicht lauffähig. Eine schlanke Eigenlösung (Chunk-Request an ein PHP-Skript) passt besser zum bestehenden „kein Build-Tool, keine Frameworks"-Prinzip der Website und braucht keine zusätzliche Server-Software.
- **HTTP-Basic-Auth (`.htaccess`/`.htpasswd`) für alles**: robust, aber passt nicht zum gewünschten gestalteten Login-Fenster und deckt Upload/Download/Löschen ohnehin nicht ab — nur als Zusatzschutz für `files/`/`tmp_uploads/` übernommen.
- **Fertiger Cloud-Dienst (Nextcloud-Einbindung o. ä.)**: würde Firmendaten bei einem Drittanbieter ablegen und zusätzliche Kosten/Komplexität bedeuten, obwohl das bestehende 1blu-Hosting (PHP + MySQL) technisch ausreicht — verworfen.
- **Downloads ohne Range-Support**: einfacher umzusetzen, aber bei 20-GB-Dateien und einem Verbindungsabbruch müsste der komplette Download neu gestartet werden — für die Praxis nicht akzeptabel, deshalb Range-Support mit in den Plan aufgenommen statt als Folgeschritt zu verschieben.

### Offene Fragen

Alle Fragen sind geklärt:

1. **Freier Speicherplatz**: 80 GB auf dem 1blu-Webspace bestätigt verfügbar. `enough_disk_space()`-Puffer in `upload_finalize.php` bleibt trotzdem als Sicherheitsnetz bestehen.
2. **Chunk-Größe**: bleibt beim Vorschlag von 8 MB pro Chunk (keine Änderung gewünscht).
3. **Löschrecht**: bestätigt — jeder eingeloggte Mitarbeiter darf jede Datei im gemeinsamen Pool löschen (Design-Entscheidung 5 bleibt wie beschrieben).
4. **Aufräumen abgebrochener Uploads**: einfache Lösung aus Schritt 15 (Check beim Dashboard-Aufruf, kein Cronjob) wird umgesetzt.

---

## Schritt-für-Schritt-Aufgaben

### Schritt 1: Ordnerstruktur, `.htaccess`-Schutz und `.gitignore` anlegen

**Aktionen:**
- Ordner `reference/Getmatic_website/intern/`, `intern/includes/`, `intern/files/`, `intern/tmp_uploads/`, `intern/assets/`, `intern/sql/` anlegen
- `intern/.htaccess`: `Options -Indexes`, Block für `config.php`/`includes/*.php` gegen direkten Browser-Aufruf
- `intern/files/.htaccess` und `intern/tmp_uploads/.htaccess`: `Require all denied`, `php_flag engine off`
- `.gitignore` um die vier Zeilen aus „Zu ändernde Dateien" ergänzen
- `intern/config.example.php` mit Platzhalter-Konstanten (`DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS`) erstellen, Kommentar „Kopieren nach config.php und mit echten Werten füllen — config.php niemals committen"

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/.htaccess`
- `reference/Getmatic_website/intern/files/.htaccess`
- `reference/Getmatic_website/intern/tmp_uploads/.htaccess`
- `reference/Getmatic_website/intern/config.example.php`
- `.gitignore`

---

### Schritt 2: Datenbankschema definieren

**Aktionen:**
- `intern/sql/schema.sql` schreiben mit vier Tabellen:
  ```sql
  CREATE TABLE intern_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    display_name VARCHAR(100) NOT NULL,
    is_active TINYINT(1) NOT NULL DEFAULT 1,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
  );

  CREATE TABLE intern_files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    original_filename VARCHAR(255) NOT NULL,
    stored_filename VARCHAR(64) NOT NULL UNIQUE,
    mime_type VARCHAR(100) NOT NULL,
    filesize_bytes BIGINT UNSIGNED NOT NULL,
    uploaded_by INT NOT NULL,
    uploaded_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (uploaded_by) REFERENCES intern_users(id)
  );

  CREATE TABLE intern_login_attempts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    attempted_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    success TINYINT(1) NOT NULL
  );

  CREATE TABLE intern_upload_sessions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    token VARCHAR(64) NOT NULL UNIQUE,
    original_filename VARCHAR(255) NOT NULL,
    total_size_bytes BIGINT UNSIGNED NOT NULL,
    received_bytes BIGINT UNSIGNED NOT NULL DEFAULT 0,
    created_by INT NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    status ENUM('in_progress','completed','failed') NOT NULL DEFAULT 'in_progress',
    FOREIGN KEY (created_by) REFERENCES intern_users(id)
  );
  ```
- `filesize_bytes`/`total_size_bytes`/`received_bytes` bewusst `BIGINT`, da `INT` bei 20 GB (≈ 21,5 Mrd. Bytes) überlaufen würde

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/sql/schema.sql`

---

### Schritt 3: `includes/db.php` — Datenbankverbindung

**Aktionen:**
- PDO-Verbindung mit `DB_HOST`/`DB_NAME`/`DB_USER`/`DB_PASS` aus `config.php`
- `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`, `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`, Charset `utf8mb4`
- Funktion `get_db(): PDO`
- Bei Verbindungsfehler: generische Fehlermeldung, keine echten DB-Zugangsdaten/Fehlermeldungen an den Browser durchreichen

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/includes/db.php`

---

### Schritt 4: `includes/auth.php` — Session, Login-Logik, Rate-Limiting, CSRF

**Aktionen:**
- Sichere Session-Cookie-Parameter **vor** `session_start()`: `httponly=true`, `secure=true`, `samesite=Strict`
- `attempt_login(string $username, string $password): bool` mit Rate-Limiting-Check gegen `intern_login_attempts` (≥5 Fehlversuche in 15 Minuten → sofort `false`), `password_verify()`, Logging jedes Versuchs, `session_regenerate_id(true)` bei Erfolg
- `require_login(): void` — prüft Session + 30-Minuten-Idle-Timeout
- `logout(): void`
- `csrf_token(): string` / `csrf_check(string $token): bool`
- `enforce_https(): void`

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/includes/auth.php`

---

### Schritt 5: `includes/functions.php` — Datei-Validierung, Speicherplatz-Check

**Aktionen:**
- `ALLOWED_EXTENSIONS` (Whitelist, siehe Design-Entscheidung 9), `CHUNK_SIZE_BYTES = 8 * 1024 * 1024`
- `validate_file(string $tmpPath, string $originalFilename): array` — prüft Endung gegen Whitelist, echten MIME-Typ per `finfo_file()` gegen Zuordnungstabelle Endung→erlaubte MIME-Typen
- `generate_stored_filename(string $ext): string` — `bin2hex(random_bytes(16)) . '.' . $ext`
- `generate_upload_token(): string` — `bin2hex(random_bytes(24))` für Chunk-Upload-Sessions
- `enough_disk_space(int $neededBytes): bool` — `disk_free_space()` mit Sicherheitspuffer (z. B. zusätzlich 500 MB frei lassen)
- `human_filesize(int $bytes): string`

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/includes/functions.php`

---

### Schritt 6: `login.php` — Login-Seite mit Login-Fenster

**Aktionen:**
- `enforce_https()`, Session-Start
- Falls bereits eingeloggt: Redirect zu `index.php`
- Bei POST: `attempt_login()`, bei Erfolg Redirect zu `index.php`, bei Misserfolg generische Fehlermeldung („Benutzername oder Passwort falsch")
- HTML: eigenständige, schlanke Seite im getmatic-Look (Logo oben, darunter das Login-Fenster als zentrierte Card — `.einsatz-cta`-Button-Stil für Submit)
- CSRF-Token als verstecktes Feld
- `<meta name="robots" content="noindex, nofollow">`

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/login.php`

---

### Schritt 7: `upload_chunk.php` — einzelnen Chunk entgegennehmen

**Aktionen:**
- `enforce_https()`, `require_login()`
- Erwartet: `token` (Upload-Session), `chunk_index`, Chunk-Binärdaten im Request-Body
- Prüft, dass `token` zu einer `in_progress`-Session des aktuell eingeloggten Nutzers gehört (aus `intern_upload_sessions`)
- Hängt die empfangenen Bytes an `tmp_uploads/<token>.part` an (`fopen(..., 'ab')`), aktualisiert `received_bytes` in der DB
- Gibt JSON zurück: `{ "ok": true, "received_bytes": ... }` bzw. Fehlermeldung
- Bei erstem Chunk (`chunk_index === 0`): legt zuerst die `intern_upload_sessions`-Zeile an (Token, Originalname, Gesamtgröße aus Request-Metadaten, `created_by`)

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/upload_chunk.php`

---

### Schritt 8: `upload_finalize.php` — Datei fertigstellen

**Aktionen:**
- `enforce_https()`, `require_login()`
- Lädt die `intern_upload_sessions`-Zeile zum `token`, prüft `received_bytes === total_size_bytes`
- Prüft `enough_disk_space()`
- Führt `validate_file()` auf der zusammengesetzten Datei in `tmp_uploads/<token>.part` aus
- Bei Erfolg: generiert Speichernamen, verschiebt die Datei nach `files/<stored_filename>`, legt Zeile in `intern_files` an, markiert Upload-Session als `completed`, löscht die Temp-Datei-Referenz
- Bei Validierungsfehler: löscht die Temp-Datei, markiert Session als `failed`, gibt Fehlermeldung zurück (z. B. „Dateityp nicht erlaubt")
- Gibt JSON-Antwort ans Frontend zurück

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/upload_finalize.php`

---

### Schritt 9: `assets/chunked-upload.js` — Client-seitiger Chunk-Upload

**Aktionen:**
- Bei Datei-Auswahl im Upload-Formular: Datei in `CHUNK_SIZE`-große `Blob`-Stücke zerlegen (`File.slice()`)
- Sequenziell (ein Chunk nach dem anderen, um den Server nicht mit Parallel-Requests zu überlasten) per `fetch()` an `upload_chunk.php` senden
- Fortschrittsbalken aktualisieren (`hochgeladene Bytes / Gesamtgröße`)
- Bei Chunk-Fehler: bis zu 3 automatische Wiederholungen mit kurzer Pause, danach Abbruch mit Fehlermeldung für den Nutzer
- Nach letztem Chunk: `upload_finalize.php` aufrufen, Ergebnis anzeigen, bei Erfolg Dateiliste neu laden (bzw. Seite neu laden)
- Kein Build-Tool/Bundler nötig — reines Vanilla-JS, per `<script>`-Tag in `index.php` eingebunden (konsistent mit dem Rest der Website)

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/assets/chunked-upload.js`

---

### Schritt 10: `index.php` — Dashboard

**Aktionen:**
- `enforce_https()`, `require_login()`
- Upload-Formular (Dateiauswahl + Fortschrittsbalken-Container, gesteuert von `chunked-upload.js`), CSRF-Token als Meta-Tag oder verstecktes Feld für die JS-Requests
- Gemeinsame Dateiliste: `SELECT intern_files.*, intern_users.display_name FROM intern_files JOIN intern_users ON ... ORDER BY uploaded_at DESC` — Spalten: Dateiname, Größe (`human_filesize()`), hochgeladen von, Datum, Download-Link (`download.php?id=...`), Löschen-Button (`delete.php?id=...`, mit JS-Bestätigungsdialog „Wirklich löschen?")
- Header-Bereich: „Angemeldet als: `$_SESSION['username']`" + Logout-Link
- `<meta name="robots" content="noindex, nofollow">`

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/index.php`

---

### Schritt 11: `download.php` — kontrollierte Dateiauslieferung mit Range-Support

**Aktionen:**
- `enforce_https()`, `require_login()`
- `id`-Parameter validieren, Datensatz aus `intern_files` laden (404 falls nicht vorhanden)
- Prüfen, dass die aufgelöste Datei tatsächlich innerhalb von `files/` liegt (defensive Pfadprüfung)
- `Range`-Header auswerten: falls vorhanden, per `fseek()`/`fread()`-Schleife nur den angefragten Byte-Bereich ausliefern, Status `206 Partial Content`, `Content-Range`-Header setzen; sonst komplette Datei per `readfile()` (bereits speichereffizient, streamt intern)
- `Content-Type` aus `mime_type`, `Content-Disposition: attachment; filename="<original_filename, sicher escaped>"`, `Content-Length`, `Accept-Ranges: bytes`
- `set_time_limit(0)` vor der Auslieferung, `ob_end_clean()`/Output-Buffering deaktivieren

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/download.php`

---

### Schritt 12: `delete.php` — Datei löschen

**Aktionen:**
- `enforce_https()`, `require_login()`, CSRF-Prüfung (Token als POST-Parameter, nicht per einfachem GET-Link, um versehentliches/erzwungenes Löschen über einen reinen Link zu verhindern)
- Lädt Datensatz aus `intern_files`, löscht die physische Datei aus `files/`, löscht den DB-Eintrag
- Redirect zurück zu `index.php` mit Erfolgsmeldung

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/delete.php`

---

### Schritt 13: `logout.php`

**Aktionen:**
- `logout()` aus `auth.php` aufrufen, Redirect zu `login.php`

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/logout.php`

---

### Schritt 14: Styling (`assets/portal.css`)

**Aktionen:**
- Eigenes Stylesheet, das `../../style.css` mit einbindet (Farbvariablen/Fonts) und ergänzt um: `.portal-login-box`, Formular-Elemente, `.portal-btn` (`.einsatz-cta`-Stil), `.portal-table` (Dateiliste, responsive Scroll), `.portal-progress`/`.portal-progress-bar` (Fortschrittsbalken für Chunked Upload), `.portal-flash-error`/`.portal-flash-success`
- Mobile-Anpassung

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/assets/portal.css`

---

### Schritt 15: Aufräumen abgebrochener Uploads

**Aktionen:**
- In `index.php` beim Laden: kurze Prüfung, ob `intern_upload_sessions`-Zeilen mit Status `in_progress` und `created_at` älter als 24 Stunden existieren — falls ja, zugehörige `.part`-Datei in `tmp_uploads/` löschen und Session als `failed` markieren
- Keine Cronjob-Abhängigkeit, läuft „nebenbei" bei normaler Nutzung (siehe Offene Frage 4 für die sauberere Cronjob-Alternative)

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/index.php` (Ergänzung zum bereits in Schritt 10 erstellten Code)

---

### Schritt 16: Ersten Mitarbeiter-Account anlegen

**Aktionen:**
- `intern/sql/add_user.php.example` erstellen: Vorlage für ein Skript, das `username`, `display_name`, `password` entgegennimmt, `password_hash()` bildet und in `intern_users` einfügt
- Hinweis-Kommentar: **nach Erstanlage der Konten diese Datei vom Server löschen oder zusätzlich absichern**
- Bei der Implementierung: Account `TG` (Zugangsdaten liegen dem Assistenten aus der Konversation vor, werden **nicht** in eine Repo-Datei geschrieben) direkt in der Datenbank anlegen — entweder per einmaliger Ausführung von `add_user.php` auf dem Server oder per direktem `INSERT` im 1blu-Datenbank-Tool mit vorher lokal (nicht im Repo) berechnetem `password_hash()`-Wert

**Betroffene Dateien:**
- `reference/Getmatic_website/intern/sql/add_user.php.example`

---

### Schritt 17: Dokumentation aktualisieren

**Aktionen:**
- `context/current-data.md`: neuen Abschnitt „Interner Mitarbeiterbereich (`intern/`)" ergänzen — Struktur-Übersicht, Hinweis dass `config.php`/echte Zugangsdaten/hochgeladene Dateien **nie** im normalen Re-Upload-Workflow committet werden, verbleibende offene Punkte (Speicherplatz-Kontingent prüfen, Löschrecht bestätigt = alle dürfen alles löschen), Deployment-Reihenfolge

**Betroffene Dateien:**
- `context/current-data.md`

---

## Verbindungen & Abhängigkeiten

### Dateien, die diesen Bereich referenzieren

Keine bestehende Seite verlinkt auf `intern/` (bewusst) — keine Änderungen an `index.html`, `docucontrol.html` oder anderen bestehenden Seiten nötig.

### Nötige Updates für Konsistenz

- `.gitignore` muss vor dem ersten Anlegen einer echten `config.php` aktualisiert sein (Schritt 1).
- `context/current-data.md` muss den abweichenden Deployment-Workflow (Server-seitige Handarbeit für DB/Config/Dateien statt reinem Re-Upload) klar dokumentieren.

### Auswirkungen auf bestehende Workflows

- Erstmals gibt es Website-Bestandteile, die **nicht** 1:1 aus `reference/Getmatic_website/` hochgeladen werden können (echte `config.php`, Datenbank, Nutzerdateien, Chunk-Temp-Dateien). Der bestehende „alles per FileZilla hochladen"-Workflow muss um die in Schritt 17 dokumentierte Reihenfolge ergänzt werden.
- Bestehender offener Punkt `.htaccess`-Upload (301-Redirect glander-led.de) bleibt unabhängig bestehen — betrifft ein anderes Verzeichnis (Root statt `intern/`).

---

## Validierungs-Checkliste

- [ ] `intern/files/` und `intern/tmp_uploads/` sind per direktem Browser-Aufruf einer bekannten Datei-URL **nicht** erreichbar (403)
- [ ] `intern/includes/` und `intern/config.php` sind per direktem Browser-Aufruf **nicht** erreichbar
- [ ] Login mit korrekten Zugangsdaten funktioniert, mit falschen nicht (generische Fehlermeldung)
- [ ] Nach 5 Fehlversuchen für denselben Nutzernamen: Login für 15 Minuten gesperrt
- [ ] Upload einer kleinen Testdatei (wenige MB) funktioniert vollständig inkl. Fortschrittsbalken
- [ ] Upload einer großen Testdatei (mehrere GB, so groß wie praktikabel testbar) funktioniert über mehrere Chunks hinweg, inkl. Retry bei simuliertem Verbindungsfehler
- [ ] Datei-Upload lehnt eine umbenannte `.php`-Datei mit erlaubter Endung ab (MIME-Check)
- [ ] Download liefert die Datei mit korrektem Original-Dateinamen aus
- [ ] Download einer großen Datei kann nach simuliertem Abbruch fortgesetzt werden (Range-Request)
- [ ] Löschen einer Datei entfernt sie aus der Liste, aus `files/` und aus der Datenbank
- [ ] Ohne Login ist `index.php`/`download.php`/`upload_chunk.php`/`delete.php` nicht erreichbar (Redirect zu `login.php`)
- [ ] Seite ist nur per HTTPS erreichbar
- [ ] `config.php` ist in `.gitignore`, taucht in `git status` nicht als stagebar auf
- [ ] `add_user.php` ist nach Anlage der Konten vom Server entfernt oder zusätzlich abgesichert
- [ ] Abgebrochene Uploads in `tmp_uploads/` werden nach 24 Stunden automatisch aufgeräumt
- [ ] `context/current-data.md` beschreibt den abweichenden Deployment-Workflow korrekt

## Erfolgskriterien

1. Mehrere individuelle Mitarbeiterkonten (mindestens „TG") können sich über ein gestaltetes Login-Fenster anmelden.
2. Eingeloggte Nutzer können Dateien bis 20 GB hochladen, herunterladen und löschen; nicht eingeloggte Besucher kommen an nichts heran.
3. Der Bereich übersteht die Validierungs-Checkliste oben vollständig.
4. Keine Zugangsdaten oder hochgeladenen Dateien landen im Git-Repository.

---

## Notizen

- Cronjob-basiertes Aufräumen abgebrochener Uploads (statt der einfachen Lösung in Schritt 15) wäre sauberer, falls 1blu Cronjobs im Kundencenter anbietet — nicht Teil dieses Plans, als Folge-Idee vermerkt.
- Passwort-Reset-Funktion (Self-Service) ist bewusst **nicht** Teil dieser ersten Version — bei wenigen internen Konten reicht es, wenn neue Passwörter direkt über die `add_user.php`-Logik gesetzt werden. Als Folge-Idee vermerkt.
- Ein `reference/Getmatic_website/intern/README.md` mit Betriebs-Hinweisen (neuen Nutzer anlegen, Passwort zurücksetzen, Speicherplatz prüfen) könnte sinnvoll sein, sobald sich der Bereich im Alltag bewährt hat — nicht Teil dieses Plans.
- Bei sehr großen Dateien (mehrere GB) hängt die tatsächlich erreichbare Upload-/Download-Geschwindigkeit stark von der 1blu-Serveranbindung und der eigenen Internetverbindung ab — 20 GB können je nach Bandbreite mehrere Stunden dauern. Das ist eine physikalische Grenze, kein Implementierungsdetail.

---

## Implementierungsnotizen

**Implementiert:** 2026-07-08

### Zusammenfassung

Alle 17 Schritte umgesetzt: vollständiger `intern/`-Bereich mit PHP-Session-Login (bcrypt, Rate-Limiting, CSRF), gemeinsamem Dateipool, Chunked Upload (8-MB-Häppchen, Fortschrittsbalken, automatischer Retry) für Dateien bis 20 GB, Resume-fähigem Download per HTTP-Range, Löschfunktion, mehrschichtiger Absicherung (`.htaccess`-Sperren auf `files/`/`tmp_uploads/`/`includes/`, Whitelist + echter MIME-Check, zufällige Speichernamen, defensive Pfadprüfung bei Download/Löschen) und automatischem Aufräumen abgebrochener Uploads. `.gitignore` erweitert und per `git add --dry-run` verifiziert: `config.php`, echte Dateien in `files/`/`tmp_uploads/` sowie ein ausgefülltes `add_user.php` würden korrekt **nicht** mit eingecheckt — nur die `.htaccess`-Dateien und `config.example.php`/`add_user.php.example` als Vorlagen.

### Abweichungen vom Plan

- **Offset-Synchronisation beim Chunk-Upload ergänzt** (nicht explizit im Plan, aber im Sinne der Resume-Anforderung): `upload_chunk.php` vergleicht einen vom Client mitgesendeten `expected_offset` mit dem tatsächlichen `received_bytes`-Stand in der Datenbank und lehnt bei Abweichung mit HTTP 409 ab, statt blind anzuhängen. Verhindert doppelt angehängte Chunks bei verlorenen Bestätigungen.
- Sonst keine inhaltlichen Abweichungen — alle Dateien, Tabellen und Sicherheitsmaßnahmen wie im Plan spezifiziert umgesetzt.

### Aufgetretene Probleme

- **Kein PHP und keine MySQL-Instanz in dieser lokalen Umgebung verfügbar** — alle PHP-Dateien wurden daher nicht per `php -l`/Laufzeittest, sondern durch sorgfältige manuelle Zeile-für-Zeile-Prüfung auf Syntax- und Logikfehler kontrolliert (u. a. `require_once`-Pfade, PDO-Parameterbindung, Klammer-/Anführungszeichen-Balance). Das JavaScript (`chunked-upload.js`) wurde erfolgreich mit `node --check` auf Syntaxfehler geprüft.
- **Kein Zugriff auf den produktiven 1blu-Server/die Datenbank** — die Validierungs-Checkliste des Plans (Login-Test, Upload/Download/Löschen, `.htaccess`-Sperren, Rate-Limiting) konnte in dieser Session **nicht live durchgeführt werden** und muss vom User nach dem Deployment nachgeholt werden (siehe Abschnitt „Interner Mitarbeiterbereich" in `context/current-data.md` für die genaue Reihenfolge).
- Das Passwort für den ersten Account („TG") wurde dem Assistenten in der Konversation mitgeteilt, aber bewusst in keine Datei geschrieben — die Account-Anlage erfolgt erst nach dem Deployment über `add_user.php` direkt auf dem Server.

### Offen für den User

- Deployment durchführen (Datenbank-Schema anlegen, `intern/`-Ordner hochladen, `config.php` mit echten Zugangsdaten + eigenem `ADD_USER_SETUP_KEY` erstellen, ersten Account anlegen, `add_user.php` danach löschen) — Schritt-für-Schritt-Anleitung in `context/current-data.md`.
- Anschließend die komplette Validierungs-Checkliste dieses Plans einmal live durchgehen, insbesondere die Sicherheits-relevanten Punkte (Direktzugriffssperren, MIME-Check mit einer umbenannten Testdatei, Verhalten ohne Login).
- Test mit einer wirklich großen Datei (mehrere GB) empfehlenswert, um Chunked Upload und Resume-Download unter realistischen Bedingungen zu prüfen.

---

## Live-Deployment-Nachtrag

**Deployt und bestätigt funktionsfähig:** 2026-07-08

Deployment gemeinsam mit dem User durchgeführt (Chat-geführt, da kein Serverzugriff für den Assistenten). Dabei kamen vier reale Bugs zum Vorschein, die im lokalen Code-Review nicht auffindbar waren (kein PHP/MySQL lokal verfügbar) — alle behoben, Details in `context/current-data.md` unter „Interner Mitarbeiterbereich":

1. `enforce_https()`-PHP-Redirect kollidierte mit 1blus Proxy-Setup → HTTPS-Erzwingung nach `.htaccess` verlagert.
2. `add_user.php.example` hatte falsche relative Pfade für den finalen Speicherort → korrigiert.
3. `finfo_close()` seit PHP 8.5 deprecated, verunreinigte JSON-Antworten → Aufruf entfernt.
4. Bekanntes 1blu/FileZilla-Resume-Upload-Problem verdoppelte `functions.php` → neu hochgeladen mit Überschreiben statt Fortsetzen.

Login, Dashboard und Chunked-Upload sind live verifiziert. Ausstehend (nicht blockierend): Test mit einer wirklich großen Datei (mehrere GB) sowie die restlichen Punkte der Validierungs-Checkliste (Direktzugriffssperren, MIME-Check-Ablehnung, Rate-Limiting) noch nicht einzeln live durchexerziert.
