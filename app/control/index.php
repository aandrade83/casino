<?php

require_once($_SERVER['DOCUMENT_ROOT'] . "/utilities/includes.php");

$request = $_SERVER['REQUEST_URI'];

// Limpiar query string
$request = explode('?', $request)[0];

// separar partes
$parts = explode('/', trim($request, '/'));

$company_name = $parts[0] ?? null;
$company_pass = $parts[1] ?? null;

// DEBUG TEMPORAL
 //echo "Company: $company_name <br>Password: $company_pass"; exit;

// validar company
$_company = get_company_by_name($company_name, $company_pass);

if(!$_company){
    echo "Invalid company";
    exit;
}

// guardar en session (clave)
session_start();
$_SESSION['company'] = $_company->vars["id"];
$_SESSION['company_url'] = $_company->vars["site_url"];

// cargar login
//print_r($_company);
include("modules/login/index.php");