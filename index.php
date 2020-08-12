<?php

// Incluimos el header.php y components.php
include 'components/header.php';
include 'components/components.php';

// Filtramos la página para que solo los cargos correspondientes puedan usarla.
if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' ||
    $_SESSION['USUARIO']['CARGO'] == 'VENTAS' ||
    $_SESSION['USUARIO']['CARGO'] == 'MOLINERO' ||
    $_SESSION['USUARIO']['CARGO'] == 'OPERARIO' ||
    $_SESSION['USUARIO']['CARGO'] == 'PRODUCCION' ||
    $_SESSION['USUARIO']['CARGO'] == 'DESPACHO' ||
    $_SESSION['USUARIO']['CARGO'] == 'CONTROL' ||
    $_SESSION['USUARIO']['CARGO'] == 'NORSAPLAST' ||
    $_SESSION['USUARIO']['CARGO'] == 'CLIENTE'):
?>

<!-- Incluimos el sidebar.php -->
<?php include 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">

    <!-- Incluimos el Navbar -->
    <?php get_navbar('Inicio', 'Panel Principal'); ?>

</div>
<!-- Fin de contenido -->

<!-- Inline JavaScript -->
<script>
</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>

<!-- En Caso de no poseer derechos, incluir error.php-->
<?php 
    else:
    include 'components/error.php';
    include_once 'components/footer.php';
    exit();
?>

<!-- Fin del filtro -->
<?php
    endif;
?>