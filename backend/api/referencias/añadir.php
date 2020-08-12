<?php
session_start();
require_once "../db.php";
// echo '<pre>'; print_r($_POST); echo '</pre>';

$referencia = trim(mb_strtoupper($_POST['referencia'], 'UTF-8'));
$marca = trim($_POST['marca']);
$talla = trim($_POST['talla']);
$material = trim($_POST['material']);
$peso_maquina = trim($_POST['peso_maquina']);
$peso_ideal = trim($_POST['peso_ideal']);
$capacidad_empaquetado = trim($_POST['capacidad_empaquetado']);

// // Se chequea si la referencia ya existe
$sql = "SELECT * FROM SUELAS WHERE REFERENCIA = ? AND MARCA = ? AND TALLA = ?;";
$result = db_query($sql, array($referencia, $marca, $talla));

if (!empty($result)) {

    echo $id = false;

} else {

    $sql = "INSERT INTO SUELAS VALUES (NULL, ?, ?, ?, ?, ?, ?, ?)";
    $data = array($referencia, $marca, $talla, $material, $peso_maquina, $peso_ideal, $capacidad_empaquetado);
    db_query($sql, $data);

    $sql = "SELECT MAX(ID) AS ID FROM SUELAS;";
    $id = db_query($sql)[0]['ID'];
    echo $id;

}

