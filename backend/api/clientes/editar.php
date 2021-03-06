<?php
session_start();
require_once "../db.php";

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = test_input($_POST["id"]);
    $tipo_cliente = test_input($_POST["tipo_cliente"]);
    $tipo_documento = test_input($_POST["tipo_documento"]);
    $numero_documento = test_input($_POST["numero_documento"]);
    $nombre = test_input($_POST["nombre"]);
    $telefono = test_input($_POST["telefono"]);
    $celular = test_input($_POST["celular"]);
    $correo = test_input($_POST["correo"]);
    $direccion = test_input($_POST["direccion"]);

    // Verificamos que cada campo haya sido ingresado, de lo contrario, lanzar ERROR.
    if(isset($id, $tipo_cliente, $tipo_documento, $numero_documento, $nombre, $telefono, $celular, $correo, $direccion)){

        // Se chequea que no se repita el elemento en la Base de Datos.
        $sql = "SELECT * FROM CLIENTES WHERE (DOCUMENTO_NRO = ? AND NOMBRE = ?) AND ID NOT IN (?);";
        $check = db_query($sql, array($numero_documento, $nombre, $id));
        
        // Si el $check no contiene data, quiere decir que no se encuentra el elemento en la Database.
        if(empty($check)){

            $sql = "UPDATE CLIENTES SET TIPO = ?, DOCUMENTO = ?, DOCUMENTO_NRO = ?, NOMBRE = ?, TELEFONO = ?, CELULAR = ?, DIRECCION = ?, CORREO = ? WHERE ID = ?;";
            db_query($sql, array($tipo_cliente, $tipo_documento, $numero_documento, $nombre, $telefono, $celular, $direccion, $correo, $id));
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