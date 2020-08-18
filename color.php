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
    <?php get_navbar('Inventario', 'Color'); ?>

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

	<!-- Boton -->
	<div class="row mt-5">
		<button class="btn btn-sm btn-main mx-auto" data-toggle="modal" data-target="#añadirColorModal">Añadir Color</button>
	</div>
	<!-- Fin de Botón -->

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

	<!-- Modal de añadir Color -->
	<div class="modal fade" id="añadirColorModal" tabindex="-1" role="dialog" aria-labelledby="añadirColorModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">

				<!-- Form -->
				<form id="añadirColorForm">

					<div class="modal-header">
						<h5 class="modal-title"><i class="fas fa-star icon-color"></i> Añadir Color</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
					
						<div class="form-row">

							<div class="form-group col-sm-6">
								<label for="añadirColor">Color</label>
								<input id="añadirColor" type="text" class="form-control" placeholder="Color" name="color" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" required>
							</div>

							<div class="form-group col-sm-6">
								<label for="añadirCodigo">Código</label>
								<input id="añadirCodigo" data-jscolor="{value:'#FFFFFF'}" class="form-control" name="codigo" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" required>
							</div>

						</div>

					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-sm btn-main" id="botonAñadirColor">Añadir Color</button>
					</div>

				</form>
				
			</div>
		</div>
	</div>
	<!-- / Fin de Modal -->

	<!-- Modal de Editar Color -->
	<div class="modal fade" id="editarColorModal" tabindex="-1" role="dialog" aria-labelledby="editarColorModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">

				<!-- Form -->
				<form id="editarColorForm">

					<div class="modal-header">
						<h5 class="modal-title"><i class="fab fa-slack-hash icon-color"></i> Editar Color</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
					
						<div class="form-row">

							<!-- ID escondido para el POST -->
							<input type="hidden" name="id" id="editarId"> 

							<div class="form-group col-sm-6">
								<label for="editarColor">Color</label>
								<input id="editarColor" type="text" class="form-control" placeholder="Color" name="color" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" required>
							</div>

							<div class="form-group col-sm-6">
								<label for="editarCodigo">Código</label>
								<input id="editarCodigo" data-jscolor="{value:'#FFFFFF'}" class="form-control" name="codigo" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" required>
							</div>

						</div>

					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-sm btn-main" id="botonEditarColor">Editar Color</button>
					</div>

				</form>
				
			</div>
		</div>
	</div>
	<!-- / Fin de Modal de Editar Color -->

</div>
<!-- / Fin de contenido -->

<!-- Inline JavaScript -->
<script>

// Declarando Variables.
var tabla;

// Datatables => Mostrando la tabla COLOR
$.ajax({
    type: 'get',
    url: 'backend/api/utils.php?fun=obtenerColores',
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
                { data: "COLOR", title: "Color" },
                { data: "CODIGO", title: "Codigo" },
                { 
                    data: 'ID',
                    title: "Opciones", render: function(value, type, row) {
                        return `<a href='javascript:void(0)' class='editarColor' data-toggle='modal' data-target='#editarColorModal' data-id='${value}'>
                            <i class='fas fa-edit icon-color'></i>
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
	   	document.getElementById('customInput').addEventListener('keyup', function () {
			tabla.search(this.value).draw();
        });
	   
    },   
    error: function(error) {
        console.log("No hay data para mostrar: " + error);
    }

});

// AÑADIR => Añadiendo Color.
document.getElementById('botonAñadirColor').addEventListener('click', function () {

	// $.post => Añadiendo el elemento al backend.
	$.post( 'backend/api/color/añadir.php', $('#añadirColorForm').serialize(), function(data) {

		switch (data) {

			case 'WARNING':
				return swal('Whoops', 'Debes rellenar todos los campos.', 'warning');
				break;
			
			case 'ERROR':
				return swal('Error', 'El color ya se encuentra registrado.', 'error');
				break;

			default:

				$('#añadirColorModal').modal('hide')

				toastNotifications('fas fa-check', 'text-success', '¡Agregado!', 'El color ha sido agregado satisfactoriamente.');

				const elems = $('#añadirColorForm').serializeArray();

				// Datatables => Añadiendo el elemento al frontend.
				tabla.row.add({
				    "ID":               data,
				    "COLOR":       		elems[0].value,
				    "CODIGO":           elems[1].value,
				    "ID":               data
				}).draw().node();

				// Limpiando los inputs del modal => Añadir.
				document.getElementById('añadirColor').value = '';
				document.getElementById('añadirCodigo').jscolor.fromString('#FFFFFF');

		}	

	});

});

// EDITAR => Editando Color.
$('#tabla tbody').on( 'click', '.editarColor', function () {

	let result = $(this).parents('tr');

    document.getElementById('botonEditarColor').addEventListener('click', function () {
        
      	// $.post => Añadiendo el elemento al backend.
		$.post( 'backend/api/color/editar.php', $('#editarColorForm').serialize(), function(data, status) {

			switch (data) {

				case 'WARNING':
					return swal('Whoops', 'Debes rellenar todos los campos.', 'warning');
					break;
				
				case 'ERROR':
					return swal('Error', 'El color ya se encuentra registrado.', 'error');
					break;

				default:

					$('#editarColorModal').modal('hide')

					toastNotifications('fas fa-edit', 'text-warning', '¡Editado!', 'El color ha sido editado satisfactoriamente.');

					const elems = $('#editarColorForm').serializeArray();

					// Datatables => Añadiendo el elemento al frontend.
					tabla.row(result).data({
						"ID":               elems[0].value,
						"COLOR":       		elems[1].value,
						"CODIGO":           elems[2].value,
						"ID":               elems[0].value,
					}).draw(false);

			}		

		});	

    });

});

// Editar Color => Modal.
$('#editarColorModal').on('show.bs.modal', function (e) {

	let id = $(e.relatedTarget).data('id');

	$.ajax({
		type: 'post',
		url: 'backend/api/utils.php?fun=obtenerColor',
		data: 'id=' + id,
		dataType: "json",
		success: function (data) {

			document.getElementById('editarId').value = data[0].ID;
			document.getElementById('editarColor').value = data[0].COLOR.toProperCase();
			document.getElementById('editarCodigo').value = data[0].CODIGO;
			document.getElementById('editarCodigo').jscolor.fromString(data[0].CODIGO);

		}
	});

});

</script>

<!-- Incluyendo el footer.php -->
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