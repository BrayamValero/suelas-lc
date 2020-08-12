<?php
session_start();
require_once '../db.php';

$maquinaria_id = $_GET['maquinaria'];

// Si llega maquina=1 por get, se deshabilita la maquina
if (isset($_GET['maquina']) && $_GET['maquina'] == '1') {

    $sql = "UPDATE MAQUINARIAS SET ESTADO = 'INACTIVO' WHERE ID = ?;";
    db_query($sql, array($maquinaria_id));

    $sql = "SELECT MIN(ID) AS ID FROM MAQUINARIAS WHERE ESTADO = 'ACTIVO';";
    $id = db_query($sql)[0]['ID'];

    if (empty($id)) {
        header("Location: ../../../maquinaria.php");
    } else {
        header("Location: ../../../control-de-calidad.php?id=$id");
    }
    
}