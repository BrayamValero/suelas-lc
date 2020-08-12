<?php

// Incluimos el header.php y components.php
include 'components/header.php';
include 'components/components.php';

// Filtramos la página para que solo los cargos correspondientes puedan usarla.
if($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'PRODUCCION' || $_SESSION['USUARIO']['CARGO'] == 'MOLINERO'):

// Si la maquina está en uso no se puede eliminar.
if (isset($_SESSION['eliminar_maquinaria']) && $_SESSION['eliminar_maquinaria'] == false) {
    echo "<script>alert('La maquinaria está en uso');</script>";
}

?>

<!-- Incluimos el sidebar.php -->
<?php include 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">

    <!-- Incluimos el Navbar -->
    <?php get_navbar('Panel de Control', 'Maquinaria'); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
    <div class="table-responsive-lg">
        <table class="table table-bordered text-center" id="tabla">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Nombre</th>
                <th scope="col">Color</th>
                <th scope="col">Material</th>
                <th scope="col">Cap. Máxima</th>
                <th scope="col">Cap. Disponible</th>
                <th scope="col">Cap. en Uso</th>
                <th scope="col">Estado</th>
                <th scope="col">Opciones</th>
            </tr>
            </thead>
            <tbody>
            <?php
            require_once 'backend/api/db.php';
            $sql = "SELECT * FROM MAQUINARIAS;";
            $result = db_query($sql);

            foreach ($result as $row) {
                $capacidad = (int)$row['CAPACIDAD'];
                $disponible = (int)$row['DISPONIBLE'];

                echo "<tr>";

                echo "<th scope='col'>{$row['ID']}</th>";
                echo "<td>" . mb_convert_case($row['NOMBRE'], MB_CASE_TITLE, "UTF-8") . "</td>";
                echo "<td>" . mb_convert_case($row['COLOR'], MB_CASE_TITLE, "UTF-8") . "</td>";
                echo "<td>" . mb_convert_case($row['MATERIAL'], MB_CASE_TITLE, "UTF-8") . "</td>";
                echo "<td>{$row['CAPACIDAD']}</td>";
                echo "<td>{$row['DISPONIBLE']}</td>";
                echo "<td>";
                echo $capacidad - $disponible;
                echo "</td>";
                echo "<td>" . mb_convert_case($row['ESTADO'], MB_CASE_TITLE, "UTF-8") . "</td>";
                echo "<td>
                        <a href='#' data-toggle='modal' data-target='#editarMaquinaria-modal' data-id='{$row['ID']}'>
                            <i class='fas fa-edit icon-color'></i>
                        </a>
                        <a href='#' class='eliminarMaquina' data-id='{$row['ID']}'>
                            <i class='fas fa-trash icon-color'></i>
                        </a>
                    </td>";

                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
    <!-- End of Table -->

    <!-- Modal de Añadir Maquinaria -->
    <div class="modal fade" id="añadirMaquinaria-modal" tabindex="-1" role="dialog" aria-labelledby="añadirMaquinaria-modal"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <form action="backend/api/maquinarias/crear.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-kaaba icon-color"></i> Añadir Maquinaria</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <!-- Form Row -->
                        <div class="form-row">

                            <div class="form-group col-sm-6">
                                <label for="inputAñadirNombre-modal">Nombre</label>
                                <input id="inputAñadirNombre-modal" name="nombre" type="text" class="form-control" placeholder="Nombre" required>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputAñadirColor-modal">Color</label>
                                <select id="inputAñadirColor-modal" name="color" class="form-control filter-select2">
                                </select>
                            </div>

                            <div class="form-group col-sm-3">
                                <label for="inputAñadirMaterial-modal">Material</label>
                                <select id="inputAñadirMaterial-modal" class="form-control filter-select2" name="material">
                                    <option value="EXPANSO" selected>Expanso</option>
                                    <option value="PVC">PVC</option>
                                    <option value="PU">PU</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-3">
                                <label for="inputAñadirCasillas-modal">Casillas</label>
                                <select id="inputAñadirCasillas-modal" class="form-control" name="casillas">
                                    <option value="30" selected>30</option>
                                    <option value="20">20</option>
                                    <option value="5">5</option>
                                    <option value="1">1</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-3">
                                <label for="inputAñadirCapacidad-modal">Capacidad</label>
                                <input id="inputAñadirCapacidad-modal" name="capacidad" type="number" min="0" class="form-control" placeholder="Capacidad" required>
                            </div>

                            <div class="form-group col-sm-3">
                                <label for="inputAñadirEstado-modal">Estado</label>
                                <select id="inputAñadirEstado-modal" class="form-control" name="estado">
                                    <option value="ACTIVO" selected>Activo</option>
                                    <option value="INACTIVO">Inactivo</option>
                                </select>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-main">Añadir Maquinaria</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <!-- Fin de Modal de Añadir Maquinaria -->

    <!-- Modal de Editar Maquinaria -->
    <div class="modal fade" id="editarMaquinaria-modal" tabindex="-1" role="dialog" aria-labelledby="editarMaquinaria-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <form action="backend/api/maquinarias/editar.php" method="POST">

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-kaaba icon-color"></i> Editar maquinaria</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <!-- Form Row -->
                        <div class="form-row">

                            <input type="hidden" name="id" id="inputEditarId-modal">

                            <div class="form-group col-sm-6">
                                <label for="inputEditarNombre-modal">Nombre</label>
                                <input id="inputEditarNombre-modal" name="nombre" type="text" class="form-control" placeholder="Nombre" required>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputEditarColor-modal">Color</label>
                                <select id="inputEditarColor-modal" name="color" class="form-control filter-select2">
                                </select>
                            </div>

                            <div class="form-group col-sm-3">
                                <label for="inputEditarMaterial-modal">Material</label>
                                <input id="inputEditarMaterial-modal" type="text" class="form-control" name="material" readonly>
                            </div>

                            <div class="form-group col-sm-3">
                                <label for="inputEditarCasillas-modal">Casillas</label>
                                <input id="inputEditarCasillas-modal" type="text" class="form-control" name="casillas" readonly>
                            </div>

                            <div class="form-group col-sm-3">
                                <label for="inputEditarCapacidad-modal">Capacidad</label>
                                <input id="inputEditarCapacidad-modal" type="number" min="0" class="form-control"  name="capacidad" placeholder="Capacidad" required>
                            </div>

                            <div class="form-group col-sm-3">
                                <label for="inputEditarEstado-modal">Estado</label>
                                <select id="inputEditarEstado-modal" class="form-control" name="estado">
                                    <option value="ACTIVO" selected>Activo</option>
                                    <option value="INACTIVO">Inactivo</option>
                                </select>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-main">Editar Maquinaria</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <!-- Fin de Modal de Editar Maquinaria -->

    <div class="row mt-5">
        <a class="btn btn-sm btn-main mx-auto" data-toggle="modal" data-target="#añadirMaquinaria-modal" href="#"
            role="button">Añadir Maquinaria</a>
    </div>

</div>
<!-- / Fin de Contenido-->

<!-- Inline JavaScript -->
<script>
// Variables Globales Inicializadas.
var obtenerColores;

// Variables Inicializadas para Añadir.
const inputAñadirColor = document.getElementById('inputAñadirColor-modal');

// Variables Inicializadas para Editar.
const inputEditarId = document.getElementById("inputEditarId-modal");
const inputEditarNombre = document.getElementById("inputEditarNombre-modal");
const inputEditarColor = document.getElementById('inputEditarColor-modal');
const inputEditarMaterial = document.getElementById('inputEditarMaterial-modal');
const inputEditarCasillas = document.getElementById('inputEditarCasillas-modal');
const inputEditarCapacidad = document.getElementById('inputEditarCapacidad-modal');
const inputEditarEstado = document.getElementById('inputEditarEstado-modal');

/* PLUGINS */

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
    $('.filter-select2').select2({
        theme: "bootstrap4",
    });
});

/* FIN DE PLUGINS */

// Obtenemos todos los Colores.
$.ajax({
	type: 'get',
	url: 'backend/api/utils.php?fun=obtenerColores',
	success: function (data) {

		obtenerColores = JSON.parse(data);

		obtenerColores.forEach(row => {

			let option = document.createElement('option');
			option.value = row.COLOR;
			option.innerText = row.COLOR.toProperCase();
			
			inputAñadirColor.append(option);

		});

	}
});

// Editar una Maquina.
$('#editarMaquinaria-modal').on('show.bs.modal', function (e) {
    let rowid = $(e.relatedTarget).data('id');

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerMaquinariaId',
        data: 'id=' + rowid,
        success: function (data) {
            
            const res = JSON.parse(data);

            if (res[0].ESTADO === 'ACTIVO') {
                inputEditarEstado.selectedIndex = 0
            } else {
                inputEditarEstado.selectedIndex = 1
            }

            inputEditarId.value = res[0].ID;
            inputEditarCasillas.value = res[0].CASILLEROS;
            inputEditarCapacidad.value = res[0].CAPACIDAD;
            inputEditarNombre.value = res[0].NOMBRE.toProperCase();
            inputEditarMaterial.value = res[0].MATERIAL.toProperCase();

            // Borramos las Opciones de los colores.
            while(inputEditarColor.firstChild)
            inputEditarColor.removeChild(inputEditarColor.firstChild);

            // forEach para seleccionar el Color correcto.
            obtenerColores.forEach(row => {

                let option = document.createElement('option');
                option.value = row.COLOR;
                option.innerText = row.COLOR.toProperCase();

                if (res[0].COLOR === row.COLOR) {
                    $(option).attr('selected', 'selected');
                }

                inputEditarColor.append(option);

            });

        }
    });
});

// Eliminar una máquina.
$('.eliminarMaquina').on('click', function (e) {

    let row = $(e.target.parentElement).data('id');

    event.preventDefault();

    swal({
        title: "¿Estás seguro?",
        text: "Si eliminas la máquina puedes alterar la producción.",
        icon: "warning",
        buttons: [
        'No',
        'Si'
        ],
        dangerMode: true,
    }).then(function(isConfirm) {
        if (isConfirm) {
        swal({
            title: '¡Eliminada!',
            text: 'La máquina ha sido eliminada.',
            icon: 'success'
        }).then(function() {

            window.location.href = `backend/api/maquinarias/delete.php?id=${row}`
            
        });
        } else {
        swal("Cancelado", "Descuida, puedes volver a intentarlo luego.", "error");
        }
    });
});

</script>

<!-- En caso de que haya un casillero usando esa suela -->
<?php
if (isset($_SESSION['casillero_suela']) && $_SESSION['casillero_suela'] == true) {

    echo "<script>alert('Ya hay un casillero usando esa suela');</script>";

    unset($_SESSION['casillero_suela']);
    $_SESSION['casillero_suela'] = null;
}
?>

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