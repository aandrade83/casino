<?php
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
        }

        // 🔹 SESSION COMPLETA
        $_SESSION['player']  = $player->vars['id'];
        $_SESSION['account'] = $player->vars['account'];
        $_SESSION['company'] = $_SESSION['company'] ?? $player->vars['company'];
        $_SESSION['b_real']  = $player->vars["balance_real"];
        $_SESSION['b_free']  = $player->vars["balance_free"];

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
        }

        if (!$player) {
            header('Location: /control/modules/login/index.php?error=player_error');
            exit;
        }

        $_SESSION['player']  = $player->vars['id'];
        $_SESSION['account'] = $player->vars['account'];
        $_SESSION['company'] = $comp->vars['id'];
        $_SESSION['b_real']  = $player->vars["balance_real"];
        $_SESSION['b_free']  = $player->vars["balance_free"];

        session_write_close();

        header('Location: /control/modules/access/index.php');
        exit;

        break;

    default:
        json_ctrl(['success' => false, 'reason' => 'unknown_action'], 400);
}

function json_ctrl(array $data, int $status = 200): void
{
    http_response_code($status);
    echo json_encode($data);
    exit;
}

