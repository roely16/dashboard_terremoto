<?php  

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/damageController.php';

	$damage_ctrl = new DamageController();

	$data = $damage_ctrl->detalle_reporte($_REQUEST["id"]);

	echo json_encode($data);

?>