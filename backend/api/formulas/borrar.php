<?php
// GET METHOD - BORRAR.PHP
session_start();
require_once "../db.php";

$formula_id = $_GET['formula-id'];
$sql = "DELETE FROM FORMULAS WHERE ID = ?;";
$data = array($formula_id);

db_query($sql, $data);

header("Location: ../../../formulas.php");