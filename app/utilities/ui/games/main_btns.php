<?php
$not_pf          = $not_pf          ?? "";
$equal_on_top    = $equal_on_top    ?? "";
$sattousd        = $sattousd        ?? false;
$show_contest    = $show_contest    ?? false;
$cashier_link    = $cashier_link    ?? "";
$_using_free_play = $_using_free_play ?? false;
?>
<?php if($sattousd && $equal_on_top){ ?>
<div class="btn black main_color_btn left_btn_solo">USD Balance: $<span id="usd_balance_field">Loading...</span></div>
<?php } ?>

<div class="lobby_box">

    <input type="button" value="Mute" onclick="mute();" id="btn_mute" class="btn main_color_btn"  />

    <?php
    if(param("popup")){
        ?> <input type="button" value="Back to Lobby" onclick="window.close();" class="btn red main_color_btn" /> <?php
    }else{
        ?> <input type="button" value="Back to Lobby" onclick="parent.location.href = '<?= get_lobby_url() ?>';" class="btn red main_color_btn" /> <?php
    }
    ?>

    <?php
    $history_protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $history_base = $history_protocol . '://' . $_SERVER['HTTP_HOST'];
    $history_gid  = isset($_game->vars["id"]) ? htmlspecialchars($_game->vars["id"]) : '';
    ?>
    <input type="button" value="History" onclick="window.open('<?= $history_base ?>/history.php?gid=<?= $history_gid ?>', '_blank');" class="btn blue main_color_btn"  />

    <?php if($show_contest){ ?>
    <input type="button" value="Contest" onclick="window.open('https://play.casinogamesonline.com/contest.php', '_blank');" class="btn red main_color_btn"  />
    <?php } ?>

    <?php if(isset($_company->vars["prov_fair"]) && $_company->vars["prov_fair"] && !$not_pf){ ?>
    	<input type="button" value="Provably Fair" onclick="$('#pfbox').toggle(500)" class="btn purple main_color_btn" />
    <?php } ?>

    <?php if($cashier_link != ""){ ?>
    	<input type="button" value="CASHIER" onclick="window.open('<?= htmlspecialchars($cashier_link) ?>', '_blank');" class="btn red main_color_btn" />
    <?php } ?>

    <?php if(isset($balance["free"]) && is_numeric($balance["free"]) && $balance["free"] > 0 && isset($_company->vars["allow_freeplay"]) && $_company->vars["allow_freeplay"] && !$_using_free_play){ ?>
    	<input type="button" value="Use Freeplay (<?= isset($_currency_symbols[$balance["currency"]]) ? $_currency_symbols[$balance["currency"]] : '' ?><?= number_format($balance["free"],2) ?>)" onclick="location.href = 'utilities/process/actions/use_free_play.php?g=<?= isset($_game->vars["id"]) ? htmlspecialchars($_game->vars["id"]) : '' ?>'" class="btn green main_color_btn" />
    <?php } ?>

    <?php if($_using_free_play){ ?>
    	<input type="button" value="Use Real Balance (<?= isset($_currency_symbols[$balance["currency"]]) ? $_currency_symbols[$balance["currency"]] : '' ?><?= isset($balance["real"]) ? number_format($balance["real"],2) : '0.00' ?>)" onclick="location.href = 'utilities/process/actions/use_free_play.php?g=<?= isset($_game->vars["id"]) ? htmlspecialchars($_game->vars["id"]) : '' ?>'" class="btn green main_color_btn" />
    <?php } ?>

</div>
