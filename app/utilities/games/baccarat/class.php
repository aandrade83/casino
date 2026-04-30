<?
class baccarat{
	var $player;
	var $win_amount = 0;
	var $session = NULL;
	var $bet_areas = array();
	var $number_of_decks = 8;
	var $cards = array();
	var $player_hand = array();
	var $banker_hand = array();
	//provably fair
	var $pf_seed = "na";
	var $using_pf = false;
	
	
	
	function __construct($id_player) {
		global $_deck;
	   	$bet_areas = array("banker","player","tie");
		$this->player = $id_player;
		
		//initialize cards
		$this ->cards = prepare_decks($this->number_of_decks);
	   
	}
	
	function start_pf($pf_seed){
		 if(is_numeric($pf_seed)){
			 //use provably fair
			 $this ->using_pf = true;
			 $this ->cards = seed_shuffle($this ->cards,$pf_seed);
		 }
	}
	
	function get_card_value($card){
		$value = substr($card,1);
		
		if(!is_numeric($value)){
			switch($value){ 
				case "J":
				case "Q":
				case "K":
					$value = 0;
				break;
				case "A":
					$value = 1;
				break;
			}	
		}else if($value == 10){
			$value = 0;
		}
		return $value*1;
	}
	
	function get_hand_value($type = "player"){
		
		switch($type){ 
			case "player":
				$cards = $this ->player_hand;
			break;
			case "banker":
				$cards = $this ->banker_hand;
			break;
		}
		
		$total = 0;			
		foreach($cards as $card){
			$total += $this->get_card_value($card);
		}
		
		if($total*1 > 9){
			$total = substr($total."",1)*1;
		}
		
		return $total;
		
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
	
	function deal($bets){
		global $game_id, $_using_free_play;
		
		$result = array();
		$bet_list = explode(",",$bets);
		$total_bet = 0;
		$winning_area = "";
		
		
		//Player card 1	
		$this ->player_hand[] = $this->deal_card();
		
		//Banker card 1	
		$this ->banker_hand[] = $this->deal_card();
		
		//Player card 2
		$this ->player_hand[] = $this->deal_card();
		
		//Banker card 2
		$this ->banker_hand[] = $this->deal_card();
		
		$player_value = $this ->get_hand_value();
		$banker_value = $this ->get_hand_value("banker");
		
		$result["player_value1"] = $player_value;
		$result["banker_value1"] = $banker_value;
		
		$naturals = array(8,9);
		if(!in_array($player_value,$naturals) && !in_array($banker_value,$naturals)){
			
			$third_player = "na";
			if($player_value < 6){
				//Player card 3
				$third_player_card = $this->deal_card();
				$this ->player_hand[] = $third_player_card;
				$third_player = $this->get_card_value($third_player_card);
			}
			
			if($banker_value < 7 && is_numeric($third_player)){ //based on player third card
				
				if($banker_value == 6 && $third_player >= 6 && $third_player <= 7 ){
					$this ->banker_hand[] = $this->deal_card();
				}else if($banker_value == 5 && $third_player >= 4 && $third_player <= 7 ){
					$this ->banker_hand[] = $this->deal_card();
				}else if($banker_value == 4 && $third_player >= 2 && $third_player <= 7){
					$this ->banker_hand[] = $this->deal_card();
				}else if($banker_value == 3 && $third_player != 8 ){
					$this ->banker_hand[] = $this->deal_card();
				}else if($banker_value < 3){
					$this ->banker_hand[] = $this->deal_card();
				}
				
			}else if(!is_numeric($third_player)){ //player stands
				if($banker_value < 6){
					$this ->banker_hand[] = $this->deal_card();
				}
			}
			
		}
		
		$player_value = $this ->get_hand_value();
		$banker_value = $this ->get_hand_value("banker");
		
		if($player_value > $banker_value){
			$winning_area = "player";
		}else if($player_value < $banker_value){
			$winning_area = "banker";
		}else{
			$winning_area = "tie";
		}
		
		$result["player_value2"] = $player_value;
		$result["banker_value2"] = $banker_value;
		$result["winning_area"] = $winning_area;
		$result["player_hand"] = $this ->player_hand;
		$result["banker_hand"] = $this ->banker_hand;
		
		$bets_amounts = array();
		foreach($bet_list as $bet){
			$parts = explode("|",$bet);
			$bets_amounts[$parts[0]] = $parts[1];
			$total_bet += $parts[1];
		}
		
		if(is_numeric($bets_amounts[$winning_area]) && $bets_amounts[$winning_area] > 0){
			$times = 0;
			switch($winning_area){ 
				case "tie": $times = 8; break;
				case "player": $times = 1; break;
				case "banker": $times = 0.95; break;
			}
			
			if($_using_free_play){
				$this->win_amount = ($bets_amounts[$winning_area] * $times);
			}else{
				$this->win_amount = ($bets_amounts[$winning_area] * $times) + $bets_amounts[$winning_area];	
			}
		}
		
		
		//TIE, rerurn player and banker bets
		if($winning_area == "tie"){
			if(is_numeric($bets_amounts["player"]) && $bets_amounts["player"] > 0){
				$this->win_amount += ($bets_amounts["player"]);
			}
			if(is_numeric($bets_amounts["banker"]) && $bets_amounts["banker"] > 0){
				$this->win_amount += ($bets_amounts["banker"]); 
			}
		}
		
		
		$session = new _baccarat_session();
		$session ->vars["player"] = $this ->player;
		$session ->vars["bet_amount"] = $total_bet;
		$session ->vars["win_amount"] = $this->win_amount;
		$session ->vars["bet_detail"] = secure_input($bets);
		$session ->vars["player_hand"] = implode(",",$this ->player_hand);
		$session ->vars["banker_hand"] = implode(",",$this ->banker_hand);
		$session ->vars["player_value"] = $player_value;
		$session ->vars["banker_value"] = $banker_value;
		$session ->vars["winner_area"] = $winning_area;
		$session ->vars["gdate"] = date("Y-m-d H:i:s");
		$session ->vars["settle"] = ($this->win_amount - $total_bet);
		$session ->insert();
		
		$log = new _settle_log();
		$log ->vars["game"] = $game_id;
		$log ->vars["player"] = $this ->player;
		$log ->vars["bet_amount"] = $total_bet;
		$log ->vars["win_amount"] = $this->win_amount;
		$log ->vars["settle"] = ($this->win_amount - $total_bet);
		$log ->vars["ldate"] = date("Y-m-d H:i:s");
		$log ->insert();
		
		
		return $result;
		
	}
   
   
}	
?>