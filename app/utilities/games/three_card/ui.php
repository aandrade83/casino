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
var min_amount = <?= isset($_min_amount) ? json_encode(round($_min_amount)) : 0 ?>;
var max_amount = <?= isset($_max_amount) ? json_encode(round($_max_amount)) : 0 ?>;
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
