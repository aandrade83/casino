<?php

class _api_connection {

    public $done = true;
    public $error_msg = "";

    private $balance = 1000; // balance fake

    public function get_player_balance($token) {

        $this->done = true;

        return [
            "amount" => $this->balance,
            "free" => 0,
            "real" => $this->balance,
            "currency" => "USD"
        ];
    }

    public function place_bet($token, $amount, $game_name, $game_id) {

        if($amount > $this->balance){
            $this->done = false;
            $this->error_msg = "Insufficient funds";
            return [];
        }

        $this->balance -= $amount;

        $this->done = true;

        return [
            "balance" => $this->balance,
            "free" => 0,
            "real" => $this->balance,
            "currency" => "USD"
        ];
    }

    public function credit_prize($token, $amount, $game_name, $game_id) {

        $this->balance += $amount;

        $this->done = true;

        return [
            "balance" => $this->balance,
            "free" => 0,
            "real" => $this->balance,
            "currency" => "USD"
        ];
    }

}