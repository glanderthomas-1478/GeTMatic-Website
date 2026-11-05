<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

enforce_https();
require_login();

header('Content-Type: application/json; charset=utf-8');

function json_fail(string $message, int $code = 400, array $extra = []): void
{
    http_response_code($code);
    echo json_encode(['ok' => false, 'error' => $message] + $extra);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_fail('Methode nicht erlaubt.', 405);
}

if (!csrf_check($_POST['csrf_token'] ?? null)) {
    json_fail('Sitzung abgelaufen. Bitte Seite neu laden.', 403);
}

$token = (string) ($_POST['token'] ?? '');
$chunkIndex = (int) ($_POST['chunk_index'] ?? -1);
$expectedOffset = isset($_POST['expected_offset']) ? (int) $_POST['expected_offset'] : null;

if ($token === '' || !preg_match('/^[a-f0-9]{48}$/', $token) || $chunkIndex < 0) {
    json_fail('Ungueltige Anfrage.');
}

if (!isset($_FILES['chunk']) || $_FILES['chunk']['error'] !== UPLOAD_ERR_OK) {
    json_fail('Chunk-Upload fehlgeschlagen.');
}

$db = get_db();
$tmpPartPath = __DIR__ . '/tmp_uploads/' . $token . '.part';

if ($chunkIndex === 0) {
    // Erster Chunk: neue Upload-Session anlegen.
    $originalFilename = (string) ($_POST['filename'] ?? '');
    $totalSize = (int) ($_POST['total_size'] ?? 0);

    if ($originalFilename === '' || $totalSize <= 0) {
        json_fail('Fehlende Datei-Metadaten.');
    }

    $ext = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));
    if (!in_array($ext, ALLOWED_EXTENSIONS, true)) {
        json_fail('Dieser Dateityp ist nicht erlaubt.');
    }

    if (!enough_disk_space($totalSize)) {
        json_fail('Nicht genuegend freier Speicherplatz auf dem Server.', 507);
    }

    $folderId = (($_POST['folder_id'] ?? '') !== '') ? (int) $_POST['folder_id'] : null;

    if ($folderId !== null) {
        $checkStmt = $db->prepare('SELECT id FROM intern_folders WHERE id = :id LIMIT 1');
        $checkStmt->execute(['id' => $folderId]);
        if (!$checkStmt->fetch()) {
            $folderId = null; // ungueltige Ordner-ID -> Wurzelebene statt Abbruch
        }
    }

    // Wiederholter erster Chunk (Antwort ging verloren): Session besteht schon.
    $existsStmt = $db->prepare('SELECT id FROM intern_upload_sessions WHERE token = :token LIMIT 1');
    $existsStmt->execute(['token' => $token]);

    if (!$existsStmt->fetch()) {
        // Falls ein alter, verwaister .part mit demselben Token existiert (sollte
        // wegen zufaelligem Token praktisch nie vorkommen) - sauber neu beginnen.
        if (is_file($tmpPartPath)) {
            unlink($tmpPartPath);
        }

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
    }
}

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

$currentReceived = (int) $session['received_bytes'];
$totalSize = (int) $session['total_size_bytes'];

// Massgeblich ist, was wirklich auf der Platte liegt: Ist der .part kuerzer als
// die Session meldet, geht es ab seinem tatsaechlichen Ende weiter.
clearstatcache(true, $tmpPartPath);
$partSize = is_file($tmpPartPath) ? (int) filesize($tmpPartPath) : 0;
if ($partSize < $currentReceived) {
    $currentReceived = $partSize;
    $stmt = $db->prepare('UPDATE intern_upload_sessions SET received_bytes = :received WHERE token = :token');
    $stmt->execute(['received' => $currentReceived, 'token' => $token]);
}

if ($expectedOffset !== null && $expectedOffset !== $currentReceived) {
    // Client und Server sind nicht synchron (z.B. nach Verbindungsabbruch) -
    // aktuellen Stand zurueckmelden, damit der Client dort weitermacht.
    json_fail('Offset stimmt nicht ueberein.', 409, ['received_bytes' => $currentReceived]);
}

$chunkPath = $_FILES['chunk']['tmp_name'];
$chunkSize = (int) $_FILES['chunk']['size'];

if ($chunkSize > CHUNK_SIZE_BYTES || $currentReceived + $chunkSize > $totalSize) {
    json_fail('Chunk ueberschreitet die angemeldete Dateigroesse.', 413);
}

$in = fopen($chunkPath, 'rb');
$out = fopen($tmpPartPath, 'c+b');

if (!$in || !$out) {
    json_fail('Konnte Chunk nicht speichern.', 500);
}

// Reste eines abgebrochenen Schreibvorgangs entfernen, dann exakt am Offset anhaengen.
ftruncate($out, $currentReceived);
fseek($out, $currentReceived);

$written = stream_copy_to_stream($in, $out);
fflush($out);

if ($written !== $chunkSize) {
    ftruncate($out, $currentReceived);
    fclose($in);
    fclose($out);
    json_fail('Chunk konnte nicht vollstaendig gespeichert werden.', 507);
}

fclose($in);
fclose($out);

$newReceived = $currentReceived + $chunkSize;

$stmt = $db->prepare(
    'UPDATE intern_upload_sessions SET received_bytes = :received WHERE token = :token'
);
$stmt->execute(['received' => $newReceived, 'token' => $token]);

echo json_encode(['ok' => true, 'received_bytes' => $newReceived]);
