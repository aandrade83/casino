<? 
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/admin_security.php");

$type = $_CODEX->decrypt(param("lt",false));

$day_max_win = param("max_win_day");
$day_max_loss = param("max_loss_day");
$week_max_win = param("max_win_week");
$week_max_loss = param("max_loss_week");

switch($type){ 
	case "company":
		if($_agent ->vars["manage_company"]){
			$company = get_company($_agent ->vars["company"]);
			$company ->vars["day_max_win"] = $day_max_win;
			$company ->vars["day_max_loss"] = $day_max_loss;
			$company ->vars["week_max_win"] = $week_max_win;
			$company ->vars["week_max_loss"] = $week_max_loss;
			$company->update("day_max_win,day_max_loss,week_max_win,week_max_loss");
		}
	break;
	case "agent":
		$data = param("agent",false);
		$parts = explode("__",$data);
		$idagent = $_CODEX->decrypt(urldecode($parts[0]));
		if(is_numeric($idagent)){$agent = get_agent($idagent);}
		if(!is_null($agent)){
			$agent ->vars["day_max_win"] = $day_max_win;
			$agent ->vars["day_max_loss"] = $day_max_loss;
			$agent ->vars["week_max_win"] = $week_max_win;
			$agent ->vars["week_max_loss"] = $week_max_loss;
			$agent->update("day_max_win,day_max_loss,week_max_win,week_max_loss");
		}
	break;
	case "player":
		$data = param("player",false);
		$parts = explode("__",$data);
		$idagent = $_CODEX->decrypt(urldecode($parts[0]));
		if(is_numeric($idagent)){$player = get_player($idagent);}
		if(!is_null($player)){
			if(is_numeric($day_max_win) && is_numeric($day_max_loss) && is_numeric($week_max_win) && is_numeric($week_max_loss)){
				$player ->vars["day_max_win"] = $day_max_win;
				$player ->vars["day_max_loss"] = $day_max_loss;
				$player ->vars["week_max_win"] = $week_max_win;
				$player ->vars["week_max_loss"] = $week_max_loss;
			}
			
			$temp_day_max_win = param("tmax_win_day");
			$temp_day_max_loss = param("tmax_loss_day");
			$temp_week_max_win = param("tmax_win_week");
			$temp_week_max_loss = param("tmax_loss_week");
			$temp_expire = param("expire_date");
			
			if(is_numeric($temp_day_max_win) && is_numeric($temp_day_max_loss) && is_numeric($temp_week_max_win) && is_numeric($temp_week_max_loss)){
				$player ->vars["temp_day_max_win"] = $temp_day_max_win;
				$player ->vars["temp_day_max_loss"] = $temp_day_max_loss;
				$player ->vars["temp_week_max_win"] = $temp_week_max_win;
				$player ->vars["temp_week_max_loss"] = $temp_week_max_loss;
				$player ->vars["temp_limit_expiration"] = $temp_expire;
			}
			
			
			$player->update("day_max_win,day_max_loss,week_max_win,week_max_loss,temp_day_max_win,temp_day_max_loss,temp_week_max_win,temp_week_max_loss,temp_limit_expiration");
		}
	break;
	case "games":
	
		$games_type = $_CODEX->decrypt(param("glt"));
		
		switch($games_type){ 
			case "casino":
				
				if($_agent ->vars["manage_company"]){
					$games = get_all_company_games($_agent ->vars["company"]);
					
					foreach($games as $game){
					
						$active = param($game ->vars["id"]."_active");
						$min = param($game ->vars["id"]."_min_amount");
						$max = param($game ->vars["id"]."_max_amount");
						
						update_company_game_limit($_agent ->vars["company"], $game ->vars["id"], $active, $min, $max);
						
					}
					
				}
						
			break;
			case "agent":
				
				$games = get_all_company_games($_agent ->vars["company"]);
				$glagent = get_agent( $_CODEX->decrypt(urldecode(param("gl_agent",false))) );
				
				
					
				if(!is_null($glagent)){
					
					delete_all_agent_games($glagent ->vars["id"]);
					foreach($games as $game){
					
						$active = param($game ->vars["id"]."_active");
						$min = param($game ->vars["id"]."_min_amount");
						$max = param($game ->vars["id"]."_max_amount");
						
						insert_agent_game_limit($glagent ->vars["id"], $game ->vars["id"], $active, $min, $max);
						
					}
				
				}
				$type .= "&subtype=agent";
						
			break;
			case "player":
				
				$games = get_all_company_games($_agent ->vars["company"]);
				$glplayer = get_player( $_CODEX->decrypt(urldecode(param("gl_player",false))) );
				
				if(!is_null($glplayer)){
					
					delete_all_player_games($glplayer ->vars["id"]);
					foreach($games as $game){
					
						$active = param($game ->vars["id"]."_active");
						$min = param($game ->vars["id"]."_min_amount");
						$max = param($game ->vars["id"]."_max_amount");
						
						insert_player_game_limit($glplayer ->vars["id"], $game ->vars["id"], $active, $min, $max);
						
					}
				
				}
				
				$type .= "&subtype=player";
						
			break;
		}
	break;
}


header("Location: https://play.casinogamesonline.com/admin/limits.php?a=1&active=$type");

?>