<?php

// Incluimos el header.php y components.php
$title = 'Referencias';
require_once 'components/header.php';
require_once 'components/navbar.php';
require_once 'backend/api/utils.php';

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
    <?php get_navbar('Inventario', 'Referencias', true); ?>

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

    <!-- Boton -->
    <div class="row mt-5">
        <button class="btn btn-sm btn-main mx-auto" data-toggle="modal" data-target="#añadirReferenciaModal">Añadir Referencias</button>
    </div>
    <!-- Fin de Botón -->

    <!-- Modal de Añadir Referencia -->
    <div class="modal fade" id="añadirReferenciaModal" tabindex="-1" role="dialog"
            aria-labelledby="añadirReferenciaModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">

            <div class="modal-content">

                <form id="añadirReferenciaForm">

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fab fa-slack-hash icon-color"></i> Añadir Referencias
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-row">

                            <div class="form-group col-sm-4">
                                <label for="añadirReferencia">Referencia</label>
                                <input id="añadirReferencia" type="text" class="form-control" placeholder="Referencia" name="referencia" required>
                            </div>

                            <div class="form-group col-sm-8">
                                <label for="añadirMarca">Marca</label>
                                <input id="añadirMarca" type="text" class="form-control" placeholder="Nombre" name="marca" required>
                            </div>

                            <div class="form-group col-sm-4">
                                <label for="añadirMaterial">Material</label>
                                <select id="añadirMaterial" class="form-control dropdown-select2" name="material" required>
                                    <option value="EXPANSO" selected>EXPANSO</option>
                                    <option value="EXPANSO/PVC">EXPANSO/PVC</option>
                                    <option value="PVC">PVC</option>
                                    <option value="PU">PU</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-4">
                                <label for="añadirPesoMaquina">Peso Máquina</label>
                                <input id="añadirPesoMaquina" type="number" min="1" class="form-control" placeholder="Peso" name="peso_maquina" required>
                            </div>

                            <div class="form-group col-sm-4">
                                <label for="añadirPesoIdeal">Peso Ideal</label>
                                <input id="añadirPesoIdeal" type="number" min="1" class="form-control" placeholder="Peso" name="peso_ideal" required>
                            </div>

                            <div class="form-group col-sm-12">
                                <label for="añadirCapEmpaquetado">Cap. Empaquetado</label>
                                <input id="añadirCapEmpaquetado" type="number" min="1" class="form-control" placeholder="Cap. Empaquetado" name="capacidad_empaquetado" required>
                            </div>

                            <div class="form-group col-sm-12">

                                <label for="añadirTalla">Tallas</label>
                                <select id="añadirTalla" class="form-control select2-multiple" name="talla[]" multiple="multiple" required>
                                    <?php
                                        $sql = "SELECT * FROM TALLAS;";
                                        $tallas = db_query($sql);
                                        foreach ($tallas as $talla) {
                                            echo "<option value='{$talla['TALLA']}'>{$talla['TALLA']}</option>";
                                        }
                                        if(empty($tallas)){
                                            echo "<option value=''>No hay tallas disponibles.</option>";
                                        }
                                    ?>
                                </select>

                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar
                        </button>
                        <button type="button" class="btn btn-sm btn-main" id="botonAñadirReferencia">
                            Añadir referencias
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>
    <!-- Fin de Modal de Añadir Referencia -->

    <!-- Modal de Editar Referencia -->
    <div class="modal fade" id="editarReferenciaModal" tabindex="-1" role="dialog"
            aria-labelledby="editarReferenciaModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">

            <div class="modal-content">

                <form id="editarReferenciaForm">

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fab fa-slack-hash icon-color"></i> Editar Referencia
                        </h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-row">

                            <!-- ID escondido para el POST -->
                            <input type="hidden" name="id" id="editarId">

                            <div class="form-group col-sm-4">
                                <label for="editarReferencia">Referencia</label>
                                <input id="editarReferencia" type="text" class="form-control"
                                        placeholder="Referencia" name="referencia" required>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="editarMarca">Marca</label>
                                <input id="editarMarca" type="text" class="form-control"
                                        placeholder="Nombre" name="marca" required>
                            </div>

                            <div class="form-group col-sm-2">
                                <label for="editarTalla">Talla</label>
                                <select id="editarTalla" class="form-control dropdown-select2" name="talla" required>
                                    <?php
                                        foreach ($tallas as $talla) {
                                            echo "<option value='{$talla['TALLA']}'>{$talla['TALLA']}</option>";
                                        }
                                        if(empty($tallas)){
                                            echo "<option value=''>No hay tallas disponibles.</option>";
                                        }
                                    ?>
                                </select>
                            </div>

                            <div class="form-group col-sm-4">
                                <label for="editarMaterial">Material</label>
                                <select id="editarMaterial" class="form-control dropdown-select2" name="material" required>
                                    <option value="EXPANSO" selected>EXPANSO</option>
                                    <option value="EXPANSO/PVC">EXPANSO/PVC</option>
                                    <option value="PVC">PVC</option>
                                    <option value="PU">PU</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-4">
                                <label for="editarPesoMaquina">Peso Máquina</label>
                                <input id="editarPesoMaquina" type="number" min="1"
                                        class="form-control" placeholder="Peso" name="peso_maquina" required>
                            </div>

                            <div class="form-group col-sm-4">
                                <label for="editarPesoIdeal">Peso Ideal</label>
                                <input id="editarPesoIdeal" type="number" min=1"
                                        class="form-control" placeholder="Peso" name="peso_ideal" required>
                            </div>

                            <div class="form-group col-sm-4">
                                <label for="editarCapacidadEmpaquetado">Cap. Empaquetado</label>
                                <input id="editarCapacidadEmpaquetado" type="number" min="1" class="form-control" placeholder="Cap. Empaquetado" name="capacidad_empaquetado" required>
                            </div>
                            
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar
                        </button>
                        <button type="button" class="btn btn-sm btn-main" id="botonEditarReferencia">
                            Editar Referencia
                        </button>
                    </div>

                </form>

            </div>

        </div>
    </div>

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

</div>
<!-- / Fin de Contenido -->

<!-- Inline JavaScript -->
<script>

// Variables Inicializadas.
var tabla;
var posicionTabla;
const botonAñadirReferencia = document.getElementById('botonAñadirReferencia');
const botonEditarReferencia = document.getElementById('botonEditarReferencia');

// Datatables => Mostrando la tabla REFERENCIAS
$.ajax({
    type: 'get',
    url: 'backend/api/utils.php?fun=obtenerSuelas',
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
                { data: "ID", title: "ID" },
                { data: "REFERENCIA", title: "Referencia", render: function(value, type, row) {
                    return value.toUpperCase();
                }},
                { data: "MARCA", title: "Marca" },
                { data: "TALLA", title: "Talla" },
                { data: "MATERIAL", title: "Material" },
                { data: "PESO_MAQUINA", title: "Peso Máquina" },
                { data: "PESO_IDEAL", title: "Peso Ideal" },
                { data: "CAP_EMPAQUETADO", title: "Cap. Empaque" },
                { 
                    data: 'ID',
                    title: "Opciones", render: function(value, type, row) {
                        return `<a href='javascript:void(0)' class='editarReferencia' data-toggle='modal' data-target='#editarReferenciaModal' data-id='${value}'>
                            <i class='fas fa-edit icon-color'></i>
                        </a>
                        <a href='javascript:void(0)' class='ml-1 eliminarReferencia' data-id='${value}'>
                            <i class='fas fa-trash icon-color'></i>
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

        // Datatables => Paginación
        $.fn.DataTable.ext.pager.numbers_length = 5;
        
    }

});

// DATATABLES => Detectar Fila Actual (Aplica para Eliminar y Editar)
$('#tabla tbody').on( 'click', 'tr', function () { 
	posicionTabla = this;
});


// AÑADIR => Añadiendo Referencia.
botonAñadirReferencia.addEventListener('click', function () {

	// ID del formulario.
	let formulario = $('#añadirReferenciaForm');

	// Si el formulario tiene algún campo incorrecto, lanzar error.
	if(!formulario[0].checkValidity()) return Swal.fire('Error', 'Por favor verifica todos los campos.', 'error');

	// Si todos los campos son correctos, Bloquear el botón de envío de data.
	botonAñadirReferencia.disabled = true;

    // $.post => Añadiendo el elemento al backend.
    $.post( 'backend/api/referencias/añadir.php', formulario.serialize(), function(data) {

	    switch (data) {
			
			case 'ERROR':
                botonAñadirReferencia.disabled = false;
				return Swal.fire('Error', 'La referencia ya se encuentra registrada, prueba con otras tallas y/o referencias.', 'error');
				break;

			default:

                let ids = JSON.parse(data);

				$('#añadirReferenciaModal').modal('hide')

				mostrarNotificacion('agregar', '¡Agregadas!', 'Las referencias han sido agregadas satisfactoriamente.');

                const elems = formulario.serializeArray();

                var i = 0;

                elems.forEach((elem, index) => {

                    if(elem.name === 'talla[]'){

                        // Datatables => Añadiendo el elemento al frontend.
                        tabla.row.add({
                            "ID":               ids[i],
                            "REFERENCIA":       elems[0].value,
                            "MARCA":            elems[1].value,
                            "TALLA":            elem.value,
                            "MATERIAL":         elems[2].value,
                            "PESO_MAQUINA":     elems[3].value,
                            "PESO_IDEAL":       elems[4].value,
                            "CAP_EMPAQUETADO":  elems[5].value,
                            "ID":               ids[i]
                        });

                        i++;
                        
                    }

                });	
                
                tabla.draw();

                // Borrando los inputs del Modal.
				$('#añadirReferenciaModal').on('hidden.bs.modal', function (e) {
                    $(this).find("input, textarea").val('').end();
                });

        }

    }).always(

        // Luego de agregar el elemento tanto en frontend como backend, habilitar el botón.
        $('#añadirReferenciaModal').on('hidden.bs.modal', function (e) {
            botonAñadirReferencia.disabled = false;
        })

    ); 

});

// EDITAR => Editando Referencia.
botonEditarReferencia.addEventListener('click', function () {

	// ID del formulario.
	let formulario = $('#editarReferenciaForm');

	// Si el formulario tiene algún campo incorrecto, lanzar error.
	if(!formulario[0].checkValidity()) return Swal.fire('Error', 'Por favor verifica todos los campos.', 'error');

	// Si todos los campos son correctos, Bloquear el botón de envío de data.
	botonEditarReferencia.disabled = true;

    // $.post => Añadiendo el elemento al backend.
    $.post( 'backend/api/referencias/editar.php', formulario.serialize(), function(data) {
        
        switch (data) {

            case 'ERROR':
                botonEditarReferencia.disabled = false;
                return Swal.fire('Error', 'La referencia ya se encuentra registrada.', 'error');
                break;

            default:

                $('#editarReferenciaModal').modal('hide')

                mostrarNotificacion('editar', '¡Editado!', 'La referencia ha sido editada satisfactoriamente.');

                const elems = formulario.serializeArray();

                // Datatables => Añadiendo el elemento al frontend.
                tabla.row(posicionTabla).data({
                    "ID":                   elems[0].value,
                    "REFERENCIA":           elems[1].value,
                    "MARCA":                elems[2].value,
                    "TALLA":                elems[3].value,
                    "MATERIAL":             elems[4].value,
                    "PESO_MAQUINA":         elems[5].value,
                    "PESO_IDEAL":           elems[6].value,
                    "CAP_EMPAQUETADO":      elems[7].value,
                    "ID":                   elems[0].value
                }).draw(false);

        }

    }).always(

        // Luego de agregar el elemento tanto en frontend como backend, habilitar el botón.
        $('#editarReferenciaModal').on('hidden.bs.modal', function (e) {
            botonEditarReferencia.disabled = false;
        })

    ); 

});

// ELIMINAR => Eliminando Referencia.
$('#tabla tbody').on( 'click', '.eliminarReferencia', function () {

    let id = $(this).data("id");

    Swal.fire({
        title: '¿Estás seguro?',
        text: "Se eliminará de las siguientes tablas: Casillero, Stock, Series y Producción.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {
        if (result.value) {

            // Quitando el elemento del backend.
            $.get(`backend/api/referencias/eliminar.php?id=${id}`);

            // Datatable => Quitando el elemento del frontend.
            tabla.row( $(this).parents('tr') ).remove().draw(false);

            // Mostrando Notificación de éxito.
            mostrarNotificacion('eliminar', '¡Eliminado!', '¡Referencia eliminada!', 'La referencia ha sido eliminada satisfactoriamente.');

        }
    });

});

// VISTA => Agregar datos importantes a la data escondida.
$('#editarReferenciaModal').on('show.bs.modal', function (e) {

    let id = $(e.relatedTarget).data('id');

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerSuelaId',
        data: 'id=' + id,
        success: function (data) {

            const result = JSON.parse(data);

            $("#editarMaterial > option").each(function() {

                if( result[0].MATERIAL == this.value ){

                    $(this).prop("selected", true).trigger("change");
                    
                    return false;
                
                }

            });

            
            $("#editarTalla > option").each(function() {

                if( result[0].TALLA == this.value ){

                    $(this).prop("selected", true).trigger("change");
                    
                    return false;
                
                }

            });


            document.getElementById('editarId').value = result[0].ID;
            document.getElementById('editarReferencia').value = result[0].REFERENCIA;
            document.getElementById('editarMarca').value = result[0].MARCA;
            document.getElementById('editarTalla').value = result[0].TALLA;
            document.getElementById('editarPesoMaquina').value = result[0].PESO_MAQUINA;
            document.getElementById('editarPesoIdeal').value = result[0].PESO_IDEAL;
            document.getElementById('editarCapacidadEmpaquetado').value = result[0].CAP_EMPAQUETADO;
            
        }
    });

});

// Select2 => Dependencia de este archivo
$(document).ready(function () {
	$('.select2-multiple').select2({
		// dropdownParent: $('#example'),
		language: {
			"noResults": function(){
				return "No se encuentran resultados";
			}
		},
		closeOnSelect: false,
		allowClear: true,
		placeholder: 'Seleccione una talla.'
	});
});

</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>