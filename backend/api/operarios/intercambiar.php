<?php
session_start();
require_once "../db.php";

$usuario1 = json_decode($_POST['usuario1']);
$usuario2 = json_decode($_POST['usuario2']);

$sql = "SELECT ID FROM OPERARIOS WHERE USUARIO_ID = ?;";
$id_1 = db_query($sql, array($usuario1))[0]['ID'];

$sql = "SELECT ID FROM OPERARIOS WHERE USUARIO_ID = ?;";
$id_2 = db_query($sql, array($usuario2))[0]['ID'];

$sql = "UPDATE OPERARIOS SET USUARIO_ID = ? WHERE ID = ?;";
db_query($sql, array($usuario2, $id_1));


$sql = "UPDATE OPERARIOS SET USUARIO_ID = ? WHERE ID = ?;";
db_query($sql, array($usuario1, $id_2));
