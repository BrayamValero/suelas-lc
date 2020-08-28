<?php
session_start();
require_once "../db.php";
// echo '<pre>'; print_r($_GET); echo '</pre>';

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $id = $_GET['id'];
    $pesado = $_GET['pesado'];
    $cantidad = $_GET['cantidad'];

    $sql = "UPDATE PRODUCCION 
            SET RESTANTE = RESTANTE - ?,
                PESADO = PESADO + ?,
                DISPONIBLE = DISPONIBLE + ?
            WHERE ID = ?;";
    db_query($sql, array($cantidad, $pesado, $cantidad, $id));

    $sql = "SELECT RESTANTE FROM PRODUCCION WHERE ID = ?;";
    $restante = db_query($sql, array($id));

    if ($restante[0]['RESTANTE'] === '0') {
        $sql = "UPDATE PRODUCCION SET ESTADO = 'POR DESPACHAR' WHERE ID = ?;";
        db_query($sql, array($id));
    }

}