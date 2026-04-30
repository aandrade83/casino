<?
class video_poker{
	var $player;
	var $win_amount = 0;
	var $win_level = 0;
	var $cards = array();
	var $session = NULL;
	//provably fair
	var $pf_seed = "na";
	var $using_pf = false;
	var $pf_data = "";
	
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
		if($session ->vars["pf"] != '""' && $session ->vars["pf"] != ''){
			$this->pf_data = json_decode($session ->vars["pf"],true);
			if(!is_null($this->pf_data) && is_numeric($this->pf_data["pos"])){
				$this->start_pf($this->pf_data["pos"]);
			}
		}
		
		$removing_cards = explode(",",$session ->vars["hand"]);
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
	
	function deal($bet_amount, $coins){
		
		$dealing_deck = $this ->cards;
		$hand = array();
		
		$hand[] = $this->deal_card();
		$hand[] = $this->deal_card();
		$hand[] = $this->deal_card();
		$hand[] = $this->deal_card();
		$hand[] = $this->deal_card();
		
		$str_hand = implode(",",$hand);
		
		$session = new _vp_session();
		$session ->vars["player"] = $this ->player;
		$session ->vars["bet_amount"] = $bet_amount*$coins;
		$session ->vars["hand"] = $str_hand;
		$session ->vars["coins"] = $coins;
		$session ->vars["vp_type"] = "afs";
		$session ->vars["start_date"] = date("Y-m-d H:i:s");
		
		//store pf data
		if($this->pf_data != ""){
			$session ->vars["pf"] = json_encode($this->pf_data);
		}
		
		$session ->insert();
		
		return $str_hand;
		
	}
	
	function play_extra_hands($holds_list){
		$extra_hands = array();
		for($i=0;$i<($this->session ->vars["coins"]-1);$i++){
			
			$this ->cards = prepare_decks($this->number_of_decks); //no pf, because is not viable
			shuffle($this ->cards);
			
			//prepare deck
			$removing_cards = explode(",",$this->session ->vars["hand"]);
			$x=0;
			foreach($removing_cards as $rcard){				
				if($holds_list[$x]){
					$position = array_search($rcard,$this ->cards);
					if(is_numeric($position)){array_splice($this ->cards, $position, 1);}
				}
				$x++;				
			}
			
			//keep holds, deal new cards
			$hand_list = explode("," , $this->session ->vars["hand"]);		
			for($e=0;$e<5;$e++){
				if(!$holds_list[$e]){
							
					$new_card = $this->deal_card();				
					$hand_list[$e] = $new_card;
					
				}	
			}
			$str_hand = implode(",",$hand_list);
			
			//calculate winnings
			$pre_win_amount = $this->win_amount;
			$this->calculate_winnings($hand_list,$this->session ->vars["bet_amount"]/$this->session ->vars["coins"]);
			$new_win_amount = round($this->win_amount - $pre_win_amount,2);
			
			//store hand
			$et = new _vp_extra_hand();
			$et ->vars["session"] = $this->session ->vars["id"];
			$et ->vars["hand"] = $str_hand;
			$et ->vars["win_amount"] = $new_win_amount;
			$et->insert();
			
			$extra_hands[] = $str_hand;
			
				
		}
		
		return $extra_hands;
		
	}
	
	function draw($holds){
		global $game_id;
		$all_hands = array();
		
		$dealing_deck = $this ->cards;
		$holds_list = explode(",",$holds);
		$hand_list = explode("," , $this->session ->vars["hand"]);
		
		for($i=0;$i<5;$i++){
			if(!$holds_list[$i]){
						
				$new_card = $this->deal_card();				
				$hand_list[$i] = $new_card;
				
			}	
		}
		
		//calculate win amount here				
		$this->calculate_winnings($hand_list,$this->session ->vars["bet_amount"]/$this->session ->vars["coins"]);
		
		$str_hand = implode(",",$hand_list);
		$all_hands[] = $str_hand;
		
		if($this->session ->vars["coins"] > 1){
			
			//store hand
			$et = new _vp_extra_hand();
			$et ->vars["session"] = $this->session ->vars["id"];
			$et ->vars["hand"] = $str_hand;
			$et ->vars["win_amount"] = $this->win_amount;
			$et->insert();
			
			$all_hands = array_merge($all_hands,$this->play_extra_hands($holds_list));
		}
		
		
		
		$this->session ->vars["win_amount"] = $this->win_amount;
		$this->session ->vars["hand"] = $str_hand;
		$this->session ->vars["end_date"] = date("Y-m-d H:i:s");
		$this->session ->vars["finished"] = 1;
		$this->session ->vars["settle"] = $this->session ->vars["win_amount"] - $this->session ->vars["bet_amount"];
		$this->session ->update();
		
		$log = new _settle_log();
		$log ->vars["game"] = $game_id;
		$log ->vars["player"] = $this ->player;
		$log ->vars["bet_amount"] = $this->session ->vars["bet_amount"];
		$log ->vars["win_amount"] = $this->session ->vars["win_amount"];
		$log ->vars["settle"] = $this->session ->vars["settle"];
		$log ->vars["ldate"] = date("Y-m-d H:i:s");
		$log ->insert();
		
		return $all_hands;
		
	}
	
	function calculate_winnings($hand,$bet_amount){
	
		$parts = $this->split_hand($hand);
		$count_hand_name = $this->get_count_hand_name($parts["values"]);
		
		if($this->is_same_color($parts["colors"]) && $this->is_royal($parts["values"])){
			
			//royal straight flush
			$this->win_level = 9;
			$this->win_amount += $bet_amount * 800;	
			
		}else if($this->is_same_color($parts["colors"]) && $this->is_straight($parts["values"])){
			
			//straight flush
			$this->win_level = 8;
			$this->win_amount += $bet_amount * 50;	
				
		}else if($count_hand_name == "four"){
			
			//four of a kind
			$this->win_level = 7;
			$expo = 25;
			if(count(array_keys($parts["values"], "A")) == 4){$expo = 80;}
			else if(count(array_keys($parts["values"], "J")) == 4 ||
			count(array_keys($parts["values"], "Q")) == 4 ||
			count(array_keys($parts["values"], "K")) == 4){$expo = 40;}
			
			$this->win_amount += $bet_amount * $expo;	
			
		}else if($count_hand_name == "full"){
			
			//full house
			$this->win_level = 6;
			$this->win_amount += $bet_amount * 8;	
			
		}else if($this->is_same_color($parts["colors"])){
			
			//flush
			$this->win_level = 5;
			$this->win_amount += $bet_amount * 5;	
			
		}else if($this->is_straight($parts["values"])){
			
			//straight
			$this->win_level = 4;
			$this->win_amount += $bet_amount * 4;	
			
		}else if($count_hand_name == "three"){
			
			//three of a kind
			$this->win_level = 3;
			$this->win_amount += $bet_amount * 3;	
			
		}else if($count_hand_name == "two_pairs"){
			
			//Two pairs
			$this->win_level = 2;
			$this->win_amount += $bet_amount * 2;	
			
		}else if($this->is_jacks_or_better($parts["values"])){
			
			//Jacks or better
			$this->win_level = 1;
			$this->win_amount += $bet_amount;	
			
		}else{
			$this->win_level = 0;	
		}
		
		
		
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
   
   
}	
?>