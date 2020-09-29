<?php

//——————————————————————————————————————————————————————————————————————
// Define el protocolo a usar => HTTP o HTTPS.
//——————————————————————————————————————————————————————————————————————
$protocol = stripos($_SERVER['SERVER_PROTOCOL'],'https') === 0 ? 'https://' : 'http://';
define('BASE_URL', $protocol . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER["REQUEST_URI"] . '?') . '/');

//——————————————————————————————————————————————————————————————————————
// Iniciar la sesión => Monitoreo del tiempo de sesión actual.
//——————————————————————————————————————————————————————————————————————
session_start();
require_once 'backend/api/db.php';

# Si los datos se sesión se encuentran vacios, devolver al login.
if (empty($_SESSION)) {
    header("Location: login.php");
}

# Si la sesión ha durado más de 60 minutos, cerrar sesión.
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity'] > 60 * 60)) {
    header("Location: backend/api/usuarios/login.php?action=unlogin&inactivity=true");
}

# De lo contrario, actualizamos el timestamp al actualizar la página.
$_SESSION['last_activity'] = time();

?>

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
    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" href="datatables/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="css/dashboard.min.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap4.css">
    <link rel="stylesheet" href="css/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" media="print" href="css/print.min.css">

    <!-- JS -->
    <script src="js/jquery.min.js"></script>
    <script src="datatables/jquery.dataTables.min.js"></script>
    <script src="datatables/dataTables.buttons.min.js"></script>
    <script src="datatables/buttons.flash.min.js"></script>
    <script src="datatables/jszip.min.js"></script>
    <script src="datatables/pdfmake.min.js"></script>
    <script src="datatables/vfs_fonts.js"></script>
    <script src="datatables/buttons.html5.min.js"></script>
    <script src="datatables/buttons.print.min.js"></script>
    <script src="datatables/dataTables.bootstrap4.min.js"></script>
    <script src="js/select2.full.js"></script>
    <script src="js/jscolor.js"></script>
    <script src="js/sweetalert2.all.min.js"></script>

    <!-- Título -->

    <?php  if(isset($title)): ?>

        <title>Suelas LC | <?php echo $title; ?></title>

    <?php endif; ?>

    <title>Suelas LC</title>

    <script>

    Date.prototype.toDateInputValue = (function() {
        const local = new Date(this);
        local.setMinutes(this.getMinutes() - this.getTimezoneOffset());

        return local.toJSON().slice(0,10);
    });

    String.prototype.toProperCase = function () {
        return this.replace(/\w\S*/g, function (txt) {
            return txt.charAt(0).toUpperCase() + txt.substr(1).toLowerCase();
        });
    }

    // Toast Notification
    function toastNotifications(icon, icon_color, msg1, msg2){
        $('.toast-icon').removeClass().addClass(`toast-icon ${icon} ${icon_color} mr-2`);
        $('.toast-title').text(msg1);
        $('.toast-body').text(msg2);
        $('.toast').toast('show');
    }

    </script>

</head>
<body>
