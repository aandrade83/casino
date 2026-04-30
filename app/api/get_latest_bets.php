<? 
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/includes.php");

$bets = get_latest_bets();

echo json_encode($bets);

?>