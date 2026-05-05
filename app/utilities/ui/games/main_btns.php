<?php 
$show_contest = $show_contest ?? false;
$not_pf = $not_pf ?? false;
?>
<? if($sattousd && $equal_on_top){ ?>
<div class="btn black main_color_btn left_btn_solo">USD Balance: $<span id="usd_balance_field">Loading...</span></div>
<? } ?>

<div class="lobby_box">
    
    <input type="button" value="Mute" onclick="mute();" id="btn_mute" class="btn main_color_btn"  />
    
    <? 
	if(param("popup")){
		?> <input type="button" value="Back to Lobby" onclick="window.close();" class="btn red main_color_btn" /> <?
	}else{
		?> <input type="button" value="Back to Lobby" onclick="parent.location.href = '<? echo get_lobby_url(); ?>';" class="btn red main_color_btn" /> <?
	}
	?>
    
    
    <input type="button" value="History" 
onclick="window.open('<?php echo CASINO_BASE_URL; ?>/history.php?gid=<? echo $_game->vars["id"] ?>', '_blank');" 
class="btn blue main_color_btn"  />


    <? if($show_contest){ ?>
    <input type="button" value="Contest" onclick="window.open('<?php echo CASINO_BASE_URL; ?>/contest.php', '_blank');" class="btn red main_color_btn"  />
    <? } ?>
    
    
    <? if($_company ->vars["prov_fair"] && !$not_pf){ ?>    
    	<input type="button" value="Provably Fair" onclick="$('#pfbox').toggle(500)" class="btn purple main_color_btn" />    
    <? } ?>
    
    <? if($cashier_link != ""){ ?> 
    	<input type="button" value="CASHIER" onclick="window.open('<? echo $cashier_link ?>', '_blank');" class="btn red main_color_btn" />    
    <? } ?>
    
     <? if(is_numeric($balance["free"]) && $balance["free"] > 0 && $_company ->vars["allow_freeplay"] && !$_using_free_play){ ?>  
    	<input type="button" value="Use Freeplay (<? echo $_currency_symbols[$balance["currency"]] ?><? echo number_format($balance["free"],2) ?>)" onclick="location.href = 'utilities/process/actions/use_free_play.php?g=<? echo $_game ->vars["id"] ?>'" class="btn green main_color_btn" />    
    <? } ?>
    
    <? if($_using_free_play){ ?>  
    	<input type="button" value="Use Real Balance (<? echo $_currency_symbols[$balance["currency"]] ?><? echo number_format($balance["real"],2) ?>)" onclick="location.href = 'utilities/process/actions/use_free_play.php?g=<? echo $_game ->vars["id"] ?>'" class="btn green main_color_btn" />    
    <? } ?>
    
    
    
    
    
    
</div>