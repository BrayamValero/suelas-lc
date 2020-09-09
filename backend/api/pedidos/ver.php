<?php
session_start();
require_once "../db.php";


// Si se ejecuta un Request, ya sea GET o POST se ejecuta el código.
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $pedido_id = $_POST['pedido_id'];

    if(isset($pedido_id)){

        $output = '';

        // 1. Primero buscamos el PEDIDO dependiendo del ID dado.
        $sql = "SELECT ID AS PROD_ID, SUELA_ID, SERIE_ID, COLOR_ID, CANTIDAD, URGENTE FROM PRODUCCION WHERE PEDIDO_ID = ?;";
        $datosPedido = db_query($sql, array($pedido_id));

        // 2. Ahora filtramos las SERIE_ID y COLOR_ID.
        $sql = "SELECT SERIE_ID, COLOR_ID FROM PRODUCCION WHERE PEDIDO_ID = ?;";
        $datosSeries = db_query($sql, array($pedido_id));
    
        $datosSeries = array_unique($datosSeries, SORT_REGULAR);


    }

}