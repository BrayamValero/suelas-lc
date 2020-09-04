<?php
session_start();
require_once "../db.php";
echo '<pre>'; print_r($_POST); echo '</pre>';

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $validacion = [];
    $actualizar_stock = json_decode($_POST['actualizar_stock']);

    foreach ($actualizar_stock as $stock){
        
        $prod_id = $stock->prod_id;
        $stock = $stock->stock;

        $sql = "SELECT RESTANTE FROM PRODUCCION WHERE ID = ?;";
        $restante = db_query($sql, array($prod_id))[0]['RESTANTE'];

        // Si lo que se va a enviar de stock (STOCK) es mayor al restante (PRODUCCION), enviar error.
        $result = $stock > $restante ? array_push($validacion, 'error') : array_push($validacion, 'success');

    }

    if(in_array('error', $validacion)){

        echo 'ERROR';

    } else {

        foreach ($actualizar_stock as $stock){
        
            $prod_id = $stock->prod_id;
            $color_id = $stock->color_id;
            $suela_id = $stock->suela_id;
            $stock = $stock->stock;
    
            $sql = "SELECT RESTANTE FROM PRODUCCION WHERE ID = ?;";
            $restante = db_query($sql, array($prod_id))[0]['RESTANTE'];


            // OLD

            // Actualizamos la "PRODUCCION" para evitar errores con "CONTROL DE CALIDAD"
            $sql = "UPDATE PRODUCCION 
                SET ESTADO = IF(CANTIDAD = ?, 'POR DESPACHAR', 'PENDIENTE'), 
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

    }

}



