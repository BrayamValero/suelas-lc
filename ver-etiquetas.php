<?php

// Incluimos el header.php y components.php
$title = 'Imprimir Etiquetas';
include 'components/header.php';
include 'components/navbar.php';
require_once 'backend/api/utils.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'DESPACHO');

if(!in_array($_SESSION['USUARIO']['CARGO'], $roles_permitidos)){
    include 'components/error.php';
    include_once 'components/footer.php';
    exit();
}

?>

<div class="text-center">
    
    <button class="btn btn-main hide-on-print my-4" type="button" onclick="imprimirDiv('areaImprimible')">Imprimir Etiquetas</button>

    <!-- Aquí se encuentra el área de impresión -->
    <div class="contenedorEtiquetas" id="areaImprimible">
        
        <div class="row">
    
        <?php 

        require_once "backend/api/db.php";
        $pedido_id = $_GET['id'];

        $sql = "SELECT PROD.PEDIDO_ID AS PEDIDO_ID, CLI.NOMBRE, SUE.MARCA AS MARCA, COL.COLOR AS COLOR, SUE.TALLA AS TALLA, PROD.CANTIDAD AS CANTIDAD, PROD.RESTANTE AS RESTANTE, PROD.STOCK AS STOCK, SUE.CAP_EMPAQUETADO AS CAP_EMPAQUETADO
        FROM PRODUCCION PROD
            LEFT JOIN SUELAS SUE
                ON PROD.SUELA_ID = SUE.ID
            LEFT JOIN COLOR COL
                ON PROD.COLOR_ID = COL.ID
            LEFT JOIN PEDIDOS PED
                ON PED.ID = ?
            LEFT JOIN CLIENTES CLI
                ON CLI.ID = PED.CLIENTE_ID
        WHERE PROD.PEDIDO_ID = ?;";
        $etiquetas = db_query($sql, array($pedido_id, $pedido_id));

        $count = 0;
        $pageBreak = 0;
        $output = '';

        foreach ($etiquetas as $key => $etiqueta) {
            
            $stock = $etiqueta['STOCK'];
            $cantidad = $etiqueta['CANTIDAD'];
            $restante = $etiqueta['RESTANTE'];
            $cap_empaquetado = $etiqueta['CAP_EMPAQUETADO'];
            $nombre = $etiqueta['NOMBRE'];
            if(strlen($nombre) > 32) $nombre = substr($nombre, 0, 32).'...';

            // Si no se usa stock durante el pedido se utiliza solo RESTANTE y CAP_EMPAQUETADO.
            if( $stock == 0 ){

                while ( $restante > $cap_empaquetado ) {
                
                    $count++;
                    $pageBreak++;

                    $output .= "
                    <div class='col-6 my-3'>
                        <div class='card etiqueta-card'>
                            <div class='etiqueta-top-fixed'>Paq $count</div>
                            <div class='centering'>
                                <div class='etiqueta-pedido'>PEDIDO {$etiqueta['PEDIDO_ID']}</div>
                                <h1 class='etiqueta-cliente'>$nombre</h1>
                                <h3 class='etiqueta-marca'>" . mb_convert_case($etiqueta['MARCA'], MB_CASE_TITLE, "UTF-8") . " " . mb_convert_case($etiqueta['TALLA'], MB_CASE_TITLE, "UTF-8") . "</h3>
                                <div class='etiqueta-numeracion'>
                                    <h1 class='text-dark font-weight-bold py-3 mb-0 etiqueta-borde etiqueta-cantidad'>
                                        {$etiqueta['TALLA']}
                                    </h1>
                                    <h1 class='text-dark font-weight-bold pt-3 mb-0 etiqueta-cantidad'>
                                        {$etiqueta['CAP_EMPAQUETADO']}
                                    </h1>
                                </div>
                            </div>
                            <div class='etiqueta-bottom-fixed'>
                                <span>Producción - " . $date = date('m/d/Y h:i a', time()) . "</span>
                            </div>
                        </div>  
                    </div>";

                    $restante -= $cap_empaquetado;

                    if($pageBreak == 4){
                        $output .= "</div><div class='pageBreak'></div><div class='row'>";
                        $pageBreak = 0;
                    }

                }

                if( $restante != 0 ){

                    $count++;
                    $pageBreak++;

                    $output .= "
                    <div class='col-6 my-3'>
                        <div class='card etiqueta-card'>
                            <div class='etiqueta-top-fixed'>Paq $count</div>
                            <div class='centering'>
                                <div class='etiqueta-pedido'>PEDIDO {$etiqueta['PEDIDO_ID']}</div>
                                <h1 class='etiqueta-cliente'>$nombre</h1>
                                <h3 class='etiqueta-marca'>" . mb_convert_case($etiqueta['MARCA'], MB_CASE_TITLE, "UTF-8") . " " . mb_convert_case($etiqueta['TALLA'], MB_CASE_TITLE, "UTF-8") . "</h3>
                                <div class='etiqueta-numeracion'>
                                    <h1 class='text-dark font-weight-bold py-3 mb-0 etiqueta-borde etiqueta-cantidad'>
                                        {$etiqueta['TALLA']}
                                    </h1>
                                    <h1 class='text-dark font-weight-bold pt-3 mb-0 etiqueta-cantidad'>
                                        $restante
                                    </h1>
                                </div>
                            </div>
                            <div class='etiqueta-bottom-fixed'>
                                <span>Producción - " . $date = date('m/d/Y h:i a', time()) . "</span>
                            </div>
                        </div>  
                    </div>";

                    if($pageBreak == 4){
                        $output .= "</div><div class='pageBreak'></div><div class='row'>";
                        $pageBreak = 0;
                    }

                }

            // De lo contrario, si hay stock, significa que se deben de hacer 2 etiquetas, unas provenientes de STOCK y otras provenientes de PRODUCCION.
            } else {

                if( $cantidad == ($restante + $stock) ){

                    while ( $stock > $cap_empaquetado ) {

                        $count++;
                        $pageBreak++;

                        $output .= "
                        <div class='col-6 my-3'>
                            <div class='card etiqueta-card'>
                                <div class='etiqueta-top-fixed'>Paq $count</div>
                                <div class='centering'>
                                    <div class='etiqueta-pedido'>PEDIDO {$etiqueta['PEDIDO_ID']}</div>
                                    <h1 class='etiqueta-cliente'>$nombre</h1>
                                    <h3 class='etiqueta-marca'>" . mb_convert_case($etiqueta['MARCA'], MB_CASE_TITLE, "UTF-8") . " " . mb_convert_case($etiqueta['TALLA'], MB_CASE_TITLE, "UTF-8") . "</h3>
                                    <div class='etiqueta-numeracion'>
                                        <h1 class='text-dark font-weight-bold py-3 mb-0 etiqueta-borde etiqueta-cantidad'>
                                            {$etiqueta['TALLA']}
                                        </h1>
                                        <h1 class='text-dark font-weight-bold pt-3 mb-0 etiqueta-cantidad'>
                                            {$etiqueta['CAP_EMPAQUETADO']}
                                        </h1>
                                    </div>
                                </div>
                                <div class='etiqueta-bottom-fixed'>
                                    <span>Producción - " . $date = date('m/d/Y h:i a', time()) . "</span>
                                </div>
                            </div>  
                        </div>";
        
                        $stock -= $cap_empaquetado;

                        if($pageBreak == 4){
                            $output .= "</div><div class='pageBreak'></div><div class='row'>";
                            $pageBreak = 0;
                        }
        
                    }
        
                    if( $stock != 0 ){

                        $count++;
                        $pageBreak++;

                        $output .= "
                        <div class='col-6 my-3'>
                            <div class='card etiqueta-card'>
                                <div class='etiqueta-top-fixed'>Paq $count</div>
                                <div class='centering'>
                                    <div class='etiqueta-pedido'>PEDIDO {$etiqueta['PEDIDO_ID']}</div>
                                    <h1 class='etiqueta-cliente'>$nombre</h1>
                                    <h3 class='etiqueta-marca'>" . mb_convert_case($etiqueta['MARCA'], MB_CASE_TITLE, "UTF-8") . " " . mb_convert_case($etiqueta['TALLA'], MB_CASE_TITLE, "UTF-8") . "</h3>
                                    <div class='etiqueta-numeracion'>
                                        <h1 class='text-dark font-weight-bold py-3 mb-0 etiqueta-borde etiqueta-cantidad'>
                                            {$etiqueta['TALLA']}
                                        </h1>
                                        <h1 class='text-dark font-weight-bold pt-3 mb-0 etiqueta-cantidad'>
                                            $stock
                                        </h1>
                                    </div>
                                </div>
                                <div class='etiqueta-bottom-fixed'>
                                    <span>Producción - " . $date = date('m/d/Y h:i a', time()) . "</span>
                                </div>
                            </div>  
                        </div>";

                        if($pageBreak == 4){
                            $output .= "</div><div class='pageBreak'></div><div class='row'>";
                            $pageBreak = 0;
                        }
        
                    }

                }

                while ( $restante > $cap_empaquetado ) {

                    $count++;
                    $pageBreak++;

                    $output .= "
                    <div class='col-6 my-3'>
                        <div class='card etiqueta-card'>
                            <div class='etiqueta-top-fixed'>Paq $count</div>
                            <div class='centering'>
                                <div class='etiqueta-pedido'>PEDIDO {$etiqueta['PEDIDO_ID']}</div>
                                <h1 class='etiqueta-cliente'>$nombre</h1>
                                <h3 class='etiqueta-marca'>" . mb_convert_case($etiqueta['MARCA'], MB_CASE_TITLE, "UTF-8") . " " . mb_convert_case($etiqueta['TALLA'], MB_CASE_TITLE, "UTF-8") . "</h3>
                                <div class='etiqueta-numeracion'>
                                    <h1 class='text-dark font-weight-bold py-3 mb-0 etiqueta-borde etiqueta-cantidad'>
                                        {$etiqueta['TALLA']}
                                    </h1>
                                    <h1 class='text-dark font-weight-bold pt-3 mb-0 etiqueta-cantidad'>
                                        {$etiqueta['CAP_EMPAQUETADO']}
                                    </h1>
                                </div>
                            </div>
                            <div class='etiqueta-bottom-fixed'>
                                <span>Producción - " . $date = date('m/d/Y h:i a', time()) . "</span>
                            </div>
                        </div>  
                    </div>";

                    $restante -= $cap_empaquetado;
                                    
                    if($pageBreak == 4){
                        $output .= "</div><div class='pageBreak'></div><div class='row'>";
                        $pageBreak = 0;
                    }

                }

                if( $restante != 0 ){

                    $count++;
                    $pageBreak++;

                    $output .= "
                    <div class='col-6 my-3'>
                        <div class='card etiqueta-card'>
                            <div class='etiqueta-top-fixed'>Paq $count</div>
                            <div class='centering'>
                                <div class='etiqueta-pedido'>PEDIDO {$etiqueta['PEDIDO_ID']}</div>
                                <h1 class='etiqueta-cliente'>$nombre</h1>
                                <h3 class='etiqueta-marca'>" . mb_convert_case($etiqueta['MARCA'], MB_CASE_TITLE, "UTF-8") . " " . mb_convert_case($etiqueta['TALLA'], MB_CASE_TITLE, "UTF-8") . "</h3>
                                <div class='etiqueta-numeracion'>
                                    <h1 class='text-dark font-weight-bold py-3 mb-0 etiqueta-borde etiqueta-cantidad'>
                                        {$etiqueta['TALLA']}
                                    </h1>
                                    <h1 class='text-dark font-weight-bold pt-3 mb-0 etiqueta-cantidad'>
                                        $restante
                                    </h1>
                                </div>
                            </div>
                            <div class='etiqueta-bottom-fixed'>
                                <span>Producción - " . $date = date('m/d/Y h:i a', time()) . "</span>
                            </div>
                        </div>  
                    </div>";

                    if($pageBreak == 4){
                        $output .= "</div><div class='pageBreak'></div><div class='row'>";
                        $pageBreak = 0;
                    }

                }

            }
        
        }

        echo $output;

        echo "</div>";

        ?>

    </div>

</div>

<script>

function imprimirDiv(divName) {
    var printContents = document.getElementById(divName).innerHTML;
    window.print();
}

</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>