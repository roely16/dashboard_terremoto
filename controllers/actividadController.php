<?php
	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/database/ave/db.php';

	class ActividadController{

		protected $conn;

		function __construct(){

			$dbc = new Oracle();
			$this->conn = $dbc->connect();

		}

		function obtener_actividades($id_usuario){

			/* Obtener todos los roles de la persona */
			$query = "	SELECT CAT_ROLES.ID_ROL, CAT_ROLES.NOMBRE
						FROM CAT_PERSONA_ROL
						INNER JOIN CAT_ROLES
						ON CAT_PERSONA_ROL.ID_ROL = CAT_ROLES.ID_ROL
						WHERE ID_PERSONA = '$id_usuario'";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$roles = array();

			while ($data = oci_fetch_array($stid, OCI_ASSOC)) {

				$id_rol = $data["ID_ROL"];

				/* Por cada rol buscar las actividades que le corresponden */

				$query = "	SELECT CAT_ACTIVIDADES_ROL.NOMBRE,
							CAT_ACTIVIDADES_ROL.DESCRIPCION, CAT_ACTIVIDADES_ROL.ORDEN, IN_ACTIVIDADES.*
							FROM IN_ACTIVIDADES
							INNER JOIN CAT_ACTIVIDADES_ROL
							ON IN_ACTIVIDADES.ID_ACTIVIDAD = CAT_ACTIVIDADES_ROL.ID_ACTIVIDAD
							AND CAT_ACTIVIDADES_ROL.ID_TARJETA_ROL IN (

							    SELECT ID_CORRELATIVO
							    FROM CAT_MENSAJES_ROL
							    WHERE ID_ROL = '$id_rol'
							    AND ID_PROTOCOLO = 168

							)
							ORDER BY CAT_ACTIVIDADES_ROL.ORDEN ASC";

				$stid_ = oci_parse($this->conn, $query);
				oci_execute($stid_);

				$actividades = array();

				while ($data_ = oci_fetch_array($stid_, OCI_ASSOC)) {

					$actividades [] = $data_;

				}

				$data["ACTIVIDADES"] = $actividades;
				$roles [] = $data;

			}

			return $roles;

		}

		function detalle_actividad($id){

			$query = "	SELECT CAT_ACTIVIDADES_ROL.NOMBRE, CAT_ACTIVIDADES_ROL.DESCRIPCION,
						CAT_ACTIVIDADES_ROL.ORDEN, IN_ACTIVIDADES.ID_CORRELATIVO, TO_CHAR(IN_ACTIVIDADES.FECHA_INI, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_INI, TO_CHAR(IN_ACTIVIDADES.FECHA_FIN, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_FIN, IN_ACTIVIDADES.COMENTARIO
						FROM IN_ACTIVIDADES
						INNER JOIN CAT_ACTIVIDADES_ROL
						ON IN_ACTIVIDADES.ID_ACTIVIDAD = CAT_ACTIVIDADES_ROL.ID_ACTIVIDAD
						WHERE IN_ACTIVIDADES.ID_CORRELATIVO = '$id'";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$actividad = oci_fetch_array($stid, OCI_ASSOC);

			return $actividad;

		}

		function finalizar_actividad($request){

			$id_correlativo = $request["ID_CORRELATIVO"];
			$comentario = $request["COMENTARIO"];
			$usuario = $request["USUARIO"];

			$query = "UPDATE IN_ACTIVIDADES SET FECHA_FIN = SYSDATE, COMENTARIO = UPPER('$comentario') WHERE ID_CORRELATIVO = $id_correlativo";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$actividades = $this->obtener_actividades($usuario);

			return $actividades;

		}
	}


?>
