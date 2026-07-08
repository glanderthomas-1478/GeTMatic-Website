<?php
/**
 * PDO-MySQL-Verbindung fuer den internen Bereich.
 */

require_once __DIR__ . '/../config.php';

function get_db(): PDO
{
    static $pdo = null;

    if ($pdo !== null) {
        return $pdo;
    }

    $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
    } catch (PDOException $e) {
        // Keine echten Fehlerdetails/Zugangsdaten an den Browser durchreichen.
        error_log('intern/db.php: Datenbankverbindung fehlgeschlagen: ' . $e->getMessage());
        http_response_code(500);
        die('Interner Fehler. Bitte spaeter erneut versuchen.');
    }

    return $pdo;
}
