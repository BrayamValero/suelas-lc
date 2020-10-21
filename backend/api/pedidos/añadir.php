<?php
session_start();
require_once "../db.php";
echo '<pre>'; print_r($_POST); echo '</pre>';

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Datos del cliente.
    $usuario_id = test_input($_SESSION['ID']);
    $cliente_id = test_input($_POST['nombre']);
    $fecha_estimada = test_input($_POST['fecha']);
    $forma_pago = test_input($_POST['pago']);

    // Datos del pedido.
    $pedidos = $_POST['pedido'];
    $prioridad_id = 1;
    $estado = 'EN ANALISIS';
    $impreso = 'NO';

    // Pedido => Añadir Pedido.
    $sql = "INSERT INTO PEDIDOS VALUES (NULL, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW());";
    db_query($sql, array($usuario_id, $cliente_id, $estado, $prioridad_id, $forma_pago, $impreso, $fecha_estimada));

    // Producción => Añadir Producción.
    $sql = "SELECT MAX(ID) AS ID FROM PEDIDOS";
    $pedido_id = db_query($sql)[0]['ID'];

    foreach ($pedidos as $pedido) {
        
        if ($pedido['cantidad'] != 0){

            // Operador Ternario.
            isset($pedido['urgente']) ? $urgente = test_input($pedido['urgente']) : $urgente = 0;

            $suela_id = test_input($pedido['suela_id']);
            $serie_id = test_input($pedido['serie_id']);
            $color_id = test_input($pedido['color_id']);
            $cantidad = test_input($pedido['cantidad']);
            $restante = test_input($pedido['cantidad']);

            $sql = "INSERT INTO PRODUCCION VALUES (NULL, ?, ?, ?, ?, ?, ?, 0, 0, 0, 0, 0, ?, ?, NULL)";
            db_query($sql, array($pedido_id, $suela_id, $serie_id, $color_id, $cantidad, $restante, $urgente, $estado));

        }

    }

}