<?php
session_start();
require_once "../db.php";
// echo '<pre>'; print_r($_POST); echo '</pre>';

$id = $_POST['id'];
$referencia = trim(mb_strtoupper($_POST['referencia'], 'UTF-8'));
$marca = trim($_POST['marca']);
$talla = trim($_POST['talla']);
$material = trim($_POST['material']);
$peso_maquina = trim($_POST['peso_maquina']);
$peso_ideal = trim($_POST['peso_ideal']);
$capacidad_empaquetado = trim($_POST['capacidad_empaquetado']);

$sql = "UPDATE SUELAS SET REFERENCIA = ?, MARCA = ?, TALLA = ?, MATERIAL = ?, PESO_MAQUINA = ?, PESO_IDEAL = ?, CAP_EMPAQUETADO = ? WHERE ID = ?;";
$data = array($referencia, $marca, $talla, $material, $peso_maquina, $peso_ideal, $capacidad_empaquetado, $id);
db_query($sql, $data);