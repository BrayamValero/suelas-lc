<?php
session_start();
require_once '../db.php';

// Intercambia un casillero con otra maquinaria, se llama desde control-de-calidad.php
$maquinaria_id_a = $_POST['maquinaria-id-a'];
$maquinaria_id_b = $_POST['maquinaria-id-b'];

$casillero_a_id = $_POST['id-casillero-a'];
$casillero_b_numero = $_POST['id-casillero-b'];


$casillero_a = db_query("SELECT * FROM CASILLEROS WHERE ID = ?;", array($casillero_a_id));
$casillero_b = db_query("SELECT * FROM CASILLEROS WHERE NUMERO = ? AND MAQUINARIA_ID = ?;", array($casillero_b_numero, $maquinaria_id_b));

$suela_id_a = $casillero_a[0]['SUELA_ID'];
$suela_id_b = $casillero_b[0]['SUELA_ID'];

if ($suela_id_a == '') {
    $sql = "UPDATE CASILLEROS SET SUELA_ID = NULL WHERE NUMERO = ? AND MAQUINARIA_ID = ?;";
    db_query($sql, array($casillero_b_numero, $maquinaria_id_b));
} else {
    $sql = "UPDATE CASILLEROS SET SUELA_ID = ? WHERE NUMERO = ? AND MAQUINARIA_ID = ?;";
    db_query($sql, array($suela_id_a, $casillero_b_numero, $maquinaria_id_b));
}


if ($suela_id_b == '') {
    $sql = "UPDATE CASILLEROS SET SUELA_ID = NULL WHERE ID = ?;";
    db_query($sql, array($casillero_a_id));
} else {
    $sql = "UPDATE CASILLEROS SET SUELA_ID = ? WHERE ID = ?;";
    db_query($sql, array($suela_id_b, $casillero_a_id));
}

header("Location: ../../../control-de-calidad.php?id=$maquinaria_id_a");