<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/database/ave/db.php';

	/**
	 *
	 */
	class AlbergueController{

		protected $conn;

		function __construct(){

			$dbc = new Oracle();
			$this->conn = $dbc->connect();

		}

		function obtener($region){

			if ($region == 1) {
			
				$region = array(1, 4, 5, 8, 9, 11);

			}else if($region == 2){

				$region = array(2, 3, 6, 7, 10, 19);

			}else if($region == 3){

				$region = array(12, 13, 14, 15, 16, 21);

			}else if($region == 4){

				$region = array(17, 18, 24, 25);

			}

			if ($region == '5') {

				$query = "	SELECT CAT_ALBERGUES.*, IN_ALBERGUES.USO, IN_ALBERGUES.ID_CORRELATIVO
								FROM IN_ALBERGUES
								INNER JOIN CAT_ALBERGUES
								ON IN_ALBERGUES.ID_ALBERGUE = CAT_ALBERGUES.ID_ALBERGUE
								ORDER BY IN_ALBERGUES.ID_CORRELATIVO ASC";

			}else{

				$str_region = implode(",", $region);

				$query = "	SELECT CAT_ALBERGUES.*, IN_ALBERGUES.USO, IN_ALBERGUES.ID_CORRELATIVO
								FROM IN_ALBERGUES
								INNER JOIN CAT_ALBERGUES
								ON IN_ALBERGUES.ID_ALBERGUE = CAT_ALBERGUES.ID_ALBERGUE
								WHERE CAT_ALBERGUES.ZONA IN ($str_region)
								ORDER BY IN_ALBERGUES.ID_CORRELATIVO ASC";

			}

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$albergues = array();

			while ($data = oci_fetch_array($stid, OCI_ASSOC)) {

				$uso = $data["USO"];
				$capacidad = $data["CAPACIDAD"];
				$porcentaje = ($uso * 100) / $capacidad;

				$data["PORCENTAJE_USO"] = "width: ".$porcentaje."%";
				$data["PORCENTAJE"] = round($porcentaje);

				$albergues [] = $data;


			}

			return $albergues;

		}

		function obtener_zonas(){

			$query = "	SELECT ZONA
						FROM CAT_ALBERGUES
						GROUP BY ZONA ORDER BY ZONA ASC";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$zona = array();

			while ($data = oci_fetch_array($stid, OCI_ASSOC)) {

				$zonas [] = $data["ZONA"];

			}

			return $zonas;

		}

		function albergues_zona($zona){

			$query = "	SELECT *
						FROM CAT_ALBERGUES
						WHERE ZONA = '$zona'
						AND ID_ALBERGUE NOT IN (
						    SELECT ID_ALBERGUE
						    FROM IN_ALBERGUES
						)";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$albergues = array();

			while ($data = oci_fetch_array($stid, OCI_ASSOC)) {

				$albergues [] = $data;

			}

			return $albergues;

		}

		function habilitarAlbergue($request){

			$id_albergue = $request["ALBERGUE_HABILITADO"];
			$region = $request["REGION"];

			$query = "SELECT ID_INCIDENTE FROM IN_INCIDENTES";
			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$incidente = oci_fetch_array($stid, OCI_ASSOC);
			$id_incidente = $incidente["ID_INCIDENTE"];

			$query = "INSERT INTO IN_ALBERGUES (ID_INCIDENTE, ID_ALBERGUE, USO) VALUES ('$id_incidente', '$id_albergue', 0)";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$albergues = $this->obtener($region);

			return $albergues;

		}

		function capacidadAlbergue($id){

			$query = "	SELECT CAT_ALBERGUES.*, IN_ALBERGUES.USO, IN_ALBERGUES.ID_CORRELATIVO
						FROM IN_ALBERGUES
						INNER JOIN CAT_ALBERGUES
						ON IN_ALBERGUES.ID_ALBERGUE = CAT_ALBERGUES.ID_ALBERGUE
						WHERE ID_CORRELATIVO = '$id'";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$capacidad = oci_fetch_array($stid, OCI_ASSOC);

			$capacidad["CAPACIDAD"] = intval($capacidad["CAPACIDAD"]);
			$capacidad["USO"] = intval($capacidad["USO"]); 

			return $capacidad;

		}

		function registrarPersonas($request){

			$tipo_registro = $request["TIPO_REGISTRO"];
			$cantidad_personas = $request["PERSONAS_AGREGAR"];
			$uso = $request["USO"];
			$id_correlativo = $request["ID_CORRELATIVO"];
			$region = $request["REGION"];

			if ($tipo_registro == 1) {
				
				//Se agrega
				$total = $cantidad_personas + $uso;

			}else{

				//Se retira
				$total = $uso - $cantidad_personas;

			}

			$query = "UPDATE IN_ALBERGUES SET USO = $total WHERE ID_CORRELATIVO = $id_correlativo";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$albergues = $this->obtener($region);

			return $albergues;

		}
	}


?>
