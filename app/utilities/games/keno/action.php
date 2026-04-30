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
	$logic = new keno($_player ->vars["id"]);
	$game = $_player->get_allowed_game($game_id);

	if(!is_null($game) && $game ->vars["path"] == "keno"){

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
				$selected_nums = param("nums");

				//for provably fair---------------
				$player_nums = explode(",",param("pnr"));
				$valid_player_nums = array();
				foreach($player_nums as $pnum){
					if(!is_numeric($pnum) || $pnum > 199 || $player_num < 0){$pnum = 0;}
					$valid_player_nums[] = $pnum;
				}
				$player_num = implode(",",$valid_player_nums);
				//-------------------------------

				$bet_amount = $coin_value;


				if(is_numeric($bet_amount)){

					if($_player->in_between_limits($bet_amount)){

						$bal_res = wallet_balance($_player->vars["id"]);
						$current_balance = $bal_res["success"] ? floatval($bal_res["data"]["real"]) : 0;
						if($bet_amount <= $current_balance){

							if($bet_amount >= $game ->vars["min_amount"] && $bet_amount <= $game ->vars["max_amount"]){

								$bet_res = wallet_bet($_player->vars["id"], $bet_amount, "bet", $game->vars["id"]);

								if($bet_res["success"]){

									$reels_positions = $logic->spin($bet_amount,$selected_nums);

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
