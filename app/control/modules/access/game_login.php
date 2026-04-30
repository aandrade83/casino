<?php
header("Access-Control-Allow-Origin: *"); 
header("Access-Control-Allow-Credentials: true");


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// validar sesión creada por login
if(!isset($_SESSION['player']) || !isset($_SESSION['company'])){
    echo "Session not valid";
    exit;
}

// cargar objetos reales
$_player = get_player($_SESSION['player']); 
$_company = get_company($_SESSION['company']);

// simular token
$player_token = base64_encode($_player->vars["account"]);

// NO usamos API externa
$_game = null;

// opcional: si viene game
if(param("game")){
    $_game = $_player->get_allowed_game(param("game"));
    if(!is_null($_game)){
        $_min_amount = round($_game->vars["min_amount"], 2);
        $_max_amount = round($_game->vars["max_amount"], 2);
    }
}