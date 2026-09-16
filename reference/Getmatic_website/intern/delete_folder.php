<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

enforce_https();
require_login();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    die('Methode nicht erlaubt.');
}

if (!csrf_check($_POST['csrf_token'] ?? null)) {
    http_response_code(403);
    die('Sitzung abgelaufen. Bitte Seite neu laden.');
}

$id = (int) ($_POST['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    die('Ungueltige Anfrage.');
}

function redirect_with(?int $folderId, string $param, $value = 1): void
{
    $query = $folderId !== null ? ['folder_id' => $folderId] : [];
    $query[$param] = $value;
    header('Location: index.php?' . http_build_query($query));
    exit;
}

$db = get_db();

$stmt = $db->prepare('SELECT * FROM intern_folders WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$folder = $stmt->fetch();

if (!$folder) {
    // Ordner existiert schon nicht mehr - einfach zur Wurzel zurueck.
    redirect_with(null, 'folder_deleted', 1);
}

$parentId = $folder['parent_id'] !== null ? (int) $folder['parent_id'] : null;

$subfolderCount = $db->prepare('SELECT COUNT(*) AS cnt FROM intern_folders WHERE parent_id = :id');
$subfolderCount->execute(['id' => $id]);
$hasSubfolders = (int) $subfolderCount->fetch()['cnt'] > 0;

$fileCount = $db->prepare('SELECT COUNT(*) AS cnt FROM intern_files WHERE folder_id = :id');
$fileCount->execute(['id' => $id]);
$hasFiles = (int) $fileCount->fetch()['cnt'] > 0;

if ($hasSubfolders || $hasFiles) {
    redirect_with($id, 'folder_error', 'Ordner ist nicht leer und kann nicht gelöscht werden.');
}

$del = $db->prepare('DELETE FROM intern_folders WHERE id = :id');
$del->execute(['id' => $id]);

redirect_with($parentId, 'folder_deleted', 1);
