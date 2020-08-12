<?php
session_start();
require_once "../db.php";

$origen = trim($_POST['origen']);
$marca = trim($_POST['marca']);
$color = trim($_POST['color']);
$cantidad = trim($_POST['cantidad']);

$sql = "INSERT INTO STOCK VALUES(NULL, ?, ?, ?, ?);";
$data = array($origen, $marca, $color, $cantidad);

db_query($sql, $data);

header("Location: ../../../suelas-en-stock.php");