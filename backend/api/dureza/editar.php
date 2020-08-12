<?php
session_start();
require_once "../db.php";

$id = $_POST['id'];
$dureza = trim(mb_strtoupper($_POST['dureza'], 'UTF-8'));

$sql = "UPDATE DUREZA SET DUREZA = ? WHERE ID = ?;";
db_query($sql, array($dureza, $id));

header("Location: ../../../dureza.php");