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
	$logic = new multi_slot($_player ->vars["id"]);
	$game = $_player->get_allowed_game($game_id);

	if(!is_null($game) && $game ->vars["path"] == "multi_slot"){

		switch($action){

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

				$coin_value = param("bet");
				$lines = param("lines");

				//for provably fair---------------
				$player_nums = explode(",",param("pnr"));
				$valid_player_nums = array();
				foreach($player_nums as $pnum){
					if(!is_numeric($pnum) || $pnum > 109 || $player_num < 0){$pnum = 0;}
					$valid_player_nums[] = $pnum;
				}
				$player_num = implode(",",$valid_player_nums);
				//-------------------------------


				$bet_amount = round($coin_value*$lines,2);

				if(is_numeric($bet_amount)){

					if($_player->in_between_limits($bet_amount)){

						$bal_res = wallet_balance($_player->vars["id"]);
						$current_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
						if($bet_amount <= $current_balance){

							if($bet_amount >= $game ->vars["min_amount"] && $bet_amount <= $game ->vars["max_amount"]*25){

								$bet_res = wallet_bet($_player->vars["id"], $bet_amount, "bet", $game->vars["id"]);
								if($bet_res["success"]){

									if($_company ->vars["prov_fair"]){
										//provalby fair
										$seed_data = discover_server_seed("egys");
										$pf_result = sum_multi_nums($seed_data["xnr"],$player_num,",",109);

										$result["pf"]["lxhs"] = $seed_data["xhs"];
										$result["pf"]["lsc1"] = $seed_data["xz1"];
										$result["pf"]["lsc2"] = $seed_data["xz2"];
										$result["pf"]["lxnr"] = $seed_data["xnr"];
										$result["pf"]["lpnr"] = $player_num;
										$result["pf"]["lpos"] = $pf_result;

										$reels_positions = $logic->spin($coin_value, $lines, $pf_result);

										$hard_numbers = array();
										for($i=0;$i<15;$i++){
											$hard_numbers[] = mt_rand(0,109);
										}

										$next_seed = generate_server_seed("egys",0,0,implode(",",$hard_numbers));

										$result["pf"]["nxnr"] = $next_seed;
									}else{
										//no provably fair
										$reels_positions = $logic->spin($coin_value, $lines);
									}

									$result["positions"] = $reels_positions;

									$win_amount = $logic->win_amount;
									$result["win_amount"] = $win_amount;
									$result["win_x5"] = $logic->win_x5;
									$result["winning_lines"] = implode(",",$logic->winning_lines);
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
