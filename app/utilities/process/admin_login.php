<? 
include($_SERVER['DOCUMENT_ROOT']."/utilities/includes.php");  

session_start();

$username = param("user");
$pass = $_CODEX->encrypt(param("password",false));

$agent = get_login_agent($username, $pass);

if(!is_null($agent)){
	session_regenerate_id(true);
	$_SESSION['hash'] = md5($_CODEX->encrypt($_SERVER['HTTP_USER_AGENT']));
	$_SESSION['agent'] = $agent->vars["id"];
	header("Location: ../../admin/dash.php");
}else{
	session_destroy();
	header("Location: ../../admin/?fail=1");
	exit;	
}

?>