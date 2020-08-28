<?php

// Incluimos el header.php y components.php
include 'components/header.php';
include 'components/components.php';

// Filtramos la página para que solo los cargos correspondientes puedan usarla.
if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'VENTAS'):

?>

<!-- Incluimos el sidebar.php -->
<?php include 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">

    <!-- Incluimos el Navbar -->
    <?php get_navbar('Pedidos', 'Añadir Pedido'); ?>

    <!-- Form -->
    <form id="añadirPedidoForm" action="backend/api/pedidos/añadir.php" method="post">

        <!-- Tabla de Datos -->
        <div class="tablaDatos shadow-sm">

            <!-- Datos del Cliente -->
            <h6 class="pb-3 font-weight-bold">
                <i class="fas fa-user-tie icon-color mr-2"></i> Datos del Cliente
            </h6>
            
            <div class="form-row pb-4">

                <div class="form-group col-lg-6 col-md-6">
                    <label for="añadirNombre">Nombre</label>
                    <select id="añadirNombre" name="nombre" class="form-control filter-select2" required>
                        <?php
                            require_once "backend/api/db.php";
                            $sql = "SELECT * FROM CLIENTES WHERE ACTIVO = 'SI';";
                            $result = db_query($sql);
                            foreach ($result as $row) {
                                echo "<option value='{$row['ID']}'>" . mb_convert_case($row['NOMBRE'], MB_CASE_TITLE, "UTF-8") . " - {$row['DOCUMENTO']} - {$row['DOCUMENTO_NRO']}</option>";
                            }
                            if(empty($result)){
                                echo "<option value=''>No hay clientes disponibles.</option>";
                            }
                        ?>
                    </select>
                </div>

                <div class="form-group col-lg-3 col-md-3">
                    <label for="añadirFecha">Fecha de Entrega</label>
                    <input id="añadirFecha" name="fecha" type="date" class="form-control" placeholder="Elige la fecha" required>
                </div>

                <div class="form-group col-lg-3 col-md-3">
                    <label for="añadirPago">Forma de Pago</label>
                    <select id="añadirPago" class="form-control filter-select2" name="pago" required>
                        <option value="CREDITO" selected>Credito</option>   
                        <option value="EFECTIVO">Efectivo</option>
                        <option value="CHEQUE">Cheque</option>
                        <option value="TARJETA">Tarjeta</option>
                        <option value="TRANSFERENCIA">Transferencia</option>
                    </select>
                </div>
            
            </div>
            <!-- Fin de Datos del Cliente -->
            
            <!-- Datos del Pedido -->
            <h6 class="pb-3 font-weight-bold">
                <i class="fas fa-shopping-bag icon-color mr-2"></i> Datos del Pedido
            </h6>

            <div class="form-row">

                <div class="form-group col-lg-6 col-md-6">
                    <label for="añadirSerie">Serie</label>
                    <select id="añadirSerie" class="form-control filter-select2" name="serie" required>
                        <?php
                            require_once "backend/api/db.php";
                            $sql = "SELECT * FROM SERIES;";
                            $result = db_query($sql);
                            foreach ($result as $row) {
                                echo "<option value='{$row['ID']}'>" . mb_convert_case($row['NOMBRE'], MB_CASE_TITLE, "UTF-8") . "</option>";
                            }
                            if(empty($result)){
                                echo "<option value=''>No hay series disponibles.</option>";
                            }
                        ?>
                    </select>
                </div>

                <div class="form-group col-lg-3 col-md-3">
                    <label for="añadirColor">Color</label>
                    <select id="añadirColor" class="form-control filter-select2" name="color" required>
                        <?php
                            require_once "backend/api/db.php";
                            $sql = "SELECT * FROM COLOR;";
                            $result = db_query($sql);
                            foreach ($result as $row) {
                                echo "<option value='{$row['ID']}'>" . mb_convert_case($row['COLOR'], MB_CASE_TITLE, "UTF-8") . "</option>";
                            }
                            if(empty($result)){
                                echo "<option value=''>No hay colores disponibles.</option>";
                            }
                        ?>
                    </select>
                </div>

                <div class="form-group col-lg-3 col-md-3">
                    <label for="botonAñadirSerie" class="hide-options">Opciones</label>
                    <button id="botonAñadirSerie" type="button" class="btn btn-main btn-block">Añadir Serie</button>
                </div>

            </div>
            <!-- Fin de Datos del Pedido -->

        </div>
        <!-- Fin de Tabla de Datos -->
        
        <!-- Tabla de Pedidos -->
        <div class="tablaPedidos shadow-sm mt-4">

            <h6 class="font-weight-bold mb-3">
                <i class="fas fa-shopping-bag icon-color mr-2"></i> Pedido
            </h6>
            
            <!-- Contenedor de Pedidos / JavaScript .clear -->
            <div class="contenedorPedidos"></div>
            <!-- / fin de contenedor de Serie -->
            
            <button id="botonFinalizarPedido" type="submit" class="btn btn-main btn-block mt-3">Finalizar Pedido</button>
        
        </div>
        <!-- Fin de Tabla de Pedidos -->

    </form>
    <!-- Fin del Form -->
    
</div>
<!-- / Fin de Contenido -->

<!-- Inline JavaScript -->
<script>

// select2 plugin: https://github.com/select2
$(document).ready(function () {
    $('.filter-select2').select2({
        theme: "bootstrap4",
    });
});

// Datos del cliente.
var i = 0;
var j = 0;
var verificadorSerie = [];
var obtenerColor, obtenerSerie; 

const añadirNombre = document.getElementById('añadirNombre');
const añadirFecha = document.getElementById('añadirFecha');
const añadirPago = document.getElementById('añadirPago');
const añadirSerie = document.getElementById('añadirSerie');
const añadirColor = document.getElementById('añadirColor');
const contenedorPedidos = document.getElementById('contenedorPedidos');
const botonAñadirSerie = document.getElementById('botonAñadirSerie');
const botonFinalizarPedido = document.getElementById('botonFinalizarPedido');

// Asignando la fecha al input fecha.
añadirFecha.min = new Date().toDateInputValue();
añadirFecha.value = new Date().toDateInputValue();


// Botón de añadir Serie al Pedido
botonAñadirSerie.addEventListener('click', function () {

    let serieId = añadirSerie.value;
    let colorId = añadirColor.value;
    let agregarSerie = { "SERIE_ID": serieId, "COLOR_ID": colorId };

    // Se verifica que haya un elemento en el array de series, luego se compara la selección con el array en cuestión para cerciorarse que no hayan repetidos.
    if(verificadorSerie.length !== 0){

        var verif = verificadorSerie.some(serie => serie.SERIE_ID === serieId && serie.COLOR_ID === colorId);

        if(verif){
            return Swal.fire("Whoops", "No puedes asignar la misma serie con el mismo color.", "warning");
        }

        verificadorSerie.push(agregarSerie);

    } else {

        verificadorSerie.push(agregarSerie);

    }

    // COLOR_ID
    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerColor',
        async: false,
        data: `id=${colorId}`,
        success: function (data) {

            obtenerColor = JSON.parse(data);

        }
    });
    
    // SERIE_ID
    $.ajax({
        type: 'get',
        url: `backend/api/utils.php?fun=obtenerGrupoSerie&id=${serieId}`,
        async: false,
        success: function (data) {

            obtenerSerie = JSON.parse(data);
            // console.log(obtenerSerie);

        }
    });
    
    // Realizando formulas para la personalización de colores.
    let color = obtenerColor[0].COLOR.toProperCase();
    let backgroundHex = obtenerColor[0].CODIGO;

    let red = parseInt(backgroundHex.substring(1, 3), 16);
    let green = parseInt(backgroundHex.substring(3, 5), 16);
    let blue = parseInt(backgroundHex.substring(5, 7), 16);

    let colorHex = red * 0.299 + green * 0.587 + blue * 0.114 > 186 ? '#000000' : '#FFFFFF';

    // AÑADIENDO LA SERIE -> NOMBRE SERIE, COLOR Y RANGO
    $('.contenedorPedidos').append(`
        <div id="serie-${i}" class="contenedor-serie shadow-sm" data-serie-id="${serieId}" data-color-id="${colorId}">
            <div class="form-row">
                <div class="col-8">
                    <strong>${obtenerSerie[0].MARCA.toProperCase()}</strong>
                    <span class="badge border" style="background-color: ${backgroundHex}; color: ${colorHex};">${color}</span>
                    <small class="text-muted">${obtenerSerie[0].TALLA} al ${obtenerSerie[obtenerSerie.length - 1].TALLA}</small>
                </div>
                <div class="col-4">
                    <button type="button" class="close eliminarSerie" data-id="${i}" data-serie-id="${serieId}" data-color-id="${colorId}" tabIndex="-1">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <button type="button" class="close esconderSerie" data-id="${i}" tabIndex="-1">
                        <span aria-hidden="true" class="mr-2">&minus;</span>
                    </button>
                </div>
            </div>
            <div id="grupoSeries-${i}" class="form-row text-center">
            </div>
        </div>        
    `);

    obtenerSerie.forEach(serie => {

        $('#grupoSeries-' + i).append(`
            <div class="form-group col mb-0 mt-2">
                <label class="label-cantidades" for="cantidades">${serie.TALLA}</label>
                <input class="form-control input-cantidades" type="number" name="pedido[${j}][cantidad]" min="0" required>
            </div>
            <input type="hidden" name="pedido[${j}][suela_id]" value="${serie.SUELA_ID}">
            <input type="hidden" name="pedido[${j}][serie_id]" value="${serieId}">
            <input type="hidden" name="pedido[${j}][color_id]" value="${colorId}">

        `);

        j++;

    });

    i++;

});

// Event Delegation = Esconder Serie.
$(document).on('click', '.esconderSerie', function() {

    let columnaId = $(this).data('id');
    let serie = document.getElementById(`grupoSeries-${columnaId}`);

    if (serie.style.display === 'none') {
        serie.style.display = 'flex';
    } else {
        serie.style.display = 'none';
    }

});

// Event Delegation = Borrar Serie.
$(document).on('click', '.eliminarSerie', function(e) {

    let serieId = $(this).data('serie-id');
    let colorId = $(this).data('color-id');
    let columnaId = $(this).data('id');

    for (let i = 0; i < verificadorSerie.length; i++) {

        if ((verificadorSerie[i].SERIE_ID == serieId) && (verificadorSerie[i].COLOR_ID == colorId)){
            
            verificadorSerie.splice(i, 1);
            // console.clear();
            // console.log(verificadorSerie);

        }

    }

    $('#serie-' + columnaId).remove();

});

// Verificar Pedido
botonFinalizarPedido.addEventListener("click", function(){

    // Comprobar que haya alguna serie ingresada en el sistema.
    if (Object.entries(verificadorSerie).length === 0) {
        event.preventDefault();
        return Swal.fire("Error", "Debes agregar al menos (1) serie al pedido.", "warning");
    }

});

$(document).ready(function () {
    $("#añadirPedidoForm").submit(function () {
        $("#botonFinalizarPedido").attr("disabled", true);
        return true;
    });
});

// Comprobar que hayan Clientes disponibles.
if( (añadirSerie.value == null || añadirSerie.value == '') || (añadirColor.value == null || añadirColor.value == '') ){
    document.getElementById('buttonAñadirSerie').disabled = true;
}

// Comprobar que hayan Series o Colores disponibles.
if(añadirNombre.value == null || añadirNombre.value == ''){
    submitPedido.disabled = true;
}

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