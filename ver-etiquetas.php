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

<button class="btn btn-main hide-on-print my-4" type="button" onclick="printDiv('printableArea')">Imprimir Etiquetas</button>

<div style="display:block !important;" class="contenedorEtiquetas" id="printableArea"></div>


<script>

const id = <?= $id ?>;

$.ajax({
    type: 'post',
    url: 'backend/api/utils.php?fun=obtenerPedidosParaEmpaquetar',
    data: 'pedido_id=' + id,
    async: false,
    success: function (data) {

        const result = JSON.parse(data);

        let count = 0;
        let pageBreak = 0;

        for (let index = 0; index < result.length; index++) {

            result.forEach(row => {
            
                let cantidad =  parseInt(row.CANTIDAD);
                let capacidad = parseInt(row.CAP_EMPAQUETADO);

                while (cantidad > capacidad) {

                    count++;
                    
                    $('.contenedorEtiquetas').append(`
                    <div class="col-5 mb-4">
                        <div class="card">
                            <div class="text-center pt-4 pb-2">
                                <p>Paquete ${count}</p>
                                <div class="btn btn-lg btn-dark mb-2">PEDIDO ${row.PEDIDO_ID}</div>
                                <h1 class="font-weight-bold">${row.NOMBRE.toUpperCase()}</h1>
                                <h3>${row.MARCA.toProperCase()} ${row.COLOR.toProperCase()}</h3>
                                <div class="mt-3">
                                    <h1 class="text-dark font-weight-bold py-3 mb-0 borde-etiqueta" style="font-size: 100px;">${row.TALLA}</h1>
                                    <h1 class="text-dark font-weight-bold pt-3 mb-0" style="font-size: 120px;">${capacidad}</h1>
                                </div>
                            </div>
                        </div>  
                    </div>
                    `);

                    cantidad = cantidad - capacidad;

                    if(pageBreak == 1){
                        $('.contenedorEtiquetas').append(`
                            <div class="page-break"></div> 
                        `);
                        pageBreak = 0;
                    } else {
                        pageBreak ++;
                    }

                    console.log(pageBreak);

                }

                count++;

                $('.contenedorEtiquetas').append(`
                <div class="col-5 mb-4">
                    <div class="card">
                        <div class="text-center pt-4 pb-2">
                            <p>Paquete ${count}</p>
                            <div class="btn btn-lg btn-dark mb-2">PEDIDO ${row.PEDIDO_ID}</div>
                            <h1 class="font-weight-bold">${row.NOMBRE.toUpperCase()}</h1>
                            <h3>${row.MARCA.toProperCase()} ${row.COLOR.toProperCase()}</h3>
                            <div class="mt-3">
                                <h1 class="text-dark font-weight-bold py-3 mb-0 borde-etiqueta" style="font-size: 100px;">${row.TALLA}</h1>
                                <h1 class="text-dark font-weight-bold pt-3 mb-0" style="font-size: 120px;">${cantidad}</h1>
                            </div>
                        </div>
                    </div>  
                </div>
                `);

                if(pageBreak == 1){
                    $('.contenedorEtiquetas').append(`
                        <div class="page-break"></div> 
                    `);
                    pageBreak = 0;
                } else {
                    pageBreak ++;
                }

                console.log(pageBreak);
                
            });

        }

    }
    
});


function printDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    window.print();
}


</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>