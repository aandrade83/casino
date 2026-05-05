<? 

$result = array();
$result["error"] = 0;
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/security.php");

$game_id = param("gid");

if($_logged){
	
	include("class.php"); 
	
	$action = param("action");
	$logic = new blackjack_sh($_player ->vars["id"]);
	$game = $_player->get_allowed_game($game_id);
	
	if(!is_null($game) && $game ->vars["path"] == "blackjack_new"){
	
	switch($action){ 
		case "start":
			//start game, check if there is an started game and load it
			$session = get_blackjack_session_by_player($_player ->vars["id"]);
			if(!is_null($session)){
				$logic->load_data($session);
				
				$result["has_started_game"]	= 1;
				$result["player_hand"] = implode(",",$logic ->player_hand);
				$result["player_hand2"] = implode(",",$logic ->player_hand2);
				$result["dealer_hand"] = $logic ->dealer_hand[1];
				$result["status"] = $logic->game_status;
				$result["status2"] = $logic->game_status2;
				$result["bet_amount"] = $logic->bet_amount;
				$result["bet_amount2"] = $logic->bet_amount2;
				$result["player_hand_value"] = $logic->get_hand_value();
				$result["player_hand_value2"] = $logic->get_hand_value("player2");
				$result["can_split"] = $logic->can_split();
				$result["splited"] = $logic->splited;
				$result["finished2"] = $session->vars["finished2"];
				
				if($logic ->using_pf){
					$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
					$result["pf"]["lpnr"] = $logic->pf_data["pnr"];	
				}
				
			}else{
				$result["has_started_game"]	= 0;
			}
		break;
		
		case "game_data":
			$session = get_blackjack_session_by_player($_player ->vars["id"],false);
			if(!is_null($session)){
				$logic->load_data($session);			
				
				$result["dealer_hidden_card"] = $logic ->dealer_hand[0];
				$result["dealer_hand_value"] = $logic->get_hand_value("dealer");
				$balance = $_api->get_player_balance($player_token);
				if(!is_numeric($balance["amount"])){$balance["amount"] = 0;}
				$result["balance"] = $balance["amount"];
				$result["win_amount"] = $logic->win_amount;
				$result["dealer_hand"] = implode(",",$logic ->dealer_hand);
				$result["win_amount2"] = $logic->win_amount2;
				$result["splited"] = $logic->splited;
				$result["bet_amount"] = $logic->bet_amount;
				$result["bet_amount2"] = $logic->bet_amount2;
				$result["status"] = $logic->game_status;
				$result["finished"] = $session->vars["finished"];			
				
			}else{
				$result["error"] = 1000;
				$result["msg"] = "Session not found";
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
		case "fold":
			$session = get_blackjack_session_by_player($_player ->vars["id"]);
			if(!is_null($session)){
				$logic->load_data($session);
				if($session ->vars["game_status"] == "dealed"){
					$win_amount = $logic ->fold();
					if(!$_using_free_play){
						$data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
					}else{
					  $balance = $_api->get_player_balance($player_token);
					  $data["balance"] = $balance["amount"];  
					}
					
					$logic->store_settle_log($win_amount);
					
					if(!$_api->done){ //error placing something in API
						$aerror = new _api_error_log();
						$aerror ->vars["session"] = $session ->vars["id"];
						$aerror ->vars["game"] = $game ->vars["id"];
						$aerror ->vars["player"] = $_player ->vars["id"];
						$aerror ->vars["msg"] = $_api->error_msg;
						$aerror->insert();
					}
					
					if(!is_numeric($data["balance"])){$data["balance"] = 0;}
					$result["balance"] = $data["balance"];
					$result["win_amount"] = $win_amount;
					$result["finished"] = $logic ->game_finished;
					$result["dealer_hidden_card"] = $logic ->dealer_hand[0];
					$result["dealer_hand_value"] = $logic->get_hand_value("dealer");
					$result["status"] = $logic ->game_status;
					
					$result["splited"] = $logic->splited;
					$result["win_amount2"] = $logic->win_amount2;
					$result["status2"] = $logic->game_status2;
					$result["bet_amount2"] = $logic->bet_amount2;
					$result["dealer_hand"] = implode(",",$logic ->dealer_hand);
					$result["second_hand"] = $logic->move_to_second_hand();
					
					//Provably fair
					if($logic->game_finished && !$result["second_hand"]){
						if($_company ->vars["prov_fair"]){
							
							$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
							$result["pf"]["lsc1"] = $logic->pf_data["xz1"];
							$result["pf"]["lsc2"] = $logic->pf_data["xz2"];
							$result["pf"]["lxnr"] = $logic->pf_data["xnr"];
							$result["pf"]["lpnr"] = $logic->pf_data["pnr"];
							$result["pf"]["lpos"] = $logic->pf_data["pos"];
							
							$next_seed = generate_server_seed("bljk",0,100000000);										
							$result["pf"]["nxnr"] = $next_seed;
							
						}
					}
					
					
				}else{
					$result["error"] = 1100;
					$result["msg"] = "Cant execute this action";
				}
			}else{
				$result["error"] = 1000;
				$result["msg"] = "Session not found";
			}
		break;
		case "hit":
			$session = get_blackjack_session_by_player($_player ->vars["id"]);
			if(!is_null($session)){
				$logic->load_data($session);
				if($session ->vars["game_status"] == "dealed" || $session ->vars["game_status"] == "hitted"){
					
					$new_card = $logic->hit();
					$result["new_card"] = $new_card;
					$result["player_hand_value"] = $logic->get_hand_value();
					$result["status"] = $logic->game_status;
					$result["finished"] = $logic->game_finished;
					
					switch($logic->game_status){ 
						case 'push':
							  $win_amount = $logic->bet_amount;
							  if(!$_using_free_play){
							  	$data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
							  }else{
								$balance = $_api->get_player_balance($player_token);
							    $data["balance"] = $balance["amount"];  
							  }
							  $logic->store_settle_log($win_amount);									  
						break;
						case 'dealer_bust':
						case 'win':
							  if(!$_using_free_play){
							  	  $win_amount = $logic->win_amount + $logic->bet_amount;
							  }else{
								  $win_amount = $logic->win_amount;
							  }
							  $data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
							  $logic->store_settle_log($win_amount);
						break;
						case 'busted':
							  $balance = $_api->get_player_balance($player_token);
							  $data["balance"] = $balance["amount"];
						break;
					}
					
					
					if(!$_api->done){ //error placing something in API
						  $aerror = new _api_error_log();
						  $aerror ->vars["session"] = $session ->vars["id"];
						  $aerror ->vars["game"] = $game ->vars["id"];
						  $aerror ->vars["player"] = $_player ->vars["id"];
						  $aerror ->vars["msg"] = $_api->error_msg;
						  $aerror->insert();
					}
					
					if($logic->game_finished){
						$result["dealer_hidden_card"] = $logic ->dealer_hand[0];
						$result["dealer_hand_value"] = $logic->get_hand_value("dealer");
						if(!is_numeric($data["balance"])){$data["balance"] = 0;}
						$result["balance"] = $data["balance"];
						$result["win_amount"] = $logic->win_amount;
						$result["dealer_hand"] = implode(",",$logic ->dealer_hand);
						
						$result["splited"] = $logic->splited;
						$result["win_amount2"] = $logic->win_amount2;
						$result["status2"] = $logic->game_status2;
						$result["bet_amount2"] = $logic->bet_amount2;
						
					}
					
					$result["second_hand"] = $logic->move_to_second_hand(true);
					
					//Provably fair
					if($logic->game_finished && !$result["second_hand"]){
						if($_company ->vars["prov_fair"]){
							
							$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
							$result["pf"]["lsc1"] = $logic->pf_data["xz1"];
							$result["pf"]["lsc2"] = $logic->pf_data["xz2"];
							$result["pf"]["lxnr"] = $logic->pf_data["xnr"];
							$result["pf"]["lpnr"] = $logic->pf_data["pnr"];
							$result["pf"]["lpos"] = $logic->pf_data["pos"];
							
							$next_seed = generate_server_seed("bljk",0,100000000);										
							$result["pf"]["nxnr"] = $next_seed;
							
						}
					}
					
					
					
					
				}else{
					$result["error"] = 1100;
					$result["msg"] = "Cant execute this action";
				}
			}else{
				$result["error"] = 1000;
				$result["msg"] = "Session not found";
			}
		break;
		
		case "double":
			$session = get_blackjack_session_by_player($_player ->vars["id"]);
			if(!is_null($session)){
				
				$logic->load_data($session);
				if($session ->vars["game_status"] == "dealed"){
					
					//if($_player->in_between_limits($session ->vars["bet_amount"])){
					
						$balance = $_api->get_player_balance($player_token);
						if($session ->vars["bet_amount"] <= $balance["amount"]){
						
							$data = $_api->place_bet($player_token, $session ->vars["bet_amount"], $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
							$logic->store_settle_log($session ->vars["bet_amount"]*-1);
							
							if($_api->done){
							
								$new_card = $logic->double();
								$result["new_card"] = $new_card;
								$result["player_hand_value"] = $logic->get_hand_value();
								$result["status"] = $logic->game_status;
								$result["finished"] = $logic->game_finished;
								$result["bet_amount"] = $logic->bet_amount;
								
								switch($logic->game_status){ 
									case 'push':
										  $win_amount = $logic->bet_amount;
										  if(!$_using_free_play){
										  		$data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);	
										  }else{
											$balance = $_api->get_player_balance($player_token);
											$data["balance"] = $balance["amount"];  
										  }
										  $logic->store_settle_log($win_amount);								  
									break;
									case 'dealer_bust':
									case 'win':
										  if(!$_using_free_play){
											  $win_amount = $logic->win_amount + $logic->bet_amount;
										  }else{
											  $win_amount = $logic->win_amount;
										  }
										  
										  $data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
										  $logic->store_settle_log($win_amount);
									break;
									case 'lose':
									case 'busted':
										  $balance = $_api->get_player_balance($player_token);
										  $data["balance"] = $balance["amount"];
									break;
								}
								
								
								if(!$_api->done){ //error placing something in API
									  $aerror = new _api_error_log();
									  $aerror ->vars["session"] = $session ->vars["id"];
									  $aerror ->vars["game"] = $game ->vars["id"];
									  $aerror ->vars["player"] = $_player ->vars["id"];
									  $aerror ->vars["msg"] = $_api->error_msg;
									  $aerror->insert();
								}
								
								if($logic->game_finished){
									$result["dealer_hidden_card"] = $logic ->dealer_hand[0];
									$result["dealer_hand_value"] = $logic->get_hand_value("dealer");
									if(!is_numeric($data["balance"])){$data["balance"] = 0;}
									$result["balance"] = $data["balance"];
									$result["win_amount"] = $logic->win_amount;
									$result["dealer_hand"] = implode(",",$logic ->dealer_hand);
									
									$result["splited"] = $logic->splited;
									$result["win_amount2"] = $logic->win_amount2;
									$result["status2"] = $logic->game_status2;
									$result["bet_amount2"] = $logic->bet_amount2;
								}
								
								$result["second_hand"] = $logic->move_to_second_hand();
								
								//Provably fair
								if($logic->game_finished && !$result["second_hand"]){
									if($_company ->vars["prov_fair"]){
										
										$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
										$result["pf"]["lsc1"] = $logic->pf_data["xz1"];
										$result["pf"]["lsc2"] = $logic->pf_data["xz2"];
										$result["pf"]["lxnr"] = $logic->pf_data["xnr"];
										$result["pf"]["lpnr"] = $logic->pf_data["pnr"];
										$result["pf"]["lpos"] = $logic->pf_data["pos"];
										
										$next_seed = generate_server_seed("bljk",0,100000000);										
										$result["pf"]["nxnr"] = $next_seed;
										
									}
								}
							
							}else{
								$result["error"] = 1301;
								$result["msg"] = "Comunication error: ". $_api->error_msg;
							}
						
						}else{
							$result["no_enough_balance"] = 1;
							$result["error"] = 2500;
							$result["msg"] = "Not enough balance to double";
						}
					
					/*}else{
						$result["not_between_limits"] = 1;
						$result["error"] = 3745;
						$result["msg"] = "You reached the Casino allowed limits.";
					}*/
					
				}else{
					$result["error"] = 1100;
					$result["msg"] = "Cant execute this action";
				}
			}else{
				$result["error"] = 1000;
				$result["msg"] = "Session not found";
			}
		break;
		
		case "split":
			$session = get_blackjack_session_by_player($_player ->vars["id"]);
			if(!is_null($session)){
				$logic->load_data($session);
				if($session ->vars["game_status"] == "dealed" && $logic->can_split()){
					
					//if($_player->in_between_limits($session ->vars["bet_amount"])){
					
						$balance = $_api->get_player_balance($player_token);
						if($session ->vars["bet_amount"] <= $balance["amount"]){
						
							$data = $_api->place_bet($player_token, $session ->vars["bet_amount"], $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
							$logic->store_settle_log($session ->vars["bet_amount"]*-1);
							
							if($_api->done){
							
								$logic->split_hand();
								$result["player_hand_value"] = $logic->get_hand_value();
								$result["player_hand_value2"] = $logic->get_hand_value("player2");
								$result["player_hand"] = implode(",",$logic ->player_hand);
								$result["player_hand2"] = implode(",",$logic ->player_hand2);
								
								$result["finished"] = $logic->game_finished;
								$result["finished2"] = $logic->temp_game_finished2;
								$result["status"] = $logic->game_status;
								$result["status2"] = $logic->game_status2;
								
								if($logic->game_status == "player_blackjack"){
									if(!$_using_free_play){
										$win_amount = $logic->bet_amount + $logic->win_amount;
									}else{
										$win_amount = $logic->win_amount;
									}
									
									$data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
									$logic->store_settle_log($win_amount);
								}
								
								
								if(!$_api->done){ //error placing something in API
									  $aerror = new _api_error_log();
									  $aerror ->vars["session"] = $session ->vars["id"];
									  $aerror ->vars["game"] = $game ->vars["id"];
									  $aerror ->vars["player"] = $_player ->vars["id"];
									  $aerror ->vars["msg"] = $_api->error_msg;
									  $aerror->insert();
								}
								
								if($logic->game_finished){
									$temp_hand = $logic->player_hand;
									$logic->player_hand = $logic->player_hand2;
									$logic->player_hand2 = $temp_hand;
								}
								
								$is_second = $logic->move_to_second_hand();
								
								//Provably fair
								if($logic->game_finished && !$is_second){
									if($_company ->vars["prov_fair"]){
										
										$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
										$result["pf"]["lsc1"] = $logic->pf_data["xz1"];
										$result["pf"]["lsc2"] = $logic->pf_data["xz2"];
										$result["pf"]["lxnr"] = $logic->pf_data["xnr"];
										$result["pf"]["lpnr"] = $logic->pf_data["pnr"];
										$result["pf"]["lpos"] = $logic->pf_data["pos"];
										
										$next_seed = generate_server_seed("bljk",0,100000000);										
										$result["pf"]["nxnr"] = $next_seed;
										
									}
								}
								
							
							}else{
								$result["error"] = 1301;
								$result["msg"] = "Comunication error: ". $_api->error_msg;
							}
						
						}else{
							$result["no_enough_balance"] = 1;
							$result["error"] = 2500;
							$result["msg"] = "Not enough balance to split";
						}
					
					/*}else{
						$result["not_between_limits"] = 1;
						$result["error"] = 3745;
						$result["msg"] = "You reached the Casino allowed limits.";
					}*/
					
				}else{
					$result["error"] = 1100;
					$result["msg"] = "Cant execute this action";
				}
			}else{
				$result["error"] = 1000;
				$result["msg"] = "Session not found";
			}
		break;
		
		case "insurance":
			$session = get_blackjack_session_by_player($_player ->vars["id"]);
			$accepted = param("accepted");
			if(!is_null($session)){
				$logic->load_data($session);
				if($session ->vars["game_status"] == "ask_insurance"){
					
					//if($_player->in_between_limits($session ->vars["bet_amount"]/2)){
					
						$balance = $_api->get_player_balance($player_token);
						if($session ->vars["bet_amount"]/2 <= $balance["amount"] || !$accepted){
						
							if($accepted){ 
								$data = $_api->place_bet($player_token, $session ->vars["bet_amount"]/2, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);	
								$logic->store_settle_log(($session ->vars["bet_amount"]/2)*-1);
							}
							
							if($_api->done || !$accepted){
							
								$logic->insurance($accepted);
								
								if($logic->win_amount > 0 && $logic->game_finished){
									if(!$_using_free_play){
										$win_amount = ($logic->bet_amount/2) + $logic->win_amount;
									}else{
										$win_amount = $logic->win_amount;
									}
									
									$data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);	
									$logic->store_settle_log($win_amount);
								}else{
									$balance = $_api->get_player_balance($player_token);
									$data["balance"] = $balance["amount"];	
								}
								
								
								if(!$_api->done){ //error placing something in API
									  $aerror = new _api_error_log();
									  $aerror ->vars["session"] = $session ->vars["id"];
									  $aerror ->vars["game"] = $game ->vars["id"];
									  $aerror ->vars["player"] = $_player ->vars["id"];
									  $aerror ->vars["msg"] = $_api->error_msg;
									  $aerror->insert();
								}
								
								$result["status"] = $logic->game_status;
								$result["finished"] = $logic->game_finished;
								$result["can_split"] = $logic->can_split();
								$result["balance"] = $data["balance"];
								
								if($logic->game_finished){
									$result["dealer_hidden_card"] = $logic ->dealer_hand[0];
									$result["dealer_hand_value"] = $logic->get_hand_value("dealer");
									if(!is_numeric($data["balance"])){$data["balance"] = 0;}
									$result["win_amount"] = $logic->win_amount;
									
									
									//Provably fair
									if($_company ->vars["prov_fair"]){
										
										$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
										$result["pf"]["lsc1"] = $logic->pf_data["xz1"];
										$result["pf"]["lsc2"] = $logic->pf_data["xz2"];
										$result["pf"]["lxnr"] = $logic->pf_data["xnr"];
										$result["pf"]["lpnr"] = $logic->pf_data["pnr"];
										$result["pf"]["lpos"] = $logic->pf_data["pos"];
										
										$next_seed = generate_server_seed("bljk",0,100000000);										
										$result["pf"]["nxnr"] = $next_seed;
										
									}
									
								}
								
							
							}else{
								$result["error"] = 1302;
								$result["msg"] = "Comunication error: ". $_api->error_msg;
							}
							
							
						
						}else{
							$result["no_enough_balance"] = 1;
							$result["error"] = 2550;
							$result["msg"] = "Not enough balance to play insurance";
						}
					
					/*}else{
						$result["not_between_limits"] = 1;
						$result["error"] = 3745;
						$result["msg"] = "You reached the Casino allowed limits.";
					}*/
					
				}else{
					$result["error"] = 1100;
					$result["msg"] = "Cant execute this action";
				}
			}else{
				$result["error"] = 1000;
				$result["msg"] = "Session not found";
			}
		break;
		
		case "stand":
			$session = get_blackjack_session_by_player($_player ->vars["id"]);
			if(!is_null($session)){
				$logic->load_data($session);
				if($session ->vars["game_status"] == "dealed" || $session ->vars["game_status"] == "hitted"){
					
					$logic->stand();
					$result["status"] = $logic->game_status;
					$result["finished"] = $logic->game_finished;
					
					switch($logic->game_status){ 
						case 'push':
							  $win_amount = $logic->bet_amount;
							  if(!$_using_free_play){
							  	$data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);	
							  }else{
								$balance = $_api->get_player_balance($player_token);
							    $data["balance"] = $balance["amount"];  
							  }
							  $logic->store_settle_log($win_amount);								  
						break;
						case 'dealer_bust':
						case 'win':
							   if(!$_using_free_play){
									$win_amount = $logic->win_amount + $logic->bet_amount;   
							   }else{
								   $win_amount = $logic->win_amount;
							   }
							  
							  $data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
							  $logic->store_settle_log($win_amount);
						break;
						case 'lose':
							  $balance = $_api->get_player_balance($player_token);
							  $data["balance"] = $balance["amount"];
						break;
					}
					
					
					if(!$_api->done){ //error placing something in API
						  $aerror = new _api_error_log();
						  $aerror ->vars["session"] = $session ->vars["id"];
						  $aerror ->vars["game"] = $game ->vars["id"];
						  $aerror ->vars["player"] = $_player ->vars["id"];
						  $aerror ->vars["msg"] = $_api->error_msg;
						  $aerror->insert();
					}
					
					if($logic->game_finished){
						$result["dealer_hidden_card"] = $logic ->dealer_hand[0];
						$result["dealer_hand_value"] = $logic->get_hand_value("dealer");
						if(!is_numeric($data["balance"])){$data["balance"] = 0;}
						$result["balance"] = $data["balance"];
						$result["win_amount"] = $logic->win_amount;
						$result["dealer_hand"] = implode(",",$logic ->dealer_hand);
						
						$result["splited"] = $logic->splited;
						$result["win_amount2"] = $logic->win_amount2;
						$result["status2"] = $logic->game_status2;
						$result["bet_amount2"] = $logic->bet_amount2;
					}
					
					$result["second_hand"] = $logic->move_to_second_hand();
					
					//Provably fair
					if($logic->game_finished && !$result["second_hand"]){
						if($_company ->vars["prov_fair"]){
							
							$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
							$result["pf"]["lsc1"] = $logic->pf_data["xz1"];
							$result["pf"]["lsc2"] = $logic->pf_data["xz2"];
							$result["pf"]["lxnr"] = $logic->pf_data["xnr"];
							$result["pf"]["lpnr"] = $logic->pf_data["pnr"];
							$result["pf"]["lpos"] = $logic->pf_data["pos"];
							
							$next_seed = generate_server_seed("bljk",0,100000000);										
							$result["pf"]["nxnr"] = $next_seed;
							
						}
					}
					
					
				}else{
					$result["error"] = 1100;
					$result["msg"] = "Cant execute this action";
				}
			}else{
				$result["error"] = 1000;
				$result["msg"] = "Session not found";
			}
		break;
		
		case "deal":
			$bet_amount = param("bet");
			
			$player_num = param("pnr"); //for provably fair
			if(!is_numeric($player_num) || $player_num > 100000000 || $player_num < 0){$player_num = 0;}
			$player_num = round($player_num);
			
			if(is_numeric($bet_amount)){
				//test if speed is slow
				$session = get_blackjack_session_by_player($_player ->vars["id"]);
				if(is_null($session)){
					
					if($_player->in_between_limits($bet_amount)){
					
						$balance = $_api->get_player_balance($player_token);
						if($bet_amount <= $balance["amount"]){
							
							if($bet_amount >= $game ->vars["min_amount"] && $bet_amount <= $game ->vars["max_amount"]){
								
								//place the bet
								$data = $_api->place_bet($player_token, $bet_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
								$logic->store_settle_log($bet_amount*-1);
								
								if($_api->done){
									
									
									//provably fair
									if($_company ->vars["prov_fair"]){
										$seed_data = discover_server_seed("bljk");
										$pf_result = $seed_data["xnr"] + $player_num;										
										if($pf_result > 100000000){$pf_result -= 100000000;}
										$seed_data["pnr"] = $player_num;
										$seed_data["pos"] = $pf_result;
										$logic->start_pf($pf_result,$seed_data);									
									}
									
									$logic->bet_amount = $bet_amount;
									$session_id = $logic->deal();
									$result["player_hand"] = implode(",",$logic ->player_hand);
									$result["dealer_hand"] = $logic ->dealer_hand[1];
									$result["status"] = $logic->game_status;
									
									switch($logic->game_status){ 
										case 'win':
										case 'dealer_bust':
										case 'player_blackjack':
											  if(!$_using_free_play){
												  $win_amount = $bet_amount + $logic->win_amount;
											  }else{
												  $win_amount = $logic->win_amount;
											  }
											  
											  $data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);	
											  $logic->store_settle_log($win_amount);								  
										break;
										case 'push':
										case 'push_blackjack':
											  $win_amount = $bet_amount;
											  if(!$_using_free_play){
											  	  $data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
											  }else{
												$balance = $_api->get_player_balance($player_token);
												$data["balance"] = $balance["amount"];  
											  }
											  $logic->store_settle_log($win_amount);
										break;
									}
									
									if(!$_api->done){ //error placing something in API
										  $aerror = new _api_error_log();
										  $aerror ->vars["session"] = $session_id;
										  $aerror ->vars["game"] = $game ->vars["id"];
										  $aerror ->vars["player"] = $_player ->vars["id"];
										  $aerror ->vars["msg"] = $_api->error_msg;
										  $aerror->insert();
									}
									
									if(!is_numeric($data["balance"])){$data["balance"] = 0;}
									$result["balance"] = $data["balance"];
									$result["win_amount"] = $logic->win_amount;
									$result["finished"] = $logic->game_finished;
									$result["player_hand_value"] = $logic->get_hand_value();
									$result["can_split"] = $logic->can_split();
									if($logic->game_finished){
										$result["dealer_hidden_card"] = $logic ->dealer_hand[0];
										$result["dealer_hand_value"] = $logic->get_hand_value("dealer");
										$result["dealer_full_hand"] = implode(",",$logic ->dealer_hand);
										
										//provably fair
										if($_company ->vars["prov_fair"]){ 
											
											$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
											$result["pf"]["lsc1"] = $logic->pf_data["xz1"];
											$result["pf"]["lsc2"] = $logic->pf_data["xz2"];
											$result["pf"]["lxnr"] = $logic->pf_data["xnr"];
											$result["pf"]["lpnr"] = $logic->pf_data["pnr"];
											$result["pf"]["lpos"] = $logic->pf_data["pos"];
											
											$next_seed = generate_server_seed("bljk",0,100000000);										
											$result["pf"]["nxnr"] = $next_seed;
											
										}
										
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
					$result["error"] = 400;
					$result["msg"] = "Player has unfinished games.";
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