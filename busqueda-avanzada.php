<?php

// Incluimos el header.php y components.php
$title = 'Busqueda Avanzada';
require_once 'components/header.php';
require_once 'components/navbar.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS', 'DESPACHO', 'PRODUCCION');

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
    <?php get_navbar('Ventas', 'Busqueda Avanzada'); ?>

    <label for="verClientes">Nombre</label>
    <select id="verClientes" name="cliente" class="form-control dropdown-select2 cambiarBusqueda" required>
    
    <?php 

    $sql = "SELECT DISTINCT CLI.ID, CLI.NOMBRE, CLI.DOCUMENTO, CLI.DOCUMENTO_NRO FROM PEDIDOS PED JOIN CLIENTES CLI ON CLI.ID = PED.CLIENTE_ID;";
    $clientes = db_query($sql);

    foreach ($clientes as $cliente) {
        echo "<option value='{$cliente['ID']}'>" . mb_convert_case($cliente['NOMBRE'], MB_CASE_TITLE, "UTF-8") . " - {$cliente['DOCUMENTO']} - {$cliente['DOCUMENTO_NRO']}</option>";
    }

    if(empty($clientes)){
        echo "<option value=''>No hay clientes disponibles.</option>";
    }

    ?>

    </select>

    <!-- Mostramos la tabla con la información correspondiente -->
    <div class="table-responsive text-center mt-3" style="width:100%">
        <div id="spinner" class="spinner-border text-danger text-center mt-5" role="status">
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

// Variables y constantes.
var tabla;
var cliente_id = document.getElementById('verClientes').value;

if(cliente_id){

    $.ajax({
        type: 'post',
        url: 'backend/api/pedidos/busqueda-avanzada.php',
        data: 'cliente=' + cliente_id,
        success: function (data) {

            const resultado = JSON.parse(data);
            console.log(resultado);

            tabla = $('#tabla').DataTable({
                "initComplete": function(settings, json) {
                    $("#spinner").css('display', 'none');
                },
                "processing": true,
                "info": false,
                "dom": "lrtip",
                "pageLength": 10,
                "lengthChange": false,
                "order": [[0, 'desc']],
                "data": resultado,
                "columns": [
                    { data: "PEDIDO_ID", title: "Pedido" },
                    { data: "MARCA", title: "Marca", 
                        render: function(value, type, row) {
                            return row.MARCA.toProperCase();
                        }
                    },
                    { data: "TALLA", title: "Talla" },
                    { data: "COLOR", title: "Color", 
                        render: function(value, type, row) {
                            return row.COLOR.toProperCase();
                        }
                    },
                    { data: "RESTANTE", title: "Restante" },
                    { data: "ESTADO", title: "Estado", 
                        render: function(value, type, row) {
                            return row.ESTADO.toProperCase();
                        }
                    }
                ],
                "columnDefs": [{
                    searchable: true,
                    orderable: true,
                    className: "align-middle", "targets": "_all"
                }],
                "language": {
                    "url": "datatables/Spanish.json"
                }
            });

            // DATATABLES => Paginación
            $.fn.DataTable.ext.pager.numbers_length = 5;    

        }

    });

} else {

    console.log("No hay resultados disponibles.");

}

// CAMBIAR => Cambiando el cliente.
$(document).on( 'change', '.cambiarBusqueda', function () {
  
    let cliente_id = $(this).val();

    $.ajax({
        type: 'post',
        url: 'backend/api/pedidos/busqueda-avanzada.php',
        data: 'cliente=' + cliente_id,
        async: false,
        dataType: 'json',
        success: function (data) {

            // Limpiando la data de la tabla.
            tabla.clear().draw();

            // Datatables => Añadiendo el elemento al frontend.
            data.forEach(elem => {

                tabla.row.add({
                    "PEDIDO_ID":        elem.PEDIDO_ID,
                    "MARCA":            elem.MARCA,
                    "TALLA":            elem.TALLA,
                    "COLOR":            elem.COLOR,
                    "RESTANTE":         elem.RESTANTE,
                    "ESTADO":           elem.ESTADO
                });

            });

            tabla.draw();

        }

    });

});

</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>