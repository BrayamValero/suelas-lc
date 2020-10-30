<?php
session_start();
require_once "../db.php";
$result = json_decode($_POST['datos']);

foreach ($result as $key => $row) {
    
    // Guardamos los valores obtenidos en el array.
    $prod_id = $row->prod_id;
    $valor = $row->valor;

    // Buscamos el ID de la suela.
    $sql  = "SELECT SUELA_ID FROM PRODUCCION WHERE ID = ?";
    $suela_id = db_query($sql, array($prod_id))[0]['SUELA_ID'];

    echo $suela_id . "<br>";

    // Ahora, buscamos el peso ideal basado en el ID de la suela.
    $sql  = "SELECT PESO_IDEAL FROM SUELAS WHERE ID = ?";
    $peso_ideal = db_query($sql, array($suela_id))[0]['PESO_IDEAL'];

    echo $peso_ideal . "<br>";

    // En este paso realizamos la multiplicación de la cantidad de pares de suelas * el peso ideal.
    $peso_total = $peso_ideal * $valor;

    echo $peso_total . "<br>";

    // // Por último, actualizamos la producción.
    // $sql = "UPDATE PRODUCCION SET
    //             ESTADO = IF(RESTANTE - ? = 0, 'DESPACHO', 'PRODUCCION'), 
    //             RESTANTE = RESTANTE - ?,
    //             PESADO = PESADO + ?,
    //             DISPONIBLE = DISPONIBLE + ?
    //         WHERE ID = ?;";
    // $result = db_query($sql, array($valor, $valor, $peso_total, $valor, $prod_id));

    // // Se chequea si todos las producciones asociadas al pedido están listas.
    // $sql = "SELECT ESTADO FROM PRODUCCION WHERE PEDIDO_ID = ?;";
    // $result = db_query($sql, array($pedido_id));
    // $completado = true;

    // foreach ($result as $row) {
    //     if ($row['ESTADO'] == 'PRODUCCION' || $row['ESTADO'] == 'DESPACHO') {
    //         $completado = false;
    //     }
    // }

    // // Si todos las producciones asociadas al pedido estan completadas se marca el pedido como completado.
    // if ($completado) {
    //     $sql = "UPDATE PEDIDOS SET ESTADO = 'COMPLETADO' WHERE ID = ?;";
    //     db_query($sql, array($pedido_id));
    // }

    
}