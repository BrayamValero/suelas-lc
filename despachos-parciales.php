<?php

// Incluimos el header.php y components.php
include 'components/header.php';
include 'components/components.php';

// Filtramos la página para que solo los cargos correspondientes puedan usarla.
if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'VENTAS' || $_SESSION['USUARIO']['CARGO'] == 'DESPACHO'): 

// Agarrar toda la producción que se encuentre "PENDIENTE".
require_once "backend/api/db.php";
$sql = "SELECT P.*, C.ID AS CLIENTE_ID, C.NOMBRE AS CLIENTE_NOMBRE, C.TIPO AS CLIENTE_TIPO FROM PEDIDOS P JOIN CLIENTES C ON P.CLIENTE_ID = C.ID WHERE P.ESTADO IN ('PENDIENTE');";

$result = db_query($sql);

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
            foreach ($result as $row) {
                echo "<tr>";

                echo "<th scope='col'>{$row['ID']}</th>";
                echo "<td>" . mb_convert_case($row['CLIENTE_NOMBRE'], MB_CASE_TITLE, "UTF-8") . "</td>";
                echo "<td>" . mb_convert_case($row['CLIENTE_TIPO'], MB_CASE_TITLE, "UTF-8") . "</td>";
                echo "<td>" . mb_convert_case($row['FORMA_PAGO'], MB_CASE_TITLE, "UTF-8") . "</td>";
                echo "<td>" . date('d-m-Y', strtotime($row['FECHA_ESTIMADA'])) . "</td>";
                echo "<td><a href='#' data-toggle='modal' data-target='#showDispatchedOrders' data-id='{$row['ID']}'><i class='fas fa-eye icon-color'></i></a></td>";

                echo "</tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
    <!-- End of Table -->
    
    <?php
        if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'DESPACHO'): 
    ?>
    <hr>

    <!-- Informacion Inferior -->
    <div class="pt-3">
        <div class="row">
            <div class="col-md-12 col-lg-10">
                <h6 class="pb-2 text-info font-weight-bold">Registro de Peso</h6>
                <!-- Tabla -->
                <div class="table-responsive-lg">
                    <table class="table table-bordered text-center" id="tabla2">
                        <thead class="thead-dark">
                        <tr>
                            <th scope="col">Pedido ID</th>
                            <th scope="col">Marca</th>
                            <th scope="col">Talla</th>
                            <th scope="col">Color</th>
                            <th scope="col">Cantidad</th>
                            <th scope="col">Estado</th>
                        </tr>
                        </thead>
                        <tbody>

                        <?php
                        $sql = "SELECT PE.ID, SU.MARCA, SU.TALLA, CO.COLOR, PRO.POR_PESAR, PRO.ID AS PRODUCCION_ID FROM PEDIDOS PE JOIN PRODUCCION PRO ON PE.ID = PRO.PEDIDO_ID JOIN SUELAS SU ON SU.ID = PRO.SUELA_ID JOIN COLOR CO ON CO.ID = PRO.COLOR_ID WHERE PE.ESTADO IN ('PENDIENTE');";
                        $result = db_query($sql, array());

                        foreach ($result as $row) {
                            if ($row['POR_PESAR'] > 0) {
                                echo "<tr>";

                                echo "<th>{$row['ID']}</th>";
                                echo "<td>" . mb_convert_case($row['MARCA'], MB_CASE_TITLE, "UTF-8") . "</td>";
                                echo "<td>" . mb_convert_case($row['TALLA'], MB_CASE_TITLE, "UTF-8") . "</td>";
                                echo "<td>" . mb_convert_case($row['COLOR'], MB_CASE_TITLE, "UTF-8") . "</td>";
                                echo "<td>" . mb_convert_case($row['POR_PESAR'], MB_CASE_TITLE, "UTF-8") . "</td>";
                                echo "<td><a href='#' class='btn btn-sm btn-main' onclick='registrarPeso({$row['PRODUCCION_ID']}, {$row['POR_PESAR']})'>Registrar Peso</a></td>";

                                echo "</tr>";
                            }
                        }
                        ?>
                        </tbody>
                    </table>
                </div>
                <!-- Fin de Tabla -->
            </div>
        </div>
    </div>
    <!-- Fin de Informacion Inferior -->

    <?php 
        endif;
    ?>

    <!-- Modal de Mostrar Orderes Despachadas -->
    <div class="modal fade" id="showDispatchedOrders" tabindex="-1" role="dialog"
            aria-labelledby="showDispatchedOrders" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">

                <!-- Form -->
                <form action="backend/api/pedidos/despachar.php" method="POST" id="form_checkboxes">

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

// Custom Search DataTables
$('#customInput').on( 'keyup', function () {
    tabla.search( this.value ).draw();
});

    // DataTables Plugin: https://datatables.net/
    const tabla_1 = $('#tabla2').DataTable({
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

function registrarPeso(id, cantidad) {
    swal({
        title: "Despachos",
        text: `Ingrese el peso correspondiente a los ${cantidad} pares de suelas`,
        content: "input",
    })
        .then((peso) => {

            if ( peso > 0 && peso !== null ) {

                swal({
                    title: "¿Estás seguro?",
                    text: `El peso a registrar es de ${peso} Kgs por (${cantidad}) pares de suelas.`,
                    icon: "warning",
                    buttons: [
                        'No',
                        'Si'
                    ],
                    dangerMode: true,
                }).then(function (isConfirm) {
                    if (isConfirm && cantidad != null) {
                        swal({
                            title: '¡Empaquetado!',
                            text: 'El pedido ha sido enviado a despacho.',
                            icon: 'success'
                        }).then(function () {
                            $.get("backend/api/pedidos/editar-produccion.php", {
                                    id: id,
                                    estado: 'PESADO',
                                    pares: cantidad,
                                    peso: peso
                                },
                                function (data, status) {
                                    if (status === "success") {
                                        window.location.reload();
                                    }
                                });
                        });
                    } else {
                        swal("Cancelado", "Descuida, puedes volver a intentarlo luego.", "error");
                    }
                });

            }
            
        });
            
}


$('#showDispatchedOrders').on('show.bs.modal', function (e) {

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
                        <input class="form-check-input" type="checkbox" name="pedido-id-${row.ID}" value="${row.ID}">
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
    if ($('#form_checkboxes input[type=checkbox]:checked').length == 0) {
        e.preventDefault();
        swal("Whoops", "Debes marcar un pedido primero.", "warning");
    }
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