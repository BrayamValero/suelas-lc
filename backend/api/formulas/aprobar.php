<?php
// GET METHOD - APROBAR.PHP
session_start();
require_once "../db.php";

$id = $_GET['id'];

$sql = "UPDATE FORMULAS SET ESTADO = 'APROBADO' WHERE ID = ?;";
$data = array($id);

db_query($sql, $data);

header("Location: ../../../formulas.php");