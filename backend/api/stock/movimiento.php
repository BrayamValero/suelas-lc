<?php
session_start();
require_once "../db.php";

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = test_input($_POST["id"]);
    $operacion = test_input($_POST["operacion"]);
    $cantidad = intval(test_input($_POST["cantidad"]));
    
    // Verificamos que cada campo haya sido ingresado, de lo contrario, lanzar ERROR.
    if(isset($id, $operacion, $cantidad) && $cantidad > 0){

        if ($operacion == '+') {
            $sql = "UPDATE STOCK SET CANTIDAD = CANTIDAD + ? WHERE ID = ?;";
            db_query($sql, array($cantidad, $id));
            echo $id;
        } else {
            $sql = "UPDATE STOCK SET CANTIDAD = CANTIDAD - ? WHERE ID = ?;";
            db_query($sql, array($cantidad, $id));
            echo $id;
        }

    } else {
        // Se envia un warning en caso de que los inputs estén vacios.
        echo 'WARNING';
    }

}