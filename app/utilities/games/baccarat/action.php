<?php
// Session must start before any output or ob manipulation
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

while (ob_get_level()) ob_end_clean();
error_reporting(E_ALL & ~E_WARNING & ~E_NOTICE & ~E_DEPRECATED);
ini_set('display_errors', 0);
header('Content-Type: application/json');

$result = array();
$result["error"] = 0;

include($_SERVER['DOCUMENT_ROOT'] . "/utilities/process/security.php");
require_once($_SERVER['DOCUMENT_ROOT'] . '/control/modules/wallet.php');

$game_id = param("gid");

if($_logged){

    include("class.php");

    $action = param("action");
    $logic = new baccarat($_player->vars["id"]);
    $game = $_player->get_allowed_game($game_id);

    if(!is_null($game) && $game->vars["path"] == "baccarat"){

        switch($action){

            // =========================calude
            // =========================
            case "balance":

                $res = wallet_balance($_player->vars["id"]);

                if($res["success"]){

                    $balance = $res["data"];

                    $result["balance"] = floatval($balance["real"]);
					$result["amount"]  = floatval($balance["real"]);

                }else{

                    $result["error"] = 900;
                    $result["msg"] = "Balance error: " . $res["error"];

                }

            break;


            // =========================
            // DEAL (APUESTA)
            // =========================
            case "deal":

                $bets = param("bets");
                $bet_amount = 0;
                $over_limit = false;

                $player_num = param("pnr");
                if(!is_numeric($player_num) || $player_num > 100000000 || $player_num < 0){
                    $player_num = 0;
                }
                $player_num = round($player_num);

                $bet_list = explode(",", $bets);

                foreach($bet_list as $bet){
                    $parts = explode("|", $bet);

                    if(isset($parts[1]) && is_numeric($parts[1]) && $parts[1] > 0){

                        $bet_amount += floatval($parts[1]);

                        if($parts[1] < $game->vars["min_amount"] || $parts[1] > $game->vars["max_amount"]){
                            $over_limit = true;
                        }
                    }
                }

                $bet_amount = floatval($bet_amount);

                if($bet_amount > 0){

                    if($_player->in_between_limits($bet_amount)){

                        // 🔥 obtener balance real
                        $res = wallet_balance($_player->vars["id"]);

                        if(!$res["success"]){
                            $result["error"] = 900;
                            $result["msg"] = "Balance error";
                            break;
                        }

                        $balance = $res["data"];
                        $current_balance = floatval($balance["real"]);

                        // 🔥 DEBUG (puedes quitar luego)
                        $result["debug_balance"] = $current_balance;
                        $result["debug_bet"] = $bet_amount;

                        if($bet_amount > $current_balance){

                            $result["no_enough_balance"] = 1;
                            $result["error"] = 500;
                            $result["msg"] = "Not enough balance";
                            break;
                        }

                        if(!$over_limit){

                            // =========================
                            // RESTAR APUESTA
                            // =========================
                            $bet_res = wallet_bet(
                                $_player->vars["id"],
                                $bet_amount,
                                "bet",
                                $game->vars["id"]
                            );

                            if(!$bet_res["success"]){
                                $result["error"] = 800;
                                $result["msg"] = $bet_res["error"];
                                break;
                            }

                            // =========================
                            // PROVABLY FAIR
                            // =========================
                            if($_company->vars["prov_fair"]){

                                $seed_data = discover_server_seed("bcrt");

                                $pf_result = $seed_data["xnr"] + $player_num;
                                if($pf_result > 100000000){
                                    $pf_result -= 100000000;
                                }

                                $logic->start_pf($pf_result);

                                $next_seed = generate_server_seed("bcrt", 0, 100000000);

                                $result["pf"] = [
                                    "lxhs" => $seed_data["xhs"],
                                    "lsc1" => $seed_data["xz1"],
                                    "lsc2" => $seed_data["xz2"],
                                    "lxnr" => $seed_data["xnr"],
                                    "lpnr" => $player_num,
                                    "lpos" => $pf_result,
                                    "nxnr" => $next_seed
                                ];
                            }

                            // =========================
                            // JUEGO
                            // =========================
                            $game_result = $logic->deal($bets);
                            $result["game_result"] = $game_result;

                            $win_amount = round($logic->win_amount, 2);
                            $result["win_amount"] = $win_amount;

                            // =========================
                            // PAGAR PREMIO
                            // =========================
                            if($win_amount > 0){

                                $win_res = wallet_win(
                                    $_player->vars["id"],
                                    $win_amount,
                                    "win",
                                    $game->vars["id"]
                                );

                                if(!$win_res["success"]){
                                    $result["error"] = 801;
                                    $result["msg"] = $win_res["error"];
                                    break;
                                }

                                $result["balance"] = $win_res["balance"];

                            }else{

                                $res = wallet_balance($_player->vars["id"]);
                                $result["balance"] = floatval($res["data"]["real"]);
                            }

                        }else{

                            $result["error"] = 700;
                            $result["msg"] = "Amount is not within the game limits";
                        }

                    }else{

                        $result["not_between_limits"] = 1;
                        $result["error"] = 3745;
                        $result["msg"] = "You reached the Casino allowed limits.";
                    }

                }else{

                    $result["error"] = 600;
                    $result["msg"] = "Invalid amount";
                }

            break;


            default:
                $result["error"] = 300;
                $result["msg"] = "Action not found.";
            break;
        }

    }else{
        $result["error"] = 345;
        $result["msg"] = "This game is not available.";
    }

}

echo json_encode($result);