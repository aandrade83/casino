<? 

$result = array();
$result["error"] = 0;
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/security.php");

$game_id = param("gid");

if($_logged){
	
	include("class.php"); 
	
	$action = param("action");
	$logic = new baccarat($_player ->vars["id"]);
	$game = $_player->get_allowed_game($game_id);
	
	if(!is_null($game) && $game ->vars["path"] == "baccarat"){
	
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
			
			case "deal":
			
				$bets = param("bets");
				$bet_amount = 0;
				$over_limit = false;
				
				$player_num = param("pnr"); //for provably fair
				if(!is_numeric($player_num) || $player_num > 100000000 || $player_num < 0){$player_num = 0;}
				$player_num = round($player_num);
				
				$bet_list = explode(",",$bets);
				foreach($bet_list as $bet){
					$parts = explode("|",$bet);
					if(is_numeric($parts[1]) && $parts[1]>0){
						$bet_amount += $parts[1];
						if($parts[1] < $game ->vars["min_amount"] || $parts[1] > $game ->vars["max_amount"]){
							$over_limit = true;
						}
					}
				}
				
				if(is_numeric($bet_amount)){
					
					if($_player->in_between_limits($bet_amount)){
						
						$balance = $_api->get_player_balance($player_token);
						if($bet_amount <= $balance["amount"]){
							
							if(/*$bet_amount >= $game ->vars["min_amount"] && $bet_amount <= $game ->vars["max_amount"]*/!$over_limit){
								$data = $_api->place_bet($player_token, $bet_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
								if($_api->done){
									
									//provably fair
									if($_company ->vars["prov_fair"]){
										
										$seed_data = discover_server_seed("bcrt");
										$pf_result = $seed_data["xnr"] + $player_num;
										if($pf_result > 100000000){$pf_result -= 100000000;}
										
										$result["pf"]["lxhs"] = $seed_data["xhs"];
										$result["pf"]["lsc1"] = $seed_data["xz1"];
										$result["pf"]["lsc2"] = $seed_data["xz2"];
										$result["pf"]["lxnr"] = $seed_data["xnr"];
										$result["pf"]["lpnr"] = $player_num;
										$result["pf"]["lpos"] = $pf_result;	
										
										$logic->start_pf($pf_result);
										
										$next_seed = generate_server_seed("bcrt",0,100000000);										
										$result["pf"]["nxnr"] = $next_seed;
										
									}
									
									$game_result = $logic->deal($bets);									
									$result["game_result"] = $game_result;									
									$win_amount = round($logic->win_amount,2);
									
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