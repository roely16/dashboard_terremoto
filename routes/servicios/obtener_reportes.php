<?php  

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/servicioController.php';

	$servicio_ctrl = new ServicioController();

	$data = $servicio_ctrl->obtener_reportes($_REQUEST["region"]);

	echo json_encode($data);

?>