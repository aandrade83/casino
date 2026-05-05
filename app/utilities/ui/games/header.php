<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><? echo $_game ->vars["name"] ?> - <? echo $casino_name ?></title>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
<link href="utilities/css/games_global.css?xd=<? echo mt_rand() ?>" rel="stylesheet" type="text/css" />
<link href="utilities/games/<? echo $_game ->vars["path"] ?>/css/style.css?xd=<? echo mt_rand() ?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="utilities/includes/shadowbox/shadowbox.css" type="text/css" media="screen" />
<script type="text/javascript">var gid = '<? echo $_game ->vars["id"]  ?>';</script>
<script type="text/javascript" src="utilities/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="utilities/js/phaser.js"></script>
<script type="text/javascript" src="utilities/js/functions.js?test=<? echo mt_rand() ?>"></script>
<script type="text/javascript" src="utilities/games/<? echo $_game ->vars["path"] ?>/functions.js?test=<? echo mt_rand() ?>"></script>
<script type="text/javascript" src="utilities/includes/shadowbox/shadowbox.js"></script>
<script type="text/javascript">Shadowbox.init();</script>
</head>

<div class="balance_msg" id="balance_msg">
	<div class="balance_msg_txt">Not enough balance</div>
    <div class="balance_msg_btns">
    
    	<? if($cashier_link != ""){ ?>
    	<input type="button" value="DEPOSIT NOW" onclick="window.open('<? echo $cashier_link ?>', '_blank');" class="btn red" style="display:inline;;"> 
        <? } ?>
        
        <input type="button" value="Close" onclick="$('#balance_msg').hide();" class="btn blue" style="display:inline;;"> 
    </div>
</div>
