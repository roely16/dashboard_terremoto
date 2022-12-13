<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/albergueController.php';

	$_POST = json_decode(file_get_contents('php://input'), true);

	$albergues_ctrl = new AlbergueController();

	$data = $albergues_ctrl->registrarPersonas($_POST);

	echo json_encode($data);

?>
