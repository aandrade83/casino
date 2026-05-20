<? 

$result = array();
$result["error"] = 0;
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/security.php");

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
					$result["bets"] = !is_null($last_session_bets) ? $last_session_bets["pending_bets"]."" : "";
				}
			break;
			
			case "balance":
				$balance = $_api->get_player_balance($player_token);
				if($_api->done){
					if(!is_numeric($balance["amount"])){$balance["amount"] = 0;}
					$result["balance"] = $balance["amount"];
				}else{
					$result["error"] = 900;
					$result["msg"] = "Comunication error: ". $_api->error_msg;
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
							
							$balance = $_api->get_player_balance($player_token);
							if($bet_amount <= $balance["amount"]){
								
								if($bet_amount >= $game ->vars["min_amount"] && $bet_amount <= $game ->vars["max_amount"]){
									
									if($_company ->vars["prov_fair"]){	
										//provalby fair
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
										$data = $_api->place_bet($player_token, $betted_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
										
										if(!$_api->done){ //error placing something in API
											  $aerror = new _api_error_log();
											  $aerror ->vars["session"] = $session_id;
											  $aerror ->vars["game"] = $game ->vars["id"];
											  $aerror ->vars["player"] = $_player ->vars["id"];
											  $aerror ->vars["msg"] = $_api->error_msg;
											  $aerror->insert();
										}
										
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
										$data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
										$result["balance"] = $data["balance"];
									}else{
										$balance = $_api->get_player_balance($player_token);	
										$result["balance"] = $balance["amount"];
									}									
									
									
									if(!$_api->done){ //error placing something in API
										  $aerror = new _api_error_log();
										  $aerror ->vars["session"] = $session_id;
										  $aerror ->vars["game"] = $game ->vars["id"];
										  $aerror ->vars["player"] = $_player ->vars["id"];
										  $aerror ->vars["msg"] = $_api->error_msg;
										  $aerror->insert();
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
					$result["msg"] = "To start the game you must place a bet on the Pass Line or Don't Pass Line.";
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