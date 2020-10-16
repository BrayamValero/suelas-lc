<?php

// Incluimos el header.php y components.php
$title = 'Referencias';
require_once 'components/header.php';
require_once 'components/navbar.php';
require_once 'backend/api/utils.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS');

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
    <?php get_navbar('Pedidos', 'Añadir Pedido'); ?>

    <!-- Form -->
    <form id="añadirPedidoForm">

        <!-- Tabla de Datos -->
        <div class="tablaDatos shadow-sm">

            <!-- Datos del Cliente -->
            <h6 class="pb-3 font-weight-bold">
                <i class="fas fa-user-tie icon-color mr-2"></i> Datos del Cliente
            </h6>
            
            <div class="form-row pb-4">

                <div class="form-group col-lg-6 col-md-6">
                    <label for="añadirNombre">Nombre</label>
                    <select id="añadirNombre" name="nombre" class="form-control dropdown-select2" required>
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
                    <label for="añadirFecha">Fecha Estimada</label>
                    <input id="añadirFecha" name="fecha" type="date" class="form-control" placeholder="Elige la fecha" required>
                </div>

                <div class="form-group col-lg-3 col-md-3">
                    <label for="añadirPago">Forma de Pago</label>
                    <select id="añadirPago" class="form-control dropdown-select2" name="pago" required>
                        <?php
                            foreach (FORMAS_PAGO as $forma_pago) {
                                echo "<option value='$forma_pago'>". mb_convert_case($forma_pago, MB_CASE_TITLE) ."</option>";
                            }
                        ?>
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
                    <select id="añadirSerie" class="form-control dropdown-select2" name="serie" required>
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
                    <select id="añadirColor" class="form-control dropdown-select2" name="color" required>
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

            <h6 class="font-weight-bold mb-3" id="inicioPedidos">
                <i class="fas fa-shopping-bag icon-color mr-2"></i> Pedido
            </h6>
            
            <button id="botonAñadirPedido" type="button" class="btn btn-main btn-block mt-3">Finalizar Pedido</button>
        
        </div>
        <!-- Fin de Tabla de Pedidos -->

    </form>
    <!-- Fin del Form -->
    
</div>
<!-- / Fin de Contenido -->

<!-- Inline JavaScript -->
<script>

// Datos del cliente.
var i = 1;
var j = 1;
var verificadorSerie = [];
var obtenerColor, obtenerSerie; 

const añadirNombre = document.getElementById('añadirNombre');
const añadirFecha = document.getElementById('añadirFecha');
const añadirPago = document.getElementById('añadirPago');
const añadirSerie = document.getElementById('añadirSerie');
const añadirColor = document.getElementById('añadirColor');
const botonAñadirSerie = document.getElementById('botonAñadirSerie');
const botonAñadirPedido = document.getElementById('botonAñadirPedido');

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

        }
    });
    
    // Realizando formulas para la personalización de colores.
    let color = obtenerColor[0].COLOR.toProperCase();
    let backgroundHex = obtenerColor[0].CODIGO;

    let red = parseInt(backgroundHex.substring(1, 3), 16);
    let green = parseInt(backgroundHex.substring(3, 5), 16);
    let blue = parseInt(backgroundHex.substring(5, 7), 16);

    let colorHex = red * 0.299 + green * 0.587 + blue * 0.114 > 186 ? '#000000' : '#FFFFFF';

    document.getElementById('inicioPedidos').insertAdjacentHTML('afterend', 
    `<div id="serie-${i}" class="contenedor-serie shadow-sm" data-serie-id="${serieId}" data-color-id="${colorId}">
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
        <div id="grupoSeries-${i}" class="form-row text-center"></div>
    </div>`);

    obtenerSerie.forEach(serie => {

        document.getElementById('grupoSeries-' + i).innerHTML +=
        `<div class="form-group col mb-0 mt-2">
            <label class="label-cantidades" for="cantidades">${serie.TALLA}</label>
            <input class="form-control input-cantidades" type="number" name="pedido[${j}][cantidad]" min="0" required>
            <input type="hidden" name="pedido[${j}][suela_id]" value="${serie.SUELA_ID}">
            <input type="hidden" name="pedido[${j}][serie_id]" value="${serieId}">
            <input type="hidden" name="pedido[${j}][color_id]" value="${colorId}">
        </div>`;

        j++;

    });

    i++;

});

// Event Delegation = Esconder Serie.
$(document).on('click', '.esconderSerie', function() {

    let id = $(this).data('id');
    let serie = document.getElementById('grupoSeries-' + id);

    serie.style.display === 'none' ? serie.style.display = 'flex' : serie.style.display = 'none';

});

// Event Delegation = Borrar Serie.
$(document).on('click', '.eliminarSerie', function(e) {

    let serieId = $(this).data('serie-id');
    let colorId = $(this).data('color-id');
    let columnaId = $(this).data('id');

    for (let i = 0; i < verificadorSerie.length; i++) {

        if ((verificadorSerie[i].SERIE_ID == serieId) && (verificadorSerie[i].COLOR_ID == colorId)){
            
            verificadorSerie.splice(i, 1);

        }

    }

    $('#serie-' + columnaId).remove();

});

// Botón de Añadir Pedido.
botonAñadirPedido.addEventListener("click", function(){

    // ID del formulario.
	let formulario = $('#añadirPedidoForm');

    // Si el formulario tiene algún campo vacio o incorrecto, lanzar error.
	if(!formulario[0].checkValidity()) return Swal.fire('Error', 'Por favor verifica todos los campos.', 'error');

    // Comprobar que haya alguna serie ingresada en el sistema.
    if (Object.entries(verificadorSerie).length === 0) {

        Swal.fire("Error", "Debes agregar al menos (1) serie al pedido.", "error");

    } else {

        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Descuida, puedes editar el pedido luego.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
        }).then((result) => {

            if (result.isConfirmed) {

                // $.post => Enviando el elemento al backend.
                $.post(`backend/api/pedidos/añadir.php`, formulario.serialize());
            
                Swal.fire({
                    title: 'Exito',
                    text: 'El pedido ha sido añadido satisfactoriamente.',
                    icon: 'success',
                    timer: 2000,
                    timerProgressBar: true,
                    allowEscapeKey: false,
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.dismiss === Swal.DismissReason.timer || result.value){
                        location.href = 'pedidos-pendientes.php';
                    }
                });

            }

        });

    }

});

</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>