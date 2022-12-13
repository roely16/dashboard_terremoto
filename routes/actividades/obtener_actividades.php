<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/actividadController.php';

	$actividad_ctrl = new ActividadController();

	$data = $actividad_ctrl->obtener_actividades($_REQUEST["id_usuario"]);

	echo json_encode($data);

?>
