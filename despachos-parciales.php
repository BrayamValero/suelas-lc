<?php

// Incluimos el header.php y components.php
$title = 'Despachos Parciales';
include 'components/header.php';
include 'components/components.php';
require_once 'backend/api/utils.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
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
    <?php get_navbar('Ventas', 'Despachos Parciales'); ?>

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

    <!-- Modal de Ver Orderes Despachadas -->
    <div id ="verDespachosModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="verDespachosModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="backend/api/pedidos/despachar.php" method="POST" id="verificarCheckBoxes">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-shopping-bag icon-color"></i> Pedidos Despachados</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="detallesDespachos" class="modal-body"></div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Fin de Modal -->

</div>
<!-- / Fin de Contenido-->

<!-- Inline JavaScript -->
<script>

// VARIABLES => Declarando Variables Globales.
var tabla;
var posicionTabla;

// DATATABLES => Mostrando la tabla DESPACHOS_PARCIALES.
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
                { data: 'ID', title: "Opciones",
					render: function(value, type, row) {
                        
                        return `<a href='javascript:void(0)' class='verDespachos' data-id='${row.ID}'>
                                <i class='fas fa-eye icon-color'></i>
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

        // DATATABLES => Paginación
        $.fn.DataTable.ext.pager.numbers_length = 5;
	   
    }

});

// DATATABLES => Detectar Fila Actual (Aplica para Eliminar y Editar un Elemento)
$('#tabla tbody').on( 'click', 'tr', function () { 
	posicionTabla = this;
});



// VER => Ver un Pedido.
$('#tabla tbody').on( 'click', '.verDespachos', function () { 
    
    let id = $(this).data("id");

    $.ajax({
        type: 'post',
        url: 'backend/api/despachos/ver.php',
        data: 'pedido_id=' + id,
        async: false,
        success: function (data) {

            document.getElementById('detallesDespachos').innerHTML = data;
            $('#verDespachosModal').modal('show');

        }

    });

});


// $('#verOrdenesDespachadas').on('show.bs.modal', function (e) {

//     let pedidoId = $(e.relatedTarget).data('id');

//     $.ajax({
//         type: 'post',
//         url: 'backend/api/utils.php?fun=obtenerProduccionReferencia',
//         data: 'pedido_id=' + pedidoId,
//         success: function (data) {

//             const result = JSON.parse(data);
//             const tabla = $('#tabla-modal > tbody:last-child');
//             tabla.empty();

//             result.forEach(row => {

//                 if (row.ESTADO === 'COMPLETADO') {
//                     row.ESTADO = `
//                     <div class="form-check p-0">
//                         <i class="fas fa-check"></i>
//                     </div>
//                     `;
//                 } else {
//                     row.ESTADO = `
//                     <div class="form-check">
//                         <input class="form-check-input" type="checkbox" name="producción-id-${row.ID}" value="${row.ID}">
//                     </div>
//                     `;
//                 }

//                 tabla.append(`<tr>
//                     <td>${row.SUELA_MARCA.toProperCase()}</td>
//                     <td>${row.SUELA_TALLA}</td>
//                     <td>${row.SUELA_COLOR.toProperCase()}</td>
//                     <td>${row.CANTIDAD}</td>
//                     <td>${row.DISPONIBLE}</td>
//                     <td>${row.DESPACHADO}</td>
//                     <td>
//                         ${row.ESTADO}
//                     </td>
//                 </tr>`);
//             });
//         }
//     });

// }).on('submit', function (e) {

//     console.log($('#verificarCheckBoxes input[type=checkbox]:checked').length);

//     if ($('#verificarCheckBoxes input[type=checkbox]:checked').length === 0) {

//         e.preventDefault();

//         return Swal.fire("Whoops", "Debes marcar un pedido primero.", "warning");

//     }

// });

</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>