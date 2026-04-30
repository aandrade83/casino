<? 
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/admin_security.php");

$contest = get_contest(param("cid"));
$name = param("name");
$start_date = param("start_date");
$end_date = param("end_date");
$imgs_folder = param("imgs_folder");
$rules = param("rules",false);

if(!is_null($contest)){
	$contest ->vars["name"] = $name;	
	$contest ->vars["start_date"] = $start_date;	
	$contest ->vars["end_date"] = $end_date;	
	$contest ->vars["imgs_folder"] = $imgs_folder;	
	$contest ->vars["rules"] = $rules;	
	$contest ->update();
}else{
	$contest = new _contest();
	$contest ->vars["name"] = $name;	
	$contest ->vars["start_date"] = $start_date;	
	$contest ->vars["end_date"] = $end_date;	
	$contest ->vars["imgs_folder"] = $imgs_folder;	
	$contest ->vars["rules"] = $rules;	
	$contest ->vars["active"] = 0;	
	$contest ->insert();
	
	$companies = get_all_companies();
	foreach($companies as $comp){
		insert_contest_by_company($comp ->vars["id"], $contest ->vars["id"]);
	}
	
}


header("Location: https://play.casinogamesonline.com/admin/contests.php?a=1");

?>