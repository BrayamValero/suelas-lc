<?php

// Incluimos el header.php y components.php
$title = 'Materia Prima';
require_once 'components/header.php';
require_once 'components/navbar.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'PRODUCCION', 'MOLINERO');

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
    <?php get_navbar('Inventario', 'Materia Prima'); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
    <div class="table-responsive-lg">
        <table class="table table-bordered text-center" id="tabla">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Descripción</th>
                    <th scope="col">Cantidad</th>
                    <th scope="col">Opciones</th>
                </tr>
            </thead>
            <tbody>

                    <?php
                    require_once "backend/api/db.php";
                    $sql = "SELECT * FROM MATERIA_PRIMA;";
                    $result = db_query($sql);

                    // echo '<pre>'; print_r($result); echo '</pre>';

                    foreach ($result as $row) {
                        echo "<tr>";

                        echo "<th scope='col'>{$row['ID']}</th>";
                        echo "<td>". mb_convert_case($row['DESCRIPCION'], MB_CASE_TITLE, "UTF-8") ."</td>";

                        echo "<td>{$row['EXISTENCIA']} Kg</td>";

                        if($_SESSION['ROL'] == 'ADMINISTRADOR'){
                            if ($row['MATERIAL'] != NULL) {
                                echo "<td>
                                    <a href='#' data-toggle='modal' data-target='#editarMateriaPrimaAvanzada-modal' data-id='{$row['ID']}'><i class='fas fa-edit icon-color mr-1'></i></a>
                                    <a href='#' data-toggle='modal' data-target='#añadirMovimiento-modal' data-id='{$row['ID']}' data-operacion='+'><i class='fas fa-plus-circle text-success mr-1'></i></a>
                                    <a href='#' data-toggle='modal' data-target='#añadirMovimiento-modal' data-id='{$row['ID']}' data-operacion='-'><i class='fas fa-minus-circle text-danger'></i></a>
                                    </td>";
                            } else {
                                echo "<td>
                                <a href='#' data-toggle='modal' data-target='#editarMateriaPrima-modal' data-id='{$row['ID']}'><i class='fas fa-edit icon-color mr-1'></i></a>
                                <a href='#' data-toggle='modal' data-target='#añadirMovimiento-modal' data-id='{$row['ID']}' data-operacion='+'><i class='fas fa-plus-circle text-success mr-1'></i></a>
                                <a href='#' data-toggle='modal' data-target='#añadirMovimiento-modal' data-id='{$row['ID']}' data-operacion='-'><i class='fas fa-minus-circle text-danger'></i></a>
                                </td>";
                            }
                        } else {
                            echo "<td><i class='fas fa-ban icon-color'></i></td>";
                        }
                    

                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
    </div>
    <!-- End of Table -->

    <!-- Modal de Añadir Materia Prima -->
    <div class="modal fade" id="añadirMateriaPrima-modal" tabindex="-1" role="dialog" aria-labelledby="añadirMateriaPrima-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                
                <!-- Form -->
                <form action="backend/api/materia-prima/crear.php" method="POST">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i class="fab fa-react icon-color"></i> Añadir Materia Prima</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-row justify-content-center">
                            <div class="form-group col-sm-6">
                                <label for="inputAñadirDescripcion-modal">Descripción</label>
                                <input id="inputAñadirDescripcion-modal" class="form-control" type="text" name="descripcion" placeholder="Descripción" required>
                            </div> 

                            <div class="form-group col-sm-6">
                                <label for="inputAñadirCantidad-modal">Cantidad <span class="badge badge-pill badge-main">Kgs</span></label>
                                <input id="inputAñadirCantidad-modal" class="form-control mb-2" type="number" min="0" step="0.001"  name="cantidad" placeholder="Cantidad" required>
                                <small>Ejemplo: 540 Gramos = 0.54</small>
                            </div>

                        </div>

                        <div id="mostrarVentana" class="form-row justify-content-center" hidden>

                            <div class="form-group col-sm-5">
                                <label for="inputAñadirMaterial-modal">Material</label>
                                <select id="inputAñadirMaterial-modal" class="form-control" name="material">
                                </select>
                            </div> 

                            <div class="form-group col-sm-5">
                                <label for="inputAñadirColor-modal">Color</label>
                                <input id="inputAñadirColor-modal" class="form-control" type="text" name="color" placeholder="Color">
                            </div>

                            <div class="form-group col-sm-2">
                                <label for="inputAñadirDureza-modal">Dureza</label>
                                <input id="inputAñadirDureza-modal" class="form-control" type="number" min="0" max="100" name="dureza" placeholder="%">
                            </div>

                        </div> 

                        <div class="form-row justify-content-center">
                            <button class="btn btn-sm btn-outline-dark mostrarCampos">Mostrar Campos Adicionales</button>
                        </div>

                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-main">Añadir Materia Prima</button>
                    </div>
                
                </form>
                <!-- End of Form -->

            </div>
        </div>
    </div>
    <!-- Fin de Modal de Añadir Materia Prima -->

    <!-- Modal de Editar Materia Prima -->
    <div class="modal fade" id="editarMateriaPrima-modal" tabindex="-1" role="dialog" aria-labelledby="editarMateriaPrima-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                
                <!-- Form -->
                <form action="backend/api/materia-prima/editar.php" method="POST">

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fab fa-react icon-color"></i> Editar Materia Prima</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-row justify-content-center">

                            <div class="form-group" hidden>
                                <!-- ID Escondido -->
                                <input name="id" id="inputEditarId-modal">
                                <!-- Material Vacio Escondido -->
                                <input name="material" id="inputEditarMaterial-modal">
                            </div>

                            <div class="form-group col-sm-12">
                                
                                <label for="inputEditarDescripcion-modal">Descripción</label>
                                <input id="inputEditarDescripcion-modal" type="text" class="form-control" placeholder="Descripción" name="descripcion" required>
                            </div> 
                            
                        </div>

                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-main">Editar Materia Prima</button>
                    </div>
                
                </form>
                <!-- End of Form -->

            </div>
        </div>
    </div>
    <!-- Fin de Modal de Editar Materia Prima -->

    <!-- Modal de Editar Materia Prima Avanzada -->
    <div class="modal fade" id="editarMateriaPrimaAvanzada-modal" tabindex="-1" role="dialog" aria-labelledby="editarMateriaPrimaAvanzada-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                
                <!-- Form -->
                <form action="backend/api/materia-prima/editar.php" method="POST">

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fab fa-react icon-color"></i> Editar Materia Prima</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-row">

                            <div class="form-group col-sm-12">
                                <!-- Hidden ID -->
                                <input type="hidden" name="id" id="inputEditarIdAvanzada-modal">
                                <!-- Fin del ID escondido -->
                                <label for="inputEditarDescripcionAvanzada-modal">Descripción</label>
                                <input id="inputEditarDescripcionAvanzada-modal" type="text" class="form-control" name="descripcion" readonly>
                            </div> 

                        </div>

                        <div class="form-row justify-content-center">

                            <div class="form-group col-sm-5">
                                <label for="inputEditarMaterialAvanzada-modal">Material</label>
                                <select id="inputEditarMaterialAvanzada-modal" class="form-control" name="material" required>
                                    <option value="EXPANSO" selected>EXPANSO</option>
                                    <option value="EXPANSO/PVC">EXPANSO/PVC</option>
                                    <option value="PVC">PVC</option>
                                    <option value="PU">PU</option>
                                </select>
                            </div> 

                            <div class="form-group col-sm-5">
                                <label for="inputEditarColorAvanzada-modal">Color</label>
                                <input id="inputEditarColorAvanzada-modal" class="form-control" type="text" name="color" placeholder="Color" required>
                            </div>

                            <div class="form-group col-sm-2">
                                <label for="inputEditarDurezaAvanzada-modal">Dureza</label>
                                <input id="inputEditarDurezaAvanzada-modal" class="form-control" type="number" min="0" max="100" name="dureza" placeholder="%" required>
                            </div>
                            
                        </div> 
                        
                    </div>
                    
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="subit" class="btn btn-sm btn-main">Editar Materia Prima</button>
                    </div>
                
                </form>
                <!-- End of Form -->

            </div>
        </div>
    </div>

    <!-- Modal de añadir Movimientos de Materia Prima -->
    <div class="modal fade" id="añadirMovimiento-modal" tabindex="-1" role="dialog" aria-labelledby="añadirMovimiento-modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                
                <!-- Form -->
                <form action="backend/api/materia-prima/movimiento.php" method="POST">

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
                        <button type="submit" class="btn btn-sm btn-main" id="submitMovimiento">Editar</button>
                    </div>
                
                </form>
                <!-- End of Form -->

            </div>
        </div>
    </div>
    <!-- Fin de Modal de Añadir Movimientos de Materia Prima -->


    <!-- Fin de Modal de Editar Materia Prima Avanzada -->
    <?php if($_SESSION['ROL'] == 'ADMINISTRADOR' || $_SESSION['ROL'] == 'MOLINERO'): ?>

    <div class="row mt-5">
        <a class="btn btn-sm btn-main mx-auto" data-toggle="modal" data-target="#añadirMateriaPrima-modal" href="#" role="button">Añadir Materia Prima</a>
    </div>

    <?php endif; ?>

</div>
<!-- Fin de Contenido -->

<!-- Inline JS -->
<script>

// Declaracion de variables y constantes.
var operacion, obtenerColores, datosMateria;
const mostrarVentana = document.getElementById('mostrarVentana');
const inputAñadirColor = document.getElementById("inputAñadirColor-modal");
const inputEditarMaterialAvanzada = document.getElementById("inputEditarMaterialAvanzada-modal");
const inputMovimientoCantidad = document.getElementById("inputMovimientoCantidad-modal");

// DataTables Plugin: https://datatables.net/
const tabla = $('#tabla').DataTable({
    info: false,
    dom: "lrtip",
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

// Mostrar / Ocultar Campos Adicionales.
$('.mostrarCampos').click(function () {
    
    event.preventDefault();

    $(this).toggleClass('mostrarCampos');

    if ($(this).hasClass('mostrarCampos')) {
        mostrarVentana.hidden = true;
        $(this).text('Mostrar Campos Adicionales');
        $('#inputAñadirDescripcion-modal').removeAttr('readOnly'); 
        $('#inputAñadirDescripcion-modal').attr('required', '');
        $('#inputAñadirColor-modal').removeAttr('required');
        $('#inputAñadirDureza-modal').removeAttr('required');
        $('#inputAñadirMaterial-modal').empty();
    } else {
        mostrarVentana.hidden = false;
        $(this).text('Ocultar Campos Adicionales');
        $('#inputAñadirDescripcion-modal').value = '';
        $('#inputAñadirDescripcion-modal').attr('readOnly', ''); 
        $('#inputAñadirDescripcion-modal').removeAttr('required');
        $('#inputAñadirColor-modal').attr('required', ''); 
        $('#inputAñadirDureza-modal').attr('required', '');
        $('#inputAñadirMaterial-modal').append(`
            <option value="EXPANSO" selected>EXPANSO</option>
            <option value="EXPANSO/PVC">EXPANSO/PVC</option>
            <option value="PVC">PVC</option>
            <option value="PU">PU</option>
        `);
    }

});

// Modal de Editar Materia Prima
$('#editarMateriaPrima-modal').on('show.bs.modal', function (e) {

    let rowid = $(e.relatedTarget).data('id');

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerMateriaPrimaId',
        data: 'id=' + rowid,
        dataType: 'json',
        success: function (data) {

            $('#inputEditarId-modal').val(data[0].ID);
            $('#inputEditarDescripcion-modal').val(data[0].DESCRIPCION.toProperCase());
            $('#inputEditarCantidad-modal').val(data[0].EXISTENCIA);

        }
    });

});


// Modal de Editar Materia Prima Avanzada
$('#editarMateriaPrimaAvanzada-modal').on('show.bs.modal', function (e) {

    let id = $(e.relatedTarget).data('id');

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerMateriaPrimaId',
        data: 'id=' + id,
        dataType: 'json',
        success: function (data) {

            $('#inputEditarIdAvanzada-modal').val(data[0].ID);
            $('#inputEditarDescripcionAvanzada-modal').val(data[0].DESCRIPCION.toProperCase());
            $('#inputEditarColorAvanzada-modal').val(data[0].COLOR.toProperCase());
            $('#inputEditarCantidadAvanzada-modal').val(data[0].EXISTENCIA);
            $('#inputEditarDurezaAvanzada-modal').val(data[0].DUREZA);

            $("#inputEditarMaterialAvanzada-modal > option").each(function() {
                if( data[0].MATERIAL == this.value ){
                    $(this).prop("selected", true);
                    return false;
                }
            });

        }
    });

});

// Modal de Añadir Movimientos
$('#añadirMovimiento-modal').on('show.bs.modal', function (e) {

    let rowid = $(e.relatedTarget).data('id');
    operacion = $(e.relatedTarget).data('operacion');

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerMateriaPrimaId',
        data: 'id=' + rowid,
        dataType: 'json',
        success: function (data) {
    
            datosMateria = data;

            var titulo = document.getElementById('inputMovimientoTitulo-modal');

            if (operacion == '+') {
                titulo.innerHTML = "Entrada";
            } else {
                titulo.innerHTML = "Salida";
            }

            $('#inputMovimientoId-modal').val(data[0].ID);
            $('#inputMovimientoOperacion-modal').val(operacion);

        }
    });

});

// Submit Movimiento
$(document).on('click', '#submitMovimiento', function () {

    if (operacion == '-') {
        
        if (parseFloat(inputMovimientoCantidad.value) > parseFloat(datosMateria[0].EXISTENCIA)) {
        
            event.preventDefault();
            
            Swal.fire("Error", "No hay stock suficiente para retirar.", "error");

            console.log(`${parseFloat(inputMovimientoCantidad.value)} es mayor a la cantidad disponible que es ${parseFloat(datosMateria[0].EXISTENCIA)}`);
            
        }

    }

});

</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>