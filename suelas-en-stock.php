<?php

// Incluimos el header.php y components.php
$title = 'Suelas en Stock';
require_once 'components/header.php';
require_once 'components/navbar.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS', 'DESPACHO');

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
    <?php get_navbar('Inventario', 'Suelas en Stock'); ?>

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

	<!-- Si el usuario es Administrador, agregar botón. -->
	<?php if ($_SESSION['ROL'] == 'ADMINISTRADOR'): ?>

	<div class="row mt-5">
		<button class="btn btn-sm btn-main mx-auto" data-toggle="modal" data-target="#añadirStockModal">Añadir Stock</button>
	</div>

	<?php endif; ?>

	<!-- Modal de Añadir Stock -->
	<div class="modal fade" id="añadirStockModal" tabindex="-1" role="dialog" aria-labelledby="añadirStockModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-md" role="document">
			<div class="modal-content">

				<form id="añadirStockForm">

					<div class="modal-header">
						<h5 class="modal-title"><i class="fas fa-dolly-flatbed icon-color"></i> Añadir Stock</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">

						<!-- Form Row -->
						<div class="form-row">

							<div class="form-group col-sm-6">
								<label for="añadirOrigen">Origen</label>
								<select id="añadirOrigen" class="form-control dropdown-select2" name="origen">
									
									<?php
										require_once "backend/api/db.php";
										$sql = "SELECT * FROM CLIENTES WHERE TIPO = 'INTERNO' && ACTIVO = 'SI';";
										$result = db_query($sql);
										foreach ($result as $row) {
											echo "<option value='{$row['ID']}'>" . mb_convert_case($row['NOMBRE'], MB_CASE_TITLE, "UTF-8") . "</option>";
										}
										if(empty($result)){
											echo "<option value=''>No hay clientes disponibles.</option>";
										}
									?>

								</select>
							</div>

							<div class="form-group col-sm-6">
								<label for="añadirMarca">Marca</label>
								<select id="añadirMarca" class="form-control dropdown-select2" name="marca">
									
									<?php
										$sql = "SELECT ID, MARCA, TALLA FROM SUELAS;";
										$result = db_query($sql);
										foreach ($result as $row) {
											echo "<option value='{$row['ID']}'>" . mb_convert_case($row['MARCA'], MB_CASE_TITLE, "UTF-8") . " - {$row['TALLA']}</option>";
										}
										if(empty($result)){
											echo "<option value=''>No hay referencias.</option>";
										}
									?>

								</select>
							</div>

							<div class="form-group col-sm-6">
								<label for="añadirColor">Color</label>
								<select id="añadirColor" class="form-control dropdown-select2" name="color">

									<?php
										$sql = "SELECT ID, COLOR FROM COLOR;";
										$result = db_query($sql);
										foreach ($result as $row) {
											echo "<option value='{$row['ID']}'>" . mb_convert_case($row['COLOR'], MB_CASE_TITLE, "UTF-8") . "</option>";
										}
										if(empty($result)){
											echo "<option value=''>No hay colores.</option>";
										}
									?>

								</select>
							</div>           

							<div class="form-group col-sm-6">
								<label for="añadirCantidad">Cantidad</label>
								<input id="añadirCantidad" type="number" min="1" name="cantidad" class="form-control" placeholder="Cantidad" required>
							</div>               
						
						</div>

					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-sm btn-main" id="botonAñadirStock">Añadir Stock</button>
					</div>

				</form>
			
			</div>
		</div>
	</div>
	<!-- Fin de Modal de Añadir Stock -->

	<!-- Modal de añadir Movimientos de Stock -->
	<div class="modal fade" id="añadirMovimientoModal" tabindex="-1" role="dialog" aria-labelledby="añadirMovimientoModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-sm" role="document">
			<div class="modal-content">
				
				<!-- Form -->
				<form id="añadirMovimientoForm">

					<div class="modal-header">
						<h5 class="modal-title"><i class="fab fa-react icon-color"></i> <span id="tituloMovimientoModal"></span> de Material</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">

						<div class="form-row justify-content-center">

							<!-- ID Escondido -->
							<input type="hidden" name="id" id="editarId">
							<!-- Operacion Escondida -->
							<input type="hidden" name="operacion" id="tipoOperacionModal">
						
							<div class="form-group col-sm-10">
								<label for="editarCantidad">Cantidad <span class="badge badge-pill badge-main">Kgs</span></label>
								<input id="editarCantidad" type="number" min="1" class="form-control mb-2" name="cantidad" placeholder="Cantidad" required>
								<small>Ejemplo: 540 Gramos = 0.54</small>
							</div>
							
						</div>

					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" id="botonAñadirMovimiento" class="btn btn-sm btn-main">Guardar Cambios</button>
					</div>
				
				</form>
				<!-- End of Form -->

			</div>
		</div>
	</div>
	<!-- Fin de Modal de Añadir Movimientos de Stock -->
		
</div>
<!-- / Fin de contenido -->

<!-- Inline JavaScript -->
<script>

// VARIABLES => Declarando Variables Globales.
var tabla;
var posicionTabla;
const botonAñadirStock = document.getElementById('botonAñadirStock');
const botonAñadirMovimiento = document.getElementById('botonAñadirMovimiento');

// DATATABLES => Mostrando la tabla STOCK.
$.ajax({
    type: 'get',
    url: 'backend/api/utils.php?fun=obtenerSuelasEnStock',
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
                { data: "NOMBRE", title: "Origen" },
                { data: "REFERENCIA", title: "Referencia" },
				{ data: "MARCA", title: "Marca" },
				{ data: "TALLA", title: "Talla" },
				{ data: "COLOR", title: "Color" },
				{ data: "CANTIDAD", title: "Cantidad" },
                { 
                    data: 'ID',
                    title: "Opciones", render: function(value, type, row) {
                        return `<a href='javascript:void(0)' data-id='${value}' class='eliminarStock-disabled'>
									<i class='fas fa-trash icon-color mr-1'></i>
								</a>
								<a href='javascript:void(0)' data-id='${value}' data-operacion='+' data-toggle='modal' data-target='#añadirMovimientoModal'>
									<i class='fas fa-plus-circle text-success mr-1'></i>
								</a>
								<a href='javascript:void(0)' data-id='${value}' data-operacion='-' data-toggle='modal' data-target='#añadirMovimientoModal'>
									<i class='fas fa-minus-circle text-danger'></i>
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

// DATATABLES => Detectar Fila Actual (Aplica para Eliminar y Editar un Elemento)
$('#tabla tbody').on( 'click', 'tr', function () { 
	posicionTabla = this;
});

// AÑADIR => Añandiendo nueva referencia al Stock. 
botonAñadirStock.addEventListener('click', function () {

	// ID del formulario.
	let formulario = $('#añadirStockForm');

	// Si el formulario tiene algún campo incorrecto, lanzar error.
	if(!formulario[0].checkValidity()) return Swal.fire('Error', 'Por favor verifica todos los campos.', 'error');

	// Si todos los campos son correctos, Bloquear el botón de envío de data.
	botonAñadirStock.disabled = true;

	// Se ejecuta el metodo $.post para enviar la data a PHP mediante AJAX.
	$.post( 'backend/api/stock/añadir.php', formulario.serialize(), function(data) {

		switch (data) {

			// En caso de que el elemento se encuentre en la DB, lanzar error.
			case 'ERROR':

				botonAñadirStock.disabled = false;
				return Swal.fire('Error', 'La referencia usada ya se encuentra registrada en esa Ubicación.', 'error');
				break;
			
			// Si todo está bien, se realizan los cambios en el front-end.
			default:

				// Se esconde el modal al momento de terminar con el query en PHP.
				$('#añadirStockModal').modal('hide')

				// Se envia la notificación al front-end.
				toastNotifications('fas fa-check', 'text-success', '¡Agregado!', 'El stock ha sido agregado satisfactoriamente.');

				// Se realiza un AJAX request en el cual se obtienen los datos del ID actual para actualizar la data en la tabla de DataTables.
				$.ajax({
					type: 'post',
					url: 'backend/api/utils.php?fun=obtenerSuelaEnStock',
					data: `id=${data}`,
					async: false,
					dataType: 'json',
					success: function (data) {

						// Datatables => Añadiendo el elemento al frontend.
						tabla.row.add({
							"ID":               data[0].ID,
							"NOMBRE":       	data[0].NOMBRE,
							"REFERENCIA":       data[0].REFERENCIA,
							"MARCA":            data[0].MARCA,
							"TALLA":     		data[0].TALLA,
							"COLOR":         	data[0].COLOR,
							"CANTIDAD":       	data[0].CANTIDAD,
							"ID":               data[0].ID
						}).draw().node();

					}
					
				});

		}

	}).always(

		// Luego de agregar el elemento tanto en frontend como backend, habilitar el botón.
		$('#añadirStockModal').on('hidden.bs.modal', function (e) {
			botonAñadirStock.disabled = false;
		})

	);

});

// EDITAR => Editando Entrada y/o Salida de Stock. 
botonAñadirMovimiento.addEventListener('click', function(e) {

	// ID del formulario.
	let formulario = $('#añadirMovimientoForm');

	// Si el formulario tiene algún campo incorrecto, lanzar error.
	if(!formulario[0].checkValidity()) return Swal.fire('Error', 'Por favor verifica todos los campos.', 'error');

	// Si todos los campos son correctos, Bloquear el botón de envío de data.
	botonAñadirMovimiento.disabled = true;

	// Se ejecuta el metodo $.post para enviar la data a PHP mediante AJAX.
	$.post("backend/api/stock/movimiento.php", formulario.serialize(), function(data) {

		// Se esconde el modal al momento de terminar con el query en PHP.
		$('#añadirMovimientoModal').modal('hide');

		// Se envia la notificación al front-end.
		toastNotifications('fas fa-edit', 'text-warning', '¡Editado!', 'El stock ha sido editado satisfactoriamente.');

		// Se realiza un AJAX request en el cual se obtienen los datos del ID actual para actualizar la data en la tabla de DataTables.
		$.ajax({
			type: 'post',
			url: 'backend/api/utils.php?fun=obtenerSuelaEnStock',
			data: `id=${data}`,
			async: false,
			dataType: 'json',
			success: function (data) {

				// Se actualiza la data de la tabla sin refrescar la página.
				tabla.row(posicionTabla).data({
					"ID":               data[0].ID,
					"NOMBRE":       	data[0].NOMBRE,
					"REFERENCIA":       data[0].REFERENCIA,
					"MARCA":            data[0].MARCA,
					"TALLA":     		data[0].TALLA,
					"COLOR":         	data[0].COLOR,
					"CANTIDAD":       	data[0].CANTIDAD,
					"ID":               data[0].ID
				}).draw(false);

			}
			
		});

	}).always(

		// Luego de agregar el elemento tanto en frontend como backend, habilitar el botón.
		$('#añadirMovimientoModal').on('hidden.bs.modal', function (e) {
			botonAñadirMovimiento.disabled = false;
		})

	);

});

// ELIMINAR => Eliminando stock de la Base de Datos.
$('#tabla tbody').on( 'click', '.eliminarStock', function () { 

	let id = $(this).data("id");

	Swal.fire({
		title: '¿Estás seguro?',
		text: 'Recuerda que puedes volver a añadir el stock luego.',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: 'Si',
		cancelButtonText: 'No',
	}).then((result) => {
		if (result.value) {

			// Eliminando del backend.
			$.get(`backend/api/stock/eliminar.php?id=${id}`);

			// Datatable => Quitando el elemento del frontend.
			tabla.row($(this).parents('tr')).remove().draw(false);

			// Mostrando Notificación de éxito.
			toastNotifications('fas fa-trash', 'text-danger', '¡Eliminado!', 'El stock ha sido eliminado satisfactoriamente.');

		}
	});

});

// VISTA => Agregar datos importantes a la data escondida.
$('#añadirMovimientoModal').on('show.bs.modal', function (e) {

	let id = $(e.relatedTarget).data('id');
	let operacion = $(e.relatedTarget).data('operacion');

	if(operacion == '+'){
		
		document.getElementById('editarId').value = id;
		document.getElementById('tipoOperacionModal').value = operacion;
		document.getElementById('tituloMovimientoModal').innerHTML = 'Entrada';

	} else {

		document.getElementById('editarId').value = id;
		document.getElementById('tipoOperacionModal').value = operacion;
		document.getElementById('tituloMovimientoModal').innerHTML = 'Salida';

	}

})

</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>