<?php
session_start();
require_once "../db.php";

$data = json_decode($_POST['data']);
$materiales = json_decode($_POST['materiales']);

$formula_id = $data->formulaId;

$material = trim(strtoupper($data->material));
$turno = trim(strtoupper($data->turno));

$sql = "SELECT U.ID, U.NOMBRE FROM OPERARIOS O JOIN USUARIOS U ON O.USUARIO_ID = U.ID WHERE O.MATERIAL = ? AND O.TURNO = ?;";
$data = array($material, $turno);
$result = db_query($sql, $data);

if(empty($result)) {
	echo json_encode('asignar_operario');
} else {
	// Este es el operario que estaba de turno al momento de realizar la entrega
	$usuario_operario_id = $result[0]['ID'];

	$usuario_molinero_id = $_SESSION['USUARIO']['ID'];

	$total = 0;

	foreach ($materiales as $dato) {
		$total = $total + $dato->cantidad;
	}

	$sql = "INSERT INTO ENTREGA_MATERIAL VALUES(NULL, ?, ?, ?, ?, ?, ?, NOW(), 'PENDIENTE');";
	$data = array($formula_id, $usuario_molinero_id, $usuario_operario_id, $material, $turno, $total);

	db_query($sql, $data);

	$id = db_query("SELECT MAX(ID) AS ID FROM ENTREGA_MATERIAL;")[0]['ID'];


	foreach ($materiales as $dato) {
		$materia_id = $dato->materiaId;
		$cantidad = trim($dato->cantidad);


		$sql = "INSERT INTO MATERIALES_ENTREGADOS VALUES (NULL, ?, ?, ?)";
		$data = array($id, $materia_id, $cantidad);
		db_query($sql, $data);
	}

	echo json_encode(array($id, $result[0]['NOMBRE'], $result[0]['ID']));
}