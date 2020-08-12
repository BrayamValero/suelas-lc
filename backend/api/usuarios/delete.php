<?php
session_start();

require_once '../db.php';

$id = trim($_GET['id']);
$time = time();
$sql = "UPDATE USUARIOS SET ACTIVO = 'NO', CEDULA = ?, CORREO = ? WHERE ID = ?";
$data = array($time, $time, $id);

db_query($sql, $data);

header("Location: ../../../usuarios.php");
