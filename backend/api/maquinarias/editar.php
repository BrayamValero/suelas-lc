<?php
session_start();
require_once "../db.php";

// Edita los datos de la maquinaria, se llama desde maquinaria.php
$id = $_POST['id'];
$nombre = trim(mb_strtoupper($_POST['nombre'], 'UTF-8'));
$color = trim(mb_strtoupper($_POST['color'], 'UTF-8'));
$material = trim(mb_strtoupper($_POST['material'], 'UTF-8'));
$capacidad = trim(mb_strtoupper($_POST['capacidad'], 'UTF-8'));
$estado = trim(mb_strtoupper($_POST['estado'], 'UTF-8'));


$sql = "SELECT * FROM MAQUINARIAS WHERE ID = ?;";
$maquinaria = db_query($sql, array($id));

// Si se le cambia el color, si se deshabilita o se cambia el material...
if (($maquinaria[0]['COLOR'] != $color) || ($maquinaria[0]['ESTADO'] != $estado) || ($maquinaria[0]['MATERIAL'] != $material)) {
    db_query("UPDATE CASILLEROS SET SUELA_ID = NULL, COLOR = NULL WHERE MAQUINARIA_ID = ?;", array($maquinaria[0]['ID']));
}

if ($maquinaria[0]['CAPACIDAD'] == $maquinaria[0]['DISPONIBLE']) {
    $sql = "UPDATE MAQUINARIAS SET NOMBRE = ?, COLOR = ?, MATERIAL = ?, CAPACIDAD = ?, DISPONIBLE = ?, ESTADO = ? WHERE ID = ?;";
    db_query($sql, array($nombre, $color, $material, $capacidad, $capacidad, $estado, $id));
} else {
    $capacidad_nueva = $capacidad;
    $capacidad_vieja = $maquinaria[0]['CAPACIDAD'];
    $capacidad = $capacidad_nueva - $capacidad_vieja;

    $sql = "UPDATE MAQUINARIAS SET NOMBRE = ?, COLOR = ?, MATERIAL = ?, CAPACIDAD = ?, DISPONIBLE = DISPONIBLE + ?, ESTADO = ? WHERE ID = ?;";
    db_query($sql, array($nombre, $color, $material, $capacidad_nueva, $capacidad, $estado, $id));
}



header("Location: ../../../maquinaria.php");
