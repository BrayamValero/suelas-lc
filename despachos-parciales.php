<?php

// Incluimos el header.php y components.php
include 'components/header.php';
include 'components/components.php';

// Agregamos los roles que se quiere que usen esta página.
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS', 'DESPACHO');

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
    <?php get_navbar('Ventas', 'Despachos Parciales'); ?>

    <!-- Tabla -->
    <div class="table-responsive-lg pb-3">
        <table class="table table-bordered text-center" id="tabla">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Cliente</th>
                <th scope="col">Tipo de Cliente</th>
                <th scope="col">Forma de Pago</th>
                <th scope="col">Fecha de Entrega</th>
                <th scope="col">Opciones</th>
            </tr>
            </thead>
            <tbody>
            <?php

            require_once "backend/api/db.php";

            // Agarrar toda la producción que se encuentre "PENDIENTE".
            $sql = "SELECT P.*, C.ID AS CLIENTE_ID, C.NOMBRE AS CLIENTE_NOMBRE, C.TIPO AS CLIENTE_TIPO FROM PEDIDOS P JOIN CLIENTES C ON P.CLIENTE_ID = C.ID WHERE P.ESTADO IN ('PENDIENTE');";
            $result = db_query($sql);

            foreach ($result as $row) {
                echo "<tr>";

                echo "<th scope='col'>{$row['ID']}</th>";
                echo "<td>" . mb_convert_case($row['CLIENTE_NOMBRE'], MB_CASE_TITLE, "UTF-8") . "</td>";
                echo "<td>" . mb_convert_case($row['CLIENTE_TIPO'], MB_CASE_TITLE, "UTF-8") . "</td>";
                echo "<td>" . mb_convert_case($row['FORMA_PAGO'], MB_CASE_TITLE, "UTF-8") . "</td>";
                echo "<td>" . date('d-m-Y', strtotime($row['FECHA_ESTIMADA'])) . "</td>";
                echo "<td><a href='#' data-toggle='modal' data-target='#verOrdenesDespachadas' data-id='{$row['ID']}'><i class='fas fa-eye icon-color'></i></a></td>";

                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
    <!-- End of Table -->

    <!-- Modal de Ver Orderes Despachadas -->
    <div class="modal fade" id="verOrdenesDespachadas" tabindex="-1" role="dialog"
            aria-labelledby="verOrdenesDespachadas" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">

                <!-- Form -->
                <form action="backend/api/pedidos/despachar.php" method="POST" id="verificarCheckBoxes">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i
                                    class="fas fa-shopping-bag icon-color"></i> Datos del Pedido</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <!-- Table -->
                        <div class="table-responsive-lg py-3">
                            <table class="table table-bordered text-center" id="tabla-modal">
                                <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Marca</th>
                                    <th scope="col">Talla</th>
                                    <th scope="col">Color</th>
                                    <th scope="col">Total</th>
                                    <th scope="col">Disponible para despachar</th>
                                    <th scope="col">Despachado</th>
                                    <th scope="col">Opciones</th>
                                </tr>
                                </thead>
                                <tbody id="tabla-tbody-modal">
                                </tbody>
                            </table>
                        </div>
                        <!-- End of Table -->
                    </div>

                    <div class="modal-footer">
                        <?php
                            if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'DESPACHO'): 
                        ?>
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" id="submit_modal" class="btn btn-sm btn-main">Entregar Pedido Parcial </button>
                        <?php 
                            endif;
                        ?>
                    </div>

                </form>
                <!-- End of Form -->

            </div>
        </div>
    </div>
    <!-- / Fin de Modal -->

</div>
<!-- / Fin de Contenido-->

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

$('#verOrdenesDespachadas').on('show.bs.modal', function (e) {

    let pedidoId = $(e.relatedTarget).data('id');

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerProduccionReferencia',
        data: 'pedido_id=' + pedidoId,
        success: function (data) {

            const result = JSON.parse(data);
            const tabla = $('#tabla-modal > tbody:last-child');
            tabla.empty();

            result.forEach(row => {

                if (row.ESTADO === 'COMPLETADO') {
                    row.ESTADO = `
                    <div class="form-check p-0">
                        <i class="fas fa-check"></i>
                    </div>
                    `;
                } else {
                    row.ESTADO = `
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="producción-id-${row.ID}" value="${row.ID}">
                    </div>
                    `;
                }

                tabla.append(`<tr>
                    <td>${row.SUELA_MARCA.toProperCase()}</td>
                    <td>${row.SUELA_TALLA}</td>
                    <td>${row.SUELA_COLOR.toProperCase()}</td>
                    <td>${row.CANTIDAD}</td>
                    <td>${row.DISPONIBLE}</td>
                    <td>${row.DESPACHADO}</td>
                    <td>
                        ${row.ESTADO}
                    </td>
                </tr>`);
            });
        }
    });

}).on('submit', function (e) {

    console.log($('#verificarCheckBoxes input[type=checkbox]:checked').length);

    if ($('#verificarCheckBoxes input[type=checkbox]:checked').length === 0) {

        e.preventDefault();

        return Swal.fire("Whoops", "Debes marcar un pedido primero.", "warning");

    }

});

</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>