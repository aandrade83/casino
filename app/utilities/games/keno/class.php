<?
class keno{
	var $player;
	var $win_amount = 0;
	
	
	function __construct($id_player) {
	   	   
		 $this->player = $id_player;
	   
	}
	
	
	function spin($bet, $selected_nums){
		global $game_id;
		
		$total_bet = $bet;		
		$this->win_amount = 0;
		
		$payable_levels_prices = array();
		$payable_levels_prices[1] = array(3.7);
		$payable_levels_prices[2] = array(1,9);
		$payable_levels_prices[3] = array(1,2,16);
		$payable_levels_prices[4] = array(0.5,2,6,12);
		$payable_levels_prices[5] = array(0.5,1,3,15,50);
		$payable_levels_prices[6] = array(0.5,1,2,3,30,75);
		$payable_levels_prices[7] = array(0.5,0.5,1,6,12,36,100);
		$payable_levels_prices[8] = array(0.5,0.5,1,3,6,19,90,720);
		$payable_levels_prices[9] = array(0.5,0.5,1,2,4,8,20,80,1200);
		$payable_levels_prices[10] = array(0,0.5,1,2,3,5,10,30,600,1800);
		
		$raw_player_numbers = explode(",",$selected_nums);
		if(count($raw_player_numbers) > 10){
			$raw_player_numbers = array_slice($raw_player_numbers, 0, 10);
		}
		
		$player_numbers = array();
		foreach($raw_player_numbers as $rpn){
			$rpn = round($rpn);
			if(is_numeric($rpn) && $rpn > 0 && $rpn <= 80){
				$player_numbers[] = $rpn;	
			}	
		}
		
		$level_prices = $payable_levels_prices[count($player_numbers)];
		$matchs = 0;
		$winner_numbers = array();
		
		while(count($winner_numbers) < 20){
			
			$rand_num = mt_rand(1,80);
			if(!in_array($rand_num,$winner_numbers)){
				$winner_numbers[] = $rand_num;
				if(in_array($rand_num,$player_numbers)){
					$matchs++;
				}
			}
				
		}
		
		if($matchs>0){
			$this->win_amount = round(($level_prices[$matchs-1])*$bet,2);
		}
		
	
		$session = new _keno_session();
		$session ->vars["player"] = $this ->player;
		$session ->vars["bet_amount"] = $total_bet;
		$session ->vars["win_amount"] = $this->win_amount;
		$session ->vars["selection"] = implode(",",$player_numbers);
		$session ->vars["result"] = implode(",",$winner_numbers);
		$session ->vars["game"] = $game_id;
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
	
		
		return implode(",",$winner_numbers);
		
	}
   
   
}		
?>