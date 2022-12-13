<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/peticionController.php';

	$peticion_ctrl = new PeticionController();

	$data = $peticion_ctrl->listado_insumos($_REQUEST["tipo_insumo"], $_REQUEST["id_usuario_ave"]);

	echo json_encode($data);

?>
