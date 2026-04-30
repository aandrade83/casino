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
	$logic = new _three_card_poker($_player ->vars["id"]);
	$game = $_player->get_allowed_game($game_id);

	if(!is_null($game) && $game ->vars["path"] == "three_card"){

		switch($action){
			case "start":

				$session = get_poker_session_by_player($_player ->vars["id"],"three_card");
				if(!is_null($session)){
					$logic->load_data($session);
					$result["has_started_game"]	= 1;

					$dealer_cards = explode(",",$logic ->session ->vars["dealer_hand"]);

					$result["ante_bet"] = $logic ->session ->vars["ante_bet"];
					$result["game_status"] = $logic ->session ->vars["status"];
					$result["player_cards"] = $logic ->session ->vars["player_hand"];
					$result["player_level"] = $logic->player_hand_level;
					$result["dealer_card"] = $dealer_cards[4];

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
				$ppbet_amount = param("pplus");

				$player_num = param("pnr"); //for provably fair
				if(!is_numeric($player_num) || $player_num > 100000000 || $player_num < 0){$player_num = 0;}
				$player_num = round($player_num);

				if(is_numeric($bet_amount) && is_numeric($ppbet_amount)){

					$session = get_poker_session_by_player($_player ->vars["id"],"three_card");

					if(is_null($session)){

						if($_player->in_between_limits($bet_amount+$ppbet_amount)){

							$bal_res = wallet_balance($_player->vars["id"]);
							$current_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
							if((($bet_amount*2)+ $ppbet_amount) <= $current_balance){// checks for balance for ante and bet

								if(
								(($bet_amount >= $game ->vars["min_amount"] && $bet_amount <= $game ->vars["max_amount"]) || $bet_amount == 0) &&
								(($ppbet_amount >= $game ->vars["min_amount"] && $ppbet_amount <= $game ->vars["max_amount"]) || $ppbet_amount == 0) &&
								($bet_amount+$ppbet_amount) >= $game ->vars["min_amount"]
								){ //checks ante

									$bet_res = wallet_bet($_player->vars["id"], ($bet_amount+$ppbet_amount), "bet", $game->vars["id"]);
									if($bet_res["success"]){

										//provably fair
										if($_company ->vars["prov_fair"]){
											$seed_data = discover_server_seed("tcpkr");
											$pf_result = $seed_data["xnr"] + $player_num;
											if($pf_result > 100000000){$pf_result -= 100000000;}
											$seed_data["pnr"] = $player_num;
											$seed_data["pos"] = $pf_result;
											$logic->start_pf($pf_result,$seed_data);
										}

										$deal_result = $logic ->deal($bet_amount,$ppbet_amount);
										$result["balance"] = $bet_res["balance"];
										$result["cards"] = $deal_result["cards"];
										$result["player_level"] = $deal_result["player_level"];
										$result["game_status"] = $deal_result["game_status"];
										$result["pp_status"] = $deal_result["pp_status"];
										$result["pp_win_amount"] = round($deal_result["pp_credit_amount"],2);

										if($deal_result["pp_credit_amount"]){
											$pp_credit = !$_using_free_play ? $deal_result["pp_credit_amount"] : $deal_result["pp_win_amount"];
											$win_res = wallet_win($_player->vars["id"], $pp_credit, "win", $game->vars["id"]);
											$result["balance"] = $win_res["success"] ? $win_res["balance"] : 0;
										}

										if($logic ->using_pf){
											$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
											$result["pf"]["lsc1"] = $logic->pf_data["xz1"];
											$result["pf"]["lsc2"] = $logic->pf_data["xz2"];
											$result["pf"]["lxnr"] = $logic->pf_data["xnr"];
											$result["pf"]["lpnr"] = $logic->pf_data["pnr"];
											$result["pf"]["lpos"] = $logic->pf_data["pos"];

											$next_seed = generate_server_seed("tcpkr",0,100000000);
											$result["pf"]["nxnr"] = $next_seed;
										}

									}else{
										$result["error"] = 800;
										$result["msg"] = $bet_res["error"];
									}

								}else{
									$result["error"] = 700;
									$result["msg"] = "Amount is not within the game limits ($ppbet_amount $bet_amount)";
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

			case "call":
				$session = get_poker_session_by_player($_player ->vars["id"],"three_card");
				if(!is_null($session)){

					$logic->load_data($session);
					$bet_amount = $logic ->session ->vars["ante_bet"];

					if($_player->in_between_limits($bet_amount)){

						$bal_res = wallet_balance($_player->vars["id"]);
						$current_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
						if($bet_amount <= $current_balance){

							if(!$logic ->session ->vars["finished"]){

								if($logic ->session ->vars["status"] == "dealed"){

									$bet_res = wallet_bet($_player->vars["id"], $bet_amount, "bet", $game->vars["id"]);
									if($bet_res["success"]){

										$result["balance"] = $bet_res["balance"];
										$call_result = $logic->call();
										$result["dealer_cards"] = $call_result["dealer_cards"];
										$result["dealer_level"] = $call_result["dealer_level"];

										/*------------*/
										$game_result = $logic->grade();
										$result["game_result"] = $game_result;
										$result["winner"] = $logic->winner;
										$result["win_amount"] = round($logic->win_amount,2);
										$result["game_status"] = "finished";

										$win_amount = $logic->win_amount;
										$credit_amount = $logic->credit_amount;
										if($credit_amount > 0){
											if(!$_using_free_play){
												$win_res = wallet_win($_player->vars["id"], $credit_amount, "win", $game->vars["id"]);
											}else{
												$win_res = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
											}
											$result["balance"] = $win_res["success"] ? $win_res["balance"] : 0;
										}else{
											$bal_res = wallet_balance($_player->vars["id"]);
											$result["balance"] = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
										}

										if($logic ->using_pf){
											$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
											$result["pf"]["lsc1"] = $logic->pf_data["xz1"];
											$result["pf"]["lsc2"] = $logic->pf_data["xz2"];
											$result["pf"]["lxnr"] = $logic->pf_data["xnr"];
											$result["pf"]["lpnr"] = $logic->pf_data["pnr"];
											$result["pf"]["lpos"] = $logic->pf_data["pos"];

											$next_seed = generate_server_seed("tcpkr",0,100000000);
											$result["pf"]["nxnr"] = $next_seed;
										}
										/*------------*/

									}else{
										$result["error"] = 800;
										$result["msg"] = $bet_res["error"];
									}

								}else{
									$result["error"] = 8877;
									$result["msg"] = "Cant do this action";
								}


							}else{
								$result["error"] = 1000;
								$result["msg"] = "This session is no longer available";
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
					$result["error"] = 1000;
					$result["msg"] = "Session not found";
				}
			break;



			case "river":
				$session = get_poker_session_by_player($_player ->vars["id"],"three_card");
				$bet = param("bet");
				if(!is_null($session)){

					$logic->load_data($session);
					$bet_amount = $logic ->session ->vars["ante_bet"];

					if(!$bet || $_player->in_between_limits($bet_amount)){

						$bal_res = wallet_balance($_player->vars["id"]);
						$current_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
						if(!$bet || $bet_amount <= $current_balance){

							if(!$logic ->session ->vars["finished"]){

								if($logic ->session ->vars["status"] == "turned"){

									if($bet){
										$bet_res = wallet_bet($_player->vars["id"], $bet_amount, "bet", $game->vars["id"]);
										$bet_ok = $bet_res["success"];
									}else{
										$bet_ok = true;
									}

									if($bet_ok){

										$card = $logic->river($bet);
										$game_result = $logic->grade();
										$result["game_result"] = $game_result;
										$result["winner"] = $logic->winner;
										$result["win_amount"] = round($logic->win_amount,2);
										$result["game_status"] = "finished";
										$result["dealer_hand"] = $logic ->session ->vars["dealer_hand"];
										$result["card"] = $card;

										$win_amount = $logic->win_amount;
										$credit_amount = $logic->credit_amount;
										if($credit_amount > 0){
											if(!$_using_free_play){
												$win_res = wallet_win($_player->vars["id"], $credit_amount, "win", $game->vars["id"]);
											}else{
												$win_res = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
											}
											$result["balance"] = $win_res["success"] ? $win_res["balance"] : 0;
										}else{
											$bal_res = wallet_balance($_player->vars["id"]);
											$result["balance"] = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
										}

										$result["best_player_game"] = $logic->player_hand_level;
										$result["best_dealer_game"] = $logic->dealer_hand_level;

										if($logic ->using_pf){
											$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
											$result["pf"]["lsc1"] = $logic->pf_data["xz1"];
											$result["pf"]["lsc2"] = $logic->pf_data["xz2"];
											$result["pf"]["lxnr"] = $logic->pf_data["xnr"];
											$result["pf"]["lpnr"] = $logic->pf_data["pnr"];
											$result["pf"]["lpos"] = $logic->pf_data["pos"];

											$next_seed = generate_server_seed("tcpkr",0,100000000);
											$result["pf"]["nxnr"] = $next_seed;
										}

									}else{
										$result["error"] = 800;
										$result["msg"] = $bet_res["error"];
									}

								}else{
									$result["error"] = 8877;
									$result["msg"] = "Cant do this action";
								}


							}else{
								$result["error"] = 1000;
								$result["msg"] = "This session is no longer available";
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
					$result["error"] = 1000;
					$result["msg"] = "Session not found";
				}
			break;

			case "fold":
				$session = get_poker_session_by_player($_player ->vars["id"],"three_card");
				if(!is_null($session)){
					$logic->load_data($session);
					if(!$logic ->session ->vars["finished"]){

						if($logic ->session ->vars["status"] == "dealed"){

							$bal_res = wallet_balance($_player->vars["id"]);
							$result["balance"] = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
							$logic->fold();
							$result["game_status"] = "folded";

							//PF
							if(is_array($logic->pf_data)){
								$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
								$result["pf"]["lsc1"] = $logic->pf_data["xz1"];
								$result["pf"]["lsc2"] = $logic->pf_data["xz2"];
								$result["pf"]["lxnr"] = $logic->pf_data["xnr"];
								$result["pf"]["lpnr"] = $logic->pf_data["pnr"];
								$result["pf"]["lpos"] = $logic->pf_data["pos"];

								$next_seed = generate_server_seed("tcpkr",0,100000000);
								$result["pf"]["nxnr"] = $next_seed;
							}

						}else{
							$result["error"] = 8877;
							$result["msg"] = "Cant do this action";
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
