<?php

define('PRIORIDADES', array(
    'ALTA',
    'BAJA'
));

define('FORMAS_PAGO', array(
    'CREDITO',
    'EFECTIVO',
    'CHEQUE',
    'TARJETA',
    'TRANSFERENCIA',
    'TRASLADO'
));

define('ROLES', array(
    'ADMINISTRADOR',
    'VENTAS',
    'MOLINERO',
    'OPERARIO',
    'PRODUCCION',
    'NORSAPLAST',
    'DESPACHO',
    'CLIENTE',
    'CONTROL',
    'LIDER'
));

if (isset($_GET['fun'])) {
    require_once 'db.php';

    switch ($_GET['fun']) {
        case 'obtenerClientes':
            obtenerClientes();
            break;

        case 'obtenerClientesInternos':
            obtenerClientesInternos();
            break;

        case 'obtenerSuelas':
            obtenerSuelas();
            break;

        case 'obtenerProduccionReferencia':
            obtenerProduccionReferencia();
            break;

        case 'obtenerCantidadesSolicitud':
            obtenerCantidadesSolicitud();
            break;

        case 'obtenerProduccionId':
            obtenerProduccionId();
            break;

        case 'obtenerUsuarioId':
            obtenerUsuarioId();
            break;

        case 'obtenerUsuarios':
            obtenerUsuarios();
            break;

        case 'obtenerSuelaId':
            obtenerSuelaId();
            break;

        case 'obtenerMateriaPrima':
            obtenerMateriaPrima();
            break;

        case 'obtenerMateriaPrimaId':
            obtenerMateriaPrimaId();
            break;

        case 'obtenerColores':
            obtenerColores();
            break;

        case 'obtenerColor':
            obtenerColor();
            break;

        case 'obtenerMateriasPrimas':
            obtenerMateriasPrimas();
            break;

        case 'obtenerClienteId':
            obtenerClienteId();
            break;

        case 'obtenerPedidoReferencia':
            obtenerPedidoReferencia();
            break;

        case 'obtenerMaquinariaId':
            obtenerMaquinariaId();
            break;

        case 'obtenerColoresSuelas':
            obtenerColoresSuelas();
            break;

        case 'obtenerCasilleros':
            obtenerCasilleros();
            break;

        case 'obtenerMaquinarias':
            obtenerMaquinarias();
            break;

        case 'obtenerRecetaFormula':
            obtenerRecetaFormula();
            break;

        case 'obtenerFormula':
            // Obtiene una formula en especifico por su ID
            obtenerFormula();
            break;

        case 'obtenerFormulas':
            // Obtiene todas las formulas aprobadas
            obtenerFormulas();
            break;

        case 'obtenerEntregaMaterial':
            obtenerEntregaMaterial();
            break;

        case 'obtenerMaterialesEntregados':
            obtenerMaterialesEntregados();
            break;

        case 'obtenerOperariosLibres':
            obtenerOperariosLibres();
            break;

        case 'obtenerOperarios':
            obtenerOperarios();
            break;

        case 'obtenerMateriaRecibidaReporte':
            obtenerMateriaRecibidaReporte();
            break;

        case 'obtenerReporteProduccion':
            obtenerReporteProduccion();
            break;

        case 'obtenerSuelasEnStock':
            obtenerSuelasEnStock();
            break;

        case 'obtenerStockCompleto':
            obtenerStockCompleto();
            break;

        case 'obtenerSuelaEnStock':
            obtenerSuelaEnStock();
            break;


        case 'obtenerSerie':
            obtenerSerie();
            break;

        case 'obtenerSeries':
            obtenerSeries();
            break;

        case 'obtenerGrupoSerie':
            obtenerGrupoSerie();
            break;

        case 'obtenerClienteIdPedido':
            obtenerClienteIdPedido();
            break;

        case 'obtenerMaterialesSolicitadosId':
            obtenerMaterialesSolicitadosId();
            break;

        case 'obtenerEstadoPedidoAProduccion':
            obtenerEstadoPedidoAProduccion();
            break;

        case 'obtenerEstadoNorsaplastAInventario':
            obtenerEstadoNorsaplastAInventario();
            break;

        case 'obtenerDureza':
            obtenerDureza();
            break;

        case 'obtenerPedidoId':
            obtenerPedidoId();
            break;

        case 'obtenerPedidoAlimentarId':
            obtenerPedidoAlimentarId();
            break;

        case 'obtenerPedidos':
            obtenerPedidos();
            break;

        case 'obtenerPedidosPendientes':
            obtenerPedidosPendientes();
            break;

        case 'obtenerMisPedidos':
            obtenerMisPedidos();
            break;

        case 'obtenerPedidosParaImprimir':
            obtenerPedidosParaImprimir();
            break;

        case 'obtenerPrioridades':
            obtenerPrioridades();
            break;

        case 'obtenerPedidosEnProceso':
            obtenerPedidosEnProceso();
            break;

        case 'obtenerPedidosParaEmpaquetar':
            obtenerPedidosParaEmpaquetar();
            break;

        case 'obtenerVentasCulminadas':
            obtenerVentasCulminadas();
            break;

        case 'obtenerAuditoriaControl':
            obtenerAuditoriaControl();
            break;
    }
}

function obtenerClientes()
{
    $sql = "SELECT * FROM CLIENTES;";
    $result = db_query($sql);

    echo json_encode($result);
}

function obtenerClientesInternos()
{
    $sql = "SELECT * FROM CLIENTES WHERE TIPO = 'INTERNO' && ACTIVO = 'SI';";
    $result = db_query($sql);

    echo json_encode($result);
}

function obtenerSuelas()
{
    $sql = "SELECT * FROM SUELAS;";
    $result = db_query($sql);

    echo json_encode($result);
}

function obtenerUsuarioId()
{
    $sql = "SELECT ID, NOMBRE, CEDULA, CARGO, TELEFONO, CORREO, ACTIVO FROM USUARIOS WHERE ID = ?;";
    $result = db_query($sql, array($_POST['id']));
    echo json_encode($result);
}

function obtenerUsuarios()
{
    $sql = "SELECT ID, NOMBRE, CEDULA, CORREO, TELEFONO, ROL FROM USUARIOS WHERE ACTIVO = 'SI';";
    $result = db_query($sql);

    echo json_encode($result);
}

function obtenerSuelaId()
{
    $sql = "SELECT * FROM SUELAS WHERE  ID = ?;";
    $result = db_query($sql, array($_POST['id']));

    echo json_encode($result);
}

function obtenerMateriaPrima()
{
    $sql = "SELECT ID, DESCRIPCION FROM MATERIA_PRIMA;";
    $result = db_query($sql);

    echo json_encode($result);
}

function obtenerMateriaPrimaId()
{
    $sql = "SELECT * FROM MATERIA_PRIMA WHERE ID = ?;";
    $result = db_query($sql, array($_POST['id']));

    echo json_encode($result);
}

function obtenerColores()
{
    $sql = "SELECT * FROM COLOR;";
    $result = db_query($sql);
    echo json_encode($result);
}

function obtenerColor()
{
    $sql = "SELECT * FROM COLOR WHERE ID = ?;";
    $result = db_query($sql, array($_POST['id']));
    echo json_encode($result);
}

function obtenerMateriasPrimas()
{
    $sql = "SELECT * FROM MATERIA_PRIMA;";
    $result = db_query($sql);
    echo json_encode($result);
}

function obtenerClienteId()
{
    $sql = "SELECT * FROM CLIENTES WHERE ID = ?;";
    $result = db_query($sql, array($_POST['id']));

    echo json_encode($result);
}

function obtenerProduccionReferencia()
{
    $sql = "SELECT P.*, S.REFERENCIA AS SUELA_REFERENCIA, S.MARCA AS SUELA_MARCA, C.COLOR AS SUELA_COLOR, S.TALLA AS SUELA_TALLA
    FROM PRODUCCION P 
    JOIN SUELAS S
        ON P.SUELA_ID = S.ID 
    JOIN COLOR C
        ON P.COLOR_ID = C.ID
    WHERE P.PEDIDO_ID = ?;";

    $result = db_query($sql, array($_POST['pedido_id']));

    echo json_encode($result);
}

function obtenerCantidadesSolicitud()
{
    $sql = "SELECT P.PEDIDO_ID, S.MATERIAL AS MATERIAL, P.CANTIDAD, C.COLOR, S.PESO_IDEAL AS PESO_IDEAL
    FROM PRODUCCION P
    JOIN SUELAS S
        ON P.SUELA_ID = S.ID
    JOIN COLOR C
        ON P.COLOR_ID = C.ID 
    WHERE P.PEDIDO_ID = ?;";

    $result = db_query($sql, array($_POST['pedido_id']));

    echo json_encode($result);
}

function obtenerProduccionId()
{
    $sql = "SELECT * FROM PRODUCCION WHERE ID = ?;";
    $result = db_query($sql, array($_POST['id']));

    echo json_encode($result);
}

function obtenerPedidoReferencia()
{
    $sql = "SELECT * FROM PEDIDOS WHERE ID = ?;";
    $result = db_query($sql, array($_POST['referencia']));

    echo json_encode($result);
}

function obtenerMaquinariaId()
{
    $sql = "SELECT * FROM MAQUINARIAS WHERE ID = ?;";
    $result = db_query($sql, array($_POST['id']));

    echo json_encode($result);
}

function obtenerColoresSuelas()
{
    $sql = "SELECT DISTINCT COLOR, MATERIAL FROM SUELAS;";
    $result = db_query($sql);

    echo json_encode($result);
}

function obtenerCasilleros()
{
    $sql = "SELECT * FROM CASILLEROS WHERE MAQUINARIA_ID = ?;";
    $result = db_query($sql, array($_POST['id']));

    echo json_encode($result);
}

function obtenerMaquinarias()
{
    $sql = "SELECT * FROM MAQUINARIAS WHERE ESTADO = 'ACTIVO';";
    $result = db_query($sql);

    echo json_encode($result);
}

function obtenerRecetaFormula()
{
    $sql = "SELECT ? AS FORMULA_ID, M.ID AS MATERIAL_ID, M.DESCRIPCION AS MATERIAL_DESCRIPCION, R.ID AS RECETA_ID FROM RECETAS R JOIN MATERIA_PRIMA M ON M.ID = R.MATERIAL_ID WHERE R.FORMULA_ID = ?;";
    $result = db_query($sql, array($_GET['id'], $_GET['id']));

    echo json_encode($result);
}

function obtenerFormula()
{
    $sql = "SELECT * FROM FORMULAS WHERE ID = ?;";
    $result = db_query($sql, array($_GET['id']));

    echo json_encode($result);
}

function obtenerFormulas()
{
    $sql = "SELECT * FROM FORMULAS WHERE ESTADO = 'APROBADO';";
    $result = db_query($sql);

    echo json_encode($result);
}

function obtenerEntregaMaterial()
{
    $sql = "SELECT * FROM ENTREGA_MATERIAL WHERE ID = ?;";
    $result = db_query($sql, array($_POST['id']));

    echo json_encode($result);
}

function obtenerMaterialesEntregados()
{
    $sql = "SELECT M_E.MATERIAL_ID, M_P.DESCRIPCION, M_E.CANTIDAD FROM MATERIALES_ENTREGADOS M_E JOIN MATERIA_PRIMA M_P ON M_E.MATERIAL_ID = M_P.ID WHERE M_E.ENTREGA_MATERIAL_ID = ?;";
    $result = db_query($sql, array($_POST['id']));

    echo json_encode($result);
}

function obtenerOperariosLibres()
{
    $sql = "SELECT U.ID, NOMBRE 
        FROM USUARIOS U 
        LEFT JOIN OPERARIOS O 
            ON O.USUARIO_ID = U.ID 
        WHERE O.USUARIO_ID IS NULL AND U.CARGO = 'OPERARIO';";
    $result = db_query($sql);

    echo json_encode($result);
}

function obtenerOperarios()
{
    $sql = "SELECT U.ID, U.NOMBRE, O.TURNO, O.MATERIAL FROM USUARIOS U JOIN OPERARIOS O ON U.ID = O.USUARIO_ID WHERE U.ROL = 'OPERARIO';";
    $result = db_query($sql);

    echo json_encode($result);
}

function obtenerMateriaRecibidaReporte()
{
    $turno = strtoupper($_GET['turno']);
    $material = strtoupper($_GET['material']);

    // Se chequea si hay materia sobrante del reporte anterior
    $materia_sobrante = 0;

    $sql = "SELECT MATERIA_SOBRANTE FROM REPORTES WHERE TURNO = ? AND MATERIAL = ?;";
    $data = array($turno, $material);

    $result = db_query($sql, $data);

    if (!empty($result)) {
        $materia_sobrante = $result[0]['MATERIA_SOBRANTE'];
    }


    // Se obtiene la materia entregada por el molinero
    $sql = "SELECT SUM(TOTAL) AS TOTAL FROM ENTREGA_MATERIAL WHERE TURNO = ? AND MATERIAL = ? AND ESTADO = 'APROBADO';";
    $data = array($turno, $material);
    $result = db_query($sql, $data);
    $total = $materia_sobrante + $result[0]['TOTAL'];

    $sql = "SELECT SUM(PRO.PESADO) AS PESADO FROM PRODUCCION PRO JOIN SUELAS SU ON SU.ID = PRO.SUELA_ID WHERE PRO.ESTADO != 'COMPLETADO' AND SU.MATERIAL = ?;";
    $data = array($material);
    $pesado = db_query($sql, $data);

    if ($pesado[0]['PESADO'] == "") {
        $pesado = 0;
    } else {
        $pesado = $pesado[0]['PESADO'];
    }

    $sql = "SELECT ? AS MATERIA_SOBRANTE, ? AS TOTAL, ? AS PESADO;";
    $data = array($materia_sobrante, $total, $pesado);
    echo json_encode(db_query($sql, $data)[0]);
}

function obtenerReporteProduccion()
{
    $sql = "SELECT * FROM REPORTES WHERE ID = ?;";
    $result = db_query($sql, array($_POST['id']));

    echo json_encode($result);
}

function obtenerSuelasEnStock()
{
    $sql = "SELECT ST.ID, C.NOMBRE, SU.REFERENCIA, SU.MARCA, SU.TALLA, COL.COLOR, ST.CANTIDAD 
                FROM STOCK ST
                    LEFT JOIN SUELAS SU
                        ON ST.SUELA_ID = SU.ID
                    LEFT JOIN CLIENTES C
                        ON ST.CLIENTE_ID = C.ID
                    LEFT JOIN COLOR COL
                        ON ST.COLOR_ID = COL.ID;";
    $result = db_query($sql);
    echo json_encode($result);
}


function obtenerSuelaEnStock()
{
    $sql = "SELECT ST.ID, C.NOMBRE, SU.REFERENCIA, SU.MARCA, SU.TALLA, COL.COLOR, ST.CANTIDAD 
    FROM STOCK ST
        LEFT JOIN SUELAS SU
            ON ST.SUELA_ID = SU.ID
        LEFT JOIN CLIENTES C
            ON ST.CLIENTE_ID = C.ID
        LEFT JOIN COLOR COL
            ON ST.COLOR_ID = COL.ID
    WHERE  ST.ID = ?;";
    $result = db_query($sql, array($_POST['id']));
    echo json_encode($result);
}


function obtenerStockCompleto()
{

    $sql = "SELECT STO.SUELA_ID, STO.COLOR_ID, STO.CANTIDAD
            FROM STOCK STO
                LEFT JOIN CLIENTES CLI
                    ON STO.CLIENTE_ID = CLI.ID
                WHERE STO.CANTIDAD > 0 AND CLI.NOMBRE = 'FABRICA';";

    $result = db_query($sql);
    echo json_encode($result);
}

function obtenerSerie()
{
    $sql = "SELECT GS.SERIE_ID, SE.NOMBRE, SU.ID AS SUELA_ID, SU.MARCA, SU.TALLA
            FROM GRUPO_SERIES GS
                LEFT JOIN SUELAS SU
                    ON GS.SUELA_ID = SU.ID
                LEFT JOIN SERIES SE
                    ON GS.SERIE_ID = SE.ID
            WHERE GS.SERIE_ID = ?;";

    $result = db_query($sql, array($_GET['id']));

    echo json_encode($result);
}

function obtenerSeries()
{
    $sql = "SELECT * FROM SERIES;";

    $result = db_query($sql);

    echo json_encode($result);
}

function obtenerGrupoSerie()
{
    $sql = "SELECT SU.ID AS SUELA_ID, SU.MARCA, SU.TALLA
            FROM GRUPO_SERIES GS
                LEFT JOIN SUELAS SU
                    ON GS.SUELA_ID = SU.ID
            WHERE GS.SERIE_ID = ?;";

    $result = db_query($sql, array($_GET['id']));

    echo json_encode($result);
}

function obtenerClienteIdPedido()
{
    $sql = "SELECT P.CLIENTE_ID FROM PEDIDOS P WHERE P.ID = ?;";

    $result = db_query($sql, array($_POST['pedido_id']));

    echo json_encode($result);
}


function obtenerMaterialesSolicitadosId()
{
    $sql = "SELECT MS.MATERIAL AS MATERIAL, MS.COLOR AS COLOR, MS.DUREZA AS DUREZA, MS.CANTIDAD AS CANTIDAD, SM.ESTADO AS ESTADO
        FROM MATERIALES_SOLICITADOS MS
            JOIN SOLICITUD_MATERIAL SM
                ON MS.SOLICITUD_MATERIAL_ID = SM.ID
        WHERE MS.SOLICITUD_MATERIAL_ID = ?;";

    $result = db_query($sql, array($_POST['solicitud_material_id']));

    echo json_encode($result);
}

function obtenerEstadoPedidoAProduccion()
{
    $sql = "SELECT ESTADO FROM AUDITORIA_PED_PRO WHERE ID = ?;";

    $result = db_query($sql, array($_POST['id']));

    echo json_encode($result);
}

function obtenerEstadoNorsaplastAInventario()
{
    $sql = "SELECT ESTADO FROM AUDITORIA_NOR_INV WHERE SOLICITUD_MATERIAL_ID = ?;";
    $result = db_query($sql, array($_POST['solicitud_material_id']));
    echo json_encode($result);
}


function obtenerDureza()
{
    $sql = "SELECT * FROM DUREZA;";
    $result = db_query($sql);
    echo json_encode($result);
}

function obtenerPedidoId()
{
    $sql = "SELECT ID AS PROD_ID, SUELA_ID, SERIE_ID, COLOR_ID, CANTIDAD, URGENTE
        FROM PRODUCCION 
        WHERE PEDIDO_ID = ?;";
    $result = db_query($sql, array($_POST['pedido_id']));
    echo json_encode($result);
}

function obtenerPedidoAlimentarId()
{
    $sql = "SELECT ID AS PROD_ID, SUELA_ID, SERIE_ID, COLOR_ID, RESTANTE, URGENTE
        FROM PRODUCCION 
        WHERE PEDIDO_ID = ?;";
    $result = db_query($sql, array($_POST['pedido_id']));
    echo json_encode($result);
}


function obtenerPedidos()
{
    $sql = "SELECT * FROM PEDIDOS;";
    $result = db_query($sql);
    echo json_encode($result);
}

function obtenerPedidosPendientes()
{
    $sql = "SELECT PED.*, PRI.TIPO_PRIORIDAD, CLI.ID AS CLIENTE_ID, CLI.TIPO AS CLIENTE_TIPO, CLI.NOMBRE AS CLIENTE_NOMBRE 
            FROM PEDIDOS PED 
                JOIN CLIENTES CLI 
                    ON PED.CLIENTE_ID = CLI.ID
                JOIN PRIORIDAD PRI
                    ON PRI.ID = PED.PRIORIDAD_ID 
            WHERE PED.ESTADO NOT IN ('COMPLETADO');";
    $result = db_query($sql);
    echo json_encode($result);
}

function obtenerMisPedidos()
{

    $user_name = $_GET['user_name'];
    $user_email = $_GET['user_email'];

    $sql = "SELECT PED.*, PRI.TIPO_PRIORIDAD, CLI.ID AS CLIENTE_ID, CLI.TIPO AS CLIENTE_TIPO, CLI.NOMBRE AS CLIENTE_NOMBRE 
            FROM PEDIDOS PED 
                JOIN CLIENTES CLI 
                    ON PED.CLIENTE_ID = CLI.ID
                JOIN PRIORIDAD PRI
                    ON PRI.ID = PED.PRIORIDAD_ID 
            WHERE PED.ESTADO NOT IN ('COMPLETADO') 
                AND CLI.NOMBRE = ?
                AND CLI.CORREO = ? ;";
    $result = db_query($sql, array($user_name, $user_email));
    echo json_encode($result);
}

function obtenerPedidosParaImprimir()
{

    // Method => Group By
    function group_by($key, $data)
    {
        $result = array();
        $final_data = array();

        foreach ($data as $val) {
            if (array_key_exists($key, $val)) {
                $result[$val[$key]][] = $val;
            } else {
                $result[""][] = $val;
            }
        }

        foreach ($result as $key => $val) {
            $tallas = array_column($val, null, 'TALLA');
            $formatted = (object) [
                'MARCA' => $val[0]['MARCA'],
                'COLOR' => $val[0]['COLOR'],
                'TALLAS' => $tallas,
            ];
            array_push($final_data, $formatted);
        }

        return $final_data;
    }

    // Declaramos la variable que almacenara toda la produccion.
    $result = array();

    // Obtenemos TODOS los pedidos.
    $sql = "SELECT PED.ID AS PEDIDO_ID, CLI.NOMBRE, PED.CREATED_AT AS FECHA_CREACION, PED.ESTADO
        FROM PEDIDOS PED 
            JOIN CLIENTES CLI 
                ON PED.CLIENTE_ID = CLI.ID
            JOIN PRIORIDAD PRI
                ON PRI.ID = PED.PRIORIDAD_ID 
        WHERE PED.ESTADO NOT IN ('COMPLETADO');";
    $allOrders = db_query($sql);

    // Obtenemos toda la produccion de cada pedido, para eso tenemos que hacer un loop de cada pedido.
    foreach ($allOrders as $key => $value) {

        // Destructuracion de los elementos
        extract($value);

        $sql = "SELECT PROD.SERIE_ID, SUE.MARCA, COL.COLOR,  SUE.TALLA, PROD.CANTIDAD
        FROM PRODUCCION PROD
            JOIN SUELAS SUE 
                ON PROD.SUELA_ID = SUE.ID
            JOIN COLOR COL 
                ON PROD.COLOR_ID = COL.ID
            WHERE PEDIDO_ID = ?;";
        $order = db_query($sql, array($PEDIDO_ID));

        // Spliteamos la orden 
        $orderGrouped = group_by("MARCA", $order);

        // Anadimos ID, Nombre, Fecha Creacion y Estado en todos los elementos.
        foreach ($orderGrouped as $key => $val) {
            $orderGrouped[$key]->PEDIDO_ID = $PEDIDO_ID;
            $orderGrouped[$key]->NOMBRE = $NOMBRE;
            $orderGrouped[$key]->FECHA_CREACION = $FECHA_CREACION;
            $orderGrouped[$key]->ESTADO = $ESTADO;
        }

        // Pusheamos los resultados obtenidos.
        array_push($result, ...$orderGrouped);
    }

    echo json_encode($result);
}

function obtenerPrioridades()
{
    $sql = "SELECT * FROM PRIORIDAD ORDER BY ID ASC;";
    $result = db_query($sql);
    echo json_encode($result);
}

function obtenerPedidosEnProceso()
{
    $sql = "SELECT P.*, C.ID AS CLIENTE_ID, C.TIPO AS CLIENTE_TIPO, C.NOMBRE AS CLIENTE_NOMBRE 
        FROM PEDIDOS P 
            JOIN CLIENTES C 
                ON P.CLIENTE_ID = C.ID 
                WHERE P.ESTADO IN ('PENDIENTE');";
    $result = db_query($sql);
    echo json_encode($result);
}

function obtenerPedidosParaEmpaquetar()
{
    $sql = "SELECT PROD.PEDIDO_ID AS PEDIDO_ID, CLI.NOMBRE, SUE.MARCA AS MARCA, COL.COLOR AS COLOR, SUE.TALLA AS TALLA, PROD.CANTIDAD AS CANTIDAD, SUE.CAP_EMPAQUETADO AS CAP_EMPAQUETADO
        FROM PRODUCCION PROD
            LEFT JOIN SUELAS SUE
                ON PROD.SUELA_ID = SUE.ID
            LEFT JOIN COLOR COL
                ON PROD.COLOR_ID = COL.ID
            LEFT JOIN PEDIDOS PED
                ON PED.ID = ?
            LEFT JOIN CLIENTES CLI
                ON CLI.ID = PED.CLIENTE_ID
        WHERE PROD.PEDIDO_ID = ?;";
    $result = db_query($sql, array($_POST['pedido_id'], $_POST['pedido_id']));
    echo json_encode($result);
}

function obtenerVentasCulminadas()
{
    $sql = "SELECT P.*, C.TIPO AS CLIENTE_TIPO, C.NOMBRE AS CLIENTE_NOMBRE 
                FROM PEDIDOS P 
                    JOIN CLIENTES C 
                        ON P.CLIENTE_ID = C.ID 
                WHERE P.ESTADO = 'COMPLETADO';";
    $result = db_query($sql);
    echo json_encode($result);
}

function obtenerAuditoriaControl()
{
    $sql = "SELECT AC.*, CONCAT (SUE.MARCA, ' '  , SUE.TALLA) AS REFERENCIA, PROD.ESTADO FROM AUDITORIA_CONTROL AC
                    JOIN PRODUCCION PROD
                        ON AC.PRODUCCION_ID = PROD.ID
                    JOIN SUELAS SUE
                        ON PROD.SUELA_ID = SUE.ID
                WHERE PROD.ESTADO NOT IN ('COMPLETADO');";
    $result = db_query($sql);
    echo json_encode($result);
}
