<?php
session_start();
require_once "../db.php";

$id = $_GET['id'];
$sql = "DELETE FROM STOCK WHERE ID = ?;";
$data = array($id);
db_query($sql, $data);

header("Location: ../../../suelas-en-stock.php");