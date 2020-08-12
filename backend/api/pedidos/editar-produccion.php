<?php
session_start();
require_once "../db.php";

$id = $_GET['id'];
$estado = trim(strtoupper($_GET['estado']));
$pares = $_GET['pares'];

if ($estado == 'POR PESAR') { // viene de control de calidad
    $sql = "UPDATE PRODUCCION SET RESTANTE = RESTANTE - ?, POR_PESAR = POR_PESAR + ? WHERE ID = ?;";
    $data = array($pares, $pares, $id);
} elseif ($estado == 'PESADO') {
    $peso = $_GET['peso'];
    // viene de pesar y ahora va a despachar
    $sql = "UPDATE PRODUCCION SET POR_PESAR = POR_PESAR - ?, DISPONIBLE = DISPONIBLE + ?, PESADO = PESADO + ? WHERE ID = ?;";
    $data = array($pares, $pares, $peso, $id);
}

db_query($sql, $data);

$sql = "SELECT RESTANTE FROM PRODUCCION WHERE ID = ?;";
$restante = db_query($sql, array($id));

if ($restante[0]['RESTANTE'] === '0') {
    $sql = "UPDATE PRODUCCION SET ESTADO = 'POR DESPACHAR' WHERE ID = ?;";
    db_query($sql, array($id));
}
