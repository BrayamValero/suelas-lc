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
    <form action="backend/api/pedidos/añadir.php" method="POST">

        <!-- Tabla de Datos -->
        <div class="tablaDatos shadow-lg">

            <!-- Datos del Cliente -->
            <h6 class="pb-3 font-weight-bold">
                <i class="fas fa-user-tie icon-color mr-2"></i> Datos del Cliente
            </h6>
            
            <div class="form-row pb-4">

                <div class="form-group col-lg-6">
                    <label for="inputAñadirNombre">Nombre</label>
                    <select id="inputAñadirNombre" name="nombre" class="form-control filter-select2" required>
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

                <div class="form-group col-lg-3">
                    <label for="inputAñadirFecha">Fecha de Entrega</label>
                    <input id="inputAñadirFecha" name="fecha" type="date" class="form-control" placeholder="Elige la fecha" required>
                </div>

                <div class="form-group col-lg-3">
                    <label for="inputAñadirPago">Forma de Pago</label>
                    <select id="inputAñadirPago" class="form-control filter-select2" name="pago" required>
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

                <div class="form-group col-lg-6">
                    <label for="inputAñadirReferencia">Serie</label>
                    <select id="inputAñadirReferencia" class="form-control filter-select2" name="serie" required>
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

                <div class="form-group col-lg-3">
                    <label for="inputAñadirColor">Color</label>
                    <select id="inputAñadirColor" class="form-control filter-select2" name="color" required>
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

                <div class="form-group col-lg-3">
                    <label for="buttonAñadirSerie" class="hide-options">Opciones</label>
                    <button id="buttonAñadirSerie" type="button" class="btn btn-main btn-block">Añadir Serie</button>
                </div>

            </div>
            <!-- Fin de Datos del Pedido -->

        </div>
        <!-- Fin de Tabla de Datos -->
        
        <!-- Tabla de Pedidos -->
        <div class="mt-4 tablaPedidos shadow-lg">

            <h6 class="font-weight-bold">
                <i class="fas fa-shopping-bag icon-color mr-2"></i> Pedido
            </h6>

            <div class="rowPedidos">

            </div>

            <button id="submitPedido" type="submit" class="btn btn-main btn-block mt-3">Finalizar Pedido</button>
        
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

// Declaramos las variables y constantes que se van a usar.
var i = 1;
var j = 1;
var obtenerColor, compararSeries = [];

// Datos del cliente.
const añadirNombre = document.getElementById('inputAñadirNombre');
const añadirFecha = document.getElementById('inputAñadirFecha');
const añadirPago = document.getElementById('inputAñadirPago');

// Datos del pedido.
const añadirReferencia = document.getElementById('inputAñadirReferencia');
const añadirColor = document.getElementById('inputAñadirColor');
const submitPedido = document.getElementById('submitPedido');

// Asignando la fecha al input fecha.
añadirFecha.min = new Date().toDateInputValue();
añadirFecha.value = new Date().toDateInputValue();

// Evento de creación de los pedidos.
document.getElementById('buttonAñadirSerie').addEventListener('click', function () {

    // Obtenemos los IDs de "serie" y "color".
    let serieId = añadirReferencia.value;
    let colorId = añadirColor.value;

    if(serieId && colorId){
        
        // Obtenemos el respectivo "color" dependiendo del ID.
        $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerColor',
        async: false,
        data: `id=${colorId}`,
        success: function (data) {

            obtenerColor = JSON.parse(data);

            }
        });

        // Creamos un objeto para almacenarlo dentro de un array.
        let agregarSerie = { "SERIE_ID": serieId, "COLOR_ID": colorId };

        if (typeof compararSeries !== 'undefined' && compararSeries.length > 0) {

            for (let i = 0; i < compararSeries.length; i++) {

                if ((compararSeries[i].SERIE_ID == serieId) && (compararSeries[i].COLOR_ID == colorId)){
                    
                    console.log(compararSeries);
                    return swal("Whoops", "No puedes asignar la misma serie con el mismo color.", "warning");

                }
    
            }
            
            compararSeries.push(agregarSerie);

        } else {

            compararSeries.push(agregarSerie);

        }

        // Obtenemos la respectiva "serie" dependiendo del ID.
        $.ajax({
        type: 'get',
        url: `backend/api/utils.php?fun=obtenerGrupoSerie&id=${serieId}`,
        success: function (data) {

            const result = JSON.parse(data);

            let colorHex;
            let color = obtenerColor[0].COLOR;
            let backgroundHex = obtenerColor[0].CODIGO;

            let red = parseInt(backgroundHex.substring(0, 2), 16);
            let green = parseInt(backgroundHex.substring(2, 4), 16);
            let blue = parseInt(backgroundHex.substring(4, 6), 16);

            if ((red*0.299 + green*0.587 + blue*0.114) > 186){
                colorHex = "000000";
            } else {
                colorHex = "FFFFFF";
            }

            $('.rowPedidos').append(
            `<div id="row-${i}" class="bordered-container p-1 mt-3">
            
                <div class="header p-3">

                    <div class="row" data-serie-id="${serieId}" data-color-id="${colorId}">
                        <div class="col-8">
                            <strong>${result[0].MARCA.toProperCase()}</strong>
                            <span class="badge badge-pill" style="color: #${colorHex}; background: #${backgroundHex};">${color.toProperCase()}</span>
                            <small class="text-muted">${result[0].TALLA} al ${result[result.length - 1].TALLA}</small>
                        </div>

                        <div class="col-4">
                            <button type="button" class="close eliminarSerie" data-id="${i}" tabIndex="-1">
                                <span aria-hidden="true">&times;</span>
                            </button>
                            <button type="button" class="close esconderSerie" data-id="${i}" tabIndex="-1">
                                <span aria-hidden="true" class="mr-2">&minus;</span>
                            </button>
                        </div>
                    </div>

                </div>

                <div id="serie-${i}" class="px-3">
                </div>

            </div>`);

            result.forEach(row => {

                $(`#serie-${i}`).append(
                `<div class="form-row">

                    <div class="form-group col-8">
                        <div class="input-group mb-2">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    <input class="single-checkbox" type="checkbox" name="pedido[${j}][urgente]" tabIndex="-1" value="1">
                                </div>
                            </div>
                            <input class="form-control" type="text" value="${row.MARCA.toProperCase()} - ${row.TALLA}" tabIndex="-1" readonly>
                        </div>
                    </div>

                    <!-- Data Marca Escondida -->
                    <input class="form-control" type="hidden" name="pedido[${j}][suela_id]" value="${row.ID}">

                    <!-- Data Serie ID Escondida -->
                    <input class="form-control" type="hidden" name="pedido[${j}][serie_id]" value="${serieId}">

                    <!-- Data Color Escondida -->
                    <input class="form-control" type="hidden" name="pedido[${j}][color_id]" value="${colorId}">

                    <div class="form-group col-4">
                        <input class="form-control" type="number" name="pedido[${j}][cantidad]" placeholder="Cantidad" min="0" required>
                    </div>
                
                </div>`);

                j++;

            });

            // Contador
            i++; 

            }

        });

    }

});

// Event Delegation = Esconder Serie.
$(document).on('click', '.esconderSerie', function() {

    let columnaId = $(this).data('id');
    let serie = document.getElementById(`serie-${columnaId}`);

    if (serie.style.display === 'none') {
        serie.style.display = 'block';
    } else {
        serie.style.display = 'none';
    }

});

// Event Delegation = Borrar Serie.
$(document).on('click', '.eliminarSerie', function(e) {

    let serieId = $(e.target.parentElement.parentElement.parentElement).data('serie-id');
    let colorId = $(e.target.parentElement.parentElement.parentElement).data('color-id');

    for (let i = 0; i < compararSeries.length; i++) {

        if ((compararSeries[i].SERIE_ID == serieId) && (compararSeries[i].COLOR_ID == colorId)){
            
            compararSeries.splice(i, 1);

            console.log(compararSeries);

        }

    }

    let columnaId = $(this).data('id');

    $('#row-' + columnaId).remove();

});

// Event Delegation = Limitar Checkboxes.
$(document).on('change','input.single-checkbox', function() {

    if ($('input.single-checkbox:checked').length > 3) {

        $(this).prop('checked', false);

        swal("Whoops", "Solo puedes seleccionar hasta (3) marcas.", "warning");

    }

});

// Deshabilitar el botón "Finalizar Pedido"
document.getElementById('submitPedido').addEventListener("click", function(){

    // Comprobar que haya alguna serie ingresada en el sistema.
    if (Object.entries(compararSeries).length === 0) {

        event.preventDefault();
        
        return swal("Whoops", "Debes agregar al menos (1) pedido.", "warning");

    }

});

// Comprobar que hayan Clientes disponibles.
if( (añadirReferencia.value == null || añadirReferencia.value == '') || (añadirColor.value == null || añadirColor.value == '') ){
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