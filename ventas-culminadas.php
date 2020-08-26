<?php

// Incluimos el header.php y components.php
include 'components/header.php';
include 'components/components.php';
require_once 'backend/api/utils.php';

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
    <?php get_navbar('Ventas', 'Ventas Culminadas'); ?>

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

    <div class="row mt-5">
        <button class="btn btn-sm btn-main mx-auto">Exportar Reporte</button>
    </div>

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

</div>
<!-- / Fin del contenido -->

<!-- Inline JavaScript -->
<script>

// VARIABLES => Declarando Variables Globales.
var tabla;

// DATATABLES => Mostrando la tabla STOCK.
$.ajax({
    type: 'get',
    url: 'backend/api/utils.php?fun=obtenerVentasCulminadas',
    async: false,
    success: function (data) {

        const result = JSON.parse(data);
        console.log(result);

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
                { data: "CLIENTE_NOMBRE", title: "Nombre Cliente" },
                { data: "CLIENTE_TIPO", title: "Tipo Cliente" },
				{ data: "UPDATED_AT", title: "Fecha Culminación" },
				{ data: "FORMA_PAGO", title: "Tipo Pago" },
                { 
                    data: 'ID',
                    title: "Opciones", render: function(value, type, row) {
                        return `<a href='javascript:void(0)' data-id='${value}' data-toggle='modal' data-target='#verPedido'>
									<i class='fas fa-eye icon-color mr-1'></i>
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
        
		// DATATABLES => Buscador Personalizado
	   	document.getElementById('searchInput').addEventListener('keyup', function () {
			tabla.search(this.value).draw();
        });
	   
    }

});

</script>

<!-- COMPONENTE = > Ver Pedido -->
<script src="js/ver-pedido.js"></script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>