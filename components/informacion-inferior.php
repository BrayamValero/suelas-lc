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
    </div>
</div>