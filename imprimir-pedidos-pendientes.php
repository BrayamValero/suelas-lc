<?php

// Incluimos el header.php y components.php
$title = 'Pedidos Pendientes';
require_once 'components/header.php';
require_once 'components/navbar.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS', 'DESPACHO', 'PRODUCCION');

$rol = $_SESSION['ROL'];

if(!in_array($rol, $roles_permitidos)){
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
    <?php get_navbar('Ventas', 'Pedidos Pendientes', true); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
	<div class="table-responsive text-center W-100">
		<div id="spinner" class="spinner-border text-center" role="status">
			<span class="sr-only">Cargando...</span>
		</div>
		<table class="table table-bordered text-center" id="tabla">
			<thead class="thead-dark"></thead>
		</table>
	</div>
	<!-- Fin de Tabla -->

</div>
<!-- / Fin del contenido -->

<!-- Inline JavaScript -->
<script>

// VARIABLES => Declarando Variables Globales.
var tabla

// DATATABLES => Mostrando la tabla PEDIDOS_PENDIENTES.
$.ajax({
    type: 'get',
    url: 'backend/api/utils.php?fun=obtenerPedidosParaImprimir',
    async: true,
    success: (data) => {

        const result = JSON.parse(data);

        console.log(result);


        // Defining Custom Column Data
        const columns = [
            { data: "PEDIDO_ID", title: '#' },
            { data: "NOMBRE", title: 'CLIENTE' },
            { data: "FECHA_CREACION", title: 'FECHA' },
            { data: "ESTADO", title: 'ESTADO' },
            { data: "MARCA", title: 'MARCA' },
            { data: "COLOR", title: 'COLOR' },
        ];

        // Pushing 0
        columns.push({ 
            data: "TALLAS", 
            title: '0', 		
            render: (value, type, row) => {
                return value[0] ? value[0]['CANTIDAD'] : null;
            }
        })

        // Printing all Columns
        for (let i = 21; i < 44; i++) {
            columns.push({ 
                data: "TALLAS", 
                title: i, 		
                render: (value, type, row) => {
                    return value[i] ? value[i]['CANTIDAD'] : null;
                }
            })
        }

        tabla = $('#tabla').DataTable({
            "initComplete": (settings, json) => {
                $("#spinner").css('display', 'none');
            },
            "info": false,
            "dom": "Blrtip",
            "pageLength": 10,
            "lengthChange": false,
            "order": [[0, 'desc']],
            "data": result,
            "columns": columns,
            "columnDefs": [{
                searchable: true,
                orderable: true,
                className: "align-middle", "targets": "_all"
            }],
            "language": {
                "url": "datatables/Spanish.json"
            },
            "buttons": [
                'csv', 'excel',
                {
                    extend: 'pdf',
                    orientation: 'landscape',
                    pageSize: 'LEGAL'
                },
                {
                    extend: "print",
                    customize: (win) => {
                        $(win.document.body)
                            .css( 'margin', '10px' )
                            .css( 'font-size', '10pt' );

                        $(win.document.body).find( 'table' )
                            .css( 'padding', 'inherit' )
                            .css( 'font-size', 'inherit' );

                        var last = null;
                        var current = null;
                        var bod = [];
                        var css = '@page { size: landscape; }',
                            head = win.document.head || win.document.getElementsByTagName('head')[0],
                            style = win.document.createElement('style');
        
                        style.type = 'text/css';
                        style.media = 'print';
        
                        if (style.styleSheet){
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