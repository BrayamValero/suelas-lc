<?php

// Incluimos el header.php y components.php
$title = 'Despachos Parciales';
require_once 'components/header.php';
require_once 'components/navbar.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS', 'DESPACHO', 'PRODUCCION');

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
    <?php get_navbar('Ventas', 'Despachos Parciales', true); ?>

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
        <div class="modal-dialog modal-dialog-centered modal-xl" role="document">
            <div class="modal-content">
                <form action="backend/api/despachos/despachar.php" method="POST" name="pedidosDespachadosForm" id="verifCheckbox">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-truck icon-color"></i> 
                                Datos del Pedido <span class="badge badge-danger" id="ordenPedidoId"></span>
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div id="detallesDespachos" class="modal-body"></div>

                    <!-- Fin de Modal de Editar Materia Prima Avanzada -->
                    <?php if($_SESSION['ROL'] == 'ADMINISTRADOR' || $_SESSION['ROL'] == 'DESPACHO'): ?>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-outline-dark mr-auto" id="marcarCheckbox" data-status="off">Marcar Todos</button>
                        <button type="button" class="btn btn-sm btn-main" id="botonDespacharPedido">Despachar Pedido</button>
                    </div>

                    <?php endif; ?>

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
var marcarCheckbox = document.getElementById('marcarCheckbox')

// DATATABLES => Mostrando la tabla DESPACHOS_PARCIALES.
$.ajax({
    type: 'get',
    url: 'backend/api/utils.php?fun=obtenerPedidosPendientes',
    async: false,
    success: function (data) {

        const result = JSON.parse(data);

        tabla = $('#tabla').DataTable({
            "initComplete": function(settings, json) {
                $("#spinner").css('display', 'none');
            },
            "info": false,
            "dom": "lrtip",
            "pageLength": 10,
            "lengthChange": false,
            "order": [[0, 'desc']],
            "data": result,
            "columns": [
                { data: "ID", title: "#" },
				{ data: "CLIENTE_NOMBRE", title: "Cliente" },
                { data: "CLIENTE_TIPO", title: "Tipo" },
                { data: "FORMA_PAGO", title: "Forma Pago" },
                { data: "FECHA_ESTIMADA", title: "Fecha Estimada", 
					render: function(value, type, row) {
                        let date = new Date(Date.parse(row.FECHA_ESTIMADA));
                        return date.toLocaleDateString('es-US');
					}
                },
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
                "url": "datatables/Spanish.json"
            }
        });

        // DATATABLES => Paginación
        $.fn.DataTable.ext.pager.numbers_length = 5;
	   
    }

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

            document.getElementById('ordenPedidoId').innerHTML = id;
            document.getElementById('detallesDespachos').innerHTML = data;
            $('#verDespachosModal').modal('show');

        }

    });

});

// Comprobar que se está enviando data al backend.
document.getElementById('botonDespacharPedido').onclick = function() {

    let checked = document.querySelectorAll("#verifCheckbox input[type=checkbox]:checked").length;

    if (checked === 0) return Swal.fire("Whoops", "Debes seleccionar un pedido primero.", "warning");
    
    // Se envia el formulario.
    document.forms["pedidosDespachadosForm"].submit();

};

// Marcar y Desmarcar Checkboxes.
marcarCheckbox.addEventListener('click', function(e){

    let status = e.target.getAttribute('data-status');

    let checkboxes = document.querySelectorAll("#verifCheckbox input[type=checkbox]");

    if (status == 'off') {

        marcarCheckbox.innerHTML = 'Desmarcar Todos';

        for (let checkbox of checkboxes) {
            checkbox.checked = true;
        }

        marcarCheckbox.setAttribute("data-status", 'on');

    } else {

        marcarCheckbox.innerHTML = 'Marcar Todos';

        for (let checkbox of checkboxes) {
            checkbox.checked = false;
        }

        marcarCheckbox.setAttribute("data-status", 'off');

    }

});

// Quitar Checkboxes al cerrar el modal.
$('#verDespachosModal').on('hidden.bs.modal', function (e) {

    marcarCheckbox.innerHTML = 'Marcar Todos';
    marcarCheckbox.setAttribute("data-status", 'off');

});
    
</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>