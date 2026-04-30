<?php

//$config = require_once($_SERVER['DOCUMENT_ROOT'] . '/config.php');

class sbo_db{
	var $databases = array();
	var $sql_server_connector = NULL;
	var $config;

	function __construct(){
		//global $config;
		//$dbConfig = $config["database"];
        $this->config = require $_SERVER['DOCUMENT_ROOT'] . '/config.php';
        $dbConfig = $this->config["database"];


		$this->databases["main"] = new database(
			$dbConfig["host"],
			$dbConfig["user"],
			$dbConfig["pass"],
			$dbConfig["name"],
			true
		);

		// AUTO CONNECT (importante para legacy)
		$this->connect("main");
	}

	function connect($id){
		$db = $this->databases[$id];
		if($db->sqlServer){
			$this->sqlServer_conection_process($db->host,$db->user,$db->pass,$db->name);
			return ($this->sql_server_connector !== NULL);
		}
		return false;
	}

	function sqlServer_conection_process($dbhost, $dbuser, $dbpass, $dbname) {
		$connectionInfo = array(
			"UID"=>$dbuser,
			"PWD"=>$dbpass,
			"Database"=>$dbname,
			"ReturnDatesAsStrings"=> true,
			"TrustServerCertificate" => true
		);

		$conn = sqlsrv_connect($dbhost, $connectionInfo);

		if($conn === false){
			$this->sql_server_connector = NULL;
			insert_error_log("SQLServer", json_encode(sqlsrv_errors()));
		}else{
			$this->sql_server_connector = $conn;
		}
	}

	function close_connection() {
		if(!is_null($this->sql_server_connector)){
			sqlsrv_close($this->sql_server_connector);
		}
	}
}

class database{
	var $host, $user, $pass, $name, $sqlServer;

	function __construct($phost, $puser, $ppass, $pname, $psqlServer = false){
		$this->host = $phost;
		$this->user = $puser;
		$this->pass = $ppass;
		$this->name = $pname;
		$this->sqlServer = $psqlServer;
	}
}
?>