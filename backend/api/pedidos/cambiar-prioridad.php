<?php
session_start();
require_once '../db.php';

$id = $_POST['id'];
$prioridad = $_POST['prioridad'];

$sql = "UPDATE PEDIDOS SET PRIORIDAD_ID = ? WHERE ID = ?;";
db_query($sql, array($prioridad, $id));