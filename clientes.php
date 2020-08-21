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
    <?php get_navbar('Ventas', 'Clientes'); ?>

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

	<!-- Boton -->
	<div class="d-flex justify-content-center mt-5">
		<button class="btn btn-sm btn-main mx-auto" data-toggle="modal" data-target="#añadirCliente-modal">Añadir Cliente</button>
	</div>

	<!-- Modal de Añadir Cliente -->
	<div class="modal fade" id="añadirCliente-modal" tabindex="-1" role="dialog" aria-labelledby="añadirCliente-modal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
			<div class="modal-content">

				<form action="backend/api/clientes/crear.php" method="POST">

					<div class="modal-header">
						<h5 class="modal-title"><i class="fas fa-user-plus icon-color"></i> Añadir Cliente</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">

						<!-- Form Row -->
						<div class="form-row">
							
							<div class="form-group col-sm-6">
								<label for="inputAñadirCliente-modal">Tipo de Cliente</label>
								<select name="cliente" id="inputAñadirCliente-modal" class="form-control">
									<option value="EXTERNO">Externo</option>
									<option value="INTERNO">Interno</option>
								</select>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputAñadirCedula-modal">Tipo Documento</label>
								<select name="cedula" id="inputAñadirCedula-modal" class="form-control">
									<option value="CC">CC</option>
									<option value="CE">CE</option>
									<option value="PA">PA</option>
									<option value="NIT" selected>NIT</option>
									<option value="N/A">N/A</option>
								</select>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputAñadirDocumento-modal">Numero documento</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-id-card"></i></div>
									</div>
									<input type="documento" min="1" id="inputAñadirDocumento-modal" name="documento" class="form-control" placeholder="Documento">
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputAñadirNombre-modal">Nombre y Apellido</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-signature"></i></div>
									</div>
									<input name="nombre" type="text" class="form-control" id="inputAñadirNombre-modal" placeholder="Nombre y Apellido">
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputAñadirTelefono-modal">Teléfono Fijo</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-phone"></i></div>
									</div>
									<input name="telefono" type="number" min="1" class="form-control" id="inputAñadirTelefono-modal" placeholder="Fijo">
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputAñadirCelular-modal">Teléfono Movil</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-mobile-alt"></i></div>
									</div>
									<input name="celular" type="number" min="1" class="form-control" id="inputAñadirCelular-modal" placeholder="Móvil">
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputAñadirCorreo-modal">Email</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-envelope"></i></div>
									</div>
									<input name="email" type="email" class="form-control" id="inputAñadirCorreo-modal" placeholder="Correo Electrónico">
								</div>
							</div>  

							<div class="form-group col-sm-6">
								<label for="inputAñadirDireccion-modal">Dirección</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-map-marker-alt"></i></div>
									</div>
									<input name="direccion" type="text" class="form-control" id="inputAñadirDireccion-modal" placeholder="Dirección">
								</div>
							</div>

						</div>
						
					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-sm btn-main">Guardar Cambios</button>
					</div>

				</form>

			</div>
		</div>
	</div>
	<!-- Fin de Modal -->

	<!-- Editar Modal de Cliente -->
	<div class="modal fade" id="editarCliente-modal" tabindex="-1" role="dialog" aria-labelledby="editarCliente-modal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
			<div class="modal-content">

				<!-- Form -->
				<form action="backend/api/clientes/editar.php" method="POST">

					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-user-edit icon-color"></i> Editar Cliente</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">

						<div class="form-row">
							
							<div class="form-group col-sm-6">
								<input type="hidden" name="id" id="id-modal-edit">
								<label for="inputTipoCliente-modal">Tipo de Cliente</label>
								<select name="tipo-cliente" id="inputTipoCliente-modal" class="form-control">
									<option value="EXTERNO">Externo</option>
									<option value="INTERNO">Interno</option>
								</select>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputTipoDocumento-modal">Tipo Documento</label>
								<select name="tipo-documento" id="inputTipoDocumento-modal" class="form-control">
									<option value="CC">CC</option>
									<option value="CE">CE</option>
									<option value="PA">PA</option>
									<option value="NIT">NIT</option>
									<option value="N/A">N/A</option>
								</select>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputNumeroDocumento-modal">Numero documento</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-id-card"></i></div>
									</div>
									<input type="number" min="1" id="inputNumeroDocumento-modal" name="numero-documento" class="form-control" placeholder="Documento">
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputNombre-modal">Nombre y Apellido</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-signature"></i></div>
									</div>
									<input name="cliente-nombre" type="text" class="form-control" id="inputNombre-modal" placeholder="Nombre y Apellido">
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputTelephone">Teléfono Fijo</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-phone"></i></div>
									</div>
									<input name="cliente-telefono" type="number" min="1" class="form-control" id="inputTelephone-modal" placeholder="Fijo">
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputTelephone">Teléfono Movil</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-mobile-alt"></i></div>
									</div>
									<input name="cliente-celular" type="number" min="1" class="form-control" id="inputMobilePhone-modal" placeholder="Móvil">
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputEmail">Email</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-envelope"></i></div>
									</div>
									<input name="cliente-email" type="email" class="form-control" id="inputEmail-modal" placeholder="Email">
								</div>
							</div>  

							<div class="form-group col-sm-6">
								<label for="inputAddress">Dirección</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-map-marker-alt"></i></div>
									</div>
									<input name="cliente-direccion" type="text" class="form-control" id="inputAddress-modal" placeholder="Dirección">
								</div>
							</div>

						</div>
						
					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-sm btn-main">Editar</button>
					</div>

				</form>

			</div>
		</div>
	</div>
	<!-- / Fin de Modal -->

	<!-- Ver Cliente Modal -->
	<div class="modal fade" id="verCliente-modal" tabindex="-1" role="dialog" aria-labelledby="verCliente-modal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-md" role="document">
			<div class="modal-content">

				<!-- Form -->
				<form>

					<div class="modal-header">
						<h5 class="modal-title"><i class="fas fa-info-circle icon-color"></i> Información del Cliente</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">

						<div class="form-row">

							<div class="form-group col-6">
								<label for="inputVerCelular-modal">Teléfono Movil</label>
								<input id="inputVerCelular-modal" name="celular" type="number" min="1" class="form-control" readonly>
							</div>

							<div class="form-group col-6">
								<label for="inputVerTelefono-modal">Teléfono Fijo</label>
								<input id="inputVerTelefono-modal" name="telefono" type="number" min="1" class="form-control" readonly>
							</div>

							<div class="form-group col-12">
								<label for="inputVerDireccion-modal">Dirección</label>
								<input id="inputVerDireccion-modal" name="direccion" type="text" class="form-control" readonly>
							</div>

							<div class="form-group col-12">
								<label for="inputVerCorreo-modal">Correo Electrónico</label>
								<input id="inputVerCorreo-modal" name="email" type="email" class="form-control" readonly>
							</div>  

						</div>

						<div id="checkEstado" class="form-group text-center"></div>
						
					</div>

				</form>

			</div>
		</div>
	</div>
	<!-- / Fin de Modal -->

</div>
<!-- Fin de Contenido  -->

<!-- Inline JavaScript -->
<script>

// VARIABLES => Declarando Variables Globales.
var tabla;
var posicionTabla;
// const botonAñadir = document.getElementById('botonAñadir');
// const botonEditar = document.getElementById('botonEditar');

// DATATABLES => Mostrando la tabla STOCK.
$.ajax({
    type: 'get',
    url: 'backend/api/utils.php?fun=obtenerClientes',
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
				{ data: "TIPO", title: "Cliente" },
                { data: "DOCUMENTO", title: "Tipo" },
                { data: "DOCUMENTO_NRO", title: "Documento" },
				{ data: "NOMBRE", title: "Nombre" },
				{ data: "ACTIVO", title: "Activo", 
					render: function(value, type, row) {
						
						if(row.ACTIVO === 'SI'){

							return `<label class='switch'>
								<input type='checkbox' class='cambiarEstado' data-id='${row.ID}' checked>
								<span class='slider round'></span>
							</label>`;

						} else {

							return `<label class='switch'>
								<input type='checkbox' class='cambiarEstado' data-id='${row.ID}'>
								<span class='slider round'></span>
							</label>`;
							
						}

					}
				},
                { data: 'ID', title: "Opciones",
					render: function(value, type, row) {
						return `<a href='javascript:void(0)' data-toggle='modal' data-target='#editarClienteModal' data-id='${row.ID}'>
									<i class='fas fa-edit icon-color'></i>
								</a>			
								<a href='javascript:void(0)' data-toggle='modal' data-target='#verClienteModal' data-id='${row.ID}'>
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
        
		// DATATABLES => Buscador Personalizado
	   	document.getElementById('searchInput').addEventListener('keyup', function () {
			tabla.search(this.value).draw();
        });
	   
    }

});

// DATATABLES => Detectar Fila Actual (Aplica para Eliminar y Editar un Elemento)
$('#tabla tbody').on( 'click', 'tr', function () { 
	posicionTabla = this;
});

// /* DATATABLES CUSTOMIZATION */
// const tabla = $('#tabla').DataTable({
// 	info: false,
// 	dom: "lrtip",
// 	pageLength: 10,
// 	lengthChange: false,
// 	order: [[0, 'desc']],
// 	processing: true,
// 	serverSide: true,
// 	ajax: {
// 		url: "<?= BASE_URL . "backend/api/tabla_ssp.php"; ?>",
// 		method: "GET",
// 		data: {
// 			tabla: 'CLIENTES',
// 			pk: 'ID',
// 			columnas: [
// 				{nombre: 'ID', index: 0},
// 				{nombre: 'DOCUMENTO', index: 1},
// 				{nombre: 'DOCUMENTO_NRO', index: 2},
// 				{nombre: 'NOMBRE', index: 3},
// 				{nombre: 'TIPO', index: 4},
// 				{nombre: 'ACTIVO', index: 5},
// 				// Esta es solo para rellenar la tabla de OPCIONES.
// 				{nombre: 'ID', index: 6}
// 			]
// 		}
// 	},
// 	// createdRow => This is particularly useful when using deferred rendering (deferRender) or server-side processing (serverSide) so you can add events, class name information or otherwise format the row when it is created.
// 	createdRow: function(row, data, dataIndex) {
		
// 		for (let rowCell of row.cells) {
// 			rowCell.innerHTML = rowCell.innerHTML.toProperCase();
// 		}

// 		row.cells[1].innerHTML = row.cells[1].innerHTML.toUpperCase();

// 		if (row.cells[5].innerHTML == 'Si') {

// 			row.cells[5].innerHTML = `<i class='fas fa-check-circle text-success'></i></button>`;

// 		} else {

// 			row.cells[5].innerHTML = `<i class='fas fa-times-circle text-danger'></i>`;
			
// 		}

// 		row.cells[6].innerHTML = 
// 		`
// 			<?php if($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR'): ?>

// 			<a href='#' data-toggle='modal' data-target='#editarCliente-modal' data-id='${data[0]}'>
// 				<i class='fas fa-edit icon-color'></i>
// 			</a>
			
// 			<a href='#' data-id='${data[0]}' onclick='desactivarCliente(${data[0]})'>
// 				<i class='fas fa-trash icon-color'></i>
// 			</a>
// 			<a href='#' data-toggle='modal' data-target='#verCliente-modal' data-id='${data[0]}'>
// 				<i class='fas fa-eye icon-color'></i>
// 			</a>

// 			<?php else: ?>
			
// 			<a href='#' data-toggle='modal' data-target='#verCliente-modal' data-id='${data[0]}'>
// 				<i class='fas fa-eye icon-color'></i>
// 			</a>

// 			<?php endif; ?>
// 		`;

// 	},
// 	columnDefs: [{
// 		searchable: true,
// 		orderable: true,
// 		className: "align-middle", "targets": "_all"
// 	}],
// 	language: {
// 		"url": "<?= BASE_URL . "datatables/Spanish.json"; ?>"
// 	}
// });

// // Paginación - DataTables
// $.fn.DataTable.ext.pager.numbers_length = 5;

// ELIMINAR => Eliminando Referencia.
$('#tabla tbody').on( 'click', '.cambiarEstado', function () {

	let id = $(this).data("id");
	var estado = $(this).is(':checked');

	console.log(estado);

	if(!estado){

		// Desactivando cliente
		$.get(`backend/api/clientes/desactivar.php?id=${id}`);


	} else {

		// Activando cliente
		$.get(`backend/api/clientes/activar.php?id=${id}`);
				

	}

});


// var checkBox = document.getElementById("myCheck");
//   var text = document.getElementById("text");
//   if (checkBox.checked == true){
//     text.style.display = "block";
//   } else {
//      text.style.display = "none";
//   }


// Modal de Editar Clientes
$('#editarCliente-modal').on('show.bs.modal', function (e) {
	let rowid = $(e.relatedTarget).data('id');

	$.ajax({
		type : 'post',
		url : 'backend/api/utils.php?fun=obtenerClienteId',
		data :  'id='+ rowid,
		dataType: "json",
		success : function(data){

			if(data[0].TIPO === 'EXTERNO') {
				$('#inputTipoCliente-modal')[0].selectedIndex = 0;
			} else {
				$('#inputTipoCliente-modal')[0].selectedIndex = 1;
			}

			if(data[0].DOCUMENTO === 'CC') {
				$('#inputTipoDocumento-modal')[0].selectedIndex = 0;
			} else if(data[0].DOCUMENTO === 'CE') {
				$('#inputTipoDocumento-modal')[0].selectedIndex = 1;
			} else if(data[0].DOCUMENTO === 'PA') {
				$('#inputTipoDocumento-modal')[0].selectedIndex = 2;
			} else if(data[0].DOCUMENTO === 'NIT') {
				$('#inputTipoDocumento-modal')[0].selectedIndex = 3;
			} else {
				$('#inputTipoDocumento-modal')[0].selectedIndex = 4;
			}

			$('#id-modal-edit').val(data[0].ID);
			$('#inputNumeroDocumento-modal').val(data[0].DOCUMENTO_NRO);
			$('#inputNombre-modal').val(data[0].NOMBRE.toProperCase());
			$('#inputTelefono-modal').val(data[0].TELEFONO);
			$('#inputEmail-modal').val(data[0].CORREO.toLowerCase());
			$('#inputTelephone-modal').val(data[0].TELEFONO);
			$('#inputMobilePhone-modal').val(data[0].CELULAR);
			$('#inputAddress-modal').val(data[0].DIRECCION.toProperCase());
		}
	});
});

// Modal de Ver Informacion del Cliente
$('#verCliente-modal').on('show.bs.modal', function (e) {
	
	let rowid = $(e.relatedTarget).data('id');

	$.ajax({
		type : 'post',
		url : 'backend/api/utils.php?fun=obtenerClienteId',
		data :  'id='+ rowid,
		dataType: "json",
		success : function(data){

			$('#inputVerTelefono-modal').val(data[0].TELEFONO);
			$('#inputVerCelular-modal').val(data[0].CELULAR);
			$('#inputVerDireccion-modal').val(data[0].DIRECCION.toProperCase());
			$('#inputVerCorreo-modal').val(data[0].CORREO.toLowerCase());

			const checkEstado = $('#checkEstado');
            checkEstado.empty();

			if (data[0].ACTIVO === 'SI') {
				checkEstado.append(
					`<small>El usuario se encuentra activo.</small>`
				);
            } else {
                checkEstado.append(
                    `<small>El usuario se encuentra inactivo, <a href='#' onclick='activarCliente(${data[0].ID})' class='text-info font-weight-bold'>Activar aquí.</a>`
                );
            }
			
		}
	});
});

// Desactivar Cliente
function desactivarCliente(id) {
	swal({
		title: "¿Estás seguro?",
		text: "El cliente pasará a estar inactivo.",
		icon: "warning",
		buttons: [
			'No',
			'Si'
		],
		dangerMode: true,
	}).then(function (isConfirm) {
		if (isConfirm) {
			swal({
				title: '¡Inactivo!',
				text: 'El cliente se encuentra inactivo.',
				icon: 'success'
			}).then(function () {
				window.location.href = `backend/api/clientes/desactivar.php?id=${id}`
			});
		} else {
			swal("Cancelado", "Descuida, puedes volver a intentarlo luego.", "error");
		}
	});
}

// Activar Cliente
function activarCliente(id) {
	swal({
		title: "¿Estás seguro?",
		text: "El cliente pasará a estar Activo.",
		icon: "warning",
		buttons: [
			'No',
			'Si'
		],
		dangerMode: true,
	}).then(function (isConfirm) {
		if (isConfirm) {
			swal({
				title: '¡Activado!',
				text: 'El cliente se encuentra activo.',
				icon: 'success'
			}).then(function () {
				window.location.href = `backend/api/clientes/activar.php?id=${id}`
			});
		} else {
			swal("Cancelado", "Descuida, puedes volver a intentarlo luego.", "error");
		}
	});
}

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