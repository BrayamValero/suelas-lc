<?php
session_start();
require_once '../db.php';
print_r($_GET);

$casillero_id = test_input($_GET["id"]);
$maquinaria_id = test_input($_GET["maquinaria"]);

if(isset($casillero_id, $maquinaria_id)){
    $sql = "UPDATE CASILLEROS SET SUELA_ID = NULL, COLOR = NULL, ACTIVO = 1 WHERE ID = ?;";
    db_query($sql, array($casillero_id));
}

header("Location: ../../../control-de-calidad.php?id=$maquinaria_id");


