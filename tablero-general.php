<?php

// Incluimos el header.php y components.php
$title = 'Tablero General';
include 'components/header.php';
include 'components/components.php';
require_once 'backend/api/utils.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS', 'PRODUCCION');

if(!in_array($_SESSION['USUARIO']['CARGO'], $roles_permitidos)){
    include 'components/error.php';
    include_once 'components/footer.php';
    exit();
}

// Chequeamos la capacidad total.
$sql = "SELECT SUM(CAPACIDAD) AS CAPACIDAD_TOTAL FROM MAQUINARIAS WHERE ESTADO = 'ACTIVO';";
$capacidad_total = db_query($sql);

if (is_null($capacidad_total[0]['CAPACIDAD_TOTAL'])) {
    $capacidad_total[0]['CAPACIDAD_TOTAL'] = 0;
}

?>

<!-- Incluimos el sidebar.php -->
<?php include 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">
        
    <!-- Header Content -->
    <div class="header-body py-3">
        <div class="row align-items-end">
            <div class="col">
                <h6 class="text-title">Tablero General</h6>
                
                <?php
                $sql = "SELECT * FROM MAQUINARIAS WHERE ESTADO = 'ACTIVO';";
                $maquinarias = db_query($sql);
                $first = false;

                if (!isset($_GET['id'])) {
                    $sql = "SELECT MIN(ID) AS ID FROM MAQUINARIAS WHERE ESTADO = 'ACTIVO';";
                    $id = db_query($sql)[0]['ID'];
                } else {
                    $id = $_GET['id'];
                }
                ?>

                <?php
                if (!empty($maquinarias)):
                $sql = "SELECT * FROM MAQUINARIAS WHERE ID = ?;";
                $maquinaria_selected = db_query($sql, array($id));
                ?>

                <div class="row">
                    <div class="col">
                        <button type="button" class="align-middle sidebarCollapse btn btn-main btn-sm mb-2">
                            <i class="fas fa-bars"></i>
                        </button>
                        <!-- Rotativas -->
                        <?php
                        foreach ($maquinarias as $maquinaria) {
                            if ($maquinaria['ID'] == $id) {
                                echo "<a href='tablero-general.php?id={$maquinaria['ID']}' class='btn btn-main btn-sm mr-1 mb-2'>" . mb_convert_case($maquinaria['NOMBRE'], MB_CASE_TITLE, "UTF-8") . "</a>";
                                $first = true;
                            } else {
                                echo "<a href='tablero-general.php?id={$maquinaria['ID']}' class='btn btn-sm btn-outline-dark mr-1 mb-2'>" . mb_convert_case($maquinaria['NOMBRE'], MB_CASE_TITLE, "UTF-8") . "</a>";
                            }
                        }
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <!-- End of Header Content -->

    <!-- Table -->

    <?php
    $sql = "SELECT ID FROM COLOR WHERE COLOR = ?;";
    $color = db_query($sql, array($maquinaria_selected[0]['COLOR']));

    $sql = "SELECT S.REFERENCIA AS SUELA_REFERENCIA, S.ID AS SUELA_ID, S.MARCA AS MARCA, S.TALLA AS TALLA FROM PRODUCCION P JOIN SUELAS S ON P.SUELA_ID = S.ID WHERE P.ESTADO = 'PENDIENTE' AND P.COLOR_ID = ?;";
    $result = db_query($sql, array($color[0]['ID']));

    $sql = "SELECT S.ID, SUM(P.CANTIDAD) AS TOTAL_PRODUCIR FROM PRODUCCION P JOIN SUELAS S ON P.SUELA_ID = S.ID WHERE P.ESTADO = 'PENDIENTE' AND P.COLOR_ID = ? GROUP BY S.ID";
    $agrupados = db_query($sql, array($color[0]['ID']));

    $sql = "SELECT * FROM CASILLEROS WHERE MAQUINARIA_ID = ?;";
    $casilleros = db_query($sql, array($id));

    $repetidos = array();
    ?>

    <div class="table-responsive text-center">
        <table class="table table-bordered text-center">
            <!-- First Row -->
            <thead class="thead-dark">
            <tr>
                <?php
                if ($maquinaria_selected[0]['CASILLEROS'] == 1) {
                    for ($i = 0; $i < 1; $i++) {
                        echo "<th scope='col' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                        echo $i + 1;
                        echo "</th>";
                    }
                } elseif ($maquinaria_selected[0]['CASILLEROS'] == 5) {
                    for ($i = 0; $i < 5; $i++) {
                        echo "<th scope='col' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                        echo $i + 1;
                        echo "</th>";
                    }
                } elseif ($maquinaria_selected[0]['CASILLEROS'] == 10) {
                    for ($i = 0; $i < 5; $i++) {
                        echo "<th scope='col' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                        echo $i + 1;
                        echo "</th>";
                    }
                } elseif ($maquinaria_selected[0]['CASILLEROS'] == 20) {
                    for ($i = 0; $i < 10; $i++) {
                        echo "<th scope='col' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                        echo $i + 1;
                        echo "</th>";
                    }
                } elseif ($maquinaria_selected[0]['CASILLEROS'] == 30) {
                    for ($i = 0; $i < 10; $i++) {
                        echo "<th scope='col' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                        echo $i + 1;
                        echo "</th>";
                    }
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <tr>
                <?php
                foreach ($casilleros as $index => $casillero) {
                    $casillero_impreso = false;

                    // Primera hilera
                    if ($casillero['NUMERO'] <= 10) {
                        foreach ($result as $row) {
                            if (!in_array($row['SUELA_ID'], $repetidos)) {
                                if ($casillero['SUELA_ID'] == $row['SUELA_ID']) {

                                    echo "<td>";
        
                                    echo "<div class='badge badge-main my-2'>Pedidos</div>";
                                    echo "<div class='font-weight-bold'>" . mb_convert_case($row['MARCA'], MB_CASE_TITLE, 'UTF-8') . " " . $row['TALLA'] . "</div>";

                                    foreach ($agrupados as $agrupado) {
                                        if ($agrupado['ID'] == $row['SUELA_ID']) {
                                            echo "<div class='my-2'> {$agrupado['TOTAL_PRODUCIR']} Pares</div>";
                                        }
                                    }

                                    echo "</td>";

                                    array_push($repetidos, $row['SUELA_ID']);
                                    $casillero_impreso = true;
                                    
                                }
                            }
                        }

                        if ($casillero_impreso == false) {
                            if ($casillero['SUELA_ID'] != '') {
                                $sql = "SELECT * FROM SUELAS WHERE ID = ?;";

                                $marca = db_query($sql, array($casillero['SUELA_ID']))[0]['MARCA'];
                                $talla = db_query($sql, array($casillero['SUELA_ID']))[0]['TALLA'];

                                echo "<td class='align-middle'><div class='badge badge-main mb-2'>Referencia</div><div class='font-weight-bold'>" . mb_convert_case($marca, MB_CASE_TITLE, 'UTF-8') . " " . $talla . "</div></td>";
                            } elseif ($casillero['ACTIVO'] == 0) {
                                echo "<td class='align-middle'><i class='fas fa-ban text-danger my-2'></i></td>";
                            } else {
                                echo "<td class='align-middle'><div class='badge badge-success my-2'>Vacio</div></td>";
                            }
                        }
                        
                    }
                }
                ?>

            </tr>
            </tbody>
            <!-- Second Row -->
            <thead class="thead-dark">
            <tr>
                <?php
                if ($maquinaria_selected[0]['CASILLEROS'] == 20) {
                    for ($i = 10; $i < 20; $i++) {
                        echo "<th scope='col' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                        echo $i + 1;
                        echo "</th>";
                    }
                } elseif ($maquinaria_selected[0]['CASILLEROS'] == 30) {
                    for ($i = 10; $i < 20; $i++) {
                        echo "<th scope='col' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                        echo $i + 1;
                        echo "</th>";
                    }
                }
                ?>
            </tr>
            </thead>
            <tbody>
            <tr>
                <?php
                foreach ($casilleros as $index => $casillero) {
                    $casillero_impreso = false;

                    // Segunda hilera
                    if ($casillero['NUMERO'] > 10 && $casillero['NUMERO'] <= 20) {
                        foreach ($result as $row) {
                            if (!in_array($row['SUELA_ID'], $repetidos)) {
                                if ($casillero['SUELA_ID'] == $row['SUELA_ID']) {

                                    echo "<td>";
        
                                    echo "<div class='badge badge-main my-2'>Pedidos</div>";
                                    echo "<div class='font-weight-bold'>" . mb_convert_case($row['MARCA'], MB_CASE_TITLE, 'UTF-8') . " " . $row['TALLA'] . "</div>";

                                    foreach ($agrupados as $agrupado) {
                                        if ($agrupado['ID'] == $row['SUELA_ID']) {
                                            echo "<div class='my-2'> {$agrupado['TOTAL_PRODUCIR']} Pares</div>";
                                        }
                                    }

                                    echo "</td>";

                                    array_push($repetidos, $row['SUELA_ID']);
                                    $casillero_impreso = true;

                                }
                            }
                        }

                        if ($casillero_impreso == false) {
                            if ($casillero['SUELA_ID'] != '') {
                                $sql = "SELECT * FROM SUELAS WHERE ID = ?;";

                                $marca = db_query($sql, array($casillero['SUELA_ID']))[0]['MARCA'];
                                $talla = db_query($sql, array($casillero['SUELA_ID']))[0]['TALLA'];

                                echo "<td class='align-middle'><div class='badge badge-main mb-2'>Referencia</div><div class='font-weight-bold'>" . mb_convert_case($marca, MB_CASE_TITLE, 'UTF-8') . " " . $talla . "</div></td>";
                            } elseif ($casillero['ACTIVO'] == 0) {
                                echo "<td class='align-middle'><i class='fas fa-ban text-danger my-2'></i></td>";
                            } else {
                                echo "<td class='align-middle'><div class='badge badge-success my-2'>Vacio</div></td>";
                            }
                        }
                    }
                }
                ?>
            </tr>
            </tbody>

            <?php
            if ($maquinaria_selected[0]['CASILLEROS'] == 30):
                ?>
                <!-- Third Table Head -->
                <thead class="thead-dark">
                <tr>
                    <?php
                    for ($i = 20; $i < 30; $i++) {
                        echo "<th scope='col' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                        echo $i + 1;
                        echo "</th>";
                    }
                    ?>
                </tr>
                </thead>

                <!-- Third Table Body -->
                <tbody>
                <tr>
                    <?php
                    foreach ($casilleros as $index => $casillero) {
                        $casillero_impreso = false;

                        // Tercera hilera
                        if ($casillero['NUMERO'] > 20) {
                            foreach ($result as $row) {
                                if (!in_array($row['SUELA_ID'], $repetidos)) {
                                    if ($casillero['SUELA_ID'] == $row['SUELA_ID']) {

                                        echo "<td>";
        
                                        echo "<div class='badge badge-main my-2'>Pedidos</div>";
                                        echo "<div class='font-weight-bold'>" . mb_convert_case($row['MARCA'], MB_CASE_TITLE, 'UTF-8') . " " . $row['TALLA'] . "</div>";

                                        foreach ($agrupados as $agrupado) {
                                            if ($agrupado['ID'] == $row['SUELA_ID']) {
                                                echo "<div class='my-2'> {$agrupado['TOTAL_PRODUCIR']} Pares</div>";
                                            }
                                        }

                                        echo "</td>";

                                        array_push($repetidos, $row['SUELA_ID']);
                                        $casillero_impreso = true;

                                    }
                                }
                            }

                            if ($casillero_impreso == false) {
                                if ($casillero['SUELA_ID'] != '') {
                                    $sql = "SELECT * FROM SUELAS WHERE ID = ?;";

                                    $marca = db_query($sql, array($casillero['SUELA_ID']))[0]['MARCA'];
                                    $talla = db_query($sql, array($casillero['SUELA_ID']))[0]['TALLA'];

                                    echo "<td class='align-middle'><div class='badge badge-main mb-2'>Referencia</div><div class='font-weight-bold'>" . mb_convert_case($marca, MB_CASE_TITLE, 'UTF-8') . " " . $talla . "</div></td>";
                                } elseif ($casillero['ACTIVO'] == 0) {
                                    echo "<td class='align-middle'><i class='fas fa-ban text-danger my-2'></i></td>";
                                } else {
                                    echo "<td class='align-middle'><div class='badge badge-success my-2'>Vacio</div></td>";
                                }
                            }
                        }
                    }
                    ?>
                </tr>
                </tbody>
            <?php
            endif;
            ?>

        </table>
    </div>
    <!-- End of Table -->

    <!-- Machine Information -->
    <div class="card" style="width: 17rem;">
        <div class="card-body">
            <h5 class="card-title"><?= mb_convert_case($maquinaria_selected[0]['NOMBRE'], MB_CASE_TITLE, "UTF-8"); ?></h5>
            <hr>
            <p class="card-text">

                <?= "<p><span class='font-weight-bold'>&#9642 Color: </span>" . mb_convert_case($maquinaria_selected[0]['COLOR'], MB_CASE_TITLE, "UTF-8") . "</p>"; ?>

                <?= "<p><span class='font-weight-bold'>&#9642 Material: </span>" . mb_convert_case($maquinaria_selected[0]['MATERIAL'], MB_CASE_TITLE, "UTF-8") . "</p>"; ?>

                <?= "<p><span class='font-weight-bold'>&#9642 Capacidad Actual: </span>" . $maquinaria_selected[0]['CAPACIDAD'] . "</p>"; ?>

                <?= "<p><span class='font-weight-bold'>&#9642 Capacidad Disponible: </span>" . $maquinaria_selected[0]['DISPONIBLE'] . "</p>"; ?>

            </p>
        </div>
    </div>

    <?php else: ?>
        <h6>No hay maquinarias</h6>
    <?php endif; ?>

</div>
<!-- / Fin de Contenido -->

<!-- Incluimos el footer.php & agregamos una comprobacion de casillero repetido -->
<?php include_once 'components/footer.php'; ?>