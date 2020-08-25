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
        $sql = "SELECT SUELA_ID, COLOR_ID FROM PRODUCCION WHERE PEDIDO_ID = ? AND DISPONIBLE + POR_PESAR > 0;";
        $result = db_query($sql, array($pedido_id));

        // Se busca todo el STOCK.
        $sql = "SELECT SUELA_ID, COLOR_ID FROM STOCK WHERE CLIENTE_ID = ?;";
        $stock = db_query($sql, array($cliente_id));


        echo "----------- PARA PUSHEAR EN STOCK -----------" . "<br>";
        echo '<pre>'; print_r($result); echo '</pre>';

        echo "----------- STOCK ACTUAL -----------" . "<br>";
        echo '<pre>'; print_r($stock); echo '</pre>';

        // Si el resultado no está vacio, quiere decir que el STOCK ya se encuentra registrado, por lo cual se debe pushear en el STOCK actual.
        if(!empty($result)){

            // Se realiza una funcion para comparar ambos valores.
            function compararValores($arr1, $arr2){
                return strcmp(serialize($arr1), serialize($arr2));
            }

            $intersect = array_uintersect($result, $stock, 'compararValores');
            
            echo "----------- ESTE SE ENCUENTRA EN STOCK, ASÍ QUE ESTE SE PUSHEA -----------" . "<br>";
            echo '<pre>'; print_r($intersect); echo '</pre>';

        }

    } else {
        // Se envia un warning en caso de que los inputs estén vacios.
        echo 'WARNING';
        
    }

}