<?php

$main_url = "tp://casinoXP.io";



class _codex{
	
	private $secretkey;
	private $master_key = "875324";
	
	function _codex($text){
		$this->secretkey = $text;
	}

	function encrypt($text) {

    $secret = (string) ($this->secretkey ?? '');

    if ($secret === '') {
        // fallback seguro para evitar romper todo el sistema
        return base64_encode($text);
    }

    $key = substr(hash('sha256', $secret, true), 0, 16); // 128-bit
    $encrypted = openssl_encrypt($text, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);

    return base64_encode($encrypted);
}

function decrypt($text) {

    $secret = (string) ($this->secretkey ?? '');

    if ($secret === '') {
        return base64_decode($text);
    }

    $key = substr(hash('sha256', $secret, true), 0, 16);
    $decoded = base64_decode($text);

    return trim(openssl_decrypt($decoded, 'AES-128-ECB', $key, OPENSSL_RAW_DATA));
}


	/*
    function encrypt($text) {
        $data = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $this->secretkey, $text, MCRYPT_MODE_ECB, 'keee');
        return base64_encode($data);
    }
    function decrypt($text) {
        $text = base64_decode($text);
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $this->secretkey, $text, MCRYPT_MODE_ECB, 'keee'));
    }


	*/
	
	function encode_number($number){
		$number = str_replace("1","H",$number);
		$number = str_replace("2","y",$number);
		$number = str_replace("3","P",$number);
		$number = str_replace("4","1",$number);
		$number = str_replace("5","W",$number);
		$number = str_replace("6","C",$number);
		$number = str_replace("7","5",$number);
		$number = str_replace("8","2",$number);
		$number = str_replace("9","q",$number);
		$number = str_replace("0","9",$number);
		return $number;		
	}
	function decode_number($code){
		$code = str_replace("9","0",$code);
		$code = str_replace("q","9",$code);
		$code = str_replace("2","8",$code);
		$code = str_replace("5","7",$code);
		$code = str_replace("C","6",$code);
		$code = str_replace("W","5",$code);
		$code = str_replace("1","4",$code);
		$code = str_replace("P","3",$code);
		$code = str_replace("y","2",$code);
		$code = str_replace("H","1",$code);		
		return $code;
	}

}

class _DB_ELEMENT{
	var $vars = array();
	function update_post_vars(){
		$keys = array_keys($_POST);
		foreach($keys as $key){
			if(contains($key,"var_")){
				$vname = str_replace("var_","",$key);
				$value = param($key);
				if(contains($vname,"date")){$value = date("Y-m-d H:i:s",strtotime($value));}
				$this->vars[$vname] = $value;
			}	
		}	
	}
	function initial(){}
	
	function insert(){
		global $_using_free_play;
		db_connect('main');
		
		/*insert if is freeplay in some tables*/
		$free_play_tables = array("settle_log","craps_session","baccarat_session","slot_session","blackjack_session","video_poker_session","roulette_session","poker_session","keno_session");
		if(in_array($this ->table,$free_play_tables)){
			$this ->vars["free_play"] = $_using_free_play;
		}
		/*----------------------------------*/
		
		$this->vars['id'] = insert($this, $this->table);
	}
	
	
	
	function update($specific = NULL){
		if(!is_array($specific) && !is_null($specific)){$specific = explode(",",$specific);}
		db_connect('main');
		return update($this, $this->table, $specific);
	}
}

class _company extends _DB_ELEMENT{
	var $table = "company";
}

class _player extends _DB_ELEMENT{
	var $table = "player";
	
	function get_allowed_games($category = ""){
		global $_company;
		$games = get_all_company_games($_company ->vars["id"], true, $category);
		$custom_games = get_player_games_limits($this);
		$allowed_games = array();
		
		foreach($games as $game){
			if($custom_games["games"][$game ->vars["id"]]["active"]){
				$game ->vars["min_amount"] = $custom_games["games"][$game ->vars["id"]]["min"];
				$game ->vars["max_amount"] = $custom_games["games"][$game ->vars["id"]]["max"];
				$allowed_games[] = $game;
			}
		}
		
		return $allowed_games;
		
	}
	function get_allowed_game($gid){
		global $_company;
		$game = get_game_by_company($gid, $_company ->vars["id"],true);
		$custom_games = get_player_games_limits($this);
		$allowed_game = NULL;
		
		if($custom_games["games"][$game ->vars["id"]]["active"]){
			$game ->vars["min_amount"] = $custom_games["games"][$game ->vars["id"]]["min"];
			$game ->vars["max_amount"] = $custom_games["games"][$game ->vars["id"]]["max"];
			$allowed_game = $game;
		}
		
		return $allowed_game;
		
	}
	
	function in_between_limits($bet){
		$is = 0;
		$wl_limits = get_logged_player_limits();
		$days = get_fisrt_last_day_of_week(date("Y-m-d"));
		
		$day_settle = get_player_settle($this ->vars["id"], date("Y-m-d"), date("Y-m-d",strtotime(date("Y-m-d") . "+ 1 day")));
		$week_settle = get_player_settle($this ->vars["id"], $days["Monday"], date("Y-m-d",strtotime($days["Sunday"] . "+ 1 day")));
		
		if( ($day_settle["total"] - $bet) >= $wl_limits["day_max_loss"]*-1 && ($day_settle["total"] + $bet) <= $wl_limits["day_max_win"] && ($week_settle["total"] - $bet) >= $wl_limits["week_max_loss"]*-1 && ($week_settle["total"] + $bet) <= $wl_limits["week_max_win"] ){$is = 1;}

		return $is;
	}
	
	function in_between_limits2($bet){
		$is = 0;
		$wl_limits = get_logged_player_limits();
		$days = get_fisrt_last_day_of_week(date("Y-m-d"));
		
		$day_settle = get_player_settle($this ->vars["id"], date("Y-m-d"), date("Y-m-d",strtotime(date("Y-m-d") . "+ 1 day")));
		$week_settle = get_player_settle($this ->vars["id"], $days["Monday"], date("Y-m-d",strtotime($days["Sunday"] . "+ 1 day")));
		
		
		print_r($day_settle);
		
		if( ($day_settle["total"] - $bet) >= $wl_limits["day_max_loss"]*-1 && ($day_settle["total"] + $bet) <= $wl_limits["day_max_win"] && ($week_settle["total"] - $bet) >= $wl_limits["week_max_loss"]*-1 && ($week_settle["total"] + $bet) <= $wl_limits["week_max_win"] ){$is = 1;}

		return $is;
	}
	
	function add_free_plays($game, $amount, $coin_value, $lines){
		global $_using_free_play;
		
		for($i=0;$i<$amount;$i++){
			$fp = new _free_play();
			$fp ->vars["player"] = $this ->vars["id"];
			$fp ->vars["game"] = $game;
			$fp ->vars["amount"] = 1;
			$fp ->vars["settings"] = json_encode(array("coin_value"=>$coin_value,"lines"=>$lines));
			$fp ->vars["created_date"] = date("Y-m-d H:i:s");
			$fp ->vars["ufree_play"] = $_using_free_play;
			$fp->insert();
		}
		
		
	}
	
	function get_free_plays($game){
		global $_using_free_play;
		$fp = get_game_freeplays($game, $this ->vars["id"], $_using_free_play);
		return $fp["total"];	
	}
	
	//function burn_free_play($game){
//		$fp = get_player_freeplay($game, $this ->vars["id"]);
//		if(!is_null($fp) && $fp ->vars["amount"] > 0){
//			
//			$fp ->vars["amount"]--;
//			$fp ->update("amount");
//			
//		}else{
//			//ERROR TRYING TO USE FREE PLAY WHEN NOT AVAILABLE
//			header("HTTP/1.1 500 Internal Server Error");
//        	echo "No Free Play Available to use";
//			exit();
//		}
//	}
	
}

class _agent extends _DB_ELEMENT{
	var $table = "agent";
	function get_kids_ids($include_me = false){
		$kids = $this->get_all_kids($include_me);
		$list = array();
		foreach($kids as $kid){
			$list[] = $kid ->vars["id"]	;
		}
		return implode(",",$list);
	}
	function get_all_kids($include_me = false){
		$kids = array();
		if($include_me){$kids[] = $this;}
		
		$list = get_agent_kids($this ->vars["id"]);
		if(count($list)>0){
			
			foreach($list as $item){
				$list = array_merge($list,$item->get_all_kids());	
			}
			
			$kids = array_merge($kids,$list);
		}
		
		$sort_kids = array();
		foreach($kids as $kid){
			$sort_kids[$kid ->vars["account"]] = $kid;	
		}
		ksort($sort_kids);
		
		
		return $sort_kids;
		
	}
	function get_all_players(){
		$players = get_agent_players($this ->vars["id"]);
		$kids = get_agent_kids($this ->vars["id"]);
		
		foreach($kids as $kid){
			$players = array_merge($players,$kid->get_all_players());
		}
		
		$sort_players = array();
		foreach($players as $player){
			$sort_players[$player ->vars["account"]] = $player;	
		}
		ksort($sort_players);
		
		
		return $sort_players;
		
	}
}

class _game extends _DB_ELEMENT{
	var $table = "game";
}

class _bj_session extends _DB_ELEMENT{
	var $table = "blackjack_session";
	function get_hand_detail(){
		$str = "Player Hand: " . prepare_hand($this ->vars["player_hand"]) ." (".$this ->vars["game_status"].")<br />";
		if( $this ->vars["player_hand2"] != ""){$str .= "Player Hand 2: " . prepare_hand($this ->vars["player_hand2"]) ." (".$this ->vars["game_status2"].")<br />";}
		if($this ->vars["finished"]){$str .= "Dealer Hand: " . prepare_hand($this ->vars["dealer_hand"]) ."<br />";}
		if($this ->vars["insurance"]*1 > 0){$str .= "<strong>Insurance Paid: ".$this ->vars["insurance"]."</strong>";}
		if(!$this ->vars["finished"]){$str .= "<strong>Unfinished Game</strong>";}
		return $str;
	}
	
	function get_bet_amount_detail(){
		return $this ->vars["bet_amount"] + $this ->vars["bet_amount2"];	
	}
	
}

class _slot_session extends _DB_ELEMENT{
	var $table = "slot_session";
	function get_hand_detail(){
		$str = prepare_reel($this ->vars["reel"], $this ->vars["game"]);
		return $str;
	}
	function get_bet_amount_detail(){
		return $this ->vars["bet_amount"] + $this ->vars["bet_amount2"];	
	}
}

class _keno_session extends _DB_ELEMENT{
	var $table = "keno_session";
	function get_hand_detail(){
		$str = "<strong>Selection:</strong> ".$this ->vars["selection"]."<br />";
		$str .= "<strong>Result Numbers:</strong> ".$this ->vars["result"];
		return $str;
	}
	function get_bet_amount_detail(){
		return $this ->vars["bet_amount"] + $this ->vars["bet_amount2"];	
	}
}

class _roulette_session extends _DB_ELEMENT{
	var $table = "roulette_session";
	function get_hand_detail(){
		$str = "Bets: Area:" . str_replace(",",", Area:",str_replace("|"," Amount:",$this ->vars["bet_detail"]))."<br />";
		$str .= "Number: " . $this ->vars["number"];
		return $str;
	}
	function get_bet_amount_detail(){
		return $this ->vars["bet_amount"] + $this ->vars["bet_amount2"];	
	}
}

class _baccarat_session extends _DB_ELEMENT{
	var $table = "baccarat_session";
	function get_hand_detail(){
		$str = "Bets: " . str_replace("|"," ",$this ->vars["bet_detail"])."<br />";
		$str .= "Player Hand: " . prepare_hand($this ->vars["player_hand"]) ." (".$this ->vars["player_value"].")<br />";
		$str .= "Banker Hand: " . prepare_hand($this ->vars["banker_hand"]) ." (".$this ->vars["banker_value"].")<br />";
		$str .= strtoupper($this ->vars["winner_area"]) . " WINS";
		return $str;
	}
	function get_bet_amount_detail(){
		return $this ->vars["bet_amount"] + $this ->vars["bet_amount2"];	
	}
}

class _api_error_log extends _DB_ELEMENT{
	var $table = "api_error_log";
}

class _settle_log extends _DB_ELEMENT{
	var $table = "settle_log";
}

class _category extends _DB_ELEMENT{
	var $table = "category";
}

class _vp_session extends _DB_ELEMENT{
	var $table = "video_poker_session";
	function get_hand_detail(){
		
		$extra_hands = get_vp_extra_hands($this ->vars["id"]);
		
		if(count($extra_hands)){
			$str = "";
			foreach($extra_hands as $eh){
				if($str != ""){$str .= "<br />";}
				$str .= prepare_hand($eh ->vars["hand"]);	
			}			
		}else{
			$str = prepare_hand($this ->vars["hand"]);	
		}
		
		if(!$this ->vars["finished"]){$str .= "<br /><strong>Unfinished Game</strong>";}
		return $str;
	}
	
	function get_bet_amount_detail(){
		return $this ->vars["bet_amount"] + $this ->vars["bet_amount2"];	
	}
}

class _poker_session extends _DB_ELEMENT{
	var $table = "poker_session";
	function get_hand_detail(){
		
		switch($this ->vars["poker_type"]){ 
			case "holdem":
				$str = "Player: " . prepare_hand($this ->vars["player_hand"])."<br />";
				if($this ->vars["finished"]){$str .= "Dealer: " . prepare_hand($this ->vars["dealer_hand"])."<br />";}
				$str .= "Table: " . prepare_hand($this ->vars["table_cards"])."<br />";
				$str .= strtoupper($this ->vars["status"]);
			break;
			case "caribbean":
				$str = "Player: " . prepare_hand($this ->vars["player_hand"])."<br />";
				if($this ->vars["finished"]){$str .= "Dealer: " . prepare_hand($this ->vars["dealer_hand"])."<br />";}
				$str .= strtoupper($this ->vars["status"]);
			break;
			case "three_card":
				$str = "Player: " . prepare_hand($this ->vars["player_hand"])."<br />";
				if($this ->vars["finished"]){$str .= "Dealer: " . prepare_hand($this ->vars["dealer_hand"])."<br />";}
				$str .= strtoupper($this ->vars["status"]);
			break;
		}
		
		
		
		if(!$this ->vars["finished"]){$str .= "<br /><strong>Unfinished Game</strong>";}
		return $str;
	}
	function get_bet_amount_detail(){
		$detail = "";
		if($this ->vars["poker_type"] == "three_card"){
			$detail = "Ante: " . $this ->vars["ante_bet"];
			$detail .= "<br />Pair+: " . $this ->vars["turn_bet"];
			$detail .= "<br />Call: " . $this ->vars["call_bet"];
			$detail .= "<br />TOTAL: " . $this ->vars["bet_amount"];
		}else{
			$detail = $this ->vars["bet_amount"] + $this ->vars["bet_amount2"];
		}
		
		return $detail;	
	}
}

class _craps_session extends _DB_ELEMENT{
	var $table = "craps_session";
	function get_hand_detail(){
		return $str;
	}
	
	function get_bet_amount_detail(){
		return $this ->vars["bet_amount"] + $this ->vars["bet_amount2"];	
	}
}

class _craps_roll extends _DB_ELEMENT{
	var $table = "craps_roll";
	function get_hand_detail(){
		$str = "Dices : " . $this ->vars["roll1"] . ", " . $this ->vars["roll2"];
		if($this ->vars["finishing_bets"] != ""){
			$str .= "<br />Ending Bets: " . $this ->vars["finishing_bets"];	
		}
		return $str;
	}
	
	function get_bet_amount_detail(){
		return $this ->vars["bet_amount"] + $this ->vars["bet_amount2"];	
	}
}

class _free_play extends _DB_ELEMENT{
	var $table = "free_play";
	var $settings = array();
	function initial(){
		$this ->settings = json_decode($this ->vars["settings"],true);
	}
}


class _contest extends _DB_ELEMENT{
	var $table = "contest";
	var $settings = array();
}

class _contest_team extends _DB_ELEMENT{
	var $table = "contest_teams";
	var $settings = array();
}

class _contest_record extends _DB_ELEMENT{
	var $table = "contest_team_by_player";
	var $settings = array();
}

class _vp_extra_hand extends _DB_ELEMENT{
	var $table = "video_poker_extra_hand";
	var $settings = array();
}

class _transactions extends _DB_ELEMENT{
	var $table = "transactions";
}

class _game_by_person extends _DB_ELEMENT{
	var $table = "game_by_person";
}




?>