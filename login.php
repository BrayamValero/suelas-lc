<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> 
    <link rel="icon" type="image/png" href="images/favicon.ico">

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
        <form id="loginForm">
            <h1 class="text-center text-title">Bienvenido</h1>
            <p class="text-center text-subtitle">Suelas LC</p>
            <div class="form-group pt-3">
                <input type="text" name="username" class="form-control" id="loginUsername" placeholder="Usuario" required>
            </div>
            <div class="form-group pb-3">
                <input name="password" type="password" class="form-control" id="loginPassword" placeholder="Contraseña" required>
            </div>
            <div class="text-center pb-2">
                <button type="button" class="btn btn-main btn-block" id="botonLogin">Iniciar Sesión</button>
            </div>
            <small class="form-text text-center text-small">Si olvidaste tus datos puedes pedirselos a un Administrador.</small>
        </form>
    </div>
</div>

<script>

var Login = (function checkLogin() {
    
    // Variable privada
    var formulario = $('#loginForm');

    // Object that's returned from the IIFE.
    return {

        loginAttemp: function() {

            // Si el formulario tiene algún campo incorrecto y/o vacio, lanzar error.
            if(!formulario[0].checkValidity()) return Swal.fire('Error', 'Por favor verifica todos los campos.', 'error');

            // $.post => Añadiendo el elemento al backend.
            $.post( 'backend/api/usuarios/login.php?action=LOGIN', formulario.serialize(), function(data) {

                switch (data) {

                    case 'ERROR':
                        return Swal.fire('Error', 'Usuario y/o Contraseña invalidos.', 'error');
                        break;

                    default:

                        Swal.fire({
                            title: 'Bienvenido',
                            text: 'En unos momentos te redirigimos al inicio.',
                            icon: 'success',
                            timer: 2000,
                            timerProgressBar: true,
                            allowEscapeKey: false,
                            allowOutsideClick: false
                            }).then((result) => {
                                if ( result.dismiss === Swal.DismissReason.timer || result.value ){
                                    location.href = 'index.php';
                                }
                            });

                }	

            });

        }

    };
    
}());

// Iniciar Sesión => Al undir el click.
document.getElementById('botonLogin').addEventListener('click', function () {
    Login.loginAttemp(); 
});

// Iniciar Sesión => Al undir el enter.
document.addEventListener('keyup', function (e) {
    if(e.code === 'Enter') Login.loginAttemp();
});

</script>

</body>
</html>

<?php
session_start();
require_once 'backend/api/db.php';

// Comprobamos si hay al menos un usuario administrador.
$sql = "SELECT COUNT(*) AS CONTEO FROM USUARIOS;";
$result = db_query($sql);

// Si no hay ni 1 cuenta creada se crea un usuario ADMINISTRADOR.
if ($result[0]['CONTEO'] == 0) {

    $sql = "INSERT INTO USUARIOS VALUES (NULL, ?, ?, ?, ?, ?, ?, NOW(), NOW(), 'SI', NULL);";
    $data = array('ADMINISTRADOR', '123456789', 'admin@suelaslc.com', '123456789', password_hash('admin123', PASSWORD_DEFAULT), 'ADMINISTRADOR');
    db_query($sql, $data);

}

// Si hay un usuario en SESSION -> Enviar a index.php
if (!empty($_SESSION['USUARIO'])) {
    header("Location: login.php");
}

// Si está setteado inactivity y este es true, lanzar alerta.
if (isset($_GET['inactivity']) && $_GET['inactivity'] == 'true') {
    echo "<script type='text/javascript'>
            $(document).ready(function(){
                Swal.fire('Alerta', 'Su sesión ha caducado.', 'warning');
            });
        </script>";
}

?>