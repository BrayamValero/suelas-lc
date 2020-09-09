<?php
session_start();
define('BASE_URL', "https://" . $_SERVER['SERVER_NAME'] . ':' . $_SERVER['SERVER_PORT'] . dirname($_SERVER["REQUEST_URI"] . '?') . '/');

function check_user( $session_id, $user_id ) {
    require_once 'backend/api/db.php';
    $sql = "SELECT * FROM USUARIOS WHERE ID = ?;";
    $usuario = db_query($sql, array($user_id));

    if ($usuario[0]['SESSION_ID'] != $session_id) {
        header("Location: backend/api/usuarios/login.php?action=unlogin&deslogueado=1");
    }
}

if (!isset($_SESSION['USUARIO'])) {
    header("Location: login.php");
}

check_user(session_id(), $_SESSION['USUARIO']['ID']);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no, shrink-to-fit=no">
    <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> 

    <!-- CSS -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/fontawesome-all.min.css">
    <link rel="stylesheet" href="css/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" href="datatables/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="css/dashboard.css">
    <link rel="stylesheet" href="css/select2.css">
    <link rel="stylesheet" href="css/select2-bootstrap4.css">
    <link rel="stylesheet" href="css/sweetalert2.min.css">
    <link rel="stylesheet" type="text/css" media="print" href="css/print.css">

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

    // Fix Select2 with MODAL
    $(document).ready(function () {
        $.fn.modal.Constructor.prototype._enforceFocus = function () { };
    });

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

    // ajaxPost(url, data, async).done(function(data){});
    function ajaxPost(url, data, async){
        return $.ajax({
            url:            url,
            data:           data,
            cache:          false,
            async:          async,
            type:           'post',
            dataType:       'json'
        });             
    }

    // ajaxGet(url, async).done(function(data){});
    function ajaxGet(url, async){
        return $.ajax({
            url:            url,
            cache:          false,
            async:          async,
            type:           'get',
            dataType:       'json'
        });             
    }    

    </script>

</head>
<body>
