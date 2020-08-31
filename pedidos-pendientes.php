<?php

// Incluimos el header.php y components.php
include 'components/header.php';
include 'components/components.php';

// Agregamos los roles que se quiere que usen esta página.
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS', 'DESPACHO', 'PRODUCCION');

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
    <?php get_navbar('Ventas', 'Pedidos Pendientes'); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
    <div class="table-responsive-lg">
        <table class="table table-bordered text-center" id="tabla">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Tipo de Cliente</th>
                    <th scope="col">Forma de Pago</th>
                    <th scope="col">Fecha de Entrega</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Opciones</th>
                </tr>
            </thead>
            <tbody>

                <?php
                require_once "backend/api/db.php";
                require_once "backend/api/utils.php";

                $sql = "SELECT P.*, C.ID AS CLIENTE_ID, C.TIPO AS CLIENTE_TIPO, C.NOMBRE AS CLIENTE_NOMBRE FROM PEDIDOS P JOIN CLIENTES C ON P.CLIENTE_ID = C.ID WHERE P.ESTADO IN ('EN ANALISIS', 'PENDIENTE');";
                $result = db_query($sql);

                foreach ($result as $row) {
                    echo "<tr id='{$row['ID']}'>";
                    echo "<th scope='col'>{$row['ID']}</th>";
                    echo "<td>" . mb_convert_case($row['CLIENTE_NOMBRE'], MB_CASE_TITLE, "UTF-8") . "</td>";
                    echo "<td>" . mb_convert_case($row['CLIENTE_TIPO'], MB_CASE_TITLE, "UTF-8") . "</td>";
                    echo "<td>" . mb_convert_case($row['FORMA_PAGO'], MB_CASE_TITLE, "UTF-8") . "</td>";
                    echo "<td>" . date('d-m-Y', strtotime($row['FECHA_ESTIMADA'])) . "</td>";

                    if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR') {
                       
                        if ($row['ESTADO'] === 'EN ANALISIS') {
                            
                            echo "<td> 
                                    <a href='aprobar-pedido.php?id={$row['ID']}' class='btn btn-sm btn-main'>Aprobar Pedido</a>
                                </td>";

                        } else {
                            echo "<td>Pendiente</td>";
                        }
                    } else {

                        if ($row['ESTADO'] === 'EN ANALISIS') {

                            echo "<td>En analisis</td>";

                        } else {

                            echo "<td>Pendiente</td>";

                        }
                    }

                    echo "<td>";
                    
                    if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR') {

                        if ($row['ESTADO'] === 'EN ANALISIS') {
                            echo "<a href='editar-pedido.php?id={$row['ID']}'><i class='fas fa-edit icon-color'></i></a>";
                            echo "<a href='#' class='ml-1 eliminarPedido' data-id='{$row['ID']}'><i class='fas fa-trash icon-color'></i></a>";                     
                            echo "<a class='ml-1' href='#' data-toggle='modal' data-target='#verPedido' data-id='{$row['ID']}'><i class='fas fa-eye icon-color'></i></a>";
                        } else {
                            echo "<a class='ml-1' href='javascript:void(0)' data-toggle='modal' data-target='#verPedido' data-id='{$row['ID']}'>
                                <i class='fas fa-eye icon-color'></i>
                            </a>";
                            echo "<a href='javascript:void(0)' class='ml-1 cancelarPedido' data-id='{$row['ID']}'>
                                <i class='fas fa-ban icon-color'></i>
                            </a>";  
                        }

                    } 

                    if ($_SESSION['USUARIO']['CARGO'] == 'VENTAS') {
                        if ($row['ESTADO'] === 'EN ANALISIS') {
                            echo "<a href='editar-pedido.php?id={$row['ID']}'><i class='fas fa-edit icon-color'></i></a>";
                        }
                    }

                    echo "</td>";
                    echo "</tr>";

                }

                ?>
            </tbody>
        </table>
    </div>
    <!-- / Fin de tabla -->

    <!-- Añadimos el botón de Añadir Pedido -->
    <?php if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'VENTAS'): ?>
        <div class="d-flex justify-content-center mt-5">
            <a class="btn btn-sm btn-main" href="añadir-pedido.php" role="button">Añadir Pedido</a>
        </div>
    <?php endif; ?>
    
    <!-- Modal de ver pedido -->
    <div class="modal fade" id="verPedido" tabindex="-1" role="dialog" aria-labelledby="verPedido"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-shopping-bag icon-color"></i> Datos del Pedido</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body contenedorPedidos">
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Fin de Modal -->

    <!-- Modal de Aprobar Pedido -->
    <div class="modal fade" id="aprobarPedido" tabindex="-1" role="dialog" aria-labelledby="aprobarPedido"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form>
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-check icon-color"></i> Aprobar Pedido</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body contenedorPedidosAprobar">
                    </div>
                </form>

            </div>
        </div>
    </div>
    <!-- / Fin de Modal -->

</div>
<!-- / Fin del contenido -->

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
        targets: 4,
        searchable: true,
        orderable: true,
        className: "align-middle", "targets": "_all"
    }],
    language: {
        "url": "<?= BASE_URL . "datatables/Spanish.json"; ?>"
    }
});

// Eliminar pedidos.
$('.eliminarPedido').on('click', function (e) {

    let id = $(e.target.parentElement).data('id');

    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Si eliminas el pedido tendrás que añadirlo de nuevo.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {
        if (result.value) {
            Swal.fire({
                title: '¡Eliminado!',
                text: 'El pedido ha sido eliminado.',
                icon: 'success'
            }).then(function () {

                $.get(`backend/api/pedidos/delete.php?id=${id}`, function () {

                    let htmlElem = document.getElementById(id);
                    htmlElem.parentNode.removeChild(htmlElem);

                });

            });
        }
    });

});

$('.cancelarPedido').on('click', function (e) {

    // Pedido_id
    let id = $(e.target.parentElement).data('id');

    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Todas las suelas producidas pasaran al INVENTARIO, además, las suelas sacadas del stock volverán a su destino.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {
        if (result.value) {
            Swal.fire({
                title: '¡Eliminado!',
                text: 'El pedido ha sido eliminado.',
                icon: 'success'
            }).then(function () {

                // $.post => Añadiendo el elemento al backend.
                $.post( 'backend/api/pedidos/cancelar.php', { 'pedido_id': id }, function() {

                    let htmlElem = document.getElementById(id);
                    htmlElem.parentNode.removeChild(htmlElem);

                });

            });
        }
    });

});
</script>

<!-- COMPONENTE = > Ver Pedido -->
<script src="js/ver-pedido.js"></script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>