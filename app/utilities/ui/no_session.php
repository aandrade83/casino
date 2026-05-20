<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0" />
<title>Access Restricted</title>
<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Segoe UI', Arial, Helvetica, sans-serif;
        background: radial-gradient(ellipse at top, #1a1a2e 0%, #0a0a0f 60%, #050508 100%);
        color: #e0e0e0;
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
        overflow: hidden;
        position: relative;
    }

    body::before {
        content: '';
        position: fixed;
        inset: 0;
        background:
            radial-gradient(circle at 20% 30%, rgba(212, 175, 55, 0.08) 0%, transparent 40%),
            radial-gradient(circle at 80% 70%, rgba(212, 175, 55, 0.06) 0%, transparent 40%);
        pointer-events: none;
        z-index: 0;
    }

    .card {
        position: relative;
        z-index: 1;
        max-width: 560px;
        width: 100%;
        padding: 60px 50px;
        background: rgba(10, 10, 18, 0.92);
        border: 1px solid rgba(212, 175, 55, 0.35);
        border-radius: 16px;
        box-shadow:
            0 20px 60px rgba(0, 0, 0, 0.6),
            0 0 80px rgba(212, 175, 55, 0.08) inset;
        text-align: center;
        backdrop-filter: blur(14px);
        -webkit-backdrop-filter: blur(14px);
    }

    .icon {
        font-size: 64px;
        color: #d4af37;
        margin-bottom: 24px;
        text-shadow: 0 0 30px rgba(212, 175, 55, 0.5);
    }

    h1 {
        font-size: 32px;
        font-weight: 600;
        margin-bottom: 18px;
        background: linear-gradient(135deg, #f4d57a 0%, #d4af37 50%, #b8941f 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        letter-spacing: 0.5px;
    }

    .subtitle {
        font-size: 17px;
        line-height: 1.6;
        color: #b8b8c0;
        margin-bottom: 14px;
    }

    .message {
        font-size: 15px;
        line-height: 1.6;
        color: #888892;
        margin-bottom: 36px;
    }

    .divider {
        width: 60px;
        height: 2px;
        background: linear-gradient(90deg, transparent, #d4af37, transparent);
        margin: 0 auto 32px;
    }

    .footer {
        margin-top: 28px;
        font-size: 12px;
        color: #5a5a64;
        letter-spacing: 0.5px;
    }

    @media (max-width: 600px) {
        .card { padding: 44px 28px; }
        h1 { font-size: 26px; }
        .icon { font-size: 52px; }
    }
</style>
</head>
<body>
    <div class="card">
        <div class="icon">&#9824;</div>
        <h1>Access Restricted</h1>
        <div class="divider"></div>
        <p class="subtitle">A valid session is required to enter the casino.</p>
        <p class="message">Please access the casino through your provider&rsquo;s platform. If you believe this is an error, contact support.</p>
        <div class="footer"><?= isset($_company) ? htmlspecialchars($_company->vars["name"]) : "Casino" ?></div>
    </div>
<?php if(!empty($_SESSION['error_reason']) || !empty($_SESSION['error_url'])): ?>
<script>
console.group('%c[Casino] Session Error', 'color:#d4af37;font-weight:bold;');
console.log('Reason :', <?= json_encode($_SESSION['error_reason'] ?? 'unknown') ?>);
console.log('URL    :', <?= json_encode($_SESSION['error_url'] ?? 'unknown') ?>);
console.groupEnd();
</script>
<?php
    unset($_SESSION['error_reason'], $_SESSION['error_url']);
endif;
?>
</body>
</html>
