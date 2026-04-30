<?
$result = array();
$result["error"] = 0;
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/security.php");	

$open_total = count_casino_open_hands($_player ->vars["id"]);

if($open_total["total"] == 0){

	$game = param("g");
	if(!is_numeric($game)){$game = 0;}
	
	if($_logged && $_company ->vars["allow_freeplay"]){
		
		if($_player ->vars["using_free_play"]){
			$_player ->vars["using_free_play"] = 0;	
		}else{
			$_player ->vars["using_free_play"] = 1;
		}
		
		$_player->update("using_free_play");
		
	}

}else{
	?> 
    <script type="text/javascript">alert("You have unfinished games, please complete them before switching balances.");</script>
    <?	
}

?> <script type="text/javascript">location.href = '<? echo get_lobby_url()."&game=".$game ?>';</script> <?

?>