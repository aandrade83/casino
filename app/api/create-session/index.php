<?php

header('Content-Type: application/json');

include($_SERVER['DOCUMENT_ROOT'] . "/utilities/includes.php");

/**
 * ---------------------------------------------------------
 * READ JSON BODY
 * ---------------------------------------------------------
 */

$input = json_decode(file_get_contents("php://input"), true);

if(!$input){

    json_ctrl([
        "success" => false,
        "msg" => "Invalid JSON body"
    ]);
}

/**
 * ---------------------------------------------------------
 * API KEY VALIDATION
 * ---------------------------------------------------------
 */

$api_key = $_SERVER['HTTP_X_API_KEY'] ?? '';

if($api_key == ''){

    json_ctrl([
        "success" => false,
        "msg" => "Missing API key"
    ]);
}

/**
 * ---------------------------------------------------------
 * LOAD COMPANY
 * ---------------------------------------------------------
 */

$_company = get_company_by_api_key($api_key);

if(is_null($_company)){

    json_ctrl([
        "success" => false,
        "msg" => "Invalid API key"
    ]);
}

/**
 * ---------------------------------------------------------
 * PLAYER DATA
 * ---------------------------------------------------------
 */

$player_id = $input["player_id"] ?? 0;
$username  = strtoupper(trim($input["username"] ?? ""));

$currency = $_company->vars['currency'];

$agent_id = $input["agent_id"] ?? 0;
$agent_account = strtoupper(trim($input["agent_account"] ?? ""));

if(!$player_id || $username == ''){

    json_ctrl([
        "success" => false,
        "msg" => "Missing player information"
    ]);
}

/**
 * ---------------------------------------------------------
 * VALIDATE AGENT DATA
 * ---------------------------------------------------------
 */

if($agent_id && !is_numeric($agent_id)){

    json_ctrl([
        "success" => false,
        "msg" => "Invalid agent_id"
    ]);
}

/**
 * ---------------------------------------------------------
 * GENERATE JWT
 * ---------------------------------------------------------
 */

$token = generate_jwt_token(
    $_company->vars['id'],
    $player_id,
    $username,
    $agent_id,
    $agent_account
);

/**
 * ---------------------------------------------------------
 * STORE TOKEN SESSION
 * ---------------------------------------------------------
 */

$config = require($_SERVER['DOCUMENT_ROOT'] . '/config.php');

$expiration_seconds = $config["jwt"]["expiration"];

$expires_at = date(
    "Y-m-d H:i:s",
    time() + $expiration_seconds
);

$token_session = new _token_session();

$token_session->vars['company_id'] = $_company->vars['id'];
$token_session->vars['player_id'] = $player_id;
$token_session->vars['username'] = $username;
$token_session->vars['token'] = $token;

$token_session->vars['agent_id'] = $agent_id;
$token_session->vars['agent_account'] = $agent_account;

$token_session->vars['ip_address'] = $_SERVER['REMOTE_ADDR'] ?? '';

$token_session->vars['user_agent'] =
    substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);

$token_session->vars['expires_at'] = $expires_at;

$token_session->insert();

/**
 * ---------------------------------------------------------
 * RESPONSE
 * ---------------------------------------------------------
 */

json_ctrl([
    "success" => true,
    "token" => $token,

    "company_id" => $_company->vars['id'],
    "username" => $username,
    "currency" => $currency,

    "expires_at" => $expires_at
]);