<? 

$result = array();
$result["error"] = 0;
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/security.php" );

$game_id = param("gid");

if($_logged){
	
	include("class.php"); 
	
	$action = param("action");
	$logic = new multi_slot($_player ->vars["id"]);
	$game = $_player->get_allowed_game($game_id);
	
	
	if(!is_null($game) && $game ->vars["path"] == "multi_slot_alien"){
	
		switch($action){ 
			
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
			
			
			case "spin":
			
				$coin_value = param("bet");
				$lines = param("lines");
				
				//for provably fair---------------
				$player_nums = explode(",",param("pnr")); 
				$valid_player_nums = array();
				foreach($player_nums as $pnum){
					if(!is_numeric($pnum) || $pnum > 109 || $player_num < 0){$pnum = 0;} //change 109 in case of amount of figures in the reel change
					$valid_player_nums[] = $pnum;
				}
				$player_num = implode(",",$valid_player_nums);
				//-------------------------------
				
				
				$bet_amount = round($coin_value*$lines,2);
				
				if(is_numeric($bet_amount)){
					
					if($_player->in_between_limits($bet_amount)){
						
						$balance = $_api->get_player_balance($player_token);
						if($bet_amount <= $balance["amount"]){
							
							
							
							if($bet_amount >= $game ->vars["min_amount"] && $bet_amount <= $game ->vars["max_amount"]*25){
								
								$data = $_api->place_bet($player_token, $bet_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
								if($_api->done){
									
									
									
									if($_company ->vars["prov_fair"]){	
										//provalby fair
										$seed_data = discover_server_seed("alinv");
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
										
										$next_seed = generate_server_seed("alinv",0,0,implode(",",$hard_numbers));
										
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
										$data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
									}
									
									$result["balance"] = $data["balance"];
									
									if(!$_api->done){ //error placing something in API
										  $aerror = new _api_error_log();
										  $aerror ->vars["session"] = $session_id;
										  $aerror ->vars["game"] = $game ->vars["id"];
										  $aerror ->vars["player"] = $_player ->vars["id"];
										  $aerror ->vars["msg"] = $_api->error_msg;
										  $aerror->insert();
									}
									
								}else{
									$result["error"] = 800;
									$result["msg"] = "Comunication error: ". $_api->error_msg;
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