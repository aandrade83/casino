<?php

include($_SERVER['DOCUMENT_ROOT']."/control/security_control.php");
//require_once($_SERVER['DOCUMENT_ROOT'].'/control/modules/access/api_local.php');

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
<html>
<head>
    <title>Casino Lobby</title>

    <link href="./css/lobby.css?test=<? echo mt_rand() ?>" rel="stylesheet" type="text/css" />

    <style>
        body { color:#fff; font-family:Arial; }
        .top_bar { padding:15px; background:#222; }
        .game_box {
            display:inline-block;
            margin:15px;
            padding:10px;
            background:#333;
            cursor:pointer;
            text-align:center;
            width:180px;
        }
        .game_box img {
            width:100%;
        }
    </style>
</head>

<body>

<div class="top_bar">
    <strong>Player: <?= htmlspecialchars($player->vars["account"]) ?></strong>
    <br>
    Balance: $<?= number_format($balance_real,2) ?>
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

        	<div class="cat_title"><? echo $category->vars["name"] ?></div>

			<? foreach($games as $game){ ?>
            <div class="game_box" onclick="window.location.href='/control/modules/access/index.php?game=<?= $game->vars["id"] ?>'">
                <img src="../../../utilities/games/<? echo $game->vars["path"] ?>/imgs/lobby.png?v4" />
                <h2><? echo $game->vars["name"] ?></h2>
                Min: <? echo $currency_symbol . round($game->vars["min_amount"],2) ?> Max: <? echo $currency_symbol . round($game->vars["max_amount"],2) ?>
            </div>
            <? } ?>

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

</body>
</html>