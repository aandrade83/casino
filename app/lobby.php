<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Lobby - <? echo $casino_name ?></title>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="utilities/css/lobby.css?test=<? echo mt_rand() ?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="utilities/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="utilities/js/functions.js?test=<? echo mt_rand() ?>"></script>
</head>

<body>
	<div class="top_bar">
    	<strong><? echo $_player ->vars["account"] ?></strong>
        <div class="balance_box">
        
        	<? if($_company ->vars["allow_freeplay"]){ ?>
				<? if(!$_using_free_play){ ?>
                
                    <span class="selected_balance head_btns">
                        Using Real Balance: <? echo $_currency_symbols[$balance["currency"]] . number_format($balance["real"],2) ?>
                    </span>
                    
                    
                    <span class="head_btns">
                        
                        <button class="blue-btn" onclick="location.href = 'utilities/process/actions/use_free_play.php';">Use Freeplay: <? echo $_currency_symbols[$balance["currency"]] . number_format($balance["free"],2) ?></button>
                        
                    </span>
                
                <? }else{ ?>
                
                    <span class="selected_balance head_btns">
                        Using Free Play: <? echo $_currency_symbols[$balance["currency"]] . number_format($balance["free"],2) ?>
                    </span>
                    
                    
                    <span class="head_btns">
                       
                        <button class="blue-btn" onclick="location.href = 'utilities/process/actions/use_free_play.php';"> Use Real Balance: <? echo $_currency_symbols[$balance["currency"]] . number_format($balance["real"],2) ?></button>
                        
                    </span>
                
                <? } ?> 
            <? } ?> 
            
            
            
            
            <? if($cashier_link != ""){ ?>
                    
            <span class="head_btns">
                <button class="red-btn" onclick="window.open('<? echo $cashier_link ?>', '_blank');">CASHIER</button>
            </span>
                
            
            <? } ?>
            
                    
            <span class="head_btns">
                <button class="blue-btn" onclick="window.open('history.php', '_blank');">History</button>
            </span>
            
            
        </div>
    </div>
    
    <? 
	
	//$link = "https://play.casinogamesonline.com/game_box.php?cid=".$_company ->vars["id"]."&cps=".$_company ->vars["password"]."&token=".urlencode($player_token)."&cshcd=".$cashier_code."&account=".$_player ->vars["account"]."&game=";
	$link = "https://play.casinogamesonline.com/?cid=".$_company ->vars["id"]."&cps=".$_company ->vars["password"]."&token=".urlencode($player_token)."&cshcd=".$cashier_code."&account=".$_player ->vars["account"]."&game=";
	$categories = get_all_categories();
	?>
    
    <? foreach($categories as $category){ ?>
    	<? $games = $_player->get_allowed_games($category ->vars["id"]); ?>
        
        <div class="cat_grp">
        
        	<div class="cat_title"><? echo $category ->vars["name"] ?></div>
    
			<? foreach($games as $game){ ?>
            <div class="game_box" onclick="location.href = '<? echo $link.$game->vars["id"] ?>';">
                <img src="utilities/games/<? echo $game->vars["path"] ?>/imgs/lobby.png?v4" />
                <h2><? echo $game->vars["name"] ?></h2>
                Min: <? echo $_currency_symbols[$balance["currency"]] . round($game->vars["min_amount"],2) ?> Max: <? echo $_currency_symbols[$balance["currency"]] . round($game->vars["max_amount"],2) ?>
            </div>
            <? } ?>
        
        </div>
    
    <? } ?>
    
</body>
</html>