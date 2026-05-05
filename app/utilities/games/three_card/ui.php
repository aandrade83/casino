<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/header.php");  ?> 

<script type="text/javascript">
var current_balance = <? echo $balance["amount"] ?>;
var min_amount = <? echo round($_min_amount) ?>;
var max_amount = <? echo round($_max_amount) ?>;
var currency_symbol = '<? echo $_currency_symbols[$balance["currency"]]  ?>';
var deck = new Array();
<? foreach($_deck as $card){ ?>
deck.push("<? echo $card ?>");
<? } ?>
</script>
<body>
	
    <?  include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/main_btns.php");  ?> 
    
    <? 
	if($_company ->vars["prov_fair"]){
		$server_num = generate_server_seed("tcpkr",0,100000000);
		$player_num = mt_rand(0,100000000);
		$sword = "Hand";
		include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/pf.php"); 
	}
	?>    
    <? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/balance_box.php");  ?> 
    
    <div class="balance_box" id="limit_box">
    	<div class="blanace_title">Limits</div>
        <div class="balance_amount">
        	Min: <? echo $_currency_symbols[$balance["currency"]] . $_min_amount ?>
            &nbsp;|&nbsp;
            Max: <? echo $_currency_symbols[$balance["currency"]] . $_max_amount ?>
        </div>
    </div>
    <div class="game_msg" id="game_msg"></div>
    
    <div id="btn_box" class="btn_box">
    	<input type="button" id="btn_fold" value="Fold" onClick="avoid_doubleclick(this.id); fold();" class="btn red game_btn" />
        <input type="button" id="btn_call" value="Bet" onClick="avoid_doubleclick(this.id); call();" class="btn blue game_btn" />        
        
        <input type="button" id="btn_clear" value="Clear" onClick="avoid_doubleclick(this.id); clear_table('player');" class="btn red game_btn" />        
        <input type="button" id="btn_spin" value="Deal" onClick="avoid_doubleclick(this.id); deal();" class="btn green game_btn" />
        
        
        <input type="button" id="btn_rebet" value="Rebet" onClick="avoid_doubleclick(this.id); rebet(false);" class="btn blue game_btn" />
        <input type="button" id="btn_rebet_spin" value="Rebet & Deal" onClick="avoid_doubleclick(this.id); rebet(true);" class="btn purple game_btn" />
    </div>
	<div id="spacer"></div>
</body>
</html>