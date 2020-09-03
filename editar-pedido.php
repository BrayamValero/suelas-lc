<?php

// Incluimos el header.php y components.php
$title = 'Editar Pedido';
include 'components/header.php';
include 'components/components.php';
require_once 'backend/api/utils.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS');

if(!in_array($_SESSION['USUARIO']['CARGO'], $roles_permitidos)){
    include 'components/error.php';
    include_once 'components/footer.php';
    exit();
}

?>

<!-- Incluimos el sidebar.php -->
<?php include 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">

    <!-- Incluimos el Navbar -->
    <?php get_navbar('Pedido', 'Editar Pedido #' . $_GET['id']); ?>

    <!-- Form -->
    <form action="backend/api/pedidos/editar.php?id=<?= $_GET['id']; ?>" method="POST">

        <!-- Tabla de Datos -->
        <div class="tablaDatos">

            <!-- Datos del Cliente -->
            <h6 class="pb-3 font-weight-bold">
                <i class="fas fa-user-tie icon-color mr-2"></i> Datos del Cliente
            </h6>

            <?php
                require_once "backend/api/db.php";
                $id = $_GET['id'];

                $sql = "SELECT * FROM CLIENTES WHERE ACTIVO = 'SI';";
                $result = db_query($sql);

                $sql_get = "SELECT * FROM PEDIDOS WHERE ID = ?;";
                $result_get = db_query($sql_get, array($id));
            ?>

            <div class="form-row mb-4">

                <div class="form-group col-lg-6">
                    <label for="editarNombre">Nombre</label>
                    <select id="editarNombre" name="nombre" class="form-control dropdown-select2" required>
                        <?php
                            foreach ($result as $row) {
                                
                                if ($row['ID'] == $result_get[0]['CLIENTE_ID']) {
                                    echo "<option selected value='{$row['ID']}'>" . mb_convert_case($row['NOMBRE'], MB_CASE_TITLE, "UTF-8") . " - {$row['DOCUMENTO']} - {$row['DOCUMENTO_NRO']}</option>";
                                } else {
                                    echo "<option value='{$row['ID']}'>" . mb_convert_case($row['NOMBRE'], MB_CASE_TITLE, "UTF-8") . " - {$row['DOCUMENTO']} - {$row['DOCUMENTO_NRO']}</option>";

                                }

                            }
                        ?>
                    </select>
                </div>

                <div class="form-group col-lg-3">
                    <label for="editarFecha">Fecha de Entrega</label>
                    <input id="editarFecha" name="fecha" type="date" class="form-control" required value="<?php echo $result_get[0]['FECHA_ESTIMADA']; ?>" >
                </div>

                <div class="form-group col-lg-3">
                    <label for="editarPago">Forma de Pago</label>
                    <select id="editarPago" class="form-control dropdown-select2" name="pago" required>
                        <?php
                            foreach (FORMAS_PAGO as $forma_pago) {
                                if ($result_get[0]['FORMA_PAGO'] == $forma_pago) {
                                    echo "<option selected value='$forma_pago'>". mb_convert_case($forma_pago, MB_CASE_TITLE, "UTF-8") ."</option>";
                                } else {
                                    echo "<option value='$forma_pago'>". mb_convert_case($forma_pago, MB_CASE_TITLE, "UTF-8") ."</option>";
                                }
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

                <div class="form-group col-lg-6">
                    <label for="editarReferencia">Serie</label>
                    <select id="editarReferencia" class="form-control dropdown-select2" name="serie" required>
                        <?php
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
                    <label for="editarColor">Color</label>
                    <select id="editarColor" class="form-control dropdown-select2" name="color" required>
                        <?php
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
                    <label for="botonAñadirSerie" class="hide-options">Opciones</label>
                    <button id="botonAñadirSerie" type="button" class="btn btn-main btn-block">Añadir Serie</button>
                </div>

            </div>
            <!-- Fin de Datos del Pedido -->

        </div>
        <!-- Fin de Tabla de Datos -->
    
        <!-- Tabla de Pedidos -->
        <div class="tablaPedidos shadow-sm mt-4">

            <h6 class="font-weight-bold mb-4">
                <i class="fas fa-shopping-bag icon-color mr-2"></i> Pedido
                <small class="ml-1">Si no carga la información, prueba refrescando con la tecla <strong>F5.</strong></small>
            </h6>

            <!-- Contenedor de Pedidos / JavaScript .clear -->
            <div class="contenedorPedidos"></div>
            <!-- / fin de contenedor de Serie -->

            <button id="botonFinalizarPedido" type="submit" class="btn btn-main btn-block mt-3">Editar Pedido</button>
        
        </div>
        <!-- Fin de Tabla de Pedidos -->

    </form>
    <!-- Fin del Form -->

</div>
<!-- Fin del Contenido -->

<!-- Inline JavaScript -->
<script>

// Declaración de Variables
var i = 0;
var j = 0;
var pedido_id = <?= $id ?>;
var datosPedido, datosSeries, obtenerColor, obtenerSerie;

const editarNombre = document.getElementById('editarNombre');
const editarFecha = document.getElementById('editarFecha');
const editarPago = document.getElementById('editarPago');
const editarSerie = document.getElementById('editarReferencia');
const editarColor = document.getElementById('editarColor');

const contenedorPedidos = document.getElementById('contenedorPedidos');
const botonAñadirSerie = document.getElementById('botonAñadirSerie');
const botonFinalizarPedido = document.getElementById('botonFinalizarPedido');

// Establecemos el rango mínimo para editar la fecha.
editarFecha.min = editarFecha.value;

// Obtenemos el pedido.
$.ajax({
type: 'post',
url: 'backend/api/utils.php?fun=obtenerPedidoId',
data: `pedido_id=${pedido_id}`,
async: false,
success: function (data) {

        datosPedido = JSON.parse(data);

        datosSeries = datosPedido.filter((pedido, index, self) =>
            index === self.findIndex((elem) => (
                elem.SERIE_ID === pedido.SERIE_ID && elem.COLOR_ID === pedido.COLOR_ID
            ))
        );

    }

});

// Al momento de cargar todos los elementos del DOM, cargar el JS.
document.addEventListener('DOMContentLoaded', function () {
    
    // Obtenemos el grupo de Series para popular la tabla.
    datosSeries.forEach(serie => {

        let serieId = serie.SERIE_ID;
        let colorId = serie.COLOR_ID;

        $.ajax({
        type: 'get',
        url: `backend/api/utils.php?fun=obtenerGrupoSerie&id=${serieId}`,
        async: false,
        success: function (data) {

            const result = JSON.parse(data);

            // Obtenemos el respectivo "color" dependiendo del ID de la serie.
            $.ajax({
            type: 'post',
            url: 'backend/api/utils.php?fun=obtenerColor',
            async: false,
            data: `id=${colorId}`,
            success: function (data) {

                obtenerColor = JSON.parse(data);

                }
                
            });

            // Decoración del color en cada serie.
            let color = obtenerColor[0].COLOR;
            let backgroundHex = obtenerColor[0].CODIGO;

            let red = parseInt(backgroundHex.substring(1, 3), 16);
            let green = parseInt(backgroundHex.substring(3, 5), 16);
            let blue = parseInt(backgroundHex.substring(5, 7), 16);

            let colorHex = red * 0.299 + green * 0.587 + blue * 0.114 > 186 ? '#000000' : '#FFFFFF';

            $('.contenedorPedidos').append(`
                <div id="serie-${i}" class="contenedor-serie shadow-sm" data-serie-id="${serieId}" data-color-id="${colorId}">
                    <div class="form-row">
                        <div class="col-8">
                            <strong>${result[0].MARCA.toProperCase()}</strong>
                            <span class="badge border" style="background-color: ${backgroundHex}; color: ${colorHex};">${color.toProperCase()}</span>
                            <small class="text-muted">${result[0].TALLA} al ${result[result.length - 1].TALLA}</small>
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

            result.forEach(row => {

                $('#grupoSeries-' + i).append(`
                    <div class="form-group col mb-0 mt-2">
                        <label class="label-cantidades" for="cantidades">${row.TALLA}</label>
                        <input class="form-control input-cantidades" data-suela-id="${row.SUELA_ID}" data-color-id="${colorId}" type="number" name="pedido[${j}][cantidad]" min="0" value="0" required>
                    </div>
                    <input type="hidden" name="pedido[${j}][suela_id]" value="${row.SUELA_ID}">
                    <input type="hidden" name="pedido[${j}][serie_id]" value="${serieId}">
                    <input type="hidden" name="pedido[${j}][color_id]" value="${colorId}">

                `);

                j++;

            });

            i++;

        }

        });

    });

    // Agregamos las cantidades.
    $('.input-cantidades').each(function() {

        datosPedido.some(elem => {

            if (elem.SUELA_ID == $(this).data('suela-id') && elem.COLOR_ID == $(this).data('color-id')) {

               return this.value = elem.CANTIDAD;

            }

        });

    });

});
// Fin de DOMContentLoaded event.

// Botón de añadir Serie al Pedido
botonAñadirSerie.addEventListener('click', function () {

    let serieId = editarSerie.value;
    let colorId = editarColor.value;
    let agregarSerie = { "SERIE_ID": serieId, "COLOR_ID": colorId };

    // Se verifica que haya un elemento en el array de series, luego se compara la selección con el array en cuestión para cerciorarse que no hayan repetidos.
    if(datosSeries.length !== 0){

        var verif = datosSeries.some(serie => serie.SERIE_ID === serieId && serie.COLOR_ID === colorId);

        if(verif){
            return Swal.fire("Whoops", "No puedes asignar la misma serie con el mismo color.", "warning");
        }

        datosSeries.push(agregarSerie);

    } else {

        datosSeries.push(agregarSerie);
        
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
                <input class="form-control input-cantidades" data-suela-id="${serie.SUELA_ID}" data-color-id="${colorId}" type="number" name="pedido[${j}][cantidad]" min="0" required>
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

    for (let i = 0; i < datosSeries.length; i++) {

        if ((datosSeries[i].SERIE_ID == serieId) && (datosSeries[i].COLOR_ID == colorId)){
            
            datosSeries.splice(i, 1);

        }

    }

    $('#serie-' + columnaId).remove();

});

// Verificar Pedido
botonFinalizarPedido.addEventListener("click", function(){

    // Comprobar que haya alguna serie ingresada en el sistema.
    if (Object.entries(datosSeries).length === 0) {
        event.preventDefault();
        return Swal.fire("Error", "Debes agregar al menos (1) serie al pedido.", "warning");
    }

});


</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>