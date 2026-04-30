<? 
include($_SERVER['DOCUMENT_ROOT']."/utilities/includes.php");  

session_start();

if(isset($_SESSION["agent"]) && $_SESSION["hash"] == md5($_CODEX->encrypt($_SERVER['HTTP_USER_AGENT']))){
	$_agent = get_agent(secure_input($_SESSION["agent"]));
	
	if(!is_null($_agent)){
		
		$_logged = true;
		$_company = get_comany($_agent ->vars["company"]);	
		
	}else{$error = true;}
	
}else{$error = true;}

if($error){
	session_destroy();
	header("Location: https://play.casinogamesonline.com/admin/?expired=1");
	exit;
}

?>