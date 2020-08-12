<?php
require_once '../db.php';
session_start();

// Habilita un casillero, se llama desde control-de-calidad.php

$casillero_id = $_GET['id'];
$maquinaria_id = $_GET['maquinaria'];

$sql = "UPDATE CASILLEROS SET ACTIVO = 1 WHERE ID = ?;";
db_query($sql, array($casillero_id));

header("Location: ../../../control-de-calidad.php?id=$maquinaria_id");
