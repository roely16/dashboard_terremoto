<?php  

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/servicioController.php';

	$servicio_ctrl = new ServicioController();

	$data = $servicio_ctrl->detalles_reporte($_REQUEST["id"]);

	echo json_encode($data);
	
?>