<?php  

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/damageController.php';

	$_POST = json_decode(file_get_contents('php://input'), true);

	$damage_ctrl = new DamageController();

	$data = $damage_ctrl->realizar_reporte($_POST);

	echo json_encode($data);

?>