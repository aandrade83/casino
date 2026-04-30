<?php
while (ob_get_level() > 0) ob_end_clean();
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include($_SERVER['DOCUMENT_ROOT']."/utilities/includes.php");
include($_SERVER['DOCUMENT_ROOT']."/modules/access/api_local.php"); // tu clase _local_api

$action = $_GET['ac'] ?? $_POST['ac'] ?? '';

if (!isset($_SESSION['player'])) {
    json_out(['success' => false, 'error' => 'no_session'], 401);
}

$api = new _local_api();
$player_id = $_SESSION['player'];

switch ($action) {

    case 'get_balance':
        $balance = $api->get_player_balance($player_id);
        if(!$api->done){
            json_out(['success'=>false,'error'=>$api->error_msg], 400);
        }
        json_out(['success'=>true, 'balance'=>$balance]);
        break;

    case 'place_bet':
        $amount = floatval($_POST['amount'] ?? $_GET['amount'] ?? 0);
        
        $balance = $api->place_bet($player_id, $amount);

//        $api->place_bet($player_id, $amount);

        if(!$api->done){
            json_out(['success'=>false,'error'=>$api->error_msg], 400);
        }

        //$balance = $api->get_player_balance($player_id);
        json_out(['success'=>true, 'balance'=>$balance]);
        break;

    case 'credit_prize':
        $amount = floatval($_POST['amount'] ?? $_GET['amount'] ?? 0);
        $api->credit_prize($player_id, $amount);

        $balance = $api->get_player_balance($player_id);
        json_out(['success'=>true, 'balance'=>$balance]);
        break;

    default:
        json_out(['success'=>false,'error'=>'unknown_action'], 400);
}

function json_out($data, $code = 200){
    http_response_code($code);
    echo json_encode($data);
    exit;
}