<?php
session_start();
require_once "../db.php";
$result = json_decode($_POST['datos']);
// $result = json_decode(json_encode($_POST['datos']), true); => Converts to Array of Objects w/o stdClass.

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Obtenemos el ID del pedido.
    $sql = "SELECT PEDIDO_ID FROM PRODUCCION WHERE ID = ?;";
    $pedido_id = db_query($sql, array($result[0]->prod_id))[0]['PEDIDO_ID'];

    // Hacemos un loop entre cada prod_id para revisar que sus restantes no sean menores al valor ingresado.
    foreach ($result as $row) {

        $sql = "SELECT RESTANTE FROM PRODUCCION WHERE ID = ?;";
        $restante = db_query($sql, array($row->prod_id))[0]['RESTANTE'];

        // Si el valor dado es mayor al restante, mandar error y evitar la ejecución del documento.
        if($row->valor > $restante){
            echo json_encode(array('status' => 'error'));
            exit();
        }
        
    }

    // Si pasó el filtro de verificación de arriba, procedemos a hacer el loop.
    foreach ($result as $row) {
        
        // Guardamos los valores a usar en variables.
        $prod_id = $row->prod_id;
        $valor = $row->valor;

        // Buscamos el ID de la suela.
        $sql  = "SELECT SUELA_ID FROM PRODUCCION WHERE ID = ?";
        $suela_id = db_query($sql, array($prod_id))[0]['SUELA_ID'];

        // Ahora, buscamos el peso ideal basado en el ID de la suela.
        $sql  = "SELECT PESO_IDEAL FROM SUELAS WHERE ID = ?";
        $peso_ideal = db_query($sql, array($suela_id))[0]['PESO_IDEAL'];

        // En este paso realizamos la multiplicación de la cantidad de pares de suelas * el peso ideal.
        $peso_total = $peso_ideal * $valor;

        // Por último, actualizamos la producción.
        $sql = "UPDATE PRODUCCION SET
                    ESTADO = IF(RESTANTE - ? = 0, 'DESPACHO', 'PRODUCCION'), 
                    RESTANTE = RESTANTE - ?,
                    PESADO = PESADO + ?,
                    DISPONIBLE = DISPONIBLE + ?
                WHERE ID = ?;";
        $result = db_query($sql, array($valor, $valor, $peso_total, $valor, $prod_id));

    }

    // Se chequea si todos las producciones asociadas al pedido están listas.
    $sql = "SELECT ESTADO FROM PRODUCCION WHERE PEDIDO_ID = ?;";
    $estados = db_query($sql, array($pedido_id));
    $completado = true;

    // Se revisa si los estados de toda la producción están listos.
    foreach ($estados as $estado) {
        if ($estado['ESTADO'] === 'PRODUCCION') $completado = false;
    }

    if ($completado) {
        $sql = "UPDATE PEDIDOS SET ESTADO = 'DESPACHO' WHERE ID = ?;";
        db_query($sql, array($pedido_id));
    }

    echo json_encode(array('status' => 'success'));
    
}