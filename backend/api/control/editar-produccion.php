<?php
session_start();
require_once "../db.php";

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    $prod_id = $_GET['id'];
    $pesado = $_GET['pesado'];
    $cantidad = $_GET['cantidad'];
    $nombre_usuario = $_SESSION['NOMBRE'];

    // Verificamos que la data no haya sido manipulada en otro documento.
    $sql = "SELECT RESTANTE FROM PRODUCCION WHERE ID = ?;";
    $restante = db_query($sql, array($prod_id))[0]['RESTANTE'];

    // Si la cantidad es mayor a la restante, mandar error.
    if($cantidad > $restante){
        echo json_encode(array('status' => 'error'));
        exit();
    } 
    
    // Si la cantidad ingresada es menor o igual a la restante, enviar la data.
    if($cantidad <= $restante){

        $sql = "SELECT PEDIDO_ID FROM PRODUCCION WHERE ID = ?;";
        $pedido_id = db_query($sql, array($prod_id))[0]['PEDIDO_ID'];

        // Insertando la data a Auditoria de Control de calidad para hacer reversiones en caso de errores.
        $sql = "INSERT INTO AUDITORIA_CONTROL VALUES (NULL, ?, ?, ?, NOW(), ?, ?);";
        $result = db_query($sql, array($prod_id, $pedido_id, $nombre_usuario, $cantidad, $pesado));

        // Actualizamos la producción, descontando los datos introducidos por el empquetador.
        $sql = "UPDATE PRODUCCION 
                SET RESTANTE = RESTANTE - ?,
                    PESADO = PESADO + ?,
                    DISPONIBLE = DISPONIBLE + ?
                WHERE ID = ?;";
        db_query($sql, array($cantidad, $pesado, $cantidad, $prod_id));
        
        $sql = "SELECT RESTANTE FROM PRODUCCION WHERE ID = ?;";
        $restante = db_query($sql, array($prod_id));

        // Si no hay producción restante se cambia de estado para quitar de CONTROL DE CALIDAD.
        if ($restante[0]['RESTANTE'] === '0') {
            $sql = "UPDATE PRODUCCION SET ESTADO = 'DESPACHO' WHERE ID = ?;";
            db_query($sql, array($prod_id));
        }

        echo json_encode(array('status' => 'success'));

    }

}