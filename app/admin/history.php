<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/header.php"); ?>

<div class="row">
    <div class="col-lg-12">
        <h1 class="page-header">Player History</h1>
    </div>
</div>

<?
$from = param("from");
if($from == ""){$from = date("Y-m-d");}	
$to = param("to");	
if($to == ""){$to = date("Y-m-d");}	
$splayer = param("splayer");
$sgame = param("sgame");

switch($sgame){ 
	case "1":
		$data = search_blackjack_session($splayer, $from, $to,"regular");		
	break;
	case "31":
		$data = search_blackjack_session($splayer, $from, $to,"spanish");		
	break;
	case "33":
		$data = search_blackjack_session($splayer, $from, $to,"double_exposure");		
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
		$data = search_slots_session($splayer, $from, $to, $sgame);		
	break;
	case "5":
		$data = search_videopoker_session($splayer, $from, $to, "jacks");		
	break;
	case "12":
		$data = search_videopoker_session($splayer, $from, $to, "deuces");		
	break;
	case "27":
		$data = search_videopoker_session($splayer, $from, $to, "afs");		
	break;
	case "20":
	case "6":
		$data = search_roulette_session($splayer, $from, $to);		
	break;
	case "7":
		$data = search_craps_session_rolls($splayer, $from, $to);		
	break;
	case "8":
		$data = search_craps_session($splayer, $from, $to);		
	break;
	case "10":
		$data = search_baccarat_session($splayer, $from, $to);		
	break;
	case "15":
		$data = search_poker_session($splayer, $from, $to,"holdem");		
	break;
	case "21":
		$data = search_poker_session($splayer, $from, $to,"caribbean");		
	break;
	case "26":
		$data = search_poker_session($splayer, $from, $to,"three_card");	 	
	break;
	case "28":
		$data = search_keno_session($splayer, $from, $to);		
	break;
	default:
		$data = array();
		$data = array_merge($data,search_blackjack_session($splayer, $from, $to,"regular"));
		$data = array_merge($data,search_blackjack_session($splayer, $from, $to,"spanish"));
		$data = array_merge($data,search_blackjack_session($splayer, $from, $to,"double_exposure"));
		$data = array_merge($data,search_slots_session($splayer, $from, $to,4));
		$data = array_merge($data,search_slots_session($splayer, $from, $to,9));
		$data = array_merge($data,search_slots_session($splayer, $from, $to,11));
		$data = array_merge($data,search_slots_session($splayer, $from, $to,14));
		$data = array_merge($data,search_slots_session($splayer, $from, $to,17));
		$data = array_merge($data,search_slots_session($splayer, $from, $to,18));
		$data = array_merge($data,search_slots_session($splayer, $from, $to,19));
		$data = array_merge($data,search_slots_session($splayer, $from, $to,24));
		$data = array_merge($data,search_slots_session($splayer, $from, $to,25));
		$data = array_merge($data,search_slots_session($splayer, $from, $to,29));
		$data = array_merge($data,search_slots_session($splayer, $from, $to,32));
		$data = array_merge($data,search_slots_session($splayer, $from, $to,34));
		$data = array_merge($data,search_videopoker_session($splayer, $from, $to,"jacks"));
		$data = array_merge($data,search_videopoker_session($splayer, $from, $to,"deuces"));
		$data = array_merge($data,search_videopoker_session($splayer, $from, $to,"afs"));
		$data = array_merge($data,search_roulette_session($splayer, $from, $to));
		$data = array_merge($data,search_craps_session_rolls($splayer, $from, $to));
		$data = array_merge($data,search_baccarat_session($splayer, $from, $to));
		$data = array_merge($data,search_keno_session($splayer, $from, $to));
		
		$data = array_merge($data,search_poker_session($splayer, $from, $to,"holdem"));
		$data = array_merge($data,search_poker_session($splayer, $from, $to,"caribbean"));
		$data = array_merge($data,search_poker_session($splayer, $from, $to,"three_card"));
		
		ksort($data);
		$data = array_reverse($data);
}

?>

<form method="get">
    
    From: 
    <input name="from" class="form-control small_control" type="date" value="<? echo $from ?>">&nbsp;&nbsp;
    
    To:
    <input name="to" class="form-control small_control" type="date" value="<? echo $to ?>">&nbsp;&nbsp;
    
    Player:
    <? $list = sort_players($_agent ->get_all_players()); ?>
    <select class="form-control small_control" name="splayer" id="splayer">
        <? foreach($list as $item){ ?>
            <option value="<? echo $item ->vars["id"] ?>" <? if($splayer == $item ->vars["id"]){ ?> selected <? } ?> >
                <? echo $item ->vars["account"] ?>
            </option>
        <? } ?>
    </select>&nbsp;&nbsp;
    
    Game:
    <? $games = get_all_company_games($_company ->vars["id"]); ?>
    <select class="form-control small_control" name="sgame" id="sgame">
    	<option value="">All</option>
        <? foreach($games as $game){ ?>
            <option value="<? echo $game ->vars["id"] ?>" <? if($sgame == $game ->vars["id"]){ ?> selected <? } ?> >
                <? echo $game ->vars["name"] ?>
            </option>
        <? } ?>
    </select>&nbsp;&nbsp;
    
    <button type="submit" class="btn btn-default" name="searcher">Search</button>
    

</form>

<br><br>
<div class="dataTable_wrapper">

	<? 
	$total_bet = 0;
	$total_win = 0;
	$settle = 0;
	?>

    <table class="table table-striped table-bordered table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Game</th>
                <th>Date</th>
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
                <td><? echo $item ->vars["game_name"] ?></td>
                <td><? echo $item ->vars["start_date"] ?></td>
                <td><? echo $item->get_hand_detail(); ?></td>
                <td><? echo print_boolean($item ->vars["free_play"]) ?></td>
                <td><? echo $item ->get_bet_amount_detail() ?></td>
                <td><? echo $item ->vars["settle"];//echo $item ->vars["win_amount"] + $item ->vars["win_amount2"] ?></td>
          </tr>
          
         <!-- <tr>
          	<td colspan="100">-->
                <? 
				/*$win_amount = ($item ->vars["win_amount"] + $item ->vars["win_amount2"]);
				$bet_amount = ($item ->vars["bet_amount"] + $item ->vars["bet_amount2"]);
				
				if(!$item ->vars["free_play"]){$total_bet += $bet_amount;}
				
				$total_win += $win_amount;				
				$settle -= $bet_amount;
				if(($win_amount > 0 || $item ->vars["game_status"] == "push") && !$item ->vars["free_play"]){$settle += ($win_amount + $bet_amount);}*/
				
				
				$settle += $item ->vars["settle"];
				?>
            <!--</td>
         </tr>	 -->
            <? } ?>
         
            
        </tbody>
        <thead>
            <tr>
                <th colspan="5">Total</th>
                <th><? //echo $total_bet; ?></th>
                <th><? echo $settle; ?></th>
            </tr>
          <?php /*?>  <tr>
                <th colspan="5"></th>
                <th><? echo $settle; ?></th>
                <th></th>
            </tr><?php */?>
        </thead>
    </table>
    
</div>



<? include($_SERVER['DOCUMENT_ROOT'] ."/utilities/ui/admin/footer.php"); ?>