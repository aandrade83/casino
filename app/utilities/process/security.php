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

if(isset($_SESSION["company"]) && isset($_SESSION["player_token"]) && isset($_SESSION["player"]) && $_SESSION["hash"] == md5($_CODEX->encrypt($_SERVER['HTTP_USER_AGENT']))){
	
	$comapny_id = $_SESSION['company'];
	$player_token = $_SESSION['player_token'];
	$player_id = $_SESSION['player'];
	
	$cashier_code = $_SESSION['cshcd'];
	$cashier_link = get_cashier_link($cashier_code);
	if($cashier_link == ""){$cashier_code = "";}//if not valid code, clear code
	
	$_company = get_comany($comapny_id);
	$_player = get_player($player_id);
	$_logged = true;
	
	
	if(!is_null($_company) && !is_null($_player)){
		include($_SERVER['DOCUMENT_ROOT'] ."/utilities/api/". $_company ->vars["path"] ."/connect.php");
		$_api = new _api_connection();	
		$_using_free_play = $_player ->vars["using_free_play"];
	}else{
		$result["error"] = 200;
		$result["msg"] = "There was a problem with your session, please login again.";
	}
	
}else{
	$result["error"] = 100;
	$result["msg"] = "Session expired, please login again.";
}





?>