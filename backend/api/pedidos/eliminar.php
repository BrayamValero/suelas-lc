<?php
session_start();
require_once '../db.php';

$id = $_POST['id'];
$sql = "DELETE FROM PEDIDOS WHERE ID = ?;";
db_query($sql, array($id));