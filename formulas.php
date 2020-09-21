<?php

// Incluimos el header.php y components.php
$title = 'Formulas';
include 'components/header.php';
include 'components/components.php';
require_once 'backend/api/utils.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'NORSAPLAST');

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
    <?php get_navbar('Inventario', 'Fórmulas'); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
    <div class="table-responsive-lg">
        <table class="table table-bordered text-center" id="tabla">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Formula</th>
                <th scope="col">Material</th>
                <th scope="col">Estado</th>
                <th scope="col">Opciones</th>
            </tr>
            </thead>
            <tbody>
            <?php
            require_once 'backend/api/db.php';

            $sql = "SELECT * FROM FORMULAS;";
            $result = db_query($sql);

            // echo '<pre>'; print_r($result); echo '</pre>';

            foreach ($result as $row) {
                echo "<tr>";

                echo "<th>{$row['ID']}</th>";
                echo "<td>". mb_convert_case($row['NOMBRE'], MB_CASE_TITLE, 'UTF-8') ."</td>";
                echo "<td>". mb_convert_case($row['MATERIAL'], MB_CASE_TITLE, 'UTF-8') ."</td>";

                // Verificación de Status
                if ($row['ESTADO'] === 'PENDIENTE') {

                    if($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR') {
                        echo "<td> 
                            <button class='btn btn-sm btn-main' onclick='aprobarFormula({$row['ID']})'>Pendiente</button>
                            </td>";
                    } else {
                        echo "<td>Pendiente</td>";
                    }

                } else {
                    echo "<td><i class='fas fa-check icon-color'></i></td>";
                }

                echo "<td>";


                // Añadir o quitar Opciones dependiendo del Status
                if ($row['ESTADO'] === 'PENDIENTE') {
                    echo "<a href='javascript:void(0);' data-toggle='modal' data-target='#editarFormulaModal' data-id='{$row['ID']}'>
                            <i class='fas fa-edit icon-color'></i>
                        </a>";
                }

                if($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR'){
                    echo "<a href='javascript:void(0);' data-toggle='modal' data-target='#verFormulaModal' data-id='{$row['ID']}'>
                            <i class='fas fa-eye icon-color'></i>
                        </a>
                        <a href='javascript:void(0);' class='eliminarFormula' data-id='{$row['ID']}'>
                            <i class='fas fa-trash icon-color'></i>
                        </a>
                    </td>";
                } else {
                    echo "<a href='javascript:void(0);' data-toggle='modal' data-target='#verFormulaModal' data-id='{$row['ID']}'>
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

    <!-- Modal de Añadir Formula -->
    <div class="modal fade" id="añadirFormulaModal" tabindex="-1" role="dialog" aria-labelledby="añadirFormulaModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Form -->
                <form>
                    <div class="modal-header">
                        <h5 class="modal-title font-weight-bold"><i class="fab fa-react icon-color"></i>
                            Añadir Formula</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row justify-content-center">
                            <div class="form-group col-sm-6">
                                <label for="añadirNombre">Formula</label>
                                <input name="nombre" type="text" class="form-control" id="añadirNombre" placeholder="Formula" required>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="añadirMaterial">Material</label>
                                <select id="añadirMaterial" class="form-control" name="material">
                                    <option value="EXPANSO">Expanso</option>
                                    <option value="PVC">PVC</option>
                                    <option value="PU">PU</option>
                                    <option value="EVA">Eva</option>
                                    <option value="CAUCHO">Caucho</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                            <div class="form-group col-sm-8">
                                <label for="añadirItem">Item</label>
                                <select id="añadirItem" class="form-control dropdown-select2" name="item">

                                    <?php

                                    $sql = "SELECT * FROM MATERIA_PRIMA;";
                                    $result = db_query($sql);

                                    foreach ($result as $row) {
                                        
                                        echo "<option value='{$row['ID']}'>" . mb_convert_case($row['DESCRIPCION'], MB_CASE_TITLE, "UTF-8") . "</option>";
                                        
                                    }
                            
                                    ?>

                                </select>
                            </div>
                            <div class="form-group col-sm-2">
                                <label for="botonAñadirItem" class="hide-label" >Opciones</label>
                                <button id="botonAñadirItem" type="button" name="añadir" class="btn btn-success btn-block">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Contenedor Items -->
                    <div class="modal-items">
                        <div class="form-row justify-content-center mx-auto">
                            <h6 class="font-weight-bold">Materiales Utilizados</h6>
                            <div class="col-sm-10 text-center">
                                <div id="contenedorItemsAñadidos">
                                    <p id="avisoAñadir" class="m-0">No hay Materiales</p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" id='botonAñadirFormula' class="btn btn-sm btn-main">Añadir Formula</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Fin de Modal -->

    <!-- Modal de Editar Formula -->
    <div class="modal fade" id="editarFormulaModal" tabindex="-1" role="dialog" aria-labelledby="editarFormulaModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <!-- Form -->
                <form>
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fab fa-react icon-color"></i>
                            Editar Formula</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row justify-content-center">
                            <div class="form-group col-sm-6">
                                <label for="editarNombre">Formula</label>
                                <input name="nombre" type="text" class="form-control" id="editarNombre" placeholder="Formula" required>
                            </div>
                            <div class="form-group col-sm-4">
                                <label for="editarMaterial">Material</label>
                                <select id="editarMaterial" class="form-control" name="material">
                                    <option value="EXPANSO/PVC">EXPANSO/PVC</option>
                                    <option value="EXPANSO">EXPANSO</option>
                                    <option value="PVC">PVC</option>
                                    <option value="PU">PU</option>
                                </select>
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                            <div class="form-group col-sm-8">
                                <label for="editarItem">Item</label>
                                <select  id="editarItem" class="form-control dropdown-select2" name="item">
                                </select>
                            </div>
                            <div class="form-group col-sm-2">
                                <label class="hide-label" for="botonEditarItem">Opciones</label>
                                <button type="button" name="add" id="botonEditarItem" class="btn btn-success btn-block">
                                    <i class="fa fa-plus"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Contenedor Items -->
                    <div class="modal-items">
                        <div class="form-row justify-content-center mx-auto">
                            <h6 class="font-weight-bold">Materiales Usados</h6>
                            <div class="col-sm-10 text-center">
                                <div id="contenedorItemsEditados">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="button" id='botonEditarFormula' class="btn btn-sm btn-main">Editar Formula</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Fin de Modal -->

    <!-- Modal de Ver Formula -->
    <div class="modal fade" id="verFormulaModal" tabindex="-1" role="dialog" aria-labelledby="verFormulaModal"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i class="fab fa-react icon-color"></i> Ver
                            Formula</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="table-responsive-lg py-3">
                            <table class="table table-bordered text-center" id="tabla-modal">
                                <thead class="thead-dark">
                                <tr>
                                    <th class="align-middle" scope="col">Ingredientes</th>
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
    <!-- / Fin de Modal -->

    <!-- Boton -->
    <div class="row mt-5">
        <a href="javascript:void(0);" class="btn btn-sm btn-main mx-auto" data-toggle="modal" data-target="#añadirFormulaModal" role="button">Añadir Formula</a>
    </div>

</div>
<!-- / Fin de contenido -->

<script>
    
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

// Añadir Formula (Variables)
var añadirMateriales = [];
const añadirNombre = document.getElementById('añadirNombre');
const añadirMaterial = document.getElementById('añadirMaterial');
const añadirItem = document.getElementById('añadirItem');
const botonAñadirItem = document.getElementById('botonAñadirItem');
const contenedorItemsAñadidos = document.getElementById('contenedorItemsAñadidos');
const botonAñadirFormula = document.getElementById('botonAñadirFormula');

// Editar Formula (Variables)
var editarMateriales = [];
const editarNombre = document.getElementById('editarNombre');
const editarMaterial = document.getElementById('editarMaterial');
const editarItem = document.getElementById('editarItem');
const botonEditarItem = document.getElementById('botonEditarItem');
const contenedorItemsEditados = document.getElementById('contenedorItemsEditados');
const botonEditarFormula = document.getElementById('botonEditarFormula');

// 1. Espera a que carguen todos los elementos del DOM
document.addEventListener('DOMContentLoaded', function () {

    // 2. Añadir un Item a la Formula
    $('#botonAñadirItem').click(function() {

        if(Object.entries(añadirMateriales).length === 0){
            document.getElementById('avisoAñadir').style.display = "none";
        }

        const itemSeleccionadoValue = añadirItem.value;
        const itemSeleccionadoText = añadirItem.options[añadirItem.selectedIndex].text;

        const item = {
            ID: itemSeleccionadoValue,
            ITEM: itemSeleccionadoText
        };

        añadirMateriales.push(item);

        // Agregar Item a la Formula
        $('#contenedorItemsAñadidos').append(
        `<span id="add-${itemSeleccionadoValue}" class="badge-lg badge-main display-inline mr-1 mt-2">${itemSeleccionadoText}  
            <a href="javascript:void(0);" id="${itemSeleccionadoValue}" class="borrarItemAñadido">
                <i class="fas fa-times fa-sm text-white ml-1"></i>
            </a>
        </span>`);

        // Eliminamos la opcion del Select para que no pueda usarse luego.
        $('#añadirItem option:selected').remove();

        // Se vuelve a preguntar, si el SELECT está vacio, se agrega la opción de No hay Materia Prima.
        if (añadirItem.value == '') {
            // Agregamos la opcion al Select para que pueda usarse luego.
            $('#añadirItem').append(`<option value="null">No hay materiales</option>`);
            botonAñadirItem.disabled = true;
        }

        añadirItem.focus();
        
    }); 
    
    // 2.1 Eliminar Item de la Formula
    $(document).on('click', '.borrarItemAñadido' , function () {

        // Si hay un elemento en el Select que sea null, quitarlo.
        if (añadirItem.value == 'null') {
            $('#añadirItem option:selected').remove();
            botonAñadirItem.disabled = false;
        }

        const itemSeleccionadoValue = $(this).attr('id');
        const itemSeleccionadoText = $(this.parentElement).text();
        $('#add-' + itemSeleccionadoValue).remove();

        // Agregamos la opcion al Select para que pueda usarse luego.
        $('#añadirItem').append(`<option value="${itemSeleccionadoValue}">${itemSeleccionadoText}</option>`);

        // Eliminamos el Elemento del Array
        añadirMateriales.forEach((elem, index) => {

            if(elem.ID == itemSeleccionadoValue){
                
                añadirMateriales.splice(index, 1);

            }

        });

        if(Object.entries(añadirMateriales).length === 0){
            document.getElementById('avisoAñadir').style.display = "block";
        }

        añadirItem.focus();

    });

    // 2.2 Añadiendo la Formula a la Base de Datos.
    botonAñadirFormula.addEventListener('click', function () {

        // Comprobar que todos los campos estén llenos.
        if (Object.entries(añadirMateriales).length === 0 || añadirNombre.value == '') {
            return Swal.fire("Whoops", "Debes rellenar todos los campos.", "warning");
        }

        Swal.fire({
            title: '¿Estás Seguro?',
            text: 'Recuerda que la formula se puede guardar luego..',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
        }).then((result) => {
            if (result.value) {
                Swal.fire({
                    title: '¡Añadida!',
                    text: 'La fórmula ha sido añadida con éxito.',
                    icon: 'success'
                }).then(function () {
                    $.post("backend/api/formulas/agregar.php", {
                        datos: JSON.stringify({
                            'formula': añadirNombre.value,
                            'material': añadirMaterial.value
                        }),
                        materiales: JSON.stringify(añadirMateriales)
                    });
                    window.location = window.location.href;
                });
            }
        });
    
    });

    // 3. Editar Formula
    $('#editarFormulaModal').on('show.bs.modal', function (e) {

        editarMateriales = [];
        botonEditarItem.disabled = false;
        
        // Obtenemos el ID de la Formula
        let formula_id = $(e.relatedTarget).data('id');

        // Obtenemos el Nombre de la formula y el Material
        $.ajax({
            type: 'get',
            async: false,
            url: `backend/api/utils.php?fun=obtenerFormula&id=${formula_id}`,
            success: function (data) {

                const result = JSON.parse(data);
                editarNombre.value = result[0].NOMBRE.toProperCase();
                
                $("#editarMaterial > option").each(function() {

                    if( result[0].MATERIAL == this.value ){
                 
                        $(this).prop("selected", true);
                        
                        return false;
                      
                    }

                });

            }
        });

        // Obtenemos los materiales usados en la fórmula.
        $.ajax({
            type: 'get',
            async: false,
            url: `backend/api/utils.php?fun=obtenerRecetaFormula&id=${formula_id}`,
            success: function (data) {

                const result = JSON.parse(data);
                $('#contenedorItemsEditados').empty();
                $('#editarItem').empty();
                
                // Obtenemos la materia prima dinámicamente para poder agregarla cada vez que salga el modal y comprarlas con las formulas.
                $.ajax({
                    type: 'get',
                    async: false,
                    url: `backend/api/utils.php?fun=obtenerMateriaPrima`,
                    success: function (data) {

                        const result = JSON.parse(data);

                        result.forEach(row => {

                            $('#editarItem').append(
                                `<option value="${row.ID}">${row.DESCRIPCION.toProperCase()}</option>`
                            );

                        });

                    }
    
                });

                result.forEach(row => {

                    const items = {
                        ID: row.MATERIAL_ID,
                        ITEM: row.MATERIAL_DESCRIPCION
                    };

                    editarMateriales.push(items);

                    // Agregar Item a la Formula
                    $('#contenedorItemsEditados').append(
                    `<span id="edit-${row.MATERIAL_ID}" class="badge-lg badge-main display-inline mr-1 mt-2">${row.MATERIAL_DESCRIPCION.toProperCase()}  
                        <a href="javascript:void(0);" id="${row.MATERIAL_ID}" class="borrarItemEditado">
                            <i class="fas fa-times text-white"></i>
                        </a>
                    </span>`);

                    // Quitar los elementos del Select
                    $("#editarItem > option").each(function() {

                        if( row.MATERIAL_ID == this.value ){
                            
                            $(this).remove();
                        
                        }
                        
                    });
                    
                    if (editarItem.value == '') {
                        // Agregamos la opcion al Select para que pueda usarse luego.
                        $("#editarItem").append(`<option value="null">No hay materiales</option>`);
                        botonEditarItem.disabled = true;
                    }

                });

                // Creamos el aviso, pero lo escondemos.
                $('#contenedorItemsEditados').append(
                    `<p id="avisoEditar" class="m-0" style="display: none;">No hay Materiales</p>`);
                }
                
        });

        // 3.3 Editando la Formula en la Base de Datos.
        botonEditarFormula.addEventListener('click', function () {
            
            Swal.fire({
                title: 'Estás Seguro?',
                text: 'Recuerda que la formula se puede editar luego..',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
            }).then((result) => {
                if (result.value) {
                    Swal.fire({
                        title: '¡Editada!',
                        text: 'La fórmula ha sido editada con éxito.',
                        icon: 'success'
                    }).then(function () {
                        $.post("backend/api/formulas/editar.php", {
                            datos: JSON.stringify({
                                'formula_id': formula_id,
                                'formula': editarNombre.value,
                                'material': editarMaterial.value
                            }),
                            materiales: JSON.stringify(editarMateriales)
                        });
                        window.location = window.location.href;
                    });
                }
            });

        });


    });

    // 3.1 Añadir un Item a la Formula
    $('#botonEditarItem').click(function() {

        if(Object.entries(editarMateriales).length === 0){
            document.getElementById('avisoEditar').style.display = "none";
        }

        const itemSeleccionadoValue = editarItem.value;
        const itemSeleccionadoText = editarItem.options[editarItem.selectedIndex].text;

        const item = {
            ID: itemSeleccionadoValue,
            ITEM: itemSeleccionadoText
        };

        editarMateriales.push(item);

        // Agregar Item a la Formula
        $('#contenedorItemsEditados').append(
        `<span id="edit-${itemSeleccionadoValue}" class="badge-lg badge-main display-inline mr-1 mt-2">${itemSeleccionadoText}  
            <a href="javascript:void(0);" id="${itemSeleccionadoValue}" class="borrarItemEditado">
                <i class="fas fa-times text-white"></i>
            </a>
        </span>`);

        // Eliminamos la opcion del Select para que no pueda usarse luego.
        $('#editarItem option:selected').remove();

        // Se vuelve a preguntar, si el SELECT está vacio, se agrega la opción de No hay Materia Prima.
        if (editarItem.value == '') {
            // Agregamos la opcion al Select para que pueda usarse luego.
            $('#editarItem').append(`<option value="null">No hay materiales</option>`);
            botonEditarItem.disabled = true;
        }

        editarItem.focus();

    }); 
    
    // 3.2 Eliminar Item de la Formula
    $(document).on('click', '.borrarItemEditado' , function () {

        editarItem.focus();
        
        // Si hay un elemento en el Select que sea null, quitarlo.
        if (editarItem.value == 'null') {
            $('#editarItem option:selected').remove();
            botonEditarItem.disabled = false;
        }

        let itemSeleccionadoValue = $(this).attr('id');
        let itemSeleccionadoText = $(this.parentElement).text();
        $('#edit-' + itemSeleccionadoValue).remove();

        // Agregamos la opcion al Select para que pueda usarse luego.
        $('#editarItem').append(`<option value="${itemSeleccionadoValue}">${itemSeleccionadoText}</option>`);

        // Eliminamos el Elemento del Array
        editarMateriales.forEach((elem, index) => {

            if(elem.ID == itemSeleccionadoValue){
                
                editarMateriales.splice(index, 1);

            }

        });

        if(Object.entries(editarMateriales).length === 0){
            document.getElementById('avisoEditar').style.display = "block";
        }

    });

    // 4. Ver Formula
    $('#verFormulaModal').on('show.bs.modal', function (e) {
        
        let formula_id = $(e.relatedTarget).data('id');

        $.ajax({
            url: `backend/api/utils.php?fun=obtenerRecetaFormula&id=${formula_id}`,
            success: function (data) {

                const result = JSON.parse(data);
                const tabla = $('#tabla-modal > tbody:last-child');
                tabla.empty();

                result.forEach(row => {
                    tabla.append(`<tr>
                                    <td>${row.MATERIAL_DESCRIPCION.toProperCase()}</td>
                                </tr>`);
                });
            }
        });
    });

    // 5. Eliminar Formula
    $('.eliminarFormula').on('click', function (e) {

        let formula_id = $(e.target.parentElement).data('id');
        
        Swal.fire({
            title: '¿Estás seguro',
            text: 'Si eliminas la fórmula tendrás que crearla de nuevo..',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
        }).then((result) => {
            if (result.value) {
                Swal.fire({
                    title: '¡Eliminada!',
                    text: 'La fórmula ha sido eliminada.',
                    icon: 'success'
                }).then(function() {
                    
                    window.location = `backend/api/formulas/borrar.php?formula-id=${formula_id}`
                    
                });
            }
        });
        
    });

});

// Aprobar Formula
function aprobarFormula(formula_id) {

    Swal.fire({
        title: 'Estás seguro?',
        text: 'Si cambias el estado de la formula no podrás editarla..',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {
        if (result.value) {
            Swal.fire({
                title: '¡Pendiente!',
                text: 'El pedido ya pasó a producción.',
                icon: 'success'
            }).then(function () {
                window.location = `backend/api/formulas/aprobar.php?id=${formula_id}`
            });
        }
    });

};
    
</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>