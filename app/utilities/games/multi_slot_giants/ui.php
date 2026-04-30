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
var current_balance = <?= isset($balance["real"]) ? json_encode(round($balance["real"],2)) : 0 ?>;
var min_amount = <?= isset($_min_amount) ? json_encode($_min_amount) : 0 ?>;
var max_amount = <?= isset($_max_amount) ? json_encode($_max_amount) : 0 ?>;
var currency_symbol = '<?= isset($_currency_symbols[$balance["currency"]]) ? $_currency_symbols[$balance["currency"]] : "" ?>';
var max_lines = 25;
</script>
<? $equal_on_top = true; //set USD equivalent balance on page top ?>
<body>
	<?  include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/main_btns.php");  ?>     
    <? 
	if($_company ->vars["prov_fair"]){
		$hard_numbers = array();
		$player_numbers = array();
		for($i=0;$i<15;$i++){
			$hard_numbers[] = mt_rand(0,109);
			$player_numbers[] = mt_rand(0,109);
		}
		$server_num = generate_server_seed("egys",0,0,implode(",",$hard_numbers));
		$player_num = implode(",",$player_numbers);
		include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/pf.php");
	}
	?>
    <? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/balance_box.php");  ?> 
    
    <div class="top_spacer"></div>
    
    
    <div class="game_btns_box">
    
        <div class="sub_btn_box">
        
            <div id="coin_value_btn" class="btn_small coin_value" onClick="change_bet();"></div>
            <div id="lines_btn" class="btn_small lines" onClick="change_lines(false);"></div>        
            
            <input name="" id="spin_btn" type="image" class="btn_big" onClick="spin();" src="utilities/games/multi_slot/imgs/spin_on.png">
            <input name="" id="betmax_btn" type="image" class="btn_big" onClick="change_lines(true); spin();" src="utilities/games/multi_slot/imgs/betmax_on.png">
        
        </div>
    
    </div>
    
    <?php /*?><input name="" type="button" value="-" onClick="move_reel_test('-');">
    <input type="text" onChange="move_reel_test();" id="reeltpos" value="3.5">
    <input onClick="move_reel_test('+');" name="0.535" type="button" value="+"><?php */?>
    
    <?php /*?><div id="betone_btn" class="btn_big betone" onClick="spin(1);"></div>
    <div id="betone_two" class="btn_big bettwo" onClick="spin(2);"></div>
    <div id="betone_plus" class="btn_small betplus" onClick="change_bet('+');"></div>
    <div id="betone_min" class="btn_small betmin" onClick="change_bet('-');"></div>
   <?php */?>
	<div id="spacer"></div>
</body>
</html>
