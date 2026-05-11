<?php

include($_SERVER['DOCUMENT_ROOT']."/control/security_control.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/control/modules/access/api_local.php');

if(!$_logged){
    echo "Session expired";
    exit;
}

// player info
$player_id = $_SESSION['player'];
$player = get_player($player_id);

// balance
$api = new _local_api();
$res = $api->get_player_balance($player_id);

$balance_real = 0;

if($api->done){
    $balance_real = $res["real"];
}

// 🔥 GAMES (hardcode inicial, luego dinámico)
$games = [
    [
        "id" => 1,
        "name" => "Baccarat",
        "path" => "baccarat"
    ],
    [
        "id" => 2,
        "name" => "Blackjack",
        "path" => "blackjack"
    ]
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Casino Lobby</title>
    <link href="./css/lobby.css?v=<?= mt_rand() ?>" rel="stylesheet" type="text/css" />
</head>

<body>

<div class="top_bar">
    <div class="top_bar_left">
        <div class="top_logo">&#9824; CASINO</div>
    </div>
    <div class="top_bar_right">
        <div class="player_info">
            <span class="player_label">Player</span>
            <span class="player_name"><?= htmlspecialchars($player->vars["account"]) ?></span>
        </div>
        <div class="balance_box">
            <span class="balance_label">Balance</span>
            <span class="balance_amount">$<?= number_format($balance_real, 2) ?></span>
        </div>
    </div>
</div>

<div class="games">

<?
    $categories = get_all_categories();
    $currency_symbol = $_currency_symbols[$_balance["currency"]] ?? "$";
    ?>

    <? foreach($categories as $category){ ?>
        <?
            // Show all company games directly — agent-based filtering bypassed
            // until agent administration is restructured.
            $games = get_all_company_games($_company->vars["id"], true, $category->vars["id"]);
        ?>

        <div class="cat_grp">

            <div class="cat_title">
                <span class="cat_title_text"><?= $category->vars["name"] ?></span>
            </div>

            <div class="games_grid">
            <? foreach($games as $game){ ?>
            <div class="game_card"
                 onclick="window.location.href='/control/modules/access/index.php?game=<?= $game->vars["id"] ?>'">

                <div class="game_image"
                     style="background-image: url('../../../utilities/games/<?= $game->vars["path"] ?>/imgs/lobby.png?v4')">
                </div>

                <div class="game_name_static"><?= $game->vars["name"] ?></div>

                <div class="game_overlay">
                    <h3><?= $game->vars["name"] ?></h3>
                    <p class="game_limits">
                        Min: <?= $currency_symbol . round($game->vars["min_amount"], 2) ?>
                        &nbsp;&middot;&nbsp;
                        Max: <?= $currency_symbol . round($game->vars["max_amount"], 2) ?>
                    </p>
                    <button class="play_btn">Play Now</button>
                </div>

            </div>
            <? } ?>
            </div>

        </div>

    <? } ?>

<?/* php foreach($games as $game): ?>
   <?
    <div class="game_box"
         //onclick="location.href='/utilities/games/<?= $game["path"] ?>/ui.php?gid=<?= $game["id"] ?>'">
         onclick="location.href='/control/modules/access/index.php?game=<?= $game["id"] ?>'" ?>

   <div class="game_box"
     onclick="window.location.href='/control/modules/access/index.php?game=<?= $game["id"] ?>'">

        <img src="/utilities/games/<?= $game["path"] ?>/imgs/lobby.png">

        <h3><?= $game["name"] ?></h3>

    </div>

<?php endforeach; */?>

</div>

<script src="./js/lobby.js?v=<?= mt_rand() ?>"></script>
</body>
</html>
