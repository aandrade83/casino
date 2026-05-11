<?
class _api_connection{

    /**
     * ---------------------------------------------------------
     * Provider Wallet API URL
     * ---------------------------------------------------------
     * This endpoint belongs to the sportsbook/provider side.
     *
     * Example:
     * https://provider.com/provider_wallet_api.php
     *
     * For local testing:
     * /provider_wallet_api.php
     * ---------------------------------------------------------
     */
    
    //var $api_url = CASINO_BASE_URL . "/provider_wallet_api.php";
    var $api_url =
    "http://host.docker.internal:8080/provider_wallet_api.php";


    var $done = true;
    var $error_msg = "";

    /**
     * ---------------------------------------------------------
     * Reset API status
     * ---------------------------------------------------------
     */
    function reset_error(){

        $this->done = true;
        $this->error_msg = "";
    }

    /**
     * ---------------------------------------------------------
     * Get Player Balance
     * ---------------------------------------------------------
     * Returns:
     * - real balance
     * - free balance
     * - current currency
     * ---------------------------------------------------------
     */
    function get_player_balance($token){

        global $_using_free_play;

        $this->reset_error();

        $balance = array();
        $data    = array();

        $data["token"]  = $token;
        $data["action"] = "get_balance";

        $result = json_decode(
            do_post_request($this->api_url, $data)
        );

        if($result->error == "0"){

            if($_using_free_play){
                $balance["amount"] = $result->free * 1;
            }else{
                $balance["amount"] = $result->balance * 1;
            }

            $balance["free"] = $result->free * 1;
            $balance["real"] = $result->balance * 1;
            $balance["currency"] = $result->currency;

            if(!is_numeric($balance["amount"])){
                $balance["amount"] = 0;
            }

        }else{

            $this->done = false;
            $this->error_msg = $result->msg;
        }

        return $balance;
    }

    /**
     * ---------------------------------------------------------
     * Place Bet
     * ---------------------------------------------------------
     * Withdraws balance from the provider wallet.
     *
     * Recommended:
     * Provider should save transaction logs in database.
     * ---------------------------------------------------------
     */
    function place_bet($token, $amount, $game_name, $game_id){

        global $_using_free_play;

        $this->reset_error();

        $balance = array();
        $data    = array();

        $data["token"] = $token;

        if($_using_free_play){
            $data["action"] = "place_free_bet";
        }else{
            $data["action"] = "place_bet";
        }

        $data["bet_amount"] = $amount;
        $data["game_name"] = $game_name;
        $data["game_id"] = $game_id;

        $result = json_decode(
            do_post_request($this->api_url, $data)
        );

        if($result->error == "0"){

            if($_using_free_play){
                $balance["balance"] = $result->free * 1;
            }else{
                $balance["balance"] = $result->balance * 1;
            }

            $balance["free"] = $result->free * 1;
            $balance["real"] = $result->balance * 1;
            $balance["currency"] = $result->currency;

            if(!is_numeric($balance["balance"])){
                $balance["balance"] = 0;
            }

        }else{

            $this->done = false;
            $this->error_msg = $result->msg;
        }

        return $balance;
    }

    /**
     * ---------------------------------------------------------
     * Credit Prize
     * ---------------------------------------------------------
     * Adds winnings to the provider wallet.
     *
     * Recommended:
     * Provider should save transaction logs in database.
     * ---------------------------------------------------------
     */
    function credit_prize($token, $amount, $game_name, $game_id){

        global $_using_free_play;

        $this->reset_error();

        $balance = array();
        $data    = array();

        $data["token"]  = $token;
        $data["action"] = "credit_prize";
        $data["wallet"] = $_using_free_play ? "free" : "real";
        $data["win_amount"] = $amount;
        $data["game_name"] = $game_name;
        $data["game_id"] = $game_id;

        $result = json_decode(
            do_post_request($this->api_url, $data)
        );

        if($result->error == "0"){

            if($_using_free_play){
                $balance["balance"] = $result->free * 1;
            }else{
                $balance["balance"] = $result->balance * 1;
            }

            $balance["free"] = $result->free * 1;
            $balance["real"] = $result->balance * 1;
            $balance["currency"] = $result->currency;

            if(!is_numeric($balance["balance"])){
                $balance["balance"] = 0;
            }

        }else{

            $this->done = false;
            $this->error_msg = $result->msg;
        }

        return $balance;
    }
}
?>