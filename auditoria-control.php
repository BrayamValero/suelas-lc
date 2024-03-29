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

var posicionTabla;

// DataTables => Init Table
var tabla = $('#tabla').DataTable({
    "initComplete": function(settings, json) {
        $("#spinner").css('display', 'none');
    },
    "info": false,
    "dom": "lrtip",
    "pageLength": 10,
    "lengthChange": false,
    "order": [[0, 'desc']],
    "ajax": { 
        "url": "backend/api/utils.php?fun=obtenerAuditoriaControl",
        "dataSrc": json => {
            console.log(json);
            return json;
        }
    },
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

// DEVOLVER => Devolver un paquete mal alimentado a Producción nuevamente.
$('#tabla tbody').on( 'click', '.devolverEmpaquetado', function () { 

    const id = this.getAttribute('data-id');

    Swal.fire({
        title: '¿Deseas revertir el empaquetado?',
        text: 'Al hacerlo se revertiran los errores cometidos.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {

        if (result.value) {

            // Saving Current Row
            const row = $(this).parents('tr');

            // $.post => Añadiendo el elemento al backend.
            $.post( 'backend/api/auditoria/devolver-produccion.php', { id } , function(data) {

                if(data == 'ERROR'){

                   return mostrarNotificacion('eliminar', '¡Error!', 'No hay suelas disponibles para corregir el error.');

                } else {

                    // Datatable => Quitando el elemento del frontend.
                    tabla.row(row).remove().draw(true);

                    // Mostrando Notificación de éxito.
                    mostrarNotificacion('añadir', '¡Devuelto!', 'Las suelas han sido reintegradas a producción.');

                }

            })
         
        }

    });

});

</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>