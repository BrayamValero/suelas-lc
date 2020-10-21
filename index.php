<?php

// Incluimos el header.php y components.php
$title = 'Inicio';
require_once 'components/header.php';
require_once 'components/navbar.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE');

if(!in_array($_SESSION['ROL'], $roles_permitidos)){
    require_once 'components/error.php';
    require_once 'components/footer.php';
    exit();
}

?>

<!-- Incluimos el sidebar.php -->
<?php require_once 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">

    <!-- Incluimos el Navbar -->
    <?php get_navbar('Inicio', 'Panel Principal', false); ?>

    <!-- <div class="card-deck mt-2"> -->
        
        <div class="row">
            <div class="col-md-6">
                <!-- Contraseñas -->
                <div class="card bg-white mb-3">
                    <div class="card-header font-weight-bold">Opciones <span class="badge badge-danger">Deshabilitado</span></div>
                    <div class="card-body">
                        <h5 class="card-title">Cambiar Clave</h5>
                        <p class="card-text text-muted">Permite cambiar tu clave actual.</p>

                        <div class="form-row">
                            <div class="form-group col-12">
                                <label for="añadirClaveAntigua">Clave antigua <span class="text-danger font-weight-bold">*</span></label>
                                <input id="añadirClaveAntigua" name="clave-antigua" type="password" class="form-control">
                            </div>
                            <div class="form-group col-12">
                                <label for="añadirClaveNueva">Nueva clave <span class="text-danger font-weight-bold">*</span></label>
                                <input id="añadirClaveNueva" name="clave-nueva" type="password" class="form-control">
                            </div>
                            <div class="form-group col-12">
                                <label for="añadirClaveNuevaRepetir">Repita la clave nueva<span class="text-danger font-weight-bold">*</span></label>
                                <input id="añadirClaveNuevaRepetir" name="clave-nueva-repetir" type="password" class="form-control">
                            </div>
                        </div>

                    </div>
                    <div class="card-footer">
                        <button type="button" class="btn btn-sm btn-main">Cambiar contraseña</button>
                    </div>
                </div>
            </div>
        </div>
        
    <!-- </div> -->

</div>
<!-- Fin de contenido -->

<!-- Inline JavaScript -->
<script>
</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>