<?php

// Incluimos el header.php y components.php
$title = 'Aprobar Pedido';
include 'components/header.php';
include 'components/navbar.php';
require_once 'backend/api/utils.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR', 'VENTAS', 'DESPACHO');

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
    <?php get_navbar('Ventas', "Aprobar Pedido <span class='badge badge-danger'>" . $_GET['id'] . "</span>"); ?>

    <!-- Form -->
    <form>

        <!-- Tabla de Datos -->
        <div class="tablaDatos">

            <!-- Datos del Cliente -->
            <h6 class="pb-3 font-weight-bold">
                <i class="fas fa-user-tie icon-color mr-2"></i> Datos del Cliente
            </h6>

            <?php
                require_once "backend/api/db.php";
                $id = $_GET['id'];

                $sql = "SELECT * FROM CLIENTES WHERE ACTIVO = 'SI';";
                $result = db_query($sql);

                $sql_get = "SELECT * FROM PEDIDOS WHERE ID = ?;";
                $result_get = db_query($sql_get, array($id));
            ?>

            <div class="form-row">

                <div class="form-group col-lg-6">
                    <label for="clienteNombre">Nombre</label>
                    <select id="clienteNombre" name="nombre" class="form-control dropdown-select2">
                        <?php
                            foreach ($result as $row) {
                                
                                if ($row['ID'] == $result_get[0]['CLIENTE_ID']) {
                                    echo "<option value='{$row['ID']}'>" . mb_convert_case($row['NOMBRE'], MB_CASE_TITLE, "UTF-8") . " - {$row['DOCUMENTO']} - {$row['DOCUMENTO_NRO']}</option>";
                                }

                            }
                        ?>
                    </select>
                </div>

                <div class="form-group col-lg-3">
                    <label for="fecha">Fecha de Entrega</label>
                    <input id="fecha" name="fecha" type="date" class="form-control" value="<?php echo $result_get[0]['FECHA_ESTIMADA']; ?>" >
                </div>

                <div class="form-group col-lg-3">
                    <label for="pago">Forma de Pago</label>
                    <select id="pago" class="form-control dropdown-select2" name="pago">
                        <?php
                            foreach (FORMAS_PAGO as $forma_pago) {
                                if ($result_get[0]['FORMA_PAGO'] == $forma_pago) {
                                    echo "<option selected value='$forma_pago'>". mb_convert_case($forma_pago, MB_CASE_TITLE, "UTF-8") ."</option>";
                                }
                            }
                        ?>
                    </select>
                </div>

            </div>
            <!-- Fin de Datos del Cliente -->

        </div>
        <!-- Fin de Tabla de Datos -->

        <!-- Tabla de Pedidos -->
        <div class="tablaPedidos shadow-sm mt-4">

            <h6 class="font-weight-bold mb-4">
                <i class="fas fa-shopping-bag icon-color mr-2"></i> Datos del Pedido
                <small class="ml-1">Si no carga la información, prueba refrescando con la tecla <strong>F5.</strong></small>
            </h6>

            <!-- Contenedor de Pedidos / JavaScript .clear -->
            <div class="contenedorPedidos"></div>
            <!-- / fin de contenedor de Serie -->

            <button type="button" id="botonAprobarPedido" class="btn btn-main btn-block mt-3">Aprobar Pedido</button>
        
        </div>
        <!-- Fin de Tabla de Pedidos -->

    </form>
    <!-- Fin del Form -->

</div>
<!-- / Fin del contenido -->

<!-- Inline JavaScript -->
<script>

var i = 0;
var j = 0;
var datosPedido, datosSeries, obtenerStock;

const pedido_id = <?= $id ?>;
const cliente_id = document.getElementById('clienteNombre').value;

// 1. Obtenemos la Dureza.
$.ajax({
    type: 'get',
    url: 'backend/api/utils.php?fun=obtenerDureza',
    success: function (data) {

        obtenerDureza = JSON.parse(data);

    }

});   

// 1. Obtenemos el Stock Completo.
$.ajax({
type: 'get',
url: 'backend/api/utils.php?fun=obtenerStockCompleto',
async: false,
success: function (data) {

        obtenerStock = JSON.parse(data);

    }

});

// 2. Obtenemos el pedido.
$.ajax({
type: 'post',
url: 'backend/api/utils.php?fun=obtenerPedidoId',
data: `pedido_id=${pedido_id}`,
async: false,
success: function (data) {

        datosPedido = JSON.parse(data);

        // console.log(datosPedido);

        datosSeries = datosPedido.filter((pedido, index, self) =>
            index === self.findIndex((elem) => (
                elem.SERIE_ID === pedido.SERIE_ID && elem.COLOR_ID === pedido.COLOR_ID
            ))
        );

    }

});

// 3. Al momento de cargar todos los elementos del DOM, cargar el JS.
document.addEventListener('DOMContentLoaded', function () {
    
    // Obtenemos el grupo de Series para popular la tabla.
    datosSeries.forEach(serie => {

        let serieId = serie.SERIE_ID;
        let colorId = serie.COLOR_ID;

        $.ajax({
        type: 'get',
        url: `backend/api/utils.php?fun=obtenerGrupoSerie&id=${serieId}`,
        async: false,
        success: function (data) {

            const result = JSON.parse(data);

            // Obtenemos el respectivo "color" dependiendo del ID de la serie.
            $.ajax({
            type: 'post',
            url: 'backend/api/utils.php?fun=obtenerColor',
            async: false,
            data: `id=${colorId}`,
            success: function (data) {

                obtenerColor = JSON.parse(data);

                }
                
            });

            // Decoración del color en cada serie.
            let color = obtenerColor[0].COLOR;
            let backgroundHex = obtenerColor[0].CODIGO;

            let red = parseInt(backgroundHex.substring(1, 3), 16);
            let green = parseInt(backgroundHex.substring(3, 5), 16);
            let blue = parseInt(backgroundHex.substring(5, 7), 16);

            let colorHex = red * 0.299 + green * 0.587 + blue * 0.114 > 186 ? '#000000' : '#FFFFFF';

            $('.contenedorPedidos').append(`
                <div id="serie-${i}" class="contenedor-serie shadow-sm" data-serie-id="${serieId}" data-color-id="${colorId}">
                    <div class="form-row mb-3">
                        <div class="col">
                            <strong>${result[0].MARCA.toProperCase()}</strong>
                            <span class="badge border" style="background-color: ${backgroundHex}; color: ${colorHex};">${color.toProperCase()}</span>
                            <small class="text-muted">${result[0].TALLA} al ${result[result.length - 1].TALLA}</small>
                        </div>
                    </div>
                    <div id="grupoSeries-${i}" class="form-row text-center mt-1">
                    </div>
                </div>        
            `);

            for (let index = 0; index < result.length; index++) {

                var check = false;

                obtenerStock.some(stock => {

                    if (stock.SUELA_ID === result[index].SUELA_ID && stock.COLOR_ID === colorId) {

                        $('#grupoSeries-' + i).append(`
                            <div class="form-group col">
                                <label class="label-cantidades" for="cantidades">${result[index].TALLA}</label>
                                <input id="input-cantidad-${j}" class="form-control input-cantidades" data-suela-id="${result[index].SUELA_ID}" data-color-id="${colorId}" type="number" value="0" tabindex="-1" readonly>
                                <div id="quitar-${j}">
                                    <input type="number" class="form-control input-stock mt-2" id="input-stock-${j}" min="0" placeholder="Stock" data-suela-id="${result[index].SUELA_ID}" data-color-id="${colorId}" >
                                    <small class="form-text text-muted mt-2">
                                    Fabrica: ${stock.CANTIDAD} Pares</small>
                                </div>                                
                            </div>
                        `);

                        let cantidades = document.getElementById('input-cantidad-' + j );
                        let inputStock = document.getElementById('input-stock-' + j );
                        let quitarStock = document.getElementById('quitar-' + j );

                        datosPedido.some(elem => {

                            if (elem.SUELA_ID == cantidades.getAttribute('data-suela-id') && elem.COLOR_ID == cantidades.getAttribute('data-color-id')) {

                                // Colocamos el produccción_id en los campos NECESARIOS
                                inputStock.setAttribute("data-prod-id", elem.PROD_ID);
                                
                                if (parseInt(stock.CANTIDAD) < parseInt(elem.CANTIDAD)) {
                                    
                                    inputStock.max = stock.CANTIDAD;
                                    cantidades.value = elem.CANTIDAD;

                                } else {

                                    inputStock.max = cantidades.value = elem.CANTIDAD;

                                }
                            
                            }

                        });

                        if (parseInt(cantidades.value) === 0) {

                            quitarStock.remove();

                        }
                    
                        check = true;

                        j++;
                        
                    }

                });

                if (check) continue; 

                $('#grupoSeries-' + i).append(`
                    <div class="form-group col">
                        <label class="label-cantidades" for="cantidades">${result[index].TALLA}</label>
                        <input id="input-cantidad-${j}" class="form-control input-cantidades" data-suela-id="${result[index].SUELA_ID}" data-color-id="${colorId}" type="number" value="0" tabindex="-1" readonly>
                    </div>
                `);

                let cantidades = document.getElementById('input-cantidad-' + j );

                datosPedido.some(elem => {

                    if (elem.SUELA_ID == cantidades.getAttribute('data-suela-id') && elem.COLOR_ID == cantidades.getAttribute('data-color-id')) {
                        
                        return cantidades.value = elem.CANTIDAD;

                    }

                });

                j++;

            };

            i++;

        }

        });

    });

});
// Fin de DOMContentLoaded event.

// Botón de Aprobar Pedido => Paso Final
document.getElementById('botonAprobarPedido').addEventListener('click', function () {
    
    let verif_disponibilidad = [];
    let actualizar_stock = [];

    document.querySelectorAll('.input-stock').forEach(input => {

        let prod_id = input.getAttribute('data-prod-id');
        let color_id = input.getAttribute('data-color-id');
        let suela_id = input.getAttribute('data-suela-id');

        let stock = input.value;
        stock = stock == '' ? 0 : input.value;

        let agregarStock = { 
            "prod_id": parseInt(prod_id),
            "color_id": parseInt(color_id),
            "suela_id": parseInt(suela_id),
            "stock": parseInt(stock)
        };

        actualizar_stock.push(agregarStock);
        
        verif_disponibilidad.push(input.checkValidity());

    });

    console.table(actualizar_stock);

    if(verif_disponibilidad.includes(false)){

        return Swal.fire('Error','Estás superando los valores permitidos del stock y/o pedido.', 'error');

    } else {

        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Si cambias el estado del pedido no podrás editarlo.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
        }).then((result) => {
            if (result.value) {
                Swal.fire({
                    title: '¡Pendiente!',
                    text: 'El pedido ya pasó a producción.',
                    icon: 'success'
                }).then(function () {

                    // Obtenemos los materiales solicitados.
                    $.ajax({
                        type: 'post',
                        url: 'backend/api/utils.php?fun=obtenerCantidadesSolicitud',
                        data: 'pedido_id=' + pedido_id,
                        success: function (data) {

                            const result = JSON.parse(data);

                            let datosFiltrados = [],
                                materiales_solicitados = [];

                            result.forEach(row => {

                                let index = datosFiltrados.findIndex(elem => {
                                    return elem.MATERIAL == row.MATERIAL && elem.COLOR == row.COLOR;
                                });

                                if(index != -1) {
                                    datosFiltrados[index].CANTIDAD = parseInt(datosFiltrados[index].CANTIDAD) + parseInt(row.CANTIDAD);
                                } else {
                                    datosFiltrados.push(row);
                                }
                                
                            });

                            datosFiltrados.forEach(row => {

                                const materiales = {
                                    material: row.MATERIAL,
                                    color: row.COLOR,
                                    cantidad: (row.CANTIDAD / row.PESO_IDEAL).toString(),
                                    dureza: obtenerDureza[0].DUREZA
                                };

                                materiales_solicitados.push(materiales);

                            });

                            // 2. Creamos la solicitud de la materia prima a Norsaplast.
                            $.post("backend/api/solicitud_material/crear.php", {
                                solicitud_material: JSON.stringify({
                                    'pedido_id': pedido_id,
                                    'cliente_id': cliente_id
                                }),
                                'materiales_solicitados': JSON.stringify(materiales_solicitados),
                                'actualizar_stock' : JSON.stringify(actualizar_stock)
                            });

                        }

                    });
                    
                    // 3. Cambiamos el botón de confirmar pedido.
                    window.location = `backend/api/pedidos/confirmar.php?id=${pedido_id}`
                    
                });
            }
        });

    }
   
});

</script>

<!-- Incluimos el footer.php -->
<?php include_once 'components/footer.php'; ?>