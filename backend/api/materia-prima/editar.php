<?php
session_start();
require_once "../db.php";

echo '<pre>'; print_r($_POST); echo '</pre>';

$id = $_POST['id'];

$descripcion = trim(mb_strtoupper($_POST['descripcion'], 'UTF-8'));
$material = trim(mb_strtoupper($_POST['material'], 'UTF-8'));
$color = trim(mb_strtoupper($_POST['color'], 'UTF-8'));
$dureza = trim(mb_strtoupper($_POST['dureza'], 'UTF-8'));

$descripcion_avanzada = $material . " " . $color . " " . $dureza . "%";

if ($material == '') {
    // Impresion Simple
    $sql = "UPDATE MATERIA_PRIMA SET DESCRIPCION = ? WHERE ID = ?;";
    $data = array($descripcion, $id);

} else {
    // Impresion Completa
    $sql = "UPDATE MATERIA_PRIMA SET DESCRIPCION = ?, MATERIAL = ?, COLOR = ?, DUREZA = ? WHERE ID = ?;";
    $data = array($descripcion_avanzada, $material, $color, $dureza, $id);
}

db_query($sql, $data);

header("Location: ../../../materia-prima.php");