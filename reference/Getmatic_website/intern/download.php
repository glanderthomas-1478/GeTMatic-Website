<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/includes/functions.php';

enforce_https();
require_login();

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    http_response_code(400);
    die('Ungueltige Anfrage.');
}

$db = get_db();
$stmt = $db->prepare('SELECT * FROM intern_files WHERE id = :id LIMIT 1');
$stmt->execute(['id' => $id]);
$file = $stmt->fetch();

if (!$file) {
    http_response_code(404);
    die('Datei nicht gefunden.');
}

$filesDir = realpath(__DIR__ . '/files');
$path = realpath($filesDir . '/' . $file['stored_filename']);

// Defensive Pfadpruefung: das aufgeloeste Ziel muss tatsaechlich innerhalb
// von files/ liegen, auch wenn stored_filename selbst kontrolliert generiert wird.
if ($path === false || strpos($path, $filesDir) !== 0 || !is_file($path)) {
    http_response_code(404);
    die('Datei nicht gefunden.');
}

$size = filesize($path);
$mime = $file['mime_type'] ?: 'application/octet-stream';
$downloadName = str_replace(['"', "\r", "\n"], '', $file['original_filename']);

set_time_limit(0);
while (ob_get_level() > 0) {
    ob_end_clean();
}

header('Content-Type: ' . $mime);
header('Content-Disposition: attachment; filename="' . $downloadName . '"');
header('Accept-Ranges: bytes');
header('X-Content-Type-Options: nosniff');

$start = 0;
$end = $size - 1;
$isRangeRequest = false;

if (!empty($_SERVER['HTTP_RANGE']) && preg_match('/bytes=(\d*)-(\d*)/', $_SERVER['HTTP_RANGE'], $m)) {
    $isRangeRequest = true;

    if ($m[1] !== '') {
        $start = (int) $m[1];
    }
    if ($m[2] !== '') {
        $end = (int) $m[2];
    }

    if ($start > $end || $start >= $size) {
        header('Content-Range: bytes */' . $size);
        http_response_code(416);
        exit;
    }
}

$length = $end - $start + 1;

if ($isRangeRequest) {
    http_response_code(206);
    header('Content-Range: bytes ' . $start . '-' . $end . '/' . $size);
} else {
    http_response_code(200);
}

header('Content-Length: ' . $length);

$fp = fopen($path, 'rb');
if ($fp === false) {
    http_response_code(500);
    die('Datei konnte nicht geoeffnet werden.');
}

fseek($fp, $start);

$bufferSize = 1024 * 1024; // 1 MB
$bytesLeft = $length;

while ($bytesLeft > 0 && !feof($fp)) {
    $readSize = min($bufferSize, $bytesLeft);
    echo fread($fp, $readSize);
    $bytesLeft -= $readSize;
    flush();
}

fclose($fp);
