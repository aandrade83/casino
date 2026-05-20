<?php

class _api_mock {

    var $done      = true;
    var $error_msg = "";

    private function _balance() {
        if (!isset($_SESSION['mock_balance'])) $_SESSION['mock_balance'] = 10000.00;
        return (float) $_SESSION['mock_balance'];
    }

    private function _free() {
        if (!isset($_SESSION['mock_free'])) $_SESSION['mock_free'] = 500.00;
        return (float) $_SESSION['mock_free'];
    }

    public function get_player_balance($token) {
        return [
            "amount"   => $this->_balance(),
            "free"     => $this->_free(),
            "real"     => $this->_balance(),
            "currency" => "USD",
        ];
    }

    public function place_bet($token, $amount, $game_name = "", $game_id = "") {
        $_SESSION['mock_balance'] = max(0, $this->_balance() - $amount);
        return [
            "balance"  => $this->_balance(),
            "free"     => $this->_free(),
            "real"     => $this->_balance(),
            "currency" => "USD",
        ];
    }

    public function place_free_bet($token, $amount, $game_name = "", $game_id = "") {
        $_SESSION['mock_free'] = max(0, $this->_free() - $amount);
        return [
            "balance"  => $this->_balance(),
            "free"     => $this->_free(),
            "real"     => $this->_balance(),
            "currency" => "USD",
        ];
    }

    public function credit_prize($token, $amount, $game_name = "", $game_id = "") {
        $_SESSION['mock_balance'] = $this->_balance() + $amount;
        return [
            "balance"  => $this->_balance(),
            "free"     => $this->_free(),
            "real"     => $this->_balance(),
            "currency" => "USD",
        ];
    }
}
