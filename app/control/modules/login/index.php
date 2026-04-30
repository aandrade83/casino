<?php
if(session_status() === PHP_SESSION_NONE) session_start();
// Build an absolute URL path to this module's folder, works regardless of which URL includes this file
$_module_path = '/' . ltrim(str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__), '/');
$_company_url = htmlspecialchars($_SESSION['company_url'] ?? '', ENT_QUOTES);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="<?= $_module_path ?>/styles/login.css">
</head>
<body>

<div class="card">
    <div class="logo"><span>Casino <em>Pro</em></span></div>

    <div class="field">
        <label>Player</label>
        <input type="text" id="player" placeholder="Username" autocomplete="off">
    </div>
    <div class="field">
        <label>Password</label>
        <input type="password" id="password" placeholder="••••••••">
    </div>

    <button id="btn-login" onclick="handleLogin()">Login</button>

    <div id="status"></div>
</div>

<script>
    const COMPANY_URL  = "<?= $_company_url ?>";
    const MODULE_PATH  = "<?= $_module_path ?>";
</script>
<script src="<?= $_module_path ?>/js/login.js"></script>

</body>
</html>
