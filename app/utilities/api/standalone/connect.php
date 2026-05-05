<?php

class _api_connection {

    public $done = true;
    public $error_msg = "";

    private $currency = "USD";
    private $provider = 3;

    private function reset_error() {
        $this->done = true;
        $this->error_msg = "";
    }

    private function getPlayer() {

        if(!isset($_SESSION["player"])){
            $this->done = false;
            $this->error_msg = "No session player";
            return null;
        }

        return get_player($_SESSION["player"]);
    }

    public function get_player_balance($token) {

    $this->reset_error();

    $player = $this->getPlayer();

    if(!$player){
        return [];
    }

    $balance_real = floatval($player->vars["balance_real"]);
    $balance_free = floatval($player->vars["balance_free"]);
    $using_free_play = intval($player->vars["using_free_play"] ?? 0) === 1;

    return [
        // amount es el balance ACTIVO que usa el juego
        "amount" => $using_free_play ? $balance_free : $balance_real,

        // estos dos siguen disponibles para UI/debug
        "free" => $balance_free,
        "real" => $balance_real,
        "currency" => $this->currency
    ];
}

public function place_bet($token, $amount, $game_name, $game_id) {

    $this->reset_error();

    $player = $this->getPlayer();

    if(!$player){
        return [];
    }

    $amount = round(floatval($amount), 2);

    if($amount <= 0){
        $this->done = false;
        $this->error_msg = "Invalid bet amount";
        return [];
    }

    // 🔥 decidir wallet activo
    $using_free_play = intval($player->vars["using_free_play"] ?? 0) === 1;
    $wallet_field = $using_free_play ? "balance_free" : "balance_real";

    $balance_before = round(floatval($player->vars[$wallet_field]), 2);

    if($amount > $balance_before){
        $this->done = false;
        $this->error_msg = "Insufficient funds";
        return [];
    }

    $balance_after = round($balance_before - $amount, 2);

    // 🔥 actualizar wallet correcto
    $player->vars[$wallet_field] = $balance_after;
    $player->update([$wallet_field]);

    // 🔥 tipo de transacción (opcional pero recomendado)
    $type = $using_free_play ? "bet_free" : "bet";

    record_transaction(
        $player->vars["id"],
        $type,
        $amount,
        $balance_before,
        $balance_after,
        $game_id,
        null,
        null,
        $this->currency,
        $this->provider
    );

    return [
        // 🔥 balance activo (el que usa el juego)
        "balance" => $balance_after,

        // 🔥 siempre devolver ambos
        "free" => floatval($player->vars["balance_free"]),
        "real" => floatval($player->vars["balance_real"]),

        "currency" => $this->currency
    ];
}
    
public function credit_prize($token, $amount, $game_name, $game_id) {

    $this->reset_error();

    $player = $this->getPlayer();

    if(!$player){
        return [];
    }

    $amount = round(floatval($amount), 2);

    // 🔥 decidir wallet activo
    $using_free_play = intval($player->vars["using_free_play"] ?? 0) === 1;
    $wallet_field = $using_free_play ? "balance_free" : "balance_real";

    // 🔥 si no hay premio, solo devolver estado actual
    if($amount <= 0){
        return [
            "balance" => floatval($player->vars[$wallet_field]),
            "free" => floatval($player->vars["balance_free"]),
            "real" => floatval($player->vars["balance_real"]),
            "currency" => $this->currency
        ];
    }

    $balance_before = round(floatval($player->vars[$wallet_field]), 2);
    $balance_after  = round($balance_before + $amount, 2);

    // 🔥 actualizar wallet correcto
    $player->vars[$wallet_field] = $balance_after;
    $player->update([$wallet_field]);

    // 🔥 tipo de transacción
    $type = $using_free_play ? "win_free" : "win";

    record_transaction(
        $player->vars["id"],
        $type,
        $amount,
        $balance_before,
        $balance_after,
        $game_id,
        null,
        null,
        $this->currency,
        $this->provider
    );

    return [
        // 🔥 balance activo
        "balance" => $balance_after,

        // 🔥 ambos visibles
        "free" => floatval($player->vars["balance_free"]),
        "real" => floatval($player->vars["balance_real"]),

        "currency" => $this->currency
    ];
}

    /*
    public function get_player_balance($token) {

        $this->reset_error();

        $player = $this->getPlayer();

        if(!$player){
            return [];
        }

        $balance_real = floatval($player->vars["balance_real"]);
        $balance_free = floatval($player->vars["balance_free"]);
        $balance_total = $balance_real + $balance_free;

        return [
            "amount" => $balance_total,
            "free" => $balance_free,
            "real" => $balance_real,
            "currency" => $this->currency
        ];
    }
   */
  /*
    public function place_bet($token, $amount, $game_name, $game_id) {

        $this->reset_error();

        $player = $this->getPlayer();

        if(!$player){
            return [];
        }

        $amount = round(floatval($amount), 2);

        if($amount <= 0){
            $this->done = false;
            $this->error_msg = "Invalid bet amount";
            return [];
        }

        $balance_before = round(floatval($player->vars["balance_real"]), 2);

        if($amount > $balance_before){
            $this->done = false;
            $this->error_msg = "Insufficient funds";
            return [];
        }

        $balance_after = round($balance_before - $amount, 2);

        $player->vars["balance_real"] = $balance_after;
        $player->update(["balance_real"]);

        record_transaction(
            $player->vars["id"],
            "bet",
            $amount,
            $balance_before,
            $balance_after,
            $game_id,
            null,
            null,
            $this->currency,
            $this->provider
        );

        return [
            "balance" => $balance_after,
            "free" => floatval($player->vars["balance_free"]),
            "real" => $balance_after,
            "currency" => $this->currency
        ];
    }
    */

    /*
    public function credit_prize($token, $amount, $game_name, $game_id) {

        $this->reset_error();

        $player = $this->getPlayer();

        if(!$player){
            return [];
        }

        $amount = round(floatval($amount), 2);

        if($amount <= 0){
            $this->done = true;

            return [
                "balance" => floatval($player->vars["balance_real"]),
                "free" => floatval($player->vars["balance_free"]),
                "real" => floatval($player->vars["balance_real"]),
                "currency" => $this->currency
            ];
        }

        $balance_before = round(floatval($player->vars["balance_real"]), 2);
        $balance_after = round($balance_before + $amount, 2);

        $player->vars["balance_real"] = $balance_after;
        $player->update(["balance_real"]);

        record_transaction(
            $player->vars["id"],
            "win",
            $amount,
            $balance_before,
            $balance_after,
            $game_id,
            null,
            null,
            $this->currency,
            $this->provider
        );

        return [
            "balance" => $balance_after,
            "free" => floatval($player->vars["balance_free"]),
            "real" => $balance_after,
            "currency" => $this->currency
        ];
    }
*/
    public function get_player_data($token){

    $player = $this->getPlayer();

    if(!$player){
        $this->done = false;
        $this->error_msg = "Player not found";
        return [];
    }

    return [
        "account" => strtoupper($player->vars["account"]),
        "player_id" => $player->vars["id"],

        // 🔥 simulamos estructura DGS
        "agent" => "",
        "agents_list" => [],

        // opcional
        "balance" => $player->vars["balance_real"]
    ];
}

private function getWalletField($player){
    return intval($player->vars["using_free_play"] ?? 0) === 1
        ? "balance_free"
        : "balance_real";
}


}