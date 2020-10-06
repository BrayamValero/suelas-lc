<?php
session_start();
require_once '../db.php';

$id = $_GET['id'];
$prioridad = $_GET['prioridad'];

$sql = "UPDATE PEDIDOS SET PRIORIDAD_ID = ? WHERE ID = ?;";
db_query($sql, array($prioridad, $id));