<? 

$result = array();
$result["error"] = 0;
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/security.php");

$game_id = param("gid");

if($_logged){
	
	include("class.php"); 
	
	$action = param("action");
	$logic = new video_poker($_player ->vars["id"]);
	$game = $_player->get_allowed_game($game_id);
	
	if(!is_null($game) && $game ->vars["path"] == "video_poker_af"){
	
		switch($action){ 
			case "start":
				$session = get_video_poker_session_by_player($_player ->vars["id"],"afs");
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
				$coins = param("coins"); //hands
				$total_bet = $bet_amount*$coins;
				$posible_coins = array(1,5,10,25,50);
				
				$player_num = param("pnr"); //for provably fair
				if(!is_numeric($player_num) || $player_num > 100000000 || $player_num < 0){$player_num = 0;}
				$player_num = round($player_num);
				
				if(is_numeric($total_bet) && is_numeric($bet_amount) && in_array($coins*1,$posible_coins)){
					
					$session = get_video_poker_session_by_player($_player ->vars["id"],"afs");
					if(is_null($session)){
						
						if($_player->in_between_limits($total_bet)){
						
							$balance = $_api->get_player_balance($player_token);
							if($total_bet <= $balance["amount"]){
								
								if($bet_amount >= $game ->vars["min_amount"] && $bet_amount <= $game ->vars["max_amount"]){
									
									//Place Bet
									$data = $_api->place_bet($player_token, $total_bet, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
									if($_api->done){
									
										//provably fair
										if($_company ->vars["prov_fair"]){
											$seed_data = discover_server_seed("vdpkraf");
											$pf_result = $seed_data["xnr"] + $player_num;										
											if($pf_result > 100000000){$pf_result -= 100000000;}
											$seed_data["pnr"] = $player_num;
											$seed_data["pos"] = $pf_result;
											$logic->start_pf($pf_result,$seed_data);									
										}
										
										$cards = $logic ->deal($bet_amount,$coins);
										$result["balance"] = $data["balance"];
										$result["cards"] = $cards;
										
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
			
			case "draw":
				$holds = param("holds");
				$session = get_video_poker_session_by_player($_player ->vars["id"],"afs");
				if(!is_null($session)){
					$logic->load_data($session);					
					if(!$logic ->session ->vars["finished"]){
						
						$hands = $logic->draw($holds);
						$win_amount = $logic->win_amount;
						
						if($win_amount > 0){
							$data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
						}else{
							$balance = $_api->get_player_balance($player_token);
							$data["balance"] = $balance["amount"];
						}
						
						$result["balance"] = $data["balance"];
						$result["win_amount"] = $win_amount;
						$result["win_level"] = $logic->win_level;
						$result["cards"] = implode("|",$hands);
						
						//provably fair
						if($_company ->vars["prov_fair"]){
							
							$result["pf"]["lxhs"] = $logic->pf_data["xhs"];
							$result["pf"]["lsc1"] = $logic->pf_data["xz1"];
							$result["pf"]["lsc2"] = $logic->pf_data["xz2"];
							$result["pf"]["lxnr"] = $logic->pf_data["xnr"];
							$result["pf"]["lpnr"] = $logic->pf_data["pnr"];
							$result["pf"]["lpos"] = $logic->pf_data["pos"];
							
							$next_seed = generate_server_seed("vdpkraf",0,100000000);										
							$result["pf"]["nxnr"] = $next_seed;
							
						}
						
						if(!$_api->done && $win_amount > 0){ //error placing something in API
							  $aerror = new _api_error_log();
							  $aerror ->vars["session"] = $session_id;
							  $aerror ->vars["game"] = $game ->vars["id"];
							  $aerror ->vars["player"] = $_player ->vars["id"];
							  $aerror ->vars["msg"] = $_api->error_msg;
							  $aerror->insert();
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