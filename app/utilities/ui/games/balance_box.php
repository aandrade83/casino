<div class="balance_box" id="balance_box">
    <div class="blanace_title">Current Balance</div>
    <div class="balance_amount" id="balance">
        <? echo $_currency_symbols[$balance["currency"]] ?><span id="balance_field"><? echo number_format($balance["amount"],2) ?></span>
        <? if($sattousd){ /* Comment Alexis?>
        	<? 
			$rates = json_decode(file_get_contents("https://transfers.ezpay.com/utilities/api/crypto/rates.php"),true); 
			$btc_sat_price = $rates["BTC"]["rate"]/100000000;
			?>
            <? if(!$equal_on_top){ ?>
        	<br /><? echo $_currency_symbols["USD"] ?><span id="usd_balance_field"><? echo round($balance["amount"]*$btc_sat_price,2) ?></span>
            <? } ?>
            <script type="text/javascript">
            	$('#balance_field').on('DOMSubtreeModified', function(){
				   update_eqv_usd_balance();
				});
				function update_eqv_usd_balance(){
					$("#usd_balance_field").html( Math.round(($("#balance_field").html().replace(",","")*<? echo $btc_sat_price ?>)*100)/100 );
				}
				update_eqv_usd_balance();
            </script>
        <? */ } ?>
    </div>
    <?php /*?><? if(is_numeric($balance["free"]) && $balance["free"] > 0 && $_player ->vars["account"] == "SBO5008"){ ?>
    <div class="balance_amount freeplay">
        <a href="javascript:;" onClick="alert('Cant use freeplay at this moment');">Use Freeplay (<? echo $_currency_symbols[$balance["currency"]] ?><? echo number_format($balance["free"],2) ?>)</a>
    </div>
    <? } ?><?php */?>
</div>