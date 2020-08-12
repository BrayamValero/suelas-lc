<!-- Informacion Inferior -->
<div class="pt-3">
    <div class="row">
        <div class="col-lg-5">
            <!-- Informacion de la máquina -->
            <h6 class="pb-2 font-weight-bold">Informacion de la maquina</h6>
            <div class="card" style="width: 17rem;">
                <div class="card-body">
                    <h5 class="card-title"><?php echo mb_convert_case($maquinaria_selected[0]['NOMBRE'], MB_CASE_TITLE, "UTF-8"); ?></h5>
                    <hr>
                    <p class="card-text">
                        
                        <?php 

                        if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'PRODUCCION') {
                            echo "<p><span class='font-weight-bold'>&#9642 Color: </span>" . mb_convert_case($maquinaria_selected[0]['COLOR'], MB_CASE_TITLE, "UTF-8") . " " . "<a href='#' data-toggle='modal' data-target='#editarColorMaquinaModal' role='button'><i class='fas fa-edit icon-color'></i></a>" . "</p>";
                        } else {
                            echo "<p><span class='font-weight-bold'>&#9642 Color: </span> " . mb_convert_case($maquinaria_selected[0]['COLOR'], MB_CASE_TITLE, "UTF-8") . "</p>";
                        }
                        
                        ?>

                        <?php echo "<p><span class='font-weight-bold'>&#9642 Material: </span>" . mb_convert_case($maquinaria_selected[0]['MATERIAL'], MB_CASE_TITLE, "UTF-8") . "</p>"; ?>

                        <?php echo "<p><span class='font-weight-bold'>&#9642 Capacidad Actual: </span>" . $maquinaria_selected[0]['CAPACIDAD'] . "</p>"; ?>

                        <?php
                        echo "<p><span class='font-weight-bold'>&#9642 Capacidad Disponible: </span>";
                        echo $maquinaria_selected[0]['DISPONIBLE'] - $total_producir;
                        echo "</p>";
                        ?>
                    </p>

                        <?php

                        if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'PRODUCCION') {
                        
                            echo "<a href='#' class='card-link icon-color deshabilitarMaquina'>Deshabilitar Máquina</a>";
                        }
                        
                        ?>
                    
                </div>
            </div>
        </div>


        <?php if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR' || $_SESSION['USUARIO']['CARGO'] == 'PRODUCCION'): ?>

        <div class="col-lg-7">

            <h6 class="pb-2 font-weight-bold">Pedidos Pendientes</h6>

            <!-- Tabla -->
            <div class="table-responsive-lg">
                <table class="table table-bordered text-center" id="tabla">
                    <thead class="thead-dark">
                    <tr>
                        <th scope="col">#</th>
                        <th scope="col">Fecha de Solicitud</th>
                        <th scope="col">Estado</th>
                        <th scope="col">Ver</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php
                    require_once "backend/api/db.php";
                    require_once "backend/api/utils.php";

                    $sql = "SELECT * FROM AUDITORIA_PED_PRO;";
                    $result = db_query($sql);

                    // echo '<pre>'; print_r($result); echo '</pre>';

                    foreach ($result as $row) {
                        echo "<tr>";

                        echo "<th scope='col'>{$row['ID']}</th>";
                        echo "<td>" . strftime("%d de %b de %Y, %H:%M %p", strtotime($row['FECHA_RECIBIDO'])) . "</td>";

                        if ($row['ESTADO'] === 'PENDIENTE') {
                            echo "<td>
                                <button class='btn btn-sm btn-main' onclick='notificarArreglo({$row['ID']})'>
                                    Pendiente
                                </button>
                            </td>";
                        } else {
                            echo "<td>Aprobado</td>";
                        }

                        echo "<td>
                                <a href='#' data-toggle='modal' data-target='#verPedido-modal' data-id='{$row['ID']}' data-pedido-id='{$row['PRODUCCION_PEDIDO_ID']}'>
                                    <i class='fas fa-eye icon-color'></i>
                                </a>
                            </td>";
        
                        echo "</tr>";
                    }
                    ?>
                    </tbody>
                </table>
            </div>
            <!-- Fin de Tabla -->
        </div>

        <?php endif; ?>

    </div>
</div>
<!-- Fin de Informacion Inferior -->