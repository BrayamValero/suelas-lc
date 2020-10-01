<?php

// Incluimos el header.php y components.php
$title = 'Entrega de Material';
require_once 'components/header.php';
require_once 'components/navbar.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'MOLINERO', 'OPERARIO');

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
						
						if($_SESSION['ROL'] == 'MOLINERO') {
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
		if($_SESSION['ROL'] == 'ADMINISTRADOR' || $_SESSION['ROL'] == 'MOLINERO'):
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
	pageLength: 10,
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

				for(let i = 0; i < cantidadesTotales; i++){

					if(parseInt(cantidadesIngresadas[i]) > parseInt(datosRecetas[i].MATERIAL_EXISTENCIA)){
						return Swal.fire("Error", "Ingresaste una cantidad mayor a la disponible en el inventario.", "error");
					}

				}

				Swal.fire({
					title: '¿Estás seguro?',
					text: 'Recuerda que podrás editar el monto hasta el cambio de turno.',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Si',
					cancelButtonText: 'No',
				}).then((result) => {
					if (result.value) {
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
								return Swal.fire('Error','Debes asignar un operario primero para entregarle material.','error');
							}
							window.location.reload();
						});

					}
				});
			}
		});
		
	} else {
		return Swal.fire("Whoops", "Rellene todos los campos.", "warning");
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

				for(var i = 0; i < editarCantidades; i++){

					if(parseInt(cantidadesIngresadas[i]) > parseInt(datosRecetas[i].MATERIAL_EXISTENCIA)){
						return Swal.fire("Error", "Ingresaste una cantidad mayor a la disponible en el inventario.", "error");
					}

				}

				Swal.fire({
					title: '¿Estás seguro?',
					text: 'Recuerda que podrás editar el monto hasta el cambio de turno.',
					icon: 'warning',
					showCancelButton: true,
					confirmButtonText: 'Si',
					cancelButtonText: 'No',
				}).then((result) => {
					if (result.value) {
						Swal.fire({
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
					}
				});

			}
		});
		
	} else {
		return Swal.fire("Whoops", "Rellene todos los campos.", "warning");
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

	Swal.fire({
		title: '¿Estás seguro?',
		text: 'Luego de verificar el material no podrá ser editado..',
		icon: 'warning',
		showCancelButton: true,
		confirmButtonText: 'Si',
		cancelButtonText: 'No',
	}).then((result) => {
		if (result.value) {
			Swal.fire({
				title: '¡Recibido!',
				text: 'El material ha sido recibido satisfactoriamente.',
				icon: 'success'
			}).then(function () {
				window.location = `backend/api/entrega_material/aprobar.php?id=${id}`
			});
		}
	});

};

</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>