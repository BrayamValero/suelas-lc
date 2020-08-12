<?php
session_start();
require_once "../db.php";
// echo '<pre>'; print_r($_POST); echo '</pre>';

// 1. Datos del Cliente
$pedido_id = $_GET['id'];
$cliente_id = trim(mb_strtoupper($_POST['nombre'], 'UTF-8'));
$forma_pago = trim(mb_strtoupper($_POST['pago'], 'UTF-8'));
$fecha_estimada = trim(mb_strtoupper($_POST['fecha'], 'UTF-8'));
$estado = 'EN ANALISIS';

$sql = "UPDATE PEDIDOS SET CLIENTE_ID = ?, FECHA_ESTIMADA = ?, FORMA_PAGO = ? WHERE ID = ?;";
$data = array($cliente_id, $fecha_estimada, $forma_pago, $pedido_id);

db_query($sql, $data);

$sql = "DELETE FROM PRODUCCION WHERE PEDIDO_ID = ?;";
db_query($sql, array($pedido_id));

// 2. Datos del Pedido
$pedidos = $_POST['pedido'];

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

        $sql = "INSERT INTO PRODUCCION VALUES (NULL, ?, ?, ?, ?, ?, ?, 0, 0, 0, 0, 0, ?, ?, NOW())";
        $data = array($pedido_id, $suela_id, $serie_id, $color_id, $cantidad, $restante, $urgente, $estado);
        db_query($sql, $data);

    }

}

header("Location: ../../../pedidos-pendientes.php");
