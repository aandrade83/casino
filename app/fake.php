<?php

// valores fake
$cid = 1; // tu company fake_dgs
$cps = "KDSI4H72AZ13R";
$token = "FAKE_TOKEN_123";
$account = "BETOWITEST";

// URL del casino
$casino_url = "/?cid=$cid&cps=$cps&token=$token&account=$account";

?>

<!DOCTYPE html>
<html>
<head>
    <title>Fake Sportsbook</title>
    <style>
        body {
            margin: 0;
            background: #0a0a0a;
            color: white;
            font-family: Arial;
        }

        .header {
            display: flex;
            justify-content: space-between;
            padding: 10px 20px;
            background: #111;
            border-bottom: 2px solid #0f0;
        }

        .menu button {
            margin-right: 10px;
            padding: 10px 20px;
            background: #222;
            color: white;
            border: none;
            cursor: pointer;
        }

        .menu button.active {
            background: #0f0;
            color: black;
        }

        .content {
            width: 100%;
            height: calc(100vh - 60px);
        }

        iframe {
            width: 100%;
            height: 100%;
            border: none;
        }
    </style>
</head>
<body>

<div class="header">
    <div class="logo">🎰 FAKE SPORTSBOOK</div>
    <div class="menu">
        <button onclick="loadCasino()">CASINO</button>
    </div>
    <div>Hi demo_player | Balance: $1000</div>
</div>

<div class="content">
    <iframe id="frame"></iframe>
</div>

<script>
function loadCasino(){
    document.getElementById("frame").src = "<?php echo $casino_url; ?>";
}
</script>

</body>
</html>