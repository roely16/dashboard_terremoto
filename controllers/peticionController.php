<?php

	require_once $_SERVER['DOCUMENT_ROOT'] . '/apps/dashboard_terremoto/database/ave/db.php';

	class PeticionController{

		protected $conn;

		function __construct(){

			$dbc = new Oracle();
			$this->conn = $dbc->connect();

		}

		function obtener_peticiones($id_usuario){

			$query = "	SELECT IN_SOLICITUDES.ID_CORRELATIVO,
							TO_CHAR(IN_SOLICITUDES.FECHA_SOLICITUD, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_SOLICITUD, 
							IN_SOLICITUDES.FECHA_ATENDIDO, IN_SOLICITUDES.OTRO_INSUMO, IN_SOLICITUDES.CANTIDAD_SOLICITADA, 
							IN_SOLICITUDES.CANTIDAD_DESPACHADA, IN_SOLICITUDES.CONTADOR_DEVOLUCIONES, IN_SOLICITUDES.REASIGNADA, 
							IN_SOLICITUDES.COMENTARIO_DEVOLUCION, IN_SOLICITUDES.ZONA,
							CAT_INSUMOS_TIPOS.DESCRIPCION AS SOLICITUD, CAT_ROLES.NOMBRE AS RESPONSABLE
							FROM IN_SOLICITUDES
							LEFT JOIN CAT_INSUMOS_TIPOS
							ON IN_SOLICITUDES.TIPO_INSUMO = CAT_INSUMOS_TIPOS.ID_TIPO_INSUMO
							INNER JOIN CAT_ROLES
							ON IN_SOLICITUDES.RESPONSABLE = CAT_ROLES.ID_ROL
							WHERE SOLICITADO_POR = '$id_usuario'
							ORDER BY IN_SOLICITUDES.ID_CORRELATIVO ASC";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$peticiones = array();

			while ($data = oci_fetch_array($stid, OCI_ASSOC)) {

				$cantidad_solicitada = intval($data["CANTIDAD_SOLICITADA"]);
				if (array_key_exists("CANTIDAD_DESPACHADA", $data)) {
				
					$cantidad_despachada = intval($data["CANTIDAD_DESPACHADA"]);
					$data["CANTIDAD_DESPACHADA"] = $cantidad_despachada;

				}
				$data["CANTIDAD_SOLICITADA"] = $cantidad_solicitada;
				$peticiones [] = $data;

			}

			return $peticiones;

		}

		function obtener_peticiones_recibidas($id_usuario){

			$query = "	SELECT IN_SOLICITUDES.ID_CORRELATIVO, 
							TO_CHAR(IN_SOLICITUDES.FECHA_SOLICITUD, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_SOLICITUD,
							TO_CHAR(IN_SOLICITUDES.FECHA_ATENDIDO, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_ATENDIDO, 
							IN_SOLICITUDES.OTRO_INSUMO, IN_SOLICITUDES.CANTIDAD_SOLICITADA, IN_SOLICITUDES.CANTIDAD_DESPACHADA, 
							IN_SOLICITUDES.CONTADOR_DEVOLUCIONES, IN_SOLICITUDES.REASIGNADA, IN_SOLICITUDES.COMENTARIO_DEVOLUCION, IN_SOLICITUDES.ZONA,
							CAT_INSUMOS_TIPOS.DESCRIPCION AS SOLICITUD, CAT_PERSONAS.NOMBRE AS SOLICITANTE
							FROM IN_SOLICITUDES
							LEFT JOIN CAT_INSUMOS_TIPOS
							ON IN_SOLICITUDES.TIPO_INSUMO = CAT_INSUMOS_TIPOS.ID_TIPO_INSUMO
							INNER JOIN CAT_PERSONAS
							ON IN_SOLICITUDES.SOLICITADO_POR = CAT_PERSONAS.ID_PERSONA
							WHERE RESPONSABLE IN (

							    SELECT ID_ROL
							    FROM CAT_PERSONA_ROL
							    WHERE ID_PERSONA = '$id_usuario'

							)";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$solicitudes_recibidas = array();

			while ($data = oci_fetch_array($stid, OCI_ASSOC)) {

				$cantidad_solicitada = intval($data["CANTIDAD_SOLICITADA"]);
				if (array_key_exists("CANTIDAD_DESPACHADA", $data)) {
				
					$cantidad_despachada = intval($data["CANTIDAD_DESPACHADA"]);
					$data["CANTIDAD_DESPACHADA"] = $cantidad_despachada;

				}
				$data["CANTIDAD_SOLICITADA"] = $cantidad_solicitada;
				
				$solicitudes_recibidas [] = $data;

			}

			return $solicitudes_recibidas;

		}

		function tipos_insumos(){

			$query = "	SELECT ID_TIPO_INSUMO AS ID, DESCRIPCION AS NOMBRE
						FROM CAT_INSUMOS_TIPOS
						WHERE DEPENDE = 0";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$tipos = array();

			while ($data = oci_fetch_array($stid, OCI_ASSOC)) {

				$tipos [] = $data;

			}

			return $tipos;

		}

		function listado_insumos($tipo_insumo, $id_usuario_ave){

			/* Listado de insumos en base al tipo */
			$query = "	SELECT ID_TIPO_INSUMO AS ID, DESCRIPCION AS NOMBRE
							FROM CAT_INSUMOS_TIPOS
							WHERE DEPENDE = '$tipo_insumo'";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$listado = array();

			while ($data = oci_fetch_array($stid, OCI_ASSOC)) {

				$listado [] = $data;

			}

			/* Listado de roles */
			$roles = $this->obtener_roles($id_usuario_ave);

			return array($listado, $roles);

		}

		function obtener_roles($id_usuario_ave){

			$query = "	SELECT ID_ROL AS ID, NOMBRE
							FROM CAT_ROLES
							WHERE UPPER(SIMULACRO_TERREMOTO) = 'S'
							AND ID_ROL NOT IN (

								SELECT ID_ROL
							   FROM CAT_PERSONA_ROL
							   WHERE ID_PERSONA = '$id_usuario_ave'

							)";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$roles = array();

			while ($data = oci_fetch_array($stid, OCI_ASSOC)) {

				$roles [] = $data;

			}

			return $roles;

		}

		function realizar_peticion($request){

			$insumo = $request["INSUMO"];
			$rol = $request["ROL"];
			$observaciones = $request["OBSERVACIONES"];
			$solicitado_por = $request["USUARIO_ID"];
			$otro_insumo = $request["OTRO_INSUMO"];
			$cantidad_solicitar = $request["CANTIDAD_SOLICITAR"];
			$zona = $request["ZONA"];

			if ($otro_insumo != '') {
		
				$query = "	INSERT INTO IN_SOLICITUDES
						(ID_INCIDENTE, OBSERVACIONES, SOLICITADO_POR, FECHA_SOLICITUD, RESPONSABLE, OTRO_INSUMO, CANTIDAD_SOLICITADA, ZONA)
						VALUES (61, UPPER('$observaciones'), '$solicitado_por', SYSDATE, '$rol', UPPER('$otro_insumo'), $cantidad_solicitar, '$zona')";

			}else{

				$query = "	INSERT INTO IN_SOLICITUDES
						(ID_INCIDENTE, OBSERVACIONES, SOLICITADO_POR, FECHA_SOLICITUD, RESPONSABLE, TIPO_INSUMO, CANTIDAD_SOLICITADA, ZONA)
						VALUES (61, UPPER('$observaciones'), '$solicitado_por', SYSDATE, '$rol', '$insumo', $cantidad_solicitar, '$zona')";

			}

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$solicitudes_realizadas = $this->obtener_peticiones($solicitado_por);

			return $solicitudes_realizadas;

		}

		function peticion_atendida($request){

			$comentario = $request["COMENTARIO"];
			$id_correlativo = $request["ID_CORRELATIVO"];
			$id_usuario = $request["ID_USUARIO"];
			$cantidad_despachada = $request["CANTIDAD_DESPACHADA"];
			$respuesta = $request["RESPUESTA"];

			if ($respuesta == 1) {
				
				//Se atiende commpleta o parcial

				$query = "UPDATE IN_SOLICITUDES SET COMENTARIO = UPPER('$comentario'), FECHA_ATENDIDO = SYSDATE, CANTIDAD_DESPACHADA = '$cantidad_despachada' WHERE ID_CORRELATIVO = '$id_correlativo'";

			}else if ($respuesta == 2) {
				
				//Se devuelve

				$comentario_devolucion = $request["COMENTARIO_DEVOLUCION"];

				if (array_key_exists("CONTADOR_DEVOLUCIONES", $request)) {
					
					$cantidad = intval($request["CONTADOR_DEVOLUCIONES"]);

					$cantidad_devoluciones = $cantidad + 1;

				}else{

					$cantidad_devoluciones = 1;

				}	

				$query = "UPDATE IN_SOLICITUDES SET CONTADOR_DEVOLUCIONES = $cantidad_devoluciones, COMENTARIO_DEVOLUCION = UPPER('$comentario_devolucion') WHERE ID_CORRELATIVO = '$id_correlativo'";

			}else if($respuesta == 3){

				$rol_reasignacion = $request["ROL_REASIGNACION"];

				$query = "UPDATE IN_SOLICITUDES SET RESPONSABLE = '$rol_reasignacion' WHERE ID_CORRELATIVO = '$id_correlativo'";

			}			

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$peticiones_recibidas = $this->obtener_peticiones_recibidas($id_usuario);

			return $peticiones_recibidas;			

		}

		function detalles_peticion($id){

			$query = "	SELECT IN_SOLICITUDES.ID_CORRELATIVO, IN_SOLICITUDES.OBSERVACIONES, IN_SOLICITUDES.COMENTARIO,
							TO_CHAR(FECHA_SOLICITUD, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_SOLICITUD, 
							TO_CHAR(FECHA_ATENDIDO, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_ATENDIDO, IN_SOLICITUDES.OTRO_INSUMO, 
							IN_SOLICITUDES.CANTIDAD_SOLICITADA, IN_SOLICITUDES.CANTIDAD_DESPACHADA, 
							IN_SOLICITUDES.CONTADOR_DEVOLUCIONES, IN_SOLICITUDES.REASIGNADA, IN_SOLICITUDES.COMENTARIO_DEVOLUCION,
							IN_SOLICITUDES.CONTADOR_DEVOLUCIONES, IN_SOLICITUDES.ZONA, IN_SOLICITUDES.CONTADOR_DEVOLUCIONES,
							CAT_PERSONAS.ID_PERSONA AS ID_PERSONA, CAT_PERSONAS.NOMBRE AS NOMBRE_PERSONA,
							CAT_INSUMOS_TIPOS.DESCRIPCION AS SOLICITUD
							FROM IN_SOLICITUDES 
							LEFT JOIN CAT_INSUMOS_TIPOS
							ON IN_SOLICITUDES.TIPO_INSUMO = CAT_INSUMOS_TIPOS.ID_TIPO_INSUMO
							INNER JOIN CAT_PERSONAS 
							ON IN_SOLICITUDES.SOLICITADO_POR = CAT_PERSONAS.ID_PERSONA
							WHERE ID_CORRELATIVO = '$id'";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$peticion = oci_fetch_array($stid, OCI_ASSOC);

			/* Obtener roles de la personas que hizo la peticion */
			$id_persona = $peticion["ID_PERSONA"];

			$query = "	SELECT CAT_ROLES.NOMBRE
						FROM CAT_PERSONA_ROL
						INNER JOIN CAT_ROLES
						ON CAT_PERSONA_ROL.ID_ROL = CAT_ROLES.ID_ROL 
						WHERE ID_PERSONA = '$id_persona'";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$roles = array();

			while ($data = oci_fetch_array($stid, OCI_ASSOC)) {
			
				$roles [] = $data["NOMBRE"];

			}			

			$peticion["ROLES"] = $roles;

			return $peticion;

		}

		function detalles_peticion_realizada($id){

			$query = "	SELECT IN_SOLICITUDES.ID_CORRELATIVO, IN_SOLICITUDES.OBSERVACIONES,
							IN_SOLICITUDES.COMENTARIO, TO_CHAR(FECHA_SOLICITUD, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_SOLICITUD, 
							TO_CHAR(FECHA_ATENDIDO, 'DD/MM/YYYY HH24:MI:SS') AS FECHA_ATENDIDO, IN_SOLICITUDES.OTRO_INSUMO, 
							IN_SOLICITUDES.CANTIDAD_SOLICITADA, IN_SOLICITUDES.CANTIDAD_DESPACHADA, 
							IN_SOLICITUDES.CONTADOR_DEVOLUCIONES, IN_SOLICITUDES.REASIGNADA, IN_SOLICITUDES.COMENTARIO_DEVOLUCION,
							IN_SOLICITUDES.SOLICITADO_POR, IN_SOLICITUDES.ZONA, IN_SOLICITUDES.CONTADOR_DEVOLUCIONES,
							CAT_INSUMOS_TIPOS.DESCRIPCION AS SOLICITUD, CAT_ROLES.NOMBRE AS RESPONSABLE
							FROM IN_SOLICITUDES 
							LEFT JOIN CAT_INSUMOS_TIPOS
							ON IN_SOLICITUDES.TIPO_INSUMO = CAT_INSUMOS_TIPOS.ID_TIPO_INSUMO
							INNER JOIN CAT_ROLES 
							ON IN_SOLICITUDES.RESPONSABLE = CAT_ROLES.ID_ROL
							WHERE ID_CORRELATIVO = '$id'";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$peticion = oci_fetch_array($stid, OCI_ASSOC);

			$peticion["CANTIDAD_SOLICITADA"] = intval($peticion["CANTIDAD_SOLICITADA"]);
			$peticion["CANTIDAD_DESPACHADA"] = intval($peticion["CANTIDAD_DESPACHADA"]);

			return $peticion;

		}

		function obtener_peticiones_recibidas_pendientes($id_usuario){

			/* Obtene la cantidad de peticiones que estan pendientes de atender */

			$query = "	SELECT COUNT(*) AS PENDIENTES 
							FROM IN_SOLICITUDES
							WHERE RESPONSABLE IN (

							    SELECT ID_ROL
							    FROM CAT_PERSONA_ROL
							    WHERE ID_PERSONA = '$id_usuario'

							)
							AND IN_SOLICITUDES.FECHA_ATENDIDO IS NULL
							AND IN_SOLICITUDES.COMENTARIO_DEVOLUCION IS NULL";	

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$pendientes = oci_fetch_array($stid, OCI_ASSOC);

			/* Obtener la cantidad de peticiones que han sido devueltas */

			$query = "	SELECT COUNT(*) AS DEVUELTAS 
							FROM IN_SOLICITUDES
							WHERE SOLICITADO_POR = '$id_usuario'
							AND IN_SOLICITUDES.FECHA_ATENDIDO IS NULL
							AND IN_SOLICITUDES.COMENTARIO_DEVOLUCION IS NOT NULL";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$devueltas = oci_fetch_array($stid, OCI_ASSOC);

			return array($pendientes, $devueltas);

		}

		function reasignar_peticion($request){

			$id_correlativo = $request["ID_CORRELATIVO"];
			$rol_reasignacion = $request["ROL_REASIGNACION"];
			$solicitado_por = $request["SOLICITADO_POR"];

			$query = "	UPDATE IN_SOLICITUDES SET RESPONSABLE = '$rol_reasignacion', COMENTARIO_DEVOLUCION = NULL WHERE ID_CORRELATIVO = '$id_correlativo'";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$solicitudes_realizadas = $this->obtener_peticiones($solicitado_por);

			return $solicitudes_realizadas;

		}

		function peticion_servicios($request){

			$insumo = $request["INSUMO"];
			$rol = $request["ROL"];
			$observaciones = $request["OBSERVACIONES"];
			$solicitado_por = $request["USUARIO_ID"];
			$otro_insumo = $request["OTRO_INSUMO"];
			$cantidad_solicitar = $request["CANTIDAD_SOLICITAR"];
			$zona = $request["ZONA"];
			$id_servicio = $request["ID_SERVICIO"];

			if ($otro_insumo != '') {
		
				$query = "	INSERT INTO IN_SOLICITUDES
						(ID_INCIDENTE, OBSERVACIONES, SOLICITADO_POR, FECHA_SOLICITUD, RESPONSABLE, OTRO_INSUMO, CANTIDAD_SOLICITADA, ZONA)
						VALUES (61, UPPER('$observaciones'), '$solicitado_por', SYSDATE, '$rol', UPPER('$otro_insumo'), $cantidad_solicitar, '$zona')";

			}else{

				$query = "	INSERT INTO IN_SOLICITUDES
						(ID_INCIDENTE, OBSERVACIONES, SOLICITADO_POR, FECHA_SOLICITUD, RESPONSABLE, TIPO_INSUMO, CANTIDAD_SOLICITADA, ZONA)
						VALUES (61, UPPER('$observaciones'), '$solicitado_por', SYSDATE, '$rol', '$insumo', $cantidad_solicitar, '$zona')";

			}

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			/* Obtener el ID de la última petición */

			$query = "	SELECT ID_CORRELATIVO AS ID_PETICION
							FROM IN_SOLICITUDES 
							WHERE ROWNUM = 1
							ORDER BY ID_CORRELATIVO DESC";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$peticion = oci_fetch_array($stid, OCI_ASSOC);
			$id_peticion = $peticion["ID_PETICION"];

			/* Insertar en tabla IN_DANIO_SERVICIO_PETICION */
			$query = "INSERT INTO IN_DANIO_SERVICIO_PETICION (ID_PETICION, ID_SERVICIO) VALUES ($id_peticion, $id_servicio)";

			$stid = oci_parse($this->conn, $query);
			$result = oci_execute($stid);

			return $result;

		}

		function peticion_damage($request){

			$insumo = $request["INSUMO"];
			$rol = $request["ROL"];
			$observaciones = $request["OBSERVACIONES"];
			$solicitado_por = $request["USUARIO_ID"];
			$otro_insumo = $request["OTRO_INSUMO"];
			$cantidad_solicitar = $request["CANTIDAD_SOLICITAR"];
			$zona = $request["ZONA"];
			$id_danio = $request["ID_DANIO"];

			if ($otro_insumo != '') {
		
				$query = "	INSERT INTO IN_SOLICITUDES
						(ID_INCIDENTE, OBSERVACIONES, SOLICITADO_POR, FECHA_SOLICITUD, RESPONSABLE, OTRO_INSUMO, CANTIDAD_SOLICITADA, ZONA)
						VALUES (61, UPPER('$observaciones'), '$solicitado_por', SYSDATE, '$rol', UPPER('$otro_insumo'), $cantidad_solicitar, '$zona')";

			}else{

				$query = "	INSERT INTO IN_SOLICITUDES
						(ID_INCIDENTE, OBSERVACIONES, SOLICITADO_POR, FECHA_SOLICITUD, RESPONSABLE, TIPO_INSUMO, CANTIDAD_SOLICITADA, ZONA)
						VALUES (61, UPPER('$observaciones'), '$solicitado_por', SYSDATE, '$rol', '$insumo', $cantidad_solicitar, '$zona')";

			}

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			/* Obtener el ID de la última petición */

			$query = "	SELECT ID_CORRELATIVO AS ID_PETICION
							FROM IN_SOLICITUDES 
							WHERE ROWNUM = 1
							ORDER BY ID_CORRELATIVO DESC";

			$stid = oci_parse($this->conn, $query);
			oci_execute($stid);

			$peticion = oci_fetch_array($stid, OCI_ASSOC);
			$id_peticion = $peticion["ID_PETICION"];

			/* Insertar en tabla IN_DANIO_SERVICIO_PETICION */
			$query = "INSERT INTO IN_DANIO_SERVICIO_PETICION (ID_PETICION, ID_DANIO) VALUES ($id_peticion, $id_danio)";

			$stid = oci_parse($this->conn, $query);
			$result = oci_execute($stid);

			return $result;

		}

	}


?>
