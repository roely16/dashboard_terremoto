<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/actividadController.php';

	$actividad_ctrl = new ActividadController();

	$data = $actividad_ctrl->detalle_actividad($_REQUEST["id"]);

	echo json_encode($data);

?>
