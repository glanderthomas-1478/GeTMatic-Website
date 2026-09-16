<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

enforce_https();
require_login();

$db = get_db();

// -----------------------------------------------------------------
// Ordner-Navigation: aktueller Ordner + Breadcrumb von der Wurzel aus
// -----------------------------------------------------------------
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

$breadcrumb = [];
$walkId = $currentFolderId;
while ($walkId !== null) {
    $stmt = $db->prepare('SELECT id, name, parent_id FROM intern_folders WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $walkId]);
    $folder = $stmt->fetch();
    if (!$folder) {
        break;
    }
    array_unshift($breadcrumb, $folder);
    $walkId = $folder['parent_id'] !== null ? (int) $folder['parent_id'] : null;
}

// -----------------------------------------------------------------
// Schritt 15: abgebrochene Chunk-Uploads aufraeumen (aelter als 24h)
// -----------------------------------------------------------------
$stale = $db->query(
    'SELECT token FROM intern_upload_sessions
     WHERE status = "in_progress" AND created_at < (NOW() - INTERVAL 24 HOUR)'
)->fetchAll();

if ($stale) {
    $cleanupStmt = $db->prepare('UPDATE intern_upload_sessions SET status = "failed" WHERE token = :token');
    foreach ($stale as $row) {
        $partPath = __DIR__ . '/tmp_uploads/' . $row['token'] . '.part';
        if (is_file($partPath)) {
            unlink($partPath);
        }
        $cleanupStmt->execute(['token' => $row['token']]);
    }
}

// -----------------------------------------------------------------
// Unterordner + Dateiliste des aktuellen Ordners (gemeinsamer Pool,
// alle eingeloggten Nutzer sehen alles)
// -----------------------------------------------------------------
$subfoldersStmt = $db->prepare(
    'SELECT id, name FROM intern_folders WHERE parent_id <=> :folder_id ORDER BY name ASC'
);
$subfoldersStmt->execute(['folder_id' => $currentFolderId]);
$subfolders = $subfoldersStmt->fetchAll();

$filesStmt = $db->prepare(
    'SELECT f.id, f.original_filename, f.filesize_bytes, f.uploaded_at, u.display_name
     FROM intern_files f
     JOIN intern_users u ON u.id = f.uploaded_by
     WHERE f.folder_id <=> :folder_id
     ORDER BY f.uploaded_at DESC'
);
$filesStmt->execute(['folder_id' => $currentFolderId]);
$files = $filesStmt->fetchAll();

$token = csrf_token();
$deleted = isset($_GET['deleted']);
$folderCreated = isset($_GET['folder_created']);
$folderDeleted = isset($_GET['folder_deleted']);
$folderError = isset($_GET['folder_error']) ? (string) $_GET['folder_error'] : null;
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex, nofollow" />
  <title>Dateien – Interner Bereich | GeTMatic</title>
  <link rel="icon" type="image/png" href="../getmatic_logo_transparent.png" />
  <link rel="stylesheet" href="../style.css" />
  <link rel="stylesheet" href="assets/portal.css" />
</head>
<body class="portal-body">

  <header class="portal-header">
    <img src="../getmatic_logo_transparent.png" alt="GeTMatic" class="portal-logo-small" />
    <div class="portal-header-user">
      Angemeldet als <strong><?= htmlspecialchars($_SESSION['display_name']) ?></strong>
      &middot; <a href="logout.php">Abmelden</a>
    </div>
  </header>

  <main class="portal-main">
    <h1 class="portal-title">Dateien</h1>

    <nav class="portal-breadcrumb" aria-label="Ordnerpfad">
      <a href="index.php">Wurzelverzeichnis</a>
      <?php foreach ($breadcrumb as $crumb): ?>
        &raquo; <a href="index.php?folder_id=<?= (int) $crumb['id'] ?>"><?= htmlspecialchars($crumb['name']) ?></a>
      <?php endforeach; ?>
    </nav>

    <?php if ($deleted): ?>
      <p class="portal-flash-success">Datei gelöscht.</p>
    <?php endif; ?>
    <?php if ($folderCreated): ?>
      <p class="portal-flash-success">Ordner angelegt.</p>
    <?php endif; ?>
    <?php if ($folderDeleted): ?>
      <p class="portal-flash-success">Ordner gelöscht.</p>
    <?php endif; ?>
    <?php if ($folderError): ?>
      <p class="portal-flash-error"><?= htmlspecialchars($folderError) ?></p>
    <?php endif; ?>

    <section class="portal-card">
      <h2>Neue Datei hochladen</h2>
      <form id="upload-form" class="portal-form" enctype="multipart/form-data">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>" />
        <input type="hidden" name="folder_id" id="upload-folder-id" value="<?= $currentFolderId !== null ? (int) $currentFolderId : '' ?>" />
        <input type="file" id="upload-file-input" name="file" required />
        <button type="submit" id="upload-submit-btn" class="portal-btn">Hochladen</button>
      </form>
      <div id="upload-progress-wrap" class="portal-progress-wrap">
        <div class="portal-progress">
          <div id="upload-progress-bar" class="portal-progress-bar">0%</div>
        </div>
        <p id="upload-status" class="portal-status"></p>
      </div>
      <p class="portal-hint">Erlaubte Dateitypen: <?= htmlspecialchars(implode(', ', ALLOWED_EXTENSIONS)) ?> — bis 20&nbsp;GB pro Datei.</p>
    </section>

    <section class="portal-card">
      <h2>Neuer Ordner</h2>
      <form method="post" action="create_folder.php" class="portal-form">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>" />
        <input type="hidden" name="parent_id" value="<?= $currentFolderId !== null ? (int) $currentFolderId : '' ?>" />
        <input type="text" name="name" maxlength="100" placeholder="Ordnername" required />
        <button type="submit" class="portal-btn">Ordner anlegen</button>
      </form>
    </section>

    <section class="portal-card">
      <h2>Inhalt dieses Ordners</h2>

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

      <?php if (!$files && !$subfolders): ?>
        <p class="portal-hint">Dieser Ordner ist leer.</p>
      <?php elseif (!$files): ?>
        <p class="portal-hint">Keine Dateien in diesem Ordner.</p>
      <?php else: ?>
        <div class="portal-table-wrap">
          <table class="portal-table">
            <thead>
              <tr>
                <th>Dateiname</th>
                <th>Größe</th>
                <th>Hochgeladen von</th>
                <th>Datum</th>
                <th></th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($files as $file): ?>
                <tr>
                  <td><?= htmlspecialchars($file['original_filename']) ?></td>
                  <td><?= htmlspecialchars(human_filesize((int) $file['filesize_bytes'])) ?></td>
                  <td><?= htmlspecialchars($file['display_name']) ?></td>
                  <td><?= htmlspecialchars(date('d.m.Y H:i', strtotime($file['uploaded_at']))) ?></td>
                  <td><a href="download.php?id=<?= (int) $file['id'] ?>" class="portal-link">Download</a></td>
                  <td>
                    <form method="post" action="delete.php" class="portal-inline-form"
                          onsubmit="return confirm('Datei „<?= htmlspecialchars(addslashes($file['original_filename'])) ?>“ wirklich löschen?');">
                      <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>" />
                      <input type="hidden" name="id" value="<?= (int) $file['id'] ?>" />
                      <button type="submit" class="portal-link portal-link-danger">Löschen</button>
                    </form>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <script src="assets/chunked-upload.js"></script>
</body>
</html>
