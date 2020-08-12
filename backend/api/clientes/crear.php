<?php
session_start();
require_once "../db.php";

echo '<pre>'; print_r($_POST); echo '</pre>';

$tipo_cliente = mb_strtoupper($_POST['cliente'], 'UTF-8');
$tipo_documento = mb_strtoupper($_POST['cedula'], 'UTF-8');
$numero_documento = $_POST['documento'];
$nombre = trim(mb_strtoupper($_POST['nombre'], 'UTF-8'));
$telefono = trim($_POST['telefono']);
$celular = trim($_POST['celular']);
$email = trim(mb_strtoupper($_POST['email'], 'UTF-8'));
$direccion = trim(mb_strtoupper($_POST['direccion'], 'UTF-8'));

$sql = "INSERT INTO CLIENTES VALUES(NULL, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI');";
$data = array($tipo_documento, $numero_documento, $nombre, $tipo_cliente, $email, $telefono, $celular, $direccion);

db_query($sql, $data);

header("Location: ../../../clientes.php");