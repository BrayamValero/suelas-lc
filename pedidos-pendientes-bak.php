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
                <!-- Obtenemos la data mediante PHP -->
                <?php
                require_once "backend/api/db.php";
                require_once "backend/api/utils.php";
                $sql = "SELECT P.*, C.ID AS CLIENTE_ID, C.TIPO AS CLIENTE_TIPO, C.NOMBRE AS CLIENTE_NOMBRE FROM PEDIDOS P JOIN CLIENTES C ON P.CLIENTE_ID = C.ID WHERE P.ESTADO IN ('EN ANALISIS', 'PENDIENTE');";
                $result = db_query($sql);

                // echo '<pre>'; print_r($result); echo '</pre>';

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
                                <button class='btn btn-sm btn-main' onclick='aprobarPedido({$row['ID']}, {$row['CLIENTE_ID']})'>En analisis</button>
                                </td>";

                            // echo "<td>  
                            //         <button type='button' class='btn btn-sm btn-main' data-toggle='modal' data-target='#aprobarPedido-modal'>
                            //             En Analisis
                            //         </button>
                            //     </td>";


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
                        }
                    } 

                    if ($_SESSION['USUARIO']['CARGO'] == 'VENTAS') {
                        if ($row['ESTADO'] === 'EN ANALISIS') {
                            echo "<a href='editar-pedido.php?id={$row['ID']}'><i class='fas fa-edit icon-color'></i></a>";
                        }
                    }
                        
                    echo "<a class='ml-1' href='#' data-toggle='modal' data-target='#verPedido-modal' data-id='{$row['ID']}'><i class='fas fa-eye icon-color'></i></a>";
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
    <div class="modal fade" id="verPedido-modal" tabindex="-1" role="dialog" aria-labelledby="verPedido-modal"
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

                    <div class="modal-body">

                        <div class="table-responsive-lg py-3">
                            <table class="table table-bordered text-center" id="tabla-modal">
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

</div>
<!-- / Fin del contenido -->

<!-- Inline JavaScript -->
<script>

    var materiales_solicitados = [];
    var obtenerDureza;

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
 
    // Ver pedidos.
    $('#verPedido-modal').on('show.bs.modal', function (e) {

        let pedidoId = $(e.relatedTarget).data('id');

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
                        urgente = '<i class="fa fa-circle" style="color: green; -webkit-text-stroke: 1px light-green;"></i>';
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
    });

    // Eliminar pedidos.
    $('.eliminarPedido').on('click', function (e) {

        let row = $(e.target.parentElement).data('id');

        swal({
            title: "¿Estás seguro?",
            text: "Si eliminas el pedido tendrás que añadirlo de nuevo.",
            icon: "warning",
            buttons: [
                'No',
                'Si'
            ],
            dangerMode: true,
        }).then(function (isConfirm) {
            if (isConfirm) {
                swal({
                    title: '¡Eliminado!',
                    text: 'El pedido ha sido eliminado.',
                    icon: 'success'
                }).then(function () {

                    $.get(`backend/api/pedidos/delete.php?id=${row}`, function () {

                        var elem = document.getElementById(row);
                        elem.parentNode.removeChild(elem);

                    });

                });
            } else {
                swal("Cancelado", "Descuida, puedes volver a intentarlo luego.", "error");
            }
        });
    });

    
    // Obtenemos la dureza en el Global Scope.
    $.ajax({
        type: 'get',
        url: 'backend/api/utils.php?fun=obtenerDureza',
        success: function (data) {

            obtenerDureza = JSON.parse(data);

        }

    });   


    // Aprobar pedidos.
    function aprobarPedido(pedido_id, cliente_id) {
        swal({
            title: "¿Estás seguro?",
            text: "Si cambias el estado del pedido no podrás editarlo.",
            icon: "warning",
            buttons: [
                'No',
                'Si'
            ],
            dangerMode: true,
        }).then(function (isConfirm) {

            if (isConfirm) {
                swal({
                    title: '¡Pendiente!',
                    text: 'El pedido ya pasó a producción.',
                    icon: 'success'
                }).then(function () {

                    // 1. Obtenemos los materiales solicitados.
                    $.ajax({
                        type: 'post',
                        url: 'backend/api/utils.php?fun=obtenerCantidadesSolicitud',
                        data: 'pedido_id=' + pedido_id,
                        success: function (data) {

                            const result = JSON.parse(data);

                            let datosFiltrados = [],
                                materiales_solicitados = [];

                            result.forEach(row => {

                                let index = datosFiltrados.findIndex(elem => {
                                    return elem.MATERIAL == row.MATERIAL && elem.COLOR == row.COLOR;
                                });

                                if(index != -1) {
                                    datosFiltrados[index].CANTIDAD = parseInt(datosFiltrados[index].CANTIDAD) + parseInt(row.CANTIDAD);
                                } else {
                                    datosFiltrados.push(row);
                                }
                                
                            });

                            datosFiltrados.forEach(row => {

                                const materiales = {
                                    material: row.MATERIAL,
                                    color: row.COLOR,
                                    cantidad: (row.CANTIDAD / row.PESO_IDEAL).toString(),
                                    dureza: obtenerDureza[0].DUREZA
                                };

                                materiales_solicitados.push(materiales);

                            });

                            console.table(materiales_solicitados);

                            // 2. Creamos la solicitud de la materia prima a Norsaplast.
                            $.post("backend/api/solicitud_material/crear.php", {
                                solicitud_material: JSON.stringify({
                                    'pedido_id': pedido_id,
                                    'cliente_id': cliente_id
                                }),
                                'materiales_solicitados': JSON.stringify(materiales_solicitados)
                            });

                        }

                    });
                    
                    // 3. Cambiamos el botón de confirmar pedido.
                    window.location = `backend/api/pedidos/confirmar.php?id=${pedido_id}`
                    
                });

            } else {
                swal("Cancelado", "Descuida, puedes volver a intentarlo luego.", "error");
            }
        });
    };

    // Cambiar Prioridad => Baja o Alta
    function cambiar_prioridad(el) {

        const prioridad = el.value;
        const pedido_id = el.dataset.pedidoid;

        window.location = `backend/api/pedidos/cambiar_prioridad.php?id=${pedido_id}&prioridad=${prioridad}`;
    }

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