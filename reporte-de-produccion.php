<?php

// Incluimos el header.php y components.php
include 'components/header.php';
include 'components/components.php';

// Filtramos la página para que solo los cargos correspondientes puedan usarla.
if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'MOLINERO' || $_SESSION['USUARIO']['CARGO'] == 'OPERARIO' || $_SESSION['USUARIO']['CARGO'] == 'PRODUCCION'): 

?>

<!-- Incluimos el sidebar.php -->
<?php include 'components/sidebar.php' ?>

<!-- Page Content -->
<div id="contenido">

    <!-- Incluimos el Navbar -->
    <?php get_navbar('Producción', 'Reporte de Producción'); ?>


    <!-- Filtro de Cargos -->
    <?php
    $boton_visible = false;

    $sql = "SELECT * FROM USUARIOS WHERE ID = ?;";
    $data = array($_SESSION['USUARIO']['ID']);

    $usuario = db_query($sql, $data);

    if ($usuario[0]['CARGO'] == 'OPERARIO') {
        $sql = "SELECT * FROM OPERARIOS WHERE USUARIO_ID = ?;";
        $data = array($_SESSION['USUARIO']['ID']);

        $operario = db_query($sql, $data);

        if (!empty($operario)) {
            $boton_visible = true;
        }
    }
    ?>

    <!-- Table -->
    <div class="table-responsive-lg">
        <table class="table table-bordered text-center" id="tabla">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Fecha</th>
                    <th scope="col">Operario</th>
                    <th scope="col">Turno</th>
                    <th scope="col">Material</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Opciones</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    require_once "backend/api/db.php";

                $sql = "SELECT R.ID AS REPORTE_ID, U.ID, R.TURNO, R.MATERIAL, R.ESTADO, R.FECHA, U.NOMBRE , R.USUARIO_OPERARIO_ID
                    FROM REPORTES R 
                    JOIN USUARIOS U 
                    ON R.USUARIO_OPERARIO_ID = U.ID;";

                    $result = db_query($sql);

                    // echo '<pre>'; print_r($result); echo '</pre>';

                    foreach ($result as $row) {
                        echo "<tr>";
    
                        echo "<th scope='col'>{$row['REPORTE_ID']}</th>";
                        echo "<td>" . strftime("%d de %b de %Y, %H:%M %p", strtotime($row['FECHA'])) . "</td>";
                        echo "<td>". mb_convert_case($row['NOMBRE'], MB_CASE_TITLE, 'UTF-8') ."</td>";
                        echo "<td>". mb_convert_case($row['TURNO'], MB_CASE_TITLE, 'UTF-8') ."</td>";
                        echo "<td>". mb_convert_case($row['MATERIAL'], MB_CASE_TITLE, 'UTF-8') ."</td>";

                        // Verificación de status.
                        if ($row['ESTADO'] === 'PENDIENTE') {
                            if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'MOLINERO') {
                                echo "<td>
                                    <button class='btn btn-sm btn-main' onclick='confirmar_reporte({$row['REPORTE_ID']})'>Pendiente</button>
                                </td>";
                            } else {
                                echo "<td>Pendiente</td>";
                            }

                            echo "<td>";

                            if ($row['USUARIO_OPERARIO_ID'] == $_SESSION['USUARIO']['ID']) {
                                echo "<a class='ml-1' href='#' data-toggle='modal' data-target='#editarReporteModal' data-id='{$row['REPORTE_ID']}'>
                                        <i class='fas fa-edit icon-color'></i>
                                    </a>";
                            }

                            echo "<a href='#' data-toggle='modal' data-target='#verReporteModal' data-id='{$row['REPORTE_ID']}'>
                                        <i class='fas fa-eye icon-color'></i>
                                    </a>
                                </td>";
                        } else {
                            echo "<td>Aprobado</td>";
                            echo "<td>
                                    <a href='#' data-toggle='modal' data-target='#verReporteModal' data-id='{$row['REPORTE_ID']}'>
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
    <!-- / Tabla -->

    <!-- Modal de Añadir Reporte -->
    <div class="modal fade" id="añadirReporteModal" tabindex="-1" role="dialog" aria-labelledby="añadirReporteModal"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <form id="form-add-reporte">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Añadir Reporte</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <!-- Form Row -->
                        <div class="form-row">

                            <div class="form-group col-sm-6">
                                <label for="inputMateriaRecibida-modal-add">Materia Recibida</label>
                                <input id="inputMateriaRecibida-modal-add" name="materia-recibida" type="number"
                                        min="0" class="form-control" readonly>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputMateriaSobrante-modal-add">Materia Sobrante</label>
                                <input id="inputMateriaSobrante-modal-add" name="materia-sobrante" type="number"
                                        min="0" class="form-control" placeholder="Materia Sobrante" required>
                            </div>

                        </div>

                        <div class="form-row">

                            <div class="form-group col-sm-6">
                                <label for="inputColillas-modal-add">Colillas</label>
                                <input id="inputColillas-modal-add" name="colillas" type="number" min="0"
                                        class="form-control" placeholder="Colillas" required>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputPatas-modal-add">Patas</label>
                                <input id="inputPatas-modal-add" name="total" type="number" min="0"
                                        class="form-control" placeholder="Patas" required>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputVarios-modal-add">Varios</label>
                                <input id="inputVarios-modal-add" name="varios" type="number" min="0"
                                        class="form-control" placeholder="Varios" required>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputProduccionActual-modal-add">Producción actual</label>
                                <input id="inputProduccionActual-modal-add" name="total" type="number" min="0"
                                        class="form-control" placeholder="Producción Actual" required>
                            </div>

                            <div class="form-group col-sm-12">
                                <label for="inputObservaciones-modal-add">Observaciones</label>
                                <input id="inputObservaciones-modal-add" name="observaciones" type="text"
                                        class="form-control" placeholder="Observaciones">
                            </div>

                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-sm btn-main" id="añadirReporteSubmit">Añadir Reporte
                        </button>
                    </div>

                </form>
                <!-- End of Form -->
            </div>
        </div>
    </div>
    <!-- Fin de Modal de Añadir Reporte -->

    <!-- Modal de Editar Reporte -->
    <div class="modal fade" id="editarReporteModal" tabindex="-1" role="dialog" aria-labelledby="editarReporteModal"
    aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <form>

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Editar Reporte</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <!-- Form Row -->
                        <div class="form-row">

                            <div class="form-group col-sm-6">
                                <label for="inputMateriaRecibida-modal-edit">Materia Recibida</label>
                                <input id="inputMateriaRecibida-modal-edit" name="materia-recibida" type="text"
                                        class="form-control" readonly>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputMateriaSobrante-modal-edit">Materia Sobrante</label>
                                <input id="inputMateriaSobrante-modal-edit" name="materia-sobrante" type="number"
                                        min="0" class="form-control" placeholder="Materia Sobrante" required>
                            </div>

                        </div>

                        <div class="form-row">

                            <div class="form-group col-sm-6">
                                <label for="inputColillas-modal-edit">Colillas</label>
                                <input id="inputColillas-modal-edit" name="colillas" type="number" min="0"
                                        class="form-control" placeholder="Colillas" required>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputPatas-modal-edit">Patas</label>
                                <input id="inputPatas-modal-edit" name="total" type="number" min="0"
                                        class="form-control" placeholder="Patas" required>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputVarios-modal-edit">Varios</label>
                                <input id="inputVarios-modal-edit" name="varios" type="number" min="0"
                                        class="form-control" placeholder="Varios" required>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputProduccionActual-modal-edit">Producción actual</label>
                                <input id="inputProduccionActual-modal-edit" name="total" type="number"
                                        class="form-control" placeholder="Producción Actual">
                            </div>

                            <div class="form-group col-sm-12">
                                <label for="inputObservaciones-modal-edit">Observaciones</label>
                                <input id="inputObservaciones-modal-edit" name="observaciones" type="text"
                                        class="form-control" placeholder="Observaciones">
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" class="btn btn-sm btn-main" id="editarReporteSubmit">Editar Reporte
                        </button>
                    </div>

                </form>
                <!-- End of Form -->
            </div>
        </div>
    </div>
    <!-- Fin de Modal de Editar Reporte -->

    <!-- Modal de Ver Reporte -->
    <div class="modal fade" id="verReporteModal" tabindex="-1" role="dialog" aria-labelledby="verReporteModal"
    aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <form>

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Ver Reporte</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <!-- Form Row -->
                        <div class="form-row">

                            <div class="form-group col-sm-6">
                                <label for="inputMateriaRecibida-modal-view">Materia Recibida</label>
                                <input id="inputMateriaRecibida-modal-view" name="materia-recibida" type="text" class="form-control" readonly>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputMateriaSobrante-modal-view">Materia Sobrante</label>
                                <input id="inputMateriaSobrante-modal-view" name="materia-sobrante" type="text" class="form-control" readonly>
                            </div>

                        </div>
                            
                        <div class="form-row">

                            <div class="form-group col-sm-6">
                                <label for="inputColillas-modal-view">Colillas</label>
                                <input id="inputColillas-modal-view" name="colillas" type="text" class="form-control" readonly>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputPatas-modal-view">Patas</label>
                                <input id="inputPatas-modal-view" name="total" type="text" class="form-control" readonly>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputVarios-modal-view">Varios</label>
                                <input id="inputVarios-modal-view" name="varios" type="text" class="form-control" readonly>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputProduccionActual-modal-view">Producción actual</label>
                                <input id="inputProduccionActual-modal-view" name="total" type="text" class="form-control" readonly>
                            </div>

                            <div class="form-group col-sm-12">
                                <label for="inputObservaciones-modal-view">Observaciones</label>
                                <input id="inputObservaciones-modal-view" name="observaciones" type="text" class="form-control" readonly>
                            </div>

                        </div>

                    </div>

                </form>
                <!-- End of Form -->
            </div>
        </div>
    </div>
    <!-- Fin de Modal de Ver Reporte -->

    <?php
    if ($boton_visible) {
        echo "<div class='row mt-5 justify-content-center'>
                <a class='btn btn-sm btn-main' data-toggle='modal' data-target='#añadirReporteModal' href='#'
                role='button'>Añadir Reporte</a>
            </div>";
    }
    ?>

</div>
<!-- / Fin de contenido-->

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
        targets: 6,
        searchable: true,
        orderable: true,
        className: "align-middle", "targets": "_all"
    }],
    language: {
        "url": "<?= BASE_URL . "datatables/Spanish.json"; ?>"
    }
});

// Variables Globales
var reporte_id;

// Añadir Constantes para Añadir Reporte.
const inputMateriaRecibidaAdd = document.getElementById('inputMateriaRecibida-modal-add');
const inputMateriaSobranteAdd = document.getElementById('inputMateriaSobrante-modal-add');
const inputColillasAdd = document.getElementById('inputColillas-modal-add');
const inputPatasAdd = document.getElementById('inputPatas-modal-add');
const inputVariosAdd = document.getElementById('inputVarios-modal-add');
const inputProduccionActualAdd = document.getElementById('inputProduccionActual-modal-add');
const inputObservacionesAdd = document.getElementById('inputObservaciones-modal-add');

// Añadir Constantes para Editar Reporte.
const inputMateriaRecibidaEdit = document.getElementById('inputMateriaRecibida-modal-edit');
const inputMateriaSobranteEdit = document.getElementById('inputMateriaSobrante-modal-edit');
const inputColillasEdit = document.getElementById('inputColillas-modal-edit');
const inputPatasEdit = document.getElementById('inputPatas-modal-edit');
const inputVariosEdit = document.getElementById('inputVarios-modal-edit');
const inputProduccionActualEdit = document.getElementById('inputProduccionActual-modal-edit');
const inputObservacionesEdit = document.getElementById('inputObservaciones-modal-edit');

// Añadir Constantes para Ver Reporte.
const inputMateriaRecibidaView = document.getElementById('inputMateriaRecibida-modal-view');
const inputMateriaSobranteView = document.getElementById('inputMateriaSobrante-modal-view');
const inputColillasView = document.getElementById('inputColillas-modal-view');
const inputPatasView = document.getElementById('inputPatas-modal-view');
const inputVariosView = document.getElementById('inputVarios-modal-view');
const inputProduccionActualView = document.getElementById('inputProduccionActual-modal-view');
const inputObservacionesView = document.getElementById('inputObservaciones-modal-view');

// Script > Mostrar botón a los opearios.
<?php if ($boton_visible): ?>

let usuario_id = <?= "'".$_SESSION['USUARIO']['ID']."'"; ?>;
let operario_turno = <?= "'".$operario[0]['TURNO']."'"; ?>;
let operario_material = <?= "'".$operario[0]['MATERIAL']."'"; ?>;

$.ajax({
    type: 'get',
    url: `backend/api/utils.php?fun=obtenerMateriaRecibidaReporte&turno=${operario_turno}&material=${operario_material}`,
    success: function (data) {
        const result = JSON.parse(data);

        if (result.TOTAL == 0) {
            Swal.fire('Atención', 'No hay materia entregada aun por parte del Molinero. Asímismo, no hay materia sobrante del turno anterior.', 'warning');
        }

        inputMateriaRecibidaAdd.value = result.TOTAL;
        inputProduccionActualAdd.value = result.PESADO;
    }
});

// Accción a realizar al momento de undir el submit de Añadir Reporte
document.getElementById('añadirReporteSubmit').addEventListener('click', function () {

    // se chequea que todos los inputs del form esten rellenos
    if (!document.getElementById('form-add-reporte').checkValidity()) {
        return alert("Por favor rellene todos los campos");
    }

    $.post("backend/api/reportes/crear.php", {
        usuario_operario_id: JSON.stringify(
            usuario_id
        ),
        turno: JSON.stringify(
            operario_turno
        ),
        material: JSON.stringify(
            operario_material
            ),
            materia_recibida: JSON.stringify(
                inputMateriaRecibidaAdd.value
            ),
            materia_sobrante: JSON.stringify(
                inputMateriaSobranteAdd.value
            ),
            colillas: JSON.stringify(
                inputColillasAdd.value
            ),
            patas: JSON.stringify(
                inputPatasAdd.value
            ),
            varios: JSON.stringify(
                inputVariosAdd.value
            ),
            produccion_actual: JSON.stringify(
                inputProduccionActualAdd.value
            ),
            observaciones: JSON.stringify(
                inputObservacionesAdd.value
            )
        }).done(function(msg) {

            window.location = window.location.href;

        });

    });

    // Editar Reportes de Producción
    $('#editarReporteModal').on('show.bs.modal', function (e) {

        let rowid = $(e.relatedTarget).data('id');

        // Guardando el id del reporte a editar en una variable global.
        reporte_id = rowid;

        $.ajax({
            type: 'post',
            url: 'backend/api/utils.php?fun=obtenerReporteProduccion',
            data: `id=${rowid}`,
            success: function (data) {

                const result = JSON.parse(data);

                // Mostramos los datos a editar.
                inputMateriaRecibidaEdit.value = result[0].MATERIA_RECIBIDA;
                inputMateriaSobranteEdit.value = result[0].MATERIA_SOBRANTE;
                inputColillasEdit.value = result[0].COLILLAS;
                inputPatasEdit.value = result[0].PATAS;
                inputVariosEdit.value = result[0].VARIOS;
                inputProduccionActualEdit.value = result[0].PRODUCCION_ACTUAL;
                inputObservacionesEdit.value = result[0].OBSERVACIONES;

            }
        });

    });

    // Accción a realizar al momento de undir el submit de Editar Reporte
    document.getElementById('editarReporteSubmit').addEventListener('click', function () {

        $.post("backend/api/reportes/editar.php", {
            reporte_id: JSON.stringify(
                reporte_id
            ),
            turno: JSON.stringify(
                operario_turno
            ),
            material: JSON.stringify(
                operario_material
            ),
            materia_recibida: JSON.stringify(
                inputMateriaRecibidaEdit.value
            ),
            materia_sobrante: JSON.stringify(
                inputMateriaSobranteEdit.value
            ),
            colillas: JSON.stringify(
                inputColillasEdit.value
            ),
            patas: JSON.stringify(
                inputPatasEdit.value
            ),
            varios: JSON.stringify(
                inputVariosEdit.value
            ),
            produccion_actual: JSON.stringify(
                inputProduccionActualEdit.value
            ),
            observaciones: JSON.stringify(
                inputObservacionesEdit.value
            )
        }).done(function(msg) {
            window.location = window.location.href;
        });

    });

<?php endif; ?>

// Mostrar Reportes de Producción
$('#verReporteModal').on('show.bs.modal', function (e) {

	let rowid = $(e.relatedTarget).data('id');

	$.ajax({
		type: 'post',
		url: 'backend/api/utils.php?fun=obtenerReporteProduccion',
		data: `id=${rowid}`,
		success: function (data) {

			const result = JSON.parse(data);

            inputMateriaRecibidaView.value = result[0].MATERIA_RECIBIDA;
            inputMateriaSobranteView.value = result[0].MATERIA_SOBRANTE;
            inputColillasView.value = result[0].COLILLAS;
            inputPatasView.value = result[0].PATAS;
            inputVariosView.value = result[0].VARIOS;
            inputProduccionActualView.value = result[0].PRODUCCION_ACTUAL;
            inputObservacionesView.value = result[0].OBSERVACIONES;

		}
	});

});

// Aprobar Reporte -> Solo Molineros pueden aprobar.
function confirmar_reporte(id) {

    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Luego de aprobar el reporte el operario no podrá editarlo.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {
        if (result.value) {
            Swal.fire({
                title: '!Aprobado!',
                text: 'El reporte ha sido aprobado satisfactoriamente.',
                icon: 'success'
            }).then(function () {
                window.location = `backend/api/reportes/aprobar.php?id=${id}`
            });
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