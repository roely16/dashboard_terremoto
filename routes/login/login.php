<?php  

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/controllers/LoginController.php';

	$_POST = json_decode(file_get_contents('php://input'), true);

	$login_ctrl = new LoginController();

	$data = $login_ctrl->login($_POST);

	echo json_encode($data);

?>