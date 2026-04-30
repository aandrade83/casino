<? 
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/process/game_login.php"); 
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Lobby - <? echo $casino_name ?></title>
<link href="utilities/css/lobby.css?test=<? echo mt_rand() ?>" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="utilities/js/jquery-3.2.1.min.js"></script>
<script type="text/javascript" src="utilities/js/functions.js?test=<? echo mt_rand() ?>"></script>
</head>

<body>
	    
    <? 
	$gid = param("game");
	if(!is_numeric($gid)){$gid = -1;}
	$link = "https://play.casinogamesonline.com/?cid=".$_company ->vars["id"]."&cps=".$_company ->vars["password"]."&token=".urlencode($player_token)."&cshcd=".$cashier_code."&account=".$_player ->vars["account"]."&game=".$gid."&cksh=v2";
	
	?>
    
    <div style="margin:0 auto; max-width:1300px; height:100%">
	    <iframe src="<? echo $link; ?>" width="100%" frameborder="0" id="gifrm"></iframe>
    </div>
    
    <script type="text/javascript">
   $("#gifrm").height($("#gifrm").width()*0.6);
    </script>
    
</body>
</html>