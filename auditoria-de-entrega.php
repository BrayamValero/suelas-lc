<?php

// Incluimos el header.php y components.php
$title = 'Auditoría de entrega';
require_once 'components/header.php';
require_once 'components/navbar.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'MOLINERO');

if(!in_array($_SESSION['ROL'], $roles_permitidos)){
    require_once 'components/error.php';
    require_once 'components/footer.php';
    exit();
}

?>

<!-- Incluimos el sidebar.php -->
<?php require_once 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">

    <!-- Incluimos el Navbar -->
    <?php get_navbar('Molino', 'Auditoría de Entrega'); ?>

    <!-- Tabla -->
    <div class="table-responsive-lg">
        <table class="table table-bordered text-center" id="tabla">
            <thead class="thead-dark">
            <tr>
                <th scope="col">#</th>
                <th scope="col">Fecha de Entrega a Molino</th>
                <th scope="col">Estado</th>
                <th scope="col">Opciones</th>
            </tr>
            </thead>
            <tbody>
            <?php
            require_once "backend/api/db.php";
            require_once "backend/api/utils.php";

            $sql = "SELECT * FROM AUDITORIA_NOR_INV;";
            $result = db_query($sql);

            // echo '<pre>'; print_r($result); echo '</pre>';

            foreach ($result as $row) {

                echo "<tr>";

                echo "<th scope='col'>{$row['ID']}</th>";
                echo "<td>" . strftime("%d de %b de %Y, %H:%M %p", strtotime($row['FECHA_RECIBIDO'])) . "</td>";

                if ($row['ESTADO'] === 'PENDIENTE') {
                    echo "<td>
                        <button class='btn btn-sm btn-main' onclick='notificarRegistro({$row['ID']})'>
                            Pendiente
                        </button>
                    </td>";
                } else {
                    echo "<td>Aprobado</td>";
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
    <!-- End of Table -->

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
<!-- / Fin de contenido -->

<!-- Inline JavaScript -->
<script>

var obtenerMaterias;

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
		"url": "datatables/Spanish.json"
	}
});

// Obtenemos todas las Materias Primas.
$.ajax({
    type: 'get',
    url: 'backend/api/utils.php?fun=obtenerMateriasPrimas',
    success: function (data) {

        obtenerMaterias = JSON.parse(data);
        // console.log(obtenerMaterias);

    }

});

 // Ver Solicitudes.
 $('#verSolicitud-modal').on('show.bs.modal', function (e) {
        
    const mostrarDatos = $('#mostrarDatos-modal');

    mostrarDatos.empty();

    let solicitudMaterialId = $(e.relatedTarget).data('solicitud-material-id');

    $.ajax({
        type: 'post',
        url: 'backend/api/utils.php?fun=obtenerMaterialesSolicitadosId',
        data: 'solicitud_material_id=' + solicitudMaterialId,
        success: function (data) {

            const result = JSON.parse(data);
            // console.log(result);

            result.forEach(row => {

                mostrarDatos.append(
                `<div class="form-group col-10">
                    <label for="${row.MATERIAL.toProperCase()}"><h6>${row.MATERIAL.toProperCase()} <span class="badge badge-primary">${row.COLOR.toProperCase()}</span> <span class="badge badge-dark">${row.DUREZA}%</span></h6></label>
                    <input name="${row.MATERIAL.toProperCase()}" type="text" value="${row.CANTIDAD} Kgs" class="form-control" readonly>
                </div>`
                );

            });
            
            $.ajax({
                type: 'post',
                url: 'backend/api/utils.php?fun=obtenerEstadoNorsaplastAInventario',
                data: 'solicitud_material_id=' + solicitudMaterialId,
                success: function (data) {

                    const result = JSON.parse(data);
                    // console.log(result);  

                    if (result[0].ESTADO === 'PENDIENTE') {
                        mostrarDatos.append(
                        `<small class="py-2">El Material se encuentra en <strong class="text-primary">Molino</strong></small>`
                        );
                    } else {
                        mostrarDatos.append(
                        `<small class="py-2">El Material se encuentra en <strong class="text-primary">Inventario</strong></small>`
                        );
                    }
        
                }
            });

        }

    });
    
});

// Notificar el Registro de Materia Prima a Inventario - De Norsaplast a Inventario.
function notificarRegistro(id) {


    Swal.fire({
        title: '¿Desea registrar la materia prima en el inventario?',
        text: 'Esta acción no se puede deshacer..',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {
        if (result.value) {
            Swal.fire({
                title: '¡Registrado!',
                text: 'El material ha sido agregado a inventario.',
                icon: 'success'
            }).then(function () {

                $.ajax({
                    type: 'post',
                    url: 'backend/api/utils.php?fun=obtenerMaterialesSolicitadosId',
                    data: 'solicitud_material_id=' + id,
                    success: function (data) {

                        const result = JSON.parse(data);

                        let index = obtenerMaterias.findIndex(row => {
                            return row.MATERIAL == result[0].MATERIAL && row.COLOR == result[0].COLOR && row.DUREZA == result[0].DUREZA;
                        });

                        if(index != -1) {
                            // console.log("Si se encuentra en Materia Prima, hay que pushear ID y CANTIDAD.");
                            // console.log(`MATERIA_PRIMA_ID: ${obtenerMaterias[index].ID}, CANTIDAD: ${result[0].CANTIDAD}`);

                            $.post("backend/api/auditoria/notificar_registro.php", {
                                registro: JSON.stringify({
                                    'id': id,
                                    'materia_prima_id': obtenerMaterias[index].ID,
                                    'cantidad': result[0].CANTIDAD
                                })
                            });

                            window.location = window.location.href;
                        
                        } else {
                            // console.log("No se encuentra en Materia Prima, hay que pushear MATERIAL, COLOR, DUREZA y CANTIDAD.");
                            // console.log(`MATERIAL: ${result[0].MATERIAL}, COLOR: ${result[0].COLOR}, DUREZA: ${result[0].DUREZA}, CANTIDAD: ${result[0].CANTIDAD}`);
                      
                            $.post("backend/api/auditoria/notificar_registro.php", {
                                registro: JSON.stringify({
                                    'id': id,
                                    'materia_prima_id': null,
                                    'material': result[0].MATERIAL,
                                    'color': result[0].COLOR,
                                    'dureza': result[0].DUREZA,
                                    'cantidad': result[0].CANTIDAD
                                })
                            });

                            window.location = window.location.href;
                        }
                               
                    }

                });
                
            });

        }
    });

};
    
</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>