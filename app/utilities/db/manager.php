<?php

$sbo_db = new sbo_db();

/* =========================
   EXECUTE
========================= */
function execute($query){
	global $sbo_db;

	$stmt = sqlsrv_query($sbo_db->sql_server_connector, $query);

	if($stmt === false){
		insert_error_log("SQLServer", json_encode(sqlsrv_errors()));
		return false;
	}
	return true;
}

/* =========================
   GET STRING ARRAY
========================= */
function get_str($sql, $unique = false, $index_field = "NO_FIELD"){
	global $sbo_db;

	$result = array();
	$stmt = sqlsrv_query($sbo_db->sql_server_connector, $sql);

	if($stmt === false){
		insert_error_log("SQLServer", json_encode(sqlsrv_errors()));
		return $result;
	}

	while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
		if($index_field != "NO_FIELD"){
			$result[$row[$index_field]] = $row;
		}else{
			$result[] = $row;
		}
	}

	if($unique){
		return count($result) ? array_values($result)[0] : NULL;
	}

	return $result;
}

/* =========================
   GET OBJECTS
========================= */
function get($sql, $type, $unique = false, $index_field = "NO_FIELD"){
	global $sbo_db;

	$result = array();
	$stmt = sqlsrv_query($sbo_db->sql_server_connector, $sql);

	if($stmt === false){
		insert_error_log("SQLServer", json_encode(sqlsrv_errors()));
		return $result;
	}

	while($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)){
		eval("\$object = new $type();");

		foreach($row as $key => $value){
			$object->vars[$key] = $value;
		}

		$object->initial();

		if($index_field != "NO_FIELD"){
			$result[$object->vars[$index_field]] = $object;
		}else{
			$result[] = $object;
		}
	}

	if($unique){
		return count($result) ? array_values($result)[0] : NULL;
	}

	return $result;
}

/* =========================
   INSERT
========================= */
function insert($object, $table){
	global $sbo_db;

	$vars = is_object($object) ? $object->vars : $object;

	$fields = array_keys($vars);
	$params = implode(",", $fields);
	$placeholders = implode(",", array_fill(0, count($vars), "?"));

	$query = "INSERT INTO $table ($params) VALUES ($placeholders)";

	$stmt = sqlsrv_query($sbo_db->sql_server_connector, $query, array_values($vars));

	if($stmt === false){
		insert_error_log("SQLServer", json_encode(sqlsrv_errors()));
		return -1;
	}

	return true;
}

/* =========================
   UPDATE
========================= */
function update($object, $table){
	global $sbo_db;

	$vars = $object->vars;
	$id = $vars["id"];

	unset($vars["id"]);

	$set = "";
	foreach($vars as $key => $val){
		$set .= "$key = ?, ";
	}

	$set = rtrim($set, ", ");
	$query = "UPDATE $table SET $set WHERE id = ?";

	$params = array_values($vars);
	$params[] = $id;

	$stmt = sqlsrv_query($sbo_db->sql_server_connector, $query, $params);

	if($stmt === false){
		insert_error_log("SQLServer", json_encode(sqlsrv_errors()));
		return false;
	}

	return true;
}

/* =========================
   DELETE
========================= */
function delete($table, $id){
	global $sbo_db;

	$query = "DELETE FROM $table WHERE id = ?";
	$stmt = sqlsrv_query($sbo_db->sql_server_connector, $query, [$id]);

	if($stmt === false){
		insert_error_log("SQLServer", json_encode(sqlsrv_errors()));
		return false;
	}

	return true;
}

?>