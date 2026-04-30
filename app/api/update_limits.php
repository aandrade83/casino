<? 
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/includes.php");

$type = param("type");
$account = param("account");
$company = param("company");

$day_win = param("day_win");
if(!is_numeric($day_win)){$day_win = -1;}
$day_loss = param("day_loss");
if(!is_numeric($day_loss)){$day_loss = -1;}
$week_win = param("week_win");
if(!is_numeric($week_win)){$week_win = -1;}
$week_loss = param("week_loss");
if(!is_numeric($week_loss)){$week_loss = -1;}

$_company = get_company($company);

switch($type){ 
	case "agent":
		$agent = get_company_agent_by_name($account,$company);
		
		if(!is_null($agent)){
			
			$agent ->vars["day_max_win"] = $day_win;
			$agent ->vars["day_max_loss"] = $day_loss;
			$agent ->vars["week_max_win"] = $week_win;
			$agent ->vars["week_max_loss"] = $week_loss;
			$agent->update("day_max_win,day_max_loss,week_max_win,week_max_loss");
			
		}
	break;
	case "player":
		$player = get_company_player($account, $company);
		if(!is_null($player)){
			
			$player ->vars["day_max_win"] = $day_win;
			$player ->vars["day_max_loss"] = $day_loss;
			$player ->vars["week_max_win"] = $week_win;
			$player ->vars["week_max_loss"] = $week_loss;
			$player->update("day_max_win,day_max_loss,week_max_win,week_max_loss");
			
		}
	break;
}

?>