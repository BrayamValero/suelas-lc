<?php
session_start();
require_once "../db.php";

echo '<pre>'; print_r($_POST); echo '</pre>';

$id = '1';
$dureza = $_POST['dureza'];

$sql = "INSERT INTO DUREZA VALUES (?, ?);";
db_query($sql, array($id, $dureza));

header("Location: ../../../dureza.php");