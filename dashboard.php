<?php

// Incluimos el header.php y components.php
$title = 'Panel de Producción';
require_once 'components/header.php';
require_once 'components/navbar.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'PRODUCCION', 'CONTROL');

if(!in_array($_SESSION['USUARIO']['CARGO'], $roles_permitidos)){
    require_once 'components/error.php';
    require_once 'components/footer.php';
    exit();
}

?>

<!-- Incluimos el sidebar.php -->
<?php require_once 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">

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
                    echo "<a href='$url_actual?id={$maquinaria['ID']}' role='button' class='btn btn-sm btn-main mr-1 mb-2'>" . mb_convert_case($maquinaria['NOMBRE'], MB_CASE_TITLE) . "</a>";
                } else {
                    echo "<a href='$url_actual?id={$maquinaria['ID']}' role='button' class='btn btn-sm btn-outline-dark mr-1 mb-2'>" . mb_convert_case($maquinaria['NOMBRE'], MB_CASE_TITLE) . "</a>";
                }

            }
            
            ?>
        </div>

        <div class="col-lg-6 px-2 mt-1">
            <form action="backend/api/maquinarias/color.php" method="POST">
                <div class="input-group">
                    <input type="hidden" name="id" value="<?= $maquinaria_id; ?>">
                    <select name="color" class="form-control dropdown-select2">

                        <?php

                        $sql = "SELECT * FROM COLOR;";
                        $result = db_query($sql);

                        foreach ($result as $row) {

                            if ($maquinaria_seleccionada[0]['COLOR'] == $row['COLOR']) {
                                echo "<option value='{$row['COLOR']}' selected>" . mb_convert_case($row['COLOR'], MB_CASE_TITLE, "UTF-8") . "</option>";
                            } else {
                                echo "<option value='{$row['COLOR']}'>" . mb_convert_case($row['COLOR'], MB_CASE_TITLE, "UTF-8") . "</option>";
                            }

                        }

                        ?>

                    </select>
                    <div class="input-group-append">
                        <button class="btn btn-sm btn-main" type="submit">Cambiar Color</button>
                    </div>
                </div>
            </form>
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

                        <a href='javascript:void(0)' onclick='marcarEmpaquetado({$produccion['ID']}, {$produccion['RESTANTE']})'>
                            <i class='fas fa-check-circle fa-casillero icon-color'></i>
                        </a>

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
                        <span class='font-weight-bold'>$index</span>";

        include 'components/tabla-dropdown.php';

        echo "</div>
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

    <!-- Modal de Asignar Referencia -->
    <div class="modal fade" id="asignarReferenciaModal" tabindex="-1" role="dialog" aria-labelledby="asignarReferenciaModal"
            aria-hidden="true">
        <div class="modal-dialog modal-md modal-dialog-centered">
            <div class="modal-content">
                <form action="backend/api/maquinarias/casillero.php?redir=cc" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fab fa-slack-hash icon-color"></i>
                            Asignar Referencia</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row justify-content-center">
                            <input type="hidden" name="casillero-id" id="idCasillero">
                            <input type="hidden" name="id" id="id-modal-referencia" value="<?= $maquinaria_id; ?>"> 
                            <input type="hidden" name="casillero-color" id="colorCasillero" value="<?= $maquinaria_seleccionada[0]['COLOR'] ?>">
                            <div class="form-group col-md-10">
                                <label for="inputReferenciaModal">Referencia</label>
                                <select name="suela-id" id="inputReferenciaModal" class="form-control dropdown-select2">

                                    <?php

                                    // Obtenemos el material.
                                    $material = $maquinaria_seleccionada[0]['MATERIAL'];
                                    
                                    // Si el material es EXPANSO/PVC obtenemos TODAS las suelas.
                                    if($material === 'EXPANSO/PVC'){
                                        $sql = "SELECT ID, REFERENCIA, MARCA, TALLA FROM SUELAS;";
                                        $result = db_query($sql);
                                    // De lo contrario solo seleccionamos la que corresponda al material.
                                    } else {
                                        $sql = "SELECT ID, REFERENCIA, MARCA, TALLA FROM SUELAS WHERE MATERIAL = ?;";
                                        $result = db_query($sql, array($material));
                                    }
                                    
                                    if($result == null){
                                        echo "<option>No hay Referencias</option>";
                                    } else {
                                        foreach ($result as $row) {
                                            echo "<option value='{$row['ID']}'>" . mb_convert_case($row['MARCA'], MB_CASE_TITLE, "UTF-8") . " - {$row['TALLA']}</option>";
                                        }
                                    }
                        
                                    ?>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-main">Asignar Referencia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Fin del Modal -->

    <!-- Modal de Intercambiar Referencia -->
    <div class="modal fade" id="intercambiarReferenciaModal" tabindex="-1" role="dialog" aria-labelledby="intercambiarReferenciaModal"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content">
                <form action="backend/api/maquinarias/swap.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fab fa-slack-hash icon-color"></i>
                            Intercambiar Referencias</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row justify-content-center">
                            <!-- Hidden Data -->
                            <input type="hidden" name="id-casillero-a" id="id-casillero-a-modal-swap">
                            <input type="hidden" name="maquinaria-id-a" value="<?= $maquinaria_id; ?>">
                            <!-- Rotativas -->
                            <div class="form-group col-md-6">
                                <label for="seleccionarCasilleroB" class="font-weight-bold text-center py-2">Seleccione la Maquina</label>
                                <select name="maquinaria-id-b" class="form-control dropdown-select2" id="seleccionarCasilleroB">
                                    <?php
                                    foreach ($maquinarias_activas as $maquinaria) {
                                        if ($maquinaria['MATERIAL'] == $maquinaria_seleccionada[0]['MATERIAL']) {
                                            if ($maquinaria['ID'] == $maquinaria_seleccionada[0]['ID']) {
                                                echo "<option selected value='{$maquinaria['ID']}'> " . mb_convert_case($maquinaria['NOMBRE'], MB_CASE_TITLE, 'UTF-8') . "</option>";
                                            } else {
                                                echo "<option value='{$maquinaria['ID']}'> " . mb_convert_case($maquinaria['NOMBRE'], MB_CASE_TITLE, 'UTF-8') . "</option>";
                                            }
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                        <h6 class="text-center font-weight-bold py-4">Seleccione el casillero</h6>
                        <div class="form-row justify-content-center">
                            <div class="btn-group-toggle" data-toggle="buttons" id="botonera-casilleros">
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="mr-auto">
                            <span class="badge badge-dark mr-1">Disponibles</span>
                            <span class="badge badge-danger mr-1">Deshabilitados</span>
                            <span class="badge badge-warning mr-1">Ocupado</span>
                            <span class="badge badge-main mr-1">Actual</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-main" id="submit-swap-modal">Intercambiar Referencia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Fin de Modal -->

</div>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>