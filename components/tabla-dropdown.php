<div class="d-inline dropdown show">
    <a href="#" class="p-1" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-ellipsis-v text-white"></i>
    </a>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
        <?php
        if (isset($i) && isset($casilleros) && $casilleros[$i]['ACTIVO'] == 1) {
            echo "<a class='dropdown-item' data-toggle='modal' data-target='#asignarReferenciaModal' href='#'>Asignar Referencia</a>";
            echo "<a class='dropdown-item' data-toggle='modal' data-target='#swapReferenceModal' href='#'>Intercambiar Referencias</a>";
            echo "<div class='dropdown-divider'></div>";
        }
        ?>

        <?php
        if (isset($i) && isset($casilleros) && $casilleros[$i]['ACTIVO'] == 0) {
            echo "<a class='dropdown-item habilitarCasillero' href='#'>Habilitar Casillero</a>";
        } elseif (isset($i) && isset($casilleros) && $casilleros[$i]['ACTIVO'] == 1) {
            echo "<a class='dropdown-item deshabilitarCasillero' href='#'>Deshabilitar Casillero</a>";
        }
        ?>
    </div>
</div>