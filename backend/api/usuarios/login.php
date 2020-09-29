<?php
session_start();
require_once '../db.php';
$action = strtoupper($_GET['action']);

switch ($action) {

    case "LOGIN":

        $username = test_input($_POST['username']);
        $password = test_input($_POST['password']);
        
        // Si el usuario y contraseña no se encuentran vacios.
        if( isset($username) && isset($password) ){

            $sql = "SELECT * FROM USUARIOS WHERE CORREO = ?";
            $user = db_query($sql, array($username));

            // Si el correo coincide.
            if(!empty($user)){

                // Si la contraseña coincide.
                if( password_verify($password, $user[0]['CONTRASENA']) ){

                    $_SESSION['USUARIO'] = array(
                        'ID'                => $user[0]['ID'],
                        'CORREO'            => $user[0]['CORREO'],
                        'NOMBRE'            => $user[0]['NOMBRE'],
                        'CARGO'             => $user[0]['CARGO'],
                        'CREADO'            => time(),
                        'ULTIMA_ACTIVIDAD'  => time()
                    );
        
                    $sql = "UPDATE USUARIOS SET SESSION_ID = ? WHERE ID = ?;";
                    $data = array(session_id(), $user[0]['ID']);
                    db_query($sql, $data);

                    echo "SUCCESS";
                    
                } else {

                    echo "ERROR";

                }

            } else {

                echo "ERROR";

            }

        }

        break;

    case "REGISTER":
        
        $nombre = test_input($_POST['nombre']);
        $telefono = test_input($_POST['telefono']);
        $cedula = test_input($_POST['cedula']);
        $cargo = test_input($_POST['cargo']);
        $correo = test_input($_POST['correo']);
        $password = test_input($_POST['contrasena']);

        if(isset($nombre, $telefono, $cedula, $cargo, $correo, $password)){

            $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
            $data = array($nombre, $cedula, $correo, $telefono, password_hash($password, PASSWORD_DEFAULT), $cargo);
            db_query($sql, $data);
    
            header("Location: ../../../usuarios.php");

        }

        break;

    case "UNLOGIN":

        session_unset();
        session_destroy();

        if (isset($_GET['inactivity']) && $_GET['inactivity'] == 'true') {
            header("Location: ../../../login.php?inactivity=true");
        } else {
            header("Location: ../../../login.php");
        }

}