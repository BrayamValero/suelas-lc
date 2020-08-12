<?php
// POST METHOD - AGREGAR.PHP
session_start();
require_once "../db.php";
// echo '<pre>'; print_r($_POST); echo '</pre>';

$datos = json_decode($_POST['datos']);
$materiales = json_decode($_POST['materiales']);

$formula = trim(strtoupper($datos->formula));
$material = trim(strtoupper($datos->material));

// Si alguno de los campos está vacio no se agrega a la DB.
if (!empty($formula) && !empty($material) && !empty($materiales)) {
    
    echo "Success";

    // 1. Agregamos a Formulas
    $sql = "INSERT INTO FORMULAS VALUES(NULL, ?, ?, 'PENDIENTE');";
    $data = array($formula, $material);
    db_query($sql, $data);

    // 2. Obtenemos el ID Máximo de Formulas para usarlo en Recetas.
    $sql = "SELECT MAX(ID) AS ID FROM FORMULAS;";
    $id = db_query($sql)[0]['ID'];

    // 3. Agregamos a Recetas
    foreach ($materiales as $material) {
        $sql = "INSERT INTO RECETAS VALUES (NULL, ?, ?);";
        $data = array($id, $material->ID);
        db_query($sql, $data);
    }

} else {

    echo "Error";
    
}