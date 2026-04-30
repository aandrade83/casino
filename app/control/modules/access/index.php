<?php
error_reporting(E_ALL & ~E_WARNING & ~E_DEPRECATED);
ini_set('display_errors', 0);
require_once($_SERVER['DOCUMENT_ROOT'] . "/utilities/includes.php");
require_once($_SERVER['DOCUMENT_ROOT']."/control/modules/access/api_local.php");
include($_SERVER['DOCUMENT_ROOT']."/control/modules/access/game_login.php");

$api = new _local_api();
$balance = $api->get_player_balance($_SESSION['player']);

if($api->done){

    if(!is_null($_game)){
        include($_SERVER['DOCUMENT_ROOT']."/utilities/games/".$_game->vars["path"]."/ui.php");
    
    }else if(param("json_lobby")){
        include($_SERVER['DOCUMENT_ROOT']."/control/modules/access/json_lobby.php");

    }else if(param("history")){
        echo "<script>location.href='history.php';</script>";

    }else{
      include($_SERVER['DOCUMENT_ROOT']."/control/modules/access/lobby.php");
    }

}else{
    $error_message = $api->error_msg;
    include($_SERVER['DOCUMENT_ROOT']."/utilities/ui/game_error.php");
}