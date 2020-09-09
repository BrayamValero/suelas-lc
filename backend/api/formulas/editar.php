<?php
// POST METHOD - EDITAR.PHP
session_start();
require_once "../db.php";
// echo '<pre>'; print_r($_POST); echo '</pre>';

$datos = json_decode($_POST['datos']);
$materiales = json_decode($_POST['materiales']);

$formula_id = trim($datos->formula_id);
$formula = trim(strtoupper($datos->formula));
$material = trim(strtoupper($datos->material));

// Si alguno de los campos está vacio no se agrega a la DB.
if (!empty($formula_id) && !empty($formula) && !empty($material) && !empty($materiales)) {
    
    echo "Success";

    // 1. Editamos las formulas.
    $sql = "UPDATE FORMULAS SET NOMBRE = ?, MATERIAL = ? WHERE ID = ?;";
    $data = array($formula, $material, $formula_id);
    db_query($sql, $data);

    // 2. Borramos las antiguas recetas.
    $sql = "DELETE FROM RECETAS WHERE FORMULA_ID = ?;";
    $data = array($formula_id);
    db_query($sql, $data);

    // 3. Agregamos las nuevas recetas
    foreach ($materiales as $material) {
        $sql = "INSERT INTO RECETAS VALUES (NULL, ?, ?);";
        $data = array($formula_id, $material->ID);
        db_query($sql, $data);
    }

} else {

    echo "Error";
    
}