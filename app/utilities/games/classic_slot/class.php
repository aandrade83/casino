<?
class classic_slot{
	var $player;
	var $win_amount = 0;
	var $reels = array();
	var $figures_on_reels = array();
	var $reels_content = array();
	var $result_block = "";
	
	
	function __construct($id_player) {
	   	   
		 $this->player = $id_player;
		 $this->reels[1] = array('cherry','orange','seven','watermelon','diamond','grapes','bananas','watermelon','orange','grapes');
		 $this->reels[2] = array('bananas','watermelon','orange','grapes','cherry','diamond','bananas','orange','seven','watermelon');
		 $this->reels[3] = array('bananas','diamond','orange','grapes','seven','cherry','bananas','orange','watermelon','grapes');
		 
		 $this->figures_on_reels[1] = array(
								  array("figure"=>"diamond","amount"=>"5"),
								  array("figure"=>"seven","amount"=>"10"),
								  array("figure"=>"grapes","amount"=>"15"),
								  array("figure"=>"watermelon","amount"=>"20"),
								  array("figure"=>"cherry","amount"=>"23"),
								  array("figure"=>"orange","amount"=>"12"),
								  array("figure"=>"bananas","amount"=>"15")
							 );
		 
		 $this->figures_on_reels[2] = array(
								  array("figure"=>"diamond","amount"=>"1"),
								  array("figure"=>"seven","amount"=>"4"),
								  array("figure"=>"grapes","amount"=>"10"),
								  array("figure"=>"watermelon","amount"=>"15"),
								  array("figure"=>"cherry","amount"=>"23"),
								  array("figure"=>"bananas","amount"=>"22"),
								  array("figure"=>"orange","amount"=>"25") 
							 );							 
		  
								 
		  $this->figures_on_reels[3] = array(
								  array("figure"=>"diamond","amount"=>"1"),
								  array("figure"=>"seven","amount"=>"4"),
								  array("figure"=>"grapes","amount"=>"10"),
								  array("figure"=>"watermelon","amount"=>"15"),
								  array("figure"=>"cherry","amount"=>"23"),
								  array("figure"=>"bananas","amount"=>"22"),
								  array("figure"=>"orange","amount"=>"25") 
							 );
							 
		  //shuffle($this->figures_on_reels);
	   
	}
	
	function load_reels_content(){
		//Using random 
		/*$creel = 1;
		foreach($this->figures_on_reels as $figures){
			foreach($figures as $figure){
				for($i=0;$i<$figure["amount"];$i++){
					$this->reels_content[$creel][] = $figure["figure"];	
				}	
			}
			$creel++;	
		}	
		
		shuffle($this->reels_content[1]);
		shuffle($this->reels_content[2]);
		shuffle($this->reels_content[3]);*/
		
		//echo json_encode($this->reels_content); //To generate results json
		
		//Using fixed json for provably fair
		$json = file_get_contents($_SERVER['DOCUMENT_ROOT']."/utilities/games/classic_slot/json/fruits.txt");
		$this->reels_content = json_decode($json,true);
		
		
	}
	
	function spin($bet_amount, $position = "na"){
		global $game_id;
		
		$this->win_amount = 0;
		$this->load_reels_content();
		
		/*shuffle($this->reels_content[1]);
		shuffle($this->reels_content[2]);
		shuffle($this->reels_content[3]);*/
	
		if(is_numeric($position) && $position >= 1000000 && $position < 9999999){
			
			//use position from provably fair caulculation
			$reel1_position = (substr($position,1,2)*1);
			$reel2_position = (substr($position,3,2)*1);
			$reel3_position = (substr($position,5,2)*1);
			
		}else{
			$reel1_position = mt_rand(0,99);
			$reel2_position = mt_rand(0,99);
			$reel3_position = mt_rand(0,99);
		}
	
		
		
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
		
		return array_search($this->reels_content[1][$reel1_position],$this->reels[1]).",".array_search($this->reels_content[2][$reel2_position],$this->reels[2]).",".array_search($this->reels_content[3][$reel3_position],$this->reels[3]);
		
	}
   
   
}	
?>