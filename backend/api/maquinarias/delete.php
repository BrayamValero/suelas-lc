<?php
session_start();
require_once '../db.php';

// Este archivo es para eliminar una maquinaria, se llama desde maquinaria.php
$id = trim($_GET['id']);

$sql = "SELECT * FROM MAQUINARIAS WHERE ID = ?;";
$maquinaria = db_query($sql, array($id));

if ($maquinaria[0]['CAPACIDAD'] != $maquinaria[0]['DISPONIBLE']) {
    $_SESSION['eliminar_maquinaria'] = false;
} else {
    $sql = "DELETE FROM MAQUINARIAS WHERE ID = ?;";
    $data = array($id);

    // Al eliminarse la maquinaria automaticamente se eliminan los casilleros
    db_query($sql, $data);
}

header("Location: ../../../maquinaria.php");
