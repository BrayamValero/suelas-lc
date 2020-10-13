<?php $protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://'; ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <!-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">  -->
    <link rel="icon" type="image/png" href="imagenes/misc/favicon.ico">

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
            <small class="form-text text-center text-small">Si olvidaste tu contraseña, has <a href="javascript:void(0);" data-toggle='modal' data-target='#recuperarClave' class="font-weight-bold text-danger">click aqui</a>.</small>
        </form>
    </div>

    <!-- Modal de Recuperar Clave -->
    <div class="modal fade" id="recuperarClave" tabindex="-1" role="dialog" aria-labelledby="recuperarClave" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fa fa-lock icon-color mr-1"></i> Recuperar Contraseña
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="recuperarClaveForm">
                        <div class="form-group">
                            <label for="correo" class="font-weight-bold">Correo Electrónico</label>
                            <input type="email" class="form-control" id="correo" name="correo" aria-describedby="emailHelp" placeholder="Correo Electrónico" required>
                            <small class="d-block text-muted mt-2">Se le enviará un correo electrónico con la clave asociada a la cuenta.</small>
                        </div>
                        <button type="button" id="botonRecuperarClave" class="btn btn-block btn-main">Recuperar Contraseña</button>
                    </form>
                </div>
            </div>
        </div>
    </div>


<!-- Fin de Modal de Ver Serie -->

<script>

var Login = (function checkLogin() {
    
    // Variables privadas
    var loginForm = $('#loginForm');
    var recuperarClaveForm = $('#recuperarClaveForm');

    // Object that's returned from the IIFE.
    return {

        iniciarSesion: function() {

            // Si el formulario tiene algún campo incorrecto y/o vacio, lanzar error.
            if(!loginForm[0].checkValidity()) return Swal.fire('Error', 'Por favor verifica todos los campos.', 'error');

            // $.post => Añadiendo el elemento al backend.
            $.post( 'backend/api/usuarios/login.php?action=LOGIN', loginForm.serialize(), function(data) {

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

        }, 

        recuperarClave: function() {

            // Si el formulario tiene algún campo incorrecto y/o vacio, lanzar error.
            if(!recuperarClaveForm[0].checkValidity()) return Swal.fire('Error', 'Por favor verifica el campo nuevamente.', 'error');

            // $.post => Añadiendo el elemento al backend.
            $.post( 'backend/api/usuarios/login.php?action=RECOVER', recuperarClaveForm.serialize(), function(data) {

                    console.log("object");
                    console.log(recuperarClaveForm.serialize());

                switch (data) {

                    case 'ERROR':
                        return Swal.fire('Error', 'El correo electrónico no se encuentra en nuestra base de datos.', 'error');
                        break;

                    case 'FAILED':
                        return Swal.fire('Error', 'El correo electrónico no se pudo enviar, intenta nuevamente.', 'error');
                        break;

                    default:

                        Swal.fire({
                            title: 'Exito',
                            text: 'Se ha enviado la clave a tu correo electrónico.',
                            icon: 'success',
                            timer: 2000,
                            timerProgressBar: true,
                            allowEscapeKey: false,
                            allowOutsideClick: false
                            }).then((result) => {
                                if ( result.dismiss === Swal.DismissReason.timer || result.value ){
                                    $('#recuperarClave').modal('hide');
                                }
                            });

                }	

            });

        }


    };
    
}());

// Iniciar Sesión => Al undir el click.
document.getElementById('botonLogin').addEventListener('click', function () {
    Login.iniciarSesion(); 
});

// Iniciar Sesión => Al undir el enter.
document.addEventListener('keyup', function (e) {
    if(e.code === 'Enter') Login.iniciarSesion();
});

// Recuperar Contraseña => Al undir el click.
document.getElementById('botonRecuperarClave').addEventListener('click', function () {
    Login.recuperarClave(); 
});

</script>

</body>
</html>

<?php
// Conexion Base de Datos.
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
if (!empty($_SESSION)) {
    header("Location: index.php");
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