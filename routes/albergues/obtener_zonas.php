<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/albergueController.php';

	$albergues_ctrl = new AlbergueController();

	$data = $albergues_ctrl->obtener_zonas();

	echo json_encode($data);

?>
