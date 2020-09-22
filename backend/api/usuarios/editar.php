<?php
session_start();
require_once '../db.php';

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $id = test_input($_POST['id']);
    $cargo = test_input($_POST['cargo']);
    $nombre = test_input($_POST['nombre']);
    $cedula = test_input($_POST['cedula']);
    $telefono = test_input($_POST['telefono']);
    $correo = test_input($_POST['correo']);

    if (isset($_POST['contrasena']) && $_POST['contrasena'] != "") {

        $contraseña = $_POST['contrasena'];
        $password_hash = password_hash($contraseña, PASSWORD_DEFAULT);

        $sql = "UPDATE USUARIOS SET CONTRASENA = ? WHERE ID = ?;";
        db_query($sql, array($password_hash, $id));
    }

    $sql = "UPDATE USUARIOS SET CARGO = ?, CEDULA = ?, NOMBRE = ?, TELEFONO = ?, CORREO = ?  WHERE ID = ?";
    db_query($sql, array($cargo, $cedula, $nombre, $telefono, $correo, $id));

    if ($id == $_SESSION['USUARIO']['ID']) {
        $sql = "SELECT * FROM USUARIOS WHERE ID = ?;";
        $result = db_query($sql, array($id));

        $_SESSION['USUARIO'] = array(
            'ID' => $result[0]['ID'],
            'CORREO' => $result[0]['CORREO'],
            'NOMBRE' => $result[0]['NOMBRE'],
            'CARGO' => $result[0]['CARGO']
        );
    }

    header("Location: ../../../usuarios.php");

}