<?php
session_start();
require_once '../db.php';

$registro = json_decode($_POST['registro']);

if ($registro->materia_prima_id != NULL) {

    $materia_prima_id = $registro->materia_prima_id;
    $cantidad = $registro->cantidad;
    
    $sql = "UPDATE MATERIA_PRIMA SET EXISTENCIA = EXISTENCIA + ? WHERE ID = ?;";
    $data = array($cantidad, $materia_prima_id);
    db_query($sql, $data);

} else {

    $material = strtoupper(trim($registro->material));
    $color = strtoupper(trim($registro->color));
    $dureza = $registro->dureza;
    $cantidad = $registro->cantidad;
    $descripcion_avanzada = $material . " " . $color . " " . $dureza . "%";

    $sql = "INSERT INTO MATERIA_PRIMA VALUES(NULL, ?, ?, ?, ? , ?);";
    $data = array($descripcion_avanzada, $material, $color, $dureza, $cantidad);
    db_query($sql, $data);

}

// Cambiar el status a APROBADO.
$id = $registro->id;

$sql = "UPDATE AUDITORIA_NOR_INV SET ESTADO = 'APROBADO', FECHA_ENTREGADO = NOW() WHERE ID = ?;";
db_query($sql, array($id));