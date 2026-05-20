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
		if(($session ->vars["pf"] ?? '') != '""' && ($session ->vars["pf"] ?? '') != ''){
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
		$session ->vars["bet_amount"] = $bet_amount;
		$session ->vars["hand"] = $str_hand;
		$session ->vars["coins"] = $coins;
		$session ->vars["vp_type"] = "deuces";
		$session ->vars["start_date"] = date("Y-m-d H:i:s");
		
		//store pf data
		if($this->pf_data != ""){
			$session ->vars["pf"] = json_encode($this->pf_data);
		}
		
		$session ->insert();
		
		return $str_hand;
		
	}
	
	function draw($holds){
		global $game_id;
		
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
		$this->calculate_winnings($hand_list,$this->session ->vars["bet_amount"],$this->session ->vars["coins"]);
		
		$str_hand = implode(",",$hand_list);
		
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
		
		return $str_hand;
		
	}
	
	function calculate_winnings($hand,$bet_amount,$coins){
	
		$parts = $this->split_hand($hand);
		
		if($this->is_same_color($parts["colors"]) && $this->is_royal($parts["values"])){
			
			//echo "royal straight flush";
			$this->win_level = 10;
			if($coins == 5){
				$this->win_amount = $bet_amount * 800;	
			}else{
				$this->win_amount = $bet_amount * 250;	
			}
			
		}else if($this->is_four_douces($parts["values"])){
			
			//echo "four deuces";
			$this->win_level = 9;
			$this->win_amount = $bet_amount * 200;	
				
		}else if($this->is_deuces_royal($parts["values"],$parts["colors"])){
			
			//echo "Deuces royal";
			$this->win_level = 8;
			$this->win_amount = $bet_amount * 25;	
				
		}else if($this->is_n_of_kind($parts["values"],5)){
			
			//echo "Five of a kind";
			$this->win_level = 7;
			$this->win_amount = $bet_amount * 16;	
				
		}else if($this->is_straight($parts["values"],$parts["colors"],true)){
			
			//echo "straight flush";
			$this->win_level = 6;
			$this->win_amount = $bet_amount * 13;	
				
		}else if($this->is_n_of_kind($parts["values"],4)){
			
			//echo "four of a kind";
			$this->win_level = 5;
			$this->win_amount = $bet_amount * 4;	
			
		}else if($this->is_full($parts["values"])){
			
			//echo "full house";
			$this->win_level = 4;
			$this->win_amount = $bet_amount * 3;	
			
		}else if($this->is_flush($parts["values"],$parts["colors"])){
			
			//echo "flush";
			$this->win_level = 2;
			$this->win_amount = $bet_amount * 2;	
			
		}else if($this->is_straight($parts["values"],$parts["colors"])){
			
			//echo "straight";
			$this->win_level = 3;
			$this->win_amount = $bet_amount * 2;	
			
		}else if($this->is_n_of_kind($parts["values"],3)){
			
			//echo "three of a kind";
			$this->win_level = 1;
			$this->win_amount = $bet_amount * 1;	
			
		}
		
		
		
	}
	
	function is_straight($values, $colors,$flush=false){
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
			if($this->check_deuce_straight($values, $colors, $phand,$flush)){
				$is = true;	
				break;
			}
		}
		
		return $is;
		
	}
	
	function check_deuce_straight($values, $colors, $compare_hand,$check_color = false){
		$is = false;
		$counts = array_count_values($values);
		
		$deuces_count = $counts["2"] ?? 0;
		if($compare_hand[0] != "2"){$a_count = $counts[$compare_hand[0]] ?? 0;}
		if($compare_hand[1] != "2"){$b_count = $counts[$compare_hand[1]] ?? 0;}
		if($compare_hand[2] != "2"){$c_count = $counts[$compare_hand[2]] ?? 0;}
		if($compare_hand[3] != "2"){$d_count = $counts[$compare_hand[3]] ?? 0;}
		if($compare_hand[4] != "2"){$e_count = $counts[$compare_hand[4]] ?? 0;}
		
		
		if($a_count < 2 && $b_count < 2 && $c_count < 2 && $d_count < 2 && $e_count < 2){

			$dcolors = array();
			for($i=0;$i<5;$i++){
				if($values[$i] != '2'){
					$dcolors[$colors[$i]] = 1;
				}
			}
			
			$points = $deuces_count+$a_count+$b_count+$c_count+$d_count+$e_count;
		
			if($points == 5 && (count($dcolors) == 1 || !$check_color)){
				$is = true;
			}
		
		}	
		return $is;
	}
	
	function is_flush($values, $colors){
	
		$is = false;
		
		$dcolors = array();
		for($i=0;$i<5;$i++){
			if($values[$i] != '2'){
				$dcolors[$colors[$i]] = 1;
			}
		}
		
		if(count($dcolors) == 1){
			$is = true;	
		}
		
		return $is;
		
	}
	
	
	
	
	function is_four_douces($values){
		$hand = $values;
		$is = false;
		$counts = array_count_values($hand);		
		
		if(($counts["2"] ?? 0) == 4){
			$is = true;	
		}		
		
		return $is;
	}
	
	function is_full($values){
		$is = false;
		$counts = array_count_values($values);	
		$deuces_count = $counts["2"] ?? 0;
		unset($counts["2"]);
		rsort($counts);

		if(($counts[0] == 3 && ($counts[1] ?? 0) == 2) || ($counts[0] == 2 && ($counts[1] ?? 0) == 2 && $deuces_count == 1)){
			$is = true;
		}
		
		return $is;
		
	}
	
	function is_n_of_kind($values,$n){
		$is = false;
		$counts = array_count_values($values);	
		$deuces_count = $counts["2"] ?? 0;
		unset($counts["2"]);
		rsort($counts);

		$points = ($counts[0] ?? 0) + $deuces_count;
		
		if($points == $n){
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
	
	function is_deuces_royal($values,$colors){
		$is = false;
		$counts = array_count_values($values);
		
		$deuces_count = $counts["2"] ?? 0;
		$a_count = $counts["A"] ?? 0;
		$k_count = $counts["K"] ?? 0;
		$q_count = $counts["Q"] ?? 0;
		$j_count = $counts["J"] ?? 0;
		$t_count = $counts["10"] ?? 0;
		
		if($deuces_count >= 1 && $a_count < 2 && $k_count < 2 && $q_count < 2 && $j_count < 2 && $t_count < 2){

			$dcolors = array();
			for($i=0;$i<5;$i++){
				if($values[$i] != '2'){
					$dcolors[$colors[$i]] = 1;
				}
			}
			
			$points = $deuces_count+$a_count+$k_count+$q_count+$j_count+$t_count;
		
			if($points == 5 && count($dcolors) == 1){
				$is = true;
			}
		
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