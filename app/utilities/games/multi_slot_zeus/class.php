<?
class multi_slot{
	var $player;
	var $win_amount = 0;
	var $reels = array();
	var $figures_on_reels = array();
	var $lines = array();
	var $winning_lines = array();
	var $reels_content = array();
	var $result_block = "";
	var $win_x5 = 0;
	
	
	function __construct($id_player) {
	   	   
		 $this->player = $id_player;
		 
		 $this->lines[1] = array(2,5,8,11,14);
		 $this->lines[2] = array(1,4,7,10,13);
		 $this->lines[3] = array(3,6,9,12,15);
		 $this->lines[4] = array(1,5,9,11,13);
		 $this->lines[5] = array(3,5,7,11,15);
		 $this->lines[6] = array(1,4,8,10,13);
		 $this->lines[7] = array(3,6,8,12,15);
		 $this->lines[8] = array(2,4,7,10,14);
		 $this->lines[9] = array(2,6,9,12,14);
		 $this->lines[10] = array(1,4,8,12,15);
		 $this->lines[11] = array(3,6,8,10,13);
		 $this->lines[12] = array(2,4,8,12,14);
		 $this->lines[13] = array(2,6,8,10,14);
		 $this->lines[14] = array(1,5,7,11,13);
		 $this->lines[15] = array(3,5,9,11,15);
		 $this->lines[16] = array(1,5,8,11,13);
		 $this->lines[17] = array(3,5,8,11,15);
		 $this->lines[18] = array(2,4,8,10,14);
		 $this->lines[19] = array(2,6,8,12,14);
		 $this->lines[20] = array(2,5,7,11,14);
		 $this->lines[21] = array(2,5,9,11,14);
		 $this->lines[22] = array(1,6,9,12,13);
		 $this->lines[23] = array(3,4,7,10,15);
		 $this->lines[24] = array(1,6,7,12,13);
		 $this->lines[25] = array(3,4,9,10,15);
		 
		 
		 $this->figures_on_reels[1] = array(
								  array("figure"=>"fig8","amount"=>"5"),
								  array("figure"=>"fig10","amount"=>"6"),
								  array("figure"=>"fig2","amount"=>"9"),
								  array("figure"=>"fig3","amount"=>"10"),
								  array("figure"=>"fig9","amount"=>"10"),
								  array("figure"=>"fig6","amount"=>"10"),
								  array("figure"=>"fig1","amount"=>"15"),
								  array("figure"=>"fig5","amount"=>"15"),
								  array("figure"=>"fig7","amount"=>"15"),
								  array("figure"=>"fig4","amount"=>"15")
							 );
		 
		 $this->figures_on_reels[2] = array(
								  array("figure"=>"fig8","amount"=>"3"),
								  array("figure"=>"fig10","amount"=>"5"),
								  array("figure"=>"fig2","amount"=>"7"),
								  array("figure"=>"fig3","amount"=>"10"),
								  array("figure"=>"fig9","amount"=>"12"),
								  array("figure"=>"fig6","amount"=>"12"),
								  array("figure"=>"fig1","amount"=>"16"),
								  array("figure"=>"fig5","amount"=>"15"),
								  array("figure"=>"fig7","amount"=>"15"),
								  array("figure"=>"fig4","amount"=>"15")
							 );
		 
		 
		 $this->figures_on_reels[3] = array(
								  array("figure"=>"fig8","amount"=>"3"),
								  array("figure"=>"fig10","amount"=>"3"),
								  array("figure"=>"fig2","amount"=>"12"),
								  array("figure"=>"fig3","amount"=>"12"),
								  array("figure"=>"fig9","amount"=>"12"),
								  array("figure"=>"fig6","amount"=>"10"),
								  array("figure"=>"fig1","amount"=>"17"),
								  array("figure"=>"fig5","amount"=>"17"),
								  array("figure"=>"fig7","amount"=>"12"),
								  array("figure"=>"fig4","amount"=>"12")
							 );
		 
		 
		 $this->figures_on_reels[4] = array(
								  array("figure"=>"fig8","amount"=>"5"),
								  array("figure"=>"fig10","amount"=>"6"),
								  array("figure"=>"fig2","amount"=>"9"),
								  array("figure"=>"fig3","amount"=>"9"),
								  array("figure"=>"fig9","amount"=>"9"),
								  array("figure"=>"fig6","amount"=>"8"),
								  array("figure"=>"fig1","amount"=>"17"),
								  array("figure"=>"fig5","amount"=>"17"),
								  array("figure"=>"fig7","amount"=>"15"),
								  array("figure"=>"fig4","amount"=>"15")
							 );
		 
		 
		 $this->figures_on_reels[5] = array(
								   array("figure"=>"fig8","amount"=>"3"),
								  array("figure"=>"fig10","amount"=>"3"),
								  array("figure"=>"fig2","amount"=>"12"),
								  array("figure"=>"fig3","amount"=>"12"),
								  array("figure"=>"fig9","amount"=>"12"),
								  array("figure"=>"fig6","amount"=>"10"),
								  array("figure"=>"fig1","amount"=>"17"),
								  array("figure"=>"fig5","amount"=>"17"),
								  array("figure"=>"fig7","amount"=>"12"),
								  array("figure"=>"fig4","amount"=>"12")
							 );
		 				 
							 
		  //shuffle($this->figures_on_reels);
	   
	}
	
	function generate_pf_batch(){
		$result = array();
		
		$total = 0;
		$final_reel = array();

		$x = 1;
		foreach($this->figures_on_reels as $reel){
			foreach($reel as $figure){
				if($x==1){$total += $figure["amount"];}
				for($i=0;$i<$figure["amount"]*1;$i++){
					$final_reel[$x][] = $figure["figure"];
				}	
			}
			
			shuffle($final_reel[$x]);
			$x++;
		}
		
		$result["json"] = json_encode($final_reel);
		$result["total"] = $total;
		
		return $result;
	}
	
	function get_prize($bet_amount, $line_position ,$reels_result){
		$win = 0;
		$line = $this->lines[$line_position];
		
		//echo $reels_result[$line[0]-1] ."-". $reels_result[$line[1]-1]  ."-".  $reels_result[$line[2]-1]  ."-".  $reels_result[$line[3]-1]  ."-".  $reels_result[$line[4]-1];
		
		if($reels_result[$line[0]-1] == $reels_result[$line[1]-1] && $reels_result[$line[1]-1] == $reels_result[$line[2]-1] && $reels_result[$line[2]-1] == $reels_result[$line[3]-1] && $reels_result[$line[3]-1] == $reels_result[$line[4]-1]){			
			//X5
			$this->win_x5 = 1;
			switch($reels_result[$line[0]-1]){ 
				case "fig8": $win = $bet_amount * 5000;  break;
				case "fig10": $win = $bet_amount * 1000;  break;
				case "fig2": $win = $bet_amount * 750;  break;
				case "fig3": $win = $bet_amount * 750;  break;
				case "fig9": $win = $bet_amount * 500;  break;
				case "fig6": $win = $bet_amount * 500;  break;
				case "fig1": $win = $bet_amount * 250;  break;
				case "fig5": $win = $bet_amount * 250;  break;
				case "fig7": $win = $bet_amount * 150;  break;
				case "fig4": $win = $bet_amount * 150;  break;
			}
			

		}else if($reels_result[$line[0]-1] == $reels_result[$line[1]-1] && $reels_result[$line[1]-1] == $reels_result[$line[2]-1] && $reels_result[$line[2]-1] == $reels_result[$line[3]-1]){
			//X4
			switch($reels_result[$line[0]-1]){ 
				case "fig8": $win = $bet_amount * 1000;  break;
				case "fig10": $win = $bet_amount * 500;  break;
				case "fig2": $win = $bet_amount * 350;  break;
				case "fig3": $win = $bet_amount * 350;  break;
				case "fig9": $win = $bet_amount * 250;  break;
				case "fig6": $win = $bet_amount * 250;  break;
				case "fig1": $win = $bet_amount * 200;  break;
				case "fig5": $win = $bet_amount * 200;  break;
				case "fig7": $win = $bet_amount * 100;  break;
				case "fig4": $win = $bet_amount * 100;  break;
			}
			
			
		}else if($reels_result[$line[0]-1] == $reels_result[$line[1]-1] && $reels_result[$line[1]-1] == $reels_result[$line[2]-1]){
			//X3
			switch($reels_result[$line[0]-1]){ 
				case "fig8": $win = $bet_amount * 500;  break;
				case "fig10": $win = $bet_amount * 250;  break;
				case "fig2": $win = $bet_amount * 100;  break;
				case "fig3": $win = $bet_amount * 100;  break;
				case "fig9": $win = $bet_amount * 75;  break;
				case "fig6": $win = $bet_amount * 75;  break;
				case "fig1": $win = $bet_amount * 50;  break;
				case "fig5": $win = $bet_amount * 50;  break;
				case "fig7": $win = $bet_amount * 25;  break;
				case "fig4": $win = $bet_amount * 25;  break; 
			}
			
		}
		
		
		
		if($win > 0){
			$this->winning_lines[] = $line_position."|".$win;
			//echo " =================> $win";
		}
		//echo "<br />";
		
		return $win;
	}
	
	function generate_reel_result($reel_content){
		//shuffle($reel_content);
		$fig1 = $reel_content[mt_rand(0,109)];
		$fig2 = $fig1;
		while($fig2 == $fig1){
			$fig2 = $reel_content[mt_rand(0,109)];
		}
		$fig3 = $fig1;
		while($fig3 == $fig1 || $fig3 == $fig2){
			$fig3 = $reel_content[mt_rand(0,109)];
		}
		
		return $fig1."|".$fig2."|".$fig3;
	}
	
	function set_reels_content($fixed = true){
		if($fixed){
			
			//Using fixed json for provably fair
			
			$json = file_get_contents($_SERVER['DOCUMENT_ROOT']."/utilities/games/multi_slot_zeus/json/pf.txt");
			$this->reels_content = json_decode($json,true);
			
		}else{
			
			//Using random order
			$creel = 1;
			foreach($this->figures_on_reels as $figures){
				foreach($figures as $figure){
					for($i=0;$i<$figure["amount"];$i++){
						$this->reels_content[$creel][] = $figure["figure"];	
					}	
				}
				shuffle($this->reels_content[$creel]);
				$creel++;	
			}			
			
		}
		
		
	}
	
	function spin($bet, $lines_amount, $positions = ""){
		global $game_id;
		
		$total_bet = $bet*$lines_amount;
		
		if(!is_numeric($lines_amount) || $lines_amount < 1){$lines_amount = 1;}
		if($lines_amount > count($this ->lines)){$lines_amount = count($this ->lines);}
		
		$this->win_amount = 0;
		
		
		
		
		$reel_result = array();	
		
		if($positions != ""){
			//using provably fair
			$this->set_reels_content();
			
			$pos_nums = explode(",",$positions);
			$fixer = 0;
			for($i=1; $i<=5; $i++){
				$reel_result[$i] = $this->reels_content[$i][$pos_nums[0+$fixer]]."|".$this->reels_content[$i][$pos_nums[1+$fixer]]."|".$this->reels_content[$i][$pos_nums[2+$fixer]];
				$fixer += 3;
			}
			
			//example winner
			/*if(mt_rand(1,2)%2){
				$reel_result[1] = "tuta|J|eye";
				$reel_result[2] = "tuta|K|Q";
				$reel_result[3] = "tuta|K|eye";
				$reel_result[4] = "tuta|pyramid|eye";
				$reel_result[5] = "tuta|A|vessel";
			}*/
			
				
		}else{
			//using random mode
			$this->set_reels_content(false);
			for($i=1; $i<=5; $i++){
				$reel_result[$i] = $this->generate_reel_result($this->reels_content[$i]);
			}
		}
		
		
		
		$result = implode("|",$reel_result);
		$result_array = explode("|",$result);
		
		for($i=1;$i<=$lines_amount;$i++){
			$this->win_amount += $this->get_prize($bet, $i,$result_array);
		}
		
		
		$session = new _slot_session();
		$session ->vars["player"] = $this ->player;
		$session ->vars["bet_amount"] = $total_bet;
		$session ->vars["win_amount"] = $this->win_amount;
		$session ->vars["reel"] = json_encode($reel_result);
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
		
		
		
		
		
	
		/*$reel1_position = mt_rand(0,99);
		$reel2_position = mt_rand(0,99);
		$reel3_position = mt_rand(0,99);
		
		$reel_block = $this->reels_content[1][$reel1_position]."|".$this->reels_content[2][$reel2_position]."|".$this->reels_content[3][$reel3_position];
		$this->result_block = $reel_block;
		
		if(substr_count($reel_block,"diamond") == 3){
			$this->win_amount = $bet_amount * 800;
		}else if(substr_count($reel_block,"seven") == 3){
			$this->win_amount = $bet_amount * 80;
		}else if(substr_count($reel_block,"grapes") == 3){
			$this->win_amount = $bet_amount * 40;
		}else if(substr_count($reel_block,"watermelon") == 3){
			$this->win_amount = $bet_amount * 25;
		}else if(substr_count($reel_block,"cherry") == 3){
			$this->win_amount = $bet_amount * 10;
		}else if(substr_count($reel_block,"cherry") > 1){
			$this->win_amount = $bet_amount * 3;
		}else if(substr_count($reel_block,"orange") > 1){
			$this->win_amount = $bet_amount * 1;
		}else if(substr_count($reel_block,"bananas") > 1){
			$this->win_amount = $bet_amount * 1;
		}
		
		$session = new _slot_session();
		$session ->vars["player"] = $this ->player;
		$session ->vars["bet_amount"] = $bet_amount;
		$session ->vars["win_amount"] = $this->win_amount;
		$session ->vars["reel"] = $reel_block;
		$session ->vars["game"] = $game_id;
		$session ->vars["gdate"] = date("Y-m-d H:i:s");
		$session ->vars["settle"] = ($this->win_amount - $bet_amount);
		$session ->insert();
		
		$log = new _settle_log();
		$log ->vars["game"] = $game_id;
		$log ->vars["player"] = $this ->player;
		$log ->vars["bet_amount"] = $bet_amount;
		$log ->vars["win_amount"] = $this->win_amount;
		$log ->vars["settle"] = ($this->win_amount - $bet_amount);
		$log ->vars["ldate"] = date("Y-m-d H:i:s");
		$log ->insert();
		
		return array_search($this->reels_content[1][$reel1_position],$this->reels[1]).",".array_search($this->reels_content[2][$reel2_position],$this->reels[2]).",".array_search($this->reels_content[3][$reel3_position],$this->reels[3]);*/
		
		return $result;
		
	}
   
   
}		
?>