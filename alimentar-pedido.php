<?php

// Incluimos el header.php y components.php
$title = 'Alimentar Pedido';
require_once 'components/header.php';
require_once 'components/navbar.php';
require_once 'backend/api/utils.php';

// Chequeamos el status para evitar ediciones luego de pasar a PENDIENTE.
$pedido_id = $_GET['id'];
$sql = "SELECT ESTADO FROM PEDIDOS WHERE ID = ?;";
$status = db_query($sql, array($pedido_id))[0]['ESTADO'];

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS', 'DESPACHO');

if( !in_array($_SESSION['ROL'], $roles_permitidos) || $status != 'PRODUCCION' ){
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
    <?php get_navbar('Ventas', "Alimentar Pedido <span class='badge badge-danger'>Pedido " . $pedido_id . "</span>", false); ?>

    <!-- Form -->
    <form id="alimentarPedidoForm">

        <!-- Tabla de Datos -->
        <div class="tablaDatos">

            <!-- Datos del Cliente -->
            <h6 class="pb-3 font-weight-bold">
                <i class="fas fa-user-tie icon-color mr-2"></i> Datos del Cliente
            </h6>

            <div class="form-row">

                <div class="form-group col-lg-6">
                    <label for="clienteNombre">Nombre</label>
                
                    <?php 
                                
                        // Obtenemos los datos del Pedido
                        $sql = "SELECT PED.*, CLI.ID AS CLIENTE_ID, CLI.NOMBRE, CLI.DOCUMENTO, CLI.DOCUMENTO_NRO FROM PEDIDOS PED JOIN CLIENTES CLI ON CLI.ID = PED.CLIENTE_ID WHERE PED.ID = ?;";
                        $datos_pedido = db_query($sql, array($pedido_id));

                        // echo '<pre>'; print_r($datos_pedido); echo '</pre>';

                        echo "<input type='text' name='nombre' id='clienteNombre' class='form-control bg-white' value='" . mb_convert_case($datos_pedido[0]['NOMBRE'], MB_CASE_TITLE, "UTF-8") . " - {$datos_pedido[0]['DOCUMENTO']} {$datos_pedido[0]['DOCUMENTO_NRO']}' readonly>";

                    ?>
                </div>

                <div class="form-group col-lg-3">
                    <label for="fecha">Fecha de Entrega</label>
                    <input id="fecha" name="fecha" type="date" class="form-control bg-white" value="<?php echo $datos_pedido[0]['FECHA_ESTIMADA']; ?>" readonly>
                </div>

                <div class="form-group col-lg-3">
                    <label for="pago">Forma de Pago</label>
    
                    <?php

                        foreach (FORMAS_PAGO as $forma_pago) {

                            if ($datos_pedido[0]['FORMA_PAGO'] === $forma_pago) {

                                echo "<input type='text' name='nombre' class='form-control bg-white' value='" . mb_convert_case($forma_pago, MB_CASE_TITLE, "UTF-8") . "' readonly>";

                            }

                        }

                    ?>

                </div>

            </div>
            <!-- Fin de Datos del Cliente -->

        </div>
        <!-- Fin de Tabla de Datos -->

        <!-- Tabla de Pedidos -->
        <div class="tablaPedidos shadow-sm mt-4">
                        
            <h6 class="font-weight-bold">
                <i class="fas fa-shopping-bag icon-color mr-2"></i> Datos del Pedido
            </h6>
            <small class="text-muted d-block mb-3">Ingrese las suelas que desea despachar en el pedido de manera manual.</small>

            <?php include_once 'backend/api/pedidos/ver-alimentacion.php'; ?>

            <button type="button" id="botonAlimentarPedido" class="btn btn-main btn-block mt-3">Alimentar Pedido</button>
        
        </div>
        <!-- Fin de Tabla de Pedidos -->

    </form>
    <!-- Fin del Form -->

</div>
<!-- / Fin del contenido -->

<!-- Inline JavaScript -->
<script>

// AÑADIR => Añandiendo nueva referencia al Stock. 
document.getElementById('botonAlimentarPedido').addEventListener('click', function () {

    Swal.fire({
        title: '¿Estás seguro?',
        text: 'Si realizas esta acción no puede ser revertida.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {

        if (result.isConfirmed) {

            let datos = [];
            let inputs = document.querySelectorAll('input[type=number]');

            inputs.forEach( input => {
                if( input.value ) {
                    datos.push({
                        'prod_id': parseInt(input.getAttribute('data-prod-id')),
                        'valor': parseInt(input.value),
                        'html': input
                    });
                } 
            });

            // 1. Verificamos que al menos haya (1) input con data.
            if( datos.length === 0 ) return Swal.fire('Error', 'Al menos (1) campo debe tener valor.', 'error');

            // 2. Verificamos que la data esté correcta.
            for (let index = 0; index < datos.length; index++) {
                if( !datos[index].html.checkValidity() ) return Swal.fire('Error', 'No puedes despachar más de la cantidad establecida.', 'error');
            }

            // 3. Enviamos la data al backend para comprobar su validez.
            $.post('backend/api/pedidos/enviar-alimentacion.php',  { 'datos': JSON.stringify(datos) }, data => {
                
                const res = JSON.parse(data);

                if(res.status === 'success') {

                    Swal.fire({
                        title: '¡Empaquetados!',
                        text: 'La producción ha sido enviado a despachos.',
                        icon: 'success'
                    }).then(() => window.location = "pedidos-pendientes.php");
                    
                } else if (res.status === 'error') {

                    Swal.fire({
                        title: '¡Error!',
                        text: 'La producción ha sido editada en producción. Actualizando...',
                        icon: 'error'
                    }).then(() => window.location.reload());

                }

            });

        }

    });

});

</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>