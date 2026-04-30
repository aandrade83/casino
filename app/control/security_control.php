<?php

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once($_SERVER['DOCUMENT_ROOT'] . "/utilities/includes.php");

$result = [
    "error" => 0,
    "msg" => ""
];

$_logged = false;
$_player = null;
$_company = null;

// Standalone mode: only player session is required.
// Company is derived from the player record so the game works
// without having to go through control/index.php every time.
if (isset($_SESSION["player"])) {

    $player_id = $_SESSION["player"];
    $_player   = get_player($player_id);

    if ($_player) {

        // Use company from session if present, otherwise read from player record
        $company_id = $_SESSION["company"] ?? $_player->vars["company"];
        $_company   = get_comany($company_id);

        // Keep session consistent for subsequent requests
        $_SESSION["company"] = $company_id;

        if ($_company) {

            $_logged = true;
            $_using_free_play = $_player->vars["using_free_play"] ?? false;

            $_balance = [
                "real"     => floatval($_player->vars["balance_real"]),
                "free"     => floatval($_player->vars["balance_free"]),
                "currency" => "USD"
            ];

        } else {
            $result["error"] = 200;
            $result["msg"]   = "Company not found";
        }

    } else {
        $result["error"] = 200;
        $result["msg"]   = "Player not found";
    }

} else {
    $result["error"] = 100;
    $result["msg"]   = "Session expired";
}