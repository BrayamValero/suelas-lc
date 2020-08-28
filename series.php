<?php

// Incluimos el header.php y components.php
include 'components/header.php';
include 'components/components.php';

// Filtramos la página para que solo los cargos correspondientes puedan usarla.
if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR'):

?>

<!-- Incluimos el sidebar.php -->
<?php include 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">

    <!-- Incluimos el Navbar -->
    <?php get_navbar('Inventario', 'Series'); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
	<div class="table-responsive-lg">
		<table class="table table-bordered text-center" id="tabla">
			<thead class="thead-dark">
				<tr>
					<th scope="col">#</th>
					<th scope="col">Nombre</th>
					<th scope="col">Opciones</th>
				</tr>
			</thead>
			<tbody>
				<?php
					require_once "backend/api/db.php";
					$sql = "SELECT * FROM SERIES;";
					$result = db_query($sql);

					foreach ($result as $row) {
						echo "<tr>";

						echo "<th scope=\"col\">{$row['ID']}</th>";
						echo "<td>". mb_convert_case($row['NOMBRE'], MB_CASE_TITLE, "UTF-8") ."</td>";
						
						echo "<td>
								<a href='#' data-toggle='modal' data-target='#verSerieModal' data-id='{$row['ID']}'>
									<i class='fas fa-eye icon-color'></i>
								</a>
								<a href='#' data-toggle='modal' data-target='#editarSerieModal' data-id='{$row['ID']}'>
									<i class='fas fa-edit icon-color'></i>
								</a>
							</td>";

						echo "</tr>";
					}
				?>
			</tbody>
		</table>
	</div>
	<!-- Fin de Tabla -->

	<!-- Modal de añadir Serie -->
	<div class="modal fade" id="añadirSerieModal" tabindex="-1" role="dialog" aria-labelledby="añadirSerieModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-md" role="document">
			<div class="modal-content">
				<form action="backend/api/series/añadir.php" method="POST">
					<div class="modal-header">
						<h5 class="modal-title"><i class="fas fa-star icon-color"></i> Añadir Serie</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-row justify-content-center">
							<div class="form-group col-sm-10">
								<label for="añadirNombre">Nombre</label>
								<input id="añadirNombre" class="form-control" type="text" placeholder="Nombre" name="nombre" required>
							</div>
							<div class="form-group col-sm-10">
								<label for="añadirItem">Marca</label>
								<select id="añadirItem" class="select-multiple form-control" name="suelas[]" multiple="multiple">

								<?php

								$sql = "SELECT * FROM SUELAS;";
								$result = db_query($sql);

								foreach ($result as $row) {
									
									echo "<option value='{$row['ID']}'>" . mb_convert_case($row['MARCA'], MB_CASE_TITLE, "UTF-8") . " " . $row['TALLA'] . "</option>";
									
								}

								?>
															
								</select>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-sm btn-main" id="botonAñadirSerie">Añadir Serie</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Fin de Modal de Añadir Serie -->

	<!-- Modal de Editar Serie -->
	<div class="modal fade" id="editarSerieModal" tabindex="-1" role="dialog" aria-labelledby="editarSerieModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-md" role="document">
			<div class="modal-content">
				<form action="backend/api/series/editar.php" method="POST">
					<div class="modal-header">
						<h5 class="modal-title"><i class="fas fa-star icon-color"></i> Editar Serie</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="form-row justify-content-center">
							<div class="form-group col-sm-10">
								<label for="editarNombre">Nombre</label>
								<input id="editarNombre" class="form-control" type="text" name="nombre" required>
								<!-- ID escondido para el POST -->
								<input type="hidden" name="id" id="editarSerieId"> 
							</div>
							<div class="form-group col-sm-10">
								<label for="editarItem">Marca</label>
								<select id="editarItem" class="select-multiple form-control" name="suelas_edit[]" multiple="multiple">

								<?php

								$sql = "SELECT * FROM SUELAS;";
								$result = db_query($sql);

								foreach ($result as $row) {
									
									echo "<option value='{$row['ID']}'>" . mb_convert_case($row['MARCA'], MB_CASE_TITLE, "UTF-8") . " " . $row['TALLA'] . "</option>";
									
								}

								?>
															
								</select>
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-sm btn-main" id="botonEditarSerie">Editar Serie</button>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Fin de Modal de Editar Serie -->

	<!-- Modal de Ver Serie -->
	<div class="modal fade" id="verSerieModal" tabindex="-1" role="dialog" aria-labelledby="verSerieModal"
			aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered modal-md" role="document">
			<div class="modal-content">
				<form>
					<div class="modal-header">
						<h5 class="modal-title"><i class="fas fa-shopping-bag icon-color"></i> Ver Series</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<div class="table-responsive-lg py-3">
							<table class="table table-bordered text-center" id="verTablaSerieModal">
								<thead class="thead-dark">
									<tr>
										<th class="align-middle" scope="col" id="verNombreSerieModal">Marcas</th>
									</tr>
								</thead>
								<tbody>
								</tbody>
							</table>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<!-- Fin de Modal de Ver Serie -->
	
	<div class="row mt-5">
        <a href="javascript:void(0);" class="btn btn-sm btn-main mx-auto" data-toggle="modal" data-target="#añadirSerieModal" role="button">Añadir Serie</a>
    </div>


</div>
<!-- Fin de contenido -->

<!-- Inline JavaScript -->
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

// 1. Modal de Añadir Serie
$('#añadirSerieModal').on('show.bs.modal', function (e) {
	// Borramos las opciones del select2
	$('.select-multiple').val(null).trigger('change');
});

// 2. Modal de Editar Serie
$('#editarSerieModal').on('show.bs.modal', function (e) {

	let serie_id = $(e.relatedTarget).data('id');

	$('.select-multiple').val(null).trigger('change');

	$.ajax({
		url: `backend/api/utils.php?fun=obtenerSerie&id=${serie_id}`,
		success: function (data) {

			const series = [];
			const result = JSON.parse(data);
			$('#editarSerieId').val(serie_id);
			$('#editarNombre').val(result[0].NOMBRE.toProperCase());

			result.forEach(row => {
				series.push(row.SUELA_ID);
			});	
			
			$('.select-multiple').val(series);
			$('.select-multiple').trigger('change');

		}
	});

});

// 3. Modal de Ver una serie.
$('#verSerieModal').on('show.bs.modal', function (e) {
	
	let serie_id = $(e.relatedTarget).data('id');

	$.ajax({
		url: `backend/api/utils.php?fun=obtenerSerie&id=${serie_id}`,
		success: function (data) {

			const result = JSON.parse(data);
			document.getElementById('verNombreSerieModal').textContent = result[0].NOMBRE.toProperCase();
			const tabla = $('#verTablaSerieModal > tbody:last-child');
			tabla.empty();

			result.forEach(row => {
				tabla.append(`<tr>
								<td>${row.MARCA.toProperCase() + " " + row.TALLA}</td>
							</tr>`);
			});
			
		}
	});
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