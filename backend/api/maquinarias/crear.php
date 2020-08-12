<?php
session_start();
require_once "../db.php";

// Este archivo crea una maquinaria, se llama desde maquinaria.php

$nombre = trim(mb_strtoupper($_POST['nombre'], 'UTF-8'));
$color = trim(mb_strtoupper($_POST['color'], 'UTF-8'));
$material = trim(mb_strtoupper($_POST['material'], 'UTF-8'));
$capacidad = trim(mb_strtoupper($_POST['capacidad'], 'UTF-8'));
$estado = trim(mb_strtoupper($_POST['estado'], 'UTF-8'));
$casilleros = trim($_POST['casillas']);

$sql = "INSERT INTO MAQUINARIAS VALUES(NULL, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW());";
$data = array($nombre, $color, $material, $capacidad, $capacidad, $estado, $casilleros);

db_query($sql, $data);

$sql = "SELECT MAX(ID) AS ID FROM MAQUINARIAS;";
$id = db_query($sql)[0]['ID'];

for ($i = 1; $i <= $casilleros; $i++) {
    $sql = "INSERT INTO CASILLEROS VALUES (NULL, ?, ?, NULL, NULL, 1);";
    $data = array($i, $id);
    db_query($sql, $data);
}

header("Location: ../../../maquinaria.php");
