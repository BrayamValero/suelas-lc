<?php

// Incluimos el header.php y components.php
$title = 'Modificaciones';
include 'components/header.php';
include 'components/navbar.php';
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
    <?php get_navbar('Panel de Control', 'Modificaciones'); ?>

    <?php

    // Dureza
    require_once 'backend/api/db.php';
    $sql = "SELECT * FROM DUREZA;";
    $dureza = db_query($sql);

    // Pedido
    $sql = "SELECT PED.ID AS PEDIDO_ID, PED.ESTADO AS ESTADO_PEDIDO, CLI.NOMBRE, CLI.DOCUMENTO, CLI.DOCUMENTO_NRO FROM PEDIDOS PED JOIN CLIENTES CLI ON CLI.ID = PED.CLIENTE_ID WHERE PED.ESTADO NOT IN ('COMPLETADO') AND PED.ESTADO NOT IN ('EN ANALISIS');";
    $pedidos = db_query($sql);

    ?>

    <div class="card-deck">
        
        <!-- Dureza -->
        <div class="card bg-white mb-3">
            <div class="card-header font-weight-bold">Dureza</div>
            <div class="card-body">
                <h5 class="card-title">Porcentaje de Dureza</h5>
                <p class="card-text text-muted">Permite editar el porcentaje de dureza</p>
            </div>
            <div class="card-footer">
                <button class="btn btn-sm btn-main" data-id="<?php echo $dureza[0]['ID']; ?>" data-dureza="<?php echo $dureza[0]['DUREZA']; ?>" data-toggle="modal" data-target="#editarDurezaModal">Editar Dureza</button>
            </div>
        </div>

        <!-- Pedido -->
        <div class="card bg-white mb-3">
            <div class="card-header font-weight-bold">Pedidos</div>
            <div class="card-body">
                <h5 class="card-title">Modificacion de Pedidos en Producción</h5>
                <p class="card-text text-muted">Solo se modifican las cantidades asignadas</p>
            </div>
            <div class="card-footer">
                <button class="btn btn-sm btn-main" id="editarPedido" data-toggle="modal" data-target="#editarPedidoModal">Modificar Pedidos</button>
            </div>
        </div>

    </div>

    <!-- Modal de Editar Dureza -->
    <div class="modal fade" id="editarDurezaModal" tabindex="-1" role="dialog" aria-labelledby="editarDurezaModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <!-- Form -->
                <form action="backend/api/modificaciones/editar.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Dureza</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row justify-content-center">

                            <!-- ID escondido para el POST -->
                            <input type="hidden" name="id" id="durezaId"> 

                            <div class="form-group col-10">
                                <label for="editarDureza">Dureza</label>
                                <input id="editarDureza" type="number" min="0" max="100" class="form-control" name="dureza" placeholder="Dureza" required>
                            </div>
                            
                        </div>  
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-main">Editar Dureza</button>
                    </div>
                </form>
                <!-- Fin de Form -->
            </div>
        </div>
    </div>
    <!-- Fin de Modal de Editar Dureza -->

    <!-- Modal de Modificar Pedido -->
    <div class="modal fade" id="editarPedidoModal" tabindex="-1" role="dialog" aria-labelledby="editarPedidoModal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <!-- Form -->
                <form action="backend/api/modificaciones/editar-pedido.php" method="post">
                    <div class="modal-header">
                        <h5 class="modal-title">Modificar Pedido</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row justify-content-center">
                            <div class="form-group col-sm-10">
                                <label for="seleccionarPedido">Seleccione el Pedido</label>
                                <select id="seleccionarPedido" class="form-control dropdown-select2" name="pedido_id" required>
                                    <?php 

                                    foreach ($pedidos as $pedido) {
                                                                    
                                        echo "<option value='{$pedido['PEDIDO_ID']}'>[Pedido {$pedido['PEDIDO_ID']}] " . mb_convert_case($pedido['NOMBRE'], MB_CASE_TITLE) . " - {$pedido['DOCUMENTO']} {$pedido['DOCUMENTO_NRO']}</option>";

                                    }

                                    ?>
                                </select>
                            </div>
                            <!-- Contenedor de los Datos del Pedido -->
                            <div class="form-group col-sm-10" id="detallesPedido"></div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-main">Modificar Pedido</button>
                    </div>
                </form>
                <!-- Fin de Form -->
            </div>
        </div>
    </div>
    <!-- Fin de Modal de Pedido -->

</div>
<!-- End of Content section -->

<script>

// Editar Dureza
$('#editarDurezaModal').on('show.bs.modal', function (e) {

    let id = e.relatedTarget.getAttribute('data-id');
    let dureza = e.relatedTarget.getAttribute('data-dureza');

    document.getElementById('durezaId').value = id;
    document.getElementById('editarDureza').value = dureza;

});

// Editar Pedido
$('#editarPedidoModal').on('show.bs.modal', function (e) {

    let pedido_id = document.getElementById('seleccionarPedido').value;

    $.ajax({
        type: 'post',
        url: 'backend/api/modificaciones/ver-pedido.php',
        data: 'pedido_id=' + pedido_id,
        async: false,
        success: function (data) {
            
            document.getElementById('detallesPedido').innerHTML = data;

        }

    });

});

document.getElementById('seleccionarPedido').onchange = function() {
    
    let pedido_id = document.getElementById('seleccionarPedido').value;

    $.ajax({
        type: 'post',
        url: 'backend/api/modificaciones/ver-pedido.php',
        data: 'pedido_id=' + pedido_id,
        async: false,
        success: function (data) {
            
            document.getElementById('detallesPedido').innerHTML = data;

        }

    });
    
}

</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>