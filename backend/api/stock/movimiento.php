<?php
session_start();
require_once "../db.php";

// echo '<pre>'; print_r($_POST); echo '</pre>';

$id = $_POST['id'];
$operacion = trim($_POST['operacion']);
$cantidad = trim($_POST['cantidad']);

if ($operacion == '+') {
    $sql = "UPDATE STOCK SET CANTIDAD = CANTIDAD + ? WHERE ID = ?;";
    $data = array($cantidad, $id);

} else {
    $sql = "UPDATE STOCK SET CANTIDAD = CANTIDAD - ? WHERE ID = ?;";
    $data = array($cantidad, $id);
}

db_query($sql, $data);

header("Location: ../../../suelas-en-stock.php");