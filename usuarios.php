<?php

// Incluimos el header.php y components.php
$title = 'Usuarios';
include 'components/header.php';
include 'components/components.php';
require_once 'backend/api/utils.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR');

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
    <?php get_navbar('Panel de Control', 'Usuarios'); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
    <div class="table-responsive-lg">
        <table class="table table-bordered text-center" id="tabla">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Nombre</th>
                <th scope="col">Cargo</th>
                <th scope="col">ID</th>
                <th scope="col">Número de Teléfono</th>
                <th scope="col">Correo</th>
                <th scope="col">Opciones</th>
            </tr>
            </thead>
            <tbody>
            <!-- Obtenemos la data mediante PHP -->
            <?php
                require_once 'backend/api/db.php';
                $sql = "SELECT * FROM USUARIOS WHERE ACTIVO = 'SI';";
                $result = db_query($sql);

                foreach ($result as $row) {
                    echo "<tr>";

                    echo "<th scope='col'>{$row['ID']}</th>";
                    echo "<td>" . mb_convert_case($row['NOMBRE'], MB_CASE_TITLE, "UTF-8") . "</td>";
                    echo "<td>" . mb_convert_case($row['CARGO'], MB_CASE_TITLE, "UTF-8") . "</td>";
                    echo "<td>" . $row['CEDULA'] . "</td>";
                    echo "<td>" . $row['TELEFONO'] . "</td>";
                    echo "<td>" . mb_strtolower($row['CORREO'], 'UTF-8') . "</td>";
                    echo "<td>
                            <a href='#' data-toggle='modal' data-target='#editarUsuario-modal' data-id='{$row['ID']}'><i class='fas fa-edit icon-color'></i></a>
                        </td>";

                    echo "</tr>";
                }
            ?>
            </tbody>
        </table>
    </div>
    <!-- / Fin de la tabla -->

    <!-- Añadimos el botón de Añadir Usuario -->
    <div class="d-flex justify-content-center mt-5">
        <a class="btn btn-sm btn-main" href="#" data-toggle="modal" data-target="#añadirUsuario-modal" role="button">Añadir Usuario</a>
    </div>

    <!-- Modal de Añadir Usuarios -->
    <div class="modal fade" id="añadirUsuario-modal" tabindex="-1" role="dialog" aria-labelledby="añadirUsuario-modal"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <form action="backend/api/usuarios/login.php?action=REGISTER" method="POST">

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-user-plus icon-color"></i></i> Añadir Usuario</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <!-- Form Row #1 -->
                        <div class="form-row">

                            <div class="form-group col-sm-6">
                                <label for="inputAñadirCargo-modal">Cargo</label>
                                <select id="inputAñadirCargo-modal" class="form-control dropdown-select2" name="cargo">
                                    <option value="ADMINISTRADOR">Administrador</option>
                                    <option value="VENTAS">Ventas</option>
                                    <option value="MOLINERO">Molinero</option>
                                    <option value="OPERARIO">Operario</option>
                                    <option value="PRODUCCION">Producción</option>
                                    <option value="NORSAPLAST">Norsaplast</option>
                                    <option value="DESPACHO">Despacho</option>
                                    <option value="CLIENTE">Cliente</option>
                                    <option value="CONTROL">Control de Calidad</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputAñadirNombre-modal">Nombre y Apellido</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-signature"></i></div>
                                    </div>
                                    <input id="inputAñadirNombre-modal" class="form-control" name="nombre" type="text" placeholder="Nombre y Apellido" pattern="[A-Za-zÀ-ž\s]+" title="El nombre solo puede contener letras." required>
                                </div>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputAñadirCedula-modal">ID</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-id-card"></i></div>
                                    </div>
                                    <input id="inputAñadirCedula-modal" class="form-control" type="number" min="1" name="cedula" placeholder="ID" required>
                                </div>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputAñadirTelefono-modal">Teléfono</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-phone"></i></div>
                                    </div>
                                    <input id="inputAñadirTelefono-modal" class="form-control" name="telefono" type="number" min="1" placeholder="Teléfono" required>
                                </div>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputAñadirCorreo-modal">Email</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-envelope"></i></div>
                                    </div>
                                    <input id="inputAñadirCorreo-modal" class="form-control" name="correo" type="email" placeholder="Email" required>
                                </div>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputAñadirContraseña-modal">Contraseña</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-lock"></i></div>
                                    </div>
                                    <input id="inputAñadirContraseña-modal" class="form-control" name="contrasena" type="password" placeholder="Contraseña" required>
                                </div>
                            </div>

                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-main">Añadir Usuario</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <!-- / Fin de Modal -->

    <!-- Modal de Editar Usuarios -->
    <div class="modal fade" id="editarUsuario-modal" tabindex="-1" role="dialog" aria-labelledby="editarUsuario-modal"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">

                <!-- Form -->
                <form action="backend/api/usuarios/editar.php" method="POST">

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-user-edit icon-color"></i> Editar Usuario</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-row">

                            <input type="hidden" name="id" id="inputEditarId-modal">

                            <div class="form-group col-sm-6">
                                <label for="inputEditarCargo-modal">Cargo</label>
                                <select id="inputEditarCargo-modal" class="form-control dropdown-select2" name="cargo">
                                    <option value="ADMINISTRADOR">Administrador</option>
                                    <option value="VENTAS">Ventas</option>
                                    <option value="MOLINERO">Molinero</option>
                                    <option value="OPERARIO">Operario</option>
                                    <option value="PRODUCCION">Producción</option>
                                    <option value="NORSAPLAST">Norsaplast</option>
                                    <option value="DESPACHO">Despacho</option>
                                    <option value="CLIENTE">Cliente</option>
                                    <option value="CONTROL">Control de Calidad</option>
                                </select>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputEditarNombre-modal">Nombre y Apellido</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-signature"></i></div>
                                    </div>
                                    <input id="inputEditarNombre-modal" class="form-control" name="nombre" type="text" placeholder="Nombre" placeholder="Nombre y Apellido" pattern="[A-Za-zÀ-ž\s]+" title="El nombre solo puede contener letras." required>
                                </div>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputEditarCedula-modal">ID</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-id-card"></i></div>
                                    </div>
                                    <input id="inputEditarCedula-modal" class="form-control" type="number" min="1" name="cedula" placeholder="ID" required>
                                </div>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputEditarTelefono-modal">Teléfono</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-phone"></i></div>
                                    </div>
                                    <input id="inputEditarTelefono-modal" class="form-control" name="telefono" type="number" min="1" placeholder="Teléfono" required>
                                </div>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputEditarCorreo-modal">Email</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-envelope"></i></div>
                                    </div>
                                    <input id="inputEditarCorreo-modal" class="form-control" name="correo" type="email" placeholder="Email" required>
                                </div>
                            </div>

                            <div class="form-group col-sm-6">
                                <label for="inputEditarContraseña-modal">Contraseña</label>
                                <div class="input-group mb-2">
                                    <div class="input-group-prepend">
                                        <div class="input-group-text"><i class="fas fa-lock"></i></div>
                                    </div>
                                    <input id="inputEditarContraseña-modal" class="form-control" name="contrasena" type="password" placeholder="Contraseña" minlength="6" readonly required>
                                </div>
                            </div>

                        </div>

                        <div class="form-row justify-content-center">
                            <button class="btn btn-sm btn-outline-dark readonlyToggler">Desbloquear</button>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-main">Editar Usuario</button>
                    </div>

                </form>

            </div>
        </div>
    </div>
    <!-- / Fin de Modal -->

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

// Custom Search DataTables
$('#customInput').on( 'keyup', function () {
	tabla.search( this.value ).draw();
});

// Variables Inicializadas para Editar.
const inputEditarId = document.getElementById("inputEditarId-modal");
const inputEditarCedula = document.getElementById("inputEditarCedula-modal");
const inputEditarNombre = document.getElementById('inputEditarNombre-modal');
const inputEditarCorreo = document.getElementById('inputEditarCorreo-modal');
const inputEditarTelefono = document.getElementById('inputEditarTelefono-modal');
const inputEditarContraseña = document.getElementById('inputEditarContraseña-modal');

// Editar Usuarios.
$('#editarUsuario-modal').on('show.bs.modal', function (e) {

    let id = $(e.relatedTarget).data('id');

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerUsuarioId',
        data: 'id=' + id,
        success: function (data) {

            const result = JSON.parse(data);

            console.log(result);

            $("#inputEditarCargo-modal > option").each(function() {

                if( result[0].CARGO == this.value ){

                    $(this).prop("selected", true);
                    
                    return false;

                }

            });

            inputEditarId.value = result[0].ID;
            inputEditarCedula.value = result[0].CEDULA;
            inputEditarNombre.value = result[0].NOMBRE;
            inputEditarTelefono.value = result[0].TELEFONO;
            inputEditarCorreo.value = result[0].CORREO;

        }
    });
});

// Activación y desactivación del botón para editar la contraseña.
$('.readonlyToggler').click(function () {
    
    event.preventDefault();

    var $this = $(this);

    $this.toggleClass('readonlyToggler');

    if ($this.hasClass('readonlyToggler')) {
        inputEditarContraseña.readOnly = true;
        $this.text('Desbloquear');
    } else {
        inputEditarContraseña.readOnly = false;
        $this.text('Bloquear');
    }

});
</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>