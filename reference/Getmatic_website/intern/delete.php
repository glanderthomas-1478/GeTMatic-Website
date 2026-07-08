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

$db = get_db();
$stmt = $db->prepare('SELECT * FROM intern_files WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$file = $stmt->fetch();

if ($file) {
    $filesDir = realpath(__DIR__ . '/files');
    $path = realpath($filesDir . '/' . $file['stored_filename']);

    if ($path !== false && strpos($path, $filesDir) === 0 && is_file($path)) {
        unlink($path);
    }

    $del = $db->prepare('DELETE FROM intern_files WHERE id = :id');
    $del->execute(['id' => $id]);
}

header('Location: index.php?deleted=1');
