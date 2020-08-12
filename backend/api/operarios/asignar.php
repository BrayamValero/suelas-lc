<?php
session_start();
require_once "../db.php";

$datos = json_decode($_POST['data']);

$usuario_id = $datos->usuarioId;
$turno = strtoupper(trim($datos->turno));
$material = strtoupper(trim($datos->material));

$sql = "SELECT * FROM OPERARIOS WHERE TURNO = ? AND MATERIAL = ?;";
$data = array($turno, $material);

$result = db_query($sql, $data);

if(empty($result)) {
	$sql = "INSERT INTO OPERARIOS VALUES (NULL, ?, ?, ?);";
	$data = array($usuario_id, $turno, $material);
} else {
	$sql = "UPDATE OPERARIOS SET USUARIO_ID = ? WHERE TURNO = ? AND MATERIAL = ?;";
	$data = array($usuario_id, $turno, $material);
}

db_query($sql, $data);