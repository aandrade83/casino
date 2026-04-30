<?
class craps{
	var $player;
	var $win_amount = 0;
	var $session = NULL;
	var $bet_areas = array();
	var $groups = array();
	
	function __construct($id_player) {
		global $_deck;
	   	   
		$this->player = $id_player;
		$this ->groups["seven_eleven"] = array(7,11);
		$this ->groups["craps"] = array(2,3,12);
		$this ->groups["field"] = array(2,3,4,9,10,11,12);
		$this ->groups["pass_line_bets"] = array("pass1", "pass2", "pass3", "pass4");
		$this ->groups["dont_pass_line_bets"] = array("dont_pass1", "dont_pass2", "dont_pass3", "dont_pass4");
		$this ->groups["pass_line_odds_bets"] = array("pass_odds1", "pass_odds2");
		$this ->groups["dont_pass_line_odds_bets"] = array("dont_pass_odds1", "dont_pass_odds2");
		$this ->groups["come_bets"] = array("come1", "come2");
		$this ->groups["dont_come_bets"] = array("dont_come1", "dont_come2");
		$this ->groups["dont_come_numbers_bets"] = array("dont_come_four","dont_come_five","dont_come_six","dont_come_eight","dont_come_nine","dont_come_ten");
		$this ->groups["come_numbers_bets"] = array("come_four","come_five","come_six","come_eight","come_nine","come_ten");
		$this ->groups["fields_bets"] = array("field1", "field2");
		$this ->groups["win_points_bets"] = array("win_four", "win_five", "win_six", "win_eight", "win_nine", "win_ten");
		$this ->groups["buy_points_bets"] = array("buy_four", "buy_five", "buy_six", "buy_eight", "buy_nine", "buy_ten");
		$this ->groups["lose_points_bets"] = array("lose_four", "lose_five", "lose_six", "lose_eight", "lose_nine", "lose_ten");
		
		
	   
	}
	
	function load_data($session){
		$result = array();
		if(!is_null($session)){
			$this->session = $session;		
			$this ->player = $session ->vars["player"];
		}else{
			$this->session = new _craps_session();
			$this->session ->vars["player"] = $this ->player;
			$this->session ->vars["point"] = 0;
			$this->session ->vars["game_status"] = "come_out";
			$this->session ->vars["start_date"] = date("Y-m-d H:i:s");	
			
			$last_session_bets = get_pending_craps_bets_by_player($this ->player);
			
			$this->session ->vars["current_bets"] = $last_session_bets["pending_bets"];
			$this->session ->insert();		
		}
		
		$bet_list = explode(",",$this->session ->vars["current_bets"]);
		$total_bet = 0;
		foreach($bet_list as $bet){$total_bet += $parts[1];}		
		
		$result["bets"] = $this->session ->vars["current_bets"];
		$result["point"] = $this->session ->vars["point"];
		$result["game_status"] = $this->session ->vars["game_status"];
		$result["total_bet"] = $total_bet;
		
		return $result;
			
	}
	
	function prepare_bets($bets){
		//Remove pass and dont pass bets from UI and use session ones when already in point status.
		if($this->session ->vars["game_status"] == "point"){
			
			$new_bets = array();
			
			$bet_list = explode(",",$bets);
			foreach($bet_list as $bet){
				$parts = explode("|",$bet);
				$bet_area = $parts[0];				
				if(!in_array($bet_area,$this ->groups["pass_line_bets"]) && !in_array($bet_area,$this ->groups["dont_pass_line_bets"]) && !in_array($bet_area,$this ->groups["dont_come_numbers_bets"]) && !in_array($bet_area,$this ->groups["come_numbers_bets"])){
					$new_bets[] = $bet;
				}				
			}
			
			$bet_list = explode(",",$this->session ->vars["current_bets"]);
			foreach($bet_list as $bet){
				$parts = explode("|",$bet);
				$bet_area = $parts[0];				
				if(in_array($bet_area,$this ->groups["pass_line_bets"]) || in_array($bet_area,$this ->groups["dont_pass_line_bets"]) || in_array($bet_area,$this ->groups["dont_come_numbers_bets"]) || in_array($bet_area,$this ->groups["come_numbers_bets"])){
					$new_bets[] = $bet;
				}				
			}
			
			$new_bets = implode(",",$new_bets);
			
		}else{
			$new_bets = $bets;
		}
		
		return $new_bets;
	}
	
	function can_roll($bets){
		$can = true;
		$haves = array();
		$totals = array();
		
		$bet_list = explode(",",$bets);
		foreach($bet_list as $bet){
			$parts = explode("|",$bet);
			$bet_area = $parts[0];
			$bet_amount = $parts[1];
			
			if(in_array($bet_area,$this ->groups["pass_line_bets"]) || in_array($bet_area,$this ->groups["dont_pass_line_bets"])){
				$haves["pass_dont_pass"] = true;
			}
			
			if(in_array($bet_area,$this ->groups["come_bets"]) || in_array($bet_area,$this ->groups["dont_come_bets"])){
				$haves["come_dont_come"] = true;
			}
			
			if(in_array($bet_area,$this ->groups["pass_line_bets"])){
				$haves["pass"] = true;
				$totals["pass"] += $bet_amount;
			}
			
			if(in_array($bet_area,$this ->groups["dont_pass_line_bets"])){
				$haves["dont_pass"] = true;
				$totals["dont_pass"] += $bet_amount;
			}
			
			if(in_array($bet_area,$this ->groups["pass_line_odds_bets"])){
				$haves["pass_odds"] = true;
				$totals["pass_odds"] += $bet_amount;
			}
			
			if(in_array($bet_area,$this ->groups["dont_pass_line_odds_bets"])){
				$haves["dont_pass_odds"] = true;
				$totals["dont_pass_odds"] += $bet_amount;
			}
			
		}
		
		
		if(
			($this->session ->vars["game_status"] == "come_out" && $haves["come_dont_come"]) ||
			($this->session ->vars["game_status"] == "come_out" && !$haves["pass_dont_pass"]) ||
			($this->session ->vars["game_status"] == "come_out" && ($haves["pass_odds"] || $haves["dont_pass_odds"])) ||
			($haves["pass_odds"] && !$haves["pass"]) ||
			($haves["dont_pass_odds"] && !$haves["dont_pass"]) ||
			($haves["pass_odds"] && $totals["pass_odds"] > ($totals["pass"]*2)) ||
			($haves["dont_pass_odds"] && $totals["dont_pass_odds"] > ($totals["dont_pass"]*2))
		){
			$can = false;
		}
		
		return $can;
		
	}
	
	function is_valid_dice_result($res){
		$is = false;
		if(is_numeric($res) && $res >= 1 && $res <= 6){
			$is = true;
		}
		return $is;	
	}
	
	function roll($bets, $pf_nums = NULL){
		global $game_id, $_using_free_play;
		
		$result = array();
		$end_session = false;
		$win_amount = 0;
		$betted_amount = 0;
		$loss_amount = 0;
		$keept_amount = 0;
		$remove_bets = array();
		$won_bets = array();
		$keep_bets = array();
		$moving_bets = array();
		
		if($this->is_valid_dice_result($pf_nums[0]) && $this->is_valid_dice_result($pf_nums[1])){
			$dice1 = $pf_nums[0];
			$dice2 = $pf_nums[1];
		}else{
			$dice1 = rc_random(1,6);
			$dice2 = rc_random(1,6);	
		}
		
		
		$total_number = $dice1 + $dice2;
		$game_status = $this ->session ->vars["game_status"];
		
		$win_areas = array();
		$loss_areas = array();
		
		$bet_list = explode(",",$bets);
		foreach($bet_list as $bet){
			$bet_won = false;
			$keep_bet = false;
			$force_keep_bet = false;
			$parts = explode("|",$bet);
			$bet_area = $parts[0];
			$bet_amount = $parts[1];
			$new_bet_area = "";
			
			//Pass and dont pass bets already tooked from balance on comeout roll
			if( ($this->session ->vars["game_status"] == "come_out" || (!in_array($bet_area,$this ->groups["dont_pass_line_bets"]) && !in_array($bet_area,$this ->groups["pass_line_bets"]))) && ( !in_array($bet_area,$this ->groups["dont_come_numbers_bets"]) && !in_array($bet_area,$this ->groups["come_numbers_bets"]) ) ){
				$betted_amount += $bet_amount;
			}
			
			if($this->session ->vars["game_status"] == "come_out"){
				
				//Come Out Roll
				
				if(in_array($total_number,$this ->groups["seven_eleven"])){
					//Seven or eleven on come_out
					$end_session = true;
					if(in_array($bet_area,$this ->groups["pass_line_bets"])){
						$win_amount += $bet_amount*2;
						$bet_won = true;
					}
				}else if(in_array($total_number,$this ->groups["craps"]) && $total_number != 12){
					//craps on come_out, but no 12
					$end_session = true;
					if(in_array($bet_area,$this ->groups["dont_pass_line_bets"])){
						$win_amount += $bet_amount*2;
						$bet_won = true;						
					}
				}else if($total_number == 12){
					//12 on come_out
					$end_session = true;
					if(in_array($bet_area,$this ->groups["dont_pass_line_bets"])){
						$win_amount += $bet_amount;
						$bet_won = true;					
					}
				}
				
				if((in_array($bet_area,$this ->groups["dont_pass_line_bets"]) || in_array($bet_area,$this ->groups["pass_line_bets"])) && !$end_session){
					$force_keep_bet = true;
				}
				
			}else{
					
				//Point stablished roll
				$point = $this->session ->vars["point"];
				
				
				//Come donde come
				if(in_array($total_number,$this ->groups["seven_eleven"])){
					//Seven or eleven
					if(in_array($bet_area,$this ->groups["come_bets"])){
						$win_amount += $bet_amount*2;
						$bet_won = true;
					}
				}else if(in_array($total_number,$this ->groups["craps"]) && $total_number != 12){
					//craps, but no 12
					if(in_array($bet_area,$this ->groups["dont_come_bets"])){
						$win_amount += $bet_amount*2;
						$bet_won = true;						
					}
				}else if($total_number == 12){
					//12 on come_out
					if(in_array($bet_area,$this ->groups["dont_come_bets"])){
						$win_amount += $bet_amount;
						$bet_won = true;					
					}
				}
				
				if(in_array($bet_area,$this ->groups["dont_come_bets"]) && !$bet_won && !in_array($total_number,$this ->groups["seven_eleven"])){
					$force_keep_bet = true;
					switch($total_number){ 
						case 4: $new_bet_area = "dont_come_four"; break;
						case 5: $new_bet_area = "dont_come_five"; break;
						case 6: $new_bet_area = "dont_come_six"; break;
						case 8: $new_bet_area = "dont_come_eight"; break;
						case 9: $new_bet_area = "dont_come_nine"; break;
						case 10: $new_bet_area = "dont_come_ten"; break;
					}
					$moving_bets[] = $bet_area."->".$new_bet_area;
				}
				if(in_array($bet_area,$this ->groups["come_bets"]) && !$bet_won && !in_array($total_number,$this ->groups["craps"])){
					$force_keep_bet = true;
					switch($total_number){ 
						case 4: $new_bet_area = "come_four"; break;
						case 5: $new_bet_area = "come_five"; break;
						case 6: $new_bet_area = "come_six"; break;
						case 8: $new_bet_area = "come_eight"; break;
						case 9: $new_bet_area = "come_nine"; break;
						case 10: $new_bet_area = "come_ten"; break;
					}
					$moving_bets[] = $bet_area."->".$new_bet_area;
				}
				
				
				
				
				
				if($total_number == 7){
					$end_session = true;
					//7 with point
					if(in_array($bet_area,$this ->groups["dont_pass_line_bets"])){
						$win_amount += $bet_amount*2;
						$bet_won = true;						
					}
					
					if(in_array($bet_area,$this ->groups["dont_pass_line_odds_bets"])){
						
						switch($point){ 
							case 4:
							case 10:
								$win_amount += $bet_amount*1.5;
							break;
							case 5:
							case 9:
								$win_amount += $bet_amount*1.667;
							break;
							case 6:
							case 8:
								$win_amount += $bet_amount*1.833;
							break;
						}
						$bet_won = true;						
					}
					
					//Lose point Bets
					if($bet_area == "lose_four"){
						$win_amount += $bet_amount*1.455;
						$bet_won = true;
					}else if($bet_area == "lose_five"){
						$win_amount += $bet_amount*1.625;
						$bet_won = true;
					}else if($bet_area == "lose_six"){
						$win_amount += $bet_amount*1.8;
						$bet_won = true;
					}else if($bet_area == "lose_eight"){
						$win_amount += $bet_amount*1.8;
						$bet_won = true;
					}else if($bet_area == "lose_nine"){
						$win_amount += $bet_amount*1.625;
						$bet_won = true;
					}else if($bet_area == "lose_ten"){
						$win_amount += $bet_amount*1.455;
						$bet_won = true;
					}
					
					//Lay point Bets
					if($bet_area == "lay_four"){
						$win_amount += $bet_amount + (($bet_amount*0.5)*0.95);
						$bet_won = true;
					}else if($bet_area == "lay_five"){
						$win_amount += $bet_amount + (($bet_amount*0.67)*0.95);
						$bet_won = true;
					}else if($bet_area == "lay_six"){
						$win_amount += $bet_amount + (($bet_amount*0.83)*0.95);
						$bet_won = true;
					}else if($bet_area == "lay_eight"){
						$win_amount += $bet_amount + (($bet_amount*0.83)*0.95);
						$bet_won = true;
					}else if($bet_area == "lay_nine"){
						$win_amount += $bet_amount + (($bet_amount*0.67)*0.95);
						$bet_won = true;
					}else if($bet_area == "lay_ten"){
						$win_amount += $bet_amount + (($bet_amount*0.5)*0.95);
						$bet_won = true;
					}
					
				}else if($total_number == $point){
					//point hitted
					$end_session = true;
					if(in_array($bet_area,$this ->groups["pass_line_bets"])){
						$win_amount += $bet_amount*2;
						$bet_won = true;						
					}
					
					if(in_array($bet_area,$this ->groups["pass_line_odds_bets"])){
						switch($point){ 
							case 4:
							case 10:
								$win_amount += $bet_amount*3;
							break;
							case 5:
							case 9:
								$win_amount += $bet_amount*2.5;
							break;
							case 6:
							case 8:
								$win_amount += $bet_amount*2.2;
							break;
						}
						$bet_won = true;						
					}
					
				}else{
					if((in_array($bet_area,$this ->groups["dont_pass_line_bets"]) || in_array($bet_area,$this ->groups["pass_line_bets"])) && !$end_session){
						$force_keep_bet = true;
					}
					if((in_array($bet_area,$this ->groups["dont_pass_line_odds_bets"]) || in_array($bet_area,$this ->groups["pass_line_odds_bets"])) && !$end_session){
						$keep_bet = true;
					}
				}
				
				//win point bets
				if($total_number == 4 && $bet_area == "win_four"){
					$win_amount += $bet_amount*2.8;
					$bet_won = true;
				}else if($total_number == 5 && $bet_area == "win_five"){
					$win_amount += $bet_amount*2.4;
					$bet_won = true;
				}else if($total_number == 6 && $bet_area == "win_six"){
					$win_amount += $bet_amount*2.167;
					$bet_won = true;
				}else if($total_number == 8 && $bet_area == "win_eight"){
					$win_amount += $bet_amount*2.167;
					$bet_won = true;
				}else if($total_number == 9 && $bet_area == "win_nine"){
					$win_amount += $bet_amount*2.4;
					$bet_won = true;
				}else if($total_number == 10 && $bet_area == "win_ten"){
					$win_amount += $bet_amount*2.8;
					$bet_won = true;
				}
				
				if(in_array($bet_area,$this ->groups["win_points_bets"]) && $total_number != 7 && !$bet_won){
					$keep_bet = true;
				}
				
				//buy point bets
				if($total_number == 4 && $bet_area == "buy_four"){
					$win_amount += $bet_amount + (($bet_amount*2)*0.95);
					$bet_won = true;
				}else if($total_number == 5 && $bet_area == "buy_five"){
					$win_amount += $bet_amount + (($bet_amount*1.5)*0.95);
					$bet_won = true;
				}else if($total_number == 6 && $bet_area == "buy_six"){
					$win_amount += $bet_amount + (($bet_amount*1.2)*0.95);
					$bet_won = true;
				}else if($total_number == 8 && $bet_area == "buy_eight"){
					$win_amount += $bet_amount + (($bet_amount*1.2)*0.95);
					$bet_won = true;
				}else if($total_number == 9 && $bet_area == "buy_nine"){
					$win_amount += $bet_amount + (($bet_amount*1.5)*0.95);
					$bet_won = true;
				}else if($total_number == 10 && $bet_area == "buy_ten"){
					$win_amount += $bet_amount + (($bet_amount*2)*0.95);
					$bet_won = true;
				}
				
				if(in_array($bet_area,$this ->groups["buy_points_bets"]) && $total_number != 7 && !$bet_won){
					$keep_bet = true;
				}
				
				
				//Keep lose point bets
				if($bet_area == "lose_four" && $total_number != 4 && !$bet_won){
					$keep_bet = true;
				}else if($bet_area == "lose_five" && $total_number != 5 && !$bet_won){
					$keep_bet = true;
				}else if($bet_area == "lose_six" && $total_number != 6 && !$bet_won){
					$keep_bet = true;
				}else if($bet_area == "lose_eight" && $total_number != 8 && !$bet_won){
					$keep_bet = true;
				}else if($bet_area == "lose_nine" && $total_number != 9 && !$bet_won){
					$keep_bet = true;
				}else if($bet_area == "lose_ten" && $total_number != 10 && !$bet_won){
					$keep_bet = true;
				}
				
				//Keep lay point bets
				if($bet_area == "lay_four" && $total_number != 4 && !$bet_won){
					$keep_bet = true;
				}else if($bet_area == "lay_five" && $total_number != 5 && !$bet_won){
					$keep_bet = true;
				}else if($bet_area == "lay_six" && $total_number != 6 && !$bet_won){
					$keep_bet = true;
				}else if($bet_area == "lay_eight" && $total_number != 8 && !$bet_won){
					$keep_bet = true;
				}else if($bet_area == "lay_nine" && $total_number != 9 && !$bet_won){
					$keep_bet = true;
				}else if($bet_area == "lay_ten" && $total_number != 10 && !$bet_won){
					$keep_bet = true;
				}
				
				
			}
			
			//Other bets
			
			
			//Stablished come/dont come bets
			if(in_array($bet_area,$this ->groups["dont_come_numbers_bets"])){
				if($total_number == 7){
					$win_amount += $bet_amount*2;
					$bet_won = true;
				}
			} 
			if($bet_area == "dont_come_four" && $total_number != 4 && $total_number != 7){$force_keep_bet = true;}
			else if($bet_area == "dont_come_five" && $total_number != 5 && $total_number != 7){$force_keep_bet = true;}
			else if($bet_area == "dont_come_six" && $total_number != 6 && $total_number != 7){$force_keep_bet = true;}
			else if($bet_area == "dont_come_eight" && $total_number != 8 && $total_number != 7){$force_keep_bet = true;}
			else if($bet_area == "dont_come_nine" && $total_number != 9 && $total_number != 7){$force_keep_bet = true;}
			else if($bet_area == "dont_come_ten" && $total_number != 10 && $total_number != 7){$force_keep_bet = true;}
			
			if($bet_area == "come_four" && $total_number == 4){
				$win_amount += $bet_amount*2;
				$bet_won = true;
			}else if($bet_area == "come_five" && $total_number == 5){
				$win_amount += $bet_amount*2;
				$bet_won = true;	
			}else if($bet_area == "come_six" && $total_number == 6){
				$win_amount += $bet_amount*2;
				$bet_won = true;	
			}else if($bet_area == "come_eight" && $total_number == 8){
				$win_amount += $bet_amount*2;
				$bet_won = true;	
			}else if($bet_area == "come_nine" && $total_number == 9){
				$win_amount += $bet_amount*2;
				$bet_won = true;	
			}else if($bet_area == "come_ten" && $total_number == 10){
				$win_amount += $bet_amount*2;
				$bet_won = true;	
			}
			if(in_array($bet_area,$this ->groups["come_numbers_bets"]) && $total_number != 7 && !$bet_won){
				$force_keep_bet = true;
			}
			
			
			
			
			//Hard ways
			if($bet_area == "hard_six"){
				if($dice1 == 3 && $dice2 == 3){
					$win_amount += ($bet_amount*9)+$bet_amount;
					if($_using_free_play){$win_amount -= $bet_amount;}
					$bet_won = true;
				}else if($total_number != 6 && $total_number != 7){
					$keep_bet = true;
				}
			}else if($bet_area == "hard_ten"){
				if($dice1 == 5 && $dice2 == 5){
					$win_amount += ($bet_amount*7)+$bet_amount;
					if($_using_free_play){$win_amount -= $bet_amount;}
					$bet_won = true;
				}else if($total_number != 10 && $total_number != 7){
					$keep_bet = true;
				}
			}else if($bet_area == "hard_eight"){
				if($dice1 == 4 && $dice2 == 4){
					$win_amount += ($bet_amount*9)+$bet_amount;
					if($_using_free_play){$win_amount -= $bet_amount;}
					$bet_won = true;
				}else if($total_number != 8 && $total_number != 7){
					$keep_bet = true;
				}
			}else if($bet_area == "hard_four"){
				if($dice1 == 2 && $dice2 == 2){
					$win_amount += ($bet_amount*7)+$bet_amount;
					if($_using_free_play){$win_amount -= $bet_amount;}
					$bet_won = true;
				}else if($total_number != 4 && $total_number != 7){
					$keep_bet = true;
				}
			}
			
			
			if($total_number == 3 && $bet_area == "three"){
				//Three (Ace Deuce): Wins if the shooter rolls a 3 and pays 15 to 1.
				$win_amount += ($bet_amount*15)+$bet_amount;
				if($_using_free_play){$win_amount -= $bet_amount;}
				$bet_won = true;				
			}
			
			if($total_number == 7 && $bet_area == "seven"){
				//Any 7 (Big Red): Wins if the shooter rolls a 7 and pays 4 to 1.
				$win_amount += ($bet_amount*4)+$bet_amount;
				if($_using_free_play){$win_amount -= $bet_amount;}
				$bet_won = true;				
			}
			
			if(in_array($total_number,$this ->groups["craps"]) && $bet_area == "any_craps"){
				//Any Craps (Three way): Wins if the shooter rolls a 2, 3 or 12 and pays 7 to 1 for each number.
				$win_amount += ($bet_amount*7)+$bet_amount;
				if($_using_free_play){$win_amount -= $bet_amount;}
				$bet_won = true;				
			}
			
			if($total_number == 2 && $bet_area == "two"){
				//Two Craps or Aces (Snake Eyes): Wins if the shooter rolls a 2 and pays 30 to 1.
				$win_amount += ($bet_amount*30)+$bet_amount;
				if($_using_free_play){$win_amount -= $bet_amount;}
				$bet_won = true;				
			}
			
			if($total_number == 12 && $bet_area == "twelve"){
				//Twelve Craps (Boxcars or Midnight): Wins if the shooter rolls a 12 and pays 30 to 1.
				$win_amount += ($bet_amount*30)+$bet_amount;
				if($_using_free_play){$win_amount -= $bet_amount;}
				$bet_won = true;				
			}
			
			if($total_number == 11 && $bet_area == "eleven"){
				//Eleven (Yo): Wins if shooter rolls an 11 and pays 15 to 1
				$win_amount += ($bet_amount*15)+$bet_amount;
				if($_using_free_play){$win_amount -= $bet_amount;}
				$bet_won = true;				
			}
			
			if(in_array($total_number,$this ->groups["field"]) && in_array($bet_area,$this ->groups["fields_bets"])){
				//Field bets				
				if($total_number == 2 || $total_number == 12){
					$win_amount += ($bet_amount*2)+$bet_amount;	
					if($_using_free_play){$win_amount -= $bet_amount;}
				}else{
					$win_amount += $bet_amount*2;
				}
				$bet_won = true;				
			}
			
			
			if($new_bet_area != ""){
				$bet_area = $new_bet_area;
				$bet = $bet_area."|".$bet_amount;
			}
			
			
			
			if($bet_won){
				$won_bets[] = $bet_area;
			}else if(!$keep_bet && !$force_keep_bet){
				$remove_bets[] = $bet_area;
				$loss_amount += $bet_amount;
			}else if($force_keep_bet){
				$keep_bets[] = $bet;
			}else{
				$keep_bets[] = $bet;
				$betted_amount -= $bet_amount;
				$keept_amount += $bet_amount;
			}
			
			
			
			
		}
		
		
		$this->session ->vars["current_bets"] = implode(",",$keep_bets);
		if($end_session){
			$this->session ->vars["game_status"] = "finished";
			$this->session ->vars["end_time"] = date("Y-m-d H:i:s");
			$this->session ->vars["finished"] = 1;	
			$this->session ->vars["point"] = 0;	
		}else{	
			$this->session ->vars["finished"] = 0;		
			if($this->session ->vars["game_status"] == "come_out"){
				$this->session ->vars["point"] = $total_number;
				$this->session ->vars["game_status"] = "point";
			}
		}
		
		$this->session ->update();
		
		$finishing_bets = array_merge($remove_bets,$won_bets);
		$finishing_bets = implode(",",$finishing_bets);
		$remove_bets = implode(",",$remove_bets);
		$won_bets = implode(",",$won_bets);
		$moving_bets = implode(",",$moving_bets);
		
		$result["dice1_value"] = $dice1;
		$result["dice2_value"] = $dice2;
		$result["win_amount"] = $win_amount;
		$result["betted_amount"] = $betted_amount;
		$result["keept_amount"] = $keept_amount;
		$result["remove_bets"] = $remove_bets;
		$result["won_bets"] = $won_bets;
		$result["move_bets"] = $moving_bets;		
		$result["game_status"] = $this->session ->vars["game_status"];
		$result["finished"] = $this->session ->vars["finished"];
		$result["point"] = $this->session ->vars["point"];
		
		
		//Roll session
		$roll_session = new _craps_roll();
		$roll_session ->vars["session"] = $this->session ->vars["id"];
		$roll_session ->vars["bet_amount"] = $betted_amount;
		$roll_session ->vars["win_amount"] = $win_amount;
		$roll_session ->vars["finishing_bets"] = $finishing_bets;
		$roll_session ->vars["roll1"] = $dice1;
		$roll_session ->vars["roll2"] = $dice2;
		$roll_session ->vars["rdate"] = date("Y-m-d H:i:s");
		$roll_session ->vars["settle"] = $win_amount - $betted_amount;
		$roll_session->insert();
		
		$settle_amount = $win_amount - $betted_amount;
		if($settle_amount != 0){
			//Settle Log
			$log = new _settle_log();
			$log ->vars["game"] = $game_id;
			$log ->vars["player"] = $this ->player;
			$log ->vars["bet_amount"] = $betted_amount;
			$log ->vars["win_amount"] = $win_amount;
			$log ->vars["settle"] = $win_amount - $betted_amount;
			$log ->vars["ldate"] = date("Y-m-d H:i:s");
			$log ->insert();
		}
		
		return $result;
		
	}
   
   
}	
?>