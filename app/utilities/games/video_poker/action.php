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
	$logic = new video_poker($_player ->vars["id"]);
	$game = $_player->get_allowed_game($game_id);

	if(!is_null($game) && $game ->vars["path"] == "video_poker"){

		switch($action){
			case "start":
				$session = get_video_poker_session_by_player($_player ->vars["id"],"jacks");
				if(!is_null($session)){
					$logic->load_data($session);
					$result["has_started_game"]	= 1;
					$result["cards"] = $logic ->session ->vars["hand"];
					$result["coins"] = $logic ->session ->vars["coins"];
					$result["total_bet"] = $logic ->session ->vars["bet_amount"];

					if($logic ->using_pf){
						$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
						$result["pf"]["lpnr"] = $logic->pf_data["pnr"];
					}

				}else{
					$result["has_started_game"]	= 0;
				}
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


			case "deal":

				$bet_amount = param("bet");
				$coins = param("coins");

				$player_num = param("pnr"); //for provably fair
				if(!is_numeric($player_num) || $player_num > 100000000 || $player_num < 0){$player_num = 0;}
				$player_num = round($player_num);

				if(is_numeric($bet_amount)){

					$session = get_video_poker_session_by_player($_player ->vars["id"],"jacks");
					if(is_null($session)){

						if($_player->in_between_limits($bet_amount)){

							$bal_res = wallet_balance($_player->vars["id"]);
							$current_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
							if($bet_amount <= $current_balance){

								if($bet_amount/$coins >= $game ->vars["min_amount"] && $bet_amount/$coins <= $game ->vars["max_amount"]){

									$bet_res = wallet_bet($_player->vars["id"], $bet_amount, "bet", $game->vars["id"]);
									if($bet_res["success"]){

										//provably fair
										if($_company ->vars["prov_fair"]){
											$seed_data = discover_server_seed("vdpkr");
											$pf_result = $seed_data["xnr"] + $player_num;
											if($pf_result > 100000000){$pf_result -= 100000000;}
											$seed_data["pnr"] = $player_num;
											$seed_data["pos"] = $pf_result;
											$logic->start_pf($pf_result,$seed_data);
										}

										$cards = $logic ->deal($bet_amount,$coins);
										$result["balance"] = $bet_res["balance"];
										$result["cards"] = $cards;

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
						$result["error"] = 400;
						$result["msg"] = "Player has unfinished games.";
					}

				}else{
					$result["error"] = 600;
					$result["msg"] = "Invalid amount";
				}

			break;

			case "draw":
				$holds = param("holds");
				$session = get_video_poker_session_by_player($_player ->vars["id"],"jacks");
				if(!is_null($session)){
					$logic->load_data($session);
					if(!$logic ->session ->vars["finished"]){

						$hand = $logic->draw($holds);
						$win_amount = $logic->win_amount;

						if($win_amount > 0){
							$win_res = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
							$result["balance"] = $win_res["success"] ? $win_res["balance"] : 0;
						}else{
							$bal_res = wallet_balance($_player->vars["id"]);
							$result["balance"] = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
						}

						$result["win_amount"] = $win_amount;
						$result["win_level"] = $logic->win_level;
						$result["cards"] = $hand;

						//provably fair
						if($_company ->vars["prov_fair"]){

							$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
							$result["pf"]["lsc1"] = $logic->pf_data["xz1"];
							$result["pf"]["lsc2"] = $logic->pf_data["xz2"];
							$result["pf"]["lxnr"] = $logic->pf_data["xnr"];
							$result["pf"]["lpnr"] = $logic->pf_data["pnr"];
							$result["pf"]["lpos"] = $logic->pf_data["pos"];

							$next_seed = generate_server_seed("vdpkr",0,100000000);
							$result["pf"]["nxnr"] = $next_seed;

						}

					}else{
						$result["error"] = 1000;
						$result["msg"] = "This session is no longer available";
					}

				}else{
					$result["error"] = 1000;
					$result["msg"] = "Session not found";
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
