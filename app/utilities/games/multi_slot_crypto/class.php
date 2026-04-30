<?
class multi_slot{
	var $player;
	var $win_amount = 0;
	var $free_spins_amount = 0;
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
								  array("figure"=>"fig1","amount"=>"3"),
								  array("figure"=>"fig2","amount"=>"4"),
								  array("figure"=>"fig3","amount"=>"6"),
								  array("figure"=>"fig4","amount"=>"8"),
								  array("figure"=>"fig5","amount"=>"10"),
								  array("figure"=>"fig6","amount"=>"15"),
								  array("figure"=>"fig7","amount"=>"18"),
								  array("figure"=>"fig8","amount"=>"22"),
								  array("figure"=>"fig9","amount"=>"23"),
								  array("figure"=>"fig10","amount"=>"25"),
								  array("figure"=>"fig11","amount"=>"27"),
								  array("figure"=>"fig12","amount"=>"29"),
								  array("figure"=>"fig13","amount"=>"10")
							 );
		 
		 $this->figures_on_reels[2] = array(
								  array("figure"=>"fig1","amount"=>"3"),
								  array("figure"=>"fig2","amount"=>"4"),
								  array("figure"=>"fig3","amount"=>"6"),
								  array("figure"=>"fig4","amount"=>"8"),
								  array("figure"=>"fig5","amount"=>"10"),
								  array("figure"=>"fig6","amount"=>"15"),
								  array("figure"=>"fig7","amount"=>"18"),
								  array("figure"=>"fig8","amount"=>"22"),
								  array("figure"=>"fig9","amount"=>"23"),
								  array("figure"=>"fig10","amount"=>"25"),
								  array("figure"=>"fig11","amount"=>"27"),
								  array("figure"=>"fig12","amount"=>"29"),
								  array("figure"=>"fig13","amount"=>"10")
							 );
		 
		 
		 $this->figures_on_reels[3] = array(
								  array("figure"=>"fig1","amount"=>"3"),
								  array("figure"=>"fig2","amount"=>"4"),
								  array("figure"=>"fig3","amount"=>"6"),
								  array("figure"=>"fig4","amount"=>"8"),
								  array("figure"=>"fig5","amount"=>"10"),
								  array("figure"=>"fig6","amount"=>"15"),
								  array("figure"=>"fig7","amount"=>"18"),
								  array("figure"=>"fig8","amount"=>"22"),
								  array("figure"=>"fig9","amount"=>"23"),
								  array("figure"=>"fig10","amount"=>"25"),
								  array("figure"=>"fig11","amount"=>"27"),
								  array("figure"=>"fig12","amount"=>"29"),
								  array("figure"=>"fig13","amount"=>"10")
							 );
		 
		 
		 $this->figures_on_reels[4] = array(
								  array("figure"=>"fig1","amount"=>"3"),
								  array("figure"=>"fig2","amount"=>"4"),
								  array("figure"=>"fig3","amount"=>"6"),
								  array("figure"=>"fig4","amount"=>"8"),
								  array("figure"=>"fig5","amount"=>"10"),
								  array("figure"=>"fig6","amount"=>"15"),
								  array("figure"=>"fig7","amount"=>"18"),
								  array("figure"=>"fig8","amount"=>"22"),
								  array("figure"=>"fig9","amount"=>"23"),
								  array("figure"=>"fig10","amount"=>"25"),
								  array("figure"=>"fig11","amount"=>"27"),
								  array("figure"=>"fig12","amount"=>"29"),
								  array("figure"=>"fig13","amount"=>"10")
							 );
		 
		 
		 $this->figures_on_reels[5] = array(
								  array("figure"=>"fig1","amount"=>"3"),
								  array("figure"=>"fig2","amount"=>"4"),
								  array("figure"=>"fig3","amount"=>"6"),
								  array("figure"=>"fig4","amount"=>"8"),
								  array("figure"=>"fig5","amount"=>"10"),
								  array("figure"=>"fig6","amount"=>"15"),
								  array("figure"=>"fig7","amount"=>"18"),
								  array("figure"=>"fig8","amount"=>"22"),
								  array("figure"=>"fig9","amount"=>"23"),
								  array("figure"=>"fig10","amount"=>"25"),
								  array("figure"=>"fig11","amount"=>"27"),
								  array("figure"=>"fig12","amount"=>"29"),
								  array("figure"=>"fig13","amount"=>"10")
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
		$fsp = 0;
		$line = $this->lines[$line_position];
		
		//echo $reels_result[$line[0]-1] ."-". $reels_result[$line[1]-1]  ."-".  $reels_result[$line[2]-1]  ."-".  $reels_result[$line[3]-1]  ."-".  $reels_result[$line[4]-1];
		
		if($reels_result[$line[0]-1] == $reels_result[$line[1]-1] && $reels_result[$line[1]-1] == $reels_result[$line[2]-1] && $reels_result[$line[2]-1] == $reels_result[$line[3]-1] && $reels_result[$line[3]-1] == $reels_result[$line[4]-1]){			
			//X5
			$this->win_x5 = 1;
			switch($reels_result[$line[0]-1]){ 
				case "fig1": $win = $bet_amount * 25000;  break; 
				case "fig2": $win = $bet_amount * 2500;  break;
				case "fig3": $win = $bet_amount * 2000;  break;
				case "fig4": $win = $bet_amount * 1500;  break;
				case "fig5": $win = $bet_amount * 1000;  break;
				case "fig6": $win = $bet_amount * 750;  break;
				case "fig7": $win = $bet_amount * 500;  break;
				case "fig8": $win = $bet_amount * 300;  break;
				case "fig9": $win = $bet_amount * 250;  break;
				case "fig10": $win = $bet_amount * 200;  break;
				case "fig11": $win = $bet_amount * 150;break;
				case "fig12": $win = $bet_amount * 100; break;
				case "fig13": /*$win = $bet_amount * 75;*/ $fsp = 6;  break; 
			}
			

		}else if($reels_result[$line[0]-1] == $reels_result[$line[1]-1] && $reels_result[$line[1]-1] == $reels_result[$line[2]-1] && $reels_result[$line[2]-1] == $reels_result[$line[3]-1]){
			//X4
			switch($reels_result[$line[0]-1]){ 
				case "fig1": $win = $bet_amount * 5000;  break;
				case "fig2": $win = $bet_amount * 1500;  break;
				case "fig3": $win = $bet_amount * 1000;  break;
				case "fig4": $win = $bet_amount * 750;  break;
				case "fig5": $win = $bet_amount * 600;  break;
				case "fig6": $win = $bet_amount * 500;  break;
				case "fig7": $win = $bet_amount * 250;  break;
				case "fig8": $win = $bet_amount * 200;  break;
				case "fig9": $win = $bet_amount * 150;  break;
				case "fig10": $win = $bet_amount * 125;  break;
				case "fig11": $win = $bet_amount * 75; break;
				case "fig12": $win = $bet_amount * 50; break;
				case "fig13": /*$win = $bet_amount * 25;*/ $fsp = 3;  break;
			}
			
			
		}else if($reels_result[$line[0]-1] == $reels_result[$line[1]-1] && $reels_result[$line[1]-1] == $reels_result[$line[2]-1]){ 
			//X3 ...
			switch($reels_result[$line[0]-1]){ 
				case "fig1": $win = $bet_amount * 2500;  break;
				case "fig2": $win = $bet_amount * 750;  break;
				case "fig3": $win = $bet_amount * 550;  break;
				case "fig4": $win = $bet_amount * 450;  break;
				case "fig5": $win = $bet_amount * 300;  break;
				case "fig6": $win = $bet_amount * 200;  break;
				case "fig7": $win = $bet_amount * 150;  break;
				case "fig8": $win = $bet_amount * 100;  break;
				case "fig9": $win = $bet_amount * 75;  break;
				case "fig10": $win = $bet_amount * 50;  break;
				case "fig11": $win = $bet_amount * 35; break;
				case "fig12": $win = $bet_amount * 25; break; 
				case "fig13": /*$win = $bet_amount * 15;*/ $fsp = 1;   break;
			}
			
		}
		
		
		
		if($win > 0){
			$this->winning_lines[] = $line_position."|".$win;
			//echo " =================> $win";
		}
		if($fsp > 0){
			$this->winning_lines[] = $line_position."|FS X".$fsp;
			$this ->free_spins_amount += $fsp;
		}
		//echo "<br />";
		
		return $win;
	}
	
	function generate_reel_result($reel_content){
		shuffle($reel_content);
		$fig1 = $reel_content[mt_rand(0,199)];
		$fig2 = $fig1;
		while($fig2 == $fig1){
			$fig2 = $reel_content[mt_rand(0,199)];
		}
		$fig3 = $fig1;
		while($fig3 == $fig1 || $fig3 == $fig2){
			$fig3 = $reel_content[mt_rand(0,199)];  
		}
		
		return $fig1."|".$fig2."|".$fig3;
	}
	
	function set_reels_content($fixed = true){
		
		
		if($fixed){
			//Using fixed json for provably fair
			$json = file_get_contents($_SERVER['DOCUMENT_ROOT']."/utilities/games/multi_slot_crypto/json/pf.txt");		
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
	
	function spin($bet, $lines_amount, $positions = "", $real_money = true){
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
			
			//test
			/*if(mt_rand(1,2)%2 || true){
				$reel_result[1] = "fig13|fig5|fig9";
				$reel_result[2] = "fig13|fig10|fig6";
				$reel_result[3] = "fig13|fig10|fig5";
				$reel_result[4] = "fig13|fig9|fig2";
				$reel_result[5] = "fig13|fig6|fig12";
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
			$this->win_amount += $this->get_prize($bet, $i, $result_array);
		}
		
		
		//update settles with free spins data
		
		if(!$real_money){$total_bet = 0;}
		
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
		
		
		return $result;
		
	}
   
   
}		
?>