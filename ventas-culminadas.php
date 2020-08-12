<?php

// Incluimos el header.php y components.php
include 'components/header.php';
include 'components/components.php';

// Filtramos la página para que solo los cargos correspondientes puedan usarla.
if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'VENTAS' || $_SESSION['USUARIO']['CARGO'] == 'DESPACHO'):

?>

<!-- Incluimos el sidebar.php -->
<?php include 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">

    <!-- Incluimos el Navbar -->
    <?php get_navbar('Ventas', 'Ventas Culminadas'); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
    <div class="table-responsive-lg">
        <table class="table table-bordered text-center" id="tabla">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Nombre cliente</th>
                    <th scope="col">Tipo cliente</th>
                    <th scope="col">Fecha culminación</th>
                    <th scope="col">Forma de pago</th>
                    <th scope="col">Opciones</th>
                </tr>
            </thead>
            <tbody>
                <!-- Obtenemos la data mediante PHP -->
                <?php
                require_once 'backend/api/db.php';
                $sql = "SELECT P.*, C.TIPO AS CLIENTE_TIPO, C.NOMBRE AS CLIENTE_NOMBRE FROM PEDIDOS P JOIN CLIENTES C ON P.CLIENTE_ID = C.ID WHERE P.ESTADO = 'COMPLETADO';";
                $result = db_query($sql);

                // echo '<pre>'; print_r($result); echo '</pre>';

                foreach ($result as $row) {
                    echo "<tr>";

                    echo "<th scope='col'>{$row['ID']}</th>";
                    echo "<td>" . mb_convert_case($row['CLIENTE_NOMBRE'], MB_CASE_TITLE, "UTF-8") . "</td>";
                    echo "<td>" . mb_convert_case($row['CLIENTE_TIPO'], MB_CASE_TITLE, "UTF-8") . "</td>";
                    echo "<td>" . date('d-m-Y H:i:s', strtotime($row['UPDATED_AT'])) . "</td>";
                    echo "<td>" . mb_convert_case($row['FORMA_PAGO'], MB_CASE_TITLE, "UTF-8") . "</td>";
                    echo "<td><a href='#' data-toggle='modal' data-target='#showFinishedSales' data-id='{$row['ID']}'><i class='fas fa-eye icon-color'></i></a></td>";

                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <!-- / Fin de tabla -->

    <!-- Boton -->
    <?php if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'VENTAS'): ?>
        <div class="d-flex justify-content-center mt-5">
            <a class="btn btn-sm btn-main" href="#" role="button">Exportar Reporte</a>
        </div>
    <?php endif; ?>

    <!-- Modal de Mostrar Ventas Culminadas -->
    <div class="modal fade" id="showFinishedSales" tabindex="-1" role="dialog" aria-labelledby="showFinishedSales"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">

                <form action="" method="POST">

                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i
                                    class="fas fa-shopping-bag icon-color"></i> Datos del Pedido</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="table-responsive-lg py-3">
                            <table class="table table-bordered text-center" id="tabla-modal">
                                <thead class="thead-dark">
                                <tr>
                                    <th scope="col">Referencia</th>
                                    <th scope="col">Marca</th>
                                    <th scope="col">Color</th>
                                    <th scope="col">Talla</th>
                                    <th scope="col">Cantidad</th>
                                </tr>
                                </thead>
                                <tbody id="tabla-tbody-modal">
                                </tbody>
                            </table>
                        </div>

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

    // Custom Search DataTables
    $('#customInput').on( 'keyup', function () {
        tabla.search( this.value ).draw();
    });
    

    $('#showFinishedSales').on('show.bs.modal', function (e) {
        let pedidoId = $(e.relatedTarget).data('id');

        $.ajax({
            type: 'post',
            url: 'backend/api/utils.php?fun=obtenerProduccionReferencia',
            data: 'pedido_id=' + pedidoId,
            success: function (data) {
                const res = JSON.parse(data);
                const tabla = $('#tabla-modal > tbody:last-child');
                tabla.empty();

                res.forEach(row => {
                    tabla.append(`<tr>
                                    <td>${row.SUELA_REFERENCIA}</td>
                                    <td>${row.SUELA_MARCA.toProperCase()}</td>
                                    <td>${row.SUELA_COLOR.toProperCase()}</td>
                                    <td>${row.SUELA_TALLA}</td>
                                    <td>${row.CANTIDAD}</td>
                                </tr>`);
                });
            }
        });
    });

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