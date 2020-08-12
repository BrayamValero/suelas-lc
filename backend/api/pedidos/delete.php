<?php
session_start();
require_once '../db.php';

// Este archivo es para eliminar un pedido, se llama desde pedidos-pendientes.php

$id = trim($_GET['id']);

$sql = "DELETE FROM PEDIDOS WHERE ID = ?;";
$data = array($id);

// Al eliminarse el pedido automaticamente se eliminan las suelas de PRODUCCION
db_query($sql, $data);
