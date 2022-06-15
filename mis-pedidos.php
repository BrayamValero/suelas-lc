<?php

// Incluimos el header.php y components.php
$title = 'Mis pedidos';
require_once 'components/header.php';
require_once 'components/navbar.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('CLIENTE');

$rol = $_SESSION['ROL'];

if (!in_array($rol, $roles_permitidos)) {
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
  <?php get_navbar('Ventas', $title, true); ?>

  <!-- Mostramos la tabla con la información correspondiente -->
  <div class="table-responsive text-center w-100">
    <div id="spinner" class="spinner-border text-center" role="status">
      <span class="sr-only">Cargando...</span>
    </div>
    <table class="table table-bordered text-center" id="tabla">
      <thead class="thead-dark"></thead>
    </table>
  </div>
  <!-- Fin de Tabla -->

  <!-- Toast => Alertas (data-delay="700" data-autohide="false") -->
  <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="2000">
    <div class="toast-header">
      <i class="toast-icon"></i>
      <strong class="mr-auto toast-title"></strong>
      <button type="button" class="ml-2 mb-1 close" data-dismiss="toast" aria-label="Close">
        <span aria-hidden="true">&times;</span>
      </button>
    </div>
    <div class="toast-body"></div>
  </div>

  <!-- Añadimos el botón de Añadir Pedido -->
  <?php if (in_array($rol, ['ADMIN', 'VENTAS', 'CLIENTE'])) : ?>
    <div class="d-flex justify-content-center mt-5">
      <a class="btn btn-sm btn-main" href="añadir-pedido.php" role="button">Añadir Pedido</a>
    </div>
  <?php endif; ?>

  <!-- Modal de Ver Pedido -->
  <div id="verPedidoModal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="verPedidoModal" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
      <div class="modal-content">
        <form>
          <div class="modal-header">
            <h5 class="modal-title"><i class="fas fa-shopping-bag icon-color"></i> Datos del Pedido <span class="badge badge-danger" id="ordenPedidoId"></span></h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body" id="detallesPedido">
          </div>
        </form>
      </div>
    </div>
  </div>
  <!-- / Fin de Modal -->

</div>
<!-- / Fin del contenido -->

<!-- Inline JavaScript -->
<script>
  // VARIABLES => Declarando Variables Globales.

  var tabla;
  var posicionTabla;
  const user_name = "<?php echo ($_SESSION['NOMBRE']) ?>";
  const user_email = "<?php echo ($_SESSION['CORREO']) ?>";

  // Axios => Mostrando la tabla Mis Pedidos.
  axios.get('/backend/api/utils.php?fun=obtenerMisPedidos', {
      params: {
        user_name,
        user_email,
      }
    })
    .then(res => {

      const result = res.data;

      tabla = $('#tabla').DataTable({
        "initComplete": () => {
          $("#spinner").css('display', 'none');
        },
        "info": false,
        "dom": "lrtip",
        "pageLength": 10,
        "lengthChange": false,
        "order": [
          [0, 'desc']
        ],
        "data": result,
        "columns": [{
            data: "ID",
            title: "#"
          },
          {
            data: "CLIENTE_NOMBRE",
            title: "Cliente"
          },
          {
            data: "FORMA_PAGO",
            title: "Forma Pago",
            render: (val, type, row) => {
              return row.FORMA_PAGO.toProperCase();
            }
          },
          {
            data: "CREATED_AT",
            title: "Fecha Registro",
            render: (val, type, row) => {
              let date = new Date(Date.parse(row.CREATED_AT));
              return `${date.toLocaleDateString('es-ES')} ${date.toLocaleTimeString('es-ES')}`;
            }
          },
          {
            data: 'ID',
            title: "Opciones",
            render: (val, type, row) => {

              if (row.ESTADO === 'ANALISIS') {

                return `
                            <?php if ($rol === 'ADMINISTRADOR' || $rol === 'VENTAS') : ?>
                            <a href='editar-pedido.php?id=${row.ID}' class='mr-1'>
                                <i class='fas fa-edit icon-color'></i>
                            </a>
                            <a href='javascript:void(0)' class='eliminarPedido mr-1' data-id='${row.ID}'>
                                <i class='fas fa-trash icon-color'></i>
                            </a>
                            <?php endif; ?>

                            <a href='javascript:void(0)' class='verPedido' data-id='${row.ID}'>
                                <i class='fas fa-eye icon-color'></i>
                            </a>`;

              } else if (row.ESTADO === 'PENDIENTE') {

                return `
                            <?php if ($rol === 'ADMINISTRADOR') : ?>
                            <a href='editar-pedido.php?id=${row.ID}' class='mr-1'>
                                <i class='fas fa-edit icon-color'></i>
                            </a>
                            <a href='javascript:void(0)' class='eliminarPedido mr-1' data-id='${row.ID}'>
                                <i class='fas fa-trash icon-color'></i>
                            </a>
                            <?php endif; ?>

                            <a href='javascript:void(0)' class='verPedido' data-id='${row.ID}'>
                                <i class='fas fa-eye icon-color'></i>
                            </a>`;

              } else if (row.ESTADO === 'PRODUCCION') {

                if (row.IMPRESO === 'NO') {

                  return `
                                <?php if ($rol === 'ADMINISTRADOR') : ?>
                                <a href='javascript:void(0)' class='cancelarPedido mr-1' data-id='${row.ID}'>
                                    <i class='fas fa-ban icon-color'></i>
                                </a>
                                <?php endif; ?>
                                
                                <?php if ($rol === 'ADMINISTRADOR' || $rol === 'DESPACHO') : ?>
                                <a href='alimentar-pedido.php?id=${row.ID}' class='mr-1'>
                                    <i class="fas fa-luggage-cart icon-color"></i>
                                </a>
                                <?php endif; ?>

                                <a href='javascript:void(0)' class='verPedido mr-1' data-id='${row.ID}'>
                                    <i class='fas fa-eye icon-color'></i>
                                </a>`;

                } else {

                  return `
                                <?php if ($rol === 'ADMINISTRADOR' || $rol === 'DESPACHO') : ?>
                                <a href='javascript:void(0)' class='cancelarPedido mr-1' data-id='${row.ID}'>
                                    <i class='fas fa-ban icon-color'></i>
                                </a>
                                <a href='alimentar-pedido.php?id=${row.ID}' class='mr-1'>
                                    <i class='fas fa-search icon-color'></i>
                                </a>
                                <?php endif; ?>
                            
                                <a href='javascript:void(0)' class='verPedido mr-1' data-id='${row.ID}'>
                                    <i class='fas fa-eye icon-color'></i>
                                </a>`;

                }

              } else {

                return `
                            <a href='javascript:void(0)' class='verPedido mr-1' data-id='${row.ID}'>
                                <i class='fas fa-eye icon-color'></i>
                            </a>`;

              }

            }
          },
        ],
        "columnDefs": [{
          searchable: true,
          orderable: true,
          className: "align-middle",
          "targets": "_all"
        }],
        "language": {
          "url": "datatables/Spanish.json"
        }
      });

      // DATATABLES => Paginación
      $.fn.DataTable.ext.pager.numbers_length = 5;


    }).catch(err => {
      console.log();
    })
    .finally(() => {

      // DATATABLES => Detectar Fila Actual (Aplica para Eliminar y Editar un Elemento)
      $('#tabla tbody').on('click', 'tr', function() {
        posicionTabla = this;
      });

      // VER => Ver un Pedido.
      $('#tabla tbody').on('click', '.verPedido', function() {

        let id = $(this).data("id");

        $.ajax({
          type: 'post',
          url: 'backend/api/pedidos/ver.php',
          data: 'pedido_id=' + id,
          async: false,
          success: function(data) {

            document.getElementById('ordenPedidoId').innerHTML = id;
            document.getElementById('detallesPedido').innerHTML = data;
            $('#verPedidoModal').modal('show');

          }

        });

      });

    });
</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>