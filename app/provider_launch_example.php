<?php

/**
 * =========================================================
 * PROVIDER LAUNCH EXAMPLE
 * =========================================================
 *
 * PURPOSE
 * ---------------------------------------------------------
 * This file demonstrates how a sportsbook / provider
 * should generate a JWT token and launch the casino.
 *
 * This is ONLY a reference implementation.
 *
 * Providers may implement this logic in:
 * - PHP
 * - Node.js
 * - Python
 * - .NET
 * - Java
 * - Go
 *
 * =========================================================
 * PROVIDER CONFIGURATION
 * =========================================================
 *
 * COMPANY ID (cid):
 * 12
 *
 * SHARED PROVIDER SECRET:
 * CASINO_VRB_SECRET_2026_!w0RldISgr3ATGaRR0b0ROcKsALwaY5s
 *
 * JWT Algorithm:
 * HS256
 *
 * CASINO URL:
 * http://localhost:8080/
 *
 * =========================================================
 * REQUIRED PHP LIBRARY
 * =========================================================
 *
 * Install:
 *
 * composer require firebase/php-jwt
 *
 * =========================================================
 */

require_once __DIR__ . '/vendor/autoload.php';

use Firebase\JWT\JWT;

/**
 * ---------------------------------------------------------
 * PROVIDER CONFIGURATION
 * ---------------------------------------------------------
 */

$casino_url = "http://localhost:8080/";

$shared_secret = "CASINO_VRB_SECRET_2026_!w0RldISgr3ATGaRR0b0ROcKsALwaY5s";

/**
 * ---------------------------------------------------------
 * PLAYER DATA
 * ---------------------------------------------------------
 *
 * Replace these values with your real sportsbook player data.
 * ---------------------------------------------------------
 */

$player_id = 800;

$username = "MACTEST";

/**
 * Currency is owned by the casino (company.currency) and is NOT included in the JWT.
 * It is agreed out-of-band at onboarding per cid.
 */

/**
 * Optional agent information
 */
$agent_id = 800;
$agent_account = "MACAGENT";

/**
 * ---------------------------------------------------------
 * JWT PAYLOAD
 * ---------------------------------------------------------
 */

$payload = [

    /**
     * Company ID assigned by casino provider
     */
    "cid" => 12,

    /**
     * Provider player ID
     */
    "player_id" => $player_id,

    /**
     * Provider username
     */
    "username" => strtoupper($username),

    /**
     * Optional agent info
     */
    "agent_id" => $agent_id,
    "agent_account" => strtoupper($agent_account),

    /**
     * JWT timestamps
     */
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
 * BUILD CASINO URL
 * ---------------------------------------------------------
 */

$launch_url = $casino_url . "?token=" . urlencode($jwt);

/**
 * ---------------------------------------------------------
 * REDIRECT PLAYER TO CASINO
 * ---------------------------------------------------------
 */

header("Location: " . $launch_url);
exit;
?>