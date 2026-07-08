<?php
/**
 * Datei-Validierung, sichere Namensgenerierung und Speicherplatz-Pruefung
 * fuer den internen Bereich.
 */

const CHUNK_SIZE_BYTES = 8 * 1024 * 1024; // 8 MB

const ALLOWED_EXTENSIONS = [
    'pdf', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx',
    'jpg', 'jpeg', 'png', 'gif',
    'zip', 'csv', 'txt',
    'dwg', 'step', 'stp',
    'mp4',
];

// Erlaubte MIME-Typen je Endung (Whitelist, mehrere moegliche Werte je
// Endung, da Browser/Betriebssysteme nicht immer denselben MIME-Typ melden).
const ALLOWED_MIME_TYPES = [
    'pdf'  => ['application/pdf'],
    'doc'  => ['application/msword'],
    'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'application/zip'],
    'xls'  => ['application/vnd.ms-excel'],
    'xlsx' => ['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'application/zip'],
    'ppt'  => ['application/vnd.ms-powerpoint'],
    'pptx' => ['application/vnd.openxmlformats-officedocument.presentationml.presentation', 'application/zip'],
    'jpg'  => ['image/jpeg'],
    'jpeg' => ['image/jpeg'],
    'png'  => ['image/png'],
    'gif'  => ['image/gif'],
    'zip'  => ['application/zip', 'application/x-zip-compressed'],
    'csv'  => ['text/csv', 'text/plain'],
    'txt'  => ['text/plain'],
    // CAD-/Binaerformate ohne verlaesslich erkennbaren MIME-Typ ueber finfo:
    // Endungs-Whitelist + .htaccess-Sperre im Ablageordner bleiben hier die
    // primaere Schutzschicht statt eines MIME-Abgleichs.
    'dwg'  => ['application/octet-stream', 'image/vnd.dwg', 'application/acad'],
    'step' => ['application/octet-stream', 'text/plain'],
    'stp'  => ['application/octet-stream', 'text/plain'],
    'mp4'  => ['video/mp4'],
];

/**
 * Prueft eine bereits vollstaendig auf der Platte liegende Datei
 * (z.B. das Ergebnis eines zusammengesetzten Chunk-Uploads).
 *
 * @return array{ok: bool, error: ?string, ext: ?string, mime: ?string}
 */
function validate_file(string $tmpPath, string $originalFilename): array
{
    $ext = strtolower(pathinfo($originalFilename, PATHINFO_EXTENSION));

    if ($ext === '' || !in_array($ext, ALLOWED_EXTENSIONS, true)) {
        return ['ok' => false, 'error' => 'Dieser Dateityp ist nicht erlaubt.', 'ext' => null, 'mime' => null];
    }

    if (!is_file($tmpPath)) {
        return ['ok' => false, 'error' => 'Datei nicht gefunden.', 'ext' => null, 'mime' => null];
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $detectedMime = finfo_file($finfo, $tmpPath);
    // Kein finfo_close() mehr: seit PHP 8.5 deprecated, finfo-Objekte werden
    // automatisch freigegeben.

    $allowedMimes = ALLOWED_MIME_TYPES[$ext] ?? [];

    if ($detectedMime === false || !in_array($detectedMime, $allowedMimes, true)) {
        return [
            'ok' => false,
            'error' => 'Der Dateiinhalt passt nicht zur Dateiendung (erkannt: ' . htmlspecialchars((string) $detectedMime) . ').',
            'ext' => $ext,
            'mime' => $detectedMime ?: null,
        ];
    }

    return ['ok' => true, 'error' => null, 'ext' => $ext, 'mime' => $detectedMime];
}

function generate_stored_filename(string $ext): string
{
    return bin2hex(random_bytes(16)) . '.' . preg_replace('/[^a-z0-9]/', '', strtolower($ext));
}

function generate_upload_token(): string
{
    return bin2hex(random_bytes(24));
}

/**
 * Prueft, ob nach Ablegen von $neededBytes weiterer Daten noch der in
 * config.php definierte Sicherheitspuffer frei bleibt.
 */
function enough_disk_space(int $neededBytes): bool
{
    $free = disk_free_space(__DIR__ . '/../files');

    if ($free === false) {
        // Konnte nicht ermittelt werden -> im Zweifel nicht blockieren,
        // aber Vorfall loggen.
        error_log('intern/functions.php: disk_free_space() konnte nicht ermittelt werden.');
        return true;
    }

    $buffer = defined('DISK_SPACE_BUFFER_BYTES') ? DISK_SPACE_BUFFER_BYTES : (500 * 1024 * 1024);

    return ($free - $neededBytes) > $buffer;
}

function human_filesize(int $bytes): string
{
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];
    $i = 0;
    $size = (float) $bytes;

    while ($size >= 1024 && $i < count($units) - 1) {
        $size /= 1024;
        $i++;
    }

    return round($size, $i === 0 ? 0 : 1) . ' ' . $units[$i];
}
