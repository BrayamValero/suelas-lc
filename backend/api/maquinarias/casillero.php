<?php
session_start();
require_once "../db.php";

// Este archivo es para asignarle una suela a un casillero, se llama desde control-de-calidad.php
$maquinaria_id = trim($_POST['id']);
$suela_id = trim($_POST['suela-id']);
$casillero_id = trim($_POST['casillero-id']);
$casillero_color = trim($_POST['casillero-color']);

if ($suela_id == 'VACIO') {
    $sql = "UPDATE CASILLEROS SET SUELA_ID = NULL, COLOR = NULL WHERE ID = ?;";
    db_query($sql, array($casillero_id));
} else {
    $sql = "SELECT * FROM CASILLEROS 
            WHERE SUELA_ID = ? 
                AND COLOR = ?
                AND ID != ? ;";
    $result = db_query($sql, array($suela_id, $casillero_color, $casillero_id));

    if (empty($result)) {
        $sql = "UPDATE CASILLEROS SET SUELA_ID = ?, COLOR = ? WHERE ID = ?;";
        db_query($sql, array($suela_id, $casillero_color, $casillero_id));
    } else {
        $_SESSION['casillero_suela'] = true;
    }
}

if (isset($_GET['redir']) && $_GET['redir'] == 'cc') {
    header("Location: ../../../control-de-calidad.php?id=$maquinaria_id");
} else {
    header("Location: ../../../maquinaria.php");
}
