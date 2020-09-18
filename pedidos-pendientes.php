<?php

// Incluimos el header.php y components.php
$title = 'Pedidos Pendientes';
include 'components/header.php';
include 'components/components.php';
require_once 'backend/api/utils.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS', 'DESPACHO', 'PRODUCCION');

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
			<strong class="mr-auto toast-title"></strong>
			<button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
			<span aria-hidden="true">&times;</span>
			</button>
		</div>
		<div class="toast-body"></div>
	</div>

    <!-- Añadimos el botón de Añadir Pedido -->
    <?php if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'VENTAS'): ?>
        <div class="d-flex justify-content-center mt-5">
            <a class="btn btn-sm btn-main" href="añadir-pedido.php" role="button">Añadir Pedido</a>
        </div>
    <?php endif; ?>
    
    <!-- Modal de Ver Pedido -->
    <div id="verPedidoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="verPedidoModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-shopping-bag icon-color"></i> Datos del Pedido <span class="badge badge-danger" id="ordenPedidoId"></span></h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body" id="detallesPedido">
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

// VARIABLES => Declarando Variables Globales.
var tabla;

// DATATABLES => Mostrando la tabla PEDIDOS_PENDIENTES.
$.ajax({
    type: 'get',
    url: 'backend/api/utils.php?fun=obtenerPedidosPendientes',
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
                { data: "ID", title: "#" },
				{ data: "CLIENTE_NOMBRE", title: "Cliente" },
                { data: "CLIENTE_TIPO", title: "Tipo" },
                { data: "FORMA_PAGO", title: "Forma Pago" },
				{ data: "FECHA_ESTIMADA", title: "Fecha Estimada" },
				{ data: "ESTADO", title: "Estado", 
					render: function(value, type, row) {
						if ( row.ESTADO === 'EN ANALISIS') {
                            return `<a href='aprobar-pedido.php?id=${row.ID}' class='btn btn-sm btn-main'>Aprobar Pedido</a>`;
                        } else {
                            return `Pendiente`;
                        }
					}
				},
                { data: 'ID', title: "Opciones",
					render: function(value, type, row) {
                        
                        if ( row.ESTADO === 'EN ANALISIS') {
                            return `<a href='editar-pedido.php?id=${row.ID}' class='mr-1'>
                                    <i class='fas fa-edit icon-color'></i>
                                </a>
                                <a href='javascript:void(0)' class='eliminarPedido mr-1' data-id='${row.ID}'>
                                    <i class='fas fa-trash icon-color'></i>
                                </a>
                                <a href='javascript:void(0)' class='verPedido' data-id='${row.ID}'>
                                    <i class='fas fa-eye icon-color'></i>
                                </a>`;
                        } 
                        
                        else {
                            return `
                            
                            <?php if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'DESPACHO'): ?>
                                <a href='javascript:void(0)' class='cancelarPedido mr-1' data-id='${row.ID}'>
                                    <i class='fas fa-ban icon-color'></i>
                                </a>
                                <a href='ver-etiquetas.php?id=${row.ID}'>
                                    <i class='fas fa-print icon-color'></i>
                                </a>
                            <?php endif; ?>
                                <a href='javascript:void(0)' class='verPedido mr-1' data-id='${row.ID}'>
                                    <i class='fas fa-eye icon-color'></i>
                                </a>`;
                        }

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

        // DATATABLES => Paginación
        $.fn.DataTable.ext.pager.numbers_length = 5;
	   
    }

});

// VER => Ver un Pedido.
$('#tabla tbody').on( 'click', '.verPedido', function () { 
    
    let id = $(this).data("id");

    $.ajax({
        type: 'post',
        url: 'backend/api/pedidos/ver.php',
        data: 'pedido_id=' + id,
        async: false,
        success: function (data) {
            
            document.getElementById('ordenPedidoId').innerHTML = id;
            document.getElementById('detallesPedido').innerHTML = data;
            $('#verPedidoModal').modal('show');

        }

    });

});

// ELIMINAR => Eliminando un Pedido.
$('#tabla tbody').on( 'click', '.eliminarPedido', function () { 

    let id = $(this).data("id");

    Swal.fire({
        title: '¿Deseas eliminar el pedido?',
        text: 'Si eliminas el pedido tendrás que agregarlo nuevamente.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {
        if (result.value) {

            // Eliminando del backend.
            $.get(`backend/api/pedidos/eliminar.php?id=${id}`);

            // Datatable => Quitando el elemento del frontend.
            tabla.row($(this).parents('tr')).remove().draw(false);

            // Mostrando Notificación de éxito.
            toastNotifications('fas fa-trash', 'text-danger', '¡Eliminado!', 'El pedido ha sido eliminado.');

        }
    });

});

// CANCELAR => Cancelando un Pedido.
$('#tabla tbody').on( 'click', '.cancelarPedido', function () { 

    let id = $(this).data("id");

    Swal.fire({
        title: '¿Deseas cancelar el pedido?',
        text: 'Todas las suelas producidas pasaran al inventario. Además, las suelas sacadas del stock volverán a su destino.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {
        if (result.value) {

            // Eliminando del backend.
            $.get(`backend/api/pedidos/cancelar.php?id=${id}`);

            // Datatable => Quitando el elemento del frontend.
            tabla.row($(this).parents('tr')).remove().draw(false);

            // Mostrando Notificación de éxito.
            toastNotifications('fas fa-trash', 'text-danger', '¡Cancelado!', 'El pedido ha sido cancelado.');

        }
    });

});

</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>