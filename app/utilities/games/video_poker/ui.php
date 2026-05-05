<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/header.php");  ?> 

<script type="text/javascript">
var current_balance = <? echo $balance["amount"] ?>;
var min_amount = <? echo $_min_amount ?>;
var max_amount = <? echo $_max_amount ?>;
var currency_symbol = '<? echo $_currency_symbols[$balance["currency"]]  ?>';
var deck = new Array();
<? foreach($_deck as $card){ ?>
deck.push("<? echo $card ?>");
<? } ?>
</script>
<? $equal_on_top = true; //set USD equivalent balance on page top ?>
<body>
	
    <?  include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/main_btns.php");  ?> 
    
    <? 
	if($_company ->vars["prov_fair"]){
		$server_num = generate_server_seed("vdpkr",0,100000000);
		$player_num = mt_rand(0,100000000);
		$sword = "Hand";
		include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/pf.php"); 
	}
	?>    
    <? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/balance_box.php");  ?> 
    
    <div id="betone_btn" class="btn_big betone" onClick="bet_one();"></div>
    <div id="betone_max" class="btn_big betmax" onClick="bet_max();"></div>
    <div id="deal_btn" class="btn_big dealbtn" onClick="deal();"></div>
    <div id="betone_plus" class="btn_small betplus" onClick="change_bet('+');"></div>
    <div id="betone_min" class="btn_small betmin" onClick="change_bet('-');"></div>
	<div id="spacer"></div>
</body>
</html>