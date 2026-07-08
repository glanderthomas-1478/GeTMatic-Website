<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

enforce_https();
require_login();

header('Content-Type: application/json; charset=utf-8');

function json_fail(string $message, int $code = 400): void
{
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $message]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_fail('Methode nicht erlaubt.', 405);
}

if (!csrf_check($_POST['csrf_token'] ?? null)) {
    json_fail('Sitzung abgelaufen. Bitte Seite neu laden.', 403);
}

$token = (string) ($_POST['token'] ?? '');

if ($token === '' || !preg_match('/^[a-f0-9]{48}$/', $token)) {
    json_fail('Ungueltige Anfrage.');
}

$db = get_db();

$stmt = $db->prepare(
    'SELECT * FROM intern_upload_sessions
     WHERE token = :token AND created_by = :user_id AND status = "in_progress"
     LIMIT 1'
);
$stmt->execute(['token' => $token, 'user_id' => current_user_id()]);
$session = $stmt->fetch();

if (!$session) {
    json_fail('Upload-Session nicht gefunden oder abgelaufen.', 404);
}

$tmpPartPath = __DIR__ . '/tmp_uploads/' . $token . '.part';

function fail_session(PDO $db, string $token, string $tmpPartPath, string $message): void
{
    if (is_file($tmpPartPath)) {
        unlink($tmpPartPath);
    }
    $stmt = $db->prepare('UPDATE intern_upload_sessions SET status = "failed" WHERE token = :token');
    $stmt->execute(['token' => $token]);
    json_fail($message);
}

if (!is_file($tmpPartPath)) {
    fail_session($db, $token, $tmpPartPath, 'Hochgeladene Datei nicht gefunden.');
}

$actualSize = filesize($tmpPartPath);
$expectedSize = (int) $session['total_size_bytes'];

if ($actualSize !== $expectedSize) {
    fail_session($db, $token, $tmpPartPath, 'Unvollstaendiger Upload (Groesse stimmt nicht ueberein). Bitte erneut hochladen.');
}

if (!enough_disk_space($expectedSize)) {
    fail_session($db, $token, $tmpPartPath, 'Nicht genuegend freier Speicherplatz auf dem Server.');
}

$originalFilename = (string) $session['original_filename'];
$validation = validate_file($tmpPartPath, $originalFilename);

if (!$validation['ok']) {
    fail_session($db, $token, $tmpPartPath, $validation['error']);
}

$storedFilename = generate_stored_filename($validation['ext']);
$destination = __DIR__ . '/files/' . $storedFilename;

if (!rename($tmpPartPath, $destination)) {
    fail_session($db, $token, $tmpPartPath, 'Datei konnte nicht abgelegt werden.');
}

$stmt = $db->prepare(
    'INSERT INTO intern_files
        (original_filename, stored_filename, mime_type, filesize_bytes, uploaded_by)
     VALUES (:original, :stored, :mime, :size, :user_id)'
);
$stmt->execute([
    'original' => $originalFilename,
    'stored' => $storedFilename,
    'mime' => $validation['mime'],
    'size' => $actualSize,
    'user_id' => current_user_id(),
]);

$stmt = $db->prepare('UPDATE intern_upload_sessions SET status = "completed" WHERE token = :token');
$stmt->execute(['token' => $token]);

echo json_encode(['ok' => true, 'file_id' => (int) $db->lastInsertId()]);
