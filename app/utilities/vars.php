<?php

$casino_name = "Vegas Casino";
$casino_id_base = 77;

$_CODEX = new _codex($main_url);
$acceptable_images = array('jpg', 'jpeg', 'png', 'gif');
$_date_format = "d-m-Y";
$_time_format = "d-m-Y h:i A";
$_hour_format = "h:i A";

$_deck = array("SA","S2","S3","S4","S5","S6","S7","S8","S9","S10","SJ","SQ","SK",
			   "HA","H2","H3","H4","H5","H6","H7","H8","H9","H10","HJ","HQ","HK",
			   "DA","D2","D3","D4","D5","D6","D7","D8","D9","D10","DJ","DQ","DK",
			   "CA","C2","C3","C4","C5","C6","C7","C8","C9","C10","CJ","CQ","CK");
			   
$_spanish_deck = array("SA","S2","S3","S4","S5","S6","S7","S8","S9","SJ","SQ","SK",
			   "HA","H2","H3","H4","H5","H6","H7","H8","H9","HJ","HQ","HK",
			   "DA","D2","D3","D4","D5","D6","D7","D8","D9","DJ","DQ","DK",
			   "CA","C2","C3","C4","C5","C6","C7","C8","C9","CJ","CQ","CK");
			   
$_card_symbols = array("J","Q","K");

$_currency_symbols = array("USD"=>"$","MicroBTC"=>"μ฿","satoshi"=>"sat ");

?>