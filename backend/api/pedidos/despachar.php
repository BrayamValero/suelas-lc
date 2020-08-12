<?php
require_once "../db.php";
session_start();
// echo '<pre>'; print_r($_POST); echo '</pre>';

$produccion_ids = $_POST;

if (!empty($produccion_ids)) {
    
    // Por cada ID seleccionado de DESPACHOS se hará lo siguiente.
    foreach ($produccion_ids as $prod_id) {
    
        // 1. Se agregan las suelas disponibles la tabla "STOCK". Para ello se tiene que comprobar que todos los datos coincidan para evitar ingresar un stock duplicado.
        $sql = "SELECT PED.CLIENTE_ID, PRO.SUELA_ID, PRO.COLOR_ID, PRO.DISPONIBLE 
                    FROM PRODUCCION PRO
                        JOIN PEDIDOS PED
                            ON PED.ID = PRO.PEDIDO_ID 
                WHERE PRO.ID = ?";
        $result = db_query($sql, array($prod_id));

        // 2. Registramos todos los campos necesarios para realizar los siguientes queries.
        $cliente_id = $result[0]['CLIENTE_ID'];
        $suela_id = $result[0]['SUELA_ID'];
        $color_id = $result[0]['COLOR_ID'];
        $disponible = $result[0]['DISPONIBLE'];

        // 3. Ahora con los datos obtenidos del $result, vamos a comprobar que no se encuentren STOCKS duplicados.
        $sql = "SELECT ID FROM STOCK WHERE CLIENTE_ID = ? AND SUELA_ID = ? AND COLOR_ID = ?";
        $data = array($cliente_id, $suela_id, $color_id);
        $result = db_query($sql, $data);

        // 4. Si, en efecto, el STOCK tiene registro, solo debemos usar UPDATE.
        if(!empty($result)){
            $stock_id = $result[0]['ID'];
            $sql = "UPDATE STOCK SET CANTIDAD = CANTIDAD + ? WHERE ID = ?";
            $result = db_query($sql, array($disponible, $stock_id));
        } 

        // 5. De lo contrario, si no está en STOCK, ingresamos el nuevo STOCK. 
        else {
            $sql = "INSERT INTO STOCK VALUES(NULL, ?, ?, ?, ?);";
            $result = db_query($sql, array($cliente_id, $suela_id, $color_id, $disponible));
        }
        
        // 6. Ahora guardamos el valor de restante y chequeamos si ya no hay suelas por despachar.
        $sql = "SELECT RESTANTE FROM PRODUCCION WHERE ID = ?;";
        $data = array($prod_id);
        $restante = db_query($sql, $data)[0]['RESTANTE'];

        // 7. Actualizamos los campos DESPACHADO y DISPONIBLE despues de haber usado el campo DISPONIBLE.
        $sql = "UPDATE PRODUCCION SET DESPACHADO = DESPACHADO + DISPONIBLE, DISPONIBLE = 0 WHERE ID = ?;";
        $data = array($prod_id);
        db_query($sql, $data);

        // 8. Damos por culminado la producción de la suela en caso de que RESTANTE === '0'.
        if ($restante === '0') {
            
            $sql = "UPDATE PRODUCCION SET ESTADO = 'COMPLETADO' WHERE ID = ?;";
            $data = array($prod_id);
            db_query($sql, $data);

        }

    }

    // 9. Luego, se busca el pedido_id para comprobar que toda la producción esté lista.
    $prod_id = array_values($produccion_ids)[0];

    $sql = "SELECT * FROM PRODUCCION WHERE ID = ?;";
    $result = db_query($sql, array($prod_id));
    $pedido_id = $result[0]['PEDIDO_ID'];

    // 10. Se chequea si todos las producciones asociadas al pedido están listas.
    $sql = "SELECT * FROM PRODUCCION WHERE PEDIDO_ID = ?;";
    $result = db_query($sql, array($pedido_id));
    $completado = true;

    foreach ($result as $row) {
        if ($row['ESTADO'] == 'PENDIENTE' || $row['ESTADO'] == 'POR DESPACHAR') {
            $completado = false;
        }
    }

    // 11. Si todos las producciones asociadas al pedido estan completadas se marca el pedido como completado.
    if ($completado) {
        $sql = "UPDATE PEDIDOS SET ESTADO = 'COMPLETADO' WHERE ID = ?;";
        db_query($sql, array($pedido_id));
    }

    header("Location: ../../../despachos-parciales.php");

}

