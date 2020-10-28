<?php
session_start();
require_once "../db.php";
echo '<pre>'; print_r($_POST); echo '</pre>';

// // 1. SOLICITUD_MATERIAL
// $solicitud_material = json_decode($_POST['solicitud_material']);

// $pedido_id = $solicitud_material->pedido_id;
// $cliente_id = $solicitud_material->cliente_id;
// $estado = 'PENDIENTE';

// $sql = "INSERT INTO SOLICITUD_MATERIAL VALUES(NULL, ?, ?, ?, NOW());";
// $data = array($pedido_id, $cliente_id, $estado);

// $result = db_query($sql, $data);

// // 2. MATERIALES_SOLICITADOS
// $sql = "SELECT MAX(ID) AS ID FROM SOLICITUD_MATERIAL;";
// $solicitud_material_id = db_query($sql)[0]['ID'];

// $materiales_solicitados = json_decode($_POST['materiales_solicitados']);

// foreach ($materiales_solicitados as $material_solicitado){
    
//     $material = trim(strtoupper($material_solicitado->material));
//     $color = trim(strtoupper($material_solicitado->color));
//     $cantidad = $material_solicitado->cantidad;
//     $dureza = $material_solicitado->dureza;

//     $sql = "INSERT INTO MATERIALES_SOLICITADOS VALUES(NULL, ?, ?, ?, ?, ?);";
//     $data = array($solicitud_material_id, $material, $color, $cantidad, $dureza);

//     $result = db_query($sql, $data);

// }

// // 3. AUDITORIA_PED_NOR
// $sql = "INSERT INTO AUDITORIA_PED_NOR VALUES(NULL, NOW(), NULL, ?, ?);";
// $data = array($estado, $solicitud_material_id);

// $result = db_query($sql, $data);

// // 4. AUDITORIA_PED_PRO
// $sql = "INSERT INTO AUDITORIA_PED_PRO VALUES(NULL, NOW(), NULL, ?, ?);";
// $data = array($estado, $pedido_id);

// $result = db_query($sql, $data);

//5. Actualización de Stock
$actualizar_stock = json_decode($_POST['actualizar_stock']);

foreach ($actualizar_stock as $stock){
    
    $prod_id = $stock->prod_id;
    $color_id = $stock->color_id;
    $suela_id = $stock->suela_id;
    $stock = $stock->stock;

    // Actualizamos la "PRODUCCION" para evitar errores con "CONTROL DE CALIDAD"
    $sql = "UPDATE PRODUCCION 
        SET ESTADO = IF(CANTIDAD = ?, 'PENDIENTE', 'PRODUCCION', 'DESPACHO'), 
            STOCK = ?,
            RESTANTE = RESTANTE - ?,
            DISPONIBLE = ?
        WHERE ID = ?;";

    $data = array($stock, $stock, $stock, $stock, $prod_id);
    $result = db_query($sql, $data);

    // Actualizando el STOCK (Restamos en caso de que se haya descontado stock)
    $sql = "UPDATE STOCK SET CANTIDAD = CANTIDAD - ? WHERE SUELA_ID = ? AND COLOR_ID = ? AND CLIENTE_ID = 19; ";
    $data = array($stock, $suela_id, $color_id);
    $result = db_query($sql, $data);

}