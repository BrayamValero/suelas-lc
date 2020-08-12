<?php
require_once '../db.php';
session_start();

// Este archivo es para cambiarle el color a una maquinaria, se llama desde control-de-calidad.php

$id = $_POST['id'];
$color = trim(mb_strtoupper($_POST['color'], 'UTF-8'));


$sql = "SELECT * FROM MAQUINARIAS WHERE ID = ?;";
$maquinaria = db_query($sql, array($id));

if ($maquinaria[0]['COLOR'] != $color) {
    db_query("UPDATE CASILLEROS SET SUELA_ID = NULL, COLOR = NULL WHERE MAQUINARIA_ID = ?;", array($maquinaria[0]['ID']));
    db_query("UPDATE MAQUINARIAS SET COLOR = ? WHERE ID = ?;", array($color, $maquinaria[0]['ID']));
}


header("Location: ../../../control-de-calidad.php?id=$id");