<?php

// Incluimos el header.php y components.php
$title = 'Panel de Producción';
require_once 'components/header.php';
require_once 'components/navbar.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'PRODUCCION', 'CONTROL');

if(!in_array($_SESSION['ROL'], $roles_permitidos)){
    require_once 'components/error.php';
    require_once 'components/footer.php';
    exit();
}

?>

<!-- Incluimos el sidebar.php -->
<?php require_once 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido" class="contenido-fixed">

    <?php

    // 1. Seleccionamos las máquinas activas.
    $sql = "SELECT * FROM MAQUINARIAS WHERE ESTADO = 'ACTIVO';";
    $maquinarias_activas = db_query($sql);

    // 2. Si hay al menos una máquina activa, permitimos mostrar toda la vista de las tablas de producción y demás.
    if( !empty($maquinarias_activas) ):
        
        // 3. Ahora, comprobamos que esté el ID setteado en la URL, en caso de no ser así, seleccionamos la primera que se encuentre entre las MAQUINAS ACTIVAS.
        if ( !isset($_GET['id']) ) {
            $sql = "SELECT MIN(ID) AS ID FROM MAQUINARIAS WHERE ESTADO = 'ACTIVO';";
            $maquinaria_id = db_query($sql)[0]['ID'];
        } else {
            $maquinaria_id = $_GET['id'];
        }

        // 4. Ya despues de verificar en la URL, buscamos la máquina seleccionada.
        $sql = "SELECT * FROM MAQUINARIAS WHERE ID = ?;";
        $maquinaria_seleccionada = db_query($sql, array($maquinaria_id));

    ?>

    <!-- Navbar contenedora de las maquinarias ACTIVAS -->
    <div class="row mb-4">
        <!-- Botones Maquinas -->
        <div class="col-lg-12 px-2">
            <h6 class="text-title">Control de Calidad</h6>
            <button type="button"class="align-middle sidebarCollapse btn btn-sm btn-main mb-2">
                <i class="fas fa-bars"></i>
            </button>
            
            <?php

            $url_actual = basename($_SERVER["PHP_SELF"]);

            // 4. Populamos el navbar de las MAQUINARIAS ACTIVAS.
            foreach ($maquinarias_activas as $maquinaria) {

                if ($maquinaria['ID'] == $maquinaria_id) {
                    echo "<a href='$url_actual?id={$maquinaria['ID']}' role='button' class='btn btn-sm btn-main mr-1 mb-2 font-weight-bold'>" . mb_convert_case($maquinaria['NOMBRE'], MB_CASE_TITLE) . "</a>";
                } else {
                    echo "<a href='$url_actual?id={$maquinaria['ID']}' role='button' class='btn btn-sm btn-outline-dark mr-1 mb-2'>" . mb_convert_case($maquinaria['NOMBRE'], MB_CASE_TITLE) . "</a>";
                }

            }
            
            ?>
        </div>

        <div class="col-lg-6 px-2 mt-1">
            <input type="text" class="form-control font-weight-bold" value="<?=  mb_convert_case($maquinaria_seleccionada[0]['COLOR'], MB_CASE_TITLE) ?>" readonly>
        </div>

    </div>

    <?php

    // 5. Obtenemos la producción que se cumpla con los siguientes requisitos
    // -- ESTADO => PENDIENTE
    // -- COLOR_ID => MAQ_COLOR_ID
    // -- ORDER_BY => CREATED_AT

    $sql = "SELECT ID FROM COLOR WHERE COLOR = ?;";
    $maquinaria_color = db_query($sql, array($maquinaria_seleccionada[0]['COLOR']));
    
    // Producción Actual
    $sql = "SELECT P.*, 
            S.ID AS SUELA_ID,
            S.REFERENCIA AS SUELA_REFERENCIA,
            S.MARCA AS SUELA_MARCA,
            S.TALLA AS SUELA_TALLA,
            S.PESO_IDEAL AS SUELA_PESO_IDEAL,
            S.PESO_MAQUINA AS SUELA_PESO_MAQUINA
                FROM PRODUCCION P 
                    JOIN SUELAS S ON P.SUELA_ID = S.ID 
                        WHERE P.ESTADO = 'PENDIENTE' AND P.COLOR_ID = ? ORDER BY CREATED_AT ASC;";

    $produccion_actual = db_query($sql, array($maquinaria_color[0]['ID']));

    // Agrupados
    $sql = "SELECT S.ID,
            SUM(P.CANTIDAD) AS TOTAL_PRODUCIR
                FROM PRODUCCION P 
                    JOIN SUELAS S ON P.SUELA_ID = S.ID 
                        WHERE P.ESTADO = 'PENDIENTE' AND P.COLOR_ID = ? GROUP BY S.ID";

    $produccion_agrupada = db_query($sql, array($maquinaria_color[0]['ID']));

    $sql = "SELECT * FROM CASILLEROS WHERE MAQUINARIA_ID = ?;";
    $casilleros = db_query($sql, array($maquinaria_id));

    ?>

    <!-- Inicio del .row -->
    <div class="row text-center">

    <?php
    
    $output = '';
    $repetidos = [];

    for ( $i = 0; $i < $maquinaria_seleccionada[0]['CASILLEROS'] ; $i++) {
      
        $index = $i + 1;
          
        $casillero_impreso = false;

        foreach ($produccion_actual as $produccion) {

            if (!in_array($produccion['SUELA_ID'], $repetidos)) {

                if ($casilleros[$i]['SUELA_ID'] == $produccion['SUELA_ID']) {
                    
                    foreach ($produccion_agrupada as $agrupado) {

                        if ($agrupado['ID'] == $produccion['SUELA_ID']) {

                            $resultado = "<div class='my-2'> {$agrupado['TOTAL_PRODUCIR']}</div>";

                        }

                    }
                    
                    $output .= "
                    <div class='casillero-content'>
                        <div class='badge badge-main badge-casillero mb-2'>Pedido {$produccion['PEDIDO_ID']}</div>
                        
                        <div class='font-weight-bold'>{$produccion['SUELA_MARCA']} {$produccion['SUELA_TALLA']}</div>

                        $resultado

                    </div>";

                    array_push($repetidos, $produccion['SUELA_ID']);

                    $casillero_impreso = true;

                }

            }

        }
        
        if ($casillero_impreso == false) {

            if ($casilleros[$i]['SUELA_ID'] != '') {

                $sql = "SELECT * FROM SUELAS WHERE ID = ?;";
                $marca = db_query($sql, array($casilleros[$i]['SUELA_ID']))[0]['MARCA'];
                $talla = db_query($sql, array($casilleros[$i]['SUELA_ID']))[0]['TALLA'];

                $output .= "<div class='casillero-content'>
                                <div class='badge badge-main badge-casillero mb-2'>Referencia</div>
                                <p class='font-weight-bold p-0'>$marca $talla</p>
                            </div>";

            } elseif ($casilleros[$i]['ACTIVO'] == 0) {

                $output .= "<div class='casillero-content'>
                                <i class='fas fa-ban fa-casillero text-danger'></i>
                            </div>";

            } else {

                $output .= "<div class='casillero-content'>
                                <div class='badge badge-success badge-casillero'>Vacio</div>
                            </div>";

            }
            
        }    

        echo "<div class='col-lg-2 col-md-3 col-sm-4 px-2 numero-casillero'>
                <div class='casillero shadow mb-3'>
                    <div class='casillero-header' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>
                        <span class='font-weight-bold'>$index</span>
                    </div>
                    $output
                </div>
            </div>";

        $output = '';

    }

    ?>

    <!-- Fin del .row -->
    </div>

    <!-- En caso de que no haya MAQUINARIA ACTIVA, mostrar el siguiente mensaje. -->
    <?php else: ?>
        <div class="container mt-4 text-center">
            <i class="fas fa-cog fa-5x icon-color mb-2"></i>
            <h4 class="font-weight-bold">¡Whoops!</h4>
            <p class="mb-2">No se encuentran máquinas activas o puede que no estén creadas aun.</p>
            <small class="text-secondary">Prueba activando una máquina.</small>
        </div>
    <?php endif; ?>

</div>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>