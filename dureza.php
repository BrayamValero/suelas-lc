<?php

// Incluimos el header.php y components.php
include 'components/header.php';
include 'components/components.php';
require_once 'backend/api/utils.php';

// Filtramos la página para que solo los cargos correspondientes puedan usarla.
if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR'): 

?>

<!-- Incluimos el sidebar.php -->
<?php include 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">

    <!-- Incluimos el Navbar -->
    <?php get_navbar('Panel de Control', 'Maquinaria'); ?>
        
    <div class="row">

        <?php
        require_once 'backend/api/db.php';
        $sql = "SELECT * FROM DUREZA WHERE ID = '1';";
        $result = db_query($sql);
        ?>

        <div class="col py-3">
            <div class="card bg-light mb-3" style="max-width: 20rem;">
                <div class="card-header font-weight-bold">Datos de Dureza</div>
                    <div class="card-body">
                        <p class="card-text">▪ Dureza:
                            <span class="dureza-data">
                            <?php 

                            if(sizeof($result) == 0){
                                echo '0';
                            } else {
                                echo $result[0]['DUREZA']; 
                            }
                                                            
                            ?>%</span>
                            <a href="#" data-toggle="modal" data-id="<?php echo $result[0]['ID']; ?>" data-target="#editarDureza-modal" role="button"><i class="fas fa-edit icon-color"></i></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mostrar Botón si no hay una Dureza asignada -->
        <?php if(sizeof($result) == 0): ?>

        <a class="btn btn-sm btn-main mx-auto" data-toggle="modal" data-target="#añadirDureza-modal" href="#" role="button">Añadir Dureza</a>

        <?php endif; ?>
        
    </div>

    <!-- Modal de Editar Dureza -->
    <div class="modal fade" id="editarDureza-modal" tabindex="-1" role="dialog" aria-labelledby="editarDureza-modal"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <!-- Form -->
                <form action="backend/api/dureza/editar.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fab fa-react icon-color"></i> Editar Dureza</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row justify-content-center">

                            <!-- ID escondido para el POST -->
                            <input type="hidden" name="id" id="inputEditarId-modal"> 

                            <div class="form-group col-10">
                                <label for="inputEditarDureza-modal">Dureza</label>
                                <input id="inputEditarDureza-modal" type="number" min="0" max="100" class="form-control" name="dureza" placeholder="Dureza" required>
                            </div>
                            
                        </div>  
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-main">Editar</button>
                    </div>
                </form>
                <!-- Fin de Form -->
            </div>
        </div>
    </div>
    <!-- Fin de Modal de Editar Dureza -->

    <!-- Modal de Añadir Dureza -->
    <div class="modal fade" id="añadirDureza-modal" tabindex="-1" role="dialog" aria-labelledby="añadirDureza-modal"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">
                <!-- Form -->
                <form action="backend/api/dureza/añadir.php" method="POST">
                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fab fa-react icon-color"></i> Añadir Dureza</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-row justify-content-center">
                            <div class="form-group col-10">
                                <label for="inputAñadirDureza-modal">Dureza</label>
                                <input id="inputAñadirDureza-modal" type="number" min="0" max="100" class="form-control" name="dureza" placeholder="Dureza" required>
                            </div>
                            
                        </div>  
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-sm btn-secondary" data-dismiss="modal">Cerrar</button>
                        <button type="submit" class="btn btn-sm btn-main">Añadir</button>
                    </div>
                </form>
                <!-- Fin de Form -->
            </div>
        </div>
    </div>
    <!-- Fin de Modal de Añadir Dureza -->


</div>
<!-- End of Content section -->

<script>

/* PLUGINS */

// DataTables Plugin: https://datatables.net/
const tabla = $('#tabla').DataTable({
	info: false,
	lengthChange: false,
	pageLength: 5,
	order: [[0, 'desc']],
	columnDefs: [{
		targets: 3,
		searchable: true,
		orderable: true,
		className: "align-middle", "targets": "_all"
	}],
	language: {
		"url": "<?= BASE_URL . "datatables/Spanish.json"; ?>"
	}
});

/* FIN DE PLUGINS */


// Editar Dureza - Modal
$('#editarDureza-modal').on('show.bs.modal', function (e) {

    let id = $(e.relatedTarget).data('id');

    console.log(id);

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerDureza',
        data: 'id=' + id,
        success: function (data) {

            const result = JSON.parse(data);
            console.log(result);

            $('#inputEditarId-modal').val(result[0].ID);
            $('#inputEditarDureza-modal').val(result[0].DUREZA);

        }
    });

});

</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>

<!-- En Caso de no poseer derechos, incluir error.php-->
<?php 
    else:
    include 'components/error.php';
    include_once 'components/footer.php';
    exit();
?>

<!-- Fin del filtro -->
<?php
    endif;
?>