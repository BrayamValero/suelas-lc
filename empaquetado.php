<?php

// Incluimos el header.php y components.php
include 'components/header.php';
include 'components/components.php';

// Filtramos la página para que solo los cargos correspondientes puedan usarla.
if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'VENTAS' || $_SESSION['USUARIO']['CARGO'] == 'DESPACHO'):

?>

<!-- Incluimos el sidebar.php -->
<?php include 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">

    <!-- Incluimos el Navbar -->
    <?php get_navbar('Ventas', 'Empaquetado'); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
    <div class="table-responsive text-center" style="width:100%">
        <div id="spinner" class="spinner-border text-center" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
        <table class="table table-bordered text-center" id="tabla">
            <thead class="thead-dark"></thead>
        </table>
    </div>
    <!-- Fin de Tabla -->

    <!-- Toast => Alertas (data-delay="700" data-autohide="false") --> 
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
        <div class="toast-header">
            <i class="toast-icon"></i>
            <strong class="mr-auto toast-title">Hello</strong>
            <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
            <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <div class="toast-body">World</div>
    </div>
    <!-- Fin de Toast Alert -->

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

    <!-- Modal de Imprimir Etiquetas -->
    <div class="modal fade" id="imprimirEtiquetas" tabindex="-1" role="dialog" aria-labelledby="imprimirEtiquetas"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">

                <form>

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-print icon-color"></i> Imprimir Etiquetas</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div id="printableArea" class="modal-body contenedorEtiquetas row">
                    </div>

                    <input type="button" onclick="printDiv('printableArea')" value="print a div!" />

                </form>

            </div>
        </div>
    </div>
    <!-- / Fin de Modal -->

</div>
<!-- / Fin del contenido -->

<!-- Inline JavaScript -->
<script>

// Definición de Variables y Constantes Globales.
var tabla;
var i = 0;
var datosPedido, datosSeries, obtenerColor, obtenerSerie;
const verPedido = document.getElementById('verPedido');
const contenedorPedidos = document.getElementById('contenedorPedidos');
const contenedorEtiquetas = document.getElementById('contenedorEtiquetas');
const botonImprimir = document.getElementById('botonImprimir');


// Datatables => Mostrando la tabla EMPAQUETADO
$.ajax({
    type: 'get',
    url: 'backend/api/utils.php?fun=obtenerPedidosEnProceso',
    async: false,
    success: function (data) {

        const result = JSON.parse(data);

        tabla = $('#tabla').DataTable({
            "initComplete": function(settings, json) {
                $("#spinner").css('visibility', 'hidden');
            },
            "info": false,
            "dom": "lrtip",
            "pageLength": 6,
            "lengthChange": false,
            "order": [[0, 'desc']],
            "data": result,
            "columns": [
                { data: "ID", title: "ID" },
                { data: "CLIENTE_NOMBRE", title: "Cliente", render: function(value, type, row) {
                    return value.toProperCase();
                }},
                { data: "FORMA_PAGO", title: "Forma de Pago" },
                { data: "FECHA_ESTIMADA", title: "Fecha de Entrega" },
                { data: "ESTADO", title: "Estado" },
                { 
                    data: 'ID',
                    title: "Opciones", render: function(value, type, row) {
                        return `<a href='javascript:void(0)' data-toggle='modal' data-target='#verPedido' data-id='${value}'>
                            <i class='fas fa-eye icon-color'></i>
                        </a>
                        <a href='javascript:void(0)' data-toggle='modal' data-target='#imprimirEtiquetas' data-id='${value}'>
                            <i class='fas fa-print icon-color'></i>
                        </a>`;
                    }
                },
            ],
            "columnDefs": [{
                searchable: true,
                orderable: true,
                className: "align-middle", "targets": "_all"
            }],
            "language": {
                "url": "<?= BASE_URL . "datatables/Spanish.json"; ?>"
            }
        });

        // Datatables => Paginación
        $.fn.DataTable.ext.pager.numbers_length = 5;
        
        // Datatables => Buscador Personalizado
        $('#customInput').on('keyup', function () {
            tabla.search(this.value).draw();
        });

    },   
    error: function(error) {
        console.log("No hay data para mostrar: " + error);
    }

});

$('#imprimirEtiquetas').on('show.bs.modal', function (e) {

    let pedidoId = $(e.relatedTarget).data('id');
    $('.contenedorEtiquetas').empty();

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerPedidosParaEmpaquetar',
        data: 'pedido_id=' + pedidoId,
        async: false,
        success: function (data) {

            const result = JSON.parse(data);

            result.forEach(row => {
                
                let cantidad =  parseInt(row.CANTIDAD);
                 
                let capacidad = parseInt(row.CAP_EMPAQUETADO);

                console.log(`MIENTRAS CANTIDAD => ${cantidad} SEA MAYOR A CAPACIDAD => ${capacidad}`);

                while (cantidad > capacidad) {
                    
                    $('.contenedorEtiquetas').append(`
                    <div class="col-3">
                        <div class="card mb-2">
                            <div class="card-body text-center">
                                <div class="badge badge-dark mb-2">Pedido ${row.PEDIDO_ID}</div>
                                <h6 class="card-title font-weight-bold">${row.MARCA.toProperCase()} ${row.COLOR.toProperCase()}</h6>
                                <h6 class="card-subtitle mb-2 text-muted">Talla ${row.TALLA}</h6>
                                <p class="card-text">${capacidad}</p>
                            </div>
                        </div>  
                    </div>
                    `);

                    cantidad = cantidad - capacidad;

                }

                $('.contenedorEtiquetas').append(`
                <div class="col-3">
                    <div class="card mb-2">
                        <div class="card-body text-center">
                            <div class="badge badge-dark mb-2">Pedido ${row.PEDIDO_ID}</div>
                            <h6 class="card-title font-weight-bold">${row.MARCA.toProperCase()} ${row.COLOR.toProperCase()}</h6>
                            <h6 class="card-subtitle mb-2 text-muted">Talla ${row.TALLA}</h6>
                            <p class="card-text">${cantidad}</p>
                        </div>
                    </div>  
                </div>
                `);

            });

        }
    });

});

function printDiv(divName) {
     var printContents = document.getElementById(divName).innerHTML;
     var originalContents = document.body.innerHTML;

     document.body.innerHTML = printContents;

     window.print();

     document.body.innerHTML = originalContents;
}

// Ver Pedido
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