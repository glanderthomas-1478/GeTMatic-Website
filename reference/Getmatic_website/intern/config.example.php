<?php
/**
 * Vorlage fuer die Datenbank-Konfiguration des internen Bereichs.
 *
 * WICHTIG:
 * 1. Diese Datei nach config.php kopieren.
 * 2. In config.php die Platzhalter unten durch die echten 1blu-Datenbank-
 *    Zugangsdaten ersetzen.
 * 3. config.php NIEMALS committen oder per FTP in ein oeffentlich einsehbares
 *    Backup hochladen - sie steht bewusst in .gitignore.
 */

define('DB_HOST', 'localhost');
define('DB_NAME', 'DEIN_DATENBANK_NAME');
define('DB_USER', 'DEIN_DATENBANK_USER');
define('DB_PASS', 'DEIN_DATENBANK_PASSWORT');

// Sicherheitspuffer in Bytes, der auf dem Webspace immer frei bleiben soll
// (siehe includes/functions.php -> enough_disk_space())
define('DISK_SPACE_BUFFER_BYTES', 500 * 1024 * 1024); // 500 MB

// Nur fuer sql/add_user.php(.example) benoetigt: langer Zufallswert als
// Zugriffsschutz fuer das Konto-Anlege-Skript. Eigenen Wert einsetzen,
// z.B. per Passwort-Generator, und nach Nutzung des Skripts aendern/entfernen.
define('ADD_USER_SETUP_KEY', 'AENDERE_MICH_ZU_EINEM_LANGEN_ZUFALLSWERT');
