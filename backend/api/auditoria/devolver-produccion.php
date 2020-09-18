<?php
session_start();
require_once "../db.php";
// echo '<pre>'; print_r($_GET); echo '</pre>';

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "GET") {

    $id = $_GET['id'];

    $sql = "SELECT * FROM AUDITORIA_CONTROL WHERE ID = ?;";
    $result = db_query($sql, array($id));

    $produccion_id = $result[0]['PRODUCCION_ID'];
    $cantidad = $result[0]['CANTIDAD'];
    $pesado = $result[0]['PESADO'];

    // Actualizamos la producción, incluyendo los datos introducidos por el empquetador.
    $sql = "UPDATE PRODUCCION 
            SET RESTANTE = RESTANTE + ?,
                PESADO = PESADO - ?,
                DISPONIBLE = DISPONIBLE - ?,
                ESTADO = 'PENDIENTE'
            WHERE ID = ?;";
    db_query($sql, array($cantidad, $pesado, $cantidad, $produccion_id));

    // Por último, eliminamos la tupla de Auditoria de Control.
    $sql = "DELETE FROM AUDITORIA_CONTROL WHERE ID = ?;";
    $result = db_query($sql, array($id));
    

}