<? 
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/game_login.php"); 

if ($mock_mode) {
    include_once($_SERVER['DOCUMENT_ROOT'] . "/utilities/mock/mock_api.php");
    $api = new _api_mock();
} else {
    $api = new _api_connection();
}

$balance = $api->get_player_balance($player_token);

if($api->done){
	
    if(!empty($_game)){
		include($_SERVER['DOCUMENT_ROOT'] ."/utilities/games/".$_game ->vars["path"]."/ui.php"); 
	}else if(param("json_lobby")){
		include("json_lobby.php");
	}else if(param("history")){
		?> <script type="text/javascript">location.href = 'history.php';</script> <?
	}else{
	  include("lobby.php");
	}
	
}else{
	$error_message = $api->error_msg;
	include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/game_error.php");	
	
}




?>