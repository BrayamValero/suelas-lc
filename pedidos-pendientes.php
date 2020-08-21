<?php

// Incluimos el header.php y components.php
include 'components/header.php';
include 'components/components.php';

// Agregamos los roles que se quiere que usen esta página.
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS', 'DESPACHO');

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
    <?php get_navbar('Ventas', 'Pedidos Pendientes'); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
    <div class="table-responsive-lg">
        <table class="table table-bordered text-center" id="tabla">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Tipo de Cliente</th>
                    <th scope="col">Forma de Pago</th>
                    <th scope="col">Fecha de Entrega</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Opciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Obtenemos la data mediante PHP -->
                <?php
                require_once "backend/api/db.php";
                require_once "backend/api/utils.php";
                $sql = "SELECT P.*, C.ID AS CLIENTE_ID, C.TIPO AS CLIENTE_TIPO, C.NOMBRE AS CLIENTE_NOMBRE FROM PEDIDOS P JOIN CLIENTES C ON P.CLIENTE_ID = C.ID WHERE P.ESTADO IN ('EN ANALISIS', 'PENDIENTE');";
                $result = db_query($sql);

                // echo '<pre>'; print_r($result); echo '</pre>';

                foreach ($result as $row) {
                    echo "<tr id='{$row['ID']}'>";
                    echo "<th scope='col'>{$row['ID']}</th>";
                    echo "<td>" . mb_convert_case($row['CLIENTE_NOMBRE'], MB_CASE_TITLE, "UTF-8") . "</td>";
                    echo "<td>" . mb_convert_case($row['CLIENTE_TIPO'], MB_CASE_TITLE, "UTF-8") . "</td>";
                    echo "<td>" . mb_convert_case($row['FORMA_PAGO'], MB_CASE_TITLE, "UTF-8") . "</td>";
                    echo "<td>" . date('d-m-Y', strtotime($row['FECHA_ESTIMADA'])) . "</td>";

                    if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR') {
                        if ($row['ESTADO'] === 'EN ANALISIS') {
                            
                            echo "<td> 
                                    <a href='aprobar-pedido.php?id={$row['ID']}' class='btn btn-sm btn-main'>Aprobar Pedido</a>
                                </td>";

                        } else {
                            echo "<td>Pendiente</td>";
                        }
                    } else {
                        if ($row['ESTADO'] === 'EN ANALISIS') {
                            echo "<td>En analisis</td>";
                        } else {
                            echo "<td>Pendiente</td>";
                        }
                    }

                    echo "<td>";
                    
                    if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR') {
                        if ($row['ESTADO'] === 'EN ANALISIS') {
                            echo "<a href='editar-pedido.php?id={$row['ID']}'><i class='fas fa-edit icon-color'></i></a>";
                            echo "<a href='#' class='ml-1 eliminarPedido' data-id='{$row['ID']}'><i class='fas fa-trash icon-color'></i></a>";
                        }
                    } 

                    if ($_SESSION['USUARIO']['CARGO'] == 'VENTAS') {
                        if ($row['ESTADO'] === 'EN ANALISIS') {
                            echo "<a href='editar-pedido.php?id={$row['ID']}'><i class='fas fa-edit icon-color'></i></a>";
                        }
                    }
                        
                    echo "<a class='ml-1' href='#' data-toggle='modal' data-target='#verPedido' data-id='{$row['ID']}'><i class='fas fa-eye icon-color'></i></a>";
                    echo "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- / Fin de tabla -->

    <!-- Añadimos el botón de Añadir Pedido -->
    <?php if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'VENTAS'): ?>
        <div class="d-flex justify-content-center mt-5">
            <a class="btn btn-sm btn-main" href="añadir-pedido.php" role="button">Añadir Pedido</a>
        </div>
    <?php endif; ?>
    
    <!-- Modal de ver pedido -->
    <div class="modal fade" id="verPedido" tabindex="-1" role="dialog" aria-labelledby="verPedido"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-shopping-bag icon-color"></i> Datos del Pedido</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body contenedorPedidos">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Fin de Modal -->

    <!-- Modal de Aprobar Pedido -->
    <div class="modal fade" id="aprobarPedido" tabindex="-1" role="dialog" aria-labelledby="aprobarPedido"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-check icon-color"></i> Aprobar Pedido</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body contenedorPedidosAprobar">
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- / Fin de Modal -->

</div>
<!-- / Fin del contenido -->

<!-- Inline JavaScript -->
<script>
// DataTables Plugin: https://datatables.net/
const tabla = $('#tabla').DataTable({
    info: false,
    dom: "lrtip",
    // searching: false,
    lengthChange: false,
    pageLength: 5,
    order: [[0, 'desc']],
    columnDefs: [{
        targets: 4,
        searchable: true,
        orderable: true,
        className: "align-middle", "targets": "_all"
    }],
    language: {
        "url": "<?= BASE_URL . "datatables/Spanish.json"; ?>"
    }
});

// Custom Search DataTables
$('#customInput').on( 'keyup', function () {
    tabla.search( this.value ).draw();
});

// Variables
var i = 0;
var datosPedido, datosSeries, obtenerColor, obtenerSerie, obtenerStock;
const verPedido = document.getElementById('verPedido');
const aprobarPedido = document.getElementById('aprobarPedido');
const contenedorPedidos = document.getElementById('contenedorPedidos');

// GET => Obtenemos el Stock Completo.
ajaxGet('backend/api/utils.php?fun=obtenerStockCompleto', false).done(function(data){
    obtenerStock = data;
});

// GET => Obtenemos la Dureza.
ajaxGet('backend/api/utils.php?fun=obtenerDureza', false).done(function(data){
    obtenerDureza = data;
});

// Ver pedidos.
$('#verPedido').on('show.bs.modal', function (e) {

    let pedidoId = $(e.relatedTarget).data('id');
    $('.contenedorPedidos').empty();

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerPedidoId',
        data: 'pedido_id=' + pedidoId,
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

            $('.contenedorPedidos').append(`
                <div id="serie-${i}" class="contenedor-serie shadow-sm">
                    <div class="form-row">
                        <div class="col">
                            <strong>${result[0].MARCA.toProperCase()}</strong>
                            <span class="badge border" style="background-color: #${backgroundHex}; color: #${colorHex};">${color.toProperCase()}</span>
                            <small class="text-muted">${result[0].TALLA} al ${result[result.length - 1].TALLA}</small>
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
                        <input class="form-control input-cantidades" data-suela-id="${row.SUELA_ID}" data-color-id="${colorId}" type="number" value="0" readonly>
                    </div>
                `);

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

// Eliminar pedidos.
$('.eliminarPedido').on('click', function (e) {

    let row = $(e.target.parentElement).data('id');

    swal({
        title: "¿Estás seguro?",
        text: "Si eliminas el pedido tendrás que añadirlo de nuevo.",
        icon: "warning",
        buttons: [
            'No',
            'Si'
        ],
        dangerMode: true,
    }).then(function (isConfirm) {
        if (isConfirm) {
            swal({
                title: '¡Eliminado!',
                text: 'El pedido ha sido eliminado.',
                icon: 'success'
            }).then(function () {

                $.get(`backend/api/pedidos/delete.php?id=${row}`, function () {

                    var elem = document.getElementById(row);
                    elem.parentNode.removeChild(elem);

                });

            });
        } else {
            swal("Cancelado", "Descuida, puedes volver a intentarlo luego.", "error");
        }
    });
});

</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>