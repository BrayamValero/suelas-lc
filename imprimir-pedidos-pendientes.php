<?php

// Incluimos el header.php y components.php
$title = 'Imprimir Pedidos';
require_once 'components/header.php';
require_once 'components/navbar.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS', 'DESPACHO', 'PRODUCCION', 'CLIENTE');

$rol = $_SESSION['ROL'];

if (!in_array($rol, $roles_permitidos)) {
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
    <?php get_navbar('Ventas', $title, true); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
    <div class="table table-responsive text-center W-100">
        <div id="spinner" class="spinner-border text-center" role="status">
            <span class="sr-only">Cargando...</span>
        </div>
        <table class="table table-bordered text-center" id="tabla">
            <thead class="thead-dark"></thead>
        </table>
    </div>
    <!-- Fin de Tabla -->

    <!-- Pagination -->
    <div class='tablePagination'>

    </div>

</div>
<!-- / Fin del contenido -->

<!-- Inline JavaScript -->
<script>
    var tabla;

    // DATATABLES => Mostrando la tabla PEDIDOS_PENDIENTES.
    $.ajax({
        type: 'get',
        url: 'backend/api/utils.php?fun=obtenerPedidosParaImprimir',
        async: true,
        success: (data) => {

            const result = JSON.parse(data);

            console.log(result);

            // Defining Custom Column Data
            const columns = [{
                    data: "PEDIDO_ID",
                    title: 'CODIGO'
                },
                {
                    data: "FECHA_CREACION",
                    title: 'FECHA',
                    render: (val, type, row) => {
                        const date = new Date(val);
                        const dateFormatter = new Intl.DateTimeFormat('es-MX', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                        });
                        const result = dateFormatter.format(date);
                        return result;
                    }
                },
                {
                    data: "NOMBRE",
                    title: 'CLIENTE'
                },
                {
                    data: "DOCUMENTO_NRO",
                    title: 'ID CLIENTE'
                },
                {
                    data: "MARCA",
                    title: 'REFERENCIA'
                },
                {
                    data: "COLOR",
                    title: 'COLOR'
                },
            ];

            // Pushing 0 as Aplique...
            columns.push({
                data: "TALLAS",
                title: "0",
                render: (val, type, row) => {
                    return val[0] ? val[0]['CANTIDAD'] : null;
                }
            })

            // Printing from 21 to 44 (as needed)
            for (let i = 21; i < 44; i++) {
                columns.push({
                    data: "TALLAS",
                    title: i.toString(),
                    render: (val, type, row) => {
                        return val[i] ? val[i]['CANTIDAD'] : null;
                    }
                })
            }

            // Pushing Table
            tabla = $('#tabla').DataTable({
                initComplete: (settings, json) => {
                    $("#spinner").css('display', 'none');
                },
                "info": false,
                "dom": "Blrtip",
                "pageLength": 10,
                "lengthChange": false,
                "order": [
                    [0, 'desc']
                ],
                "data": result,
                "columns": columns,
                "columnDefs": [{
                    searchable: true,
                    orderable: true,
                    className: "align-middle",
                    "targets": "_all"
                }],
                "language": {
                    "url": "datatables/Spanish.json"
                },
                "buttons": [{
                        extend: 'csv',
                        text: 'CSV',
                        title: 'Suelas LC | Impresión de Pedidos',
                        className: 'btn btn-info',
                    },
                    {
                        extend: 'excel',
                        text: 'Excel',
                        title: 'Suelas LC | Impresión de Pedidos',
                        className: 'btn btn-success',
                    },
                    {
                        extend: 'pdf',
                        text: 'PDF',
                        title: 'Suelas LC | Impresión de Pedidos',
                        className: 'btn btn-danger',
                        orientation: 'landscape',
                        pageSize: 'LEGAL'
                    },
                    {
                        extend: "print",
                        text: 'Imprimir',
                        title: 'Suelas LC | Impresión de Pedidos',
                        className: 'btn btn-primary',
                        customize: (win) => {
                            $(win.document.body)
                                .css('margin', '10px')
                                .css('font-size', '10pt');

                            $(win.document.body).find('table')
                                .css('padding', 'inherit')
                                .css('font-size', 'inherit');

                            var last = null;
                            var current = null;
                            var bod = [];
                            var css = '@page { size: landscape; }',
                                head = win.document.head || win.document.getElementsByTagName('head')[0],
                                style = win.document.createElement('style');

                            style.type = 'text/css';
                            style.media = 'print';

                            if (style.styleSheet) {
                                style.styleSheet.cssText = css;
                            } else {
                                style.appendChild(win.document.createTextNode(css));
                            }

                            head.appendChild(style);
                        }
                    },
                ]
            });

            // DATATABLES => Paginación
            $.fn.DataTable.ext.pager.numbers_length = 5;

        }

    });
</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>