<? 
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/admin_security.php");

$team = get_contest_team(param("tid"));

if(!is_null($team)){
	if($team ->vars["active"]){
		$new_status = 0;
	}else{
		$new_status = 1;	
	}
	
	$team ->vars["active"] = $new_status;
	$team->update("active");
	
}


header("Location: https://play.casinogamesonline.com/admin/contest_teams.php?cid=".$team ->vars["contest"]."#t".$team ->vars["id"]);

?>