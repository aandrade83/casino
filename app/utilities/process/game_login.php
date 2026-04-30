<? 
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Credentials: true");


include($_SERVER['DOCUMENT_ROOT'] ."/utilities/includes.php");

//for session to work on iframes
session_set_cookie_params([
    'lifetime' => 86400,         // Session duration (1 day)
    'path' => '/',               // Available for all paths on domain2.com
    'domain' => '',              // Empty to allow subdomains (optional)
    'secure' => true,            // Required for SameSite=None (must use HTTPS)
    'httponly' => true,          // Prevent JavaScript access to cookies
    'samesite' => 'None'         // Required to allow cookies in iframes
]);
//-----------------------------


session_start();

$comapny_id = param("cid");
$password = param("cps");
$player_token = param("token",false); //"lKkMLLEP9c0yfNynhdrUEGqvbcgjFtntLyuy2YknY+c=";//BETOWITEST
$player_account = strtoupper(param("account"));
$game_id = param("game");
$sattousd = param("sattousd");
$cashier_code = param("cshcd");
$cashier_link = get_cashier_link($cashier_code);
if($cashier_link == ""){$cashier_code = "";}//if not valid code, clear code

$_company = get_validated_comany($comapny_id,$password);


if(!is_null($_company) ){include($_SERVER['DOCUMENT_ROOT'] ."/utilities/api/". $_company ->vars["path"] ."/connect.php");}

if(!is_null($_company) && $player_account != "" && $player_token != ""){
	
	$_player = get_company_player($player_account, $_company ->vars["id"]);
	
	$api = new _api_connection();
	$info = $api->get_player_data($player_token);
	
	if(strtoupper($info["account"]) == $player_account){
	
		if(is_null($_player)){
			
			$_agent = insert_agents($info["agents_list"]);				
			
			$_player = new _player();
			$_player ->vars["account"] = $player_account;
			$_player ->vars["company"] = $_company ->vars["id"];
			$_player ->vars["external_id"] = $info["player_id"];
			$_player ->vars["agent"] = $_agent ->vars["id"];
			$_player ->insert();
			
		}else{
			$_agent = get_agent($_player ->vars["agent"]);
			if($_agent ->vars["account"] != $info["agent"]){
				$_agent = insert_agents($info["agents_list"]);	
				$_player ->vars["agent"] = $_agent ->vars["id"];
				$_player->update("agent");
			}
		}
		
		$_game = $_player->get_allowed_game($game_id);
	
		$_max_amount = round($_game ->vars["max_amount"],2);
		$_min_amount = round($_game ->vars["min_amount"],2);
	
	}else{
		$_player = NULL; //URL account is different from API account
	}
	
}

if(!is_null($_company) && !is_null($_player)){
	
	
	session_regenerate_id(true);
	$_SESSION['player'] = $_player->vars["id"];
	$_SESSION['player_token'] = $player_token;
	$_SESSION['company'] = $_company->vars["id"];
	$_SESSION['cshcd'] = $cashier_code;
	$_SESSION['hash'] = md5($_CODEX->encrypt($_SERVER['HTTP_USER_AGENT']));
	
	$_using_free_play = $_player ->vars["using_free_play"];
	
}else{
	echo "Unable to connect, please verify your connection information.";
	exit;	

}
?>