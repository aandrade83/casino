<? 
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/admin_security.php");

$type = $_CODEX->decrypt(param("lt",false));
$did = $_CODEX->decrypt(param("did",false));
$data = array();

switch($type){ 
	case "agent":
		$agent = get_agent($did);
		if(!is_null($agent)){
			echo json_encode(get_agent_limits($agent));
		}
	break;
	case "agent_games":
		$agent = get_agent($did);
		if(!is_null($agent)){
			echo json_encode(get_agent_games_limits($agent));
		}
	break;
	case "player":
		$player = get_player($did);
		if(!is_null($player)){
			echo json_encode(get_logged_player_limits($player));
		}
	break;
	case "player_games":
		$player = get_player($did);
		if(!is_null($player)){
			echo json_encode(get_player_games_limits($player));
		}
	break;
}

?>