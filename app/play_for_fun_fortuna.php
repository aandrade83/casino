<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Fortuna Casino Games Previews</title>
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="description" content="Fortuna Casino Games Previews">
</head>
<body bgcolor="#000000">
<? 
include_once('D:/WebDC 4.0.3.2/www.sportsbettingonline.ag/utilities/ui/casinos/lobby-components.php');
?>

<style>
.container {
  position: relative;
  overflow: hidden;
  width: 100%;
  padding-top: 56.25%; /* 16:9 Aspect Ratio (divide 9 by 16 = 0.5625) */
}

/* Then style the iframe to fit in the container div with full height and width */
.responsive-iframe {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 100%;
}
</style>

<div class="container">
	<iframe class="responsive-iframe" id="igames" name="igames" frameborder="0" scrolling="auto"></iframe>
</div>

<script>

function CasinoGameOpen(nCasinoGameId, nAccountId, sGameCode) {
    var LaunchForm = document.forms["LaunchForm"];
    if (LaunchForm == null) { alert("Launch Form not found"); return; }

    if (nAccountId != null) LaunchForm["AccountId"].value = nAccountId;

    LaunchForm["CasinoGameId"].value = nCasinoGameId;

    var windowname = "Casino";
    if (nCasinoGameId != null && nCasinoGameId > 0) windowname += nCasinoGameId;
    if (sGameCode != null) windowname += sGameCode;
    if (nAccountId == -1) windowname += "aDemo";
    else if (nAccountId != null) windowname += "a" + nAccountId;
    windowname += "Sol";

    //LaunchForm.target = windowname;
	LaunchForm.target = 'igames';   
    LaunchForm.submit();
}
  	CasinoGameOpen(<? echo $_GET["game_id"] ?>, -1, null);
</script>

</body>
</html>
