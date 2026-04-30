<? 
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/admin_security.php");

$type = $_CODEX->decrypt(param("lt",false));
$did = $_CODEX->decrypt(param("did",false));

switch($type){ 
	case "agent":
		$agent = get_agent($did);
		if(!is_null($agent)){
			$agent ->vars["day_max_win"] = -1;
			$agent ->vars["day_max_loss"] = -1;
			$agent ->vars["week_max_win"] = -1;
			$agent ->vars["week_max_loss"] = -1;
			$agent->update("day_max_win,day_max_loss,week_max_win,week_max_loss");
		}
	break;
	case "player":
		$player = get_player($did);
		if(!is_null($player)){
			$player ->vars["day_max_win"] = -1;
			$player ->vars["day_max_loss"] = -1;
			$player ->vars["week_max_win"] = -1;
			$player ->vars["week_max_loss"] = -1;
			$player->update("day_max_win,day_max_loss,week_max_win,week_max_loss");
		}
	break;
	case "agent_games":
		$agent = get_agent($did);
		if(!is_null($agent)){
			delete_all_agent_games($agent ->vars["id"]);
		}
		$type = "games&subtype=agent";
	break;
	case "player_games":
		$player = get_player($did);
		if(!is_null($player)){
			delete_all_player_games($player ->vars["id"]);
		}
		$type = "games&subtype=player";
	break;
}


header("Location: https://play.casinogamesonline.com/admin/limits.php?a=1&active=$type");

?>