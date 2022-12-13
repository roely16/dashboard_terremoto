<?php  

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/database/ave/db.php';

	class DamageController{
		
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
				
				$query = "	SELECT IN_DANIOS.ID_CORRELATIVO, IN_DANIOS.ZONA, 
							IN_DANIOS.CANTIDAD_ESTIMADA, 
							IN_DANIOS.CANTIDAD_CONFIRMADA, 
							TO_CHAR(FECHA_REPORTADO, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_REPORTADO, 
							TO_CHAR(FECHA_AVALADO, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_AVALADO,
							CAT_DANIOS.NOMBRE AS REPORTE, CAT_DANIOS.TIPO AS TIPO_REPORTE,
							IN_DANIO_SERVICIO_PETICION.ID_DANIO AS PETICION_REALIZADA
							FROM IN_DANIOS 
							INNER JOIN CAT_DANIOS
							ON IN_DANIOS.ID_DANIO = CAT_DANIOS.ID
							LEFT JOIN IN_DANIO_SERVICIO_PETICION
							ON IN_DANIO_SERVICIO_PETICION.ID_DANIO = IN_DANIOS.ID_CORRELATIVO
							ORDER BY IN_DANIOS.ZONA ASC, IN_DANIOS.ID_CORRELATIVO DESC";

			}else{

				$str_region = implode(",", $region);

				$query = "	SELECT IN_DANIOS.ID_CORRELATIVO, IN_DANIOS.ZONA, 
							IN_DANIOS.CANTIDAD_ESTIMADA, 
							IN_DANIOS.CANTIDAD_CONFIRMADA, 
							TO_CHAR(FECHA_REPORTADO, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_REPORTADO, 
							TO_CHAR(FECHA_AVALADO, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_AVALADO,
							CAT_DANIOS.NOMBRE AS REPORTE, CAT_DANIOS.TIPO AS TIPO_REPORTE,
							IN_DANIO_SERVICIO_PETICION.ID_DANIO AS PETICION_REALIZADA
							FROM IN_DANIOS 
							INNER JOIN CAT_DANIOS
							ON IN_DANIOS.ID_DANIO = CAT_DANIOS.ID
							LEFT JOIN IN_DANIO_SERVICIO_PETICION
							ON IN_DANIO_SERVICIO_PETICION.ID_DANIO = IN_DANIOS.ID_CORRELATIVO
							WHERE IN_DANIOS.ZONA IN ($str_region)
							ORDER BY IN_DANIOS.ZONA ASC, IN_DANIOS.ID_CORRELATIVO DESC";

			}

			
			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$reportes = array();

			while ($data = oci_fetch_array($stid, OCI_ASSOC)) {
			
				$reportes [] = $data;

			}

			return $reportes;

		}

		function obtener_tipos($tipo, $zona){

			$query = "SELECT * FROM CAT_DANIOS WHERE TIPO = '$tipo'";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$tipos = array();

			while ($data = oci_fetch_array($stid, OCI_ASSOC)) {
			
				$tipos [] = $data;

			}

			return $tipos;

		}

		function realizar_reporte($request){

			$tipo_reporte = $request["TIPO_REPORTE"];
			$incidente = $request["INCIDENTE"];
			$zona = $request["ZONA"];
			$observacion = $request["OBSERVACION"];
			$cantidad_estimada = $request["CANTIDAD_ESTIMADA"];
			$observacion = $request["OBSERVACION"];
			$region = $request["REGION"];

			$query = "INSERT INTO IN_DANIOS (ID_INCIDENTE, ID_DANIO, ZONA, CANTIDAD_ESTIMADA, FECHA_REPORTADO, OBSERVACION) VALUES ('61', '$incidente', '$zona', '$cantidad_estimada', SYSDATE, UPPER('$observacion'))";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$reportes = $this->obtener_reportes($region);

			return $reportes;

		}

		function detalle_reporte($id){

			$query = "	SELECT IN_DANIOS.ID_CORRELATIVO, IN_DANIOS.ZONA, 
							IN_DANIOS.CANTIDAD_ESTIMADA, 
							IN_DANIOS.CANTIDAD_CONFIRMADA,
							TO_CHAR(FECHA_REPORTADO, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_REPORTADO, 
							TO_CHAR(FECHA_AVALADO, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_AVALADO,
							IN_DANIOS.OBSERVACION, IN_DANIOS.OBSERVACION_CONFIRMACION,
							CAT_DANIOS.NOMBRE AS REPORTE, CAT_DANIOS.TIPO AS TIPO_REPORTE,
							IN_DANIO_SERVICIO_PETICION.ID_DANIO AS PETICION_REALIZADA
							FROM IN_DANIOS 
							INNER JOIN CAT_DANIOS
							ON IN_DANIOS.ID_DANIO = CAT_DANIOS.ID
							LEFT JOIN IN_DANIO_SERVICIO_PETICION
							ON IN_DANIO_SERVICIO_PETICION.ID_DANIO = IN_DANIOS.ID_CORRELATIVO
							WHERE IN_DANIOS.ID_CORRELATIVO = '$id'";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$reporte = oci_fetch_array($stid, OCI_ASSOC);

			return $reporte;

		}

		function confirmar_reporte($request){

			$id_correlativo = $request["ID_CORRELATIVO"];
			$cantidad_confirmada = $request["CANTIDAD_CONFIRMADA"];
			$observacion_confirmacion = $request["OBSERVACION_CONFIRMACION"];
			$region = $request["REGION"];

			$query = "UPDATE IN_DANIOS SET CANTIDAD_CONFIRMADA = '$cantidad_confirmada', FECHA_AVALADO = SYSDATE, OBSERVACION_CONFIRMACION = UPPER('$observacion_confirmacion') WHERE ID_CORRELATIVO = '$id_correlativo'";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$reportes = $this->obtener_reportes($region);

			return $reportes;

		}
	}

?>