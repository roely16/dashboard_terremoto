<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/database/ave/db.php';

	class LoginController
	{
		private $conn;

		function __construct()
		{
			$dbc = new Oracle();
			$this->conn = $dbc->connect();
		}

		function login($request){

			$usuario = $request["user"];
			$password = $request["password"];

			$query = "	SELECT CAT_USUARIOS.USUARIO AS USUARIO, CAT_USUARIOS.ID_PERSONA AS ID_PERSONA, CAT_PERSONAS.NOMBRE AS NOMBRE, CAT_PERSONA_ROL.ID_ROL 
							FROM CAT_USUARIOS
							INNER JOIN CAT_PERSONAS
							ON CAT_USUARIOS.ID_PERSONA = CAT_PERSONAS.ID_PERSONA
							INNER JOIN CAT_PERSONA_ROL
							ON CAT_USUARIOS.ID_PERSONA = CAT_PERSONA_ROL.ID_PERSONA
			   			WHERE UPPER(RTRIM(CAT_USUARIOS.USUARIO)) = '". strtoupper(trim($usuario)) ."'
				 			AND UPPER(RTRIM(DESENCRIPTAR(CAT_USUARIOS.PASS))) = '". strtoupper(trim($password)) ."'";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$usuario = oci_fetch_array($stid, OCI_ASSOC);

			return $usuario;

		}
	}

?>
