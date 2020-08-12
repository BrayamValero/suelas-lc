<?php
session_start();
require_once "../db.php";

$entrega_material_id = json_decode($_POST['entrega-material-id']);
$materiales = json_decode($_POST['materiales']);
$total = 0;

foreach ($materiales as $dato) {
    $materia_id = $dato->materiaId;
    $cantidad = trim($dato->cantidad);

    $total = $total + $dato->cantidad;


    $sql = "UPDATE MATERIALES_ENTREGADOS SET CANTIDAD = ? WHERE MATERIAL_ID = ? AND ENTREGA_MATERIAL_ID = ?;";
    $data = array($cantidad, $materia_id, $entrega_material_id);
    db_query($sql, $data);
}

$sql = "UPDATE ENTREGA_MATERIAL SET TOTAL = ? WHERE ID = ?;";
$data = array($total, $entrega_material_id);
db_query($sql, $data);