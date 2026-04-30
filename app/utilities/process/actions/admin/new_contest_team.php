<? 
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/admin_security.php");

$contest = get_contest(param("cid"));
$team = get_contest_team(param("tid"));

if(!is_null($contest)){

	$name = param("name");
	$image = param("image");
	$points = param("points");
	
	if(!is_null($team)){
		$team ->vars["name"] = $name;	
		$team ->vars["image"] = $image;	
		$team ->vars["points"] = $points;	
		$team ->update();
	}else{
		$team = new _contest_team();
		$team ->vars["name"] = $name;	
		$team ->vars["image"] = $image;	
		$team ->vars["points"] = $points;	
		$team ->vars["active"] = 1;	
		$team ->vars["contest"] = $contest ->vars["id"];	 	
		$team ->insert();
	}

}


header("Location: https://play.casinogamesonline.com/admin/contest_teams.php?cid=".$contest ->vars["id"]."&a=1");

?>