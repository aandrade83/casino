<?php
require_once($_SERVER['DOCUMENT_ROOT'] . "/control/modules/access/api_local.php");

function wallet_balance($player_id){
    $api = new _local_api();
    $balance = $api->get_player_balance($player_id);
    if(!$api->done){
        return ["success" => false, "error" => $api->error_msg];
    }
    return ["success" => true, "data" => $balance];
}

function wallet_bet($player_id, $amount, $type = "bet", $game_id = null, $round_id = null){
    $api = new _local_api();
    $result = $api->place_bet($player_id, $amount);
    if(!$api->done){
        return ["success" => false, "error" => $api->error_msg];
    }
    record_transaction($player_id, $type, $amount, $result["balance_before"], $result["real"], $game_id, $round_id);
    return ["success" => true, "balance" => $result["real"]];
}

function wallet_win($player_id, $amount, $type = "win", $game_id = null, $round_id = null){
    $api = new _local_api();
    $result = $api->credit_prize($player_id, $amount);
    if(!$api->done){
        return ["success" => false, "error" => $api->error_msg];
    }
    record_transaction($player_id, $type, $amount, $result["balance_before"], $result["real"], $game_id, $round_id);
    return ["success" => true, "balance" => $result["real"]];
}

function record_transaction($player_id, $type, $amount, $balance_before, $balance_after, $game_id = null, $round_id = null, $reference = null){
    $trans = new _transactions();
    $trans->vars['player_id']      = intval($player_id);
    $trans->vars['type']           = $type;
    $trans->vars['amount']         = round(floatval($amount), 2);
    $trans->vars['balance_before'] = round(floatval($balance_before), 2);
    $trans->vars['balance_after']  = round(floatval($balance_after), 2);
    $trans->vars['game']           = $game_id ? intval($game_id) : null;
    $trans->vars['round_id']       = $round_id ? intval($round_id) : null;
    $trans->vars['reference']      = $reference;
    $trans->insert();
}
