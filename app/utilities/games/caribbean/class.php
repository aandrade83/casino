<?
class _caribbean_poker{
	var $player;
	var $win_amount = 0;
	var $credit_amount = 0;
	var $winner = 0;
	var $win_level = 0;
	var $session = NULL;
	//provably fair
	var $pf_seed = "na";
	var $using_pf = false;
	var $pf_data = "";
	var $player_hand_level = 0;
	var $dealer_hand_level = 0;
	var $player_best_hand = array();
	var $dealer_best_hand = array();
	
	var $cards = array();

	//SETTINGS
	var $number_of_decks = 1;
	
	
	function __construct($id_player) {
		 global $_deck;
	   	   
		 $this->player = $id_player;
		
		 //initialize cards
		 $this ->cards = prepare_decks($this->number_of_decks);
		 
	   
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
	
	function load_data($session){
		$this->session = $session;
		
		$this ->player = $session ->vars["player"];
		
		//load pf data
		if(($session ->vars["pf"] ?? '') != '""' && ($session ->vars["pf"] ?? '') != ''){
			$this->pf_data = json_decode($session ->vars["pf"],true);
			if(!is_null($this->pf_data) && is_numeric($this->pf_data["pos"])){
				$this->start_pf($this->pf_data["pos"]);
			}
		}
		
		$this->player_hand_level = $this->calculate_hand_level(explode(",",$this->session ->vars["player_hand"]));
		
		$removing_cards = explode(",",$session ->vars["player_hand"].",".$session ->vars["dealer_hand"].",".($session ->vars["table_cards"] ?? ""));
		foreach($removing_cards as $rcard){
			$position = array_search($rcard,$this ->cards);
			if(is_numeric($position)){array_splice($this ->cards, $position, 1);}
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
	
	function deal($bet_amount){
		
		$player_hand = array();
		$dealer_hand = array();
		
		$player_hand[] = $this->deal_card();
		$player_hand[] = $this->deal_card();
		$player_hand[] = $this->deal_card();
		$player_hand[] = $this->deal_card();
		$player_hand[] = $this->deal_card();
		
		$dealer_hand[] = $this->deal_card();
		$dealer_hand[] = $this->deal_card();
		$dealer_hand[] = $this->deal_card();
		$dealer_hand[] = $this->deal_card();
		$dealer_hand[] = $this->deal_card();
		
		$player_str_hand = implode(",",$player_hand);
		$dealer_str_hand = implode(",",$dealer_hand);
		
		$session = new _poker_session();
		$session ->vars["player"] = $this ->player;
		$session ->vars["ante_bet"] = $bet_amount;
		$session ->vars["player_hand"] = $player_str_hand;
		$session ->vars["dealer_hand"] = $dealer_str_hand;
		$session ->vars["status"] = "dealed";
		$session ->vars["poker_type"] = "caribbean";
		$session ->vars["settle"] = $bet_amount*-1;
		$session ->vars["start_date"] = date("Y-m-d H:i:s");
		
		//store pf data
		if($this->pf_data != ""){
			$session ->vars["pf"] = json_encode($this->pf_data);
		}
		
		$session ->insert();
		$this->player_hand_level = $this->calculate_hand_level($player_hand);
		
		$deal_result = array();
		$deal_result["cards"] = $player_str_hand;
		$deal_result["player_level"] = $this->player_hand_level;
		$deal_result["dealer_card"] = $dealer_hand[4];
		
		return $deal_result;
		
	}
	
	function call(){
		
		$this->dealer_hand_level = $this->calculate_hand_level(explode(",",$this->session ->vars["dealer_hand"]),true);
		$this->player_hand_level = $this->calculate_hand_level(explode(",",$this->session ->vars["player_hand"]));
		
		$this->session ->vars["call_bet"] = ($this->session ->vars["ante_bet"]*2);
		$this->session ->vars["settle"] += ($this->session ->vars["call_bet"]*-1);
		$this->session ->update();	
		
		$call_result = array();
		$call_result["dealer_cards"] = $this->session ->vars["dealer_hand"];
		$call_result["dealer_level"] = $this->dealer_hand_level;
		
		return $call_result;
		
		
	}
	
	function get_exp_by_level($level){ 
		switch($level){ 
			case 9:
				$exp = 500;
			break;
			case 8:
				$exp = 50;
			break;
			case 7:
				$exp = 20;
			break;
			case 6:
				$exp = 7;
			break;
			case 5:
				$exp = 5;
			break;
			case 4:
				$exp = 4;
			break;
			case 3:
				$exp = 3;
			break;
			case 2:
				$exp = 2;
			break;
			case 1:
				$exp = 1;
			break;
			default :
				$exp = 1;
			break;
		}
		return $exp;
	}
	
	function grade(){
		$tie_winner = "";
		if($this->player_hand_level == $this->dealer_hand_level){
			$tie_winner = $this->break_tie(); 
		}
		
		if($this->player_hand_level > $this->dealer_hand_level || $tie_winner == "player"){
			$result = "win";
			$this->winner = "player";
			$this->win_amount = 0;
			//calculate win amount here
			
			if($this->dealer_hand_level < 0){
				//dealer dont qualify
				$this->win_amount += $this->session ->vars["ante_bet"];	
				$this->credit_amount = $this->win_amount;
			}else{
				$exp = $this->get_exp_by_level($this->player_hand_level);	
				$this->win_amount += ($this->session ->vars["ante_bet"] + ($this->session ->vars["call_bet"]*$exp));  	
				$this->credit_amount = $this->win_amount;				
			}
			
			$this->credit_amount += ($this->session ->vars["call_bet"]+$this->session ->vars["ante_bet"]);
			
			
		}else if($this->player_hand_level < $this->dealer_hand_level || $tie_winner == "dealer"){
			$result = "lose";
			$this->winner = "dealer";
			$this->win_amount = 0;
		}else{
			$result = "push";
			$this->winner = "player";
			$this->win_amount = 0;
			$this->credit_amount = ($this->session ->vars["call_bet"]+$this->session ->vars["ante_bet"]);
		}
		
		$this->session ->vars["finished"] = 1;
		$this->session ->vars["status"] = $result;
		$this->session ->vars["win_amount"] = $this->win_amount;
		if($this->win_amount > 0){$this->session ->vars["settle"] = $this->win_amount;}
		$this->session ->vars["end_date"] = date("Y-m-d H:i:s");
		$this->session ->update();	
		
		$this->store_settle();
		
		return $result;
		
	}
	
	function grade_high_card($pvalues,$dvalues, $max = 5){ //already reverse sorted by value
		$breaked = false;
		$i = 0;
		$winner = "tie";
		while(!$breaked && $i<$max){
			if($pvalues[$i] > $dvalues[$i]){
				$winner = "player";
				$breaked = true;
			}else if($pvalues[$i] < $dvalues[$i]){
				$winner = "dealer";
				$breaked = true;
			}else{
				$i++;
			}
		}
		return $winner;
	}
	
	function break_tie(){
		$winner = "tie";
		$pall_cards = array_merge(explode(",",($this->session ->vars["table_cards"] ?? "")),explode(",",$this->session ->vars["player_hand"]));
		$dall_cards = array_merge(explode(",",($this->session ->vars["table_cards"] ?? "")),explode(",",$this->session ->vars["dealer_hand"]));
		switch($this->player_hand_level){ 
			case 0:
				//tie with no winning hands, get highst cards
				$pvalues = $this->get_hand_in_rsorted_values($pall_cards);
				$dvalues = $this->get_hand_in_rsorted_values($dall_cards);
				$winner = $this->grade_high_card($pvalues,$dvalues);
			break;
			case 1:
				//tie with 1 pair
				$pvalues = $this->get_hand_in_rsorted_values($pall_cards);
				$pcounts = array_count_values($pvalues);
				arsort($pcounts);
				$pkeys = array_keys($pcounts);
				$ppair_value = $pkeys[0];
				
				$dvalues = $this->get_hand_in_rsorted_values($dall_cards);
				$dcounts = array_count_values($dvalues);
				arsort($dcounts);
				$dkeys = array_keys($dcounts);
				$dpair_value = $dkeys[0];
				
				if($ppair_value > $dpair_value){
					$winner = "player";	
				}else if($ppair_value < $dpair_value){
					$winner = "dealer";	
				}else{
					$pvalues = array_diff($pvalues, array($ppair_value));
					$dvalues = array_diff($dvalues, array($ppair_value));
					$winner = $this->grade_high_card(array_values($pvalues),array_values($dvalues),3);
				}				
			break;
			case 2:
				//tie with 2 pair
				$pvalues = $this->get_hand_in_rsorted_values($pall_cards);
				$pcounts = array_count_values($pvalues);
				
				//get just pairs values
				$fil_pcounts = array();
				foreach(array_keys($pcounts) as $pc){
					if($pcounts[$pc] == 2){
						$fil_pcounts[] = $pc;	
					}	
				}
				
				rsort($fil_pcounts);
				
				$ppair_value1 = $fil_pcounts[0];
				$ppair_value2 = $fil_pcounts[1];
				
				$dvalues = $this->get_hand_in_rsorted_values($dall_cards);
				$dcounts = array_count_values($dvalues);
				
				//get just pairs values
				$fil_dcounts = array();
				foreach(array_keys($dcounts) as $dc){
					if($dcounts[$dc] == 2){
						$fil_dcounts[] = $dc;	
					}	
				}
				
				rsort($fil_dcounts);
				
				$dpair_value1 = $fil_dcounts[0];
				$dpair_value2 = $fil_dcounts[1];
				
				if($ppair_value1 > $dpair_value1){
					$winner = "player";	
				}else if($ppair_value1 < $dpair_value1){
					$winner = "dealer";	
				}else{
					
					if($ppair_value2 > $dpair_value2){
						$winner = "player";	
					}else if($ppair_value2 < $dpair_value2){
						$winner = "dealer";	
					}else{
					
						$pvalues = array_diff($pvalues, array($ppair_value1,$ppair_value2));
						$dvalues = array_diff($dvalues, array($dpair_value1,$dpair_value2));
						$winner = $this->grade_high_card(array_values($pvalues),array_values($dvalues),1);
					}
				}				
			break;
			case 3:
				//tie with 3 of a kind
				$pvalues = $this->get_hand_in_rsorted_values($pall_cards);
				$pcounts = array_count_values($pvalues);
				
				//get just pairs values
				$fil_pcounts = array();
				foreach(array_keys($pcounts) as $pc){
					if($pcounts[$pc] == 3){
						$fil_pcounts[] = $pc;	
					}	
				}
				
				rsort($fil_pcounts);
				$ppair_value = $fil_pcounts[0];
				
				$dvalues = $this->get_hand_in_rsorted_values($dall_cards);
				$dcounts = array_count_values($dvalues);
				
				//get just pairs values
				$fil_dcounts = array();
				foreach(array_keys($dcounts) as $dc){
					if($dcounts[$dc] == 3){
						$fil_dcounts[] = $dc;	
					}	
				}
				
				rsort($fil_dcounts);				
				$dpair_value = $fil_dcounts[0];
				
				if($ppair_value > $dpair_value){
					$winner = "player";	
				}else if($ppair_value < $dpair_value){
					$winner = "dealer";	
				}else{
					$pvalues = array_diff($pvalues, array($ppair_value));
					$dvalues = array_diff($dvalues, array($ppair_value));
					$winner = $this->grade_high_card(array_values($pvalues),array_values($dvalues),2);
				}				
			break;
			case 4:
				//tie with straigth
				$pvalues = $this->get_hand_in_rsorted_values($pall_cards);
				$puniques = array_values(array_unique($pvalues));
				$pmax_straight = 0;			
				for($i=0;$i<3;$i++){
					$temp_check = array();
					$start = $pvalues[$i];
					$temp_check[] = $start;
					for($x=1;$x<5;$x++){
						$temp_check[] = ($start - $x);
					}

					if(in_array($temp_check[0],$puniques) && in_array($temp_check[1],$puniques) && in_array($temp_check[2],$puniques) && in_array($temp_check[3],$puniques) && in_array($temp_check[4],$puniques)){
						$pmax_straight = $start;
						break;
					}
				}
				
				$dvalues = $this->get_hand_in_rsorted_values($dall_cards);
				$duniques = array_values(array_unique($dvalues));
				$dmax_straight = 0;			
				for($i=0;$i<3;$i++){
					$temp_check = array();
					$start = $dvalues[$i];
					$temp_check[] = $start;
					for($x=1;$x<5;$x++){
						$temp_check[] = ($start - $x);
					}

					if(in_array($temp_check[0],$duniques) && in_array($temp_check[1],$duniques) && in_array($temp_check[2],$duniques) && in_array($temp_check[3],$duniques) && in_array($temp_check[4],$duniques)){
						$dmax_straight = $start;
						break;
					}
				}
				
				if($pmax_straight > $dmax_straight){
					$winner = "player";	
				}else if($pmax_straight < $dmax_straight){
					$winner = "dealer";	
				}else{
					$winner = "tie";		
				}
				
			break;
			
			case 5:
				//tie with flush
				$pparts = $this->split_hand($pall_cards);
				$pcounts = array_count_values($pparts["colors"]);
				arsort($pcounts);
				$pkeys = array_keys($pcounts);
				$pflush_color = $pkeys[0];
				$pflush_cards = array();
				foreach($pall_cards as $card){
					if(contains($card,$pflush_color)){$pflush_cards[] = $card;}	
				}
				$pvalues = $this->get_hand_in_rsorted_values($pflush_cards);
				
				$dparts = $this->split_hand($dall_cards);
				$dcounts = array_count_values($dparts["colors"]);
				arsort($dcounts);
				$dkeys = array_keys($dcounts);
				$dflush_color = $dkeys[0];
				$dflush_cards = array();
				foreach($dall_cards as $card){
					if(contains($card,$dflush_color)){$dflush_cards[] = $card;}	
				}
				$dvalues = $this->get_hand_in_rsorted_values($dflush_cards);
				
				$breaked = false;
				$i = 0;
				$winner = "tie";
				while(!$breaked && $i<5){
					if($pvalues[$i] > $dvalues[$i]){
						$winner = "player";
						$breaked = true;
					}else if($pvalues[$i] < $dvalues[$i]){
						$winner = "dealer";
						$breaked = true;
					}else{
						$i++;
					}
				}
				
			break;
			
			case 6:
				//tie with full house
				$pvalues = $this->get_hand_in_rsorted_values($pall_cards);
				$pcounts = array_count_values($pvalues);
				arsort($pcounts);
				$pkeys = array_keys($pcounts);
				$ppair_value1 = $pkeys[0];
				$ppair_value2 = $pkeys[1];
				
				$dvalues = $this->get_hand_in_rsorted_values($dall_cards);
				$dcounts = array_count_values($dvalues);
				arsort($dcounts);
				$dkeys = array_keys($dcounts);
				$dpair_value1 = $dkeys[0];
				$dpair_value2 = $dkeys[1];
				
				if($ppair_value1 > $dpair_value1){
					$winner = "player";	
				}else if($ppair_value1 < $dpair_value1){
					$winner = "dealer";	
				}else{
					
					if($ppair_value2 > $dpair_value2){
						$winner = "player";	
					}else if($ppair_value2 < $dpair_value2){
						$winner = "dealer";	
					}else{
					
						$winner = "tie";	
					
					}
				}				
			break;
			
			case 7:
				//tie with four of a kind
				$pvalues = $this->get_hand_in_rsorted_values($pall_cards);
				$pcounts = array_count_values($pvalues);
				arsort($pcounts);
				$pkeys = array_keys($pcounts);
				$ppair_value = $pkeys[0];
				
				$dvalues = $this->get_hand_in_rsorted_values($dall_cards);
				$dcounts = array_count_values($dvalues);
				arsort($dcounts);
				$dkeys = array_keys($dcounts);
				$dpair_value = $dkeys[0];
				
				if($ppair_value > $dpair_value){
					$winner = "player";	
				}else if($ppair_value < $dpair_value){
					$winner = "dealer";	
				}else{
					$pvalues = array_diff($pvalues, array($ppair_value));
					$dvalues = array_diff($dvalues, array($ppair_value));
					$winner = $this->grade_high_card(array_values($pvalues),array_values($dvalues),1);
				}				
			break;
			
			case 8:
				//tie with straigth flush
				$pparts = $this->split_hand($pall_cards);
				$pcounts = array_count_values($pparts["colors"]);
				arsort($pcounts);
				$pkeys = array_keys($pcounts);
				$pflush_color = $pkeys[0];
				$pflush_cards = array();
				foreach($pall_cards as $card){
					if(contains($card,$pflush_color)){$pflush_cards[] = $card;}	
				}
				$pvalues = $this->get_hand_in_rsorted_values($pflush_cards);
				$puniques = array_values(array_unique($pvalues));
				$pmax_straight = 0;			
				for($i=0;$i<3;$i++){
					$temp_check = array();
					$start = $pvalues[$i];
					$temp_check[] = $start;
					for($x=1;$x<5;$x++){
						$temp_check[] = ($start - $x);
					}

					if(in_array($temp_check[0],$puniques) && in_array($temp_check[1],$puniques) && in_array($temp_check[2],$puniques) && in_array($temp_check[3],$puniques) && in_array($temp_check[4],$puniques)){
						$pmax_straight = $start;
						break;
					}
				}
				
				$dparts = $this->split_hand($dall_cards);
				$dcounts = array_count_values($dparts["colors"]);
				arsort($dcounts);
				$dkeys = array_keys($dcounts);
				$dflush_color = $dkeys[0];
				$dflush_cards = array();
				foreach($dall_cards as $card){
					if(contains($card,$dflush_color)){$dflush_cards[] = $card;}	
				}
				$dvalues = $this->get_hand_in_rsorted_values($dflush_cards);
				$duniques = array_values(array_unique($dvalues));
				$dmax_straight = 0;			
				for($i=0;$i<3;$i++){
					$temp_check = array();
					$start = $dvalues[$i];
					$temp_check[] = $start;
					for($x=1;$x<5;$x++){
						$temp_check[] = ($start - $x);
					}

					if(in_array($temp_check[0],$duniques) && in_array($temp_check[1],$duniques) && in_array($temp_check[2],$duniques) && in_array($temp_check[3],$duniques) && in_array($temp_check[4],$duniques)){
						$dmax_straight = $start;
						break;
					}
				}
				
				//echo $dmax_straight.":".$pmax_straight; 
				
				if($pmax_straight > $dmax_straight){
					$winner = "player";	
				}else if($pmax_straight < $dmax_straight){
					$winner = "dealer";	
				}else{
					$winner = "tie";		
				}
				
			break;
			case 9:
				//tie with royal straight flush
				$winner = "tie";
			break;
		}
		
		return $winner;	
	}
	
	function get_hand_in_rsorted_values($cards){
		$parts = $this->split_hand($cards);
		$res = array();
		foreach($parts["values"] as $value){
			$res[] = str_replace("J","11",str_replace("Q","12",str_replace("K","13",str_replace("A","14",$value))));
		}
		arsort($res);
		return array_values($res);
	}
	
	function fold(){
		
		
		$this->session ->vars["finished"] = 1;
		$this->session ->vars["status"] = "folded";
		$this->session ->vars["end_date"] = date("Y-m-d H:i:s");
		$this->session ->update();	
		
		$this->store_settle();
		
		
	}
	
	function store_settle(){
		global $game_id;
		
		$log = new _settle_log();
		$log ->vars["game"] = $game_id;
		$log ->vars["player"] = $this ->player;
		$log ->vars["bet_amount"] = (($this->session ->vars["ante_bet"] ?? 0)+($this->session ->vars["call_bet"] ?? 0));
		$log ->vars["win_amount"] = $this->session ->vars["win_amount"] ?? 0;
		$log ->vars["settle"] = $this->session ->vars["settle"];
		$log ->vars["ldate"] = date("Y-m-d H:i:s");
		$log ->insert();
	}
	
	function get_best_hand_level($cards){
		$posible_hands = $this->getCombinations($cards,5);
		$max_level = 0;
		foreach($posible_hands as $hand){
			$hlevel = $this->calculate_hand_level($hand);
			if($hlevel > $max_level){
				$max_level = $hlevel; 
			}
			
		}
				
		return $max_level;
	}
	
	function calculate_hand_level($hand, $is_dealer = false){
	
		$win_level = 0;
		$parts = $this->split_hand($hand);
		$count_hand_name = $this->get_count_hand_name($parts["values"]);
		
		if($this->is_same_color($parts["colors"]) && $this->is_royal($parts["values"])){
			
			//royal straight flush
			$win_level = 9;
			
		}else if($this->is_same_color($parts["colors"]) && $this->is_straight($parts["values"])){
			
			//straight flush
			$win_level = 8;	
				
		}else if($count_hand_name == "four"){
			
			//four of a kind
			$win_level = 7;	
			
		}else if($count_hand_name == "full"){
			
			//full house
			$win_level = 6;	
			
		}else if($this->is_same_color($parts["colors"])){
			
			//flush
			$win_level = 5;	
			
		}else if($this->is_straight($parts["values"])){
			
			//straight
			$win_level = 4;	
			
		}else if($count_hand_name == "three"){
			
			//three of a kind
			$win_level = 3;	
			
		}else if($count_hand_name == "two_pairs"){
			
			//Two pairs
			$win_level = 2;	
			
		}else if($count_hand_name == "pair"){
			
			//pair
			$win_level = 1;	
			
		}else{
			
			if($is_dealer){
				if($this->qualify($hand)){
					$win_level = 0;		
				}else{
					$win_level = -1;		
				}
			}else{
				$win_level = 0;		
			}
		}
		
		return $win_level;
		
	}
	
	function qualify($hand){
		$qu = false;
		$aces = 0;
		$ks = 0;
		foreach($hand as $card){
			$num = substr($card,1,1);
			if($num == "A"){
				$aces++;
			}else if($num == "K"){
				$ks++;
			} 
		}
		
		if($aces > 1 || ($aces > 0 && $ks > 0)){
			$qu = true;	 
		}
		
		return $qu;	
	}
	
	function is_straight($values){
		$is = false;
		
		$posible_hands = array();
		for($i=0;$i<10;$i++){
			$hand = array();
			for($e=$i;$e<5+$i;$e++){
				$hand[] = $e+1;
			}
			$hand = implode(",",$hand);
			$hand = str_replace("11","J",$hand);
			$hand = str_replace("12","Q",$hand);
			$hand = str_replace("13","K",$hand);
			$hand = str_replace("14","A",$hand);
			$hand = str_replace("1,","A,",$hand);
			$posible_hands[] = explode(",",$hand);
		}	
		
		foreach($posible_hands as $phand){
			if(in_array($phand[0],$values) && in_array($phand[1],$values) && in_array($phand[2],$values) && in_array($phand[3],$values) && in_array($phand[4],$values)){
				$is = true;	
				break;
			}
		}
		
		return $is;
		
	}
	
	function get_count_hand_name($values){
		$hand = $values;

		$counts = array_count_values($hand);
		rsort($counts);
		
		$res_hand = "";
		if($counts[0] == 4){
			$res_hand = "four";
		}else if($counts[0] == 3 && $counts[1] == 2){
			$res_hand = "full";
		}else if($counts[0] == 3){
			$res_hand = "three";
		}else if($counts[0] == 2 && $counts[1] == 2){
			$res_hand = "two_pairs";
		}else if($counts[0] == 2){
			$res_hand = "pair";
		}
		
		return $res_hand;
	}
	
	function is_jacks_or_better($values){
		$hand = $values;
		$is = false;
		$counts = array_count_values($hand);
		
		if($counts["J"] == 2 || $counts["Q"] == 2 || $counts["K"] == 2 || $counts["A"] == 2){
			$is = true;	
		}		
		
		return $is;
	}
	
	function is_royal($values){
		$is = false;
		if(in_array("A",$values) && in_array("K",$values) && in_array("Q",$values) && in_array("J",$values) && in_array("10",$values)){
			$is = true;	
		}	
		return $is;
	}
	
	function is_same_color($colors){
		$is = false;
		if($colors[0] == $colors[1] && $colors[1] == $colors[2] && $colors[2] == $colors[3] && $colors[3] == $colors[4]){
			$is = true;	
		}	
		return $is;
	}
	
	function split_hand($hand){
	
		$parts["colors"] = array();
		$parts["values"] = array();
		
		foreach($hand as $card){
			
			$parts["colors"][] = substr($card,0,1);
			$parts["values"][] = substr($card,1);
				
		}
		
		return $parts;
		
	}
	
	function getCombinations($base,$n){

		$baselen = count($base);
		if($baselen == 0){
			return;
		}
		if($n == 1){
			$return = array();
			foreach($base as $b){
				$return[] = array($b);
			}
			return $return;
		}else{
			$oneLevelLower = $this->getCombinations($base,$n-1);
			$newCombs = array();
	
			foreach($oneLevelLower as $oll){
	
				$lastEl = $oll[$n-2];
				$found = false;
				foreach($base as  $key => $b){
					if($b == $lastEl){
						$found = true;
						continue;
		
					}
					if($found == true){
						if($key < $baselen){
		
							$tmp = $oll;
							$newCombination = array_slice($tmp,0);
							$newCombination[]=$b;
							$newCombs[] = array_slice($newCombination,0);
						}
		
					}
				}
	
			}
	
		}
	
		return $newCombs;
	
	}
   
   
}	
?>