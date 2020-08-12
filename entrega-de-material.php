<?php

// Incluimos el header.php y components.php
include 'components/header.php';
include 'components/components.php';

// Filtramos la página para que solo los cargos correspondientes puedan usarla.
if($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'MOLINERO' || $_SESSION['USUARIO']['CARGO'] == 'OPERARIO'):

?>

<!-- Incluimos el sidebar.php -->
<?php include 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">

    <!-- Incluimos el Navbar -->
    <?php get_navbar('Inventario', 'Entrega de Material'); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
	<div class="table-responsive-lg">
		<table class="table table-bordered text-center" id="tabla">
			<thead class="thead-dark">
				<tr>
					<th scope="col">#</th>
					<th scope="col">Fecha</th>
					<th scope="col">Turno</th>
					<th scope="col">Material</th>
					<th scope="col">Operario</th>
					<th scope="col">Estado</th>
					<th scope="col">Opciones</th>
				</tr>
			</thead>
			<tbody id="appendMateriaPrima">
				<?php
				require_once 'backend/api/db.php';
				$sql = "SELECT E_M.ID, E_M.FECHA, E_M.TURNO, E_M.ESTADO, U.NOMBRE, E_M.USUARIO_OPERARIO_ID, E_M.MATERIAL FROM ENTREGA_MATERIAL E_M JOIN USUARIOS U ON E_M.USUARIO_OPERARIO_ID = U.ID;";

				$result = db_query($sql);
				
				// echo '<pre>'; print_r($result); echo '</pre>';

				foreach ($result as $row) {
					echo "<tr>";
					
					echo "<th>{$row['ID']}</th>";
					echo "<td>" . strftime("%d de %b de %Y, %H:%M %p", strtotime($row['FECHA'])) . "</td>";
					echo "<td>" . mb_convert_case($row['TURNO'], MB_CASE_TITLE, "UTF-8") . "</td>";
					echo "<td>" . mb_convert_case($row['MATERIAL'], MB_CASE_TITLE, "UTF-8") . "</td>";
					echo "<td>" . mb_convert_case($row['NOMBRE'], MB_CASE_TITLE, "UTF-8") . "</td>";

						// Verificación de Status
						if ($row['ESTADO'] === 'PENDIENTE') {
							if ($_SESSION['USUARIO']['ID'] == $row['USUARIO_OPERARIO_ID']) {
								echo "<td> 
								<button class='btn btn-sm btn-main' onclick='confirmar_entrega({$row['ID']})'>Pendiente</button>
							</td>";
							} else {
								echo "<td>Pendiente</td>";
							}
					} else {
						echo "<td><i class='fas fa-check icon-color'></i></td>";
					}

					// Verificación de Status
					if ($row['ESTADO'] === 'PENDIENTE') {
						echo "<td>";
						
						if($_SESSION['USUARIO']['CARGO'] == 'MOLINERO') {
							echo "<a href='#' data-toggle='modal' data-target='#editarMaterialEntregadoModal' data-id='{$row['ID']}'>
								<i class='fas fa-edit icon-color'></i>
							</a>";
						}
						
						echo "<a href='#' data-toggle='modal' data-target='#mostrarMaterialEntregadoModal' data-id='{$row['ID']}'>
								<i class='fas fa-eye icon-color'></i>
							</a>
						</td>";

					} else {
						echo "<td>
								<a href='#' data-toggle='modal' data-target='#mostrarMaterialEntregadoModal' data-id='{$row['ID']}'>
									<i class='fas fa-eye icon-color'></i>
								</a>
							</td>";
					}
					
					echo "</tr>";
				}
				?>
			</tbody>
		</table>
	</div>
	<!-- / Fin de Tabla -->

	<!-- Modal de Entregar Material  -->
	<div class="modal fade" id="entregarMaterialModal" tabindex="-1" role="dialog" aria-labelledby="entregarMaterialModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">

				<!-- Form -->
				<form>

					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"><i class="fab fa-react icon-color"></i> Entrega de Material</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">

						<div class="form-row">

							<div class="form-group col-sm-6">
								<label for="inputTurno-modal-add-1">Turno</label>
								<select id="inputTurno-modal-add-1" name="turno-1" class="form-control">
									<option value="DIURNO">Diurno</option>
									<option value="NOCTURNO">Nocturno</option>
								</select>
							</div>  	

							<div class="form-group col-sm-6">
								<label for="inputMaterial-modal-add-1">Material</label>
								<input id="inputMaterial-modal-add-1" name="material-1" type="text" class="form-control" readonly>
							</div>

							<div class="form-group col-sm-12">
								<label for="inputFormula-modal-add-1">Fórmula</label>
								<select id="inputFormula-modal-add-1" name="formula-1" class="form-control">
								</select>
							</div>

						</div>

						<h6 class="text-center pb-2">Materiales</h6>

						<!-- Contenedor para cambiar los opciones cuando se use el dropdown de Fórmula -->
						<div class="form-row" id="cambiar_formula_add">
						</div>

					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-sm btn-main" id="entregarMaterial">Entregar Material</button>
					</div>
				
				</form>
				<!-- End of Form -->
				
			</div>
		</div>
	</div>
	<!-- Fin de Modal de Entregar Material -->

	<!-- Editar Material Entregado -->
	<div class="modal fade" id="editarMaterialEntregadoModal" tabindex="-1" role="dialog" aria-labelledby="editarMaterialEntregadoModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">

				<!-- Form -->
				<form>

					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"><i class="fab fa-react icon-color"></i> Editar Material Entregado</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">

						<div class="form-row">

							<div class="form-group col-sm-6">
								<label for="inputTurno-modal-edit-1">Turno</label>
								<input id="inputTurno-modal-edit-1" name="turno-1" type="text" class="form-control" readonly>
							</div>  	

							<div class="form-group col-sm-6">
								<label for="inputMaterial-modal-edit-1">Material</label>
								<input id="inputMaterial-modal-edit-1" name="material-1" type="text" class="form-control" readonly>
							</div>

							<div class="form-group col-sm-12">
								<label for="inputFormula-modal-edit-1">Fórmula</label>
								<input id="inputFormula-modal-edit-1" name="formula-1" type="text" class="form-control" readonly>
								<!-- Hidden Formula Id -->
								<input id="inputFormulaId-modal-edit-1" name="id-1" type="hidden" class="form-control" readonly>
							</div>

						</div>

						<h6 class="text-center pb-2">Materiales</h6>

						<!-- Contenedor para cambiar los opciones cuando se cambie el dropdown de Material -->
						<div class="form-row" id="cambiar_formula_edit">
						</div>

					</div>
					
					<div class="modal-footer">
						<button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
						<button type="button" class="btn btn-sm btn-main" id="entregarMaterialEdit">Editar Entrega de Material</button>
					</div>
				
				</form>
				<!-- End of Form -->
				
			</div>
		</div>
	</div>
	<!-- Fin de Editar Material Entregado -->

	<!-- Mostrar Material Entregado -->
	<div class="modal fade" id="mostrarMaterialEntregadoModal" tabindex="-1" role="dialog" aria-labelledby="mostrarMaterialEntregadoModal" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">

				<!-- Form -->
				<form>

					<div class="modal-header">
						<h5 class="modal-title" id="exampleModalLabel"><i class="fab fa-react icon-color"></i> Mostrar Material Entregado</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
						</button>
					</div>

					<div class="modal-body">

						<div class="form-row">

							<div class="form-group col-sm-6">
								<label for="inputTurno-modal-view-1">Turno</label>
								<input id="inputTurno-modal-view-1" name="turno-1" type="text" class="form-control" readonly>
							</div>  	

							<div class="form-group col-sm-6">
								<label for="inputMaterial-modal-view-1">Material</label>
								<input id="inputMaterial-modal-view-1" name="material-1" type="text" class="form-control" readonly>
							</div>

							<div class="form-group col-sm-12">
								<label for="inputFormula-modal-view-1">Fórmula</label>
								<input id="inputFormula-modal-view-1" name="formula-1" type="text" class="form-control" readonly>
							</div>

						</div>
	
						<!-- Tabla para Mostrar los componentes de la formula entregada -->
						<div class="table-responsive-lg py-3">
							<table class="table table-bordered text-center" id="tabla-modal">
							
								<!-- Table Head -->
								<thead class="thead-dark">
									<tr>
										<th class="align-middle" scope="col">Item</th>
										<th class="align-middle" scope="col">Cantidad</th>
									</tr>
								</thead>
								<!-- Table Body -->
								<tbody>
								</tbody>

							</table>
						</div>
						<!-- End of Table -->
					</div>
				</form>
				<!-- End of Form -->
			</div>
		</div>
	</div>
	<!-- Fin de Editar Material Entregado -->

	<?php
		if($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'MOLINERO'):
	?>
		<div class="row mt-5">
			<a id="comprobarMateriales" class="btn btn-sm btn-main mx-auto" data-toggle="modal" data-target="#entregarMaterialModal" href="#" role="button">Entregar Material</a>
		</div>
	<?php
		endif;
	?>

</div>
<!-- / Fin de Contenido -->


<!-- Inline JavaScript -->
<script>

// Declaramos las variables globales y constantes que se van a usar.
var datosFormulas, cantidadesTotales, editarCantidades, rowEdit;

// Añadir Constantes para la Entrega de Material.
const inputTurnoAdd = document.getElementById('inputTurno-modal-add-1');
const inputMaterialAdd = document.getElementById('inputMaterial-modal-add-1');
const inputFormulaAdd = document.getElementById('inputFormula-modal-add-1');

// Añadir Constantes para Editar los Materiales Entregados.
const inputTurnoView = document.getElementById('inputTurno-modal-view-1');
const inputMaterialView = document.getElementById('inputMaterial-modal-view-1');
const inputFormulaView = document.getElementById('inputFormula-modal-view-1');

// Añadir Constantes para Ver los Materiales Entregados.
const inputTurnoEdit = document.getElementById('inputTurno-modal-edit-1');
const inputMaterialEdit = document.getElementById('inputMaterial-modal-edit-1');
const inputFormulaEdit = document.getElementById('inputFormula-modal-edit-1');
const inputFormulaIdEdit = document.getElementById('inputFormulaId-modal-edit-1');

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

// AJAX => Obtenemos las formulas.
$.ajax({
	type: 'get',
	url: 'backend/api/utils.php?fun=obtenerFormulas',
	success: function (data) {
		
		datosFormulas = JSON.parse(data);

		// Comprobando que hayan formulas aprobadas.
		if(Object.entries(datosFormulas).length === 0){
			return console.log('No hay formulas aprobadas aun.') 
		}

		let primeraFormula = datosFormulas[0].ID;

		datosFormulas.forEach(formula => {

			let option = document.createElement('option');
			option.value = formula.ID;
			option.innerText = formula.NOMBRE.toProperCase();

			// Agregando las opciones al dropdown de Formula.
			inputFormulaAdd.append(option);
			
		});

		// Asignamos el readonly de Material al 1er elemento creado de la Formula.
		inputMaterialAdd.value = datosFormulas[0].MATERIAL.toProperCase();

		// AJAX REQUEST => Obtenemos los materiales usados en las formulas.
		$.ajax({
			type: 'get',
			url: `backend/api/utils.php?fun=obtenerRecetaFormula&id=${primeraFormula}`,
			success: function (data) {

				let i = 1;

				let datosRecetas = JSON.parse(data);
				cantidadesTotales = datosRecetas.length;

				// Borrando la data del row cada vez que se quiera agregar un elemento.
				const rows = $('#cambiar_formula_add');
                rows.empty();

				datosRecetas.forEach(receta => {

					rows.append(
						`<div class="form-group col-sm-8">
							<input id="inputItem-modal-add-${i}" name="item-${i}" type="text" class="form-control" value="${receta.MATERIAL_DESCRIPCION.toProperCase()}" readonly>
							<input id="inputItemId-modal-add-${i}" name="id-${i}" type="hidden" class="form-control" value="${receta.MATERIAL_ID}" readonly>
						</div>

						<div class="form-group col-sm-4">
							<input id="inputCantidad-modal-add-${i}" name="cantidad-${i}" type="number" class="form-control" placeholder="Cantidad" required>
						</div>`
					);

					i++;
			
				});
			}
		});
	}
});


document.getElementById('comprobarMateriales').addEventListener('click', function(){

	// Comprobando que hayan formulas aprobadas.
	if(Object.entries(datosFormulas).length === 0){
			
		let option = document.createElement("option");
		option.innerText = "No hay formulas aprobadas disponible";
		
		inputFormulaAdd.append(option);
		inputFormulaAdd.disabled = true;
		
		inputMaterialAdd.value = "No disponible";

		document.getElementById('entregarMaterial').disabled = true;

	}
	
});
// Al cambiar la Formula, se cambia la data de material e items.
inputFormulaAdd.addEventListener('change', function (evt) {

	let formulaId = inputFormulaAdd.value;

	// A. Material.
	let material = datosFormulas.filter(elemento => {

		//Devuelve el valor TRUE cuando coinciden el elemento.ID y el evento.
		return elemento.ID == evt.srcElement.value;
		
	});

	inputMaterialAdd.value = material[0].MATERIAL.toProperCase();

	// B. Formulas.
	
	// AJAX REQUEST => backend/api/utils.php?fun=obtenerRecetaFormula&id=?
	$.ajax({
		type: 'get',
		url: `backend/api/utils.php?fun=obtenerRecetaFormula&id=${formulaId}`,
		success: function (data) {

			let i = 1;
			let datosRecetas = JSON.parse(data);
			cantidadesTotales = datosRecetas.length;

			// Borrando la data del row cada vez que se quiera agregar un elemento.
			const rows = $('#cambiar_formula_add');
			rows.empty();

			datosRecetas.forEach(receta => {

				rows.append(
					`<div class="form-group col-sm-8">
						<input id="inputItem-modal-add-${i}" name="item-${i}" type="text" class="form-control" value="${receta.MATERIAL_DESCRIPCION.toProperCase()}" readonly>
						<input id="inputItemId-modal-add-${i}" name="id-${i}" type="hidden" class="form-control" value="${receta.MATERIAL_ID}" readonly>
					</div>

					<div class="form-group col-sm-4">
						<input id="inputCantidad-modal-add-${i}" name="cantidad-${i}" type="number" class="form-control" placeholder="Cantidad" required>
					</div>`
				);

				i++;
		
			});
		}
	});

});

// Acción a realizar al momento de entregar el material.
document.getElementById('entregarMaterial').addEventListener('click', function () {

	// console.log(cantidadesTotales);
	let datosEnviar = [];
	let formulaId = inputFormulaAdd.value;

	for (let i = 1; i <= cantidadesTotales; i++) {

		var materiaId = document.getElementById(`inputItemId-modal-add-${i}`);
		var cantidad = document.getElementById(`inputCantidad-modal-add-${i}`);

		const materiales = {
			materiaId: materiaId.value,
			cantidad: cantidad.value
		};

		datosEnviar.push(materiales);
		
	}

	// Mostramos la data a comparar.
	// console.clear();
	// console.log(datosEnviar);

	let cantidadesIngresadas = [];

	datosEnviar.forEach(function(e) {

		cantidadesIngresadas.push(e.cantidad);

	});

	let cantidades = !cantidadesIngresadas.includes("");
	
	if (cantidades) {

		$.ajax({
			type: 'get',
			url: `backend/api/utils.php?fun=obtenerRecetaFormula&id=${formulaId}`,
			success: function (data) {

				let datosRecetas = JSON.parse(data);
				// console.clear();
				// console.log("Comparasión final:");
				// console.log(JSON.stringify(datosRecetas, null, 2));

				for(let i = 0; i < cantidadesTotales; i++){

					if(parseInt(cantidadesIngresadas[i]) > parseInt(datosRecetas[i].MATERIAL_EXISTENCIA)){
						return swal("Error", "Ingresaste una cantidad mayor a la disponible en el inventario.", "error");
					}

				}

				// Luego de verificar todo hacemos la pregunta.
				swal({
					title: "¿Estás seguro?",
					text: "Recuerda que podrás editar el monto hasta el cambio de turno.",
					icon: "warning",
					buttons: [
						'No',
						'Si'
					],
					dangerMode: true,
					}).then(function(isConfirm) {
					if (isConfirm) {
					
						$.post("backend/api/entrega_material/crear.php", {
							data: JSON.stringify({
								formulaId: inputFormulaAdd.value,
								material: inputMaterialAdd.value.toUpperCase(),
								turno: inputTurnoAdd.value
							}),
							materiales: JSON.stringify(datosEnviar)
							}
						).done(data => {

							if(JSON.parse(data) == 'asignar_operario'){
								return swal('Error','Debes asignar un operario primero para entregarle material.','error');
							}
							
							window.location.reload();

							// data = JSON.parse(data);

							// // data[0] es el id de la entrega recien creada
							// // data[1] es el nombre del operario que estaba de turno al momento de crearse la entrega
							// // data[2] es el ID del operario que estaba de turno al momento de crearse la entrega

							// const add = $('#appendMateriaPrima');

							// let date,
							// 	time,
							// 	dateTime,
							// 	today = new Date();


							// // Get Full date with leading zeros
							// date = ('0' + today.getDate()).slice(-2) + '-' + ('0' + (today.getMonth()+1)).slice(-2) + '-' +  today.getFullYear();

							// // Get Full Time with leading zeros
							// time = ('0' + today.getHours()).slice(-2) + ":" + ('0' + today.getMinutes()).slice(-2) + ":" + ('0' + today.getSeconds()).slice(-2);

							// let btnConfirmar;

							// if (data[2] == '<?= $_SESSION['USUARIO']['ID'] ?>') {
							// 	btnConfirmar = `<button class='btn btn-sm btn-main' onclick='confirmar_entrega(${data[0]})'>Pendiente</button>`;
							// } else {
							// 	btnConfirmar = `<button class='btn btn-sm btn-main' disabled>Pendiente</button>`;
							// }

							// // DataTables => https://datatables.net/examples/api/add_row.html
							// var rowNode = tabla.row.add([
							// 	`${data[0]}`,
							// 	`${date}`,
							// 	`${inputTurnoAdd.value.toProperCase()}`,
							// 	`${inputMaterialAdd.value.toProperCase()}`,
							// 	`${data[1].toProperCase()}`,
							// 	btnConfirmar,
							// 	`<a href='#' data-toggle='modal' data-target='#editarMaterialEntregadoModal' data-id='${data[0]}'>
							// 		<i class='fas fa-edit icon-color'></i>
							// 	</a>
							// 	<a href='#' data-toggle='modal' data-target='#mostrarMaterialEntregadoModal' data-id='${data[0]}'>
							// 		<i class='fas fa-eye icon-color'></i>
							// 	</a>`
							// ]).draw(false).node();

							// // Añadiendo clases a DataTables.
							// $(rowNode).find('td').eq(0).addClass('font-weight-bold');
					
							// // Ocultando el modal.
							// // $('#entregarMaterialModal').modal('hide');
					
						});
					} else {
						swal("Cancelado", "Cuando termines puedes intentarlo de nuevo.", "error");
					}
				});
			}
		});
		
	} else {
		return swal("Whoops", "Rellene todos los campos.", "warning");
	}

});

// Modal => Editar Material Entregado
$('#editarMaterialEntregadoModal').on('show.bs.modal', function (e) {

	rowEdit = $(e.relatedTarget).data('id');

	// AJAX => Obtener el Turno, Fecha, Usuario ID, Formula usada.
	$.ajax({
		type: 'post',
		url: 'backend/api/utils.php?fun=obtenerEntregaMaterial',
		data: 'id=' + rowEdit,
		success: function (data) {

			const result = JSON.parse(data);

			// Turno y Material.
			$('#inputTurno-modal-edit-1').val(result[0].TURNO.toProperCase());
			$('#inputMaterial-modal-edit-1').val(result[0].MATERIAL.toProperCase());

			// Id de la Formula para pasarlo al AJAX request.
			let formulaId = result[0].FORMULA_ID;

			// AJAX => Obtener el Nombre de la fórmula.
			$.ajax({
				type: 'get',
				url: `backend/api/utils.php?fun=obtenerFormula&id=${formulaId}`,
				success: function (data) {

					const result = JSON.parse(data);

					// Nombre de la fórmula + ID de la fórmula.
					$('#inputFormula-modal-edit-1').val(result[0].NOMBRE.toProperCase());
					$('#inputFormulaId-modal-edit-1').val(result[0].ID.toProperCase());
					
					
				}
			});

		}
	});

	// AJAX => Obtener Materiales y Cantidades usados.
	$.ajax({
		type: 'post',
		url: 'backend/api/utils.php?fun=obtenerMaterialesEntregados',
		data: 'id=' + rowEdit,
		success: function (data) {

			let i = 1;
			
			const result = JSON.parse(data);
				
			editarCantidades = result.length;
		
			// Borrando la data del row cada vez que se quiera agregar un elemento.
			const rows = $('#cambiar_formula_edit');
			rows.empty();


			result.forEach(row => {

				rows.append(
					`<div class="form-group col-sm-8">
						<input id="inputItem-modal-edit-${i}" name="item-${i}" type="text" class="form-control" value="${row.DESCRIPCION.toProperCase()}" readonly>
						<input id="inputItemId-modal-edit-${i}" name="id-${i}" type="hidden" class="form-control" value="${row.MATERIAL_ID}" readonly>
					</div>

					<div class="form-group col-sm-4">
						<input id="inputCantidad-modal-edit-${i}" name="cantidad-${i}" type="number" class="form-control" placeholder="Cantidad" value="${row.CANTIDAD}" required>
					</div>`
				);

				i++;
				
			});

		}
	});

});

// Acción a realizar al momento de Editar las Cantidades
document.getElementById('entregarMaterialEdit').addEventListener('click', function () {

	// console.log(editarCantidades);
	let datosEnviar = [];
	let formulaId = inputFormulaIdEdit.value;

	for (let i = 1; i <= editarCantidades; i++) {

		var materiaId = document.getElementById(`inputItemId-modal-edit-${i}`);
		var cantidad = document.getElementById(`inputCantidad-modal-edit-${i}`);
		
		const materiales = {
			materiaId: materiaId.value,
			cantidad: cantidad.value
		};

		datosEnviar.push(materiales);
		
	}

	// Mostramos la data a comparar.
	// console.log(datosEnviar);

	var cantidadesIngresadas = [];

	datosEnviar.forEach(function(e) {

		cantidadesIngresadas.push(e.cantidad);

	});

	var cantidades = !cantidadesIngresadas.includes("");
	
	if (cantidades) {

		$.ajax({
			type: 'get',
			url: `backend/api/utils.php?fun=obtenerRecetaFormula&id=${formulaId}`,
			success: function (data) {

				let datosRecetas = JSON.parse(data);
				// console.clear();
				// console.log(JSON.stringify(datosRecetas, null, 2));

				for(var i = 0; i < editarCantidades; i++){

					if(parseInt(cantidadesIngresadas[i]) > parseInt(datosRecetas[i].MATERIAL_EXISTENCIA)){
						return swal("Error", "Ingresaste una cantidad mayor a la disponible en el inventario.", "error");
					}

				}
				
				swal({
					title: "¿Estás seguro?",
					text: "Recuerda que podrás editar el monto hasta el cambio de turno.",
					icon: "warning",
					buttons: [
						'No',
						'Si'
					],
					dangerMode: true,
					}).then(function(isConfirm) {
					if (isConfirm) {
						swal({
						title: 'Editado!',
						text: 'Las cantidades han sido editadas satisfactoriamente.',
						icon: 'success'
						}).then(function() {
							$.post("backend/api/entrega_material/editar.php", {
								'entrega-material-id': rowEdit,
								materiales: JSON.stringify(datosEnviar)
                                }
							).done(function(){
								$('#editarMaterialEntregadoModal').modal('hide');
							});
						});
					} else {
						swal("Cancelado", "Cuando termines puedes intentarlo de nuevo.", "error");
					}
				});
			}
		});
		
	} else {
		return swal("Whoops", "Rellene todos los campos.", "warning");
	}

});

// Modal => Mostrar Material Entregado
$('#mostrarMaterialEntregadoModal').on('show.bs.modal', function (e) {

	let rowid = $(e.relatedTarget).data('id');

	// AJAX => Obtener el Turno, Fecha, Usuario ID, Formula usada.
	$.ajax({
		type: 'post',
		url: 'backend/api/utils.php?fun=obtenerEntregaMaterial',
		data: 'id=' + rowid,
		success: function (data) {

			const result = JSON.parse(data);

			// Material y Turno.
			$('#inputTurno-modal-view-1').val(result[0].TURNO.toProperCase());
			$('#inputMaterial-modal-view-1').val(result[0].MATERIAL.toProperCase());

			// Id de la Formula para pasarlo al AJAX request.
			let formulaId = result[0].FORMULA_ID;

			// AJAX => Obtener el Nombre de la fórmula.
			$.ajax({
				type: 'get',
				url: `backend/api/utils.php?fun=obtenerFormula&id=${formulaId}`,
				success: function (data) {

					const result = JSON.parse(data);

					// Nombre de la Formula.
					$('#inputFormula-modal-view-1').val(result[0].NOMBRE.toProperCase());
					
				}
			});

		}
	});

	// AJAX => Obtener Materiales y Cantidades usados.
	$.ajax({
		type: 'post',
		url: 'backend/api/utils.php?fun=obtenerMaterialesEntregados',
		data: 'id=' + rowid,
		success: function (data) {

			const result = JSON.parse(data);
			const tabla = $('#tabla-modal > tbody:last-child');
			tabla.empty();

			result.forEach(row => {
				tabla.append(`<tr>
								<td>${row.DESCRIPCION.toProperCase()}</td>
								<td>${row.CANTIDAD} Kg</td>
							</tr>`);
			});
		}
	});

});

// Confirmar la entrega de PENDIENTE => CHECK
function confirmar_entrega(id) {
	swal({
		title: "¿Estás seguro?",
		text: "Luego de verificar el material no podrá ser editado.",
		icon: "warning",
		buttons: [
			'No',
			'Si'
		],
		dangerMode: true,
	}).then(function (isConfirm) {
		if (isConfirm) {
			swal({
				title: '¡Recibido!',
				text: 'El material ha sido recibido satisfactoriamente.',
				icon: 'success'
			}).then(function () {
				window.location = `backend/api/entrega_material/aprobar.php?id=${id}`
			});
		} else {
			swal("Cancelado", "Descuida, puedes volver a intentarlo luego.", "error");
		}
	});
};

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