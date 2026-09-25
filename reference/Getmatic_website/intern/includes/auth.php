<?php
/**
 * Session-Handling, Login/Logout, Rate-Limiting und CSRF-Schutz
 * fuer den internen Bereich.
 */

require_once __DIR__ . '/db.php';

const SESSION_IDLE_TIMEOUT_SECONDS = 30 * 60; // 30 Minuten
const LOGIN_MAX_ATTEMPTS = 5;
const LOGIN_LOCKOUT_WINDOW_MINUTES = 15;
// Datenschutz (DSGVO Art. 5 Abs. 1 lit. e): Login-Versuche inkl. IP nach dieser Frist loeschen
const LOGIN_ATTEMPTS_RETENTION_DAYS = 30;

/**
 * HTTPS wird primaer per .htaccess (Server-Ebene) erzwungen, siehe
 * intern/.htaccess - das ist zuverlaessiger als eine PHP-Pruefung, die je
 * nach Proxy-/Server-Setup $_SERVER['HTTPS'] uneinheitlich sieht und dann
 * in eine Redirect-Schleife laufen kann (beobachtet auf 1blu-Hosting).
 *
 * Diese Funktion bleibt als Aufruf-Stelle in allen PHP-Dateien bestehen,
 * macht aber bewusst keinen eigenen Redirect mehr - nur ein Log-Eintrag,
 * falls sie doch einmal ueber eine unverschluesselte Verbindung erreicht wird
 * (sollte durch die .htaccess-Regel praktisch nie vorkommen).
 */
function enforce_https(): void
{
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && stripos($_SERVER['HTTP_X_FORWARDED_PROTO'], 'https') !== false);

    if (!$isHttps) {
        error_log('intern: Seite ueber HTTP statt HTTPS aufgerufen (.htaccess-Redirect sollte das eigentlich verhindern): ' . ($_SERVER['REQUEST_URI'] ?? ''));
    }
}

function start_secure_session(): void
{
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => true,
        'httponly' => true,
        'samesite' => 'Strict',
    ]);

    session_start();
}

function current_ip(): string
{
    return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
}

function is_username_locked_out(string $username): bool
{
    $db = get_db();
    $stmt = $db->prepare(
        'SELECT COUNT(*) AS fails FROM intern_login_attempts
         WHERE username = :username
           AND success = 0
           AND attempted_at >= (NOW() - INTERVAL :window MINUTE)'
    );
    $stmt->execute([
        'username' => $username,
        'window' => LOGIN_LOCKOUT_WINDOW_MINUTES,
    ]);
    $row = $stmt->fetch();

    return (int) $row['fails'] >= LOGIN_MAX_ATTEMPTS;
}

function record_login_attempt(string $username, bool $success): void
{
    $db = get_db();
    $stmt = $db->prepare(
        'INSERT INTO intern_login_attempts (username, ip_address, success)
         VALUES (:username, :ip, :success)'
    );
    $stmt->execute([
        'username' => $username,
        'ip' => current_ip(),
        'success' => $success ? 1 : 0,
    ]);

    // Alte Eintraege bei jedem Login-Versuch mit aufraeumen (kein Cronjob noetig)
    $db->prepare(
        'DELETE FROM intern_login_attempts
         WHERE attempted_at < (NOW() - INTERVAL :days DAY)'
    )->execute(['days' => LOGIN_ATTEMPTS_RETENTION_DAYS]);
}

function attempt_login(string $username, string $password): bool
{
    $username = trim($username);

    if ($username === '' || $password === '') {
        return false;
    }

    if (is_username_locked_out($username)) {
        return false;
    }

    $db = get_db();
    $stmt = $db->prepare(
        'SELECT id, username, password_hash, display_name
         FROM intern_users
         WHERE username = :username AND is_active = 1
         LIMIT 1'
    );
    $stmt->execute(['username' => $username]);
    $user = $stmt->fetch();

    $ok = $user !== false && password_verify($password, $user['password_hash']);

    record_login_attempt($username, $ok);

    if (!$ok) {
        return false;
    }

    start_secure_session();
    session_regenerate_id(true);

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['display_name'] = $user['display_name'];
    $_SESSION['last_activity'] = time();

    return true;
}

function require_login(): void
{
    start_secure_session();

    $loggedIn = isset($_SESSION['user_id']);
    $notExpired = $loggedIn
        && isset($_SESSION['last_activity'])
        && (time() - $_SESSION['last_activity']) <= SESSION_IDLE_TIMEOUT_SECONDS;

    if (!$loggedIn || !$notExpired) {
        logout();
        header('Location: login.php');
        exit;
    }

    $_SESSION['last_activity'] = time();
}

function logout(): void
{
    start_secure_session();
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'],
            $params['secure'],
            $params['httponly']
        );
    }

    session_destroy();
}

function csrf_token(): string
{
    start_secure_session();

    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function csrf_check(?string $token): bool
{
    start_secure_session();

    return isset($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

function current_user_id(): int
{
    return (int) ($_SESSION['user_id'] ?? 0);
}
