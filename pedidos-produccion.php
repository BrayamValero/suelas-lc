<?php

// Incluimos el header.php y components.php
$title = 'Pedidos Producción';
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
    <?php get_navbar('Auditorias', 'Pedidos a Producción'); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
    <div class="table-responsive-lg">
        <table class="table table-bordered text-center" id="tabla">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">De Pedidos a Producción</th>
                <th scope="col">De Producción a Administración</th>
                <th scope="col">Estado</th>
                <th scope="col">Ver</th>
            </tr>
            </thead>
            <tbody>
            <?php
            require_once "backend/api/db.php";
            require_once "backend/api/utils.php";

            $sql = "SELECT * FROM AUDITORIA_PED_PRO;";
            $result = db_query($sql);

            // echo '<pre>'; print_r($result); echo '</pre>';

            foreach ($result as $row) {

                echo "<tr>";
                echo "<th scope='col'>{$row['ID']}</th>";
                echo "<td>" . strftime("%d de %b de %Y, %H:%M %p", strtotime($row['FECHA_RECIBIDO'])) . "</td>";

                if ($row['FECHA_ENTREGADO'] === NULL) {
                    echo "<td>Pendiente</td>";
                } else {
                    echo "<td>" . strftime("%d de %B de %Y, %H:%M %p", strtotime($row['FECHA_ENTREGADO'])) . "</td>";
                }
                
                if ($row['ESTADO'] === 'PENDIENTE') {
                    echo "<td> 
                        <i class='fas fa-times-circle text-danger'></i></button>
                    </td>";
                } else {
                    echo "<td> 
                        <i class='fas fa-check-circle text-success'></i></button>
                    </td>";
                }

                echo "<td>
                        <a href='#' data-toggle='modal' data-target='#verPedido-modal' data-id='{$row['ID']}' data-pedido-id='{$row['PRODUCCION_PEDIDO_ID']}'>
                            <i class='fas fa-eye icon-color'></i>
                        </a>
                    </td>";

                echo "</tr>";
            }
            
            ?>

            </tbody>
        </table>
    </div>
    <!-- Fin de Tabla -->

    <!-- Modal de Ver Pedido -->
    <div class="modal fade" id="verPedido-modal" tabindex="-1" role="dialog" aria-labelledby="verPedido-modal"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">

                <!-- Form -->
                <form>

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-shopping-bag icon-color"></i> Datos del Pedido</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <!-- Table -->
                        <div class="table-responsive-lg">
                            <table class="table table-bordered text-center" id="tabla-modal">
                                <!-- Table Head -->
                                <thead class="thead-dark">
                                <tr>    
                                    <th class="align-middle" scope="col">Prioridad</th>  
                                    <th class="align-middle" scope="col">Marca</th>
                                    <th class="align-middle" scope="col">Color</th>
                                    <th class="align-middle" scope="col">Talla</th>
                                    <th class="align-middle" scope="col">Cantidad</th>
                                    <th class="align-middle" scope="col">Estado</th>
                                </tr>
                                </thead>
                                <!-- Table Body -->
                                <tbody></tbody>
                            </table>
                            <!-- Aviso -->
                            <div id="checkEstado" class="form-group text-center"></div>
                        </div>
                        <!-- End of Table -->

                    </div>

                </form>
                <!-- End of Form -->

            </div>
        </div>
    </div>
    <!-- Fin de Modal de Ver Pedido -->

</div>
<!-- Fin de Content -->

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

// Ver pedidos.
$('#verPedido-modal').on('show.bs.modal', function (e) {

    // Obtener Datos del Pedido
    let pedidoId = $(e.relatedTarget).data('pedido-id');

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerProduccionReferencia',
        data: 'pedido_id=' + pedidoId,
        success: function (data) {

            const result = JSON.parse(data);
            const tabla = $('#tabla-modal > tbody:last-child');
            tabla.empty();

            let urgente;

            result.forEach(row => {

                if (row.URGENTE == '0') {
                    urgente = '<i class="fa fa-circle" style="color: white; -webkit-text-stroke: 1px #323232;"></i>';
                } else {
                    urgente = '<i class="fa fa-circle" style="color: light-green; -webkit-text-stroke: 1px light-green;"></i>';
                }
                
                tabla.append(`<tr>
                    <td>${urgente}</td>
                    <td>${row.SUELA_MARCA.toProperCase()}</td>
                    <td>${row.SUELA_COLOR.toProperCase()}</td>
                    <td>${row.SUELA_TALLA}</td>
                    <td>${row.CANTIDAD}</td>
                    <td>${row.ESTADO.toProperCase()}</td>
                </tr>`);

            });

        }
    });

    // Obtenemos el estado de Pedido a Produccion
    let id = $(e.relatedTarget).data('id');

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerEstadoPedidoAProduccion',
        data: 'id=' + id,
        success: function (data) {

            const result = JSON.parse(data);
            const checkEstado = $('#checkEstado');
            checkEstado.empty();
    
            if (result[0].ESTADO === 'PENDIENTE') {
                checkEstado.append(
                    `<small>Los moldes <strong class="text-info">no se han acomodado aun.</strong></small>`
                );
            } else {
                checkEstado.append(
                    `<small>Los moldes <strong class="text-info">ya se encuentran acomodados.</strong></small>`
                );
            }
            
        }
    });

});

</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>