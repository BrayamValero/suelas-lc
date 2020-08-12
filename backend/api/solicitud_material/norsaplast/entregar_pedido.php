<?php
session_start();
require_once '../../db.php';

$id = $_GET['id'];

$sql = "UPDATE SOLICITUD_MATERIAL, AUDITORIA_PED_NOR
        SET SOLICITUD_MATERIAL.ESTADO = 'APROBADO',
            AUDITORIA_PED_NOR.ESTADO = 'APROBADO',
            AUDITORIA_PED_NOR.FECHA_ENTREGADO = NOW()
        WHERE
            SOLICITUD_MATERIAL.ID = ? AND
            AUDITORIA_PED_NOR.ID = SOLICITUD_MATERIAL.ID;";

db_query($sql, array($id));

// AUDITORIA_NOR_INV
$estado = 'PENDIENTE';

$sql = "INSERT INTO AUDITORIA_NOR_INV VALUES(NULL, NOW(), NULL, ?, ?);";
$data = array($estado, $id);

$result = db_query($sql, $data);

header("Location: ../../../../solicitud-material.php");