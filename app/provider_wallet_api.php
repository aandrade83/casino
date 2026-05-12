<?php

/**
 * =========================================================
 * PROVIDER WALLET API - REFERENCE IMPLEMENTATION
 * =========================================================
 *
 * PURPOSE
 * ---------------------------------------------------------
 * Reference wallet API implementation for third party
 * sportsbook / wallet providers integrating with
 * the VRB Casino platform.
 *
 * IMPORTANT:
 * ---------------------------------------------------------
 * This file is ONLY a demo implementation.
 *
 * Replace ALL fake balances and fake wallet logic with:
 *
 * - real database balances
 * - real transactions
 * - real wallet persistence
 * - real transaction logging
 *
 * =========================================================
 */

header('Content-Type: application/json');

/**
 * =========================================================
 * REQUEST DATA
 * =========================================================
 */

$action = $_POST["action"] ?? "";
$token  = $_POST["token"] ?? "";
$wallet = $_POST["wallet"] ?? "real";

/**
 * =========================================================
 * TOKEN VALIDATION
 * =========================================================
 *
 * IMPORTANT:
 * Replace with REAL JWT validation.
 */

if($token == ""){

    echo json_encode([
        "error" => 100,
        "msg" => "Missing token"
    ]);

    exit;
}

/**
 * =========================================================
 * DEMO JWT PAYLOAD
 * =========================================================
 *
 * IMPORTANT:
 * Replace with REAL JWT decoding and validation.
 */

$jwt_payload = [
    "cid" => 12,
    "player_id" => 800,
    "username" => "MACTEST"
];

/**
 * =========================================================
 * CURRENCY RESOLUTION
 * =========================================================
 *
 * IMPORTANT:
 * Currency is NOT carried by the JWT and NOT accepted from
 * the request. The provider MUST derive currency from its
 * own per-cid configuration (the value agreed with the
 * casino at onboarding for this company).
 *
 * Replace this demo lookup with your real per-cid config.
 */

$provider_currency_by_cid = [
    12 => "USD"
];

$currency = $provider_currency_by_cid[$jwt_payload["cid"]] ?? "USD";

/**
 * =========================================================
 * VALIDATE COMPANY
 * =========================================================
 */

if($jwt_payload["cid"] != 12){

    echo json_encode([
        "error" => 101,
        "msg" => "Invalid company id"
    ]);

    exit;
}

/**
 * =========================================================
 * PLAYER DATA
 * =========================================================
 */

$player_id = $jwt_payload["player_id"];
$username  = $jwt_payload["username"];

/**
 * =========================================================
 * DEMO WALLET BALANCES
 * =========================================================
 *
 * IMPORTANT:
 * Replace with REAL DATABASE balances.
 */

$starting_balance = 5500.00;
$starting_free_balance = 200.00;

/**
 * =========================================================
 * ACTION: GET BALANCE
 * =========================================================
 */

if($action == "get_balance"){

    echo json_encode([
        "error" => 0,

        "balance" => $starting_balance,
        "free" => $starting_free_balance,

        "currency" => $currency,
        "account" => $username
    ]);

    exit;
}

/**
 * =========================================================
 * ACTION: PLACE REAL BET
 * =========================================================
 */

if($action == "place_bet"){

    $bet_amount = floatval($_POST["bet_amount"] ?? 0);

    if($bet_amount <= 0){

        echo json_encode([
            "error" => 201,
            "msg" => "Invalid bet amount"
        ]);

        exit;
    }

    $new_balance = $starting_balance - $bet_amount;

    if($new_balance < 0){

        echo json_encode([
            "error" => 202,
            "msg" => "Insufficient balance"
        ]);

        exit;
    }

    /**
     * -----------------------------------------------------
     * RECOMMENDED:
     * Save REAL wallet transaction in database.
     * -----------------------------------------------------
     */

    echo json_encode([
        "error" => 0,

        "balance" => round($new_balance, 2),
        "free" => $starting_free_balance,

        "currency" => $currency,
        "account" => $username
    ]);

    exit;
}

/**
 * =========================================================
 * ACTION: PLACE FREEPLAY BET
 * =========================================================
 */

if($action == "place_free_bet"){

    $bet_amount = floatval($_POST["bet_amount"] ?? 0);

    if($bet_amount <= 0){

        echo json_encode([
            "error" => 211,
            "msg" => "Invalid freeplay bet amount"
        ]);

        exit;
    }

    $new_free_balance = $starting_free_balance - $bet_amount;

    if($new_free_balance < 0){

        echo json_encode([
            "error" => 212,
            "msg" => "Insufficient freeplay balance"
        ]);

        exit;
    }

    /**
     * -----------------------------------------------------
     * RECOMMENDED:
     * Save FREEPLAY wallet transaction in database.
     * -----------------------------------------------------
     */

    echo json_encode([
        "error" => 0,

        "balance" => $starting_balance,
        "free" => round($new_free_balance, 2),

        "currency" => $currency,
        "account" => $username
    ]);

    exit;
}

/**
 * =========================================================
 * ACTION: CREDIT PRIZE
 * =========================================================
 *
 * wallet parameter:
 * - real
 * - free
 *
 * IMPORTANT:
 * The casino sends the active wallet type.
 * =========================================================
 */

if($action == "credit_prize"){

    $win_amount = floatval($_POST["win_amount"] ?? 0);

    if($win_amount < 0){

        echo json_encode([
            "error" => 301,
            "msg" => "Invalid win amount"
        ]);

        exit;
    }

    /**
     * -----------------------------------------------------
     * CREDIT REAL WALLET
     * -----------------------------------------------------
     */

    if($wallet == "real"){

        $new_balance = $starting_balance + $win_amount;

        echo json_encode([
            "error" => 0,

            "balance" => round($new_balance, 2),
            "free" => $starting_free_balance,

            "currency" => $currency,
            "account" => $username
        ]);

        exit;
    }

    /**
     * -----------------------------------------------------
     * CREDIT FREEPLAY WALLET
     * -----------------------------------------------------
     */

    $new_free_balance = $starting_free_balance + $win_amount;

    echo json_encode([
        "error" => 0,

        "balance" => $starting_balance,
        "free" => round($new_free_balance, 2),

        "currency" => $currency,
        "account" => $username
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
    "msg" => "Invalid action"
]);
?>