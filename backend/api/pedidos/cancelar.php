<?php
session_start();
require_once "../db.php";

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $pedido_id = test_input($_POST["pedido_id"]);
    $cliente_id = 19;

    // Verificamos que cada campo haya sido ingresado, de lo contrario, lanzar ERROR.
    if(isset($pedido_id)){

        // Se buscan todos los pedidos en donde el resultado de (POR_PESAR + DISPONIBLE) > 0.
        $sql = "SELECT SUELA_ID, COLOR_ID, DISPONIBLE, POR_PESAR FROM PRODUCCION WHERE PEDIDO_ID = ? AND DISPONIBLE + POR_PESAR > 0;";
        $result = db_query($sql, array($pedido_id));

        // Buscando => STOCK.
        $sql = "SELECT SUELA_ID, COLOR_ID FROM STOCK WHERE CLIENTE_ID = ?;";
        $stock = db_query($sql, array($cliente_id));

        // Si hay suelas producidas, enviar a STOCK las suelas producidas y ELIMINAR pedido.
        if(!empty($result)){

            // Si se encuentra en stock => EDITAR.
            foreach($result as $key => &$row){

                foreach ($stock as &$elem){

                    if($row['SUELA_ID'] == $elem['SUELA_ID'] && $row['COLOR_ID'] == $elem['COLOR_ID']){

                        $sql = "UPDATE STOCK SET CANTIDAD = CANTIDAD + ? WHERE CLIENTE_ID = ? AND SUELA_ID = ? AND COLOR_ID = ?;";
                        db_query($sql, array($row['DISPONIBLE'] + $row['POR_PESAR'], $cliente_id, $row['SUELA_ID'], $row['COLOR_ID']));
                        unset($result[$key]);

                    }

                }

            }

            // Si no se encuentra en stock => AGREGAR.
            foreach($result as $key => &$row){

                $sql = "INSERT INTO STOCK VALUES(NULL, ?, ?, ?, ?);";
                db_query($sql, array($cliente_id, $row['SUELA_ID'], $row['COLOR_ID'], $row['DISPONIBLE'] + $row['POR_PESAR']));

            }

            // Luego eliminamos el pedido => ELIMINAR.
            $sql = "DELETE FROM PEDIDOS WHERE ID = ?;";
            db_query($sql, array($pedido_id));

        } else {

            // No hay suelas producidas, eliminar pedido => ELIMINAR.
            $sql = "DELETE FROM PEDIDOS WHERE ID = ?;";
            db_query($sql, array($pedido_id));

        }

    } else {
        // Se envia un warning en caso de que los inputs estén vacios.
        echo 'WARNING'; 
    }

}