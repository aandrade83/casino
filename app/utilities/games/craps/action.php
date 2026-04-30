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
	$logic = new craps($_player ->vars["id"]);
	$game = $_player->get_allowed_game($game_id);

	if(!is_null($game) && $game ->vars["path"] == "craps"){

		switch($action){

			case "start":
				$session = get_craps_session_by_player($_player ->vars["id"]);
				if(!is_null($session)){
					$data = $logic->load_data($session);
					$result["has_started_game"]	= 1;
					$result["bets"] = $data["bets"];
					$result["point"] = $data["point"];
					$result["total_bet"] = $data["total_bet"];
					$result["game_status"] = $data["game_status"];
					$result["point"] = $data["point"];
				}else{
					$result["has_started_game"]	= 0;
					$last_session_bets = get_pending_craps_bets_by_player($_player ->vars["id"]);
					$result["bets"] = $last_session_bets["pending_bets"]."";
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

			case "roll":

				$session = get_craps_session_by_player($_player ->vars["id"]);
				$logic->load_data($session);

				$bets = $logic->prepare_bets(param("bets"));
				$bet_amount = 0;

				$player_nums = explode(",",param("pnr")); //for provably fair ***** receive and check 2 numbers!!
				for($i=0;$i<2;$i++){
					if(!is_numeric($player_nums[$i]) || $player_nums[$i] > 6 || $player_nums[$i] < 1){$player_nums[$i] = 1;}
					$player_nums[$i] = round($player_nums[$i]);
				}


				if($logic->can_roll($bets)){

					$bet_list = explode(",",$bets);
					foreach($bet_list as $bet){
						$parts = explode("|",$bet);
						if(is_numeric($parts[1]) && $parts[1]>0){
							$bet_amount += $parts[1];
						}
					}

					if(is_numeric($bet_amount)){

						if($_player->in_between_limits($bet_amount)){

							$bal_res = wallet_balance($_player->vars["id"]);
							$current_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
							if($bet_amount <= $current_balance){

								if($bet_amount >= $game ->vars["min_amount"] && $bet_amount <= $game ->vars["max_amount"]){

									if($_company ->vars["prov_fair"]){
										//provably fair
										$seed_data = discover_server_seed("crps");
										$seed_list = str_split($seed_data["xnr"]);
										$dice1_list = array_splice($seed_list,6);
										$dice2_list = array_splice($seed_list,0,6);
										$pf_result = $dice2_list[$player_nums[0]-1].",".$dice1_list[$player_nums[1]-1];

										$result["pf"]["lxhs"] = $seed_data["xhs"];
										$result["pf"]["lsc1"] = $seed_data["xz1"];
										$result["pf"]["lsc2"] = $seed_data["xz2"];
										$result["pf"]["lxnr"] = $seed_data["xnr"];
										$result["pf"]["lpnr"] = implode(",",$player_nums);
										$result["pf"]["lpos"] = $pf_result;

										$roll_result = $logic->roll($bets, explode(",",$pf_result));

										//new seed
										$numbers = array(1,2,3,4,5,6);
										shuffle($numbers);
										$hard_num = implode("",$numbers);
										shuffle($numbers);
										$hard_num .= implode("",$numbers);

										$next_seed = generate_server_seed("crps",0,0,$hard_num);

										$result["pf"]["nxnr"] = $next_seed;
									}else{
										//no provably fair
										$roll_result = $logic->roll($bets);
									}

									$betted_amount = $roll_result["betted_amount"];
									if($betted_amount > 0){
										wallet_bet($_player->vars["id"], $betted_amount, "bet", $game->vars["id"]);
									}

									$result["dice1_value"] = $roll_result["dice1_value"];
									$result["dice2_value"] = $roll_result["dice2_value"];
									$result["remove_bets"] = $roll_result["remove_bets"];
									$result["won_bets"] = $roll_result["won_bets"];
									$result["move_bets"] = $roll_result["move_bets"];
									$result["game_status"] = $roll_result["game_status"];
									$result["finished"] = $roll_result["finished"];
									$result["keept_amount"] = $roll_result["keept_amount"];
									$result["point"] = $roll_result["point"];

									$win_amount = round($roll_result["win_amount"],2);
									$result["win_amount"] = $win_amount;
									if($win_amount > 0){
										$win_res = wallet_win($_player->vars["id"], $win_amount, "win", $game->vars["id"]);
										$result["balance"] = $win_res["success"] ? $win_res["balance"] : 0;
									}else{
										$bal_res = wallet_balance($_player->vars["id"]);
										$result["balance"] = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
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

				}else{
					$result["error"] = 978;
					$result["msg"] = "Incorrect bets found";
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
