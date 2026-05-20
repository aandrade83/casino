<?php
ob_start();
session_start();

require_once($_SERVER['DOCUMENT_ROOT'] . "/utilities/includes.php");

//$request = $_SERVER['REQUEST_URI'];
$request = $_SERVER['HTTP_X_ORIGINAL_URI'] ?? $_SERVER['REQUEST_URI'];
$request = parse_url($request, PHP_URL_PATH);

// Limpiar query string
$request = explode('?', $request)[0];

// separar partes
$parts = explode('/', trim($request, '/'));

$company_name = $parts[0] ?? null;
$company_pass = $parts[1] ?? null;

// 🔥 SI YA HAY SESSION, USARLA
if(isset($_SESSION['company']) && !$company_name){
    
    $_company = get_company($_SESSION['company']);

    // 🔥 fallback por si la session es inválida
    if(!$_company){
        session_destroy();
        session_start();
        $_SESSION['error_reason'] = "index_session_expired";
        $_SESSION['error_url'] = $_SERVER['REQUEST_URI'];
        include($_SERVER['DOCUMENT_ROOT'] . "/utilities/ui/no_session.php");
        exit;
    }

} else {

    // 🔥 PRIMER ACCESO → validar por URL
    $_company = get_company_by_name($company_name, $company_pass);

    if(!$_company || $_company->vars['provider_system_id'] != 3){
        $_SESSION['error_reason'] = "index_invalid_company";
        $_SESSION['error_url'] = $_SERVER['REQUEST_URI'];
        include($_SERVER['DOCUMENT_ROOT'] . "/utilities/ui/no_session.php");
        exit;
    }

    // guardar en session
    $_SESSION['company'] = $_company->vars["id"];
    $_SESSION['company_url'] = $_company->vars["site_url"];
}

// cargar login
include("modules/login/index.php");