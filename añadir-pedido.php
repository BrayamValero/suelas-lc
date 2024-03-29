<?php

// Incluimos el header.php y components.php
$title = 'Referencias';
require_once 'components/header.php';
require_once 'components/navbar.php';
require_once 'backend/api/utils.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS', 'CLIENTE');

if (!in_array($_SESSION['ROL'], $roles_permitidos)) {
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
    <?php get_navbar('Pedidos', 'Añadir Pedido', false); ?>

    <!-- Form -->
    <form id="añadirPedidoForm">

        <!-- Tabla de Datos -->
        <div class="tablaDatos shadow-sm">

            <!-- Datos del Cliente -->
            <h6 class="pb-3 font-weight-bold">
                <i class="fas fa-user-tie icon-color mr-2"></i> Datos del Cliente
            </h6>

            <div class="form-row pb-4">

                <div class="form-group col-lg-6 col-md-6">
                    <label for="añadirNombre">Nombre</label>
                    <select id="añadirNombre" name="nombre" class="form-control dropdown-select2" required>
                    <?php
                    
                        list('ROL' => $user_role, 'NOMBRE' => $user_name, 'CORREO' => $user_email) = $_SESSION;
                
                        if ($user_role === 'CLIENTE') {
                            $sql = "SELECT * FROM CLIENTES WHERE ACTIVO = 'SI' AND NOMBRE = ? AND CORREO = ?;";
                            $result = db_query($sql, array($user_name, $user_email));
                        } else {
                            $sql = "SELECT * FROM CLIENTES WHERE ACTIVO = 'SI';";
                            $result = db_query($sql);
                        }

                        if (empty($result)){
                            echo "<option value=''>No hay clientes disponibles.</option>";           
                        }
   
                        foreach ($result as $client) {

                            list(
                                'ID' => $client_id,
                                'NOMBRE' => $client_name,
                                'CORREO' => $client_email,
                                'DOCUMENTO' => $client_doctype,
                                'DOCUMENTO_NRO' => $client_docnum,
                            ) = $client;

                            echo "<option value='{$client_id}'>" . mb_convert_case($client_name, MB_CASE_TITLE, "UTF-8") . " - {$client_doctype} - {$client_docnum}</option>";
                            
                        }
                    
                    ?>
                    </select>
                </div>


                <div class="form-group col-lg-3 col-md-3">
                    <label for="añadirFecha">Fecha Estimada</label>
                    <input id="añadirFecha" name="fecha" type="date" class="form-control" placeholder="Elige la fecha" required>
                </div>

                <div class="form-group col-lg-3 col-md-3">
                    <label for="añadirPago">Forma de Pago</label>
                    <select id="añadirPago" class="form-control dropdown-select2" name="pago" required>
                        <?php
                        foreach (FORMAS_PAGO as $forma_pago) {
                            echo "<option value='$forma_pago'>" . mb_convert_case($forma_pago, MB_CASE_TITLE) . "</option>";
                        }
                        ?>
                    </select>
                </div>

            </div>
            <!-- Fin de Datos del Cliente -->

            <!-- Datos del Pedido -->
            <h6 class="pb-3 font-weight-bold">
                <i class="fas fa-shopping-bag icon-color mr-2"></i> Datos del Pedido
            </h6>

            <div class="form-row">

                <div class="form-group col-lg-6 col-md-6">
                    <label for="añadirSerie">Serie</label>
                    <select id="añadirSerie" class="form-control dropdown-select2" required>
                        <?php
                        $sql = "SELECT * FROM SERIES;";
                        $result = db_query($sql);
                        foreach ($result as $row) {
                            echo "<option value='{$row['ID']}'>" . mb_convert_case($row['NOMBRE'], MB_CASE_TITLE, "UTF-8") . "</option>";
                        }
                        if (empty($result)) {
                            echo "<option value=''>No hay series disponibles.</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group col-lg-3 col-md-3">
                    <label for="añadirColor">Color</label>
                    <select id="añadirColor" class="form-control dropdown-select2" required>
                        <?php
                        $sql = "SELECT * FROM COLOR;";
                        $result = db_query($sql);
                        foreach ($result as $row) {
                            echo "<option value='{$row['ID']}'>" . mb_convert_case($row['COLOR'], MB_CASE_TITLE, "UTF-8") . "</option>";
                        }
                        if (empty($result)) {
                            echo "<option value=''>No hay colores disponibles.</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group col-lg-3 col-md-3">
                    <label for="botonAñadirSerie" class="hide-options">Opciones</label>
                    <button id="botonAñadirSerie" type="button" class="btn btn-main btn-block">Añadir Serie</button>
                </div>

            </div>
            <!-- Fin de Datos del Pedido -->

        </div>
        <!-- Fin de Tabla de Datos -->

        <!-- Tabla de Pedidos -->
        <div class="tablaPedidos shadow-sm mt-4">

            <h6 class="font-weight-bold mb-3" id="inicioPedidos">
                <i class="fas fa-shopping-bag icon-color mr-2"></i> Pedido
            </h6>

            <button id="botonAñadirPedido" type="button" class="btn btn-main btn-block mt-3">Finalizar Pedido</button>

        </div>
        <!-- Fin de Tabla de Pedidos -->

    </form>
    <!-- Fin del Form -->

</div>
<!-- / Fin de Contenido -->

<!-- Inline JavaScript -->
<script>
    // Declaración de Variables y Constantes
    var i = 1;
    var j = 1;
    var verificadorSerie = [];
    var obtenerColor, obtenerSerie;
    const userRole = "<?php echo ($_SESSION['ROL']) ?>";

    const añadirFecha = document.getElementById('añadirFecha');
    const añadirSerie = document.getElementById('añadirSerie');
    const añadirColor = document.getElementById('añadirColor');
    const botonAñadirSerie = document.getElementById('botonAñadirSerie');

    // Asignando la fecha al input fecha.
    añadirFecha.min = new Date().toDateInputValue();
    añadirFecha.value = new Date().toDateInputValue();

    // Botón de añadir Serie al Pedido
    botonAñadirSerie.addEventListener('click', function() {

        let serieId = añadirSerie.value;
        let colorId = añadirColor.value;

        let agregarSerie = {
            "SERIE_ID": serieId,
            "COLOR_ID": colorId
        };

        // Se verifica que haya un elemento en el array de series, luego se compara la selección con el array en cuestión para cerciorarse que no hayan repetidos.
        if (verificadorSerie.length !== 0) {
            let verif = verificadorSerie.some(serie => serie['SERIE_ID'] === serieId && serie['COLOR_ID'] === colorId);
            if (verif) return Swal.fire("Whoops", "No puedes asignar la misma serie con el mismo color.", "warning");
            verificadorSerie.push(agregarSerie);
        } else {
            verificadorSerie.push(agregarSerie);
        }


        const submitResults = async () => {

            try {

                // 1. Obteniendo el color seleccionado
                const {
                    'data': color
                } = await axios.post('backend/api/utils.php?fun=obtenerColor', `id=${colorId}`);

                // 2. Obteniendo la serie_id seleccionada
                const {
                    'data': serie
                } = await axios.get('backend/api/utils.php?fun=obtenerGrupoSerie', {
                    params: {
                        'id': serieId
                    }
                });

                // 3. Object Destructuring (color & suela)
                const {
                    'ID': color_id,
                    'COLOR': color_name,
                    'CODIGO': color_code
                } = color[0];
                const {
                    'SUELA_ID': sole_id,
                    'MARCA': sole_brand
                } = serie[0];

                // 4. Realizando formulas para la personalización de colores.
                const red = parseInt(color_code.substring(1, 3), 16);
                const green = parseInt(color_code.substring(3, 5), 16);
                const blue = parseInt(color_code.substring(5, 7), 16);
                const color_hex = red * 0.299 + green * 0.587 + blue * 0.114 > 186 ? '#000000' : '#FFFFFF';

                // 4. Incrustando el HTML
                document.getElementById('inicioPedidos').insertAdjacentHTML('afterend',
                    `<div id="serie-${i}" class="contenedor-serie shadow-sm" data-serie-id="${serieId}" data-color-id="${colorId}">
                <div class="form-row">
                    <div class="col-8">
                        <strong>${sole_brand.toProperCase()}</strong>
                        <span class="badge border" style="background-color: ${color_code}; color: ${color_hex};">${color_name.toProperCase()}</span>
                        <small class="text-muted">${serie[0]['TALLA']} al ${serie[serie.length - 1].TALLA}</small>
                    </div>
                    <div class="col-4">
                        <button type="button" class="close eliminarSerie" data-id="${i}" data-serie-id="${serieId}" data-color-id="${colorId}" tabIndex="-1">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <button type="button" class="close esconderSerie" data-id="${i}" tabIndex="-1">
                            <span aria-hidden="true" class="mr-2">&minus;</span>
                        </button>
                    </div>
                </div>
                <div id="grupoSeries-${i}" class="form-row text-center"></div>
            </div>`);

                serie.forEach(elem => {

                    document.getElementById('grupoSeries-' + i).innerHTML +=
                        `<div class="form-group col mb-0 mt-2">
                    <label class="label-cantidades" for="cantidades">${elem['TALLA']}</label>
                    <input class="form-control input-cantidades" type="number" name="pedido[${j}][cantidad]" min="0" required>
                    <input type="hidden" name="pedido[${j}][suela_id]" value="${elem['SUELA_ID']}">
                    <input type="hidden" name="pedido[${j}][serie_id]" value="${serieId}">
                    <input type="hidden" name="pedido[${j}][color_id]" value="${colorId}">
                </div>`;

                    j++;

                });

                i++;

            } catch (err) {
                // Handle Error Here
                console.error(err);
            }

        }

        submitResults();

    });

    // Event Delegation = Esconder Serie.
    $(document).on('click', '.esconderSerie', function() {

        let id = $(this).data('id');
        let serie = document.getElementById('grupoSeries-' + id);
        serie.style.display === 'none' ? serie.style.display = 'flex' : serie.style.display = 'none';

    });

    // Event Delegation = Borrar Serie.
    $(document).on('click', '.eliminarSerie', function(e) {

        let serieId = $(this).data('serie-id');
        let colorId = $(this).data('color-id');
        let columnaId = $(this).data('id');

        for (let i = 0; i < verificadorSerie.length; i++) {

            if ((verificadorSerie[i].SERIE_ID == serieId) && (verificadorSerie[i].COLOR_ID == colorId)) {

                verificadorSerie.splice(i, 1);

            }

        }

        $('#serie-' + columnaId).remove();

    });

    // Botón de Añadir Pedido.
    document.getElementById('botonAñadirPedido').addEventListener("click", function() {

        // ID del formulario.
        let formulario = $('#añadirPedidoForm');

        // Si el formulario tiene algún campo vacio o incorrecto, lanzar error.
        if (!formulario[0].checkValidity()) return Swal.fire('Error', 'Por favor verifica todos los campos.', 'error');

        // Comprobar que haya alguna serie ingresada en el sistema.
        if (Object.entries(verificadorSerie).length === 0) {

            Swal.fire("Error", "Debes agregar al menos (1) serie al pedido.", "error");

        } else {

            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Descuida, puedes editar el pedido luego.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No',
            }).then((result) => {

                if (result.isConfirmed) {

                    // $.post => Enviando el elemento al backend.
                    $.post(`backend/api/pedidos/añadir.php`, formulario.serialize(), function(data) {

                        if (data === 'ERROR') {
                            return Swal.fire("Error", "Ha ocurrido un error al agregar el pedido, recarga la página.", "error");
                        }

                        Swal.fire({
                            title: 'Exito',
                            text: 'El pedido ha sido añadido satisfactoriamente.',
                            icon: 'success',
                            timer: 2000,
                            timerProgressBar: true,
                            allowEscapeKey: false,
                            allowOutsideClick: false
                        }).then((result) => {
                            if (result.dismiss === Swal.DismissReason.timer || result.value) {
                                if (userRole !== 'CLIENTE') {
                                    location.href = 'pedidos-pendientes.php';
                                } else {
                                    location.href = 'imprimir-pedidos-pendientes.php';
                                }
                            }
                        });

                    })

                }

            });

        }

    });
</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>