<?php
session_start();
require_once '../db.php';

$id = $_GET['id'];

$sql = "UPDATE PEDIDOS SET ESTADO = 'PENDIENTE' WHERE ID = ?;";
db_query($sql, array($id));

$sql = "UPDATE PRODUCCION SET ESTADO = 'PENDIENTE', CREATED_AT = NOW() WHERE PEDIDO_ID = ?;";
db_query($sql, array($id));

header("Location: ../../../pedidos-pendientes.php");
