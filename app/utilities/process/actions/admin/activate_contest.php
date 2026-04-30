<? 
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/admin_security.php");

$contest = get_contest(param("cid"));
$action = param("action");


if(!is_null($contest)){
	if($action == "disable"){
		$new_status = 0;
	}else{
		disable_all_contests();
		$new_status = 1;	
	}
	
	$contest ->vars["active"] = $new_status;
	$contest->update("active");
	
}


header("Location: https://play.casinogamesonline.com/admin/contests.php");

?>