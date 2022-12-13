<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/peticionController.php';

	$_POST = json_decode(file_get_contents('php://input'), true);

	$peticion_ctrl = new PeticionController();

	$data = $peticion_ctrl->peticion_atendida($_POST);

	echo json_encode($data);

?>
