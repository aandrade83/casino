<?
class _api_connection{
	var $user = "HTML5_Casino";
	var $password = "K34JGSDKJ22S689DD";
	var $api_url = "http://www.sportsbettingonline.ag/utilities/api/external_casino/";
	var $done = true;
	var $error_msg = "";
	function reset_error(){
		$this->done = true;
		$this->error_msg = "";
	}
	function get_player_balance($token){
		global $_using_free_play;
		$this->reset_error();
		$data = array();
		$data["user"] = $this->user;
		$data["password"] = $this->password;
		$data["token"] = $token;
		$data["action"] = "get_balance";
		$result = json_decode(do_post_request($this->api_url, $data));
		if($result->error == "0"){
			
			if($_using_free_play){$balance["amount"] = $result->free*1;}else{$balance["amount"] = $result->balance*1;}
			
			$balance["free"] = $result->free*1;	
			$balance["real"] = $result->balance*1;	
			$balance["currency"] = $result->currency;	
			if(!is_numeric($balance["amount"])){$balance["amount"] = 0;}
		}else{
			$this->done = false;
			$this->error_msg = $result->msg;
		}
		return $balance;
	}
	function get_player_data($token){
		$info = array();
		$this->reset_error();
		$data = array();
		$data["user"] = $this->user;
		$data["password"] = $this->password;
		$data["token"] = $token;
		$data["action"] = "get_info";
		$result = json_decode(do_post_request($this->api_url, $data));
		
		if($result->error == "0"){
			$info["player_id"] = $result->player_id;
			$info["agent"] = $result->agent;
			$info["account"] = $result->account;
			$info["agents_list"] = (array) $result->agents_list;	
		}else{
			$this->done = false;
			$this->error_msg = $result->msg;
		}
		return $info;
	}
	function place_bet($token, $amount, $game_name, $game_id){
		global $_using_free_play;
		$this->reset_error();
		$data = array();
		$data["user"] = $this->user;
		$data["password"] = $this->password;
		$data["token"] = $token;
		
		if($_using_free_play){$data["action"] = "place_free_bet";}else{$data["action"] = "place_bet";}
		
		$data["bet_amount"] = $amount;
		$data["game_name"] = $game_name;
		$data["game_id"] = $game_id;
		$result = json_decode(do_post_request($this->api_url, $data));
		
		if($result->error == "0"){
			if($_using_free_play){$balance["balance"] = $result->free*1;}else{$balance["balance"] = $result->balance*1;}
			$balance["free"] = $result->free*1;	
			$balance["real"] = $result->balance*1;
			$balance["currency"] = $result->currency;	
			if(!is_numeric($balance["balance"])){$balance["balance"] = 0;}
		}else{
			$this->done = false;
			$this->error_msg = $result->msg;
		}
		
		return $balance;
	}
	function credit_prize($token, $amount, $game_name, $game_id){
		global $_using_free_play;
		$this->reset_error();
		$data = array();
		$data["user"] = $this->user;
		$data["password"] = $this->password;
		$data["token"] = $token;
		$data["action"] = "credit_prize";
		$data["win_amount"] = $amount;
		$data["game_name"] = $game_name;
		$data["game_id"] = $game_id;
		$result = json_decode(do_post_request($this->api_url, $data));
		if($result->error == "0"){
			if($_using_free_play){$balance["balance"] = $result->free*1;}else{$balance["balance"] = $result->balance*1;}
			$balance["free"] = $result->free*1;
			$balance["real"] = $result->balance*1;	
			$balance["currency"] = $result->currency;	
			if(!is_numeric($balance["balance"])){$balance["balance"] = 0;}
		}else{
			$this->done = false;
			$this->error_msg = $result->msg;
		}
		return $balance;
	}
}	
?>