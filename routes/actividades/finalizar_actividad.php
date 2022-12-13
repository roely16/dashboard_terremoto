<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/actividadController.php';

	$_POST = json_decode(file_get_contents('php://input'), true);

	$actividad_ctrl = new ActividadController();

	$data = $actividad_ctrl->finalizar_actividad($_POST);

	echo json_encode($data);

?>
