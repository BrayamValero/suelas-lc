<?php
session_start();
require_once "../db.php";

$id = $_GET['id'];
$sql = "DELETE FROM STOCK WHERE ID = ?;";
db_query($sql, array($id));