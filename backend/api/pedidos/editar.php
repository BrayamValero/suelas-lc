<?php
session_start();
require_once "../db.php";
// echo '<pre>'; print_r($_POST); echo '</pre>';

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $pedido_id = $_GET['id'];
    $pedidos = $_POST['pedido'];
    $estado = 'EN ANALISIS';

    $cliente_id = test_input($_POST['nombre']);
    $forma_pago = test_input($_POST['pago']);
    $fecha_estimada = test_input($_POST['fecha']);
    
    $sql = "SELECT * FROM PEDIDOS WHERE ID = ?;";
    $estado_actual = db_query($sql, array($pedido_id))[0]['ESTADO'];

    if($estado_actual == 'EN ANALISIS'){

        $sql = "UPDATE PEDIDOS SET CLIENTE_ID = ?, FECHA_ESTIMADA = ?, FORMA_PAGO = ? WHERE ID = ?;";
        db_query($sql, array($cliente_id, $fecha_estimada, $forma_pago, $pedido_id));
    
        $sql = "DELETE FROM PRODUCCION WHERE PEDIDO_ID = ?;";
        db_query($sql, array($pedido_id));
    
        foreach ($pedidos as $pedido) {
            
            if ($pedido['cantidad'] != 0){
    
                if (isset($pedido['urgente'])) {
                    $urgente = test_input($pedido['urgente']);
                } else {
                    $urgente = 0;
                }
    
                $suela_id = test_input($pedido['suela_id']);
                $serie_id = test_input($pedido['serie_id']);
                $color_id = test_input($pedido['color_id']);
                $cantidad = test_input($pedido['cantidad']);
                $restante = test_input($pedido['cantidad']);
    
                $sql = "INSERT INTO PRODUCCION VALUES (NULL, ?, ?, ?, ?, ?, ?, 0, 0, 0, 0, 0, ?, ?, NOW())";
                db_query($sql, array($pedido_id, $suela_id, $serie_id, $color_id, $cantidad, $restante, $urgente, $estado));
    
            }
    
        }

        echo 'SUCCESS';

    } else {

        echo 'ERROR';

    }
    
}