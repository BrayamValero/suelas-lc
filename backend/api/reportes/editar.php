<?php
session_start();
require_once "../db.php";

$reporte_id = json_decode($_POST['reporte_id']);

$turno = json_decode($_POST['turno']);
$material = json_decode($_POST['material']);
$materia_recibida = json_decode($_POST['materia_recibida']);
$materia_sobrante = json_decode($_POST['materia_sobrante']);
$colillas = json_decode($_POST['colillas']);
$patas = json_decode($_POST['patas']);
$varios = json_decode($_POST['varios']);
$produccion_actual = json_decode($_POST['produccion_actual']);
$observaciones = json_decode($_POST['observaciones']);

$sql = "UPDATE REPORTES SET TURNO = ?, MATERIAL = ?, MATERIA_RECIBIDA = ?, MATERIA_SOBRANTE = ?, COLILLAS = ?, PATAS = ?, VARIOS = ?, PRODUCCION_ACTUAL = ?, OBSERVACIONES = ? WHERE ID = ?;";
$data = array($turno, $material, $materia_recibida, $materia_sobrante, $colillas, $patas, $varios, $produccion_actual, $observaciones, $reporte_id);

db_query($sql, $data);