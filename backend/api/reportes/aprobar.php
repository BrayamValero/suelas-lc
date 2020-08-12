<?php
session_start();
require_once "../db.php";

$id = $_GET['id'];

$sql = "UPDATE REPORTES SET ESTADO = 'APROBADO' WHERE ID = ?;";
$data = array($id);

db_query($sql, $data);

header("Location: ../../../reporte-de-produccion.php");