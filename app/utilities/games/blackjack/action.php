<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json');
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 0);

$result = array();
$result["error"] = 0;

include($_SERVER['DOCUMENT_ROOT'] . "/utilities/process/security.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/control/modules/wallet.php');

$game_id = param("gid");

if ($_logged) {

    include("class.php");

    $action = param("action");
    $logic  = new blackjack_sh($_player->vars["id"]);
    $game   = $_player->get_allowed_game($game_id);

    if (!is_null($game) && $game->vars["path"] == "blackjack") {

        switch ($action) {

            // =========================
            // START — load existing session if any
            // =========================
            case "start":
                $session = get_blackjack_session_by_player($_player->vars["id"]);
                if (!is_null($session)) {
                    $logic->load_data($session);

                    $result["has_started_game"]      = 1;
                    $result["player_hand"]           = implode(",", $logic->player_hand);
                    $result["player_hand2"]          = implode(",", $logic->player_hand2);
                    $result["dealer_hand"]           = $logic->dealer_hand[1];
                    $result["status"]                = $logic->game_status;
                    $result["status2"]               = $logic->game_status2;
                    $result["bet_amount"]            = $logic->bet_amount;
                    $result["bet_amount2"]           = $logic->bet_amount2;
                    $result["player_hand_value"]     = $logic->get_hand_value();
                    $result["player_hand_value2"]    = $logic->get_hand_value("player2");
                    $result["can_split"]             = $logic->can_split();
                    $result["splited"]               = $logic->splited;
                    $result["finished2"]             = $session->vars["finished2"];

                    if ($logic->using_pf) {
                        $result["pf"]["lxhs"] = $logic->pf_data["xhs"];
                        $result["pf"]["lpnr"] = $logic->pf_data["pnr"];
                    }
                } else {
                    $result["has_started_game"] = 0;
                }
            break;

            // =========================
            // GAME DATA — used after split hand switch
            // =========================
            case "game_data":
                $session = get_blackjack_session_by_player($_player->vars["id"], false);
                if (!is_null($session)) {
                    $logic->load_data($session);

                    $bal_res = wallet_balance($_player->vars["id"]);
                    $result["balance"]           = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
                    $result["dealer_hidden_card"] = $logic->dealer_hand[0];
                    $result["dealer_hand_value"]  = $logic->get_hand_value("dealer");
                    $result["win_amount"]         = $logic->win_amount;
                    $result["dealer_hand"]        = implode(",", $logic->dealer_hand);
                    $result["win_amount2"]        = $logic->win_amount2;
                    $result["splited"]            = $logic->splited;
                    $result["bet_amount"]         = $logic->bet_amount;
                    $result["bet_amount2"]        = $logic->bet_amount2;
                    $result["status"]             = $logic->game_status;
                    $result["finished"]           = $session->vars["finished"];
                } else {
                    $result["error"] = 1000;
                    $result["msg"]   = "Session not found";
                }
            break;

            // =========================
            // BALANCE
            // =========================
            case "balance":
                $bal_res = wallet_balance($_player->vars["id"]);
                if ($bal_res["success"]) {
                    $result["balance"] = floatval($bal_res["data"]["real"]);
                } else {
                    $result["error"] = 900;
                    $result["msg"]   = "Balance error: " . $bal_res["error"];
                }
            break;

            // =========================
            // FOLD (SURRENDER)
            // =========================
            case "fold":
                $session = get_blackjack_session_by_player($_player->vars["id"]);
                if (!is_null($session)) {
                    $logic->load_data($session);
                    if ($session->vars["game_status"] == "dealed") {

                        $win_amount = $logic->fold();

                        if ($win_amount > 0) {
                            $win_res = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
                            $new_balance = $win_res["success"] ? $win_res["balance"] : 0;
                        } else {
                            $bal_res     = wallet_balance($_player->vars["id"]);
                            $new_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
                        }

                        $logic->store_settle_log($win_amount);

                        $result["balance"]            = $new_balance;
                        $result["win_amount"]         = $win_amount;
                        $result["finished"]           = $logic->game_finished;
                        $result["dealer_hidden_card"] = $logic->dealer_hand[0];
                        $result["dealer_hand_value"]  = $logic->get_hand_value("dealer");
                        $result["status"]             = $logic->game_status;
                        $result["splited"]            = $logic->splited;
                        $result["win_amount2"]        = $logic->win_amount2;
                        $result["status2"]            = $logic->game_status2;
                        $result["bet_amount2"]        = $logic->bet_amount2;
                        $result["dealer_hand"]        = implode(",", $logic->dealer_hand);
                        $result["second_hand"]        = $logic->move_to_second_hand();

                        if ($logic->game_finished && !$result["second_hand"]) {
                            if ($_company->vars["prov_fair"]) {
                                $result["pf"]["lxhs"] = $logic->pf_data["xhs"];
                                $result["pf"]["lsc1"] = $logic->pf_data["xz1"];
                                $result["pf"]["lsc2"] = $logic->pf_data["xz2"];
                                $result["pf"]["lxnr"] = $logic->pf_data["xnr"];
                                $result["pf"]["lpnr"] = $logic->pf_data["pnr"];
                                $result["pf"]["lpos"] = $logic->pf_data["pos"];
                                $result["pf"]["nxnr"] = generate_server_seed("bljk", 0, 100000000);
                            }
                        }

                    } else {
                        $result["error"] = 1100;
                        $result["msg"]   = "Cant execute this action";
                    }
                } else {
                    $result["error"] = 1000;
                    $result["msg"]   = "Session not found";
                }
            break;

            // =========================
            // HIT
            // =========================
            case "hit":
                $session = get_blackjack_session_by_player($_player->vars["id"]);
                if (!is_null($session)) {
                    $logic->load_data($session);
                    if ($session->vars["game_status"] == "dealed" || $session->vars["game_status"] == "hitted") {

                        $new_card = $logic->hit();
                        $result["new_card"]          = $new_card;
                        $result["player_hand_value"] = $logic->get_hand_value();
                        $result["status"]            = $logic->game_status;
                        $result["finished"]          = $logic->game_finished;

                        $new_balance = 0;
                        switch ($logic->game_status) {
                            case 'push':
                                $win_amount  = $logic->bet_amount;
                                $win_res     = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
                                $new_balance = $win_res["success"] ? $win_res["balance"] : 0;
                                $logic->store_settle_log($win_amount);
                            break;
                            case 'dealer_bust':
                            case 'win':
                                $win_amount  = $_using_free_play ? $logic->win_amount : $logic->win_amount + $logic->bet_amount;
                                $win_res     = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
                                $new_balance = $win_res["success"] ? $win_res["balance"] : 0;
                                $logic->store_settle_log($win_amount);
                            break;
                            case 'busted':
                                $bal_res     = wallet_balance($_player->vars["id"]);
                                $new_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
                            break;
                        }

                        if ($logic->game_finished) {
                            $result["dealer_hidden_card"] = $logic->dealer_hand[0];
                            $result["dealer_hand_value"]  = $logic->get_hand_value("dealer");
                            $result["balance"]            = $new_balance;
                            $result["win_amount"]         = $logic->win_amount;
                            $result["dealer_hand"]        = implode(",", $logic->dealer_hand);
                            $result["splited"]            = $logic->splited;
                            $result["win_amount2"]        = $logic->win_amount2;
                            $result["status2"]            = $logic->game_status2;
                            $result["bet_amount2"]        = $logic->bet_amount2;
                        }

                        $result["second_hand"] = $logic->move_to_second_hand(true);

                        if ($logic->game_finished && !$result["second_hand"]) {
                            if ($_company->vars["prov_fair"]) {
                                $result["pf"]["lxhs"] = $logic->pf_data["xhs"];
                                $result["pf"]["lsc1"] = $logic->pf_data["xz1"];
                                $result["pf"]["lsc2"] = $logic->pf_data["xz2"];
                                $result["pf"]["lxnr"] = $logic->pf_data["xnr"];
                                $result["pf"]["lpnr"] = $logic->pf_data["pnr"];
                                $result["pf"]["lpos"] = $logic->pf_data["pos"];
                                $result["pf"]["nxnr"] = generate_server_seed("bljk", 0, 100000000);
                            }
                        }

                    } else {
                        $result["error"] = 1100;
                        $result["msg"]   = "Cant execute this action";
                    }
                } else {
                    $result["error"] = 1000;
                    $result["msg"]   = "Session not found";
                }
            break;

            // =========================
            // DOUBLE
            // =========================
            case "double":
                $session = get_blackjack_session_by_player($_player->vars["id"]);
                if (!is_null($session)) {
                    $logic->load_data($session);
                    if ($session->vars["game_status"] == "dealed") {

                        $bal_res = wallet_balance($_player->vars["id"]);
                        $current_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;

                        if ($session->vars["bet_amount"] <= $current_balance) {

                            $bet_res = wallet_bet($_player->vars["id"], $session->vars["bet_amount"], "bet", $game->vars["id"]);
                            if ($bet_res["success"]) {

                                $logic->store_settle_log($session->vars["bet_amount"] * -1);
                                $new_card = $logic->double();

                                $result["new_card"]          = $new_card;
                                $result["player_hand_value"] = $logic->get_hand_value();
                                $result["status"]            = $logic->game_status;
                                $result["finished"]          = $logic->game_finished;
                                $result["bet_amount"]        = $logic->bet_amount;

                                $new_balance = 0;
                                switch ($logic->game_status) {
                                    case 'push':
                                        $win_amount  = $logic->bet_amount;
                                        $win_res     = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
                                        $new_balance = $win_res["success"] ? $win_res["balance"] : 0;
                                        $logic->store_settle_log($win_amount);
                                    break;
                                    case 'dealer_bust':
                                    case 'win':
                                        $win_amount  = $_using_free_play ? $logic->win_amount : $logic->win_amount + $logic->bet_amount;
                                        $win_res     = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
                                        $new_balance = $win_res["success"] ? $win_res["balance"] : 0;
                                        $logic->store_settle_log($win_amount);
                                    break;
                                    case 'lose':
                                    case 'busted':
                                        $bal_res     = wallet_balance($_player->vars["id"]);
                                        $new_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
                                    break;
                                }

                                if ($logic->game_finished) {
                                    $result["dealer_hidden_card"] = $logic->dealer_hand[0];
                                    $result["dealer_hand_value"]  = $logic->get_hand_value("dealer");
                                    $result["balance"]            = $new_balance;
                                    $result["win_amount"]         = $logic->win_amount;
                                    $result["dealer_hand"]        = implode(",", $logic->dealer_hand);
                                    $result["splited"]            = $logic->splited;
                                    $result["win_amount2"]        = $logic->win_amount2;
                                    $result["status2"]            = $logic->game_status2;
                                    $result["bet_amount2"]        = $logic->bet_amount2;
                                }

                                $result["second_hand"] = $logic->move_to_second_hand();

                                if ($logic->game_finished && !$result["second_hand"]) {
                                    if ($_company->vars["prov_fair"]) {
                                        $result["pf"]["lxhs"] = $logic->pf_data["xhs"];
                                        $result["pf"]["lsc1"] = $logic->pf_data["xz1"];
                                        $result["pf"]["lsc2"] = $logic->pf_data["xz2"];
                                        $result["pf"]["lxnr"] = $logic->pf_data["xnr"];
                                        $result["pf"]["lpnr"] = $logic->pf_data["pnr"];
                                        $result["pf"]["lpos"] = $logic->pf_data["pos"];
                                        $result["pf"]["nxnr"] = generate_server_seed("bljk", 0, 100000000);
                                    }
                                }

                            } else {
                                $result["error"] = 1301;
                                $result["msg"]   = $bet_res["error"];
                            }

                        } else {
                            $result["no_enough_balance"] = 1;
                            $result["error"] = 2500;
                            $result["msg"]   = "Not enough balance to double";
                        }

                    } else {
                        $result["error"] = 1100;
                        $result["msg"]   = "Cant execute this action";
                    }
                } else {
                    $result["error"] = 1000;
                    $result["msg"]   = "Session not found";
                }
            break;

            // =========================
            // SPLIT
            // =========================
            case "split":
                $session = get_blackjack_session_by_player($_player->vars["id"]);
                if (!is_null($session)) {
                    $logic->load_data($session);
                    if ($session->vars["game_status"] == "dealed" && $logic->can_split()) {

                        $bal_res = wallet_balance($_player->vars["id"]);
                        $current_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;

                        if ($session->vars["bet_amount"] <= $current_balance) {

                            $bet_res = wallet_bet($_player->vars["id"], $session->vars["bet_amount"], "bet", $game->vars["id"]);
                            if ($bet_res["success"]) {

                                $logic->store_settle_log($session->vars["bet_amount"] * -1);
                                $logic->split_hand();

                                $result["player_hand_value"]  = $logic->get_hand_value();
                                $result["player_hand_value2"] = $logic->get_hand_value("player2");
                                $result["player_hand"]        = implode(",", $logic->player_hand);
                                $result["player_hand2"]       = implode(",", $logic->player_hand2);
                                $result["finished"]           = $logic->game_finished;
                                $result["finished2"]          = $logic->temp_game_finished2;
                                $result["status"]             = $logic->game_status;
                                $result["status2"]            = $logic->game_status2;

                                if ($logic->game_status == "player_blackjack") {
                                    $win_amount = $_using_free_play ? $logic->win_amount : $logic->bet_amount + $logic->win_amount;
                                    $win_res    = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
                                    $logic->store_settle_log($win_amount);
                                }

                                if ($logic->game_finished) {
                                    $temp_hand          = $logic->player_hand;
                                    $logic->player_hand  = $logic->player_hand2;
                                    $logic->player_hand2 = $temp_hand;
                                }

                                $is_second = $logic->move_to_second_hand();

                                if ($logic->game_finished && !$is_second) {
                                    if ($_company->vars["prov_fair"]) {
                                        $result["pf"]["lxhs"] = $logic->pf_data["xhs"];
                                        $result["pf"]["lsc1"] = $logic->pf_data["xz1"];
                                        $result["pf"]["lsc2"] = $logic->pf_data["xz2"];
                                        $result["pf"]["lxnr"] = $logic->pf_data["xnr"];
                                        $result["pf"]["lpnr"] = $logic->pf_data["pnr"];
                                        $result["pf"]["lpos"] = $logic->pf_data["pos"];
                                        $result["pf"]["nxnr"] = generate_server_seed("bljk", 0, 100000000);
                                    }
                                }

                            } else {
                                $result["error"] = 1301;
                                $result["msg"]   = $bet_res["error"];
                            }

                        } else {
                            $result["no_enough_balance"] = 1;
                            $result["error"] = 2500;
                            $result["msg"]   = "Not enough balance to split";
                        }

                    } else {
                        $result["error"] = 1100;
                        $result["msg"]   = "Cant execute this action";
                    }
                } else {
                    $result["error"] = 1000;
                    $result["msg"]   = "Session not found";
                }
            break;

            // =========================
            // INSURANCE
            // =========================
            case "insurance":
                $session  = get_blackjack_session_by_player($_player->vars["id"]);
                $accepted = param("accepted");
                if (!is_null($session)) {
                    $logic->load_data($session);
                    if ($session->vars["game_status"] == "ask_insurance") {

                        $bal_res         = wallet_balance($_player->vars["id"]);
                        $current_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
                        $insurance_cost  = $session->vars["bet_amount"] / 2;

                        if ($insurance_cost <= $current_balance || !$accepted) {

                            $api_ok = true;
                            if ($accepted) {
                                $bet_res = wallet_bet($_player->vars["id"], $insurance_cost, "bet", $game->vars["id"]);
                                $api_ok  = $bet_res["success"];
                                if ($api_ok) {
                                    $logic->store_settle_log($insurance_cost * -1);
                                } else {
                                    $result["error"] = 1302;
                                    $result["msg"]   = $bet_res["error"];
                                    break;
                                }
                            }

                            $logic->insurance($accepted);

                            $new_balance = 0;
                            if ($logic->win_amount > 0 && $logic->game_finished) {
                                $win_amount  = $_using_free_play ? $logic->win_amount : ($logic->bet_amount / 2) + $logic->win_amount;
                                $win_res     = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
                                $new_balance = $win_res["success"] ? $win_res["balance"] : 0;
                                $logic->store_settle_log($win_amount);
                            } else {
                                $bal_res     = wallet_balance($_player->vars["id"]);
                                $new_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
                            }

                            $result["status"]    = $logic->game_status;
                            $result["finished"]  = $logic->game_finished;
                            $result["can_split"] = $logic->can_split();
                            $result["balance"]   = $new_balance;

                            if ($logic->game_finished) {
                                $result["dealer_hidden_card"] = $logic->dealer_hand[0];
                                $result["dealer_hand_value"]  = $logic->get_hand_value("dealer");
                                $result["win_amount"]         = $logic->win_amount;

                                if ($_company->vars["prov_fair"]) {
                                    $result["pf"]["lxhs"] = $logic->pf_data["xhs"];
                                    $result["pf"]["lsc1"] = $logic->pf_data["xz1"];
                                    $result["pf"]["lsc2"] = $logic->pf_data["xz2"];
                                    $result["pf"]["lxnr"] = $logic->pf_data["xnr"];
                                    $result["pf"]["lpnr"] = $logic->pf_data["pnr"];
                                    $result["pf"]["lpos"] = $logic->pf_data["pos"];
                                    $result["pf"]["nxnr"] = generate_server_seed("bljk", 0, 100000000);
                                }
                            }

                        } else {
                            $result["no_enough_balance"] = 1;
                            $result["error"] = 2550;
                            $result["msg"]   = "Not enough balance to play insurance";
                        }

                    } else {
                        $result["error"] = 1100;
                        $result["msg"]   = "Cant execute this action";
                    }
                } else {
                    $result["error"] = 1000;
                    $result["msg"]   = "Session not found";
                }
            break;

            // =========================
            // STAND
            // =========================
            case "stand":
                $session = get_blackjack_session_by_player($_player->vars["id"]);
                if (!is_null($session)) {
                    $logic->load_data($session);
                    if ($session->vars["game_status"] == "dealed" || $session->vars["game_status"] == "hitted") {

                        $logic->stand();
                        $result["status"]   = $logic->game_status;
                        $result["finished"] = $logic->game_finished;

                        $new_balance = 0;
                        switch ($logic->game_status) {
                            case 'push':
                                $win_amount  = $logic->bet_amount;
                                $win_res     = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
                                $new_balance = $win_res["success"] ? $win_res["balance"] : 0;
                                $logic->store_settle_log($win_amount);
                            break;
                            case 'dealer_bust':
                            case 'win':
                                $win_amount  = $_using_free_play ? $logic->win_amount : $logic->win_amount + $logic->bet_amount;
                                $win_res     = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
                                $new_balance = $win_res["success"] ? $win_res["balance"] : 0;
                                $logic->store_settle_log($win_amount);
                            break;
                            case 'lose':
                                $bal_res     = wallet_balance($_player->vars["id"]);
                                $new_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
                            break;
                        }

                        if ($logic->game_finished) {
                            $result["dealer_hidden_card"] = $logic->dealer_hand[0];
                            $result["dealer_hand_value"]  = $logic->get_hand_value("dealer");
                            $result["balance"]            = $new_balance;
                            $result["win_amount"]         = $logic->win_amount;
                            $result["dealer_hand"]        = implode(",", $logic->dealer_hand);
                            $result["splited"]            = $logic->splited;
                            $result["win_amount2"]        = $logic->win_amount2;
                            $result["status2"]            = $logic->game_status2;
                            $result["bet_amount2"]        = $logic->bet_amount2;
                        }

                        $result["second_hand"] = $logic->move_to_second_hand();

                        if ($logic->game_finished && !$result["second_hand"]) {
                            if ($_company->vars["prov_fair"]) {
                                $result["pf"]["lxhs"] = $logic->pf_data["xhs"];
                                $result["pf"]["lsc1"] = $logic->pf_data["xz1"];
                                $result["pf"]["lsc2"] = $logic->pf_data["xz2"];
                                $result["pf"]["lxnr"] = $logic->pf_data["xnr"];
                                $result["pf"]["lpnr"] = $logic->pf_data["pnr"];
                                $result["pf"]["lpos"] = $logic->pf_data["pos"];
                                $result["pf"]["nxnr"] = generate_server_seed("bljk", 0, 100000000);
                            }
                        }

                    } else {
                        $result["error"] = 1100;
                        $result["msg"]   = "Cant execute this action";
                    }
                } else {
                    $result["error"] = 1000;
                    $result["msg"]   = "Session not found";
                }
            break;

            // =========================
            // DEAL
            // =========================
            case "deal":
                $bet_amount = floatval(param("bet"));
                $player_num = param("pnr");
                if (!is_numeric($player_num) || $player_num > 100000000 || $player_num < 0) { $player_num = 0; }
                $player_num = round($player_num);

                if ($bet_amount > 0) {

                    $session = get_blackjack_session_by_player($_player->vars["id"]);
                    if (is_null($session)) {

                        if ($_player->in_between_limits($bet_amount)) {

                            $bal_res = wallet_balance($_player->vars["id"]);
                            $current_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;

                            if ($bet_amount <= $current_balance) {

                                if ($bet_amount >= $game->vars["min_amount"] && $bet_amount <= $game->vars["max_amount"]) {

                                    $bet_res = wallet_bet($_player->vars["id"], $bet_amount, "bet", $game->vars["id"]);
                                    if ($bet_res["success"]) {

                                        $logic->store_settle_log($bet_amount * -1);

                                        if ($_company->vars["prov_fair"]) {
                                            $seed_data          = discover_server_seed("bljk");
                                            $pf_result          = $seed_data["xnr"] + $player_num;
                                            if ($pf_result > 100000000) { $pf_result -= 100000000; }
                                            $seed_data["pnr"]   = $player_num;
                                            $seed_data["pos"]   = $pf_result;
                                            $logic->start_pf($pf_result, $seed_data);
                                        }

                                        $logic->bet_amount = $bet_amount;
                                        $session_id        = $logic->deal();

                                        $result["player_hand"]       = implode(",", $logic->player_hand);
                                        $result["dealer_hand"]       = $logic->dealer_hand[1];
                                        $result["status"]            = $logic->game_status;
                                        $result["balance"]           = $bet_res["balance"];
                                        $result["win_amount"]        = $logic->win_amount;
                                        $result["finished"]          = $logic->game_finished;
                                        $result["player_hand_value"] = $logic->get_hand_value();
                                        $result["can_split"]         = $logic->can_split();

                                        // Immediate finish (blackjack / push)
                                        switch ($logic->game_status) {
                                            case 'win':
                                            case 'dealer_bust':
                                            case 'player_blackjack':
                                                $win_amount  = $_using_free_play ? $logic->win_amount : $bet_amount + $logic->win_amount;
                                                $win_res     = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
                                                $result["balance"] = $win_res["success"] ? $win_res["balance"] : $result["balance"];
                                                $logic->store_settle_log($win_amount);
                                            break;
                                            case 'push':
                                            case 'push_blackjack':
                                                $win_amount  = $bet_amount;
                                                $win_res     = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
                                                $result["balance"] = $win_res["success"] ? $win_res["balance"] : $result["balance"];
                                                $logic->store_settle_log($win_amount);
                                            break;
                                        }

                                        if ($logic->game_finished) {
                                            $result["dealer_hidden_card"] = $logic->dealer_hand[0];
                                            $result["dealer_hand_value"]  = $logic->get_hand_value("dealer");
                                            $result["dealer_full_hand"]   = implode(",", $logic->dealer_hand);

                                            if ($_company->vars["prov_fair"]) {
                                                $result["pf"]["lxhs"] = $logic->pf_data["xhs"];
                                                $result["pf"]["lsc1"] = $logic->pf_data["xz1"];
                                                $result["pf"]["lsc2"] = $logic->pf_data["xz2"];
                                                $result["pf"]["lxnr"] = $logic->pf_data["xnr"];
                                                $result["pf"]["lpnr"] = $logic->pf_data["pnr"];
                                                $result["pf"]["lpos"] = $logic->pf_data["pos"];
                                                $result["pf"]["nxnr"] = generate_server_seed("bljk", 0, 100000000);
                                            }
                                        }

                                    } else {
                                        $result["error"] = 800;
                                        $result["msg"]   = $bet_res["error"];
                                    }

                                } else {
                                    $result["error"] = 700;
                                    $result["msg"]   = "Amount is not within the game limits";
                                }

                            } else {
                                $result["no_enough_balance"] = 1;
                                $result["error"] = 500;
                                $result["msg"]   = "Not enough balance";
                            }

                        } else {
                            $result["not_between_limits"] = 1;
                            $result["error"] = 3745;
                            $result["msg"]   = "You reached the Casino allowed limits.";
                        }

                    } else {
                        $result["error"] = 400;
                        $result["msg"]   = "Player has unfinished games.";
                    }

                } else {
                    $result["error"] = 600;
                    $result["msg"]   = "Invalid amount";
                }
            break;

            default:
                $result["error"] = 300;
                $result["msg"]   = "Action not found.";
            break;
        }

    } else {
        $result["error"] = 345;
        $result["msg"]   = "This game is not available.";
    }
}

echo json_encode($result);
