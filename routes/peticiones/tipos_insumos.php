<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/peticionController.php';

	$peticion_ctrl = new PeticionController();

	$data = $peticion_ctrl->tipos_insumos();

	echo json_encode($data);

?>
