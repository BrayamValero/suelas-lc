<?php
session_start();
require_once "../db.php";
// echo '<pre>'; print_r($_POST); echo '</pre>';

// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $pedido_id = $_POST['pedido_id'];

    if(isset($pedido_id)){

        $output = '';

        // 1. Primero buscamos el PEDIDO dependiendo del ID dado.
        $sql = "SELECT ID AS PROD_ID, SUELA_ID, SERIE_ID, COLOR_ID, CANTIDAD, URGENTE FROM PRODUCCION WHERE PEDIDO_ID = ?;";
        $datosPedido = db_query($sql, array($pedido_id));

        // 2. Ahora filtramos las SERIE_ID y COLOR_ID.
        $sql = "SELECT SERIE_ID, COLOR_ID FROM PRODUCCION WHERE PEDIDO_ID = ?;";
        $datosSeries = db_query($sql, array($pedido_id));
        $datosSeries = array_unique($datosSeries, SORT_REGULAR);

       
        // Primer Bucle => Datos de las SERIES.
        foreach ($datosSeries as $serie) {

            // Se declara deontro del bucle para evitar datos duplicados.
            $append = '';

            $serie_id = $serie['SERIE_ID'];
            $color_id = $serie['COLOR_ID'];

            // Obtenemos los GRUPO_SERIES.
            $sql = "SELECT SUE.ID AS SUELA_ID, SUE.MARCA, SUE.TALLA FROM GRUPO_SERIES GS LEFT JOIN SUELAS SUE ON GS.SUELA_ID = SUE.ID WHERE GS.SERIE_ID = ?;";
            $grupo_series = db_query($sql, array($serie_id));

            // Obtenemos el COLOR.
            $sql = "SELECT COLOR, CODIGO FROM COLOR WHERE ID = ?;";
            $color = db_query($sql, array($color_id));

            $red = hexdec(substr($color[0]['CODIGO'], 1, 2));
            $green = hexdec(substr($color[0]['CODIGO'], 3, -2));
            $blue = hexdec(substr($color[0]['CODIGO'], 5, 6));

            $colorHex = $red * 0.299 + $green * 0.587 + $blue * 0.114 > 186 ? '#000000' : '#FFFFFF';

            // Segundo Bucle => Datos de las REFERENCIAS que van dentro de las series.
            foreach ($grupo_series as $referencia) {

                if(!isset($cantidad)){
                    $cantidad = '<i class="fas fa-ban text-danger"></i>';
                }
                
                $suela_id = $referencia['SUELA_ID'];
                $marca = $referencia['MARCA'];
                $talla = $referencia['TALLA'];

                // Tercer Bucle => Obtenemos la cantidad correspondiente al pedido.
                foreach ($datosPedido as $pedido) {
                    if($suela_id == $pedido['SUELA_ID'] && $color_id == $pedido['COLOR_ID']){
                        $cantidad = $pedido['CANTIDAD'];
                    }
                }

                $append .= "
                    <div class='form-group col mb-0'>
                        <label class='label-cantidades' for='cantidades'>$talla</label>
                        <div class='form-control input-cantidades'>$cantidad</div>
                    </div>";

                $cantidad = '<i class="fas fa-ban text-danger"></i>';

            }

            $output .= "
            <div class='contenedor-serie shadow-sm'>
                <div class='form-row mb-2'>
                    <div class='col'>
                        <strong>" . mb_convert_case($grupo_series[0]['MARCA'], MB_CASE_TITLE) . "</strong>
                        <span class='badge border' style='color: $colorHex; background: {$color[0]['CODIGO']};'>" . mb_convert_case($color[0]['COLOR'], MB_CASE_TITLE) . "</span>
                        <small class='text-muted'>{$grupo_series[0]['TALLA']} al " . end($grupo_series)['TALLA'] . "</small>
                    </div>
                </div>
                <div class='form-row text-center'>$append</div>
            </div>";

        }

        echo $output;

    }

}