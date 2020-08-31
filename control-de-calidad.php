<?php

// Incluimos el header.php y components.php
$title = 'Control de Calidad';
include 'components/header.php';
include 'components/components.php';
require_once 'backend/api/utils.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'PRODUCCION', 'CONTROL');

if(!in_array($_SESSION['USUARIO']['CARGO'], $roles_permitidos)){
    include 'components/error.php';
    include_once 'components/footer.php';
    exit();
}

// Chequear capacidad total del sistema.
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

    <!-- Contenido del Header -->
    <div class="header-body py-3">
        <div class="row">
            <div class="col">
                <h6 class="text-title">Control de Calidad</h6>

                <!-- Revisamos que haya una Maquinaria ACTIVA, en caso de que no haya,  -->
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

                <!-- Si no está vacio selecciona las maquinas segun el ID Correspondiente -->
                <?php
                if (!empty($maquinarias)):
                    $sql = "SELECT * FROM MAQUINARIAS WHERE ID = ?;";
                    $maquinaria_selected = db_query($sql, array($id));
                ?>
                <!-- Barra Inferior (Botones de las Maquinas) -->
                <div class="row">
                    <div class="col">
                        <button type="button"class="align-middle sidebarCollapse btn btn-sm btn-main mb-2">
                            <i class="fas fa-bars"></i>
                        </button>

                        <!-- Selecciona la maquina correspondiente al ID -->
                        <?php
                        foreach ($maquinarias as $maquinaria) {
                            if ($maquinaria['ID'] == $id) {
                                echo "<a href='control-de-calidad.php?id={$maquinaria['ID']}' class='btn btn-sm btn-main mr-1 mb-2'>" . mb_convert_case($maquinaria['NOMBRE'], MB_CASE_TITLE, "UTF-8") . "</a>";
                                $first = true;
                            } else {
                                echo "<a href='control-de-calidad.php?id={$maquinaria['ID']}' class='btn btn-sm btn-outline-dark mr-1 mb-2'>" . mb_convert_case($maquinaria['NOMBRE'], MB_CASE_TITLE, "UTF-8") . "</a>";
                            }
                        }
                        ?>

                    </div>
                </div>

                <!-- Creamos los casilleros y los filtramos dependiendo de su requerimiento -->
                <?php
                $sql = "SELECT ID FROM COLOR WHERE COLOR = ?;";
                $color = db_query($sql, array($maquinaria_selected[0]['COLOR']));

                $sql = "SELECT P.*, S.REFERENCIA AS SUELA_REFERENCIA,
                                    S.ID AS SUELA_ID, S.PESO_IDEAL AS PESO_IDEAL,
                                    S.PESO_MAQUINA AS PESO_MAQUINA, S.MARCA AS MARCA,
                                    S.TALLA AS TALLA 
                                        FROM PRODUCCION P 
                                            JOIN SUELAS S ON P.SUELA_ID = S.ID WHERE P.ESTADO = 'PENDIENTE' 
                                                AND P.COLOR_ID = ? 
                                            ORDER BY CREATED_AT ASC;";

                $result = db_query($sql, array($color[0]['ID']));

                $sql = "SELECT * FROM CASILLEROS WHERE MAQUINARIA_ID = ?;";
                $casilleros = db_query($sql, array($id));
                // $repetidos = array();

                ?>
                
            </div>
        </div>
    </div>
    <!-- / Fin de Header -->

    <!-- Mostramos las tablas con la información correspondiente -->
    <div class="table-responsive text-center">
        <table class="table table-bordered text-center">

            <!-- Primer <TH> -->
            <thead class="thead-dark">
                <tr>
                    <?php
                    if ($maquinaria_selected[0]['CASILLEROS'] == 1) {
                        for ($i = 0; $i < 1; $i++) {
                            echo "<th scope='col' class='numero-casillero' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                            echo $i + 1;
                            include 'components/tabla-dropdown.php';
                            echo "</th>";
                        }
                    } elseif ($maquinaria_selected[0]['CASILLEROS'] == 5) {
                        for ($i = 0; $i < 5; $i++) {
                            echo "<th scope='col' class='numero-casillero' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                            echo $i + 1;
                            include 'components/tabla-dropdown.php';
                            echo "</th>";
                        }
                    } elseif ($maquinaria_selected[0]['CASILLEROS'] == 10) {
                        for ($i = 0; $i < 5; $i++) {
                            echo "<th scope='col' class='numero-casillero' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                            echo $i + 1;
                            include 'components/tabla-dropdown.php';
                            echo "</th>";
                        }
                    } elseif ($maquinaria_selected[0]['CASILLEROS'] == 20) {
                        for ($i = 0; $i < 10; $i++) {
                            echo "<th scope='col' class='numero-casillero' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                            echo $i + 1;
                            include 'components/tabla-dropdown.php';
                            echo "</th>";
                        }
                    } elseif ($maquinaria_selected[0]['CASILLEROS'] == 30) {
                        for ($i = 0; $i < 10; $i++) {
                            echo "<th scope='col' class='numero-casillero' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                            echo $i + 1;
                            include 'components/tabla-dropdown.php';
                            echo "</th>";
                        }
                    }
                    ?>
                </tr>
            </thead>

            <!-- Primer <TB> -->
            <tbody>
                <tr>
                    <?php

                    $total_producir = 0;

                    foreach ($casilleros as $index => $casillero) {
                        
                        $casillero_impreso = false;

                        // Primera hilera
                        if ($casillero['NUMERO'] <= 10) {

                            foreach ($result as $row) {

                                // if (!in_array($row['SUELA_ID'], $repetidos)) {

                                    if ($casillero['SUELA_ID'] == $row['SUELA_ID']) {
                                        
                                        echo "<td>";

                                        echo "<div class='badge badge-main badge-casillero m-2'>Pedido {$row['PEDIDO_ID']}</div>";
                                        echo "<div class='font-weight-bold referencia-casillero'>" . mb_convert_case($row['MARCA'], MB_CASE_TITLE, "UTF-8") . " " . "{$row['TALLA']}</div>";
                                        echo "<div class='m-2 pares-casillero'>{$row['RESTANTE']}</div>";
                                        $total_producir = $total_producir + $row['CANTIDAD'];

                                        // Habilitar el check solo para CONTROL y ADMIN
                                        if($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'CONTROL') {
                                            echo "<a href='#' onclick='marcarEmpaquetado({$row['ID']}, {$row['RESTANTE']})'>
                                                    <i class='fas fa-check-circle fa-casillero icon-color'></i>
                                                </a>";
                                        }
                                        
                                        echo "<hr>";

                                        echo "<div class='badge badge-secondary badge-casillero mb-1'>P. Ide {$row['PESO_IDEAL']} GR</div> <br>";

                                        echo "<div class='badge badge-secondary badge-casillero'>P. Maq {$row['PESO_MAQUINA']} GR</div>";
                                        
                                        echo "</td>";

                                        // array_push($repetidos, $row['SUELA_ID']);

                                        $casillero_impreso = true;

                                    }

                                // }

                            }

                            if ($casillero_impreso == false) {

                                if ($casillero['SUELA_ID'] != '') {

                                    $sql = "SELECT * FROM SUELAS WHERE ID = ?;";
                                    $marca = db_query($sql, array($casillero['SUELA_ID']))[0]['MARCA'];
                                    $talla = db_query($sql, array($casillero['SUELA_ID']))[0]['TALLA'];

                                    echo "<td class='align-middle'>
                                            <div class='badge badge-main badge-casillero mb-2'>Referencia</div>
                                                <div class='font-weight-bold'>" . mb_convert_case($marca, MB_CASE_TITLE, 'UTF-8') . " " . $talla . "</div>
                                            </td>";

                                } elseif ($casillero['ACTIVO'] == 0) {

                                    echo "<td class='align-middle'><i class='fas fa-ban fa-casillero text-danger my-2'></i></td>";

                                } else {

                                    echo "<td class='align-middle'><div class='badge badge-success badge-casillero my-2'>Vacio</div></td>";

                                }
                                
                            }

                        }

                    }
                    
                    ?>
                </tr>
            </tbody>

            <!-- Segundo <TH> -->
            <thead class="thead-dark">
                <tr>
                    <?php
                    if ($maquinaria_selected[0]['CASILLEROS'] == 20) {
                        for ($i = 10; $i < 20; $i++) {
                            echo "<th scope='col' class='numero-casillero' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                            echo $i + 1;
                            include 'components/tabla-dropdown.php';
                            echo "</th>";
                        }
                    } elseif ($maquinaria_selected[0]['CASILLEROS'] == 30) {
                        for ($i = 10; $i < 20; $i++) {
                            echo "<th scope='col' class='numero-casillero' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                            echo $i + 1;
                            include 'components/tabla-dropdown.php';
                            echo "</th>";
                        }
                    }
                    ?>
                </tr>
            </thead>

            <!-- Segundo <TB> -->
            <tbody>
                <tr>
                    <?php
                    foreach ($casilleros as $index => $casillero) {

                        $casillero_impreso = false;

                        // Segunda hilera
                        if ($casillero['NUMERO'] > 10 && $casillero['NUMERO'] <= 20) {

                            foreach ($result as $row) {

                                // if (!in_array($row['SUELA_ID'], $repetidos)) {

                                    if ($casillero['SUELA_ID'] == $row['SUELA_ID']) {

                                        echo "<td>";

                                        echo "<div class='badge badge-main badge-casillero m-2'>Pedido {$row['PEDIDO_ID']}</div>";
                                        echo "<div class='font-weight-bold referencia-casillero'>" . mb_convert_case($row['MARCA'], MB_CASE_TITLE, "UTF-8") . " " . "{$row['TALLA']}</div>";
                                        echo "<div class='m-2 pares-casillero'>{$row['RESTANTE']}</div>";
                                        $total_producir = $total_producir + $row['CANTIDAD'];

                                        // Habilitar el check solo para CONTROL y ADMIN
                                        if($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'CONTROL') {
                                            echo "<a href='#' onclick='marcarEmpaquetado({$row['ID']}, {$row['RESTANTE']})'>
                                                    <i class='fas fa-check-circle fa-casillero icon-color'></i>
                                                </a>";
                                        }
                                                        
                                        echo "<hr>";
                                 
                                        echo "<div class='badge badge-secondary badge-casillero mb-1'>P. Ide {$row['PESO_IDEAL']} GR</div> <br>";

                                        echo "<div class='badge badge-secondary badge-casillero'>P. Maq {$row['PESO_MAQUINA']} GR</div>";

                                        echo "</td>";

                                        // array_push($repetidos, $row['SUELA_ID']);

                                        $casillero_impreso = true;
                                        
                                    }

                                // }

                            }

                            if ($casillero_impreso == false) {
                                if ($casillero['SUELA_ID'] != '') {
                                    $sql = "SELECT * FROM SUELAS WHERE ID = ?;";

                                    $marca = db_query($sql, array($casillero['SUELA_ID']))[0]['MARCA'];
                                    $talla = db_query($sql, array($casillero['SUELA_ID']))[0]['TALLA'];

                                    echo "<td class='align-middle'><div class='badge badge-main badge-casillero mb-2'>Referencia</div><div class='font-weight-bold'>" . mb_convert_case($marca, MB_CASE_TITLE, 'UTF-8') . " " . $talla . "</div></td>";
                                } elseif ($casillero['ACTIVO'] == 0) {
                                    echo "<td class='align-middle'><i class='fas fa-ban fa-casillero text-danger my-2'></i></td>";
                                } else {
                                    echo "<td class='align-middle'><div class='badge badge-success badge-casillero my-2'>Vacio</div></td>";
                                }
                            }
                        }
                    }
                    ?>
                </tr>
            </tbody>

            <?php if ($maquinaria_selected[0]['CASILLEROS'] == 30): ?>

            <!-- Tercer <TH> -->
            <thead class="thead-dark">
                <tr>
                    <?php
                    for ($i = 20; $i < 30; $i++) {
                        echo "<th scope='col' data-id='{$casilleros[$i]['ID']}' data-numero='{$casilleros[$i]['NUMERO']}'>";
                        echo $i + 1;
                        include 'components/tabla-dropdown.php';
                        echo "</th>";
                    }
                    ?>
                </tr>
            </thead>

            <!-- Tercer <TB> -->
            <tbody>
                <tr>
                    <?php
                    foreach ($casilleros as $index => $casillero) {

                        $casillero_impreso = false;

                        // Tercera hilera
                        if ($casillero['NUMERO'] > 20) {

                            foreach ($result as $row) {

                                // if (!in_array($row['SUELA_ID'], $repetidos)) {

                                    if ($casillero['SUELA_ID'] == $row['SUELA_ID']) {

                                        echo "<td>";

                                        echo "<div class='badge badge-main badge-casillero m-2'>Pedido {$row['PEDIDO_ID']}</div>";
                                        echo "<div class='font-weight-bold referencia-casillero'>" . mb_convert_case($row['MARCA'], MB_CASE_TITLE, "UTF-8") . " " . "{$row['TALLA']}</div>";
                                        echo "<div class='m-2 pares-casillero'>{$row['RESTANTE']}</div>";
                                        $total_producir = $total_producir + $row['CANTIDAD'];

                                        // Habilitar el check solo para CONTROL y ADMIN
                                        if($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'CONTROL') {
                                            echo "<a href='#' onclick='marcarEmpaquetado({$row['ID']}, {$row['RESTANTE']})'>
                                                    <i class='fas fa-check-circle fa-casillero icon-color'></i>
                                                </a>";
                                        }

                                                        
                                        echo "<hr>";

                                        echo "<div class='badge badge-secondary badge-casillero mb-1'>P. Ide {$row['PESO_IDEAL']} GR</div> <br>";

                                        echo "<div class='badge badge-secondary badge-casillero'>P. Maq {$row['PESO_MAQUINA']} GR</div>";

                                        echo "</td>";

                                        // array_push($repetidos, $row['SUELA_ID']);
                                        
                                        $casillero_impreso = true;

                                    
                                    }

                                // }

                            }

                            if ($casillero_impreso == false) {
                                if ($casillero['SUELA_ID'] != '') {
                                    $sql = "SELECT * FROM SUELAS WHERE ID = ?;";

                                    $marca = db_query($sql, array($casillero['SUELA_ID']))[0]['MARCA'];
                                    $talla = db_query($sql, array($casillero['SUELA_ID']))[0]['TALLA'];

                                    echo "<td class='align-middle'><div class='badge badge-main badge-casillero mb-2'>Referencia</div><div class='font-weight-bold'>" . mb_convert_case($marca, MB_CASE_TITLE, 'UTF-8') . " " . $talla . "</div></td>";
                                } elseif ($casillero['ACTIVO'] == 0) {
                                    echo "<td class='align-middle'><i class='fas fa-ban fa-casillero text-danger my-2'></i></td>";
                                } else {
                                    echo "<td class='align-middle'><div class='badge badge-success badge-casillero my-2'>Vacio</div></td>";
                                }
                            }
                        }
                    }
                    ?>
                </tr>
            </tbody>

            <?php endif; ?>

        </table>
    </div>
    <!-- / Fin de Tabla -->
    
    <!-- Información Inferior -->
    <?php include 'components/informacion-inferior.php' ?>

    <?php else: ?>
        <h6>No hay maquinarias</h6>
    <?php endif; ?>

    <!-- Modal de Editar Color en Máquina -->
    <div class="modal fade" id="editarColorMaquinaModal" aria-labelledby="editarColorMaquinaModal" tabindex="-1" role="dialog" aria-hidden="true" >
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <form action="backend/api/maquinarias/color.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Color</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row justify-content-center">
                            <!--  (ESCONDIDO) ID de la máquina -->
                            <input type="hidden" name="id" value="<?= $id; ?>">
                            <div class="form-group col-md-10">
                                <label for="inputColorModal">Color</label>
                                <select name="color" class="form-control dropdown-select2" id="inputColorModal">

                                    <?php

                                    $sql = "SELECT * FROM COLOR;";
                                    $result = db_query($sql);

                                    foreach ($result as $row) {

                                        if ($maquinaria_selected[0]['COLOR'] == $row['COLOR']) {
                                            echo "<option value='{$row['COLOR']}' selected>" . mb_convert_case($row['COLOR'], MB_CASE_TITLE, "UTF-8") . "</option>";
                                        } else {
                                            echo "<option value='{$row['COLOR']}'>" . mb_convert_case($row['COLOR'], MB_CASE_TITLE, "UTF-8") . "</option>";
                                        }

                                    }

                                    ?>

                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-main">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Fin del Modal -->

    <!-- Modal de Asignar Referencia -->
    <div class="modal fade" id="asignarReferenciaModal" tabindex="-1" role="dialog" aria-labelledby="asignarReferenciaModal"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
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
                            <input type="hidden" name="id" id="id-modal-referencia" value="<?= $id; ?>"> 
                            <input type="hidden" name="casillero-color" id="colorCasillero" value="<?= $maquinaria_selected[0]['COLOR'] ?>">
                            <div class="form-group col-md-10">
                                <label for="inputReferenciaModal">Referencia</label>
                                <select name="suela-id" id="inputReferenciaModal" class="form-control dropdown-select2">

                                    <?php

                                    // Obtenemos el material.
                                    $material = $maquinaria_selected[0]['MATERIAL'];
                                    
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
                        <button type="submit" class="btn btn-sm btn-main">Guardar Cambios</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- / Fin del Modal -->

    <!-- Modal de Intercambiar Referencia -->
    <div class="modal fade" id="swapReferenceModal" tabindex="-1" role="dialog" aria-labelledby="swapReferenceModal"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md" role="document">
            <div class="modal-content">
                <form action="backend/api/maquinarias/swap.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel"><i class="fab fa-slack-hash icon-color"></i>
                            Intercambiar Referencias</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row justify-content-center">
                            <!-- Hidden Data -->
                            <input type="hidden" name="id-casillero-a" id="id-casillero-a-modal-swap">
                            <input type="hidden" name="maquinaria-id-a" value="<?= $id; ?>">
                            <!-- Rotativas -->
                            <div class="form-group col-md-6">
                                <label for="id-maquinaria-modal-swap" class="text-center py-2">Seleccione la rotativa</label>
                                <select name="maquinaria-id-b" class="form-control" id="id-maquinaria-modal-swap">
                                    <?php
                                    foreach ($maquinarias as $maquinaria) {
                                        if ($maquinaria['MATERIAL'] == $maquinaria_selected[0]['MATERIAL']) {
                                            if ($maquinaria['ID'] == $maquinaria_selected[0]['ID']) {
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
                        <h6 class="text-center pt-4 pb-3">Seleccione el casillero</h6>
                        <div class="form-row justify-content-center">
                            <div class="btn-group-toggle" data-toggle="buttons" id="botonera-casilleros">
                            </div>
                        </div>
                        <div class="form-row justify-content-center">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <div class="mr-auto">
                            <span class="badge badge-dark mx-1">Disponibles</span>
                            <span class="badge badge-danger mx-1">Deshabilitados</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-main" id="submit-swap-modal">Intercambiar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <!-- Fin de Modal -->

</div>
<!-- Fin del contenido -->

<!-- Inline JavaScript -->
<script>

// Declaramos las Variables y Constantes
var numero;
const selectMaquinariaBSwap = document.getElementById('id-maquinaria-modal-swap');
const botoneraCasilleros = document.getElementById('botonera-casilleros');

// Marcar Empaquetado (Control de Calidad)
function marcarEmpaquetado(id, restante) {

    let cantidad, pesado;

    Swal.mixin({
        input: 'number',
        confirmButtonText: 'Siguiente &rarr;',
        showCancelButton: true,
        progressSteps: ['1', '2']
    }).queue([
        {
            title: 'Primer Paso',
            text: 'Ingrese la cantidad de pares de suelas.',
            preConfirm: function(value){
                if((value <= restante) && (value !== null) && (value > 0)){
                    return cantidad = value;
                } else {
                    Swal.showValidationMessage('Error, verifique el campo.');
                }
            }
        },
        {
            title: 'Segundo Paso',
            text: 'Ingrese el peso correspondiente.',
            preConfirm: function(value){
                if((value > 0) && (value !== null)){
                    return pesado = value;
                } else {
                    Swal.showValidationMessage('Error, verifique el campo.');
                }
            }
        }
    ]).then((result) => {
        if (result.value) {
            Swal.fire({
                title: '¿Estás seguro?',
                html: `
                ¿Deseas enviar ${cantidad} pares de suelas, los cuales pesan ${pesado} Kgs?
                `,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Si',
                cancelButtonText: 'No'
            }).then((result) => {
                if (result.value) {

                    Swal.fire({
                        title: '¡Empaquetado!',
                        text: 'El paquete ha sido enviado a despachos.',
                        icon: 'success'
                    }).then(function () {

                        $.get("backend/api/pedidos/editar-produccion.php", {
                            id: id,
                            pesado: pesado,
                            cantidad: cantidad
                        }, function (data, status) {
                            if (status === "success") {
                                window.location.reload();
                            }
                        });
                        
                    });
                    
                }
            });
        }
    });

}

// Habilitar Casillero previamente Deshabilitado.
$('.habilitarCasillero').on('click', function (e) {

    let casilleroId = $(e.target.parentElement.parentElement.parentElement).data('id');

    Swal.fire({
        title: '¿Deseas habilitar el casillero?',
        text: 'Descuida, puedes deshabilitarlo luego.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {
        if (result.value) {
            Swal.fire({
                title: '¡Habilitado!',
                text: 'El casillero ha sido habilitado.',
                icon: 'success'
            }).then(function () {
                window.location = `backend/api/maquinarias/habilitar-casillero.php?id=${casilleroId}&maquinaria=<?= $id; ?>`
            });
        }
    });

});

// Vaciar Casillero
$('.vaciarCasillero').on('click', function (e) {

    let casilleroId = $(e.target.parentElement.parentElement.parentElement).data('id');

    Swal.fire({
        title: '¿Deseas vaciar el casillero?',
        text: 'Descuida, puedes agregar referencias luego.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {
        if (result.value) {
            Swal.fire({
                title: '¡Éxito!',
                text: 'El casillero se encuentra vacio.',
                icon: 'success'
            }).then(function () {
                window.location = `backend/api/maquinarias/vaciar-casillero.php?id=${casilleroId}&maquinaria=<?= $id; ?>`
            });
        }
    });

});

// Deshabilitar Casillero
$('.deshabilitarCasillero').on('click', function (e) {

    let casilleroId = $(e.target.parentElement.parentElement.parentElement).data('id');

    Swal.fire({
        title: '¿Deseas deshabilitar el casillero?',
        text: 'Descuida, puedes habilitarlo luego',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {
        if (result.value) {
            Swal.fire({
                title: '¡Deshabilitado!',
                text: 'El casillero ha sido deshabilitado.',
                icon: 'success'
            }).then(function () {
                window.location = `backend/api/maquinarias/deshabilitar-casillero.php?id=${casilleroId}&maquinaria=<?= $id; ?>`
            });
        }
    });
    
});

// Deshabilitar Máquina
$('.deshabilitarMaquina').on('click', function (e) {
    
    let casilleroId = $(e.target.parentElement.parentElement.parentElement).data('id');

    Swal.fire({
        title: '¿Deseas deshabilitar la máquina?',
        text: 'Descuida, puedes habilitarla luego',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {
        if (result.value) {
            Swal.fire({
                title: '¡Deshabilitada!',
                text: 'La máquina ha sido deshabilitada.',
                icon: 'success'
            }).then(function () {

                window.location = `backend/api/maquinarias/deshabilitar-maquina.php?maquina=1&id=${casilleroId}&maquinaria=<?= $id; ?>`

                form.submit();
                
            });
        }
    });

});

//  Modal de Asignar Referencias
$('#asignarReferenciaModal').on('show.bs.modal', function (e) {

    // Obtenemos el ID del Casillero
    const casilleroId = $(e.relatedTarget.parentElement.parentElement.parentElement).data('id');

    // Lo colocamos en el input de tipo "hidden"
    document.getElementById('idCasillero').value = casilleroId;

});

// Modal de Intercambiar Referencias
$('#swapReferenceModal').on('show.bs.modal', function (e) {
    
    const casilleroAId = $(e.relatedTarget.parentElement.parentElement.parentElement).data('id');
    numero = $(e.relatedTarget.parentElement.parentElement.parentElement).data('numero');

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerCasilleros',
        data: "id=<?= $maquinaria_selected[0]['ID']; ?>",
        success: function (data) {
            
            const res = JSON.parse(data);
            document.getElementById('id-casillero-a-modal-swap').value = casilleroAId;
            $(botoneraCasilleros).empty();

            res.forEach((casillero, index) => {
                let string = "";

                if (index + 1 == numero) {
                    string = `<label class='btn btn-sm btn-outline-dark custom-width'>
                                    <input disabled type='radio' name='id-casillero-b' value='${index + 1}'>${index + 1}
                                </label>
                                `;

                } else if(casillero.SUELA_ID !== null){
                    string = `<label class='btn btn-sm btn-outline-dark custom-width'>
                                    <input type='radio' name='id-casillero-b' value='${index + 1}'>${index + 1}
                                </label>
                                `;
                } else {
                    string = `<label class='btn btn-sm btn-outline-dark custom-width'>
                                    <input type='radio' name='id-casillero-b' value='${index + 1}'>${index + 1}
                                </label>
                                `;
                }

                // Casillero Deshabilitado
                if (casillero.ACTIVO == '0') {
                    string = `<label class='btn btn-sm btn-danger custom-width'>
                                <input disabled type='radio' name='id-casillero-b' value='${index + 1}'>${index + 1}
                            </label>
                                `;
                }

                botoneraCasilleros.insertAdjacentHTML('beforeend', string);

                if (index === 9 || index === 19) {
                    botoneraCasilleros.insertAdjacentHTML('beforeend', '<hr />');
                }
            });
        }
    });
});

$(selectMaquinariaBSwap).on('change', function () {

    const id = this.value;

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerCasilleros',
        data: `id=${id}`,
        success: function (data) {

            const res = JSON.parse(data);
            $(botoneraCasilleros).empty();

            res.forEach((casillero, index) => {
                let string = "";

                if (index + 1 == numero) {
                    string = `<label class='btn btn-sm btn-outline-dark custom-width'>
                                    <input disabled type='radio' name='id-casillero-b' value='${index + 1}'>${index + 1}
                                </label>
                                `;

                } else if(casillero.SUELA_ID !== null){
                    string = `<label class='btn btn-sm btn-outline-dark custom-width'>
                                    <input type='radio' name='id-casillero-b' value='${index + 1}'>${index + 1}
                                </label>
                                `;
                } else {
                    string = `<label class='btn btn-sm btn-outline-dark custom-width'>
                                    <input type='radio' name='id-casillero-b' value='${index + 1}'>${index + 1}
                                </label>
                                `;
                }

                // Casillero Deshabilitado
                if (casillero.ACTIVO == '0') {
                    string = `<label class='btn btn-sm btn-danger custom-width'>
                                <input disabled type='radio' name='id-casillero-b' value='${index + 1}'>${index + 1}
                            </label>
                                `;
                }

                botoneraCasilleros.insertAdjacentHTML('beforeend', string);

                if (index === 9 || index === 19) {
                    botoneraCasilleros.insertAdjacentHTML('beforeend', '<hr />');
                }
            });
        }
    });
});

$('#submit-swap-modal').click(function () {
    if (!$("input[name='id-casillero-b']:checked").val()) {
        Swal.fire("Whoops", "Debes seleccionar un casillero primero.", "warning");
        return false;
    }
});

</script>

<!-- Incluimos el footer.php & agregamos una comprobacion de casillero repetido -->
<?php 

if (isset($_SESSION['casillero_suela']) && $_SESSION['casillero_suela'] == true) {

    echo "<script>Swal.fire('Error', 'Ya hay un casillero usando esa referencia.', 'error');</script>";

    unset($_SESSION['casillero_suela']);
    $_SESSION['casillero_suela'] = null;

}

include_once 'components/footer.php'; 

?>