<?php
session_start();

require_once '../db.php';

$id = trim($_GET['id']);
$sql = "UPDATE CLIENTES SET ACTIVO = 'SI' WHERE ID = ?";
$data = array($id);

db_query($sql, $data);

header("Location: ../../../clientes.php");