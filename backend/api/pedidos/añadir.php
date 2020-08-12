<?php
session_start();
require_once "../db.php";
// echo '<pre>'; print_r($_POST); echo '</pre>';

// Datos del cliente.
$usuario_id = $_SESSION['USUARIO']['ID'];
$cliente_id = trim(mb_strtoupper($_POST['nombre'], 'UTF-8'));
$forma_pago = trim(mb_strtoupper($_POST['pago'], 'UTF-8'));
$fecha_estimada = trim(mb_strtoupper($_POST['fecha'], 'UTF-8'));

$estado = 'EN ANALISIS';
$prioridad = 'BAJA';

unset($_POST['nombre']);
unset($_POST['fecha']);
unset($_POST['pago']);

$sql = "INSERT INTO PEDIDOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW());";
$data = array($usuario_id, $cliente_id, $estado, $prioridad, $forma_pago, $fecha_estimada);

db_query($sql, $data);

// Datos del pedido.
$sql = "SELECT MAX(ID) AS ID FROM PEDIDOS";
$pedido_id = db_query($sql)[0]['ID'];

$pedidos = $_POST['pedido'];
// echo '<pre>'; print_r($pedidos); echo '</pre>';

foreach ($pedidos as $pedido) {
    
    if ($pedido['cantidad'] != 0){

        if (isset($pedido['urgente'])) {
            $urgente = trim(mb_strtoupper($pedido['urgente'], 'UTF-8'));
        } else {
            $urgente = 0;
        }

        $suela_id = trim(mb_strtoupper($pedido['suela_id'], 'UTF-8'));
        $serie_id = trim(mb_strtoupper($pedido['serie_id'], 'UTF-8'));
        $color_id = trim(mb_strtoupper($pedido['color_id'], 'UTF-8'));
        $cantidad = trim(mb_strtoupper($pedido['cantidad'], 'UTF-8'));
        $restante = trim(mb_strtoupper($pedido['cantidad'], 'UTF-8'));

        $sql = "INSERT INTO PRODUCCION VALUES (NULL, ?, ?, ?, ?, ?, ?, 0, 0, 0, 0, 0, ?, ?, NULL)";
        $data = array($pedido_id, $suela_id, $serie_id, $color_id, $cantidad, $restante, $urgente, $estado);
        db_query($sql, $data);

    }

}

header("Location: ../../../pedidos-pendientes.php");
