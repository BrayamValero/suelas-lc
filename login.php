<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">

    <!-- CSS -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/fontawesome-all.min.css">
    <link rel="stylesheet" href="css/sweetalert2.min.css">
    <link rel="stylesheet" href="css/login.min.css">

    <!-- JavaScript -->
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.js"></script>
    <script src="js/sweetalert2.all.min.js"></script>
    
    <title>Suelas LC | Iniciar Sesión</title>

</head>
<body>
<div class="container-fluid">
    <div class="form-container">
        <form action="backend/api/usuarios/login.php?action=LOGIN" method="POST">
            <h1 class="text-center text-title">Bienvenido</h1>
            <p class="text-center text-subtitle">Suelas LC</p>
            <div class="form-group pt-3">
                <input type="text" name="correo" class="form-control" id="inputUsuario" placeholder="Correo" required>
            </div>
            <div class="form-group pb-3">
                <input name="contrasena" type="password" class="form-control" id="inputContraseña" placeholder="Contraseña" required>
            </div>
            <div class="text-center pb-2">
                <button type="submit" class="btn btn-main" style="width: 360px;">Iniciar Sesión</button>
            </div>
            <small id="emailHelp" class="form-text text-center text-small">Si olvidaste tus datos puedes pedirselos a un Administrador.</small>
        </form>
    </div>
</div>
</body>
</html>


<?php
session_start();
require_once 'backend/api/db.php';

// 1. Comprobamos si hay al menos un usuario administrador.
$sql = "SELECT COUNT(*) AS CONTEO FROM USUARIOS;";
$result = db_query($sql);

// Si no hay ni 1 cuenta creada se crea un usuario ADMINISTRADOR.
if ($result[0]['CONTEO'] == 0) {

    $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
    $data = array('ADMINISTRADOR', '123456789', 'admin@suelaslc.com', '123456789', password_hash('admin123*', PASSWORD_DEFAULT), 'ADMINISTRADOR');
    db_query($sql, $data);

}

// Si hay un usuario en SESSION -> Enviar a index.php
if (isset($_SESSION['USUARIO'])) {
    header("Location: index.php");
}

// Si está setteado el 'deslogueado' y  este es igual a '1', lanzar alerta.
if (isset($_GET['deslogueado']) && $_GET['deslogueado'] == '1') {
    echo "<script type='text/javascript'>
            $(document).ready(function(){
                Swal.fire('Alerta', 'Su sesión ha caducado.', 'warning');
            });
        </script>";
}

?>

<?php

if (isset($_SESSION['LOGIN']) && $_SESSION['LOGIN'] == "Credenciales invalidas"){
    
    $_SESSION['LOGIN'] = null;
    unset($_SESSION['LOGIN']);
    
    echo "<script type='text/javascript'>
            $(document).ready(function(){
                Swal.fire('Error', 'Usuario y/o contraseña inválidos.', 'error');
            });
        </script>";
}

?>