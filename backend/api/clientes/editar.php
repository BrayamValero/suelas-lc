<?php
session_start();
require_once "../db.php";

$id = $_POST['id'];
$tipo_cliente = trim(mb_strtoupper($_POST['tipo-cliente'], 'UTF-8'));
$tipo_documento = trim(mb_strtoupper($_POST['tipo-documento'], 'UTF-8'));
$numero_documento = trim(mb_strtoupper($_POST['numero-documento'], 'UTF-8'));
$cliente_nombre = trim(mb_strtoupper($_POST['cliente-nombre'], 'UTF-8'));
$cliente_telefono = trim(mb_strtoupper($_POST['cliente-telefono'], 'UTF-8'));
$cliente_celular = trim(mb_strtoupper($_POST['cliente-celular'], 'UTF-8'));
$cliente_direccion = trim(mb_strtoupper($_POST['cliente-direccion'], 'UTF-8'));
$cliente_email = trim(mb_strtoupper($_POST['cliente-email'], 'UTF-8'));

$sql = "UPDATE CLIENTES SET TIPO = ?, DOCUMENTO = ?, DOCUMENTO_NRO = ?, NOMBRE = ?, TELEFONO = ?, CELULAR = ?, DIRECCION = ?, CORREO = ? WHERE ID = ?;";
db_query($sql, array($tipo_cliente, $tipo_documento, $numero_documento, $cliente_nombre, $cliente_telefono, $cliente_celular, $cliente_direccion, $cliente_email, $id));

header("Location: ../../../clientes.php");