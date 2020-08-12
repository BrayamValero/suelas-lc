<?php
session_start();
require_once "../db.php";
echo '<pre>'; print_r($_POST); echo '</pre>';

$id = $_POST['id'];
$suelas = $_POST['suelas_edit'];
$nombre = trim(mb_strtoupper($_POST['nombre'], 'UTF-8'));
$nombre = preg_replace('!\s+!', ' ', $nombre);

// Si el NOMBRE no está vacio y tenemos SERIES => PUSHEAR.
if ( !empty($nombre) && !empty($suelas) ) {

    // PUSH => SERIES
    $sql = "UPDATE SERIES SET NOMBRE = ? WHERE ID = ?;";
    $data = array($nombre, $id);   
    db_query($sql, $data);

    // DELETE => SERIES
    $sql = "DELETE FROM GRUPO_SERIES WHERE SERIE_ID = ?;";
    $data = array($id);
    db_query($sql, $data);

    // PUSH => GRUPO_SERIES
    foreach ($suelas as $suela) {

        $sql = "INSERT INTO GRUPO_SERIES VALUES (NULL, ?, ?);";
        $data = array($id, $suela);
        db_query($sql, $data);
        
    }

    header("Location: ../../../series.php");

}

header("Location: ../../../series.php");