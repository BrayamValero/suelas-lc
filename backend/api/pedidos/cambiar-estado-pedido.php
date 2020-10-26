<?php
session_start();
require_once '../db.php';

$id = $_POST['id'];
$estado  = $_POST['estado'];

if( $estado === 'analisis' ){

    $sql = "UPDATE PEDIDOS SET ESTADO = 'PENDIENTE' WHERE ID = ?;";
    db_query($sql, array($id));
    
    $sql = "UPDATE PRODUCCION SET ESTADO = 'PENDIENTE' WHERE PEDIDO_ID = ?;";
    db_query($sql, array($id));

} else if ( $estado === 'pendiente' ){

    $sql = "UPDATE PEDIDOS SET ESTADO = 'PRODUCCION' WHERE ID = ?;";
    db_query($sql, array($id));
    
    $sql = "UPDATE PRODUCCION SET ESTADO = 'PRODUCCION', CREATED_AT = NOW() WHERE PEDIDO_ID = ?;";
    db_query($sql, array($id));

}