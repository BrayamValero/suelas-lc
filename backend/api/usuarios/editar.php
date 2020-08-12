<?php
session_start();
require_once '../db.php';

$cargo = trim(mb_strtoupper($_POST['cargo'], 'UTF-8'));
$cedula = trim(mb_strtoupper($_POST['cedula'], 'UTF-8'));
$nombre = trim(mb_strtoupper($_POST['nombre'], 'UTF-8'));
$telefono = trim(mb_strtoupper($_POST['telefono'], 'UTF-8'));
$correo = trim(mb_strtoupper($_POST['correo'], 'UTF-8'));
$id = trim(mb_strtoupper($_POST['id'], 'UTF-8'));

if (isset($_POST['contraseña']) && $_POST['contraseña'] != "") {
    $contraseña = trim($_POST['contraseña']);
    $password_hash = password_hash($contraseña, PASSWORD_DEFAULT);

    $sql = "UPDATE USUARIOS SET CONTRASENA = ? WHERE ID = ?;";
    db_query($sql, array($password_hash, $id));
}


$sql = "UPDATE USUARIOS SET CARGO = ?, CEDULA = ?, NOMBRE = ?, TELEFONO = ?, CORREO = ?  WHERE ID = ?";
$data = array($cargo, $cedula, $nombre, $telefono, $correo, $id);
db_query($sql, $data);

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