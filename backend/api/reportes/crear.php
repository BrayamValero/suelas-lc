<?php
session_start();
require_once "../db.php";

$usuario_operario_id = json_decode($_POST['usuario_operario_id']);
$turno = json_decode($_POST['turno']);
$material = json_decode($_POST['material']);
$materia_recibida = json_decode($_POST['materia_recibida']);
$materia_sobrante = json_decode($_POST['materia_sobrante']);
$colillas = json_decode($_POST['colillas']);
$patas = json_decode($_POST['patas']);
$varios = json_decode($_POST['varios']);
$produccion_actual = json_decode($_POST['produccion_actual']);
$observaciones = json_decode($_POST['observaciones']);

$sql = "INSERT INTO REPORTES VALUES(NULL, ?, NOW(), ?, ?, ?, ?, ?, ?, ?, ? , 'PENDIENTE', ?);";
$data = array($usuario_operario_id, $turno, $material, $materia_recibida, $materia_sobrante, $colillas, $patas, $varios, $produccion_actual, $observaciones);

db_query($sql, $data);

$sql = "UPDATE PRODUCCION PRO JOIN SUELAS SU ON SU.ID = PRO.SUELA_ID SET PRO.PESADO = 0 WHERE PRO.ESTADO != 'COMPLETADO' AND SU.MATERIAL = ?;";
$data = array($material);

db_query($sql, $data);


// Marca como usado todas las entrega de material previas
$sql = "UPDATE ENTREGA_MATERIAL SET ESTADO = 'USADO' WHERE TURNO = ? AND MATERIAL = ?;";
$data = array($turno, $material);

db_query($sql, $data);