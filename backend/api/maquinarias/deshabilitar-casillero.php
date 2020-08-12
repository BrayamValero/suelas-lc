<?php
session_start();
require_once '../db.php';

$casillero_id = $_GET['id'];
$maquinaria_id = $_GET['maquinaria'];

$sql = "UPDATE CASILLEROS SET SUELA_ID = NULL, COLOR = NULL, ACTIVO = 0 WHERE ID = ?;";
db_query($sql, array($casillero_id));

header("Location: ../../../control-de-calidad.php?id=$maquinaria_id");
