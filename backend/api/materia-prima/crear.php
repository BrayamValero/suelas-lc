<?php
session_start();
require_once "../db.php";

echo '<pre>'; print_r($_POST); echo '</pre>';

$descripcion = trim(mb_strtoupper($_POST['descripcion'], 'UTF-8'));
$cantidad = trim(mb_strtoupper($_POST['cantidad'], 'UTF-8'));
$material = trim(mb_strtoupper($_POST['material'], 'UTF-8'));
$color = trim(mb_strtoupper($_POST['color'], 'UTF-8'));
$dureza = trim(mb_strtoupper($_POST['dureza'], 'UTF-8'));

$descripcion_avanzada = $material . " " . $color . " " . $dureza . "%";

if ($material == '') {
    // Impresion Simple
    $sql = "INSERT INTO MATERIA_PRIMA VALUES(NULL, ?, NULL, NULL, NULL, ?);";
    $data = array($descripcion, $cantidad);
} else {
    // Impresion Completa
    $sql = "INSERT INTO MATERIA_PRIMA VALUES(NULL, ?, ?, ?, ? , ?);";
    $data = array($descripcion_avanzada, $material, $color, $dureza, $cantidad);
}

db_query($sql, $data);

header("Location: ../../../materia-prima.php");