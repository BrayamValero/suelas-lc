<div class="d-inline dropdown show">
    <a href="javascript:void(0)" class="pl-1" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        <i class="fas fa-cog text-white"></i>
    </a>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
        <?php
        if (isset($i) && isset($casilleros) && $casilleros[$i]['ACTIVO'] == 1) {
            echo "<a class='dropdown-item' data-toggle='modal' data-target='#asignarReferenciaModal' href='javascript:void(0)'>Asignar Referencia</a>";
            echo "<a class='dropdown-item' data-toggle='modal' data-target='#intercambiarReferenciaModal' href='javascript:void(0)'>Intercambiar Referencias</a>";
            echo "<div class='dropdown-divider'></div>";
        }
        ?>

        <?php
        if (isset($i) && isset($casilleros) && $casilleros[$i]['ACTIVO'] == 0) {
            echo "<a class='dropdown-item habilitarCasillero' href='javascript:void(0)'>Habilitar Casillero</a>";
        } elseif (isset($i) && isset($casilleros) && $casilleros[$i]['ACTIVO'] == 1) {
            echo "<a class='dropdown-item vaciarCasillero' href='javascript:void(0)'>Vaciar Casillero</a>";
            echo "<a class='dropdown-item deshabilitarCasillero' href='javascript:void(0)'>Deshabilitar Casillero</a>";
        }
        ?>
    </div>
</div>