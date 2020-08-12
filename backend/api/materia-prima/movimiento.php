<?php
session_start();
require_once "../db.php";

// echo '<pre>'; print_r($_POST); echo '</pre>';

$id = $_POST['id'];
$operacion = trim($_POST['operacion']);
$cantidad = trim($_POST['cantidad']);

if ($operacion == '+') {
    $sql = "UPDATE MATERIA_PRIMA SET EXISTENCIA = EXISTENCIA + ? WHERE ID = ?;";
    $data = array($cantidad, $id);

} else {
    $sql = "UPDATE MATERIA_PRIMA SET EXISTENCIA = EXISTENCIA - ? WHERE ID = ?;";
    $data = array($cantidad, $id);
}

db_query($sql, $data);

header("Location: ../../../materia-prima.php");