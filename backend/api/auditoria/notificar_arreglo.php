<?php
session_start();
require_once '../db.php';

$id = $_GET['id'];
            
$sql = "UPDATE AUDITORIA_PED_PRO
        SET ESTADO = 'APROBADO',
            FECHA_ENTREGADO = NOW()
        WHERE ID = ?;";

db_query($sql, array($id));

header("Location: ../../../control-de-calidad.php");