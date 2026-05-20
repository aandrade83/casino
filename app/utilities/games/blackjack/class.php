<?
class blackjack_sh{
	var $cards = array();
	var $player_hand = array();
	var $player_hand2 = array();
	var $dealer_hand = array();
	var $player;
	var $game_status = "";
	var $game_status2 = "";
	var $win_amount = 0;
	var $win_amount2 = 0;
	var $game_finished = 0;
	var $game_finished2 = 0;
	var $temp_game_finished2 = 0;
	var $can_split = 0;
	var $bet_amount = 0;
	var $bet_amount2 = 0;
	var $session = NULL;
	var $splited = 0;
	var $settle = 0;
	var $pf_seed = "na";
	var $using_pf = false;
	var $pf_data = "";
	
	//SETTINGS
	var $number_of_decks = 8;
	
	function __construct($id_player) {
	   global $_deck;
	   
	   //initialize cards
	   $this ->cards = prepare_decks($this->number_of_decks);
	   
	   $this->player = $id_player;
	   
	}
	
	function start_pf($pf_seed, $pf_data = NULL){
		 if(is_numeric($pf_seed)){
			 //use provably fair
			 $this ->using_pf = true;
			 $this ->cards = seed_shuffle($this ->cards,$pf_seed);
			 if(!is_null($pf_data)){
				 $this->pf_data = $pf_data;
			 }
		 }
	}
	
	function start_game(){
		
	}
	
	function get_card_value($card){
		$value = substr($card,1);
		
		if(!is_numeric($value)){
			switch($value){ 
				case "J":
				case "Q":
				case "K":
					$value = 10;
				break;
				case "A":
					$value = 1;
				break;
			}	
		}
		return $value*1;
	}
	
	function get_card_symbol($card){
		$parts = str_split($card);
		$symbol = $parts[1];
		return $symbol;
	}
	
	function is_ten($card){
		$is = false;
		$number = substr($card,1);
		if($number."" == "10"){$is = true;}
		return $is;
	}
	
	function is_blackjack($card1,$card2){
		global $_card_symbols;
		$sym1 = $this ->get_card_symbol($card1);
		$sym2 = $this ->get_card_symbol($card2);

		if(($sym1 == "A" && (in_array($sym2,$_card_symbols) || $this ->is_ten($card2) )) || ($sym2 == "A" && (in_array($sym1,$_card_symbols) || $this ->is_ten($card1) ))){
			$is = true;
		}else{$is = false;}
		
		return $is;		
	}
	
	function is_dealer_start_blackjack($card1,$card2){
		global $_card_symbols;
		$sym1 = $this ->get_card_symbol($card1);
		$sym2 = $this ->get_card_symbol($card2);
		
		if((in_array($sym2,$_card_symbols) || $this ->is_ten($card2)) && $sym1 == "A"){
			$is = true;
		}else{$is = false;}
		
		return $is;		
	}
	
	function get_hand_value($type = "player", $to_compare = false){
		
		switch($type){ 
			case "player":
				$cards = $this ->player_hand;
			break;
			case "dealer":
				$cards = $this ->dealer_hand;
			break;
			case "player2":
				$cards = $this ->player_hand2;
			break;
		}
		
		$total = 0;	
		$has_a = false;	
		foreach($cards as $card){
			$total += $this->get_card_value($card);
			if($this->get_card_symbol($card) == "A"){$has_a = true;}
		}
		
		if($has_a && $total < 21 && ($total+10) <= 21){
			if(($total+10) == 21){
				if($this->is_blackjack($cards[0],$cards[1])){
					$total = "BJ";
				}else{
					$total = 21;	
				}
			}else{
				$high = $total + 10;
				if($to_compare){$total = $high;}
				else{$total = $total."/".$high;}
			}
		}
		
		
		return $total;
	}
	
	function load_data($session){
		$this->session = $session;
		
		$this ->player = $session ->vars["player"];
		$this ->player_hand = explode(",",$session ->vars["player_hand"]);
		$this ->player_hand2 = explode(",",$session ->vars["player_hand2"]);
		$this ->dealer_hand = explode(",",$session ->vars["dealer_hand"]);	
		$this ->game_status = $session ->vars["game_status"];
		$this ->game_status2 = $session ->vars["game_status2"];
		$this ->bet_amount = $session ->vars["bet_amount"];
		$this ->bet_amount2 = $session ->vars["bet_amount2"];
		if(!is_numeric($this ->bet_amount2)){$this ->bet_amount2 = 0;}
		$this ->splited = $session ->vars["splited"];
		$this ->game_finished2 = $session ->vars["finished2"];
		$this ->win_amount = $session ->vars["win_amount"];
		if(!is_numeric($this ->win_amount)){$this ->win_amount = 0;}
		$this ->win_amount2 = $session ->vars["win_amount2"];
		if(!is_numeric($this ->win_amount2)){$this ->win_amount2 = 0;}
		$this ->settle = $session ->vars["settle"];
		
		//load pf data
		if(($session ->vars["pf"] ?? '') != '""' && ($session ->vars["pf"] ?? '') != ''){
			$this->pf_data = json_decode($session ->vars["pf"],true);
			if(!is_null($this->pf_data) && is_numeric($this->pf_data["pos"])){
				$this->start_pf($this->pf_data["pos"]);
			}
		}
		
		$removing_cards = array_merge($this ->player_hand,$this ->player_hand2);
		$removing_cards = array_merge($removing_cards,$this ->dealer_hand);
		foreach($removing_cards as $rcard){
			$position = array_search($rcard,$this ->cards);
			if(is_numeric($position)){array_splice($this ->cards, $position, 1);}
		}
		
	}
	
	function close_game(){
		global $game_id;
		if($this->session ->vars["finished"] && (!$this->session ->vars["splited"] || $this->session ->vars["finished2"])){
			$log = new _settle_log();
			$log ->vars["game"] = $game_id;
			$log ->vars["player"] = $this ->player;
			$log ->vars["bet_amount"] = $this->session ->vars["bet_amount"];
			$log ->vars["win_amount"] = $this->session ->vars["win_amount"];
			$log ->vars["settle"] = 0; //settle was move to action calling store settle log, to match dgs trasnactions, keep this one to have bet amounts
			$log ->vars["ldate"] = date("Y-m-d H:i:s");
			$log ->insert();	

			
		}
	}
	
	function store_settle_log($amount){
		global $game_id;
		$log = new _settle_log();
		$log ->vars["game"] = $game_id;
		$log ->vars["player"] = $this ->player;
		$log ->vars["bet_amount"] = 0;
		$log ->vars["win_amount"] = 0;
		$log ->vars["settle"] = $amount;
		$log ->vars["ldate"] = date("Y-m-d H:i:s");
		$log ->insert();	
	}
	
	function fold(){
		$this->win_amount = round($this->bet_amount/2,1);
		$this->game_status = "folded";
		$this->game_finished = 1;
		
		$this->session ->vars["game_status"] = "folded";
		$this->session ->vars["end_date"] = date("Y-m-d H:i:s");
		$this->session ->vars["finished"] = 1;
		$this->session ->vars["win_amount"] = 0;
		$this->session ->vars["settle"] += $this->win_amount*-1;
		$this->session ->vars["bet_amount"] = $this->win_amount;
		$this->session->update("game_status,end_date,finished,win_amount,bet_amount,settle");
		
		$this->close_game();	
		
		return $this->win_amount;
	}
	
	function dealer_hit(){
		$this ->dealer_hand[] = $this ->deal_card();
		$new_total = $this->get_hand_value("dealer",true);
		
		if($new_total <= 16){
			$new_total = $this->dealer_hit();
		}
		
		return $new_total;
	}
	
	function grade_game(){
		$player_total = $this->get_hand_value("player",true);
		$dealer_total = $this->get_hand_value("dealer",true);
		
		if($player_total > 21){
			$this->game_status = "busted";
			$this->settle = $this->bet_amount*-1;
		}else if($player_total == $dealer_total){
			$this->game_status = "push";
			$this->settle = 0;
		}else if($dealer_total > 21){
			$this->game_status = "dealer_bust";	
			$this->win_amount = $this->bet_amount;	
			$this->settle = $this->win_amount;
		}else if($player_total > $dealer_total){
			$this->game_status = "win";	
			$this->win_amount = $this->bet_amount;	
			$this->settle = $this->win_amount;
		}else if($dealer_total > $player_total){
			$this->game_status = "lose";	
			$this->settle = $this->bet_amount*-1;	
		}
		
	}
	
	function deal_card(){
		
		if($this ->using_pf){
			//provably fair
			$position = 0;
		}else{
			//random
			shuffle($this ->cards);
			$position = mt_rand(0,count($this ->cards)-1);
		}
			
		$new_card = $this ->cards[$position];
		array_splice($this ->cards, $position, 1);
		return $new_card;
	}
	
	function hit(){
		
		$this->can_split = 0;
		$this->game_status = "hitted";
		
		$new_card = $this->deal_card();
		$this ->player_hand[] = $new_card;
		
		$new_total = $this->get_hand_value("player", true);
		
		if($new_total > 21){
			$this->game_status = "busted";
			$this->game_finished = 1;
			$this->settle = $this->bet_amount*-1;
		}else if($new_total == 21){
			$dealer_total = $this->get_hand_value("dealer", true);
			$this->game_finished = 1;
			if($dealer_total <= 16){$this->dealer_hit();}
			$this->grade_game();
		}
		
		
		$this->session ->vars["finished"] = $this->game_finished;
		if($this->session ->vars["finished"]){
			$this->session ->vars["win_amount"] = $this->win_amount;
			$this->session ->vars["settle"] += $this->settle;
			$this->close_game();
		}else{
			$this->session ->vars["win_amount"] = 0;
		}
		$this->session ->vars["end_date"] = date("Y-m-d H:i:s");
		$this->session ->vars["player_hand"] = implode(",",$this->player_hand);
		$this->session ->vars["dealer_hand"] = implode(",",$this->dealer_hand);
		$this->session ->vars["game_status"] = $this->game_status;
		$this->session->update("player_hand,dealer_hand,game_status,finished,end_date,win_amount,settle");		
		
		return $new_card;
		
	}
	
	function double(){
		
		$this->bet_amount *= 2;
		
		$new_card = $this->deal_card();
		$this ->player_hand[] = $new_card;
		
		$dealer_total = $this->get_hand_value("dealer", true);
		$player_total = $this->get_hand_value("player", true);
		$this->game_finished = 1;
		if($dealer_total <= 16 && $player_total <= 21){$this->dealer_hit();}
		$this->grade_game();
		
		$this->session ->vars["settle"] += $this->settle;
		$this->session ->vars["finished"] = $this->game_finished;
		$this->session ->vars["win_amount"] = $this->win_amount;
		$this->session ->vars["bet_amount"] = $this->bet_amount;
		$this->session ->vars["end_date"] = date("Y-m-d H:i:s");
		$this->session ->vars["player_hand"] = implode(",",$this->player_hand);
		$this->session ->vars["dealer_hand"] = implode(",",$this->dealer_hand);
		$this->session ->vars["game_status"] = $this->game_status;
		$this->session->update("player_hand,dealer_hand,game_status,finished,end_date,win_amount,bet_amount,settle");	
		
		$this->close_game();	
		
		return $new_card;
		
	}
	
	function split_hand(){
		
		$this ->player_hand2[0] = $this ->player_hand[1];
		
		$this ->player_hand[1] = $this ->deal_card();
		
		$this ->player_hand2[1] = $this ->deal_card();
		
		$this->splited = 1;
		
		if($this ->is_blackjack($this ->player_hand2[0],$this ->player_hand2[1])){
		
			
			$this->game_status = "player_blackjack";
			$this->game_finished = 1;
			$this->win_amount = $this ->bet_amount*1.5;
			$this->settle = $this->win_amount;
			
		}else{
			$this->game_status = "dealed";	
			$this->settle = $this->bet_amount*-1;
		}
		
		
		$this->game_status2 = "dealed";	
		
		$this->bet_amount2 = $this->bet_amount;
		$this->session ->vars["settle"] += $this->settle;
		$this->session ->vars["splited"] = 1;
		$this->session ->vars["bet_amount2"] = $this->bet_amount;
		$this->session ->vars["player_hand"] = implode(",",$this->player_hand2);
		$this->session ->vars["player_hand2"] = implode(",",$this->player_hand);
		$this->session ->vars["game_status"] = $this->game_status;
		$this->session ->vars["game_status2"] = $this->game_status2;
		
		/*$this->session ->vars["win_amount"] = $this->win_amount;
		
		$this->session ->vars["finished"] = $this->game_finished;*/
		$this->session->update("splited,bet_amount2,player_hand,player_hand2,game_status,game_status2,settle");
		
		
	}
	
	function stand(){
		
		$dealer_total = $this->get_hand_value("dealer", true);
		$this->game_finished = 1;
		if($dealer_total <= 16){$this->dealer_hit();}
		$this->grade_game();
		
		$this->session ->vars["settle"] += $this->settle;
		$this->session ->vars["finished"] = $this->game_finished;
		$this->session ->vars["win_amount"] = $this->win_amount;
		$this->session ->vars["end_date"] = date("Y-m-d H:i:s");
		$this->session ->vars["player_hand"] = implode(",",$this->player_hand);
		$this->session ->vars["dealer_hand"] = implode(",",$this->dealer_hand);
		$this->session ->vars["game_status"] = $this->game_status;
		$this->session->update("player_hand,dealer_hand,game_status,finished,end_date,win_amount,settle");	
		
		$this->close_game();	
		
	}
	
	function insurance($accepted){
	
		$dealer_total = $this->get_hand_value("dealer", true);
		
		if($accepted){
			$this->settle = ($this->bet_amount/2)*-1;
			$this->session ->vars["insurance"]  = ($this->bet_amount/2);
		}else{
			$this->session ->vars["insurance"] = 0;	
		}
		
		if($this ->is_blackjack($this ->dealer_hand[0],$this ->dealer_hand[1]) || $dealer_total == 21){
			if($accepted){
				$this->win_amount = $this->bet_amount;
				$this->settle += ($this->bet_amount/2);
			}
			$this->game_status = "dealer_blackjack";
			$this->game_finished = 1;
		}else{
			$this->game_status = "dealed";	
		}
		
		$this->session ->vars["finished"] = $this->game_finished;
		$this->session ->vars["win_amount"] = $this->win_amount;
		$this->session ->vars["settle"] = $this->settle;//($this->bet_amount*-1)+$this->win_amount;
		if($this->game_finished){			
			$this->session ->vars["end_date"] = date("Y-m-d H:i:s");
			$this->close_game();
		}
		$this->session ->vars["game_status"] = $this->game_status;
		$this->session->update("game_status,finished,end_date,win_amount,settle,insurance");
		
	}
	
	function can_split(){
		$can = 0;
		if($this->game_status = "dealed" && $this->get_card_value($this ->player_hand[0]) == $this->get_card_value($this ->player_hand[1]) && count($this ->player_hand2) < 2){
			$can = 1;	
		}	
		return $can;
	}
	
	function deal(){
		$this->game_status = "dealed";
		
		
		if($this ->player == 38){
			$this ->player_hand[] = "HK";
			$this ->dealer_hand[] = $this ->deal_card();		
			$this ->player_hand[] = "HQ";
			$this ->dealer_hand[] = $this ->deal_card();
		}else{
			$this ->player_hand[] = $this ->deal_card();
			$this ->dealer_hand[] = $this ->deal_card();		
			$this ->player_hand[] = $this ->deal_card();
			$this ->dealer_hand[] = $this ->deal_card();	
		}
		
		$session = new _bj_session();
		$session ->vars["player"] = $this ->player;
		$session ->vars["player_hand"] = implode(",",$this ->player_hand);
		$session ->vars["dealer_hand"] = implode(",",$this ->dealer_hand);
		$session ->vars["bet_amount"] = $this ->bet_amount;
		$session ->vars["game_status"] = "dealed";
		$session ->vars["start_date"] = date("Y-m-d H:i:s");
		
		$player_total = $this->get_hand_value("player", true);	
		$dealer_total = $this->get_hand_value("dealer", true);	
		
		if($this ->is_blackjack($this ->player_hand[0],$this ->player_hand[1])){
			if($this ->is_blackjack($this ->dealer_hand[0],$this ->dealer_hand[1])){
				$this->game_status = "push_blackjack";
				$this->game_finished = 1;
				$session ->vars["win_amount"] = 0;
				$this->settle = 0;
			}else{
				$this->game_status = "player_blackjack";
				$this->game_finished = 1;
				
				$this->win_amount = $this ->bet_amount*1.5;
				$session ->vars["win_amount"] = $this->win_amount;
				$this->settle = $this->win_amount;
			}
		}else if($this ->is_dealer_start_blackjack($this ->dealer_hand[0],$this ->dealer_hand[1])){
			$this->game_status = "dealer_blackjack";
			$this->game_finished = 1;
			$session ->vars["win_amount"] = 0;
			$this->settle = $this->bet_amount*-1;
		}else if($this ->get_card_symbol($this ->dealer_hand[1]) == "A"){
			$this->game_status = "ask_insurance";
		}else if($player_total == 21){
			$this->game_finished = 1;
			if($dealer_total <= 16){$this->dealer_hit();}
			$this->grade_game();
		}
		
		$session ->vars["finished"] = $this->game_finished;
		if($session ->vars["finished"]){
			$session ->vars["end_date"] = date("Y-m-d H:i:s");
			$session ->vars["settle"] = $this->settle;
			$this->session = $session;
			$this->close_game();
		}
		$session ->vars["game_status"] = $this->game_status;
		
		//store pf data
		if($this->pf_data != ""){
			$session ->vars["pf"] = json_encode($this->pf_data);
		}
		
		$session->insert();

		
		return $session ->vars["id"];
		
	}
	
	function move_to_second_hand($check_unfinish = false){
		global $player_token, $casino_name, $game, $casino_id_base, $_api;
		$second_hand = 0;
		if($this ->splited && $this ->game_finished && !$this ->game_finished2){
			
			
			if($this ->is_blackjack($this ->player_hand2[0],$this ->player_hand2[1])){
		
				$this->game_status2 = "player_blackjack";
				$this->temp_game_finished2 = 1;
				$this->win_amount2 = $this ->bet_amount2*1.5;
				$this->settle = $this->win_amount2;
				
				$win_amount = $this->bet_amount2 + $this->win_amount2;
				$data = $_api->credit_prize($player_token, $win_amount, $casino_name ." ". $game ->vars["name"], $casino_id_base . $game ->vars["id"]);
				
			}
			
			
			$second_hand = 1;
			$this->game_finished = $this->temp_game_finished2;
			$this->game_finished2 = 1;
			$temp_hand = $this->player_hand;
			$this->player_hand = $this->player_hand2;
			$this->player_hand2 = $temp_hand;
			$temp_amount =  $this->bet_amount;
			$this->bet_amount = $this->bet_amount2;
			$this->bet_amount2 = $temp_amount;
			$temp_win =  $this->win_amount2;
			$this->win_amount2 = $this->win_amount;
			$this->win_amount = $temp_win;
			$temp_status =  $this->game_status2;
			$this->game_status2 = $this->game_status;
			
			if($this->game_finished ){
				$this->game_status = $temp_status;
				$this->session ->vars["settle"] += $this->settle;
			}else{
				$this->game_status = "dealed";	
			}
			
			
			$this->session ->vars["finished"] = $this->game_finished;
			$this->session ->vars["finished2"] = $this->game_finished2;
			$this->session ->vars["player_hand"] = implode(",",$this->player_hand);
			$this->session ->vars["player_hand2"] = implode(",",$this->player_hand2);
			$this->session ->vars["bet_amount"] = $this->bet_amount;
			$this->session ->vars["bet_amount2"] = $this->bet_amount2;
			$this->session ->vars["win_amount2"] = $this->win_amount2;
			$this->session ->vars["win_amount"] = $this->win_amount;
			$this->session ->vars["game_status2"] = $this->game_status2;
			$this->session ->vars["game_status"] = $this->game_status;
			if($this->game_finished && $this->game_finished2){
				$this->session ->vars["end_date"] = date("Y-m-d H:i:s");
			}
			$this->session ->update("settle,finished,finished2,player_hand,player_hand2,bet_amount,bet_amount2,win_amount2,win_amount,game_status2,game_status,end_date");
			
		}else if($this ->splited && $check_unfinish && !$this ->game_finished2){
			$second_hand = 1;
		}
		return $second_hand;
	}
   
   
}	
?>