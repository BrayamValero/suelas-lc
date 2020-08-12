<?php
session_start();
require_once "../db.php";

$datos = json_decode($_POST['data']);

$turno = strtoupper(trim($datos->turno));
$material = strtoupper(trim($datos->material));

$sql = "SELECT * FROM OPERARIOS WHERE TURNO = ? AND MATERIAL = ?;";
$data = array($turno, $material);

$result = db_query($sql, $data);

if(empty($result)) {
    $sql = "INSERT INTO OPERARIOS VALUES (NULL, NULL, ? , ?);";
    $data = array($turno, $material);
} else {
    $sql = "UPDATE OPERARIOS SET USUARIO_ID = NULL WHERE TURNO = ? AND MATERIAL = ?;";
    $data = array($turno, $material);
}

db_query($sql, $data);