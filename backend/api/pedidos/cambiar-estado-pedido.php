<?php
session_start();
require_once '../db.php';

$id = $_GET['id'];

$sql = "UPDATE PEDIDOS SET IMPRESO = 'SI' WHERE ID = ?;";
db_query($sql, array($id));