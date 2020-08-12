<?php
session_start();
$action = strtoupper(trim($_GET['action']));

if ($action == 'UNLOGIN') {
    session_destroy();

    unset($_SESSION['USUARIO']);
    $_SESSION['USUARIO'] = null;

    if (isset($_GET['deslogueado']) && $_GET['deslogueado'] == '1') {
        header("Location: ../../../login.php?deslogueado=1");
    } else {
        header("Location: ../../../login.php");
    }

}

if (isset($_POST['correo']) && isset($_POST['contrasena'])) {

    require_once '../db.php';

    $correo = strtoupper(trim($_POST['correo']));
    $contrasena = trim($_POST['contrasena']);

    // LOGIN
    if ($action == "LOGIN") {

        $sql = "SELECT * FROM USUARIOS WHERE CORREO = ?";
        $data = array($correo);

        $result = db_query($sql, $data);

        if (empty($result)) {

            $_SESSION['LOGIN'] = "Credenciales invalidas";
            header("Location: ../../../login.php");

        }

        $verify = password_verify($contrasena, $result[0]['CONTRASENA']);

        if (!$verify) {
            $_SESSION['LOGIN'] = "Credenciales invalidas";
            header("Location: ../../../login.php");
        } else {
            $_SESSION['USUARIO'] = array(
                'ID' => $result[0]['ID'],
                'CORREO' => $result[0]['CORREO'],
                'NOMBRE' => $result[0]['NOMBRE'],
                'CARGO' => $result[0]['CARGO']
            );
            $session_id = session_id();

            $sql = "UPDATE USUARIOS SET SESSION_ID = ? WHERE ID = ?;";
            $data = array($session_id, $result[0]['ID']);
            db_query($sql, $data);


            header("Location: ../../../index.php");
        }
    } elseif ($action == "REGISTER") {
        echo "registrar";
        $nombre = trim(mb_strtoupper($_POST['nombre'], 'UTF-8'));
        $telefono = trim(mb_strtoupper($_POST['telefono'], 'UTF-8'));
        $cedula = trim($_POST['cedula']);
        $cargo = trim($_POST['cargo']);

        /* CHEQUEAR QUE NO EXISTA CORREO NI CEDULA */
        $duplicado = false;
        // Se verifica que el correo o la cedula no existan ya en la base de datos
        $sql = "SELECT * FROM USUARIOS WHERE CEDULA = ?";
        $data = array($cedula);
        $vCedula = db_query($sql, $data);

        // Si encontró coinciendia y no pertenece al ID que se vaya a editar (si se va a crear uno nuevo no se manda ID pero igual $vCedula[0]['ID'] != $_GET['id'] sirve) se manda el error
        if (!empty($vCedula) && $vCedula[0]['ID'] != $_GET['id']) {
            $_SESSION['REGISTER'] = 'CEDULA DUPLICADA';
            $duplicado = true;

            header("Location: ../../../usuarios.php");
        }

        $sql = "SELECT * FROM USUARIOS WHERE CORREO = ?";
        $data = array($correo);
        $vCorreo = db_query($sql, $data);

        // El mismo comentario anterior
        if (!empty($vCorreo) && $vCorreo[0]['ID'] != $_GET['id']) {
            $_SESSION['REGISTER'] = 'CORREO DUPLICADO';
            $duplicado = true;

            header("Location: ../../../usuarios.php");
        }

        if ($duplicado == false) {
            $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
            $data = array($nombre, $cedula, $correo, $telefono, password_hash($contrasena, PASSWORD_DEFAULT), $cargo);

            db_query($sql, $data);

            header("Location: ../../../usuarios.php");
        }
    }
}
  