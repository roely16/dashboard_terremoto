<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/peticionController.php';

	$peticion_ctrl = new PeticionController();

	$data = $peticion_ctrl->obtener_peticiones_recibidas($_REQUEST["id_usuario"]);

	echo json_encode($data);

?>
