<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/auth.php';

logout();

if (!headers_sent()) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="de">
<head>
  <meta charset="utf-8" />
  <meta name="robots" content="noindex, nofollow" />
  <meta http-equiv="refresh" content="0; url=login.php" />
  <title>Abgemeldet</title>
</head>
<body>
  <p>Du wurdest abgemeldet. <a href="login.php">Weiter zum Login</a></p>
</body>
</html>
