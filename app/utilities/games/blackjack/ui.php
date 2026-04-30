<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/header.php");  ?>

<?php
$config = require $_SERVER['DOCUMENT_ROOT'].'/config.php';
$base_url = $config['app']['base_url'] ?? '';
?>
<script>
    var BASE_URL = "<?= $base_url ?>";
</script>
<script src="/utilities/js/config.js"></script>
<script type="text/javascript">
var current_balance = <?= isset($balance["real"]) ? json_encode($balance["real"]) : 0 ?>;
var min_amount = <?= isset($_min_amount) ? json_encode($_min_amount) : 0 ?>;
var max_amount = <?= isset($_max_amount) ? json_encode($_max_amount) : 0 ?>;
var currency_symbol = '<?= isset($_currency_symbols[$balance["currency"]]) ? $_currency_symbols[$balance["currency"]] : "" ?>';
var deck = new Array();
<?php foreach(isset($_deck) ? $_deck : [] as $card){ ?>
deck.push("<?= $card ?>");
<?php } ?>
</script>
<body>
	<?  include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/main_btns.php");  ?>    
        
    <? 
	if($_company ->vars["prov_fair"]){
		$server_num = generate_server_seed("bljk",0,100000000);
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
        <input type="button" id="btn_insurance_1" value="NO" onClick="insurance(0);" class="msg_btn btn red" />
        <input type="button" id="btn_insurance_0" value="YES" onClick="insurance(1);" class="msg_btn btn green" />
    </div>
    <div id="btn_box" class="btn_box">
    	<input type="button" id="btn_clear" value="Clear Bets" onClick="clear_bets(true);" class="btn red game_btn" />
        <input type="button" id="btn_deal" value="Deal" onClick="deal();" class="btn green game_btn" />
        <input type="button" id="btn_fold" value="Surrender" onClick="fold();" class="btn gray game_btn" />
        <input type="button" id="btn_split" value="Split" onClick="split_hand();" class="btn orange game_btn" />
        <input type="button" id="btn_double" value="Double" onClick="double();" class="btn blue game_btn" />
        <input type="button" id="btn_hit" value="Hit" onClick="hit();" class="btn green game_btn" />
        <input type="button" id="btn_stand" value="Stand" onClick="stand();" class="btn red game_btn" />
        <input type="button" id="btn_insurance" value="Pay Insurance" class="btn purple game_btn" />
        <input type="button" id="btn_game_clear" value="Clear" onClick="clear_game();" class="btn red game_btn" />
        <input type="button" id="btn_repeat" value="Repeat Bet" onClick="rebet();" class="btn green game_btn" />
    </div>
	<div id="spacer"></div>
    <a name="game_zone" id="game_zone"></a>
</body>
</html>
