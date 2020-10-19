<?php

// Incluimos el header.php y components.php
$title = 'Series';
require_once 'components/header.php';
require_once 'components/navbar.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR');

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
    <?php get_navbar('Inventario', 'Series', true); ?>

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
								<select id="añadirItem" class="select-multiple form-control" name="suelas[]" multiple="multiple" required>

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
								<select id="editarItem" class="select-multiple form-control" name="suelas_edit[]" multiple="multiple" required>

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

var tabla;
var posicionTabla;

// DATATABLES => Mostrando la tabla STOCK.
$.ajax({
    type: 'get',
    url: 'backend/api/utils.php?fun=obtenerSeries',
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
				{ data: "NOMBRE", title: "Nombre", 
					render: function(value, type, row) {
                        return row.NOMBRE.toProperCase();
					}
				},
                { 
                    data: 'ID',
                    title: "Opciones", render: function(value, type, row) {
                        return `<a href='javascript:void(0)' data-id='${row.ID}' data-toggle='modal' data-target='#editarSerieModal'>
									<i class='fas fa-edit icon-color mr-1'></i>
								</a>
								<a href='javascript:void(0)' data-id='${row.ID}' data-toggle='modal' data-target='#verSerieModal'>
									<i class='fas fa-eye icon-color mr-1'></i>
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

// Select2 => Dependencia de este archivo
$(document).ready(function () {
	$('.select-multiple').select2({
		// dropdownParent: $('#example'),
		language: {
			"noResults": function(){
				return "No se encuentran resultados";
			}
		},
		closeOnSelect: false,
		allowClear: true,
		placeholder: 'Seleccione una marca.'
	});
});

// VISTA => Modal de añadir serie.
$('#añadirSerieModal').on('show.bs.modal', function (e) {
	// Borramos las opciones del select2
	$('.select-multiple').val(null).trigger('change');
});


// EDITAR => Editar una serie.
$('#editarSerieModal').on('show.bs.modal', function (e) {

	let id = $(e.relatedTarget).data('id');

	$('.select-multiple').val(null).trigger('change');

	$.ajax({
		url: `backend/api/utils.php?fun=obtenerSerie&id=${id}`,
		success: function (data) {

			const series = [];
			const result = JSON.parse(data);

			document.getElementById('editarSerieId').value = id;
			document.getElementById('editarNombre').value = result[0].NOMBRE.toProperCase();

			result.forEach(row => {
				series.push(row.SUELA_ID);
			});	

			$('.select-multiple').val(series);
			$('.select-multiple').trigger('change');
	
		}
	});

});

// VER => Ver una serie.
$('#verSerieModal').on('show.bs.modal', function (e) {
	
	let id = $(e.relatedTarget).data('id');

	$.ajax({
		url: `backend/api/utils.php?fun=obtenerSerie&id=${id}`,
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
<?php require_once 'components/footer.php'; ?>