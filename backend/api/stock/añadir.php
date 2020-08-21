<?php
session_start();
require_once "../db.php";

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $origen = test_input($_POST["origen"]);
    $marca = test_input($_POST["marca"]);
    $color = test_input($_POST["color"]);
    $cantidad = test_input($_POST["cantidad"]);


    // Verificamos que cada campo haya sido ingresado, de lo contrario, lanzar ERROR.
    if(isset($origen, $marca, $color, $cantidad) && $cantidad > 0){

        // Se chequea que no se repita el elemento en la Base de Datos.
        $sql = "SELECT * FROM STOCK WHERE CLIENTE_ID = ? AND SUELA_ID = ? AND COLOR_ID = ?;";
        $check = db_query($sql, array($origen, $marca, $color));
        
        // Si el $check no contiene data, quiere decir que no se encuentra el elemento en la Database.
        if(empty($check)){

            $sql = "INSERT INTO STOCK VALUES(NULL, ?, ?, ?, ?);";
            $data = array($origen, $marca, $color, $cantidad);
            db_query($sql, $data);
        
            $sql = "SELECT MAX(ID) AS ID FROM STOCK;";
            $id = db_query($sql)[0]['ID'];
            echo $id;
          
        } else {
            // Se envia el error en caso de que se encuentre el elemento en la Database.
            echo 'ERROR';
        }
        
    } else {
        // Se envia un warning en caso de que los inputs estén vacios.
        echo 'WARNING';
    }

}