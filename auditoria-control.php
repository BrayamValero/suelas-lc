<?php

// Incluimos el header.php y components.php
$title = 'Pedidos Pendientes';
require_once 'components/header.php';
require_once 'components/navbar.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR');

if(!in_array($_SESSION['ROL'], $roles_permitidos)){
    require_once 'components/error.php';
    require_once 'components/footer.php';
    exit();
}

?>

<!-- Incluimos el sidebar.php -->
<?php include 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">

    <!-- Incluimos el Navbar -->
    <?php get_navbar('Auditoria', 'Auditoria de Control', true); ?>

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

</div>
<!-- / Fin del contenido -->

<!-- Inline JavaScript -->
<script>

// VARIABLES => Declarando Variables Globales.
var tabla;

// DATATABLES => Mostrando la tabla PEDIDOS_PENDIENTES.
$.ajax({
    type: 'get',
    url: 'backend/api/utils.php?fun=obtenerAuditoriaControl',
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
				{ data: "PEDIDO_ID", title: "Pedido" },
                { data: "FECHA_EMPAQUETADO", title: "Fecha Registro", 
					render: function(value, type, row) {

                        let date = new Date(Date.parse(row.FECHA_EMPAQUETADO));
                        
                        return `${date.toLocaleDateString('es-US')} ${date.toLocaleTimeString('en-US')}`;

					}
				},
                { data: "NOMBRE_USUARIO", title: "Usuario" },
                { data: "REFERENCIA", title: "Referencia" },
				{ data: "CANTIDAD", title: "Cantidad" },
                { data: "PESADO", title: "Peso", 
					render: function(value, type, row) {
                        return `${row.PESADO} Kgs`;
					}
				},
				{ data: "ID", title: "Opciones", 
					render: function(value, type, row) {
                        return `<a href='javascript:void(0)' data-id='${row.ID}' class='btn btn-sm btn-main devolverEmpaquetado'>
                            <i class="fas fa-undo-alt"></i>
                        </a>`;
					}
				}
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

// DEVOLVER => Devolver un paquete mal alimentado a Producción nuevamente.
$('#tabla tbody').on( 'click', '.devolverEmpaquetado', function () { 

    let id = $(this).data("id");

    Swal.fire({
        title: '¿Deseas revertir el empaquetado?',
        text: 'Al hacerlo se revertiran los errores cometidos.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {
        if (result.value) {

            // Eliminando del backend.
            $.get(`backend/api/auditoria/devolver-produccion.php?id=${id}`);

            // Datatable => Quitando el elemento del frontend.
            tabla.row($(this).parents('tr')).remove().draw(false);

            // Mostrando Notificación de éxito.
            mostrarNotificacion('agregar', '¡Devuelto!', 'Los errores han sido corregidos.');

        }
    });

});

</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>