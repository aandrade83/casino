<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/web/header.php"); ?>

<?
$game = get_game(param("gid"));	
if(!is_null($game) && !is_null($_player)){
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/games/".$game ->vars["path"]."/class.php"); 	
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Provably fair (<? echo $game ->vars["name"] ?>)</h1>
    </div>
</div>

<br><br>

<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/games/".$game ->vars["path"]."/pf.php"); 	 ?>

<? }else{echo "No data found";} ?>


<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/web/footer.php"); ?>