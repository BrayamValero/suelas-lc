<?php

// Incluimos el header.php y components.php
$title = 'Pedidos Norsaplast';
include 'components/header.php';
include 'components/navbar.php';
require_once 'backend/api/utils.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR');

if(!in_array($_SESSION['USUARIO']['CARGO'], $roles_permitidos)){
    include 'components/error.php';
    include_once 'components/footer.php';
    exit();
}

?>

<!-- Incluimos el sidebar.php -->
<?php include 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">

    <!-- Incluimos el Navbar -->
    <?php get_navbar('Auditorias', 'Pedidos a Norsaplast'); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
    <div class="table-responsive-lg">
        <table class="table table-bordered text-center" id="tabla">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">De Pedidos a Norsaplast</th>
                <th scope="col">De Norsaplast a Molino</th>
                <th scope="col">Estado</th>
                <th scope="col">Ver</th>
            </tr>
            </thead>
            <tbody>
            <?php
            require_once "backend/api/db.php";
            require_once "backend/api/utils.php";

            $sql = "SELECT * FROM AUDITORIA_PED_NOR;";
            $result = db_query($sql);

            // echo '<pre>'; print_r($result); echo '</pre>';

            foreach ($result as $row) {

                echo "<tr>";
                echo "<th scope='col'>{$row['ID']}</th>";
                echo "<td>" . strftime("%d de %b de %Y, %H:%M %p", strtotime($row['FECHA_RECIBIDO'])) . "</td>";

                if ($row['FECHA_ENTREGADO'] === NULL) {
                    echo "<td>Pendiente</td>";
                } else {
                    echo "<td>" . strftime("%d de %B de %Y, %H:%M %p", strtotime($row['FECHA_ENTREGADO'])) . "</td>";
                }
                
                if ($row['ESTADO'] === 'PENDIENTE') {
                    echo "<td> 
                        <i class='fas fa-times-circle text-danger'></i></button>
                    </td>";
                } else {
                    echo "<td> 
                        <i class='fas fa-check-circle text-success'></i></button>
                    </td>";
                }

                echo "<td>
                        <a href='#' data-toggle='modal' data-target='#verSolicitud-modal' data-id='{$row['ID']}' data-solicitud-material-id='{$row['SOLICITUD_MATERIAL_ID']}'>
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

    <!-- Modal de Ver Solicitud de Material -->
    <div class="modal fade" id="verSolicitud-modal" tabindex="-1" role="dialog" aria-labelledby="verSolicitud-modal"
            aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
            <div class="modal-content">

                <!-- Form -->
                <form>

                    <div class="modal-header">
                        <h5 class="modal-title"><i class="fas fa-shopping-bag icon-color"></i> Datos de la Solicitud</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">
                        <div id="mostrarDatos-modal" class="form-row justify-content-center">
                        </div>
                    </div>

                </form>
                <!-- End of Form -->

            </div>
        </div>
    </div>
    <!-- Fin de Modal de Ver Solicitud de Material -->

</div>
<!-- Fin de Content -->

<!-- Inline JS -->
<script>

    // DataTables Plugin: https://datatables.net/
    const tabla = $('#tabla').DataTable({
        info: false,
        dom: "lrtip",
        // searching: false,
        lengthChange: false,
        pageLength: 10,
        order: [[0, 'desc']],
        columnDefs: [{
            targets: 4,
            searchable: true,
            orderable: true,
            className: "align-middle", "targets": "_all"
        }],
        language: {
            "url": "<?= BASE_URL . "datatables/Spanish.json"; ?>"
        }
    });

    // Ver Solicitudes.
    $('#verSolicitud-modal').on('show.bs.modal', function (e) {
        
        const mostrarDatos = $('#mostrarDatos-modal');

        mostrarDatos.empty();

        let solicitudMaterialId = $(e.relatedTarget).data('solicitud-material-id');

        // console.log(solicitudMaterialId);

        $.ajax({
            type: 'post',
            url: 'backend/api/utils.php?fun=obtenerMaterialesSolicitadosId',
            data: 'solicitud_material_id=' + solicitudMaterialId,
            success: function (data) {

                const result = JSON.parse(data);
                console.log(result);

                result.forEach(row => {

                    mostrarDatos.append(
                    `<div class="form-group col-10">
                        <label for="${row.MATERIAL.toProperCase()}"><h6>${row.MATERIAL.toProperCase()} <span class="badge badge-primary">${row.COLOR.toProperCase()}</span> <span class="badge badge-dark">${row.DUREZA}%</span></h6></label>
                        <input name="${row.MATERIAL.toProperCase()}" type="text" value="${row.CANTIDAD} Kgs" class="form-control" readonly>
                    </div>`
                    );

                });

                if (result[0].ESTADO === 'PENDIENTE') {
                    mostrarDatos.append(
                    `<small class="py-2">El Material se encuentra en <strong class="text-primary">Norsaplast</strong></small>`
                    );
                } else {
                    mostrarDatos.append(
                    `<small class="py-2">El Material se encuentra en <strong class="text-primary">Molino</strong></small>`
                    );
                }
         
            }

        });
        
    });

</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>