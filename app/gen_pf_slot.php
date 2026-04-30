<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/includes.php"); ?>
<?
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/games/multi_slot_giants/class.php");   

$slot = new multi_slot(0);

$pfdata = $slot->generate_pf_batch(); 

echo "TOTAL FIGURES PER REEL: " . $pfdata["total"] ;
	
?>
<br /><br /><br />
<textarea name="" cols="100" rows="200"><? echo $pfdata["json"] ?></textarea>