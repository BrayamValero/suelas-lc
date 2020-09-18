<?php
session_start();
require_once "../db.php";
echo '<pre>'; print_r($_POST); echo '</pre>';

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $pedido_id = $_POST['pedido_id'];
    $pedidos = $_POST['pedido'];
    
    foreach ($pedidos as $pedido) {

        $prod_id = $pedido['prod_id'];
        $cantidad = $pedido['cantidad'];

        // Comprobamos que el pedido se tenga que eliminar o actualizar dependiendo del estado de producción.
        $sql = "SELECT CANTIDAD, RESTANTE, ESTADO FROM PRODUCCION WHERE ID = ?;";
        $verificacion = db_query($sql, array($prod_id));

        // Si la cantidad introducida es mayor a la actual, es decir si 250 es mayor a 200, quiere decir que se quiere aumentar, de resto, se quiere restar.
        if( $cantidad > $verificacion[0]['RESTANTE'] ){

            // Suma
            echo 'RESULTADO, ESTO SE SUMA CON CANTIDAD Y RESTANTE' . '<br>';
            $suma = $cantidad - $verificacion[0]['RESTANTE'];
            echo $suma . '<br>';

            // Cambiamos los valores de la cantidad y restante.
            $sql = "UPDATE PRODUCCION SET CANTIDAD = CANTIDAD + ?, RESTANTE = RESTANTE + ? WHERE ID = ?;";
            db_query($sql, array($suma, $suma, $prod_id));

        } else {

            // Se realiza una RESTA.
            $resta = $verificacion[0]['RESTANTE'] - $cantidad;
            
            if($cantidad != 0){
 
                $sql = "UPDATE PRODUCCION SET CANTIDAD = CANTIDAD - ?, RESTANTE = RESTANTE - ? WHERE ID = ?;";
                db_query($sql, array($resta, $resta, $prod_id));

            } elseif ($cantidad == 0 && $verificacion[0]['CANTIDAD'] != $verificacion[0]['RESTANTE'] && $verificacion[0]['ESTADO'] == 'PENDIENTE') {

                $sql = "UPDATE PRODUCCION SET CANTIDAD = CANTIDAD - ?, RESTANTE = RESTANTE - ?, ESTADO = 'POR DESPACHAR' WHERE ID = ?;";
                db_query($sql, array($resta, $resta, $prod_id));

            } else {

                $sql = "ALTER TABLE tableName DROP FOREIGN KEY fk; DELETE FROM PRODUCCION WHERE ID = ?;";
               
                db_query($sql, array($prod_id));

                echo $prod_id;

                echo "Eliminado el pedido individual.";

                $sql = "SELECT ID FROM PRODUCCION WHERE PEDIDO_ID = ?;";
                $resultado = db_query($sql, array($pedido_id));
                
                echo '<pre>'; print_r($resultado); echo '</pre>';

                // Si no hay producción, eliminar el pedido.
                if(empty($resultado)){
                    $sql = "DELETE FROM PEDIDOS WHERE ID = ?;";
                    db_query($sql, array($pedido_id));

                    echo "Eliminado el pedido individual.";
                }

            }
            
        }

    }

}


// // Si la cantidad de la producción actual se ve afectada.
// if($estado[0]['CANTIDAD'] != $estado[0]['RESTANTE']){

//     if($cantidad == 0){

//         // Query para cambiar estado a POR DESPACHAR.
//         $sql = "UPDATE PRODUCCION SET CANTIDAD = CANTIDAD - ?, RESTANTE = RESTANTE - ?, ESTADO = 'COMPLETADO' WHERE ID = ?;";
//         db_query($sql, array($cantidad, $cantidad, $prod_id));

//         // Se chequea si todas las producciones asociadas al pedido están listas.
//         $sql = "SELECT ESTADO FROM PRODUCCION WHERE PEDIDO_ID = ?;";
//         $resultado = db_query($sql, array($pedido_id));
//         $completado = true;

//         foreach ($resultado as $elem) {
//             if ($elem['ESTADO'] == 'PENDIENTE' || $elem['ESTADO'] == 'POR DESPACHAR') {
//                 $completado = false;
//             }
//         }

//         // Si todos las producciones asociadas al pedido estan completadas se marca el pedido como completado.
//         if ($completado) {
//             $sql = "UPDATE PEDIDOS SET ESTADO = 'COMPLETADO' WHERE ID = ?;";
//             db_query($sql, array($pedido_id));
//         }


//     } else {
//         // Query para actualizar.
//         $sql = "UPDATE PRODUCCION SET RESTANTE = ?, CANTIDAD = CANTIDAD - RESTANTE WHERE ID = ?;";
//         db_query($sql, array($cantidad, $cantidad, $prod_id));
//     }

// // De lo contrario, si no ha habido ningún cambio. 
// } else {

//     if($cantidad == 0){
//         // Query para eliminar.
//         $sql = "DELETE FROM PRODUCCION WHERE ID = ?;";
//         db_query($sql, array($prod_id));
//     } else {
//         // Query para actualizar.
//         $sql = "UPDATE PRODUCCION SET CANTIDAD = CANTIDAD + ?, RESTANTE = RESTANTE + ? WHERE ID = ?;";
//         db_query($sql, array($cantidad, $cantidad, $prod_id));
//     }

// }