<?php
while (ob_get_level()) ob_end_clean();
header('Content-Type: application/json');
error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED);
ini_set('display_errors', 0);


$result = array();
$result["error"] = 0;
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/security.php");
require_once($_SERVER['DOCUMENT_ROOT'].'/control/modules/wallet.php');

$game_id = param("gid");

if($_logged){

	include("class.php");

	$action = param("action");
	$logic = new classic_slot($_player ->vars["id"]);
	$game = $_player->get_allowed_game($game_id);

	if(!is_null($game) && $game ->vars["path"] == "classic_slot"){

		switch($action){
			case "start":

			break;

			case "balance":
				$bal_res = wallet_balance($_player->vars["id"]);
				if($bal_res["success"]){
					$result["balance"] = floatval($bal_res["data"]["real"]);
				}else{
					$result["error"] = 900;
					$result["msg"] = "Balance error: " . $bal_res["error"];
				}
			break;


			case "spin":

				$bet_amount = param("bet");

				$player_num = param("pnr"); //for provably fair
				if(!is_numeric($player_num) || $player_num > 1999999 || $player_num < 1000000){$player_num = 1000000;}
				$player_num = round($player_num);

				if(is_numeric($bet_amount)){

					if($_player->in_between_limits($bet_amount)){

						$bal_res = wallet_balance($_player->vars["id"]);
						$current_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
						if($bet_amount <= $current_balance){

							if($bet_amount >= $game ->vars["min_amount"] && $bet_amount <= $game ->vars["max_amount"]*3){

								$bet_res = wallet_bet($_player->vars["id"], $bet_amount, "bet", $game->vars["id"]);
								if($bet_res["success"]){

									if($_company ->vars["prov_fair"]){
										//provalby fair
										$seed_data = discover_server_seed("fs");
										$pf_result = $seed_data["xnr"] + $player_num;

										$result["pf"]["lxhs"] = $seed_data["xhs"];
										$result["pf"]["lsc1"] = $seed_data["xz1"];
										$result["pf"]["lsc2"] = $seed_data["xz2"];
										$result["pf"]["lxnr"] = $seed_data["xnr"];
										$result["pf"]["lpnr"] = $player_num;
										$result["pf"]["lpos"] = $pf_result;

										$reels_positions = $logic->spin($bet_amount,$pf_result);

										$next_seed = generate_server_seed("fs");

										$result["pf"]["nxnr"] = $next_seed;
									}else{
										//no provably fair
										$reels_positions = $logic->spin($bet_amount);
									}

									$result["positions"] = $reels_positions;

									$win_amount = $logic->win_amount;
									$result["win_amount"] = $win_amount;
									if($win_amount > 0){
										$win_res = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
										$result["balance"] = $win_res["success"] ? $win_res["balance"] : 0;
									}else{
										$bal_res = wallet_balance($_player->vars["id"]);
										$result["balance"] = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
									}

								}else{
									$result["error"] = 800;
									$result["msg"] = $bet_res["error"];
								}

							}else{
								$result["error"] = 700;
								$result["msg"] = "Amount is not within the game limits";
							}

						}else{
							$result["no_enough_balance"] = 1;
							$result["error"] = 500;
							$result["msg"] = "Not enough balance";
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


			default :
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


?>
