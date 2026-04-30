<?php
if(!empty($_GET["show_errors"])){
	error_reporting(E_ALL);
	ini_set('display_errors', 1);
}

//date_default_timezone_set("America/Costa_Rica");
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/classes.php");
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/vars.php");
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/functions.php");
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/db/handler.php");
include($_SERVER['DOCUMENT_ROOT'] ."/utilities/var_loader.php");
?>
