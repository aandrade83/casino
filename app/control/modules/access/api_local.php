<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/utilities/includes.php");

class _local_api {

    public $done = false;
    public $error_msg = "";

    function get_player_balance($player_id){

        $player = get_player($player_id);

        if(!$player){
            $this->done = false;
            $this->error_msg = "Player not found";
            return [];
        }

        $this->done = true;

        return [
            "real" => floatval($player->vars["balance_real"]),
            "free" => floatval($player->vars["balance_free"]),
            "currency" => "USD"
        ];
    }

    function place_bet($player_id, $amount){

        $amount = floatval($amount);

        $player = get_player($player_id);

        if(!$player){
            $this->done = false;
            $this->error_msg = "Player not found";
            return [];
        }

        if($player->vars["balance_real"] < $amount){
            $this->done = false;
            $this->error_msg = "Insufficient funds";
            return [];
        }

        $new_balance = floatval($player->vars["balance_real"]) - $amount;

        if($new_balance < 0){
            $this->done = false;
            $this->error_msg = "Balance error";
            return [];
        }

        $old_balance = floatval($player->vars["balance_real"]);
        $player->vars["balance_real"] = $new_balance;
        $player->update(["balance_real"]);

        $this->done = true;

        return [
            "real"           => $new_balance,
            "free"           => floatval($player->vars["balance_free"]),
            "currency"       => "USD",
            "balance_before" => $old_balance,
        ];
    }

    function credit_prize($player_id, $amount){

        $amount = floatval($amount);

        $player = get_player($player_id);

        if(!$player){
            $this->done = false;
            $this->error_msg = "Player not found";
            return [];
        }

        $old_balance = floatval($player->vars["balance_real"]);
        $new_balance = $old_balance + $amount;

        $player->vars["balance_real"] = $new_balance;
        $player->update(["balance_real"]);

        $this->done = true;

        return [
            "real"           => $new_balance,
            "free"           => floatval($player->vars["balance_free"]),
            "currency"       => "USD",
            "balance_before" => $old_balance,
        ];
    }
}