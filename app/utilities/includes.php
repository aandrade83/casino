<?php

require_once $_SERVER['DOCUMENT_ROOT'] . '/vendor/autoload.php';


if(($_GE["show_errors"] ?? false)){
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

define("CASINO_BASE_URL", 
    ($_SERVER['HTTP_HOST'] == 'localhost:8080')
        ? "http://localhost:8080"
        : "https://casino.vrbmarketing.com"
);



//date_default_timezone_set("America/Costa_Rica");
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/classes.php");
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/vars.php");
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/functions.php");
include($_SERVER['DOCUMENT_ROOT'] . "/utilities/jwt_helper.php");
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/db/handler.php");
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/var_loader.php");
?>
