<?php
session_start();
require_once '../db.php';

$id = $_GET['id'];
$sql = "UPDATE CLIENTES SET ACTIVO = 'SI' WHERE ID = ?";
db_query($sql, array($id));