<?php  

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/damageController.php';

	$damage_ctrl = new DamageController();

	$data = $damage_ctrl->obtener_tipos($_REQUEST["tipo_reporte"], $_REQUEST["zona"]);

	echo json_encode($data);

?>