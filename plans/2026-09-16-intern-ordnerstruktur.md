# Plan: Ordnerstruktur für den internen Mitarbeiterbereich

**Erstellt:** 2026-09-16
**Status:** Implementiert
**Anforderung:** Im internen Mitarbeiterbereich (`intern/index.php`) sollen Mitarbeiter Ordner anlegen und löschen können, um den bisher komplett flachen Dateipool zu strukturieren.

---

## Überblick

### Was dieser Plan erreicht

Der interne Bereich bekommt eine virtuelle Ordnerhierarchie: Nutzer können Unterordner anlegen, hineinnavigieren, Dateien gezielt in einen Ordner hochladen und leere Ordner wieder löschen. Die physische Ablage der Dateien auf der Festplatte (`intern/files/`, flach, mit zufälligen Dateinamen) bleibt dabei komplett unverändert — Ordner existieren ausschließlich als Struktur in der Datenbank.

### Warum das wichtig ist

Der interne Bereich wird von mehreren Mitarbeitern gemeinsam genutzt (siehe `context/current-data.md`, Abschnitt „Interner Mitarbeiterbereich") und sammelt seit der Einführung (2026-07-08) laufend Dateien in einer einzigen flachen Liste. Ohne Ordner wird diese Liste mit der Zeit unübersichtlich und erschwert es, thematisch zusammengehörige Dateien (z. B. nach Kunde, Projekt oder Anlage) wiederzufinden.

---

## Aktueller Zustand

### Relevante bestehende Struktur

- `reference/Getmatic_website/intern/sql/schema.sql` — Tabellen `intern_users`, `intern_files` (flach, kein Hierarchie-Feld), `intern_login_attempts`, `intern_upload_sessions`
- `reference/Getmatic_website/intern/index.php` — Dashboard: Upload-Formular + eine einzige `ORDER BY uploaded_at DESC`-Liste aller Dateien, Lösch-Buttons mit `confirm()` + CSRF-Token
- `reference/Getmatic_website/intern/delete.php` — POST-only, CSRF-geprüft, löscht Datei per prepared statement + `realpath()`-abgesichertem Dateisystempfad, dann DB-Zeile, Redirect zu `index.php?deleted=1`
- `reference/Getmatic_website/intern/upload_chunk.php` — nimmt 8-MB-Häppchen entgegen, legt bei `chunk_index === 0` eine Zeile in `intern_upload_sessions` an (`token`, `original_filename`, `total_size_bytes`, `created_by`)
- `reference/Getmatic_website/intern/upload_finalize.php` — prüft Größe/MIME-Typ, verschiebt die fertige Datei nach `files/<random-hex>.<ext>`, schreibt die `intern_files`-Zeile
- `reference/Getmatic_website/intern/includes/auth.php` — `require_login()`, `csrf_token()`/`csrf_check()`, `current_user_id()`
- `reference/Getmatic_website/intern/includes/functions.php` — Whitelists, `validate_file()`, `generate_stored_filename()`, `human_filesize()`
- `reference/Getmatic_website/intern/assets/chunked-upload.js` — treibt den Chunk-Upload-Ablauf im Browser, ruft `upload_chunk.php` und `upload_finalize.php` per `fetch()` auf
- `reference/Getmatic_website/intern/assets/portal.css` — Portal-Styling (`.portal-card`, `.portal-table`, `.portal-btn`, `.portal-link`, `.portal-link-danger`, …)
- Gemeinsamer Dateipool: alle eingeloggten Mitarbeiter sehen alles und dürfen alles löschen (bewusste Vereinfachung, siehe `context/current-data.md`) — dieses Verhalten gilt unverändert auch für Ordner

### Lücken oder Probleme, die adressiert werden

- Keine Möglichkeit, Dateien zu gruppieren — alles liegt in einer einzigen, wachsenden Tabelle/Liste
- Kein Mechanismus, um beim Hochladen ein Ziel-Verzeichnis anzugeben

---

## Vorgeschlagene Änderungen

### Zusammenfassung der Änderungen

- Neue Tabelle `intern_folders` (virtuelle Ordner, rein in der Datenbank — keine echten Verzeichnisse auf der Platte)
- `intern_files` und `intern_upload_sessions` bekommen je eine neue, nullable Spalte `folder_id` (`NULL` = Root-Ebene, unverändertes Verhalten für alle vor der Migration hochgeladenen Dateien)
- Zwei neue Endpunkte: `create_folder.php` (Ordner anlegen) und `delete_folder.php` (nur leere Ordner löschbar — siehe Design-Entscheidung)
- `index.php` navigiert per `?folder_id=<id>`, zeigt Breadcrumb, Unterordner-Liste und Datei-Liste des aktuellen Ordners, hat ein „Neuer Ordner"-Formular
- Upload-Formular übergibt den aktuell geöffneten Ordner an `upload_chunk.php`/`upload_finalize.php`, damit neue Dateien dort landen
- `portal.css` bekommt Styles für Breadcrumb und Ordner-Zeilen

### Neue Dateien erstellen

| Dateipfad | Zweck |
| --- | --- |
| `reference/Getmatic_website/intern/create_folder.php` | POST-Endpunkt: legt einen neuen Unterordner im aktuellen Ordner an |
| `reference/Getmatic_website/intern/delete_folder.php` | POST-Endpunkt: löscht einen Ordner, aber nur wenn er leer ist (keine Unterordner, keine Dateien) |
| `reference/Getmatic_website/intern/sql/migration_2026-09-16_ordner.sql` | ALTER/CREATE-Statements für bestehende Live-Installationen (schema.sql wurde bereits einmal ausgeführt, daher separate Migration statt Änderung von schema.sql) |

### Zu ändernde Dateien

| Dateipfad | Änderungen |
| --- | --- |
| `reference/Getmatic_website/intern/sql/schema.sql` | `intern_folders`-Tabelle ergänzen, `folder_id`-Spalten in `intern_files`/`intern_upload_sessions` ergänzen — damit eine **komplett neue** Installation direkt mit Ordnern startet |
| `reference/Getmatic_website/intern/index.php` | Ordner-Navigation (`?folder_id=`), Breadcrumb, Unterordner-Liste, „Neuer Ordner"-Formular, Lösch-Buttons für Ordner, `folder_id` als Hidden-Input im Upload-Formular |
| `reference/Getmatic_website/intern/upload_chunk.php` | `folder_id` aus `$_POST` lesen (nur bei `chunk_index === 0`), validieren, in `intern_upload_sessions` mitschreiben |
| `reference/Getmatic_website/intern/upload_finalize.php` | `folder_id` aus der Upload-Session übernehmen und in `intern_files` mitschreiben |
| `reference/Getmatic_website/intern/assets/chunked-upload.js` | `folder_id` aus dem Formular lesen und bei `chunk_index === 0` mitsenden |
| `reference/Getmatic_website/intern/assets/portal.css` | Neue Klassen `.portal-breadcrumb`, `.portal-folder-row`, `.portal-folder-icon` |
| `context/current-data.md` | Neuer Eintrag zur Ordnerstruktur-Funktion inkl. Deployment-Hinweis (SQL zuerst manuell ausführen) |

### Zu löschende Dateien

Keine.

---

## Design-Entscheidungen

### Getroffene Schlüsselentscheidungen

1. **Ordner sind rein virtuell (nur in der DB), keine echten Verzeichnisse auf der Platte.** Physische Dateien bleiben wie bisher flach in `files/` mit zufälligem `stored_filename`. Begründung: `delete.php`s Pfadsicherheit (`realpath()` + Prefix-Check), `download.php` und der komplette Chunked-Upload-Mechanismus bleiben dadurch komplett unangetastet — kein neues Path-Traversal-Risiko durch echte verschachtelte Verzeichnisse, keine Race-Conditions beim Verschieben großer Dateien zwischen echten Ordnern.
2. **Nur leere Ordner können gelöscht werden** (keine Unterordner, keine Dateien darin). Begründung: verhindert versehentlichen Massenverlust durch einen einzigen Klick in einem Mehrbenutzer-Pool ohne Papierkorb/Undo. Ein Nutzer, der einen vollen Ordner löschen will, muss zuerst bewusst die Dateien verschieben/löschen — das ist die risikoärmere Variante gegenüber rekursivem Löschen.
3. **`folder_id = NULL` bedeutet Root-Ebene**, sowohl in `intern_files` als auch in `intern_upload_sessions`. Dadurch bleiben alle vor dieser Migration hochgeladenen Dateien ohne jede Datenmigration automatisch auf der Root-Ebene sichtbar.
4. **Kein Datenbank-`UNIQUE`-Constraint gegen doppelte Ordnernamen im selben Parent**, stattdessen ein Check auf Anwendungsebene in `create_folder.php` (SELECT vor INSERT). Begründung: MySQL/InnoDB behandelt in einem `UNIQUE`-Index mehrere `NULL`-Werte als paarweise verschieden — ein `UNIQUE(parent_id, name)`-Index würde also doppelte Namen auf der Root-Ebene (`parent_id IS NULL`) nicht verhindern. Ein Anwendungs-Check funktioniert dagegen unabhängig von `NULL`-Semantik korrekt für Root und Unterordner gleichermaßen.
5. **Gemeinsamer Pool bleibt bestehen**: Jeder eingeloggte Mitarbeiter sieht alle Ordner und darf jeden leeren Ordner löschen — konsistent mit dem bereits bestehenden Verhalten bei Dateien, keine neue Owner-Restriktion.
6. **Separates Migrations-SQL-Skript statt Änderung der bereits live ausgeführten `schema.sql`-Historie**: `schema.sql` selbst wird für Neuinstallationen ergänzt (damit sie direkt vollständig ist), zusätzlich gibt es `migration_2026-09-16_ordner.sql` mit reinen `ALTER`/`CREATE`-Statements, die der User auf der bestehenden Live-Datenbank nachträglich ausführt — exakt das Muster, das beim ursprünglichen Deployment des internen Bereichs bereits etabliert wurde (SQL manuell in phpMyAdmin, dann Dateien hochladen).

### Betrachtete Alternativen

- **Echte Verzeichnisse auf der Festplatte je Ordner**: verworfen — hätte `delete.php`, `download.php` und den Chunked-Upload-Mechanismus grundlegend anfassen müssen und neue Path-Traversal-Angriffsfläche geschaffen, ohne echten Mehrwert gegenüber der virtuellen Lösung.
- **Rekursives Löschen nicht-leerer Ordner (mit "X Dateien werden mitgelöscht"-Warnung)**: verworfen für die erste Version — höheres Risiko für versehentlichen Datenverlust im gemeinsamen Pool ohne Papierkorb-Funktion. Kann bei Bedarf später als separater, bewusst gewählter Folge-Schritt ergänzt werden.
- **Mehrfachauswahl/Verschieben von Dateien zwischen Ordnern (Drag&Drop)**: nicht Teil dieses Plans — Scope bewusst auf „Ordner anlegen/löschen" begrenzt, wie angefragt. Kann als separater Folge-Plan behandelt werden, falls gewünscht.

### Offene Fragen

Keine — alle nötigen Entscheidungen (virtuelle vs. echte Ordner, Löschverhalten bei nicht-leeren Ordnern, Sichtbarkeit) wurden oben mit Begründung getroffen. Der User kann beim Review widersprechen, falls z. B. doch rekursives Löschen gewünscht ist.

---

## Schritt-für-Schritt-Aufgaben

### Schritt 1: `schema.sql` für Neuinstallationen ergänzen

**Aktionen:**

- In `reference/Getmatic_website/intern/sql/schema.sql` nach der `intern_users`-Tabelle folgende neue Tabelle einfügen:
  ```sql
  CREATE TABLE intern_folders (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      parent_id INT NULL,
      created_by INT NOT NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (parent_id) REFERENCES intern_folders(id) ON DELETE CASCADE,
      FOREIGN KEY (created_by) REFERENCES intern_users(id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
  ```
  (Kein `UNIQUE`-Constraint auf `(parent_id, name)` — siehe Design-Entscheidung 4, Duplikat-Check läuft in PHP.)
- Die bestehende `intern_files`-Tabelle um eine Spalte ergänzen (direkt im `CREATE TABLE`-Statement, da `schema.sql` nur für Neuinstallationen gilt):
  ```sql
  folder_id INT NULL,
  ...
  FOREIGN KEY (folder_id) REFERENCES intern_folders(id) ON DELETE SET NULL,
  ```
  (Spalte direkt nach `uploaded_by` einfügen, zusätzliches `FOREIGN KEY` nach dem bestehenden `FOREIGN KEY (uploaded_by) ...`.)
- Die bestehende `intern_upload_sessions`-Tabelle ebenso um `folder_id INT NULL` (nach `created_by`) plus passendem `FOREIGN KEY (folder_id) REFERENCES intern_folders(id) ON DELETE SET NULL` ergänzen.
- Kommentar am Dateianfang ergänzen, der auf die separate Migrationsdatei für bestehende Installationen verweist.

**Betroffene Dateien:**

- `reference/Getmatic_website/intern/sql/schema.sql`

---

### Schritt 2: Migrations-Skript für die bestehende Live-Datenbank erstellen

**Aktionen:**

- Neue Datei `reference/Getmatic_website/intern/sql/migration_2026-09-16_ordner.sql` mit folgendem Inhalt anlegen:
  ```sql
  -- Migration: Ordnerstruktur fuer den internen Bereich (2026-09-16)
  -- Einmalig im 1blu-Datenbank-Tool (phpMyAdmin o.ae.) auf der BESTEHENDEN
  -- Live-Datenbank ausfuehren (schema.sql wurde dort bereits ausgefuehrt).

  CREATE TABLE intern_folders (
      id INT AUTO_INCREMENT PRIMARY KEY,
      name VARCHAR(100) NOT NULL,
      parent_id INT NULL,
      created_by INT NOT NULL,
      created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
      FOREIGN KEY (parent_id) REFERENCES intern_folders(id) ON DELETE CASCADE,
      FOREIGN KEY (created_by) REFERENCES intern_users(id)
  ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

  ALTER TABLE intern_files
      ADD COLUMN folder_id INT NULL AFTER uploaded_by,
      ADD FOREIGN KEY (folder_id) REFERENCES intern_folders(id) ON DELETE SET NULL;

  ALTER TABLE intern_upload_sessions
      ADD COLUMN folder_id INT NULL AFTER created_by,
      ADD FOREIGN KEY (folder_id) REFERENCES intern_folders(id) ON DELETE SET NULL;
  ```
- Bestehende Zeilen in `intern_files`/`intern_upload_sessions` brauchen kein `UPDATE`: `folder_id` ist `NULL` per Default, das entspricht automatisch der Root-Ebene.

**Betroffene Dateien:**

- `reference/Getmatic_website/intern/sql/migration_2026-09-16_ordner.sql` (neu)

---

### Schritt 3: `create_folder.php` erstellen

**Aktionen:**

- Neue Datei nach dem Muster von `delete.php` (gleicher Header: `config.php`, `auth.php`, `functions.php`, `enforce_https()`, `require_login()`, POST-only, CSRF-Check).
- Logik:
  1. `$name = trim((string) ($_POST['name'] ?? ''));`
  2. `$parentId = isset($_POST['parent_id']) && $_POST['parent_id'] !== '' ? (int) $_POST['parent_id'] : null;`
  3. Validierung: `$name === ''` → Fehler „Bitte einen Ordnernamen angeben."; `mb_strlen($name) > 100` → Fehler „Ordnername ist zu lang (max. 100 Zeichen)."
  4. Falls `$parentId !== null`: prüfen, dass dieser Ordner existiert (`SELECT id FROM intern_folders WHERE id = :id`), sonst Fehler „Übergeordneter Ordner nicht gefunden."
  5. Duplikat-Check: `SELECT COUNT(*) FROM intern_folders WHERE name = :name AND parent_id <=> :parent_id` (der `<=>`-Operator (NULL-safe equal) behandelt `NULL`-Vergleiche korrekt) → falls > 0, Fehler „Ein Ordner mit diesem Namen existiert hier bereits."
  6. Bei einem Fehler: zurück zu `index.php?folder_id=<parentId oder leer>&folder_error=<urlencodierte Nachricht>` (Pattern wie `delete.php`s Redirect mit Query-Flag, aber mit Fehlertext statt reinem Flag).
  7. Bei Erfolg: `INSERT INTO intern_folders (name, parent_id, created_by) VALUES (:name, :parent_id, :user_id)`, danach Redirect zu `index.php?folder_id=<parentId oder leer>&folder_created=1`.

**Betroffene Dateien:**

- `reference/Getmatic_website/intern/create_folder.php` (neu)

---

### Schritt 4: `delete_folder.php` erstellen

**Aktionen:**

- Gleicher Datei-Header wie `delete.php`.
- Logik:
  1. `$id = (int) ($_POST['id'] ?? 0);` — bei `<= 0` Fehler 400.
  2. Ordner laden: `SELECT * FROM intern_folders WHERE id = :id LIMIT 1` — nicht gefunden → Redirect zu `index.php` (kein Fehler nötig, Ordner ist schon weg).
  3. Prüfen, ob leer: `SELECT COUNT(*) FROM intern_folders WHERE parent_id = :id` UND `SELECT COUNT(*) FROM intern_files WHERE folder_id = :id`. Falls eine der beiden Zahlen > 0 → **nicht löschen**, Redirect zu `index.php?folder_id=<id>&folder_error=` + urlencodierte Nachricht „Ordner ist nicht leer und kann nicht gelöscht werden."
  4. Falls leer: `DELETE FROM intern_folders WHERE id = :id`, Redirect zu `index.php?folder_id=<parent_id des geloeschten Ordners oder leer>&folder_deleted=1` (damit der Nutzer nach dem Löschen im übergeordneten Ordner landet statt in einem nicht mehr existierenden).

**Betroffene Dateien:**

- `reference/Getmatic_website/intern/delete_folder.php` (neu)

---

### Schritt 5: `index.php` um Ordner-Navigation erweitern

**Aktionen:**

- Nach `$db = get_db();` und vor der Aufräum-Logik für Stale-Uploads (Reihenfolge unkritisch, aber vor der Dateiliste) folgenden Block einfügen:
  ```php
  $currentFolderId = (isset($_GET['folder_id']) && $_GET['folder_id'] !== '') ? (int) $_GET['folder_id'] : null;

  $currentFolder = null;
  if ($currentFolderId !== null) {
      $stmt = $db->prepare('SELECT * FROM intern_folders WHERE id = :id LIMIT 1');
      $stmt->execute(['id' => $currentFolderId]);
      $currentFolder = $stmt->fetch();
      if (!$currentFolder) {
          $currentFolderId = null; // ungueltige/geloeschte Ordner-ID -> zurueck zur Wurzel
      }
  }

  // Breadcrumb: von der Wurzel bis zum aktuellen Ordner
  $breadcrumb = [];
  $walkId = $currentFolderId;
  while ($walkId !== null) {
      $stmt = $db->prepare('SELECT id, name, parent_id FROM intern_folders WHERE id = :id LIMIT 1');
      $stmt->execute(['id' => $walkId]);
      $folder = $stmt->fetch();
      if (!$folder) break;
      array_unshift($breadcrumb, $folder);
      $walkId = $folder['parent_id'] !== null ? (int) $folder['parent_id'] : null;
  }
  ```
- Die bestehende Stale-Upload-Aufräum-Logik unverändert lassen.
- Die bestehende Dateiliste-Query ersetzen durch eine ordnerbezogene Variante:
  ```php
  $subfolders = $db->prepare(
      'SELECT id, name FROM intern_folders WHERE parent_id <=> :folder_id ORDER BY name ASC'
  );
  $subfolders->execute(['folder_id' => $currentFolderId]);
  $subfolders = $subfolders->fetchAll();

  $files = $db->prepare(
      'SELECT f.id, f.original_filename, f.filesize_bytes, f.uploaded_at, u.display_name
       FROM intern_files f
       JOIN intern_users u ON u.id = f.uploaded_by
       WHERE f.folder_id <=> :folder_id
       ORDER BY f.uploaded_at DESC'
  );
  $files->execute(['folder_id' => $currentFolderId]);
  $files = $files->fetchAll();
  ```
- `$deleted = isset($_GET['deleted']);` ergänzen um:
  ```php
  $folderCreated = isset($_GET['folder_created']);
  $folderDeleted = isset($_GET['folder_deleted']);
  $folderError = isset($_GET['folder_error']) ? (string) $_GET['folder_error'] : null;
  ```
- Im HTML-Body nach dem bestehenden `<?php if ($deleted): ?>`-Block die neuen Flash-Meldungen ergänzen (gleiches Muster, `portal-flash-success`/`portal-flash-error`):
  ```php
  <?php if ($folderCreated): ?>
    <p class="portal-flash-success">Ordner angelegt.</p>
  <?php endif; ?>
  <?php if ($folderDeleted): ?>
    <p class="portal-flash-success">Ordner gelöscht.</p>
  <?php endif; ?>
  <?php if ($folderError): ?>
    <p class="portal-flash-error"><?= htmlspecialchars($folderError) ?></p>
  <?php endif; ?>
  ```
- Breadcrumb direkt unter `<h1 class="portal-title">Dateien</h1>` einfügen:
  ```php
  <nav class="portal-breadcrumb" aria-label="Ordnerpfad">
    <a href="index.php">Wurzelverzeichnis</a>
    <?php foreach ($breadcrumb as $crumb): ?>
      &raquo; <a href="index.php?folder_id=<?= (int) $crumb['id'] ?>"><?= htmlspecialchars($crumb['name']) ?></a>
    <?php endforeach; ?>
  </nav>
  ```
- Im Upload-Formular (`<form id="upload-form" ...>`) direkt nach dem `csrf_token`-Hidden-Input einen weiteren Hidden-Input ergänzen:
  ```php
  <input type="hidden" name="folder_id" id="upload-folder-id" value="<?= $currentFolderId !== null ? (int) $currentFolderId : '' ?>" />
  ```
- Neue Card „Neuer Ordner" zwischen der Upload-Card und der „Vorhandene Dateien"-Card einfügen:
  ```php
  <section class="portal-card">
    <h2>Neuer Ordner</h2>
    <form method="post" action="create_folder.php" class="portal-form">
      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>" />
      <input type="hidden" name="parent_id" value="<?= $currentFolderId !== null ? (int) $currentFolderId : '' ?>" />
      <input type="text" name="name" maxlength="100" placeholder="Ordnername" required />
      <button type="submit" class="portal-btn">Ordner anlegen</button>
    </form>
  </section>
  ```
- In der „Vorhandene Dateien"-Card (Überschrift ggf. zu „Inhalt dieses Ordners" ändern) VOR der Datei-Tabelle die Unterordner-Liste rendern, falls vorhanden:
  ```php
  <?php if ($subfolders): ?>
    <ul class="portal-folder-list">
      <?php foreach ($subfolders as $folder): ?>
        <li class="portal-folder-row">
          <a href="index.php?folder_id=<?= (int) $folder['id'] ?>" class="portal-link">
            <span class="portal-folder-icon" aria-hidden="true">&#128193;</span>
            <?= htmlspecialchars($folder['name']) ?>
          </a>
          <form method="post" action="delete_folder.php" class="portal-inline-form"
                onsubmit="return confirm('Ordner „<?= htmlspecialchars(addslashes($folder['name'])) ?>“ wirklich löschen? (nur möglich, wenn er leer ist)');">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>" />
            <input type="hidden" name="id" value="<?= (int) $folder['id'] ?>" />
            <button type="submit" class="portal-link portal-link-danger">Löschen</button>
          </form>
        </li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>
  ```
- Die bestehende `<?php if (!$files): ?>`-Bedingung anpassen: „Noch keine Dateien hochgeladen." soll nur erscheinen, wenn AUCH keine Unterordner vorhanden sind (`if (!$files && !$subfolders)`), sonst wird bei einem Ordner, der nur Unterordner enthält, fälschlich „keine Dateien" angezeigt, obwohl die Ordnerliste direkt darüber schon etwas zeigt — die Datei-Tabelle selbst bleibt aber nur sichtbar, wenn `$files` nicht leer ist (`<?php if ($files): ?>` um die bestehende `<table>` legen, unabhängig von der neuen Unterordner-Liste).

**Betroffene Dateien:**

- `reference/Getmatic_website/intern/index.php`

---

### Schritt 6: Upload-Flow um `folder_id` erweitern

**Aktionen:**

In `reference/Getmatic_website/intern/upload_chunk.php`:

- Im Block `if ($chunkIndex === 0) { ... }` nach dem Auslesen von `$totalSize` ergänzen:
  ```php
  $folderId = ($_POST['folder_id'] ?? '') !== '' ? (int) $_POST['folder_id'] : null;

  if ($folderId !== null) {
      $checkStmt = $db->prepare('SELECT id FROM intern_folders WHERE id = :id LIMIT 1');
      $checkStmt->execute(['id' => $folderId]);
      if (!$checkStmt->fetch()) {
          $folderId = null; // ungueltige Ordner-ID -> Root, statt Upload hart abzubrechen
      }
  }
  ```
- Das `INSERT INTO intern_upload_sessions`-Statement um die Spalte `folder_id` erweitern:
  ```php
  $stmt = $db->prepare(
      'INSERT INTO intern_upload_sessions
          (token, original_filename, total_size_bytes, received_bytes, created_by, folder_id, status)
       VALUES (:token, :filename, :total_size, 0, :user_id, :folder_id, "in_progress")'
  );
  $stmt->execute([
      'token' => $token,
      'filename' => $originalFilename,
      'total_size' => $totalSize,
      'user_id' => current_user_id(),
      'folder_id' => $folderId,
  ]);
  ```

In `reference/Getmatic_website/intern/upload_finalize.php`:

- Das `INSERT INTO intern_files`-Statement um `folder_id` erweitern und den Wert aus `$session['folder_id']` übernehmen:
  ```php
  $stmt = $db->prepare(
      'INSERT INTO intern_files
          (original_filename, stored_filename, mime_type, filesize_bytes, uploaded_by, folder_id)
       VALUES (:original, :stored, :mime, :size, :user_id, :folder_id)'
  );
  $stmt->execute([
      'original' => $originalFilename,
      'stored' => $storedFilename,
      'mime' => $validation['mime'],
      'size' => $actualSize,
      'user_id' => current_user_id(),
      'folder_id' => $session['folder_id'],
  ]);
  ```

In `reference/Getmatic_website/intern/assets/chunked-upload.js`:

- In `initUploadForm()`, wo `csrfToken` aus dem Formular gelesen wird, zusätzlich lesen:
  ```js
  var folderIdInput = form.querySelector('input[name="folder_id"]');
  ```
- Dies der `ui`-Objektstruktur hinzufügen: `folderId: folderIdInput ? folderIdInput.value : ''`.
- In `uploadChunk(...)`, im `if (chunkIndex === 0)`-Block, zusätzlich anhängen:
  ```js
  formData.append('folder_id', meta.folderId || '');
  ```
- In `uploadFile(file, ui)`, beim Erstellen von `meta`, `folderId: ui.folderId` ergänzen:
  ```js
  var meta = { filename: file.name, totalSize: file.size, folderId: ui.folderId };
  ```

**Betroffene Dateien:**

- `reference/Getmatic_website/intern/upload_chunk.php`
- `reference/Getmatic_website/intern/upload_finalize.php`
- `reference/Getmatic_website/intern/assets/chunked-upload.js`

---

### Schritt 7: CSS für Breadcrumb und Ordnerliste ergänzen

**Aktionen:**

- In `reference/Getmatic_website/intern/assets/portal.css` nach den bestehenden `.portal-table-wrap`/`.portal-table`-Regeln ergänzen:
  ```css
  .portal-breadcrumb {
    font-size: 0.85rem;
    color: var(--text-light);
    margin-bottom: var(--space-3);
  }
  .portal-breadcrumb a { color: var(--teal); }
  .portal-breadcrumb a:hover { color: var(--teal-dark); }

  .portal-folder-list {
    list-style: none;
    margin: 0 0 var(--space-3);
    padding: 0;
  }
  .portal-folder-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: var(--space-2) 0;
    border-bottom: 1px solid rgba(0,0,0,0.07);
  }
  .portal-folder-icon { margin-right: 6px; }
  ```

**Betroffene Dateien:**

- `reference/Getmatic_website/intern/assets/portal.css`

---

### Schritt 8: Validierung — Syntax-Check und Review

**Aktionen:**

- Für jede geänderte/neue PHP-Datei `php -l <datei>` ausführen (Syntax-Check), da auf diesem Rechner kein lokaler MySQL-Server für einen vollständigen funktionalen Test zur Verfügung steht.
- Alle neuen/geänderten Dateien nochmals gegen die Checkliste unten lesen (CSRF auf jedem POST-Endpunkt, `require_login()` überall, prepared statements überall, keine rohe Nutzereingabe ungeschützt in HTML ausgegeben).
- Dem User zusätzlich einen manuellen Test-Ablauf für nach dem Live-Deployment mitgeben (siehe Validierungs-Checkliste).

**Betroffene Dateien:**

- Keine (reiner Prüfschritt)

---

### Schritt 9: `context/current-data.md` aktualisieren

**Aktionen:**

- Neuen datierten Eintrag im Abschnitt „Interner Mitarbeiterbereich" ergänzen, der die neue Ordnerfunktion beschreibt und den abweichenden Deployment-Workflow für diesen Bereich fortschreibt:
  ```
  - **2026-09-16:** Ordnerstruktur ergänzt — Mitarbeiter können im internen Bereich jetzt Unterordner anlegen und (nur wenn leer) wieder löschen. Ordner sind rein virtuell (nur in der DB, `intern_folders`-Tabelle), physische Dateien bleiben unverändert flach in `files/`. Neue Endpunkte `create_folder.php`/`delete_folder.php`, `index.php` navigiert per `?folder_id=`. **Deployment-Reihenfolge (abweichend, wie beim ursprünglichen intern/-Rollout):** 1) `intern/sql/migration_2026-09-16_ordner.sql` einmalig in phpMyAdmin auf der bestehenden Live-Datenbank ausführen, 2) betroffene Dateien hochladen (`index.php`, `create_folder.php`, `delete_folder.php`, `upload_chunk.php`, `upload_finalize.php`, `assets/chunked-upload.js`, `assets/portal.css`, `sql/schema.sql`). **Noch nicht deployt.**
  ```
- Neuen Punkt in „Offene Aufgaben / nächste Schritte" ergänzen mit demselben Upload-/Migrations-Hinweis.

**Betroffene Dateien:**

- `context/current-data.md`

---

## Verbindungen & Abhängigkeiten

### Dateien, die diesen Bereich referenzieren

- `context/current-data.md` — dokumentiert den gesamten `intern/`-Bereich inkl. Deployment-Workflow, DB-Zugangsdaten-Hinweisen und bereits behobenen Bugs; muss um den neuen Stand ergänzt werden (Schritt 9)
- `reference/Getmatic_website/index.html` — der Footer-Link zu `intern/login.php` ist unverändert, keine Anpassung nötig

### Nötige Updates für Konsistenz

- `intern/sql/schema.sql` UND die neue Migrationsdatei müssen inhaltlich zum selben Endzustand führen (einmal für Neuinstallationen, einmal für die bestehende Live-DB) — beim Review beide nebeneinander gegenlesen.

### Auswirkungen auf bestehende Workflows

- Bestehende Dateien und ihr Lösch-/Download-Verhalten sind unverändert (weiterhin `folder_id IS NULL`, funktional identisch zum bisherigen Zustand).
- Der bisherige Upload-Ablauf (Chunking, Retry-Logik, Fortschrittsanzeige) bleibt unverändert; es wird lediglich ein zusätzliches, optionales Feld (`folder_id`) durchgereicht.

---

## Validierungs-Checkliste

- [ ] `php -l` läuft fehlerfrei für alle neuen/geänderten `.php`-Dateien
- [ ] `intern_folders`-Tabelle sowie die neuen `folder_id`-Spalten sind in `schema.sql` UND in der separaten Migrationsdatei konsistent
- [ ] Jeder neue POST-Endpunkt prüft `require_login()`, `csrf_check()` und nutzt ausschließlich prepared statements
- [ ] Ordner anlegen funktioniert auf Root-Ebene und in einem Unterordner
- [ ] Doppelter Ordnername im selben Ordner wird mit verständlicher Fehlermeldung abgelehnt
- [ ] Ordner löschen funktioniert nur bei leeren Ordnern; bei nicht-leeren Ordnern erscheint eine klare Fehlermeldung statt eines stillen Fehlschlags
- [ ] Datei-Upload landet im aktuell geöffneten Ordner (nicht immer auf Root-Ebene)
- [ ] Alle vor der Migration hochgeladenen Dateien sind weiterhin auf der Root-Ebene sichtbar (kein Datenverlust)
- [ ] Breadcrumb zeigt den korrekten Pfad von der Wurzel bis zum aktuellen Ordner
- [ ] `context/current-data.md` enthält den neuen Eintrag inkl. Deployment-Reihenfolge

## Nach dem Live-Deployment manuell zu testen (für den User)

1. `intern/sql/migration_2026-09-16_ordner.sql` in phpMyAdmin ausführen
2. Betroffene Dateien per FTP hochladen (Überschreiben, nicht Resume — bekannte 1blu/FileZilla-Falle)
3. Einloggen, prüfen: alle bisherigen Dateien sind weiterhin auf der Wurzelebene sichtbar
4. Neuen Ordner anlegen, hineinnavigieren, eine Testdatei hochladen — landet sie im richtigen Ordner?
5. Versuchen, einen Ordner mit Inhalt zu löschen — sollte mit Fehlermeldung abgelehnt werden
6. Inhalt entfernen, Ordner erneut löschen — sollte jetzt funktionieren
7. Breadcrumb-Navigation in mehreren Ebenen testen

---

## Erfolgskriterien

Die Implementierung ist abgeschlossen, wenn:

1. Mitarbeiter im internen Bereich Ordner anlegen und navigieren können.
2. Leere Ordner gelöscht werden können, nicht-leere Ordner mit klarer Fehlermeldung geschützt sind.
3. Hochgeladene Dateien im jeweils aktuell geöffneten Ordner landen.
4. Alle bereits vorhandenen Dateien nach der Migration ohne Datenverlust weiterhin sichtbar sind.
5. Alle Sicherheitsmuster des bestehenden Codes (CSRF, prepared statements, `require_login()`) konsequent auf die neuen Endpunkte angewendet wurden.

---

## Notizen

- Verschieben von Dateien/Ordnern zwischen Ordnern sowie rekursives Löschen sind bewusst nicht Teil dieses Plans (siehe „Betrachtete Alternativen") — bei Bedarf als eigener Folge-Plan.
- Der interne Bereich hat einen dokumentiert abweichenden Deployment-Workflow (SQL zuerst manuell, dann Dateien) — dieser Plan folgt demselben Muster, das beim ursprünglichen Rollout etabliert wurde.

---

## Implementierungsnotizen

**Implementiert:** 2026-09-16

### Zusammenfassung

Alle 9 Schritte des Plans wurden umgesetzt: `schema.sql` und die neue Migrationsdatei `sql/migration_2026-09-16_ordner.sql` enthalten die `intern_folders`-Tabelle sowie die `folder_id`-Spalten in `intern_files`/`intern_upload_sessions`; `create_folder.php` und `delete_folder.php` sind neu erstellt (POST, CSRF, prepared statements, „nur leere Ordner löschbar", NULL-sicherer Duplikat-Check per `<=>`); `index.php` navigiert per `?folder_id=`, zeigt Breadcrumb, Unterordner-Liste, „Neuer Ordner"-Formular und Flash-Meldungen; der komplette Upload-Pfad (`index.php` Hidden-Input → `chunked-upload.js` → `upload_chunk.php` → `intern_upload_sessions.folder_id` → `upload_finalize.php` → `intern_files.folder_id`) reicht den aktuell geöffneten Ordner durch; `portal.css` hat neue Klassen für Breadcrumb und Ordnerliste; `context/current-data.md` ist aktualisiert.

### Abweichungen vom Plan

- Keine inhaltlichen Abweichungen. Die im Plan vorgeschlagenen Code-Snippets wurden nahezu unverändert übernommen.

### Aufgetretene Probleme

- **Kein PHP-CLI auf diesem Rechner installiert** — `php -l` (Schritt 8) konnte nicht ausgeführt werden. Ersatzweise wurden alle neuen/geänderten PHP-Dateien vollständig erneut gelesen und manuell auf Syntax (Klammer-/Anführungszeichen-Balance, korrekte PHP-Tags) sowie Konsistenz mit den bestehenden Sicherheitsmustern (CSRF, `require_login()`, prepared statements) geprüft. Ein echter funktionaler Test (PHP + MySQL) ist erst nach dem Live-Deployment durch den User möglich — siehe „Nach dem Live-Deployment manuell zu testen" oben.
- Kein Zugriff auf die Live-Datenbank/den Live-Server — Migration und Datei-Upload muss der User gemäß der dokumentierten Deployment-Reihenfolge selbst durchführen (unverändert gegenüber dem bereits etablierten Workflow für diesen Bereich).
