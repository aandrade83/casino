<?php

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

/**
 * ---------------------------------------------------------
 * GENERATE JWT TOKEN
 * ---------------------------------------------------------
 */

function generate_jwt_token(
    $company_id,
    $player_id,
    $username,
    $agent_id = 0,
    $agent_account = ""
){

    $config = require($_SERVER['DOCUMENT_ROOT'] . '/config.php');

    $secret = $config["jwt"]["secret"];
    $issuer = $config["jwt"]["issuer"];
    $expiration = $config["jwt"]["expiration"];

    // currency is owned by the casino (company.currency) and is NOT included in the JWT
    $payload = [
        "iss" => $issuer,
        "iat" => time(),
        "exp" => time() + $expiration,

        "cid" => $company_id,
        "player_id" => $player_id,
        "username" => $username,

        "agent_id" => $agent_id,
        "agent_account" => $agent_account
    ];

    return JWT::encode($payload, $secret, 'HS256');
}

/**
 * ---------------------------------------------------------
 * VALIDATE JWT TOKEN
 * ---------------------------------------------------------
 */

function validate_jwt_token($token){

    try{

        $config = require($_SERVER['DOCUMENT_ROOT'] . '/config.php');

        $secret = $config["jwt"]["secret"];

        return JWT::decode($token, new Key($secret, 'HS256'));

    }catch(Exception $e){

        return false;
    }
}

/**
 * ---------------------------------------------------------
 * DETECT JWT FORMAT
 * ---------------------------------------------------------
 */

function is_jwt_token($token){

    return is_string($token)
        && substr_count($token, '.') === 2;
}