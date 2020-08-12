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
    <?php get_navbar('Inventario', 'Suelas en Stock'); ?>

		<!-- Inicio de Tabla -->
		<div class="table-responsive-lg">
			<table class="table table-bordered text-center" id="tabla">
				<thead class="thead-dark">
					<tr>
						<th scope="col">#</th>
						<th scope="col">Origen</th>
						<th scope="col">Referencia</th>
						<th scope="col">Marca</th>
						<th scope="col">Talla</th>
						<th scope="col">Color</th>
						<th scope="col">Cantidad</th>
						<th scope="col">Opciones</th>
					</tr>
				</thead>
				<tbody>
                	<?php
                        require_once "backend/api/db.php";
                        require_once "backend/api/utils.php";					

						$sql = "SELECT ST.ID, C.NOMBRE, SU.REFERENCIA, SU.MARCA, SU.TALLA, COL.COLOR, ST.CANTIDAD 
						FROM STOCK ST
						LEFT JOIN SUELAS SU
							ON ST.SUELA_ID = SU.ID
						LEFT JOIN CLIENTES C
							ON ST.CLIENTE_ID = C.ID
						LEFT JOIN COLOR COL
							ON ST.COLOR_ID = COL.ID;";

						$result = db_query($sql);

						// echo '<pre>'; print_r($result); echo '</pre>';

                        foreach ($result as $row) {
							echo "<tr>";
							echo "<td>{$row['ID']}</td>";
                            echo "<td>". mb_convert_case($row['NOMBRE'], MB_CASE_TITLE, "UTF-8") ."</td>";
							echo "<td>". mb_convert_case($row['REFERENCIA'], MB_CASE_TITLE, "UTF-8") ."</td>";
							echo "<td>". mb_convert_case($row['MARCA'], MB_CASE_TITLE, "UTF-8") ."</td>";
							echo "<td>". mb_convert_case($row['TALLA'], MB_CASE_TITLE, "UTF-8") ."</td>";
							echo "<td>". mb_convert_case($row['COLOR'], MB_CASE_TITLE, "UTF-8") ."</td>";
							echo "<td>{$row['CANTIDAD']}</td>";
							if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR'){
								echo "<td>
									<a href='#' data-id='{$row['ID']}' class='eliminarStock'>
										<i class='fas fa-trash icon-color mr-1'></i>
									</a>
									<a href='#' data-toggle='modal' data-target='#añadirMovimiento-modal' data-id='{$row['ID']}' data-operacion='+'>
										<i class='fas fa-plus-circle text-success mr-1'></i>
									</a>
									<a href='#' data-toggle='modal' data-target='#añadirMovimiento-modal' data-id='{$row['ID']}' data-operacion='-'>
										<i class='fas fa-minus-circle text-danger'></i>
									</a>
								</td>";
							} else {
								echo "<td><i class='fas fa-ban icon-color'></i></td>";
							}
						

                            echo "</tr>";
                        }
                    ?>
				</tbody>
			</table>
		</div>
		<!-- / Fin de tabla -->

		<!-- Modal de Añadir Stock -->
		<div class="modal fade" id="añadirStock-modal" tabindex="-1" role="dialog" aria-labelledby="añadirStock-modal" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered modal-md" role="document">
				<div class="modal-content">

                    <form action="backend/api/stock/crear.php" method="POST">

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
									<label for="inputAñadirOrigen-modal">Origen</label>
									<select id="inputAñadirOrigen-modal" class="form-control" name="origen">
									</select>
								</div>

								<div class="form-group col-sm-6">
                                    <label for="inputAñadirMarca-modal">Marca</label>
                                    <select id="inputAñadirMarca-modal" class="form-control dropdown-select2" name="marca">
									</select>
								</div>

								<div class="form-group col-sm-6">
                                    <label for="inputAñadirColor-modal">Color</label>
									<select id="inputAñadirColor-modal" class="form-control" name="color">
									</select>
								</div>           

								<div class="form-group col-sm-6">
                                    <label for="inputAñadirCantidad-modal">Cantidad</label>
                                    <input id="inputAñadirCantidad-modal" type="number" min="1" name="cantidad" class="form-control" placeholder="Cantidad">
								</div>               
							
							</div>

						</div>
						
						<div class="modal-footer">
							<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
							<button type="submit" class="btn btn-sm btn-main" id="submitAñadirStock-modal">Añadir Stock</button>
						</div>

					</form>
				
				</div>
			</div>
		</div>
		<!-- Fin de Modal de Añadir Stock -->

		<!-- Modal de añadir Movimientos de Stock -->
		<div class="modal fade" id="añadirMovimiento-modal" tabindex="-1" role="dialog" aria-labelledby="añadirMovimiento-modal" aria-hidden="true">
		    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
			    <div class="modal-content">
                    
                    <!-- Form -->
                    <form action="backend/api/stock/movimiento.php" method="POST">

                        <div class="modal-header">
                            <h5 class="modal-title"><i class="fab fa-react icon-color"></i> <span id="inputMovimientoTitulo-modal"></span> de Material</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                            </button>
                        </div>

                        <div class="modal-body">

                            <div class="form-row justify-content-center">

                                <div class="form-group" hidden>
                                    <!-- ID Escondido -->
                                    <input name="id" id="inputMovimientoId-modal">
                                    <!-- Operacion Escondida -->
                                    <input name="operacion" id="inputMovimientoOperacion-modal">
                                </div>
                                
                                <div class="form-group col-sm-10">
                                    <label for="inputMovimientoCantidad-modal">Cantidad <span class="badge badge-pill badge-main">Kgs</span></label>
                                    <input id="inputMovimientoCantidad-modal" class="form-control mb-2" type="number" min="0" step="0.001" name="cantidad" placeholder="Cantidad" required>
                                    <small>Ejemplo: 540 Gramos = 0.54</small>
                                </div>
                               
                            </div>

                        </div>
                        
                        <div class="modal-footer">
                            <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" id="submitMovimiento" class="btn btn-sm btn-main">Editar</button>
                        </div>
                    
                    </form>
                    <!-- End of Form -->

			    </div>
		    </div>
		</div>
		<!-- Fin de Modal de Añadir Movimientos de Stock -->

		<?php
			if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR'): 
		?>
		<div class="row mt-5">
			<a class="btn btn-sm btn-main mx-auto" data-toggle="modal" data-target="#añadirStock-modal" href="#" role="button">Añadir Stock</a>
		</div>
		<?php 
			endif;
		?>
		
</div>
<!-- / Fin de contenido -->

<!-- Inline JavaScript -->
<script>
// Variables Inicializadas para Añadir.
var datosSuela, operacion;
const inputAñadirOrigen = document.getElementById('inputAñadirOrigen-modal');
const inputAñadirMarca = document.getElementById('inputAñadirMarca-modal');
const inputAñadirColor = document.getElementById('inputAñadirColor-modal');
const inputAñadirCantidad = document.getElementById('inputAñadirCantidad-modal');
const inputMovimientoCantidad = document.getElementById("inputMovimientoCantidad-modal");

// DataTables Plugin: https://datatables.net/
const tabla = $('#tabla').DataTable({
	info: false,
	dom: "lrtip",
	// searching: false,
	lengthChange: false,
	pageLength: 5,
	order: [[0, 'desc']],
	columnDefs: [{
		targets: 4,
		searchable: true,
		orderable: true,
		className: "align-middle", "targets": "_all"
	}],
	language: {
		"url": "<?= BASE_URL . "datatables/Spanish.json"; ?>"
	}
});

// Custom Search DataTables
$('#customInput').on( 'keyup', function () {
	tabla.search( this.value ).draw();
});

// select2 plugin: https://github.com/select2
$(document).ready(function () {
    $('.dropdown-select2').select2({
        theme: "bootstrap4",
    });
});

// Obtenemos todas las Sucursales.
$.ajax({
	type: 'get',
	url: 'backend/api/utils.php?fun=obtenerClientesInternos',
	success: function (data) {

		const result = JSON.parse(data);

		result.forEach(row => {

			let option = document.createElement('option');
			option.value = row.ID;
			option.innerText = row.NOMBRE.toProperCase();
			
			inputAñadirOrigen.append(option);

		});

	}
});

// Obtenemos todas las Marcas.
$.ajax({
	type: 'get',
	url: 'backend/api/utils.php?fun=obtenerSuelas',
	success: function (data) {

		const result = JSON.parse(data);

		result.forEach(row => {

			let option = document.createElement('option');
			option.value = row.ID;
			option.innerText = `${row.MARCA.toProperCase()} - ${row.TALLA}`;
			
			inputAñadirMarca.append(option);

		});

	}
});

// Obtenemos todos los Colores.
$.ajax({
	type: 'get',
	url: 'backend/api/utils.php?fun=obtenerColores',
	success: function (data) {

		const result = JSON.parse(data);

		result.forEach(row => {

			let option = document.createElement('option');
			option.value = row.ID;
			option.innerText = row.COLOR.toProperCase();
			
			inputAñadirColor.append(option);

		});

	}
});

// Obtenemos todos las Suelas en Stock para evitar la creación de duplicados.
$.ajax({
	type: 'get',
	url: 'backend/api/utils.php?fun=obtenerSuelasEnStock',
	async: false,
	success: function (data) {

		const result = JSON.parse(data);

		document.getElementById('submitAñadirStock-modal').addEventListener('click', function(){

			for (let i = 0; i < result.length; i++) {

				if ((result[i].CLIENTE_ID == inputAñadirOrigen.value) && (result[i].SUELA_ID == inputAñadirMarca.value) && (result[i].COLOR_ID == inputAñadirColor.value)){

					event.preventDefault();
					return swal("Whoops", "No puedes asignar la misma referencia en la misma sucursal.", "warning");

				}else if(inputAñadirCantidad.value == ""){

					event.preventDefault();
					return swal("Whoops", "Ingrese la cantidad.", "warning");

				}
	
			}

		});

	}
});

// Eliminar Stock del inventario.
$('.eliminarStock').on('click', function (e) {

	let row = $(e.target.parentElement).data('id');

	event.preventDefault();

	swal({
		title: "¿Estás seguro?",
		text: "Recuerda que puedes volver a añadir las suelas luego.",
		icon: "warning",
		buttons: [
		'No',
		'Si'
		],
		dangerMode: true,
	}).then(function(isConfirm) {
		if (isConfirm) {
		swal({
			title: '¡Eliminado!',
			text: 'Las suelas han sido eliminadas del stock.',
			icon: 'success'
		}).then(function() {
			window.location.href = `backend/api/stock/eliminar.php?id=${row}`
		});
		} else {
		swal("Cancelado", "Descuida, puedes volver a intentarlo luego.", "error");
		}
	});
});

// Modal de Añadir Movimientos
$('#añadirMovimiento-modal').on('show.bs.modal', function (e) {

	let rowid = $(e.relatedTarget).data('id');
	operacion = $(e.relatedTarget).data('operacion');

	$.ajax({
		type: 'post',
		url: 'backend/api/utils.php?fun=obtenerSuelaEnStock',
		data: 'id=' + rowid,
		dataType: 'json',
		success: function (data) {
	
			datosSuela = data;

			var titulo = document.getElementById('inputMovimientoTitulo-modal');

			if (operacion == '+') {
				titulo.innerHTML = "Entrada";
			} else {
				titulo.innerHTML = "Salida";
			}

			$('#inputMovimientoId-modal').val(datosSuela[0].ID);
			$('#inputMovimientoOperacion-modal').val(operacion);

		}
	});

});

// Submit Movimiento
$(document).on('click', '#submitMovimiento', function () {

	if (operacion == '-') {
		
		if (parseFloat(inputMovimientoCantidad.value) > parseFloat(datosSuela[0].CANTIDAD)) {
		
			event.preventDefault();
			swal("Error", "No hay stock suficiente para retirar.", "error");

			console.log(`${parseFloat(inputMovimientoCantidad.value)} es mayor a la cantidad disponible que es ${parseFloat(datosSuela[0].CANTIDAD)}`);
			
		}

	}

});

</script>

<!-- Incluimos el footer.php -->
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