<?php
session_start();
require_once "../db.php";

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $referencia = test_input($_POST["referencia"]);
    $marca = test_input($_POST["marca"]);
    $tallas = $_POST["talla"];
    $material = test_input($_POST["material"]);
    $peso_maquina = test_input($_POST["peso_maquina"]);
    $peso_ideal = test_input($_POST["peso_ideal"]);
    $capacidad_empaquetado = test_input($_POST["capacidad_empaquetado"]);

    // Verificamos que cada campo haya sido ingresado, de lo contrario, lanzar ERROR.
    if(isset($referencia, $marca, $tallas, $material, $peso_maquina, $peso_ideal, $capacidad_empaquetado)){
        
        // Se declara una variable la cual va a confirmar que todas las referencias sean únicas y no se encuentren registradas en la base de datos.
        $cancelado = false;

        foreach ($tallas as $talla) {  

            // Se chequea que no se repita el elemento en la Base de Datos.
            $sql = "SELECT * FROM SUELAS WHERE REFERENCIA = ? AND MARCA = ? AND TALLA = ?;";
            $check = db_query($sql, array($referencia, $marca, $talla));

            // Si alerta un error, cambiar el query a CANCELADO.
            if(!empty($check)) $cancelado = true;

        }

        if(!$cancelado){

            $ids = [];

            foreach ($tallas as $talla) {

                // Si todas las referencias son únicas, agregarlas todas a la base de datos.
                $sql = "INSERT INTO SUELAS VALUES (NULL, ?, ?, ?, ?, ?, ?, ?);";
                $data = array($referencia, $marca, $talla, $material, $peso_maquina, $peso_ideal, $capacidad_empaquetado);
                db_query($sql, $data);
            
                $sql = "SELECT MAX(ID) AS ID FROM SUELAS;";
                $id = db_query($sql)[0]['ID'];

                array_push($ids, $id);

            }

            echo json_encode($ids);
          
        } else {
            // Se envia el error en caso de que se encuentre el elemento en la Database.
            echo 'ERROR';
        }
        
    } else {
        // Se envia un warning en caso de que los inputs estén vacios.
        echo 'WARNING';
    }

}