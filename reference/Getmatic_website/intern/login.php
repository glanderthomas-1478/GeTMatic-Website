<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

enforce_https();
start_secure_session();

if (isset($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf_token'] ?? null)) {
        $error = 'Sitzung abgelaufen, bitte erneut versuchen.';
    } else {
        $username = (string) ($_POST['username'] ?? '');
        $password = (string) ($_POST['password'] ?? '');

        if (attempt_login($username, $password)) {
            header('Location: index.php');
            exit;
        }

        $error = 'Benutzername oder Passwort falsch.';
    }
}

$token = csrf_token();
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex, nofollow" />
  <title>Login – Interner Bereich | GeTMatic</title>
  <link rel="icon" type="image/png" href="../getmatic_logo_transparent.png" />
  <link rel="stylesheet" href="../style.css" />
  <link rel="stylesheet" href="assets/portal.css" />
</head>
<body class="portal-body">

  <main class="portal-center">
    <div class="portal-login-box">
      <a href="../index.html" class="portal-logo-link" aria-label="Zur getmatic-Startseite">
        <img src="../getmatic_logo_transparent.png" alt="GeTMatic" class="portal-logo" />
      </a>
      <h1 class="portal-title">Interner Bereich</h1>

      <?php if ($error): ?>
        <p class="portal-flash-error"><?= htmlspecialchars($error) ?></p>
      <?php endif; ?>

      <form method="post" class="portal-form" autocomplete="off">
        <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($token) ?>" />

        <label for="username">Benutzername</label>
        <input type="text" id="username" name="username" required autofocus />

        <label for="password">Passwort</label>
        <input type="password" id="password" name="password" required />

        <button type="submit" class="portal-btn">Anmelden</button>
      </form>
    </div>
  </main>

</body>
</html>
