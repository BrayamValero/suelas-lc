<?php
session_start();
require_once "../db.php";
// echo '<pre>'; print_r($_POST); echo '</pre>';

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = $_POST['id'];

    $sql = "SELECT * FROM AUDITORIA_CONTROL WHERE ID = ?;";
    $result = db_query($sql, array($id));

    $produccion_id = $result[0]['PRODUCCION_ID'];
    $cantidad = $result[0]['CANTIDAD'];
    $pesado = $result[0]['PESADO'];

    // Revisamos la producción de tal forma que si la misma NO se encuentra disponible, no se puede hacer este proceso.
    $sql = "SELECT DISPONIBLE FROM PRODUCCION WHERE ID = ?";
    $disponible = db_query($sql, array($produccion_id))[0]['DISPONIBLE'];
 
    // Si la cantidad devuelta es mayor o igual a la cantidad disponible en producción, realizar query.
    if($disponible >= $cantidad){

        // Actualizamos la producción, incluyendo los datos introducidos por el empquetador.
        $sql = "UPDATE PRODUCCION 
        SET RESTANTE = RESTANTE + ?,
            PESADO = PESADO - ?,
            DISPONIBLE = DISPONIBLE - ?,
            ESTADO = 'PRODUCCION'
        WHERE ID = ?;";
        db_query($sql, array($cantidad, $pesado, $cantidad, $produccion_id));

        // Por último, eliminamos la tupla de Auditoria de Control.
        $sql = "DELETE FROM AUDITORIA_CONTROL WHERE ID = ?;";
        $result = db_query($sql, array($id));

    } else {
        echo "ERROR";
    }

}