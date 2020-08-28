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
		<button class="btn btn-sm btn-main mx-auto" data-toggle="modal" data-target="#añadirClienteModal">Añadir Cliente</button>
	</div>

	<!-- Añadir Cliente -->
	<div class="modal fade" id="añadirClienteModal" tabindex="-1" role="dialog" aria-labelledby="añadirClienteModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
			<div class="modal-content">

				<form id="añadirClienteForm">

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
								<label for="añadirTipoCliente">Tipo de Cliente</label>
								<select name="tipo_cliente" id="añadirTipoCliente" class="form-control" required>
									<option value="Externo">Externo</option>
									<option value="Interno">Interno</option>
								</select>
							</div>

							<div class="form-group col-sm-6">
								<label for="añadirTipoDocumento">Tipo Documento</label>
								<select name="tipo_documento" id="añadirTipoDocumento" class="form-control" required>
									<option value="CC">CC</option>
									<option value="CE">CE</option>
									<option value="PA">PA</option>
									<option value="NIT" selected>NIT</option>
									<option value="N/A">N/A</option>
								</select>
							</div>

							<div class="form-group col-sm-6">
								<label for="añadirNumeroDocumento">Numero documento</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-id-card"></i></div>
									</div>
									<input name="numero_documento" type="number" min="1" id="añadirNumeroDocumento"  class="form-control" placeholder="Documento" required>
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="añadirNombre">Nombre y Apellido</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-signature"></i></div>
									</div>
									<input name="nombre" type="text" class="form-control" id="añadirNombre" placeholder="Nombre y Apellido" required>
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="añadirTeléfono">Teléfono Fijo</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-phone"></i></div>
									</div>
									<input name="telefono" type="number" min="1" class="form-control" id="añadirTeléfono" placeholder="Fijo" required>
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="añadirCelular">Teléfono Movil</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-mobile-alt"></i></div>
									</div>
									<input name="celular" type="number" min="1" class="form-control" id="añadirCelular" placeholder="Móvil" required>
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="añadirCorreo">Email</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-envelope"></i></div>
									</div>
									<input name="correo" type="email" class="form-control" id="añadirCorreo" placeholder="Correo Electrónico" required>
								</div>
							</div>  

							<div class="form-group col-sm-6">
								<label for="añadirDireccion">Dirección</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-map-marker-alt"></i></div>
									</div>
									<input name="direccion" type="text" class="form-control" id="añadirDireccion" placeholder="Dirección" required>
								</div>
							</div>

						</div>
						
					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" id="botonAñadirCliente" class="btn btn-sm btn-main">Guardar Cambios</button>
					</div>

				</form>

			</div>
		</div>
	</div>
	<!-- Fin de Modal -->

	<!-- Editar Cliente -->
	<div class="modal fade" id="editarClienteModal" tabindex="-1" role="dialog" aria-labelledby="editarClienteModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-lg" role="document">
			<div class="modal-content">

				<!-- Form -->
				<form id="editarClienteForm">

					<div class="modal-header">
						<h5 class="modal-title"><i class="fas fa-user-edit icon-color"></i> Editar Cliente</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">

						<div class="form-row">
							
							<div class="form-group col-sm-6">
								<input type="hidden" name="id" id="editarId">
								<label for="editarTipoCliente">Tipo de Cliente</label>
								<select name="tipo_cliente" id="editarTipoCliente" class="form-control">
									<option value="Externo">Externo</option>
									<option value="Interno">Interno</option>
								</select>
							</div>

							<div class="form-group col-sm-6">
								<label for="editarTipoDocumento">Tipo Documento</label>
								<select name="tipo_documento" id="ieditarTipoDocumento" class="form-control">
									<option value="CC">CC</option>
									<option value="CE">CE</option>
									<option value="PA">PA</option>
									<option value="NIT">NIT</option>
									<option value="N/A">N/A</option>
								</select>
							</div>

							<div class="form-group col-sm-6">
								<label for="editarNumeroDocumento">Numero documento</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-id-card"></i></div>
									</div>
									<input type="number" min="1" id="editarNumeroDocumento" name="numero_documento" class="form-control" placeholder="Documento">
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="editarNombre">Nombre y Apellido</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-signature"></i></div>
									</div>
									<input name="nombre" type="text" class="form-control" id="editarNombre" placeholder="Nombre y Apellido">
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="editarTelefono">Teléfono Fijo</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-phone"></i></div>
									</div>
									<input name="telefono" type="number" min="1" class="form-control" id="editarTelefono" placeholder="Fijo">
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="editarCelular">Teléfono Movil</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-mobile-alt"></i></div>
									</div>
									<input name="celular" type="number" min="1" class="form-control" id="editarCelular" placeholder="Móvil">
								</div>
							</div>

							<div class="form-group col-sm-6">
								<label for="editarCorreo">Email</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-envelope"></i></div>
									</div>
									<input name="correo" type="email" class="form-control" id="editarCorreo" placeholder="Email">
								</div>
							</div>  

							<div class="form-group col-sm-6">
								<label for="editarDireccion">Dirección</label>
								<div class="input-group mb-2">
									<div class="input-group-prepend">
										<div class="input-group-text"><i class="fas fa-map-marker-alt"></i></div>
									</div>
									<input name="direccion" type="text" class="form-control" id="editarDireccion" placeholder="Dirección">
								</div>
							</div>

						</div>
						
					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" id="botonEditarCliente" class="btn btn-sm btn-main">Editar</button>
					</div>

				</form>

			</div>
		</div>
	</div>
	<!-- / Fin de Modal -->

	<!-- Ver Cliente -->
	<div class="modal fade" id="verClienteModal" tabindex="-1" role="dialog" aria-labelledby="verClienteModal" aria-hidden="true">
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
								<label for="verCelular">Teléfono Movil</label>
								<input id="verCelular" name="celular" type="number" min="1" class="form-control" readonly>
							</div>

							<div class="form-group col-6">
								<label for="verTelefono">Teléfono Fijo</label>
								<input id="verTelefono" name="telefono" type="number" min="1" class="form-control" readonly>
							</div>

							<div class="form-group col-12">
								<label for="verDireccion">Dirección</label>
								<input id="verDireccion" name="direccion" type="text" class="form-control" readonly>
							</div>

							<div class="form-group col-12">
								<label for="verCorreo">Correo Electrónico</label>
								<input id="verCorreo" name="correo" type="email" class="form-control" readonly>
							</div>  

						</div>
						
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
const botonAñadirCliente = document.getElementById('botonAñadirCliente');
const botonEditarCliente = document.getElementById('botonEditarCliente');

// DATATABLES => Mostrando la tabla CLIENTES.
$.ajax({
    type: 'get',
    url: 'backend/api/utils.php?fun=obtenerClientes',
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
	   
    }

});

// DATATABLES => Detectar Fila Actual (Aplica para Eliminar y Editar un Elemento)
$('#tabla tbody').on( 'click', 'tr', function () { 
	posicionTabla = this;
});

// AÑADIR => Añadiendo Cliente.
botonAñadirCliente.addEventListener('click', function () {

	// ID del formulario.
	let formulario = $('#añadirClienteForm');

	// Si el formulario tiene algún campo incorrecto, lanzar error.
	if(!formulario[0].checkValidity()) return Swal.fire('Error', 'Por favor verifica todos los campos.', 'error');

	// Si todos los campos son correctos, Bloquear el botón de envío de data.
	botonAñadirCliente.disabled = true;

    // $.post => Añadiendo el elemento al backend.
    $.post( 'backend/api/clientes/añadir.php', formulario.serialize(), function(data) {

	    switch (data) {
			
			case 'ERROR':
                botonAñadirCliente.disabled = false;
				return Swal.fire('Error', 'El cliente ya se encuentra registrado.', 'error');
				break;

			default:

				$('#añadirClienteModal').modal('hide')

				toastNotifications('fas fa-check', 'text-success', '¡Agregado!', 'El cliente ha sido agregado satisfactoriamente.');

				const elems = formulario.serializeArray();

				console.log(elems);

				// Datatables => Añadiendo el elemento al frontend.
				tabla.row.add({
                    "ID":               data,
                    "TIPO":       		elems[0].value,
					"DOCUMENTO":        elems[1].value,
					"DOCUMENTO_NRO":    elems[2].value,
                    "NOMBRE":         	elems[3].value,
                    "ACTIVO":     		'SI',
                    "ID":               data
				}).draw().node();

                // Borrando los inputs del Modal.
				 $('#añadirClienteModal').on('hidden.bs.modal', function (e) {
                    $(this).find("input, textarea").val('').end();
                 });

        }

    }).always(

        // Luego de agregar el elemento tanto en frontend como backend, habilitar el botón.
        $('#añadirClienteModal').on('hidden.bs.modal', function (e) {
            botonAñadirCliente.disabled = false;
        })

    ); 

});

// EDITAR => Editando Clientes.
botonEditarCliente.addEventListener('click', function () {

	// ID del formulario.
	let formulario = $('#editarClienteForm');

	// Si el formulario tiene algún campo incorrecto, lanzar error.
	if(!formulario[0].checkValidity()) return Swal.fire('Error', 'Por favor verifica todos los campos.', 'error');

	// Si todos los campos son correctos, Bloquear el botón de envío de data.
	botonEditarCliente.disabled = true;

    // $.post => Añadiendo el elemento al backend.
    $.post( 'backend/api/clientes/editar.php', formulario.serialize(), function(data) {
        
        switch (data) {

            case 'ERROR':
                botonEditarCliente.disabled = false;
                return Swal.fire('Error', 'El cliente ya se encuentra registado.', 'error');
                break;

            default:

                $('#editarClienteModal').modal('hide')

                toastNotifications('fas fa-edit', 'text-warning', '¡Editado!', 'El cliente ha sido editado satisfactoriamente.');

				const elems = formulario.serializeArray();
				
				// Se realiza un AJAX request en el cual se obtienen los datos del ID actual para actualizar la data en la tabla de DataTables.
				$.ajax({
					type : 'post',
					url : 'backend/api/utils.php?fun=obtenerClienteId',
					data :  'id='+ data,
					dataType: 'json',
					success: function (data) {

						console.table(data);

						// Datatables => Añadiendo el elemento al frontend.
						tabla.row(posicionTabla).data({
							"ID":               data[0].ID,
							"TIPO":       		data[0].TIPO,
							"DOCUMENTO":       	data[0].DOCUMENTO,
							"DOCUMENTO_NRO":    data[0].DOCUMENTO_NRO,
							"NOMBRE":     		data[0].NOMBRE,
							"ACTIVO":         	data[0].ACTIVO,
							"ID":               data[0].ID
						}).draw(false);

					}
					
				});

        }

    }).always(

        // Luego de agregar el elemento tanto en frontend como backend, habilitar el botón.
        $('#editarClienteModal').on('hidden.bs.modal', function (e) {
            botonEditarCliente.disabled = false;
        })

    ); 

});


// CAMBIAR => Cambiando el estado del cliente.
$('#tabla tbody').on( 'click', '.cambiarEstado', function () {

	let id = $(this).data("id");
	let estado = $(this).is(':checked');
	let result = !estado ? $.get(`backend/api/clientes/desactivar.php?id=${id}`) : $.get(`backend/api/clientes/activar.php?id=${id}`);

});

// VISTA -> Editar Clientes 
$('#editarClienteModal').on('show.bs.modal', function (e) {
	
	let id = $(e.relatedTarget).data('id');

	$.ajax({
		type : 'post',
		url : 'backend/api/utils.php?fun=obtenerClienteId',
		data :  'id='+ id,
		dataType: 'json',
		success : function(data){

			$("#editarTipoCliente > option").each(function() {

                if( data[0].TIPO == this.value ){

                    $(this).prop("selected", true);
                    
                    return false;
                
                }

			});

			$("#editarTipoDocumento > option").each(function() {

                if( data[0].DOCUMENTO == this.value ){

                    $(this).prop("selected", true);
                    
                    return false;
                
                }

			});			

			document.getElementById('editarId').value = data[0].ID;
			document.getElementById('editarNumeroDocumento').value = data[0].DOCUMENTO_NRO;
			document.getElementById('editarNombre').value = data[0].NOMBRE;
			document.getElementById('editarTelefono').value = data[0].TELEFONO;
			document.getElementById('editarCelular').value = data[0].CELULAR;
			document.getElementById('editarCorreo').value = data[0].CORREO;
			document.getElementById('editarDireccion').value = data[0].DIRECCION;

		}
	});
});

// VISTA -> Ver Clientes 
$('#verClienteModal').on('show.bs.modal', function (e) {
	
	let id = $(e.relatedTarget).data('id');

	$.ajax({
		type : 'post',
		url : 'backend/api/utils.php?fun=obtenerClienteId',
		data :  'id='+ id,
		dataType: 'json',
		success : function(data){

			document.getElementById('verTelefono').value = data[0].TELEFONO;
			document.getElementById('verCelular').value = data[0].CELULAR;
			document.getElementById('verCorreo').value = data[0].CORREO;
			document.getElementById('verDireccion').value = data[0].DIRECCION;
			
		}

	});
});

</script>

<!-- Incluyendo el footer.php -->
<?php include_once 'components/footer.php'; ?>