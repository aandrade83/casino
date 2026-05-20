<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/web/header.php"); ?>

<?
$game = get_game(param("gid"));	

if(!is_null($game) && !is_null($_player)){
?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header"><? echo $game ->vars["name"] ?> History</h1>
    </div>
</div>

<?
$from = param("from");
if($from == ""){$from = date("Y-m-d");}	
$to = param("to");	
if($to == ""){$to = date("Y-m-d");}	

switch($game ->vars["id"]){ 
	case "1":
		$data = search_blackjack_session($_player ->vars["id"], $from, $to,"regular");		
	break;
	case "31":
		$data = search_blackjack_session($_player ->vars["id"], $from, $to,"spanish");		
	break;
	case "33":
		$data = search_blackjack_session($_player ->vars["id"], $from, $to,"double_exposure");		
	break;
	case "9":
	case "11":
	case "14":
	case "17":
	case "18":
	case "19":
	case "4":
	case "24":
	case "25":
	case "29":
	case "32":
	case "34":
		$data = search_slots_session($_player ->vars["id"], $from, $to, $game ->vars["id"]);		
	break;
	case "5":
		$data = search_videopoker_session($_player ->vars["id"], $from, $to, "jacks");		
	break;
	case "12":
		$data = search_videopoker_session($_player ->vars["id"], $from, $to, "deuces");		
	break;
	case "27":
		$data = search_videopoker_session($_player ->vars["id"], $from, $to, "afs");		
	break;
	case "6":
	case "20":
		$data = search_roulette_session($_player ->vars["id"], $from, $to);		
	break;
	case "7":
		$data = search_craps_session_rolls($_player ->vars["id"], $from, $to);		
	break;
	case "8":
		$data = search_craps_session($_player ->vars["id"], $from, $to);		
	break;
	case "10":
		$data = search_baccarat_session($_player ->vars["id"], $from, $to);		
	break;
	case "15":
		$data = search_poker_session($_player ->vars["id"], $from, $to,"holdem");		
	break;
	case "21":
		$data = search_poker_session($_player ->vars["id"], $from, $to,"caribbean");		
	break;
	case "26":
		$data = search_poker_session($_player ->vars["id"], $from, $to,"three_card");		
	break;
	case "28":
		$data = search_keno_session($_player ->vars["id"], $from, $to);		
	break;
	default:
		$data = array();
		$data = array_merge($data,search_blackjack_session($_player ->vars["id"], $from, $to,"regular"));
		$data = array_merge($data,search_blackjack_session($_player ->vars["id"], $from, $to,"spanish"));
		$data = array_merge($data,search_blackjack_session($_player ->vars["id"], $from, $to,"double_exposure"));
		$data = array_merge($data,search_slots_session($_player ->vars["id"], $from, $to,4));
		$data = array_merge($data,search_slots_session($_player ->vars["id"], $from, $to,9));
		$data = array_merge($data,search_slots_session($_player ->vars["id"], $from, $to,11));
		$data = array_merge($data,search_slots_session($_player ->vars["id"], $from, $to,14));
		$data = array_merge($data,search_slots_session($_player ->vars["id"], $from, $to,17));
		$data = array_merge($data,search_slots_session($_player ->vars["id"], $from, $to,18));
		$data = array_merge($data,search_slots_session($_player ->vars["id"], $from, $to,24));
		$data = array_merge($data,search_slots_session($_player ->vars["id"], $from, $to,25));
		$data = array_merge($data,search_slots_session($_player ->vars["id"], $from, $to,29));
		$data = array_merge($data,search_slots_session($_player ->vars["id"], $from, $to,32));
		$data = array_merge($data,search_slots_session($_player ->vars["id"], $from, $to,34));
		$data = array_merge($data,search_videopoker_session($_player ->vars["id"], $from, $to,"jacks"));
		$data = array_merge($data,search_videopoker_session($_player ->vars["id"], $from, $to,"deuces"));
		$data = array_merge($data,search_videopoker_session($_player ->vars["id"], $from, $to,"afs"));
		$data = array_merge($data,search_roulette_session($_player ->vars["id"], $from, $to));
		$data = array_merge($data,search_craps_session_rolls($_player ->vars["id"], $from, $to));
		$data = array_merge($data,search_baccarat_session($_player ->vars["id"], $from, $to));
		$data = array_merge($data,search_keno_session($_player ->vars["id"], $from, $to));
		
		$data = array_merge($data,search_poker_session($_player ->vars["id"], $from, $to,"holdem"));
		$data = array_merge($data,search_poker_session($_player ->vars["id"], $from, $to,"caribbean"));
		$data = array_merge($data,search_poker_session($_player ->vars["id"], $from, $to,"three_card"));
		
		ksort($data);
		$data = array_reverse($data);
}
?>

<form method="post">
    
    From: 
    <input name="from" class="form-control small_control" type="date" value="<? echo $from ?>">&nbsp;&nbsp;
    
    To:
    <input name="to" class="form-control small_control" type="date" value="<? echo $to ?>">&nbsp;&nbsp;
    
    <button type="submit" class="btn btn-default">Search</button>
    

</form>

<br><br>
<div class="dataTable_wrapper">

    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Date</th>
                <? if(is_null($game)){ ?>
                <th>Game</th>
                <? } ?>
                <th>Detail</th>
                <th>Free Play</th>
                <th>Bet Amount</th>
                <th>Settle Amount</th>
            </tr>
        </thead>
        <tbody>
            <? $i=1; foreach($data as $item){ $i++; if($i%2){$class = "odd";}else{$class = "even";} ?>
            <tr class="<? echo $class ?>">
                <td><? echo $item ->vars["id"] ?></td>
                <td><? echo $item ->vars["start_date"] ?></td>
                <? if(is_null($game)){ ?>
                <td><? echo $item ->vars["game_name"] ?></td>
                <? } ?>
                <td><? echo $item->get_hand_detail(); ?></td>
                <td><? echo print_boolean($item ->vars["free_play"]) ?></td>
                <td><? echo $item ->get_bet_amount_detail() ?></td>
                <td><? echo $item ->vars["settle"];//echo $item ->vars["win_amount"] + $item ->vars["win_amount2"] ?></td>
          </tr>
            <? } ?>
            
        </tbody>
    </table>
    
</div>

<? }else{echo "No data found";} ?>


<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/web/footer.php"); ?>