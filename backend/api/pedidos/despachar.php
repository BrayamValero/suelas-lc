<?php
require_once "../db.php";
session_start();

$ids = $_POST;

if (!empty($ids)) {
    foreach ($ids as $id) {
        $sql = "UPDATE PRODUCCION SET DESPACHADO = DESPACHADO + DISPONIBLE, DISPONIBLE = 0 WHERE ID = ?;";
        $data = array($id);

        db_query($sql, $data);

        $sql = "SELECT RESTANTE FROM PRODUCCION WHERE ID = ?;";
        $data = array($id);

        $restante = db_query($sql, $data)[0]['RESTANTE'];

        if ($restante === '0') {
            $sql = "UPDATE PRODUCCION SET ESTADO = 'COMPLETADO' WHERE ID = ?;";
            $data = array($id);

            db_query($sql, $data);
        }
    }

    $id = array_values($ids)[0];

    $sql = "SELECT * FROM PRODUCCION WHERE ID = ?;";
    $result = db_query($sql, array($id));
    $referencia = $result[0]['PEDIDO_ID'];

    // Se chequea si todos las producciones asociadas al pedido están listas
    $sql = "SELECT * FROM PRODUCCION WHERE PEDIDO_ID = ?;";
    $result = db_query($sql, array($referencia));

    $completado = true;

    foreach ($result as $row) {
        if ($row['ESTADO'] == 'PENDIENTE' || $row['ESTADO'] == 'POR DESPACHAR') {
            $completado = false;
        }
    }

    // Si todos las producciones asociadas al pedido estan completadas se marca el pedido como completado
    if ($completado) {
        $sql = "UPDATE PEDIDOS SET ESTADO = 'COMPLETADO' WHERE ID = ?;";
        db_query($sql, array($referencia));
    }


    header("Location: ../../../despachos-parciales.php");
}

