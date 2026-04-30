<?
class fortune_wheel{
	var $player;
	var $win_amount = 0;
	var $big_win = 0;
	
	
	function __construct($id_player) {
	   	   
		 $this->player = $id_player;
	   
	}
	
	
	function spin($bet, $test = false){
		global $game_id;
		
		$total_bet = $bet;		
		$this->win_amount = 0;
		
		$winX = -1;
		$bonusX = -1;
		
		/*Main WHEEL----------------*/
		$rand = mt_rand(0,999);
		if($rand == 751){
			if(mt_rand(1,100)<=25){
				$winX = 100;	
			}
		}
		
		if($winX < 0){
			if($rand <= 600){
				$winX = 0;	
			}else if($rand <= 800){
				$winX = 1;
			}else if($rand <= 950){
				$winX = 2;	
			}else if ($rand <= 985){
				$winX = 5;
			}else if($rand <= 995){
				$winX = 10;
			}else if($rand <= 997){
				$winX = 25;
			}else if($rand <= 999){
				$winX = 50;
			}
		}
		/*----------------------------*/
		
		
		/*BONUS WHEEL----------------*/
		$rand2 = mt_rand(1,100);
		if($rand2 == 23){
			if(mt_rand(1,100)==7){
				$bonusX = 10;
			}
		}
		
		if($bonusX < 0){
			if($rand2 <= 52){ //change to 50 to get 5% hold, currently is in 3%
				$bonusX = 1;	
			}else if($rand2 <= 75){
				$bonusX = 0;		
			}else{
				$bonusX = 2;	
			}
		}
		/*----------------------------*/
		
		$pre_winX = $winX;
		$winX *= $bonusX;
		
		if($pre_winX == 100 && $bonusX == 10){$this->big_win = 1;}
		
		$this->win_amount = ($total_bet*$winX);
		
		if(!$test){
			$session = new _slot_session();
			$session ->vars["player"] = $this ->player;
			$session ->vars["bet_amount"] = $total_bet;
			$session ->vars["win_amount"] = $this->win_amount;
			$session ->vars["reel"] = "$pre_winX|$bonusX";
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
		}
		
		return "$pre_winX|$bonusX";
		
	}
   
   
}		
?>