<?php
session_start();
require_once "db.php";
// http://localhost/php/suelas-lc/backend/api/script.php

$sql = "SELECT * FROM SERIES;";
$series = db_query($sql);
// echo '<pre>'; print_r($series); echo '</pre>';

// Por cada serie que haya en sistema.
foreach ($series as $key => $serie) {

    $sql = "SELECT * FROM SUELAS WHERE UPPER(MARCA) = ?;";
    $suelas = db_query($sql, array($serie['NOMBRE']));
    // echo '<pre>'; print_r($suelas); echo '</pre>';

    foreach ($suelas as $key => $suela) {

        $sql = "INSERT INTO GRUPO_SERIES VALUES(NULL, ?, ?);";
        $data = array($serie['ID'], $suela['ID']);
        $result = db_query($sql, $data);

    }

}

echo "¡Query Finalizado!";
