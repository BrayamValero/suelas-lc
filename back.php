  <!-- Mostramos la tabla con la información correspondiente -->
  <div class="table-responsive-lg">
        <table class="table table-bordered text-center" id="tabla">
            <thead class="thead-dark">
                <tr>
                    <th scope="col">#</th>
                    <th scope="col">Cliente</th>
                    <th scope="col">Tipo de Cliente</th>
                    <th scope="col">Forma de Pago</th>
                    <th scope="col">Fecha de Entrega</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Opciones</th>
                </tr>
            </thead>
            <tbody>

                <?php
                require_once "backend/api/db.php";
                require_once "backend/api/utils.php";

                $sql = "SELECT P.*, C.ID AS CLIENTE_ID, C.TIPO AS CLIENTE_TIPO, C.NOMBRE AS CLIENTE_NOMBRE FROM PEDIDOS P JOIN CLIENTES C ON P.CLIENTE_ID = C.ID WHERE P.ESTADO IN ('EN ANALISIS', 'PENDIENTE');";
                $result = db_query($sql);

                foreach ($result as $row) {

                    echo "<tr id='{$row['ID']}'>
                            <th scope='col'>{$row['ID']}</th>
                            <td>{$row['CLIENTE_NOMBRE']}</td>
                            <td>{$row['CLIENTE_TIPO']}</td>
                            <td>{$row['FORMA_PAGO']}</td>
                            <td>" . date('d-m-Y', strtotime($row['FECHA_ESTIMADA'])) . "</td>";

                    // Estado => Pedidos Pendientes
                    if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR') {
                       
                        if ($row['ESTADO'] === 'EN ANALISIS') {
                            echo "<td> 
                                    <a href='aprobar-pedido.php?id={$row['ID']}' class='btn btn-sm btn-main'>Aprobar Pedido</a>
                                </td>";
                        } else {
                            echo "<td>Pendiente</td>";
                        }

                    } else {
                        $row['ESTADO'] == 'EN ANALISIS' ? print "<td>En analisis</td>" : print "<td>Pendiente</td>";
                    }

                    // Opciones => Pedidos Pendientes.
                    echo "<td>";

                    if ($_SESSION['USUARIO']['CARGO'] == 'ADMINISTRADOR') {

                        if ($row['ESTADO'] === 'EN ANALISIS') {

                            echo "<a href='editar-pedido.php?id={$row['ID']}' class='mr-1'>
                                    <i class='fas fa-edit icon-color'></i>
                                </a>
                                <a href='javascript:void(0)' class='eliminarPedido mr-1' data-id='{$row['ID']}'>
                                    <i class='fas fa-trash icon-color'></i>
                                </a>";

                        } else {

                            echo "<a href='javascript:void(0)' class='cancelarPedido mr-1' data-id='{$row['ID']}'>
                                    <i class='fas fa-ban icon-color'></i>
                                </a>";

                        }

                        echo "<a href='javascript:void(0)' data-toggle='modal' data-target='#verPedido' data-id='{$row['ID']}'>
                                <i class='fas fa-eye icon-color'></i>
                            </a>";

                    }

                    if ($_SESSION['USUARIO']['CARGO'] == 'VENTAS' || $_SESSION['USUARIO']['CARGO'] == 'DESPACHO') {

                        if ($row['ESTADO'] === 'EN ANALISIS') {
                            echo "<a href='editar-pedido.php?id={$row['ID']}'>
                                    <i class='fas fa-edit icon-color'></i>
                                </a>";
                        }

                        echo "<a href='javascript:void(0)' data-toggle='modal' data-target='#verPedido' data-id='{$row['ID']}'>
                                <i class='fas fa-eye icon-color'></i>
                            </a>";

                    }

                    echo "</td></tr>";

                }

                ?>
            </tbody>
        </table>
    </div>
    <!-- / Fin de tabla -->