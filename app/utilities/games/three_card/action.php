<? 

$result = array();
$result["error"] = 0;
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/security.php");

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
				$balance = $_api->get_player_balance($player_token);
				if($_api->done){
					if(!is_numeric($balance["amount"])){$balance["amount"] = 0;}
					$result["balance"] = $balance["amount"];
				}else{
					$result["error"] = 900;
					$result["msg"] = "Comunication error: ". $_api->error_msg;
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
						
							$balance = $_api->get_player_balance($player_token);
							if((($bet_amount*2)+ $ppbet_amount) <= $balance["amount"]){// checks for balance for ante and bet
								
								if(
								(($bet_amount >= $game ->vars["min_amount"] && $bet_amount <= $game ->vars["max_amount"]) || $bet_amount == 0) &&  //Ante bet limits or 0
								(($ppbet_amount >= $game ->vars["min_amount"] && $ppbet_amount <= $game ->vars["max_amount"]) || $ppbet_amount == 0) && //PP bet limits or 0
								($bet_amount+$ppbet_amount) >= $game ->vars["min_amount"] //Ante + pp in min limits
								){ //checks ante
									
									//Place Bet
									$data = $_api->place_bet($player_token, ($bet_amount+$ppbet_amount), $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
									if($_api->done){
									
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
										$result["balance"] = $data["balance"];
										$result["cards"] = $deal_result["cards"];
										$result["player_level"] = $deal_result["player_level"];
										$result["game_status"] = $deal_result["game_status"];
										$result["pp_status"] = $deal_result["pp_status"];
										$result["pp_win_amount"] = round($deal_result["pp_credit_amount"],2);
										
										
										if($deal_result["pp_credit_amount"]){
											if($_using_free_play){
											    $data = $_api->credit_prize($player_token, $deal_result["pp_credit_amount"], $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
											}else{
												$data = $_api->credit_prize($player_token, $deal_result["pp_win_amount"], $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);	
											}
											
										}
										
										$result["balance"] = $data["balance"];  
										
										
										
										if($logic ->using_pf){
											
											//PF
											$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
											$result["pf"]["lsc1"] = $logic->pf_data["xz1"];
											$result["pf"]["lsc2"] = $logic->pf_data["xz2"];
											$result["pf"]["lxnr"] = $logic->pf_data["xnr"]; 
											$result["pf"]["lpnr"] = $logic->pf_data["pnr"];
											$result["pf"]["lpos"] = $logic->pf_data["pos"];
											
											$next_seed = generate_server_seed("tcpkr",0,100000000);										
											$result["pf"]["nxnr"] = $next_seed;
										
										}
										
										//insert win pp, generate pf if finished
										
									}else{
										$result["error"] = 800;
										$result["msg"] = "Comunication error: ". $_api->error_msg;
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
						
						$balance = $_api->get_player_balance($player_token);
						if($bet_amount <= $balance["amount"]){				
												
							if(!$logic ->session ->vars["finished"]){
								
								if($logic ->session ->vars["status"] == "dealed"){
								
									$data = $_api->place_bet($player_token, $bet_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
									if($_api->done){
									
										$result["balance"] = $data["balance"];  
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
											    $data = $_api->credit_prize($player_token, $credit_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
											}else{
												$data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);	
											}
										}else{
										  $balance = $_api->get_player_balance($player_token);
										  $data["balance"] = $balance["amount"];  
										}
										
										$result["balance"] = $data["balance"];
										
										
										if($logic ->using_pf){
											
											//PF
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
										$result["msg"] = "Comunication error: ". $_api->error_msg;
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
						
						$balance = $_api->get_player_balance($player_token);
						if(!$bet || $bet_amount <= $balance["amount"]){				
												
							if(!$logic ->session ->vars["finished"]){
								
								if($logic ->session ->vars["status"] == "turned"){
								
									if($bet){$data = $_api->place_bet($player_token, $bet_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);}
									if(!$bet || $_api->done){
									
										
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
											    $data = $_api->credit_prize($player_token, $credit_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
											}else{
												$data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);	
											}
										}else{
										  $balance = $_api->get_player_balance($player_token);
										  $data["balance"] = $balance["amount"];  
										}
										
										$result["balance"] = $data["balance"];
										$result["best_player_game"] = $logic->player_hand_level;
										$result["best_dealer_game"] = $logic->dealer_hand_level;
										
										
										if($logic ->using_pf){
											
											//PF
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
										$result["msg"] = "Comunication error: ". $_api->error_msg;
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
						
							$balance = $_api->get_player_balance($player_token);
							$result["balance"] = $balance["amount"]; 
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