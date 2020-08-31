<?php

// Incluimos el header.php y components.php
$title = 'Operarios';
include 'components/header.php';
include 'components/components.php';
require_once 'backend/api/utils.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'PRODUCCION');

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
    <?php get_navbar('Producción', 'Operarios'); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
	<div class="table-responsive-lg">
		<table class="table table-bordered text-center" id="tabla">
			<thead class="thead-dark">
				<tr>
					<th scope="col">#</th>
					<th scope="col">Operario</th>
					<th scope="col">Turno</th>
					<th scope="col">Material</th>
					<th scope="col">Opciones</th>
				</tr>
			</thead>
			<tbody>
				<?php
				require_once "backend/api/db.php";

				$sql = "SELECT O.ID AS OPERARIO_ID, U.ID, O.TURNO, O.MATERIAL, U.NOMBRE
				FROM OPERARIOS O 
				JOIN USUARIOS U
					ON O.USUARIO_ID = U.ID;";

				$result = db_query($sql);
				
				// echo '<pre>'; print_r($result); echo '</pre>';

				foreach ($result as $row) {
					echo "<tr>";
					
					echo "<th>{$row['OPERARIO_ID']}</th>";
					echo "<td>". mb_convert_case($row['NOMBRE'], MB_CASE_TITLE, 'UTF-8') ."</td>";
					echo "<td>". mb_convert_case($row['TURNO'], MB_CASE_TITLE, 'UTF-8') ."</td>";
					echo "<td>". mb_convert_case($row['MATERIAL'], MB_CASE_TITLE, 'UTF-8') ."</td>";
					echo "<td><a href='#' data-toggle='modal' data-target='#intercambiarOperarioModal' data-id='{$row['ID']}'><i class='fas fa-sync-alt icon-color'></i></a></td>";

					echo "</tr>";
					
				}
				?>
			</tbody>
		</table>
	</div>
	<!-- End of Table -->

	<!-- Modal de Operarios Asignados -->
	<div class="modal fade" id="asignarOperarioModal" tabindex="-1" role="dialog" aria-labelledby="asignarOperarioModal"
	aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-md" role="document">
			<div class="modal-content">

				<!-- Form -->
				<form>

					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-user-plus icon-color"></i>
							Asignar Operarios</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body alerta-operarios">

						<div class="form-row justify-content-center">

							<div class="form-group col-sm-12">
								<label for="inputOperario-modal">Operario</label>
								<select id="inputOperario-modal" class="form-control" name="operario">
								</select>
							</div>
							

							<div class="form-group col-sm-6">
								<label for="inputTurno-modal">Turno</label>
								<select id="inputTurno-modal" type="text" class="form-control" name="turno">
									<option value="DIURNO">Diurno</option>
									<option value="NOCTURNO">Nocturno</option>
								</select>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputMaterial-modal">Material</label>
								<select id="inputMaterial-modal" type="text" class="form-control" name="material">
									<option value="EXPANSO">Expanso</option>
									<option value="PVC">PVC</option>
									<option value="PU">PU</option>
								</select>
							</div>

						</div>

					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-sm btn-main" id="asignarOperarioSubmit">Asignar</button>
					</div>

				</form>
				<!-- End of Form -->
			</div>
		</div>
	</div>
	<!-- Fin de Modal de Operarios Asignados -->

	<!-- Modal de Intercambiar Operarios Asignados -->
	<div class="modal fade" id="intercambiarOperarioModal" tabindex="-1" role="dialog" aria-labelledby="intercambiarOperarioModal"
	aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-md" role="document">
			<div class="modal-content">

				<!-- Form -->
				<form>

					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"><i class="fas fa-user-edit icon-color"></i>
							Intercambiar Operarios</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">

						<div class="form-row justify-content-center">

							<div class="form-group col-sm-6">
								<label for="inputOperario1-modal-edit">Operario Actual</label>
								<select id="inputOperario1-modal-edit" class="form-control" name="operario-1" disabled>
								</select>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputOperario2-modal-edit">Operario Nuevo</label>
								<select id="inputOperario2-modal-edit" class="form-control" name="operario-2">
								</select>
							</div>

						</div>

					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-sm btn-main" id="intercambiarOperarioSubmit">Intercambiar</button>
					</div>

				</form>
				<!-- End of Form -->
			</div>
		</div>
	</div>
	<!-- Fin de Modal de Intercambiar Operarios Asignados -->

	<div class="row mt-5">
		<a class="btn btn-sm btn-main mx-auto" data-toggle="modal" data-target="#asignarOperarioModal" href="#" role="button">Asignar Operario</a>
	</div>

</div>
<!-- End of Content section -->

<!-- Inline JS -->
<script>

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

// Constantes para el Modal de Asignar Operario.
const inputOperario = document.getElementById('inputOperario-modal');
const inputTurno = document.getElementById('inputTurno-modal');
const inputMaterial = document.getElementById('inputMaterial-modal');
const deshabilitarBoton = document.getElementById('asignarOperarioSubmit');

// Constantes para el Modal de Intercambiar Operario.
const inputOperario1Edit = document.getElementById('inputOperario1-modal-edit');
const inputOperario2Edit = document.getElementById('inputOperario2-modal-edit');
// const alertaOperarios = $('.alerta-operarios');

// Modal de Asignar Operario.
$('#asignarOperarioModal').on('show.bs.modal', function (e) {

    $.ajax({
        type: 'get',
        url: 'backend/api/utils.php?fun=obtenerOperariosLibres',
        success: function (data) {

            const result = JSON.parse(data);
			// console.log(result);

			// Borramos el select cada vez que undimos el modal
			$("#inputOperario-modal").empty();

			// Comprobando que hayan Operarios disponibles.
			if(Object.entries(result).length === 0){
				
				let option = document.createElement("option");
				option.innerText = "No hay operarios disponibles.";

				inputOperario.append(option);

				deshabilitarBoton.disabled = true;

			}

            result.forEach(operario => {

                let option = document.createElement('option');
                option.value = operario.ID;
                option.innerText = operario.NOMBRE.toProperCase();

                inputOperario.append(option);

            });

        }
    });

});

// Accción a realizar al momento de undir el submit de Asignar Operarios.
document.getElementById('asignarOperarioSubmit').addEventListener('click', function () {

    $.ajax({
        type: 'get',
        url: 'backend/api/utils.php?fun=obtenerOperarios',
        success: function (data) {

            const result = JSON.parse(data);

			for (let i = 0; i < result.length; i++) {
				
				if((inputTurno.value == result[i].TURNO) && (inputMaterial.value == result[i].MATERIAL)){
                    return Swal.fire('Error', 'No puedes asignar (2) operarios en el mismo turno.', 'error');
				}

			}

			Swal.fire({
				title: '¿Estás seguro?',
				text: 'Recuerda que puedes intercambiar al opeario luego.',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Si',
				cancelButtonText: 'No',
			}).then((result) => {
				if (result.value) {
					Swal.fire({
						title: 'Asignado!',
						text: 'El opeario ha sido asignado satisfactoriamente.',
						icon: 'success'
					}).then(function () {

						$.post("backend/api/operarios/asignar.php", {
							data: JSON.stringify({
								usuarioId: inputOperario.value,
								turno: inputTurno.value,
								material: inputMaterial.value
							})
						}).done(function(){

							window.location = window.location.href;

						});

					});
				}
			});

        }

    });

});

// Modal de Intercambiar Operario.
$('#intercambiarOperarioModal').on('show.bs.modal', function (e) {

	let rowId = $(e.relatedTarget).data('id');

	console.log(rowId);

	// Borramos los select cada vez que undimos el modal
	$("#inputOperario1-modal-edit").empty();
	$("#inputOperario2-modal-edit").empty();

	$.ajax({
        type: 'get',
        url: 'backend/api/utils.php?fun=obtenerOperarios',
        success: function (data) {
			
			const result = JSON.parse(data);

			result.forEach(operario => {

				let option = document.createElement('option');
				option.value = operario.ID;
				option.innerText = operario.NOMBRE.toProperCase();

				if (operario.ID == rowId) {
					inputOperario1Edit.append(option);
				} else {
					inputOperario2Edit.append(option);
				}

			});
          
        }

    });

});

// Accción a realizar al momento de undir el submit de Intercambiar Operarios.
document.getElementById('intercambiarOperarioSubmit').addEventListener('click', function () {

	$.post("backend/api/operarios/intercambiar.php", {
		usuario1: JSON.stringify(
			inputOperario1Edit.value
		),
		usuario2: JSON.stringify(
			inputOperario2Edit.value
		)
	}).done(function(msg) {
		window.location = window.location.href;
	});

});

</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>