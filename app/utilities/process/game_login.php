<?
/**
 * =========================================================
 * GAME LOGIN PROCESS
 * =========================================================
 * Entry point for launching a game session.
 *
 * Supports two authentication modes:
 *   1. JWT provider mode (third-party sportsbook integration)
 *   2. Standalone mode (legacy casino session)
 *
 * JWT has priority over the standalone session detection.
 * Legacy DGS lookup is no longer used and has been removed.
 * =========================================================
 */

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Credentials: true");

include($_SERVER['DOCUMENT_ROOT'] . "/utilities/includes.php");

/**
 * =========================================================
 * SESSION COOKIE PARAMS
 * =========================================================
 * Required so the session cookie works inside iframes
 * loaded from a different origin (cross-site).
 * =========================================================
 */
$is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');


session_set_cookie_params([
    'lifetime' => 86400,
    'path'     => '/',
    'domain'   => $mock_mode ? '' : 'casino.zytom-studios.com',
    'secure'   => $is_https,
    'httponly' => true,
    'samesite' => $is_https ? 'None' : 'Lax'
]);


session_start();

/**
 * =========================================================
 * REQUEST PARAMETERS
 * =========================================================
 */
$comapny_id     = param("cid");
$password       = param("cps");
$player_token   = param("token", false);
$player_account = strtoupper(param("account"));
$game_id        = param("game");
$sattousd       = param("sattousd");
$cashier_code   = param("cshcd");
$cashier_link   = get_cashier_link($cashier_code);

if($cashier_link == ""){ $cashier_code = ""; }

$jwt_login = false;
$jwt_data  = null;
$_company  = null;
$_player   = null;

/*
echo "<!-- DEBUG_SESSION ";
echo "game=" . ($_GET['game'] ?? 'NO_GAME') . " ";
echo "player=" . ($_SESSION['player'] ?? 'NO_PLAYER') . " ";
echo "company=" . ($_SESSION['company'] ?? 'NO_COMPANY') . " ";
echo "token=" . ($_SESSION['player_token'] ?? 'NO_TOKEN') . " ";
echo "-->"; */
/**
 * =========================================================
 * AUTH RESOLUTION (JWT HAS PRIORITY)
 * =========================================================
 */
if($player_token != "" && is_jwt_token($player_token)){

    $jwt_data = validate_jwt_token($player_token);

    if(!$jwt_data){
        $_SESSION['error_reason'] = "game_login_tokenInvalid";
        $_SESSION['error_url'] = $_SERVER['REQUEST_URI'];
        include($_SERVER['DOCUMENT_ROOT'] . "/utilities/ui/no_session.php");
        exit;
    }

    $jwt_company_id = $jwt_data->cid ?? 0;
    $_company       = get_company($jwt_company_id);

    if(is_null($_company)){
        $_SESSION['error_reason'] = "game_login_invalidCompany";
        $_SESSION['error_url'] = $_SERVER['REQUEST_URI'];
        include($_SERVER['DOCUMENT_ROOT'] . "/utilities/ui/no_session.php");
        exit;
    }

    if ($mock_mode) {
        include_once($_SERVER['DOCUMENT_ROOT'] . "/utilities/mock/mock_api.php");
        $_api = new _api_mock();
    } else {
        include($_SERVER['DOCUMENT_ROOT'] . "/utilities/api/" . $_company->vars["path"] . "/connect.php");
        $_api = new _api_connection();
    }

    $jwt_login = true;
}
/**
 * ---------------------------------------------------------
 * STANDALONE SESSION FALLBACK
 * ---------------------------------------------------------
 */
else if(isset($_SESSION['company'])){

    $_company = get_company($_SESSION['company']);

    if(!is_null($_company)){
        if ($mock_mode) {
            include_once($_SERVER['DOCUMENT_ROOT'] . "/utilities/mock/mock_api.php");
            $_api = new _api_mock();
        } else {
            include($_SERVER['DOCUMENT_ROOT'] . "/utilities/api/" . $_company->vars["path"] . "/connect.php");
            $_api = new _api_connection();
        }
    }
}

/**
 * =========================================================
 * STANDALONE MODE - RESTORE PLAYER FROM SESSION
 * =========================================================
 * Only runs when NOT logging in via JWT.
 * =========================================================
 */
if(
    !$jwt_login &&
    isset($_SESSION['player']) &&
    isset($_SESSION['company'])
){
    $_company = get_company($_SESSION['company']);
    $_player  = get_player($_SESSION['player']);

    $player_token = $_SESSION['player_token'];

    if(!is_null($_company) && !is_null($_player)){
        $_using_free_play = $_player->vars["using_free_play"];
    } else {
        $_SESSION['error_reason'] = "game_login_sessionInvalid";
        $_SESSION['error_url'] = $_SERVER['REQUEST_URI'];
        include($_SERVER['DOCUMENT_ROOT'] . "/utilities/ui/no_session.php");
        exit;
    }
}

/**
 * =========================================================
 * JWT MODE - DYNAMIC PLAYER / AGENT PROVISIONING
 * =========================================================
 * Creates the local player record on first login and keeps
 * the external provider IDs (player + agent) in sync.
 * =========================================================
 */
if($jwt_login){

    $jwt_player_id     = $jwt_data->player_id ?? 0;
    $jwt_agent_id      = $jwt_data->agent_id ?? 0;
    $jwt_username      = strtoupper(trim($jwt_data->username ?? ""));
    $jwt_agent_account = strtoupper(trim($jwt_data->agent_account ?? ""));

    if(!$jwt_player_id || $jwt_username == ""){
        echo "Invalid token payload.";
        exit;
    }

    $player_account = $jwt_username;
    $_player        = get_company_player($player_account, $_company->vars["id"]);

    /**
     * ---------------------------------------------------------
     * Create player on first JWT login + assign all company games
     * ---------------------------------------------------------
     */
    if(is_null($_player)){

        $_player = new _player();
        $_player->vars["account"]     = $player_account;
        $_player->vars["company"]     = $_company->vars["id"];
        $_player->vars["agent"]       = $jwt_agent_id;
        $_player->vars["external_id"] = $jwt_player_id;
        $_player->insert();

        $games   = get_all_company_games($_company->vars["id"]);
        $_player = get_company_player($player_account, $_company->vars["id"]);

        foreach($games as $g){
            $insert = new _game_by_person();
            $insert->vars['person']     = $_player->vars['id'];
            $insert->vars['game']       = $g->vars['game'];
            $insert->vars['visible']    = $g->vars['visible'];
            $insert->vars['min_amount'] = $g->vars['min_amount'];
            $insert->vars['max_amount'] = $g->vars['max_amount'];
            $insert->vars['is_agent']   = 0;
            $insert->insert();
        }

    } else {

        /**
         * Keep external IDs in sync if provider data changed.
         */
        if(($_player->vars["external_id"] ?? "") != $jwt_player_id){
            $_player->vars["external_id"] = $jwt_player_id;
            $_player->vars["agent"]       = $jwt_agent_id;
            $_player->update(array("external_id", "agent"));
        }
    }

    /**
     * ---------------------------------------------------------
     * Sync agent record with provider data
     * ---------------------------------------------------------
     */
    if($_player->vars["agent"]){

        $agent = get_company_agent_by_name($jwt_agent_account, $_company->vars["id"]);

        if(!empty($agent)){
            $agent->vars['external_id'] = $_player->vars["agent"];
            $agent->update(array('external_id'));
        } else {
            $agent = new _agent();
            $agent->vars['account']     = $jwt_agent_account;
            $agent->vars['external_id'] = $jwt_agent_id;
            $agent->vars['company']     = $_company->vars["id"];
            $agent->insert();
        }
    }
}

/**
 * =========================================================
 * GAME LIMITS
 * =========================================================
 */
if($game_id && !is_null($_player)){
    $_game        = $_player->get_allowed_game($game_id);
    $_max_amount  = round($_game->vars["max_amount"], 2);
    $_min_amount  = round($_game->vars["min_amount"], 2);
}

/**
 * =========================================================
 * FINALIZE SESSION
 * =========================================================
 * One single point that regenerates the session id and
 * persists player/company/cashier state. Reloads the player
 * to read a fresh using_free_play flag.
 * =========================================================
 */
if(!is_null($_company) && !is_null($_player)){

    //session_regenerate_id(true);

    $_SESSION['player']       = $_player->vars["id"];
    $_SESSION['player_token'] = $player_token;
    $_SESSION['company']      = $_company->vars["id"];
    $_SESSION['cshcd']        = $cashier_code;
    $_SESSION['hash']         = md5($_CODEX->encrypt($_SERVER['HTTP_USER_AGENT']));

    $_player          = get_player($_player->vars["id"]);
    $_using_free_play = $_player->vars["using_free_play"];

} else {

    $_SESSION['error_reason'] = "game_login_unableToConnect";
    $_SESSION['error_url'] = $_SERVER['REQUEST_URI'];
    include($_SERVER['DOCUMENT_ROOT'] . "/utilities/ui/no_session.php");
    exit;
}
?>
