<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Lobby - <? echo $casino_name ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="utilities/css/lobby_new.css?v=<?= mt_rand() ?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="utilities/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="utilities/js/functions.js?v=<?= mt_rand() ?>"></script>
</head>

<body>

<?php
if(isset($_GET["logout"])){
    session_start();
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params["path"],
            $params["domain"],
            $params["secure"],
            $params["httponly"]
        );
    }
    session_destroy();
    ?>
    <script>location.href = '/';</script>
    <?php
    exit;
}
?>

<div class="top_bar">
    <div class="top_bar_left">
        <div class="top_logo">&#9824; <?= htmlspecialchars($casino_name) ?></div>
    </div>
    <div class="top_bar_right">
        <div class="player_info">
            <span class="player_label">Player</span>
            <span class="player_name"><?= htmlspecialchars($_player->vars["account"]) ?></span>
        </div>

        <div class="balance_box">
            <? if($_company->vars["allow_freeplay"]){ ?>
                <? if(!$_using_free_play){ ?>
                    <span class="selected_balance head_btns">
                        <span class="balance_label">Real Balance</span>
                        <span class="balance_amount"><?= $_currency_symbols[$balance["currency"]] . number_format($balance["real"],2) ?></span>
                    </span>
                    <span class="head_btns">
                        <button class="blue-btn" onclick="location.href = 'utilities/process/actions/use_free_play.php';">Use Freeplay: <?= $_currency_symbols[$balance["currency"]] . number_format($balance["free"],2) ?></button>
                    </span>
                <? }else{ ?>
                    <span class="selected_balance head_btns">
                        <span class="balance_label">Free Play</span>
                        <span class="balance_amount"><?= $_currency_symbols[$balance["currency"]] . number_format($balance["free"],2) ?></span>
                    </span>
                    <span class="head_btns">
                        <button class="blue-btn" onclick="location.href = 'utilities/process/actions/use_free_play.php';">Use Real Balance: <?= $_currency_symbols[$balance["currency"]] . number_format($balance["real"],2) ?></button>
                    </span>
                <? } ?>
            <? }else{ ?>
                <span class="balance_label">Balance</span>
                <span class="balance_amount"><?= $_currency_symbols[$balance["currency"]] . number_format($balance["real"],2) ?></span>
            <? } ?>
        </div>

        <? if($cashier_link != ""){ ?>
            <span class="head_btns">
                <button class="red-btn" onclick="window.open('<?= $cashier_link ?>', '_blank');">CASHIER</button>
            </span>
        <? } ?>

        <span class="head_btns">
            <button class="blue-btn" onclick="window.open('history.php', '_blank');">History</button>
        </span>

        <span class="head_btns">
            <a href="?logout=1" class="logout-btn"
               style="background:#c00;color:#fff;padding:10px 15px;border-radius:5px;text-decoration:none;font-weight:bold;">
                LOGOUT
            </a>
        </span>
    </div>
</div>

<?
    $link = CASINO_BASE_URL .
        "/index.php?cid=" . $_company->vars["id"] .
        "&cps=" . $_company->vars["password"] .
        "&token=" . urlencode($player_token) .
        "&cshcd=" . $cashier_code .
        "&account=" . $_player->vars["account"] .
        "&game=";

    $categories = get_all_categories();
?>

<div class="games">

    <? foreach($categories as $category){ ?>
        <? $games = $_player->get_allowed_games($category->vars["id"]); ?>

        <div class="cat_grp">

            <div class="cat_title">
                <span class="cat_title_text"><?= $category->vars["name"] ?></span>
            </div>

            <div class="games_grid">
                <? foreach($games as $game){ ?>
                <div class="game_card"
                     onclick="location.href='<?= $link . $game->vars["id"] ?>';">

                    <div class="game_image"
                         style="background-image: url('utilities/games/<?= $game->vars["path"] ?>/imgs/lobby.png?v4')">
                    </div>

                    <div class="game_name_static"><?= $game->vars["name"] ?></div>

                    <div class="game_overlay">
                        <h3><?= $game->vars["name"] ?></h3>
                        <p class="game_limits">
                            Min: <?= $_currency_symbols[$balance["currency"]] . round($game->vars["min_amount"], 2) ?>
                            &nbsp;&middot;&nbsp;
                            Max: <?= $_currency_symbols[$balance["currency"]] . round($game->vars["max_amount"], 2) ?>
                        </p>
                        <button class="play_btn">Play Now</button>
                    </div>

                </div>
                <? } ?>
            </div>

        </div>

    <? } ?>

</div>

<script src="utilities/js/lobby.js?v=<?= mt_rand() ?>"></script>
</body>
</html>
