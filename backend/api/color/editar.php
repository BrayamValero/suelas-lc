<?php
session_start();
require_once "../db.php";

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = test_input($_POST["id"]);
    $color = test_input($_POST["color"]);
    $codigo = test_input($_POST["codigo"]);

    // Verificamos que cada campo haya sido ingresado, de lo contrario, lanzar ERROR.
    if(isset($id, $color, $codigo)){
    
        // Se chequea que no se repita el elemento en la Base de Datos.
        $sql = "SELECT * FROM COLOR WHERE (COLOR = ? OR CODIGO = ?) AND ID NOT IN (?);";
        $check = db_query($sql, array($color, $codigo, $id));

        // Si el $check no contiene data, quiere decir que no se encuentra el elemento en la Database.
        if(empty($check)){

            $sql = "UPDATE COLOR SET COLOR = ?, CODIGO = ? WHERE ID = ?;";
            db_query($sql, array($color, $codigo, $id));
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