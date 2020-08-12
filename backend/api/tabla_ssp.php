<?php
// usleep(350000);

/*
 * ESTA TABLA RECIBE 3 PARAMETROS GET QUE SE DEBEN ENVIAR
 * $_GET['tabla'] que es el nombre de la tabla
 * $_GET['pk'] es la llave primaria de la tabla
 * $_GET['columnas'] es un array de objetos con la siguiente estructura
 * solo se deben mandar la cantidad de columnas que sale en la tabla HTML
 * y si la tabla tiene una ultima columna tipo de opciones, lo que se debe hacer es
 * ponerle un objeto mas al array con cualquier cosa, y en el javascript de la tabla
 * se maneja lo que se tenga que hacer para poner la opcion
 *
 *      [nombre: 'xyz', index: 123456789, *opcional*, string]
 *      el nombre corresponde a el nombre de la columna en la BD
 *      el index corresponde a en que posicion de la tabla HTML se va a ligar la
 *      columna de la tabla
 *      el string es por si quieres que te devuelva algo formateado
 *      string: 'xyz = %REEMPLAZO%' el reemplazara %REEMPLAZO% con el nombre de la columna que pases
 *
 *      ejemplo:
 *      columna TIPO_PAGO, se llama asi en la BD
 *      en la tabla HTML se llama forma pago, y de izquierda a derecha esta en la posicion
 *      3 (empezando desde 0),
 *      [nombre: 'TIPO_PAGO', index: 3]
 */

/*
 * DataTables example server-side processing script.
 *
 * Please note that this script is intentionally extremely simple to show how
 * server-side processing can be implemented, and probably shouldn't be used as
 * the basis for a large complex system. It is suitable for simple use cases as
 * for learning.
 *
 * See http://datatables.net/usage/server-side for full details on the server-
 * side processing requirements of DataTables.
 *
 * @license MIT - http://datatables.net/license_mit
 */

/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * Easy set variables
 */

// DB table to use
$table = $_GET['tabla'];

// Table's primary key
$primaryKey = $_GET['pk'];

// Array of database columns which should be read and sent back to DataTables.
// The `db` parameter represents the column name in the database, while the `dt`
// parameter represents the DataTables column identifier. In this case simple
// indexes
$columns = array();

foreach ($_GET['columnas'] as $columna) {
    if (isset($columna['string'])) {
        $string = $columna['string'];

        array_push(
            $columns,
            array(
                'db' => $columna['nombre'],
                'dt' => $columna['index'],
                'formatter' => function ($d, $row) {
                    global $string;
                    return str_replace("%REEMPLAZO%", $d, $string);
                }
            )
        );
    } else {
        array_push($columns, array('db' => $columna['nombre'], 'dt' => $columna['index']));
    }
}

// SQL server connection information
$sql_details = array(
    'user' => 'root',
    'pass' => '',
    'db' => 'SUELAS',
    'host' => 'localhost'
);


/* * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * * *
 * If you just want to use the basic configuration for DataTables with PHP
 * server-side, there is no need to edit below this line.
 */

require('ssp.class.php');

if (isset($_GET['where'])) {
    echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns, $_GET['where'])
    );
} else {
    echo json_encode(
        SSP::simple($_GET, $sql_details, $table, $primaryKey, $columns)
    );
}