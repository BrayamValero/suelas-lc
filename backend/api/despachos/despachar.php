<?php
require_once "../db.php";
session_start();
// echo '<pre>'; print_r($_POST); echo '</pre>';

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $produccion_ids = $_POST;

    // Por cada producto despachado se hace lo siguiente.
    foreach ($produccion_ids as $prod_id) {
    
        // Seleccionamos la información de cada paquete enviado.
        $sql = "SELECT CLI.TIPO, PED.CLIENTE_ID, PRO.SUELA_ID, PRO.COLOR_ID, PRO.DISPONIBLE 
                    FROM PRODUCCION PRO
                        JOIN PEDIDOS PED
                            ON PED.ID = PRO.PEDIDO_ID 
                        JOIN CLIENTES CLI
                            ON CLI.ID = PED.CLIENTE_ID 
                WHERE PRO.ID = ?";
        $produccion = db_query($sql, array($prod_id));

        $cliente_tipo = strtoupper($produccion[0]['TIPO']);
        $cliente_id = $produccion[0]['CLIENTE_ID'];
        $suela_id = $produccion[0]['SUELA_ID'];
        $color_id = $produccion[0]['COLOR_ID'];
        $disponible = $produccion[0]['DISPONIBLE'];

        if($disponible === '0'){

            echo 'ERROR';

        } else {

            // Si el cliente es INTERNO, se registra todo el pedido en la tabla STOCK.
            if($cliente_tipo === 'INTERNO'){
                
                // Revisamos a ver si se encuentra registrado en stock.
                $sql = "SELECT ID FROM STOCK WHERE CLIENTE_ID = ? AND SUELA_ID = ? AND COLOR_ID = ?;";
                $stock = db_query($sql, array($cliente_id, $suela_id, $color_id));
                
                // Si no se encuentra registrado, lo registramos por primera vez (INSERT), de lo contrario solo se hace (UPDATE).
                if(empty($stock)){
                    $sql = "INSERT INTO STOCK VALUES(NULL, ?, ?, ?, ?);";
                    $result = db_query($sql, array($cliente_id, $suela_id, $color_id, $disponible));
                } else {
                    $sql = "UPDATE STOCK SET CANTIDAD = CANTIDAD + ? WHERE ID = ?";
                    $result = db_query($sql, array($disponible, $stock[0]['ID']));
                }

            }

            // Ahora, independientemente del tipo de CLIENTE que sea, guardamos el valor de RESTANTE y chequeamos si ya no hay suelas por despachar.
            $sql = "SELECT RESTANTE FROM PRODUCCION WHERE ID = ?;";
            $restante = db_query($sql, array($prod_id))[0]['RESTANTE'];

            // Actualizamos los campos DESPACHADO y DISPONIBLE despues de haber usado el campo DISPONIBLE.
            $sql = "UPDATE PRODUCCION SET DESPACHADO = DESPACHADO + DISPONIBLE, DISPONIBLE = 0 WHERE ID = ?;";
            db_query($sql, array($prod_id));

            // Damos por culminado la producción de la suela en caso de que RESTANTE === '0'.
            if ($restante === '0') {
                $sql = "UPDATE PRODUCCION SET ESTADO = 'COMPLETADO' WHERE ID = ?;";
                $data = array($prod_id);
                db_query($sql, $data);
            }

        }

        // Luego, se busca el pedido_id para comprobar que toda la producción esté lista.
        $prod_id = array_values($produccion_ids)[0];

        $sql = "SELECT PEDIDO_ID FROM PRODUCCION WHERE ID = ?;";
        $result = db_query($sql, array($prod_id));
        $pedido_id = $result[0]['PEDIDO_ID'];

        // Se chequea si todos las producciones asociadas al pedido están listas.
        $sql = "SELECT ESTADO FROM PRODUCCION WHERE PEDIDO_ID = ?;";
        $result = db_query($sql, array($pedido_id));
        $completado = true;

        foreach ($result as $row) {
            if ($row['ESTADO'] == 'PRODUCCION' || $row['ESTADO'] == 'DESPACHO') {
                $completado = false;
            }
        }

        // Si todos las producciones asociadas al pedido estan completadas se marca el pedido como completado.
        if ($completado) {
            $sql = "UPDATE PEDIDOS SET ESTADO = 'COMPLETADO' WHERE ID = ?;";
            db_query($sql, array($pedido_id));
        }

        header("Location: ../../../despachos-parciales.php");

    }

}
