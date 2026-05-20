<?php

/**
 * =========================================================
 * PROVIDER LAUNCH EXAMPLE — SANITIZED DEMO
 * =========================================================
 *
 * Reference implementation showing how a sportsbook /
 * provider should generate a JWT token and launch the ZYTOM
 * Casino.
 *
 * This file is NON-FUNCTIONAL as shipped — the placeholders
 * marked "REQUEST FROM CASINO PROVIDER" must be replaced
 * with the values you receive during onboarding.
 *
 * Providers may reimplement this in any language:
 * PHP, Node.js, Python, .NET, Java, Go. The wire format
 * (HS256 JWT in the `?token=` query string) is the contract.
 *
 * =========================================================
 * REQUIRED PHP LIBRARY
 * =========================================================
 *
 *   composer require firebase/php-jwt
 *
 * =========================================================
 */

require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;

/**
 * ---------------------------------------------------------
 * PROVIDER CONFIGURATION
 * ---------------------------------------------------------
 *
 * REQUEST FROM CASINO PROVIDER (onboarding):
 *  - casino launch URL
 *  - JWT shared secret (HS256)
 *  - cid (company id assigned to you)
 *  - currency (agreed per cid; NOT carried in the JWT)
 * ---------------------------------------------------------
 */

$casino_url    = "<REQUEST FROM CASINO PROVIDER: casino launch URL>";
$shared_secret = "<REQUEST FROM CASINO PROVIDER: JWT shared secret>";
$cid           = "<REQUEST FROM CASINO PROVIDER: company id (cid)>";

/**
 * ---------------------------------------------------------
 * PLAYER DATA
 * ---------------------------------------------------------
 *
 * Replace these values with your real sportsbook player data.
 * ---------------------------------------------------------
 */

$player_id = 0;            // your internal player id
$username  = "PLAYER_USERNAME";

/**
 * Optional agent / affiliate information.
 * Send 0 / "" if not applicable.
 */
$agent_id      = 0;
$agent_account = "";

/**
 * ---------------------------------------------------------
 * JWT PAYLOAD
 * ---------------------------------------------------------
 *
 * NOTE: currency is NOT a JWT claim. It is owned by the
 * casino (company.currency) and agreed per cid out-of-band.
 * ---------------------------------------------------------
 */

$payload = [

    // Company id assigned by the casino provider
    "cid" => $cid,

    // Your internal player id
    "player_id" => $player_id,

    // Player account (uppercased on the casino side)
    "username" => strtoupper($username),

    // Optional agent info
    "agent_id"      => $agent_id,
    "agent_account" => strtoupper($agent_account),

    // JWT timestamps — exp MUST be <= 1 hour from iat
    "iat" => time(),
    "exp" => time() + 3600
];

/**
 * ---------------------------------------------------------
 * GENERATE JWT
 * ---------------------------------------------------------
 */

$jwt = JWT::encode(
    $payload,
    $shared_secret,
    'HS256'
);

/**
 * ---------------------------------------------------------
 * BUILD CASINO URL AND REDIRECT
 * ---------------------------------------------------------
 */

$launch_url = $casino_url . "?token=" . urlencode($jwt);

header("Location: " . $launch_url);
exit;
