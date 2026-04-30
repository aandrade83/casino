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
