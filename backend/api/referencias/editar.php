<?php
session_start();
require_once "../db.php";

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = test_input($_POST["id"]);
    $referencia = test_input($_POST["referencia"]);
    $marca = test_input($_POST["marca"]);
    $talla = test_input($_POST["talla"]);
    $material = test_input($_POST["material"]);
    $peso_maquina = test_input($_POST["peso_maquina"]);
    $peso_ideal = test_input($_POST["peso_ideal"]);
    $capacidad_empaquetado = test_input($_POST["capacidad_empaquetado"]);
    
    // Verificamos que cada campo haya sido ingresado, de lo contrario, lanzar ERROR.
    if(isset($id, $referencia, $marca, $talla, $material, $peso_maquina, $peso_ideal, $capacidad_empaquetado)){

        // Se chequea que no se repita el elemento en la Base de Datos.
        $sql = "SELECT * FROM SUELAS WHERE (REFERENCIA = ? AND MARCA = ? AND TALLA = ?) AND ID NOT IN (?);";
        $check = db_query($sql, array($referencia, $marca, $talla, $id));

        // Si el $check no contiene data, quiere decir que no se encuentra el elemento en la Database.
        if(empty($check)){

            $sql = "UPDATE SUELAS SET REFERENCIA = ?, MARCA = ?, TALLA = ?, MATERIAL = ?, PESO_MAQUINA = ?, PESO_IDEAL = ?, CAP_EMPAQUETADO = ? WHERE ID = ?;";
            db_query($sql, array($referencia, $marca, $talla, $material, $peso_maquina, $peso_ideal, $capacidad_empaquetado, $id));
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