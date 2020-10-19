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
        $sql = "SELECT CANTIDAD, RESTANTE, DISPONIBLE, ESTADO FROM PRODUCCION WHERE ID = ?;";
        $verificacion = db_query($sql, array($prod_id));

        // Si la cantidad introducida es mayor a la actual, es decir si 250 es mayor a 200, quiere decir que se quiere aumentar, de resto, se quiere restar.
        if( $cantidad > $verificacion[0]['RESTANTE'] ){

            // Se realiza una suma RESTA.
            $suma = $cantidad - $verificacion[0]['RESTANTE'];

            // Cambiamos los valores de la cantidad y restante.
            $sql = "UPDATE PRODUCCION SET CANTIDAD = CANTIDAD + ?, RESTANTE = RESTANTE + ? WHERE ID = ?;";
            db_query($sql, array($suma, $suma, $prod_id));

        } else {

            // Se realiza una RESTA.
            $resta = $verificacion[0]['RESTANTE'] - $cantidad;
            
            if($cantidad != 0){
 
                $sql = "UPDATE PRODUCCION SET CANTIDAD = CANTIDAD - ?, RESTANTE = RESTANTE - ? WHERE ID = ?;";
                db_query($sql, array($resta, $resta, $prod_id));

            } elseif ($cantidad == 0 && $verificacion[0]['CANTIDAD'] != $verificacion[0]['RESTANTE']) {

                // Si Disponible = 0 se pasa a COMPLETADO, de lo contrario se pasa a POR DESPACHAR.
                $verificacion[0]['DISPONIBLE'] == 0 ? $estado = 'COMPLETADO' : $estado = 'POR DESPACHAR';
                
                $sql = "UPDATE PRODUCCION SET CANTIDAD = CANTIDAD - ?, RESTANTE = RESTANTE - ?, ESTADO = ? WHERE ID = ?;";
                db_query($sql, array($resta, $resta, $estado, $prod_id));
                
                // Verificador Total => Este script realiza las siguientes evaluaciones.
                    
                    # Luego de actualizarse la producción, se debe verificar que todos los pedidos asociados al ID se encuentren en estado = COMPLETADO, de ser así, se cambia el estado del pedido, de lo contrario, se deja igual.
                     
                $sql = "SELECT ESTADO FROM PRODUCCION WHERE PEDIDO_ID = ?;";
                $resultado = db_query($sql, array($pedido_id));
                $completado = true;

                foreach ($resultado as $elem) {
                    if ($elem['ESTADO'] == 'PENDIENTE' || $elem['ESTADO'] == 'POR DESPACHAR') {
                        $completado = false;
                    }
                }

                // Si todos las producciones asociadas al pedido estan completadas se marca el pedido como completado.
                if ($completado) {
                    $sql = "UPDATE PEDIDOS SET ESTADO = 'COMPLETADO' WHERE ID = ?;";
                    db_query($sql, array($pedido_id));
                }

            } else {

                $sql = "DELETE FROM PRODUCCION WHERE ID = ?;";
                db_query($sql, array($prod_id));

                // Verificador Total => Este script realiza las siguientes evaluaciones.

                    # Verifica que haya al menos una referencia en producción, de no ser así, se elimina el pedido completo. 
                    # En el caso de que si haya al menos una referencia, se evalua el estado de cada referencia, en caso de que todas concuerden con el estado = COMPLETADO, se pasa el pedido a completado también.
                    # Esto de cambiar el estado del pedido no se ejecuta si al menos uno de los pedidos en producción se encuentra en PENDIENTE o POR DESPACHAR.
                    
                $sql = "SELECT ESTADO FROM PRODUCCION WHERE PEDIDO_ID = ?;";
                $resultado = db_query($sql, array($pedido_id));
                $completado = true;

                // Si no hay producción, eliminar el pedido.
                if(empty($resultado)){

                    $sql = "DELETE FROM PEDIDOS WHERE ID = ?;";
                    db_query($sql, array($pedido_id));
                    echo "Eliminado el pedido completo.";
                    
                } else {
                                        
                    foreach ($resultado as $elem) {
                        if ($elem['ESTADO'] == 'PENDIENTE' || $elem['ESTADO'] == 'POR DESPACHAR') {
                            $completado = false;
                        }
                    }

                    // Si todos las producciones asociadas al pedido estan completadas se marca el pedido como completado.
                    if ($completado) {
                        $sql = "UPDATE PEDIDOS SET ESTADO = 'COMPLETADO' WHERE ID = ?;";
                        db_query($sql, array($pedido_id));
                    }

                }

            }
            
        }

    }

    // Redireccionamos.
    header("Location: ../../../modificaciones.php");

}