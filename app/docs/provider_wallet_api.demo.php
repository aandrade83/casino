<?php

/**
 * =========================================================
 * PROVIDER WALLET API — SANITIZED REFERENCE
 * =========================================================
 *
 * This is the endpoint the ZYTOM Casino will POST to in order
 * to query balances, debit bets, and credit prizes.
 *
 * You — the provider — host this file at an HTTPS URL and
 * share that URL with the casino during onboarding.
 *
 * This file is NON-FUNCTIONAL as shipped — the placeholders
 * marked "REQUEST FROM CASINO PROVIDER" must be replaced,
 * and the demo wallet / demo JWT decoding MUST be replaced
 * with your real database lookups and real JWT validation.
 *
 * Wire format:
 *  - HTTP POST, application/x-www-form-urlencoded
 *  - Response: JSON envelope { error, msg, balance, free,
 *    currency, account }
 *
 * Supported actions: get_balance, place_bet, place_free_bet,
 * credit_prize.
 *
 * Required PHP library (for real JWT validation):
 *   composer require firebase/php-jwt
 *
 * =========================================================
 */

header('Content-Type: application/json');

/**
 * =========================================================
 * PROVIDER CONFIGURATION
 * =========================================================
 *
 * REQUEST FROM CASINO PROVIDER:
 *  - JWT shared secret (HS256)
 *  - cid assigned to you
 *  - currency agreed for your cid (NOT carried by the JWT)
 * =========================================================
 */

$shared_secret = "<REQUEST FROM CASINO PROVIDER: JWT shared secret>";

$provider_currency_by_cid = [
    // <cid> => "<CURRENCY>"  e.g. 12 => "USD"
    // Multiple cids supported if you serve more than one casino integration.
];

/**
 * =========================================================
 * REQUEST DATA
 * =========================================================
 */

$action = $_POST["action"] ?? "";
$token  = $_POST["token"]  ?? "";
$wallet = $_POST["wallet"] ?? "real";

/**
 * =========================================================
 * TOKEN VALIDATION
 * =========================================================
 *
 * IMPORTANT: replace the demo block below with REAL
 * HS256 verification using $shared_secret.
 *
 * Example (firebase/php-jwt):
 *
 *   use Firebase\JWT\JWT;
 *   use Firebase\JWT\Key;
 *   $jwt_payload = (array) JWT::decode(
 *       $token,
 *       new Key($shared_secret, 'HS256')
 *   );
 *
 * On any decoding failure, return error 100.
 * =========================================================
 */

if($token == ""){
    echo json_encode([
        "error" => 100,
        "msg"   => "Missing token"
    ]);
    exit;
}

// ---- DEMO PAYLOAD — REPLACE WITH REAL JWT DECODE ----
$jwt_payload = [
    "cid"       => 0,
    "player_id" => 0,
    "username"  => ""
];
// -----------------------------------------------------

/**
 * =========================================================
 * VALIDATE COMPANY
 * =========================================================
 */

if(!isset($provider_currency_by_cid[$jwt_payload["cid"]])){
    echo json_encode([
        "error" => 101,
        "msg"   => "Invalid company id"
    ]);
    exit;
}

/**
 * =========================================================
 * CURRENCY RESOLUTION
 * =========================================================
 *
 * Currency is owned by the casino (company.currency) and
 * agreed per cid at onboarding. It is NOT carried by the
 * JWT and NOT accepted from the request.
 * =========================================================
 */

$currency = $provider_currency_by_cid[$jwt_payload["cid"]];

/**
 * =========================================================
 * PLAYER DATA
 * =========================================================
 */

$player_id = $jwt_payload["player_id"];
$username  = $jwt_payload["username"];

/**
 * =========================================================
 * WALLET BALANCES
 * =========================================================
 *
 * IMPORTANT: replace these placeholders with REAL DB reads
 * for the player identified by ($jwt_payload["cid"],
 * $player_id).
 * =========================================================
 */

$starting_balance      = 0.00; // real wallet
$starting_free_balance = 0.00; // freeplay wallet

/**
 * =========================================================
 * ACTION: get_balance
 * =========================================================
 */

if($action == "get_balance"){
    echo json_encode([
        "error"    => 0,
        "balance"  => $starting_balance,
        "free"     => $starting_free_balance,
        "currency" => $currency,
        "account"  => $username
    ]);
    exit;
}

/**
 * =========================================================
 * ACTION: place_bet — debit real wallet
 * =========================================================
 */

if($action == "place_bet"){

    $bet_amount = floatval($_POST["bet_amount"] ?? 0);

    if($bet_amount <= 0){
        echo json_encode([ "error" => 201, "msg" => "Invalid bet amount" ]);
        exit;
    }

    $new_balance = $starting_balance - $bet_amount;

    if($new_balance < 0){
        echo json_encode([ "error" => 202, "msg" => "Insufficient balance" ]);
        exit;
    }

    // RECOMMENDED: persist transaction (tx_id, player, game_id,
    // amount, balance_before, balance_after, timestamp).

    echo json_encode([
        "error"    => 0,
        "balance"  => round($new_balance, 2),
        "free"     => $starting_free_balance,
        "currency" => $currency,
        "account"  => $username
    ]);
    exit;
}

/**
 * =========================================================
 * ACTION: place_free_bet — debit freeplay wallet
 * =========================================================
 */

if($action == "place_free_bet"){

    $bet_amount = floatval($_POST["bet_amount"] ?? 0);

    if($bet_amount <= 0){
        echo json_encode([ "error" => 211, "msg" => "Invalid freeplay bet amount" ]);
        exit;
    }

    $new_free_balance = $starting_free_balance - $bet_amount;

    if($new_free_balance < 0){
        echo json_encode([ "error" => 212, "msg" => "Insufficient freeplay balance" ]);
        exit;
    }

    // RECOMMENDED: persist freeplay transaction.

    echo json_encode([
        "error"    => 0,
        "balance"  => $starting_balance,
        "free"     => round($new_free_balance, 2),
        "currency" => $currency,
        "account"  => $username
    ]);
    exit;
}

/**
 * =========================================================
 * ACTION: credit_prize
 * =========================================================
 *
 * The casino sends `wallet` = "real" | "free" to indicate
 * which wallet should receive the credit.
 * =========================================================
 */

if($action == "credit_prize"){

    $win_amount = floatval($_POST["win_amount"] ?? 0);

    if($win_amount < 0){
        echo json_encode([ "error" => 301, "msg" => "Invalid win amount" ]);
        exit;
    }

    if($wallet == "real"){

        $new_balance = $starting_balance + $win_amount;

        echo json_encode([
            "error"    => 0,
            "balance"  => round($new_balance, 2),
            "free"     => $starting_free_balance,
            "currency" => $currency,
            "account"  => $username
        ]);
        exit;
    }

    // wallet == "free"
    $new_free_balance = $starting_free_balance + $win_amount;

    echo json_encode([
        "error"    => 0,
        "balance"  => $starting_balance,
        "free"     => round($new_free_balance, 2),
        "currency" => $currency,
        "account"  => $username
    ]);
    exit;
}

/**
 * =========================================================
 * UNKNOWN ACTION
 * =========================================================
 */

echo json_encode([
    "error" => 999,
    "msg"   => "Invalid action"
]);
