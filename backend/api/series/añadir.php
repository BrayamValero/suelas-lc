<?php 
session_start();
require_once "../db.php";
// echo '<pre>'; print_r($_POST); echo '</pre>';

$suelas = $_POST['suelas'];
$nombre = trim(mb_strtoupper($_POST['nombre'], 'UTF-8'));
$nombre = preg_replace('!\s+!', ' ', $nombre);

// Revisamos que no haya ningún nombre repetido.
$sql = "SELECT * FROM SERIES WHERE NOMBRE = ?";
$data = array($nombre);
$verifNombre = db_query($sql, $data);

// Si el NOMBRE no es repetido y tenemos SERIES => PUSHEAR.
if ( empty($verifNombre) && !empty($suelas) ) {

    // PUSH => SERIES
    $sql = "INSERT INTO SERIES VALUES(NULL, ?);";
    $data = array($nombre);
    db_query($sql, $data);

    $sql = "SELECT MAX(ID) AS ID FROM SERIES;";
    $id = db_query($sql)[0]['ID'];

    // PUSH => GRUPO_SERIES
    foreach ($suelas as $suela) {

        $sql = "INSERT INTO GRUPO_SERIES VALUES (NULL, ?, ?);";
        $data = array($id, $suela);
        db_query($sql, $data);
        
    }

    header("Location: ../../../series.php");

}

header("Location: ../../../series.php");