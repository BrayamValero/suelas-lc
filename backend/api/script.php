<?php
session_start();
require_once "db.php";
// http://localhost/php/suelas-lc/backend/api/script.php


// 2. SCRIPT => Agregar inventario al stock (Automático)
// $sql = "SELECT SUE.ID AS SUELA_ID, COL.ID AS COLOR_ID, IMP.CANTIDAD 
//         FROM IMPORTAR_INVENTARIO IMP
//             JOIN COLOR COL
//                 ON IMP.COLOR = COL.COLOR
//             JOIN SUELAS SUE
//                 ON IMP.MARCA = SUE.MARCA AND IMP.TALLA = SUE.TALLA;";

// $inventario = db_query($sql);

// // echo '<pre>'; print_r($inventario); echo '</pre>';

// foreach ($inventario as $key => $item) {

//     $sql = "INSERT INTO STOCK VALUES(NULL, ?, ?, ?, ?);";
//     $data = array(19, $item['SUELA_ID'], $item['COLOR_ID'], $item['CANTIDAD']);
//     $result = db_query($sql, $data);

// }

// echo "Done";

// // SCRIPT => Crear tabla GRUPO_SERIES (Automático)
// $sql = "SELECT * FROM SERIES;";
// $series = db_query($sql);
// // echo '<pre>'; print_r($series); echo '</pre>';

// // Por cada serie que haya en sistema.
// foreach ($series as $key => $serie) {

//     $sql = "SELECT * FROM SUELAS WHERE UPPER(MARCA) = ?;";
//     $suelas = db_query($sql, array($serie['NOMBRE']));
//     // echo '<pre>'; print_r($suelas); echo '</pre>';

//     foreach ($suelas as $key => $suela) {

//         $sql = "INSERT INTO GRUPO_SERIES VALUES(NULL, ?, ?);";
//         $data = array($serie['ID'], $suela['ID']);
//         $result = db_query($sql, $data);

//     }

// }

// echo "Done";