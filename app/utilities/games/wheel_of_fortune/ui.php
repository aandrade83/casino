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
<? $freeplay = get_game_freeplays($_game->vars["id"], $_player->vars["id"], $_using_free_play ?? false); ?>
var free_spins = <?= isset($freeplay["total"]) ? json_encode(round($freeplay["total"])) : 0 ?>;
var current_balance = <?= isset($balance["real"]) ? json_encode(round($balance["real"],2)) : 0 ?>;
var min_amount = <?= isset($_min_amount) ? json_encode($_min_amount) : 0 ?>;
var max_amount = <?= isset($_max_amount) ? json_encode($_max_amount) : 0 ?>;
var currency_symbol = '<?= isset($_currency_symbols[$balance["currency"]]) ? $_currency_symbols[$balance["currency"]] : "" ?>';
</script>
<? $equal_on_top = true; //set USD equivalent balance on page top ?>
<body>
	<?  $not_pf = true; include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/main_btns.php");  ?>     
    <? 
	if($_company ->vars["prov_fair"]){
		$hard_numbers = array();
		$player_numbers = array();
		for($i=0;$i<15;$i++){
			$hard_numbers[] = mt_rand(0,199);
			$player_numbers[] = mt_rand(0,199);
		}
		$server_num = generate_server_seed("pirts",0,0,implode(",",$hard_numbers));
		$player_num = implode(",",$player_numbers);
		include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/pf.php");
	}
	?>
    <? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/games/balance_box.php");  ?> 
    
    <div class="top_spacer"></div>
    
    <?php /*?><div class="game_btns_box">
    
        <div class="sub_btn_box">
        
            <div id="coin_value_btn" class="btn_small coin_value" onClick="change_bet();" style="border:1px solid #f00;"></div>      
            
            <input name="" id="spin_btn" type="image" class="btn_big" onClick="spin();" src="utilities/games/multi_slot_FS2/imgs/spin_on.png" style="border:1px solid #f00;">
        
        </div>
    
    </div>
<?php */?>
	<div id="spacer"></div>
</body>
</html>
