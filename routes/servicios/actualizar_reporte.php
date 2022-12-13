<?php  

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/servicioController.php';

	$_POST = json_decode(file_get_contents('php://input'), true);

	$servicio_ctrl = new ServicioController();

	$data = $servicio_ctrl->actualizar_reporte($_POST);

	echo json_encode($data);

?>