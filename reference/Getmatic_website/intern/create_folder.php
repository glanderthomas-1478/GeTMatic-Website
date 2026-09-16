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

$name = trim((string) ($_POST['name'] ?? ''));
$parentId = (($_POST['parent_id'] ?? '') !== '') ? (int) $_POST['parent_id'] : null;

function redirect_with(?int $folderId, string $param, $value = 1): void
{
    $query = $folderId !== null ? ['folder_id' => $folderId] : [];
    $query[$param] = $value;
    header('Location: index.php?' . http_build_query($query));
    exit;
}

if ($name === '') {
    redirect_with($parentId, 'folder_error', 'Bitte einen Ordnernamen angeben.');
}

if (mb_strlen($name) > 100) {
    redirect_with($parentId, 'folder_error', 'Ordnername ist zu lang (max. 100 Zeichen).');
}

$db = get_db();

if ($parentId !== null) {
    $stmt = $db->prepare('SELECT id FROM intern_folders WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $parentId]);
    if (!$stmt->fetch()) {
        redirect_with(null, 'folder_error', 'Übergeordneter Ordner nicht gefunden.');
    }
}

// NULL-sicherer Vergleich (<=>), da parent_id auf der Wurzelebene NULL ist
// und ein gewoehnliches "=" NULL-Werte nicht wie erwartet vergleicht.
$dupStmt = $db->prepare(
    'SELECT COUNT(*) AS cnt FROM intern_folders WHERE name = :name AND parent_id <=> :parent_id'
);
$dupStmt->execute(['name' => $name, 'parent_id' => $parentId]);
if ((int) $dupStmt->fetch()['cnt'] > 0) {
    redirect_with($parentId, 'folder_error', 'Ein Ordner mit diesem Namen existiert hier bereits.');
}

$insert = $db->prepare(
    'INSERT INTO intern_folders (name, parent_id, created_by) VALUES (:name, :parent_id, :user_id)'
);
$insert->execute([
    'name' => $name,
    'parent_id' => $parentId,
    'user_id' => current_user_id(),
]);

redirect_with($parentId, 'folder_created', 1);
