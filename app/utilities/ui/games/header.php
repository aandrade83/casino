<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?= isset($_game->vars["name"]) ? htmlspecialchars($_game->vars["name"]) : 'Game' ?> - <?= isset($casino_name) ? htmlspecialchars($casino_name) : '' ?></title>
<link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto&display=swap" rel="stylesheet">
<link href="/utilities/css/games_global.css?xd=<?= mt_rand() ?>" rel="stylesheet" type="text/css" />
<link href="/utilities/games/<?= isset($_game->vars["path"]) ? htmlspecialchars($_game->vars["path"]) : '' ?>/css/style.css?xd=<?= mt_rand() ?>" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="/utilities/includes/shadowbox/shadowbox.css" type="text/css" media="screen" />
<script type="text/javascript">var gid = <?= json_encode(isset($_game->vars["id"]) ? $_game->vars["id"] : null) ?>;</script>
<script type="text/javascript" src="/utilities/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="/utilities/js/phaser.js"></script>
<script type="text/javascript" src="/utilities/js/functions.js?test=<?= mt_rand() ?>"></script>
<script type="text/javascript" src="/utilities/games/<?= isset($_game->vars["path"]) ? htmlspecialchars($_game->vars["path"]) : '' ?>/functions.js?test=<?= mt_rand() ?>"></script>
<script type="text/javascript" src="/utilities/includes/shadowbox/shadowbox.js"></script>
<?php /* Shadowbox.init() disabled — Shadowbox may not be available in all contexts */ ?>
</head>

<div class="balance_msg" id="balance_msg">
	<div class="balance_msg_txt">Not enough balance</div>
    <div class="balance_msg_btns">

    	<?php
        $cashier_link = $cashier_link ?? "";
        if($cashier_link != ""){ ?>
    	<input type="button" value="DEPOSIT NOW" onclick="window.open('<?= htmlspecialchars($cashier_link) ?>', '_blank');" class="btn red" style="display:inline;;">
        <?php } ?>

        <input type="button" value="Close" onclick="$('#balance_msg').hide();" class="btn blue" style="display:inline;;">
    </div>
</div>
