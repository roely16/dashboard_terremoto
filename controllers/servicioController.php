<?php  

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/database/ave/db.php';

	class ServicioController{
		
		protected $conn;

		function __construct(){
			
			$dbc = new Oracle();
			$this->conn = $dbc->connect();

		}

		function obtener_reportes($region){

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

				$query = "	SELECT * 
								FROM CAT_ZONAS";

			}else{

				$str_region = implode(",", $region);

				$query = "	SELECT * 
							FROM CAT_ZONAS
							WHERE ZONA IN ($str_region)";

			}
			
			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$zonas = array();

			while ($data = oci_fetch_array($stid, OCI_ASSOC)) {
				
				$zona = $data["ZONA"];

				/* Obtener todos los servicios */
				$query = "SELECT ID FROM CAT_SERVICIOS ORDER BY ID ASC";
				$stid_ = oci_parse($this->conn, $query);
				oci_execute($stid_);

				$servicios = array();

				while ($data_ = oci_fetch_array($stid_, OCI_ASSOC)) {
				
					$id_servicio = $data_["ID"];

					/* Por cada servicio buscar el reporte */
					$query = "	SELECT IN_SERVICIOS.ID_CORRELATIVO, 
								TO_CHAR(IN_SERVICIOS.FECHA_REPORTE, 'DD/MM/YYYY HH24:MI:SS') AS 	FECHA_REPORTE, 
								IN_SERVICIOS.ZONA, IN_SERVICIOS.PORCENTAJE, IN_SERVICIOS.OBSERVACION, 
								IN_SERVICIOS.OBSERVACION_CONFIRMACION, IN_SERVICIOS.PORCENTAJE_CONFIRMADO, 
								IN_SERVICIOS.FECHA_CONFIRMACION, CAT_SERVICIOS.NOMBRE AS SERVICIO, 
								CAT_SERVICIOS.ICONO, IN_DANIO_SERVICIO_PETICION.ID_SERVICIO AS PETICION_SERVICIO
								FROM IN_SERVICIOS
								INNER JOIN CAT_SERVICIOS
								ON IN_SERVICIOS.ID_SERVICIO = CAT_SERVICIOS.ID
								LEFT JOIN IN_DANIO_SERVICIO_PETICION
								ON IN_SERVICIOS.ID_CORRELATIVO = IN_DANIO_SERVICIO_PETICION.ID_SERVICIO
								WHERE IN_SERVICIOS.ZONA = '$zona'
								AND IN_SERVICIOS.ID_SERVICIO = '$id_servicio'
								ORDER BY IN_SERVICIOS.ID_CORRELATIVO ASC";

					$stid2 = oci_parse($this->conn, $query);
					oci_execute($stid2);

					$reporte = oci_fetch_array($stid2, OCI_ASSOC);
					$data_["REPORTE"] = $reporte; 

					$servicios [] = $data_;

				}

				$data["SERVICIOS"] = $servicios;

				$zonas [] = $data;

			}

			return $zonas;

		}

		function realizar_reporte($request){

			$porcentaje = $request["PORCENTAJE"];
			$servicio = $request["SERVICIO"];
			$zona = $request["ZONA"];
			$observacion = $request["OBSERVACION"];
			$region = $request["REGION"];

			$query = "INSERT INTO IN_SERVICIOS (ID_INCIDENTE, ID_SERVICIO, FECHA_REPORTE, ZONA, PORCENTAJE, OBSERVACION) VALUES (61, '$servicio', SYSDATE, '$zona', '$porcentaje', UPPER('$observacion'))";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$reportes = $this->obtener_reportes($region);

			return $reportes;

		}

		function obtener_servicios($zona){

			/* Obtener los servicios que no han sido reportados */
			$query = "	SELECT *
						FROM CAT_SERVICIOS
						WHERE ID NOT IN (
						    SELECT ID_SERVICIO 
						    FROM IN_SERVICIOS 
						    WHERE ID_INCIDENTE = 61 AND
						    ZONA = '$zona'
						)
						ORDER BY ID ASC";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$servicios = array();

			while ($data = oci_fetch_array($stid, OCI_ASSOC)) {
			
				$servicios [] = $data;

			}

			return $servicios;

		}

		function detalles_reporte($id){

			$query = "	SELECT IN_SERVICIOS.ID_CORRELATIVO, 
							TO_CHAR(IN_SERVICIOS.FECHA_REPORTE, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_REPORTE, 
							IN_SERVICIOS.ZONA, IN_SERVICIOS.PORCENTAJE, IN_SERVICIOS.OBSERVACION, 
							IN_SERVICIOS.OBSERVACION_CONFIRMACION, IN_SERVICIOS.PORCENTAJE_CONFIRMADO, 
							CAT_SERVICIOS.NOMBRE, CAT_SERVICIOS.ICONO, IN_DANIO_SERVICIO_PETICION.ID_SERVICIO AS PETICION_SERVICIO
							FROM IN_SERVICIOS
							INNER JOIN CAT_SERVICIOS 
							ON IN_SERVICIOS.ID_SERVICIO = CAT_SERVICIOS.ID
							LEFT JOIN IN_DANIO_SERVICIO_PETICION
							ON IN_SERVICIOS.ID_CORRELATIVO = IN_DANIO_SERVICIO_PETICION.ID_SERVICIO
							WHERE ID_CORRELATIVO = '$id'";

			$stid = oci_parse($this->conn, $query)			;
			oci_execute($stid);

			$reporte = oci_fetch_array($stid, OCI_ASSOC);

			return $reporte;

		}

		function actualizar_reporte($request){

			$id_correlativo = $request["ID_CORRELATIVO"];
			$porcentaje = $request["PORCENTAJE"];
			$porcentaje_confirmacion = $request["PORCENTAJE_CONFIRMADO"];
			$region = $request["REGION"];

			if (array_key_exists('OBSERVACION', $request)) {
			
				$observacion = $request["OBSERVACION"];

			}else{

				$observacion = '';

			}

			if (array_key_exists('OBSERVACION_CONFIRMACION', $request)) {
			
				$observacion_confirmacion = $request["OBSERVACION_CONFIRMACION"];

			}else{

				$observacion_confirmacion = '';

			}

			$query = "UPDATE IN_SERVICIOS SET PORCENTAJE = '$porcentaje', OBSERVACION = UPPER('$observacion'), OBSERVACION_CONFIRMACION = UPPER('$observacion_confirmacion'), PORCENTAJE_CONFIRMADO = '$porcentaje_confirmacion' WHERE ID_CORRELATIVO = '$id_correlativo'";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$reportes = $this->obtener_reportes($region);

			return $reportes;

		}

		function obtener_zonas($region){

			if ($region == 1) {
			
				$region = array(1, 4, 5, 8, 9, 11);

			}else if($region == 2){

				$region = array(2, 3, 6, 7, 10, 19);

			}else if($region == 3){

				$region = array(12, 13, 14, 15, 16, 21);

			}else if($region == 4){

				$region = array(17, 18, 24, 25);

			}

			return $region;

		}

	}

?>