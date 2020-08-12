<?php
session_start();
require_once "../db.php";

$color = trim(mb_strtoupper($_POST['color'], 'UTF-8'));
$codigo = trim(mb_strtoupper($_POST['codigo'], 'UTF-8'));

$sql = "INSERT INTO COLOR VALUES(NULL, ?, ?);";
$data = array($color, $codigo);

db_query($sql, $data);

header("Location: ../../../color.php");