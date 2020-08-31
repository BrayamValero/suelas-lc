<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <!-- CSS -->
    <link rel="stylesheet" href="css/bootstrap.css">
    <link rel="stylesheet" href="css/fontawesome-all.min.css">
    <link rel="stylesheet" href="css/login.css">
    <!-- JavaScript -->
    <script src="js/sweetalert.min.js"></script>
    <!-- Title -->
    <title>Suelas LC | Iniciar Sesión</title>
</head>
<body>
<div class="container-fluid">
    <!-- Form -->
    <div class="form-container">
        <form action="backend/api/usuarios/login.php?action=LOGIN" method="POST">
            <h1 class="text-center text-title">Bienvenido</h1>
            <p class="text-center text-subtitle">Industrias de Suelas LC</p>
            <div class="form-group pt-3"
            ">
            <input name="correo" type="email" class="form-control" id="inputUsuario" placeholder="Correo" required>
    </div>
    <div class="form-group pb-3">
        <input name="contrasena" type="password" class="form-control" id="inputContraseña" placeholder="Contraseña"
               required>
    </div>
    <div class="text-center pb-2">
        <button type="submit" class="btn btn-main" style="width: 360px;">Iniciar Sesión</button>
    </div>
    <small id="emailHelp" class="form-text text-center text-small">Si olvidaste tus datos puedes pedirselos a un
        Administrador.</small>
    </form>
</div>
<!-- End of form -->
</div>
<!-- JavaScript -->
    <script src="js/jquery.min.js"></script>
    <script src="js/popper.min.js"></script>
    <script src="js/bootstrap.js"></script>

    <!-- Header -->
    <?php
    session_start();
    require_once 'backend/api/db.php';

    $sql = "SELECT COUNT(*) AS CONTEO FROM USUARIOS;";
    $result = db_query($sql);

    if ($result[0]['CONTEO'] == 0) {
        header("Location: backend/create_users.php");
    }

    if (isset($_SESSION['USUARIO'])) {
        header("Location: index.php");
    }

    if (isset($_GET['deslogueado']) && $_GET['deslogueado'] == '1') {
        echo "<script>Swal.fire('Aviso', 'Se ha cerrado su sesión previa.','warning');</script>";
    }
    ?>

    <?php
        if (isset($_SESSION['LOGIN']) && $_SESSION['LOGIN'] == "Credenciales invalidas"):
            $_SESSION['LOGIN'] = null;
            unset($_SESSION['LOGIN']);
    ?>

    <script>

    // SweetAlert - Aviso de error.
    Swal.fire({
        title: "Error",
        text: "Usuario o contraseña inválidos.",
        icon: "error",
    });

    </script>

    <?php endif; ?>

</body>
</html>