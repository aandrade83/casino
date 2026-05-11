<?php

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'domain' => 'casino.vrbmarketing.com',
    'secure' => true,
    'httponly' => true,
    'samesite' => 'None'
]);

while (ob_get_level() > 0) ob_end_clean();
header('Content-Type: application/json');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include($_SERVER['DOCUMENT_ROOT'] . "/utilities/includes.php");

$ac      = $_GET['ac']     ?? $_POST['ac']     ?? '';
$account = strtoupper($_GET['player'] ?? $_POST['player'] ?? '');

switch ($ac) {

    case 'login':

        if (!isset($_SESSION['company'])) {
            json_ctrl(['success' => false, 'reason' => 'no_company'], 400);
        }

        $player = get_company_player($account, $_SESSION['company']);
       
        // 🔹 SI NO EXISTE → CREAR
        if (!$player) {

            $new = new _player();
            $new->vars['account'] = $account;
            $new->vars['company'] = $_SESSION['company'];
            $new->vars["balance_real"] = 0;
            $new->vars["balance_free"] = 0;
            $new->insert();

            // 🔥 recargar desde DB (seguro)
            $player = get_company_player($account, $_SESSION['company']);
            
            // Ingresamos permisos de company al player

            $games = get_all_company_games($player->vars['company']);

              foreach($games as $g){

                $insert = new _game_by_person();
                $insert->vars['person']     = $player->vars['id'];
                $insert->vars['game']       = $g->vars['game'];
                $insert->vars['visible']    = $g->vars['visible'];
                $insert->vars['min_amount'] = $g->vars['min_amount'];
                $insert->vars['max_amount'] = $g->vars['max_amount'];
                $insert->vars['is_agent']   = 0;
                $insert->insert();
               }

        }

  

        // 🔹 SESSION COMPLETA
        $_SESSION['player']  = $player->vars['id'];
        $_SESSION['account'] = $player->vars['account'];
        $_SESSION['company'] = $_SESSION['company'] ?? $player->vars['company'];
        //$_SESSION['b_real']  = $player->vars["balance_real"];
        //$_SESSION['b_free']  = $player->vars["balance_free"];

        //  TOKEN STANDALONE
        $_SESSION["player_token"] = base64_encode("standalone_" . $player->vars['id'] . "_" . time());
        //  HASH (igual que security espera)
        $_SESSION["hash"] = md5($_SERVER['HTTP_USER_AGENT']);

        session_write_close(); // flush session to disk before response

        json_ctrl([
            'success' => true,
            'ac' => 'login',
            'player_id' => $player->vars['id'],
            'balance_real' => $player->vars["balance_real"],
            'balance_free' => $player->vars["balance_free"]
        ]);

        break;

        
    case 'l_ext':

        global $_CODEX;

        $site     = $_GET['s']        ?? $_POST['s']        ?? '';
        $password = $_GET['password'] ?? $_POST['password'] ?? '';

        $comp = get_company_by_site($site);
        
        if (!$comp) {
            header('Location: /control/modules/login/index.php?error=no_company');
            exit;
        }

        $encrypted_password = $_CODEX->encrypt($password);
        $player = get_validated_player($account, $comp->vars['id'], $encrypted_password);
          


        if (!$player) {
            $new = new _player();
            $new->vars['account'] = $account;
            $new->vars['company'] = $comp->vars['id'];
            $new->vars["balance_real"] = 0;
            $new->vars["balance_free"] = 0;
            $new->insert();
            $player = get_company_player($account, $comp->vars['id']);
       

        //  recargar desde DB (seguro)
            $player = get_company_player($account, $comp->vars['id']);
            
            // Ingresamos permisos de company al player

            $games = get_all_company_games($player->vars['company']);

              foreach($games as $g){

                $insert = new _game_by_person();
                $insert->vars['person']     = $player->vars['id'];
                $insert->vars['game']       = $g->vars['game'];
                $insert->vars['visible']    = $g->vars['visible'];
                $insert->vars['min_amount'] = $g->vars['min_amount'];
                $insert->vars['max_amount'] = $g->vars['max_amount'];
                $insert->vars['is_agent']   = 0;
                $insert->insert();
               }
       
        }

        if (!$player) {
            header('Location: /control/modules/login/index.php?error=player_error');
            exit;
        }


        // 🔹 SESSION COMPLETA
        $_SESSION['player']  = $player->vars['id'];
        $_SESSION['account'] = $player->vars['account'];
        $_SESSION['company'] = $_SESSION['company'] ?? $player->vars['company'];
       
        //  TOKEN STANDALONE
        $_SESSION["player_token"] = base64_encode("standalone_" . $player->vars['id'] . "_" . time());
        //  HASH (igual que security espera)
        $_SESSION["hash"] = md5($_SERVER['HTTP_USER_AGENT']);

        session_write_close(); // flush session to disk before response

        json_ctrl([
            'success' => true,
            'ac' => 'login',
            'player_id' => $player->vars['id'],
            'balance_real' => $player->vars["balance_real"],
            'balance_free' => $player->vars["balance_free"]
        ]);

       // header('Location: /control/modules/access/index.php');
        exit;

        break;

    default:
        json_ctrl(['success' => false, 'reason' => 'unknown_action'], 400);
}



