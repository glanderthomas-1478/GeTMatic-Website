<?php
/**
 * GeTMatic – Kontaktformular-Versand
 *
 * Nimmt die Anfrage aus dem Kontaktbereich der Startseite (index.html#kontakt) entgegen, prüft sie und schickt sie per
 * PHP mail() an EMPFAENGER. Es wird nichts gespeichert und kein Cookie gesetzt.
 *
 * Einstellungen:
 *   EMPFAENGER  – Postfach, in dem die Anfragen ankommen
 *   ABSENDER    – muss ein bei 1blu eingerichtetes Postfach/Alias der Domain sein,
 *                 sonst landen die Mails im Spam oder werden abgelehnt
 */

const EMPFAENGER = 'info@getmatic.de';
const ABSENDER   = 'info@getmatic.de';
const ABSENDER_NAME = 'GeTMatic Website';
const MIN_SEKUNDEN = 3;        // schneller ausgefüllt = Bot
const MAX_PRO_STUNDE = 5;      // Anfragen je IP und Stunde
const ANLIEGEN = [
    'sps'        => 'SPS & Steuerung',
    'antriebe'   => 'Antriebstechnik',
    'visu'       => 'Visualisierung / HMI',
    'retrofit'   => 'Retrofit & Legacy-Integration',
    'getraenke'  => 'Getränkeverbundkarton',
    'visioncontrol' => 'Qualitätskontrolle / VisionControl',
    'medizin'    => 'Medizin & Sterilisation',
    'schnittstelle' => 'Schnittstelle',
    'docucontrol'=> 'DocuControl',
    'sonstiges'  => 'Sonstiges',
];

date_default_timezone_set('Europe/Berlin');
ini_set('display_errors', '0');   // PHP-Warnungen dürfen die JSON-Antwort nicht verunreinigen
header('X-Content-Type-Options: nosniff');
header('Cache-Control: no-store');

$wantsJson = isset($_SERVER['HTTP_ACCEPT']) && str_contains($_SERVER['HTTP_ACCEPT'], 'application/json');

function antwort(bool $ok, string $code, array $felder = []): never
{
    global $wantsJson;
    if ($wantsJson) {
        http_response_code($ok ? 200 : ($code === 'methode' ? 405 : ($code === 'limit' ? 429 : 422)));
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['ok' => $ok, 'code' => $code, 'felder' => $felder], JSON_UNESCAPED_UNICODE);
        exit;
    }
    // Fallback ohne JavaScript: zurück zum Formular mit Statusparameter
    header('Location: index.html?status=' . urlencode($ok ? 'ok' : $code) . '#kontakt', true, 303);
    exit;
}

if (($_SERVER['REQUEST_METHOD'] ?? '') !== 'POST') {
    antwort(false, 'methode');
}

function feld(string $name, int $max): string
{
    $v = $_POST[$name] ?? '';
    if (!is_string($v)) return '';
    $v = trim(str_replace("\0", '', $v));
    return mb_substr($v, 0, $max, 'UTF-8');
}

// --- Spam-Schutz -----------------------------------------------------------
// 1) Honeypot: für Menschen unsichtbares Feld, Bots füllen es aus
if (feld('website', 200) !== '') {
    antwort(true, 'ok');            // Bot nichts merken lassen
}
// 2) Zeitfalle: Zeitstempel wird per JavaScript beim Laden gesetzt
$t = feld('t', 20);
if ($t !== '' && ctype_digit($t)) {
    $alter = time() - (int)$t;
    if ($alter < MIN_SEKUNDEN) antwort(true, 'ok');
}
// 3) Einfache Begrenzung je IP (pseudonymisierter Hash im Temp-Ordner, abgelaufene Dateien werden hier gelöscht)
foreach (glob(sys_get_temp_dir() . '/gm_kontakt_*') ?: [] as $alt) {
    if (time() - (int)@filemtime($alt) > 3600) @unlink($alt);
}
$ipHash = hash('sha256', ($_SERVER['REMOTE_ADDR'] ?? '') . date('Y-m-d-H') . __FILE__);
$limitDatei = sys_get_temp_dir() . '/gm_kontakt_' . substr($ipHash, 0, 24);
$anzahl = is_file($limitDatei) ? (int)file_get_contents($limitDatei) : 0;
if ($anzahl >= MAX_PRO_STUNDE) {
    antwort(false, 'limit');
}

// --- Felder prüfen ---------------------------------------------------------
$name      = feld('name', 100);
$firma     = feld('firma', 120);
$email     = feld('email', 160);
$telefon   = feld('telefon', 40);
$anliegen  = feld('anliegen', 20);
$nachricht = feld('nachricht', 5000);
$dsgvo     = feld('datenschutz', 5);

$fehler = [];
if (mb_strlen($name) < 2)                                   $fehler[] = 'name';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))             $fehler[] = 'email';
if ($telefon !== '' && !preg_match('/^[0-9 +()\/.\-]{5,40}$/', $telefon)) $fehler[] = 'telefon';
if (!array_key_exists($anliegen, ANLIEGEN))                 $anliegen = 'sonstiges';
if (mb_strlen($nachricht) < 10)                             $fehler[] = 'nachricht';
if ($dsgvo !== '1')                                         $fehler[] = 'datenschutz';
// Zeilenumbrüche in Kopfzeilen-Werten verhindern (Header-Injection)
foreach ([$name, $email, $firma, $telefon] as $v) {
    if (preg_match('/[\r\n]/', $v)) { $fehler[] = 'ungueltig'; break; }
}
if ($fehler) {
    antwort(false, 'eingabe', $fehler);
}

// --- Mail zusammenbauen ----------------------------------------------------
$thema = ANLIEGEN[$anliegen];
$betreff = 'Anfrage über getmatic.de: ' . $thema . ' – ' . $name . ($firma !== '' ? " ({$firma})" : '');

$text  = "Neue Anfrage über das Kontaktformular auf getmatic.de\n";
$text .= str_repeat('=', 54) . "\n\n";
$text .= "Name:     {$name}\n";
$text .= 'Firma:    ' . ($firma !== '' ? $firma : '–') . "\n";
$text .= "E-Mail:   {$email}\n";
$text .= 'Telefon:  ' . ($telefon !== '' ? $telefon : '–') . "\n";
$text .= "Anliegen: {$thema}\n\n";
$text .= "Nachricht:\n" . str_repeat('-', 54) . "\n{$nachricht}\n" . str_repeat('-', 54) . "\n\n";
$text .= 'Gesendet am ' . date('d.m.Y \u\m H:i') . " Uhr. Einwilligung Datenschutz: ja.\n";
$text .= "Antworten Sie einfach auf diese Mail, um dem Interessenten zu schreiben.\n";

$headers = [
    'From: ' . mb_encode_mimeheader(ABSENDER_NAME, 'UTF-8') . ' <' . ABSENDER . '>',
    'Reply-To: ' . $email,
    'MIME-Version: 1.0',
    'Content-Type: text/plain; charset=UTF-8',
    'Content-Transfer-Encoding: base64',   // lange Zeilen ohne Umbruch bleiben so unversehrt
    'X-Mailer: GeTMatic-Kontaktformular',
];

$gesendet = mail(
    EMPFAENGER,
    mb_encode_mimeheader($betreff, 'UTF-8'),
    chunk_split(base64_encode($text)),
    implode("\r\n", $headers),
    '-f' . ABSENDER
);

if (!$gesendet) {
    error_log('kontakt.php: mail() fehlgeschlagen');
    antwort(false, 'versand');
}

@file_put_contents($limitDatei, (string)($anzahl + 1), LOCK_EX);
antwort(true, 'ok');
