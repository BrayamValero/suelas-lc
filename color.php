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
	<div class="table-responsive-lg">
		<table class="table table-bordered text-center" id="tabla">
			<thead class="thead-dark">
				<tr>
					<th scope="col">#</th>
					<th scope="col">Color</th>
					<th scope="col">Codigo</th>
					<th scope="col">Muestra</th>
					<th scope="col">Opciones</th>
				</tr>
			</thead>
			<tbody>
				<?php
					require_once "backend/api/db.php";
					$sql = "SELECT * FROM COLOR;";
					$result = db_query($sql);

					foreach ($result as $row) {
						echo "<tr>";

						echo "<th scope=\"col\">{$row['ID']}</th>";
						echo "<td>". mb_convert_case($row['COLOR'], MB_CASE_TITLE, "UTF-8") ."</td>";
						echo "<td>#{$row['CODIGO']}</td>";
						echo "<td>
								<i class='fa fa-circle' style='color: #{$row['CODIGO']}; -webkit-text-stroke: 1px #dee2e6;'></i>
							</td>";

						echo "<td>
								<a href='#' data-toggle='modal' data-target='#editarColor-modal' data-id='{$row['ID']}'>
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

	<!-- Boton -->
	<div class="d-flex justify-content-center mt-5">
		<a class="btn btn-sm btn-main mx-auto" data-toggle="modal" data-target="#añadirColor-modal" href="#" role="button">Añadir Color</a>
	</div>

	<!-- Modal de añadir Color -->
	<div class="modal fade" id="añadirColor-modal" tabindex="-1" role="dialog" aria-labelledby="añadirColor-modal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">

				<!-- Form -->
				<form action="backend/api/color/crear.php" method="POST">

					<div class="modal-header">
						<h5 class="modal-title"><i class="fas fa-star icon-color"></i> Añadir Color</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
					
						<div class="form-row">

							<div class="form-group col-sm-6">
								<label for="inputAñadirColor-modal">Color</label>
								<input id="inputAñadirColor-modal" type="text" class="form-control" placeholder="Color" name="color" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" required>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputAñadirCodigo-modal">Código</label>
								<input id="inputAñadirCodigo-modal" class="jscolor form-control" value="FFFFFF" name="codigo" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" required>
							</div>

						</div>

					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-sm btn-main" id="submitAñadirColor-modal">Añadir Color</button>
					</div>

				</form>
				
			</div>
		</div>
	</div>
	<!-- / Fin de Modal -->

	<!-- Modal de Editar Color -->
	<div class="modal fade" id="editarColor-modal" tabindex="-1" role="dialog" aria-labelledby="editarColor-modal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">

				<!-- Form -->
				<form action="backend/api/color/editar.php" method="POST">

					<div class="modal-header">
						<h5 class="modal-title"><i class="fab fa-slack-hash icon-color"></i> Editar Color</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">
					
						<div class="form-row">

							<!-- ID escondido para el POST -->
							<input type="hidden" name="id" id="inputEditarId-modal"> 

							<div class="form-group col-sm-6">
								<label for="inputEditarColor-modal">Color</label>
								<input id="inputEditarColor-modal" type="text" class="form-control" placeholder="Color" name="color" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" required>
							</div>

							<div class="form-group col-sm-6">
								<label for="inputEditarCodigo-modal">Código</label>
								<input id="inputEditarCodigo-modal" class="jscolor form-control" name="codigo" autocomplete="off" autocorrect="off" autocapitalize="off" spellcheck="false" required>
							</div>

						</div>

					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="submit" class="btn btn-sm btn-main" id="submitEditarColor-modal">Editar Color</button>
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

// Editar Color > dataType
$('#editarColor-modal').on('show.bs.modal', function (e) {

	let rowid = $(e.relatedTarget).data('id');

	$.ajax({
		type: 'post',
		url: 'backend/api/utils.php?fun=obtenerColor',
		data: 'id=' + rowid,
		dataType: "json",
		success: function (data) {

			$('#inputEditarId-modal').val(data[0].ID);
			$('#inputEditarColor-modal').val(data[0].COLOR.toProperCase());
			$('#inputEditarCodigo-modal').val(data[0].CODIGO);

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