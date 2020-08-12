<?php
session_start();
require_once "../db.php";

$id = $_POST['id'];
$color = trim(mb_strtoupper($_POST['color'], 'UTF-8'));
$codigo = trim(mb_strtoupper($_POST['codigo'], 'UTF-8'));

$sql = "UPDATE COLOR SET COLOR = ?, CODIGO = ? WHERE ID = ?;";
db_query($sql, array($color, $codigo, $id));

header("Location: ../../../color.php");