<?php

// Incluimos el header.php y components.php
$title = 'Series';
include 'components/header.php';
include 'components/components.php';
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

<?php
    require_once "backend/api/db.php";
    $id = $_GET['id'];
?>

<div class="container-fluid text-center">
    <button class="btn btn-main hide-on-print my-4" type="button" onclick="printDiv('printableArea')">Imprimir Etiquetas</button>
    <div class="row contenedorEtiquetas" id="printableArea"></div>
</div>

<script>

const id = <?= $id ?>;

$.ajax({
    type: 'post',
    url: 'backend/api/utils.php?fun=obtenerPedidosParaEmpaquetar',
    data: 'pedido_id=' + id,
    async: false,
    success: function (data) {

        const result = JSON.parse(data);

        console.log(result.length);

        result.forEach(row => {
            
            let cantidad =  parseInt(row.CANTIDAD);
            let capacidad = parseInt(row.CAP_EMPAQUETADO);

            while (cantidad > capacidad) {
                
                $('.contenedorEtiquetas').append(`
                <div class="col-6 mb-4">
                    <div class="card">
                        <div class="text-center pt-4 pb-2">
                            <div class="btn btn-lg btn-dark mb-2">PEDIDO ${row.PEDIDO_ID}</div>
                            <h1 class="font-weight-bold">${row.NOMBRE.toUpperCase()}</h1>
                            <h3>${row.MARCA.toProperCase()} ${row.COLOR.toProperCase()}</h3>
                            <div class="mt-3">
                                <h1 class="text-dark font-weight-bold py-3 mb-0 border-bottom" style="font-size: 85px;">${row.TALLA}</h1>
                                <h1 class="text-dark font-weight-bold pt-3 mb-0" style="font-size: 85px;">${capacidad}</h1>
                            </div>
                        </div>
                    </div>  
                </div>
                `);

                cantidad = cantidad - capacidad;

            }

            $('.contenedorEtiquetas').append(`
            <div class="col-6 mb-4">
                <div class="card">
                    <div class="text-center pt-4 pb-2">
                        <div class="btn btn-lg btn-dark mb-2">PEDIDO ${row.PEDIDO_ID}</div>
                        <h1 class="font-weight-bold">${row.NOMBRE.toUpperCase()}</h1>
                        <h3>${row.MARCA.toProperCase()} ${row.COLOR.toProperCase()}</h3>
                        <div class="mt-3">
                            <h1 class="text-dark font-weight-bold py-3 mb-0 border-bottom" style="font-size: 85px;">${row.TALLA}</h1>
                            <h1 class="text-dark font-weight-bold pt-3 mb-0" style="font-size: 85px;">${cantidad}</h1>
                        </div>
                    </div>
                </div>  
            </div>
            `);

        });

    }
});


function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    window.print();
}


</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>