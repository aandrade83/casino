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
<body>
	<? 
	$contest = get_active_contest($_player ->vars["company"]);
	if(!is_null($contest)){$show_contest = true;}
	include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/main_btns.php");  
	?>    
        
    <? 
	if($_company ->vars["prov_fair"]){
		$server_num = generate_server_seed("bljkerp",0,100000000);
		$player_num = mt_rand(0,100000000);
		$sword = "Hand";
		$pf_use_session = true;
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
    <div class="game_msg" id="insurance">
    	Would you like insurance?
        <input type="button" id="btn_insurance_1" value="NO" onClick="avoid_doubleclick(this.id); insurance(0);" class="msg_btn btn red" />
        <input type="button" id="btn_insurance_0" value="YES" onClick="avoid_doubleclick(this.id); insurance(1);" class="msg_btn btn green" />
    </div>
    <div id="btn_box" class="btn_box">
    	<input type="button" id="btn_clear" value="Clear Bets" onClick="avoid_doubleclick(this.id); clear_bets(true);" class="btn red game_btn" />
        <input type="button" id="btn_deal" value="Deal" onClick="avoid_doubleclick(this.id); deal();" class="btn green game_btn" />
        <input type="button" id="btn_fold" value="Surrender" onClick="avoid_doubleclick(this.id); fold();" class="btn gray game_btn" />
        <input type="button" id="btn_split" value="Split" onClick="avoid_doubleclick(this.id); split_hand();" class="btn orange game_btn" />
        <input type="button" id="btn_double" value="Double" onClick="avoid_doubleclick(this.id); double();" class="btn blue game_btn" />
        <input type="button" id="btn_hit" value="Hit" onClick="avoid_doubleclick(this.id); hit();" class="btn green game_btn" />
        <input type="button" id="btn_stand" value="Stand" onClick="avoid_doubleclick(this.id); stand();" class="btn red game_btn" />
        <input type="button" id="btn_insurance" value="Pay Insurance" class="btn purple game_btn" />
        <input type="button" id="btn_game_clear" value="Clear" onClick="avoid_doubleclick(this.id); clear_game();" class="btn red game_btn" />
        <input type="button" id="btn_repeat" value="Repeat Bet" onClick="avoid_doubleclick(this.id); rebet();" class="btn green game_btn" />
    </div>
	<div id="spacer"></div>
    <a name="game_zone" id="game_zone"></a>
</body>
</html>