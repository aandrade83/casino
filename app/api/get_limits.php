<? 
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/includes.php");

$type = param("type");
$account = param("account");
$company = param("company");
$player_token = param("ttoken",false);

$data = array();

$_company = get_company($company);
if(!is_null($_company) ){include($_SERVER['DOCUMENT_ROOT'] ."/utilities/api/". $_company ->vars["path"] ."/connect.php");}
$api = new _api_connection();

switch($type){ 
	case "agent":
		$agent = get_company_agent_by_name($account,$company);
		
		//insert new agent
		if(is_null($agent)){
			$info = $api->get_player_data($player_token);
			$agent = insert_agents($info["agents_list"]);			
		}
		
		if(!is_null($agent)){
			echo json_encode(get_agent_limits($agent));
		}
	break;
	case "player":
		$player = get_company_player($account, $company);
		
		if(is_null($player)){
			$info = $api->get_player_data($player_token);
			$agent = insert_agents($info["agents_list"]);
			$player = new _player();
			$player ->vars["account"] = $account;
			$player ->vars["company"] = $_company ->vars["id"];
			$player ->vars["external_id"] = $info["player_id"];
			$player ->vars["agent"] = $agent ->vars["id"];
			$player ->insert();
		}
		
		if(!is_null($player)){
			echo json_encode(get_logged_player_limits($player));
		}
	break;
}

?>