<?php
$sattousd        = $sattousd        ?? false;
$balance         = $balance         ?? ["real" => 0, "free" => 0, "currency" => "USD", "amount" => 0];
$_currency_symbols = $_currency_symbols ?? ["USD" => "$"];
?>
<div class="balance_box" id="balance_box">
    <div class="blanace_title">Current Balance</div>
    <div class="balance_amount" id="balance">
        <?= isset($_currency_symbols[$balance["currency"]]) ? $_currency_symbols[$balance["currency"]] : '' ?><span id="balance_field"><?= number_format($balance["real"] ?? 0, 2) ?></span>
        <?php if($sattousd){ ?>
            <script type="text/javascript">
            	$('#balance_field').on('DOMSubtreeModified', function(){
				   update_eqv_usd_balance();
				});
				function update_eqv_usd_balance(){
					// USD conversion disabled
				}
            </script>
        <?php } ?>
    </div>
</div>
