<?
class roulette{
	var $player;
	var $win_amount = 0;
	var $session = NULL;
	var $bet_areas = array();
	
	
	
	function __construct($id_player) {
		global $_deck;
	   	   
		$this->player = $id_player;
		 
		$this->bet_areas["red"] = array("numbers"=>array(1,3,5,7,9,12,14,16,18,19,21,23,25,27,30,32,34,36), "odds"=>1);
		$this->bet_areas["black"] = array("numbers"=>array(2,4,6,8,10,11,13,15,17,20,22,24,26,28,29,31,33,35), "odds"=>1);
		$this->bet_areas["1to18"] = array("numbers"=>array(1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18), "odds"=>1);
		$this->bet_areas["even"] = array("numbers"=>array(2,4,6,8,10,12,14,16,18,20,22,24,26,28,30,32,34,36), "odds"=>1);
		$this->bet_areas["odd"] = array("numbers"=>array(1, 3, 5, 7, 9, 11, 13, 15, 17, 19, 21, 23, 25, 27, 29, 31,33,35), "odds"=>1);
		$this->bet_areas["19to36"] = array("numbers"=>array(19,20,21,22,23,24,25,26,27,28,29,30,31,32,33,34,35,36), "odds"=>1);
		$this->bet_areas["1st12"] = array("numbers"=>array(1,2,3,4,5,6,7,8,9,10,11,12), "odds"=>2);
		$this->bet_areas["2nd12"] = array("numbers"=>array(13,14,15,16,17,18,19,20,21,22,23,24), "odds"=>2);
		$this->bet_areas["3rd12"] = array("numbers"=>array(25,26,27,28,29,30,31,32,33,34,35,36), "odds"=>2);
		$this->bet_areas["1strow"] = array("numbers"=>array(1,4,7,10,13,16,19,22,25,28,31,34), "odds"=>2);
		$this->bet_areas["2ndrow"] = array("numbers"=>array(2,5,8,11,14,17,20,23,26,29,32,35), "odds"=>2);
		$this->bet_areas["3rdrow"] = array("numbers"=>array(3,6,9,12,15,18,21,24,27,30,33,36), "odds"=>2);
		$this->bet_areas["0"] = array("numbers"=>array(0), "odds"=>35);
		$this->bet_areas["0-2"] = array("numbers"=>array(0,2), "odds"=>17);
		$this->bet_areas["1"] = array("numbers"=>array(1), "odds"=>35);
		$this->bet_areas["4"] = array("numbers"=>array(4), "odds"=>35);
		$this->bet_areas["7"] = array("numbers"=>array(7), "odds"=>35);
		$this->bet_areas["10"] = array("numbers"=>array(10), "odds"=>35);
		$this->bet_areas["13"] = array("numbers"=>array(13), "odds"=>35);
		$this->bet_areas["16"] = array("numbers"=>array(16), "odds"=>35);
		$this->bet_areas["19"] = array("numbers"=>array(19), "odds"=>35);
		$this->bet_areas["22"] = array("numbers"=>array(22), "odds"=>35);
		$this->bet_areas["25"] = array("numbers"=>array(25), "odds"=>35);
		$this->bet_areas["28"] = array("numbers"=>array(28), "odds"=>35);
		$this->bet_areas["31"] = array("numbers"=>array(31), "odds"=>35);
		$this->bet_areas["34"] = array("numbers"=>array(34), "odds"=>35);
		$this->bet_areas["2"] = array("numbers"=>array(2), "odds"=>35);
		$this->bet_areas["5"] = array("numbers"=>array(5), "odds"=>35);
		$this->bet_areas["8"] = array("numbers"=>array(8), "odds"=>35);
		$this->bet_areas["11"] = array("numbers"=>array(11), "odds"=>35);
		$this->bet_areas["14"] = array("numbers"=>array(14), "odds"=>35);
		$this->bet_areas["17"] = array("numbers"=>array(17), "odds"=>35);
		$this->bet_areas["20"] = array("numbers"=>array(20), "odds"=>35);
		$this->bet_areas["23"] = array("numbers"=>array(23), "odds"=>35);
		$this->bet_areas["26"] = array("numbers"=>array(26), "odds"=>35);
		$this->bet_areas["29"] = array("numbers"=>array(29), "odds"=>35);
		$this->bet_areas["32"] = array("numbers"=>array(32), "odds"=>35);
		$this->bet_areas["35"] = array("numbers"=>array(35), "odds"=>35);
		$this->bet_areas["3"] = array("numbers"=>array(3), "odds"=>35);
		$this->bet_areas["6"] = array("numbers"=>array(6), "odds"=>35);
		$this->bet_areas["9"] = array("numbers"=>array(9), "odds"=>35);
		$this->bet_areas["12"] = array("numbers"=>array(12), "odds"=>35);
		$this->bet_areas["15"] = array("numbers"=>array(15), "odds"=>35);
		$this->bet_areas["18"] = array("numbers"=>array(18), "odds"=>35);
		$this->bet_areas["21"] = array("numbers"=>array(21), "odds"=>35);
		$this->bet_areas["24"] = array("numbers"=>array(24), "odds"=>35);
		$this->bet_areas["27"] = array("numbers"=>array(27), "odds"=>35);
		$this->bet_areas["30"] = array("numbers"=>array(30), "odds"=>35);
		$this->bet_areas["33"] = array("numbers"=>array(33), "odds"=>35);
		$this->bet_areas["36"] = array("numbers"=>array(36), "odds"=>35);
		$this->bet_areas["0-1-2-3"] = array("numbers"=>array(0,1,2,3), "odds"=>8);
		$this->bet_areas["1-4-2-5-3-6"] = array("numbers"=>array(1,4,2,5,3,6), "odds"=>5);
		$this->bet_areas["4-7-5-8-6-9"] = array("numbers"=>array(4,7,5,8,6,9), "odds"=>5);
		$this->bet_areas["7-10-8-11-9-12"] = array("numbers"=>array(7,10,8,11,9,12), "odds"=>5);
		$this->bet_areas["10-13-11-14-12-15"] = array("numbers"=>array(10,13,11,14,12,15), "odds"=>5);
		$this->bet_areas["13-16-14-17-15-18"] = array("numbers"=>array(13,16,14,17,15,18), "odds"=>5);
		$this->bet_areas["16-19-17-20-18-21"] = array("numbers"=>array(16,19,17,20,18,21), "odds"=>5);
		$this->bet_areas["19-22-20-23-21-24"] = array("numbers"=>array(19,22,20,23,21,24), "odds"=>5);
		$this->bet_areas["22-25-23-26-24-27"] = array("numbers"=>array(22,25,23,26,24,27), "odds"=>5);
		$this->bet_areas["25-28-26-29-27-30"] = array("numbers"=>array(25,28,26,29,27,30), "odds"=>5);
		$this->bet_areas["28-31-29-32-30-33"] = array("numbers"=>array(28,31,29,32,30,33), "odds"=>5);
		$this->bet_areas["31-34-32-35-33-36"] = array("numbers"=>array(31,34,32,35,33,36), "odds"=>5);
		$this->bet_areas["1-2-3"] = array("numbers"=>array(1,2,3), "odds"=>11);
		$this->bet_areas["4-5-6"] = array("numbers"=>array(4,5,6), "odds"=>11);
		$this->bet_areas["7-8-9"] = array("numbers"=>array(7,8,9), "odds"=>11);
		$this->bet_areas["10-11-12"] = array("numbers"=>array(10,11,12), "odds"=>11);
		$this->bet_areas["13-14-15"] = array("numbers"=>array(13,14,15), "odds"=>11);
		$this->bet_areas["16-17-18"] = array("numbers"=>array(16,17,18), "odds"=>11);
		$this->bet_areas["19-20-21"] = array("numbers"=>array(19,20,21), "odds"=>11);
		$this->bet_areas["22-23-24"] = array("numbers"=>array(22,23,24), "odds"=>11);
		$this->bet_areas["25-26-27"] = array("numbers"=>array(25,26,27), "odds"=>11);
		$this->bet_areas["28-29-30"] = array("numbers"=>array(28,29,30), "odds"=>11);
		$this->bet_areas["31-32-33"] = array("numbers"=>array(31,32,33), "odds"=>11);
		$this->bet_areas["34-35-36"] = array("numbers"=>array(34,35,36), "odds"=>11);
		
		$this->bet_areas["1-2"] = array("numbers"=>array(1,2), "odds"=>17);
		$this->bet_areas["4-5"] = array("numbers"=>array(4,5), "odds"=>17);
		$this->bet_areas["7-8"] = array("numbers"=>array(7,8), "odds"=>17);
		$this->bet_areas["10-11"] = array("numbers"=>array(10,11), "odds"=>17);
		$this->bet_areas["13-14"] = array("numbers"=>array(13,14), "odds"=>17);
		$this->bet_areas["16-17"] = array("numbers"=>array(16,17), "odds"=>17);
		$this->bet_areas["19-20"] = array("numbers"=>array(19,20), "odds"=>17);
		$this->bet_areas["22-23"] = array("numbers"=>array(22,23), "odds"=>17);
		$this->bet_areas["25-26"] = array("numbers"=>array(25,26), "odds"=>17);
		$this->bet_areas["28-29"] = array("numbers"=>array(28,29), "odds"=>17);
		$this->bet_areas["31-32"] = array("numbers"=>array(31,32), "odds"=>17);
		$this->bet_areas["34-35"] = array("numbers"=>array(34,35), "odds"=>17);
		$this->bet_areas["2-3"] = array("numbers"=>array(2,3), "odds"=>17);
		$this->bet_areas["5-6"] = array("numbers"=>array(5,6), "odds"=>17);
		$this->bet_areas["8-9"] = array("numbers"=>array(8,9), "odds"=>17);
		$this->bet_areas["11-12"] = array("numbers"=>array(11,12), "odds"=>17);
		$this->bet_areas["14-15"] = array("numbers"=>array(14,15), "odds"=>17);
		$this->bet_areas["17-18"] = array("numbers"=>array(17,18), "odds"=>17);
		$this->bet_areas["20-21"] = array("numbers"=>array(20,21), "odds"=>17);
		$this->bet_areas["23-24"] = array("numbers"=>array(23,24), "odds"=>17);
		$this->bet_areas["26-27"] = array("numbers"=>array(26,27), "odds"=>17);
		$this->bet_areas["29-30"] = array("numbers"=>array(29,30), "odds"=>17);
		$this->bet_areas["32-33"] = array("numbers"=>array(32,33), "odds"=>17);
		$this->bet_areas["35-36"] = array("numbers"=>array(35,36), "odds"=>17);
		$this->bet_areas["0-1"] = array("numbers"=>array(0,1), "odds"=>17);
		$this->bet_areas["1-4"] = array("numbers"=>array(1,4), "odds"=>17);
		$this->bet_areas["4-7"] = array("numbers"=>array(4,7), "odds"=>17);
		$this->bet_areas["7-10"] = array("numbers"=>array(7,10), "odds"=>17);
		$this->bet_areas["10-13"] = array("numbers"=>array(10,13), "odds"=>17);
		$this->bet_areas["13-16"] = array("numbers"=>array(13,16), "odds"=>17);
		$this->bet_areas["16-19"] = array("numbers"=>array(16,19), "odds"=>17);
		$this->bet_areas["19-22"] = array("numbers"=>array(19,22), "odds"=>17);
		$this->bet_areas["22-25"] = array("numbers"=>array(22,25), "odds"=>17);
		$this->bet_areas["25-28"] = array("numbers"=>array(25,28), "odds"=>17);
		$this->bet_areas["28-31"] = array("numbers"=>array(28,31), "odds"=>17);
		$this->bet_areas["31-34"] = array("numbers"=>array(31,34), "odds"=>17);
		$this->bet_areas["2-5"] = array("numbers"=>array(2,5), "odds"=>17);
		$this->bet_areas["5-8"] = array("numbers"=>array(5,8), "odds"=>17);
		$this->bet_areas["8-11"] = array("numbers"=>array(8,11), "odds"=>17);
		$this->bet_areas["11-14"] = array("numbers"=>array(11,14), "odds"=>17);
		$this->bet_areas["14-17"] = array("numbers"=>array(14,17), "odds"=>17);
		$this->bet_areas["17-20"] = array("numbers"=>array(17,20), "odds"=>17);
		$this->bet_areas["20-23"] = array("numbers"=>array(20,23), "odds"=>17);
		$this->bet_areas["23-26"] = array("numbers"=>array(23,26), "odds"=>17);
		$this->bet_areas["26-29"] = array("numbers"=>array(26,29), "odds"=>17);
		$this->bet_areas["29-32"] = array("numbers"=>array(29,32), "odds"=>17);
		$this->bet_areas["32-35"] = array("numbers"=>array(32,35), "odds"=>17);
		$this->bet_areas["0-3"] = array("numbers"=>array(0,3), "odds"=>17);
		$this->bet_areas["3-6"] = array("numbers"=>array(3,6), "odds"=>17);
		$this->bet_areas["6-9"] = array("numbers"=>array(6,9), "odds"=>17);
		$this->bet_areas["9-12"] = array("numbers"=>array(9,12), "odds"=>17);
		$this->bet_areas["12-15"] = array("numbers"=>array(12,15), "odds"=>17);
		$this->bet_areas["15-18"] = array("numbers"=>array(15,18), "odds"=>17);
		$this->bet_areas["18-21"] = array("numbers"=>array(18,21), "odds"=>17);
		$this->bet_areas["21-24"] = array("numbers"=>array(21,24), "odds"=>17);
		$this->bet_areas["24-27"] = array("numbers"=>array(24,27), "odds"=>17);
		$this->bet_areas["27-30"] = array("numbers"=>array(27,30), "odds"=>17);
		$this->bet_areas["30-33"] = array("numbers"=>array(30,33), "odds"=>17);
		$this->bet_areas["33-36"] = array("numbers"=>array(33,36), "odds"=>17);
		
		$this->bet_areas["0-1-2"] = array("numbers"=>array(0,1,2), "odds"=>11);
		$this->bet_areas["1-4-2-5"] = array("numbers"=>array(1,4,2,5), "odds"=>8);
		$this->bet_areas["4-7-5-8"] = array("numbers"=>array(4,7,5,8), "odds"=>8);
		$this->bet_areas["7-10-8-11"] = array("numbers"=>array(7,10,8,11), "odds"=>8);
		$this->bet_areas["10-13-11-14"] = array("numbers"=>array(10,13,11,14), "odds"=>8);
		$this->bet_areas["13-16-14-17"] = array("numbers"=>array(13,16,14,17), "odds"=>8);
		$this->bet_areas["16-19-17-20"] = array("numbers"=>array(16,19,17,20), "odds"=>8);
		$this->bet_areas["19-22-20-23"] = array("numbers"=>array(19,22,20,23), "odds"=>8);
		$this->bet_areas["22-25-23-26"] = array("numbers"=>array(22,25,23,26), "odds"=>8);
		$this->bet_areas["25-28-26-29"] = array("numbers"=>array(25,28,26,29), "odds"=>8);
		$this->bet_areas["28-31-29-32"] = array("numbers"=>array(28,31,29,32), "odds"=>8);
		$this->bet_areas["31-34-32-35"] = array("numbers"=>array(31,34,32,35), "odds"=>8);
		
		$this->bet_areas["2-0-3"] = array("numbers"=>array(2,0,3), "odds"=>11);
		$this->bet_areas["2-5-3-6"] = array("numbers"=>array(2,5,3,6), "odds"=>8);
		$this->bet_areas["5-8-6-9"] = array("numbers"=>array(5,8,6,9), "odds"=>8);
		$this->bet_areas["8-11-9-12"] = array("numbers"=>array(8,11,9,12), "odds"=>8);
		$this->bet_areas["11-14-12-15"] = array("numbers"=>array(11,14,12,15), "odds"=>8);
		$this->bet_areas["14-17-15-18"] = array("numbers"=>array(14,17,15,18), "odds"=>8);
		$this->bet_areas["17-20-18-21"] = array("numbers"=>array(17,20,18,21), "odds"=>8);
		$this->bet_areas["20-23-21-24"] = array("numbers"=>array(20,23,21,24), "odds"=>8);
		$this->bet_areas["23-26-24-27"] = array("numbers"=>array(23,26,24,27), "odds"=>8);
		$this->bet_areas["26-29-27-30"] = array("numbers"=>array(26,29,27,30), "odds"=>8);
		$this->bet_areas["29-32-30-33"] = array("numbers"=>array(29,32,30,33), "odds"=>8);
		$this->bet_areas["32-35-33-36"] = array("numbers"=>array(32,35,33,36), "odds"=>8); 
	   
	}
	
	function spin($bets, $pf_result = "na"){
		global $game_id, $_using_free_play;
		
		
		if(is_numeric($pf_result)){
			//Rpovably fair
			$winning_number = intval($pf_result) % 37;
		}else{
			//Random
			$winning_number = rc_random(0,36);
		}
		
		
		$bet_list = explode(",",$bets);
		$total_bet = 0;
		$winning_areas = array();
		
		foreach($bet_list as $bet){
			$parts = explode("|",$bet);
			if(is_array($this->bet_areas[$parts[0]]) && is_numeric($parts[1]) && $parts[1] > 0){
				$total_bet += $parts[1];
				
				if(in_array($winning_number,$this->bet_areas[$parts[0]]["numbers"],true)){
					
					if($_using_free_play){
						$this->win_amount += ($parts[1]*$this->bet_areas[$parts[0]]["odds"]);
					}else{
						$this->win_amount += ($parts[1]*$this->bet_areas[$parts[0]]["odds"]) + $parts[1];	
					}
					
					$winning_areas[] = $parts[0];
				}
			}
		}
		
		$session = new _roulette_session();
		$session ->vars["player"] = $this ->player;
		$session ->vars["bet_amount"] = $total_bet;
		$session ->vars["win_amount"] = $this->win_amount;
		$session ->vars["bet_detail"] = secure_input($bets);
		$session ->vars["number"] = $winning_number;
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
		
		if(in_array($winning_number,$this->bet_areas["red"]["numbers"])){
			$color = "F00";	
			$str_color = "red";
		}else if(in_array($winning_number,$this->bet_areas["black"]["numbers"])){
			$color = "000";	
			$str_color = "black";	
		}else{
			$color = "0F0";	
			$str_color = "";	
		}
		
		$winner = array("value"=>$winning_number,"color"=>$color,"str_color"=>$str_color,"areas"=>implode(",",$winning_areas));
		
		return $winner;
		
	}
   
   
}	
?>