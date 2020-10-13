<?php

// Incluimos el header.php y components.php
$title = 'Registrar Salida';
require_once 'components/header.php';
require_once 'components/navbar.php';

// Agregamos los roles que se quiere que usen esta página.
// 'ADMINISTRADOR', 'VENTAS', 'MOLINERO', 'OPERARIO', 'PRODUCCION', 'DESPACHO', 'CONTROL', 'NORSAPLAST', 'CLIENTE'
$roles_permitidos = array('ADMINISTRADOR');

if(!in_array($_SESSION['ROL'], $roles_permitidos)){
    require_once 'components/error.php';
    require_once 'components/footer.php';
    exit();
}

// Agregar imagenes a la base de datos.
if(isset($_POST["submit"])) {
    
    // Set image placement folder    
    $target_dir = "imagenes/pedidos/";
    // Get file path
    $target_file = $target_dir . basename($_FILES["fileUpload"]["name"]);
    // Get file extension
    $imageExt = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    // Allowed file types
    $allowd_file_ext = array("jpg", "jpeg", "png");
    
    if (!file_exists($_FILES["fileUpload"]["tmp_name"])) {
       $resMessage = array(
           "status" => "alert-danger",
           "message" => "Select image to upload."
       );
    } else if (!in_array($imageExt, $allowd_file_ext)) {
        $resMessage = array(
            "status" => "alert-danger",
            "message" => "Allowed file formats .jpg, .jpeg and .png."
        );            
    } else if ($_FILES["fileUpload"]["size"] > 2097152) {
        $resMessage = array(
            "status" => "alert-danger",
            "message" => "File is too large. File size should be less than 2 megabytes."
        );
    } else if (file_exists($target_file)) {
        $resMessage = array(
            "status" => "alert-danger",
            "message" => "File already exists."
        );
    } else {
        if (move_uploaded_file($_FILES["fileUpload"]["tmp_name"], $target_file)) {
            $sql = "INSERT INTO REGISTRO_SALIDA VALUES (NULL, ?, ?, ?, NOW())";
            $result = db_query($sql, array($_SESSION['NOMBRE'], 1, $target_file));
            
             if($result){
                $resMessage = array(
                    "status" => "alert-success",
                    "message" => "Image uploaded successfully."
                );                 
             }
        } else {
            $resMessage = array(
                "status" => "alert-danger",
                "message" => "Image coudn't be uploaded."
            );
        }
    }
}

?>

<!-- Incluimos el sidebar.php -->
<?php include 'components/sidebar.php' ?>

<!-- Incluimos el contenido --> 
<div id="contenido">

    <!-- Incluimos el Navbar -->
    <?php get_navbar('Ventas', 'Registrar Salida'); ?>

    <!-- Mostramos la tabla con la información correspondiente -->
	<div class="table-responsive text-center" style="width:100%">
		<div id="spinner" class="spinner-border text-center" role="status">
			<span class="sr-only">Cargando...</span>
		</div>
		<table class="table table-bordered text-center" id="tabla">
			<thead class="thead-dark"></thead>
		</table>
	</div>
	<!-- Fin de Tabla -->

    <form action="" method="post" enctype="multipart/form-data" class="mb-3">
      <h3 class="text-center mb-5">Upload File in PHP 7</h3>

      <div class="user-image mb-3 text-center">
        <div style="width: 100px; height: 100px; overflow: hidden; background: #cccccc; margin: 0 auto">
          <img src="..." class="figure-img img-fluid rounded" id="imgPlaceholder" alt="">
        </div>
      </div>

      <div class="custom-file">
        <input type="file" name="fileUpload" class="custom-file-input" id="chooseFile">
        <label class="custom-file-label" for="chooseFile">Select file</label>
      </div>

      <button type="submit" name="submit" class="btn btn-primary btn-block mt-4">
        Upload File
      </button>
    </form>

    <!-- Display response messages -->
    <?php if(!empty($resMessage)) {?>
    <div class="alert <?php echo $resMessage['status']?>">
      <?php echo $resMessage['message']?>
    </div>
    <?php }?>

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

</div>
<!-- / Fin del contenido -->

<!-- Inline JavaScript -->
<script>


function readURL(input) {
    if (input.files && input.files[0]) {
    var reader = new FileReader();

    reader.onload = function (e) {
        $('#imgPlaceholder').attr('src', e.target.result);
    }

    // base64 string conversion
    reader.readAsDataURL(input.files[0]);
    }
}

$("#chooseFile").change(function () {
    readURL(this);
});

// VARIABLES => Declarando Variables Globales.
var tabla;

// DATATABLES => Mostrando la tabla PEDIDOS_PENDIENTES.
$.ajax({
    type: 'get',
    url: 'backend/api/utils.php?fun=obtenerAuditoriaControl',
    async: false,
    success: function (data) {

        const result = JSON.parse(data);

        tabla = $('#tabla').DataTable({
            "initComplete": function(settings, json) {
                $("#spinner").css('display', 'none');
            },
            "info": false,
            "dom": "lrtip",
            "pageLength": 10,
            "lengthChange": false,
            "order": [[0, 'desc']],
            "data": result,
            "columns": [
                { data: "ID", title: "#" },
				{ data: "PEDIDO_ID", title: "Pedido" },
                { data: "FECHA_EMPAQUETADO", title: "Fecha Registro", 
					render: function(value, type, row) {

                        let date = new Date(Date.parse(row.FECHA_EMPAQUETADO));
                        
                        return `${date.toLocaleDateString('es-US')} ${date.toLocaleTimeString('en-US')}`;

					}
				},
                { data: "NOMBRE_USUARIO", title: "Usuario" },
                { data: "REFERENCIA", title: "Referencia" },
				{ data: "CANTIDAD", title: "Cantidad" },
                { data: "PESADO", title: "Peso", 
					render: function(value, type, row) {
                        return `${row.PESADO} Kgs`;
					}
				},
				{ data: "ID", title: "Opciones", 
					render: function(value, type, row) {
                        return `<a href='javascript:void(0)' data-id='${row.ID}' class='btn btn-sm btn-main devolverEmpaquetado'>
                            <i class="fas fa-undo-alt"></i>
                        </a>`;
					}
				}
            ],
            "columnDefs": [{
                searchable: true,
                orderable: true,
                className: "align-middle", "targets": "_all"
            }],
            "language": {
                "url": "datatables/Spanish.json"
            }
        });

        // DATATABLES => Paginación
        $.fn.DataTable.ext.pager.numbers_length = 5;
	   
    }

});

// DEVOLVER => Devolver un paquete mal alimentado a Producción nuevamente.
$('#tabla tbody').on( 'click', '.devolverEmpaquetado', function () { 

    let id = $(this).data("id");

    Swal.fire({
        title: '¿Deseas revertir el empaquetado?',
        text: 'Al hacerlo se revertiran los errores cometidos.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
    }).then((result) => {
        if (result.value) {

            // Eliminando del backend.
            $.get(`backend/api/auditoria/devolver-produccion.php?id=${id}`);

            // Datatable => Quitando el elemento del frontend.
            tabla.row($(this).parents('tr')).remove().draw(false);

            // Mostrando Notificación de éxito.
            toastNotifications('fas fa-trash', 'text-success', '¡Devuelto!', 'Los errores han sido corregidos.');

        }
    });

});

</script>

<!-- Incluimos el footer.php -->
<?php require_once 'components/footer.php'; ?>