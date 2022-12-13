app.controller('serviciosBasicosController', ['$scope', '$http', '$timeout', '$rootScope', '$location', '$window', function($scope, $http, $timeout, $rootScope, $location, $window){

	if (!$.cookie("usuario_ave")) {

		$location.path('/login');
		$location.replace();

	}else{

		$rootScope.logueado = true
		$rootScope.NOMBRE_USUARIO = $.cookie("usuario_ave")

	}

	if ($.cookie("id_rol_usuario") != 223 ) {
		
		$('#modalCambiarRegion').modal('show')

	}else{

		$http({

			method: 'GET',
			url: 'routes/servicios/obtener_reportes.php',
			params: {region: '5'}

		}).then(function successCallback(response){

			console.log(response.data)

			$scope.reportes = response.data
			$scope.filter_data = $scope.reportes.length

		})

	}

	const toast = swal.mixin({
  		toast: true,
  		position: 'top-end',
  		showConfirmButton: false,
  		timer: 3000
	});

	$scope.current_grid = 1
	$rootScope.id_rol = $.cookie("id_rol_usuario")


	$scope.modalCambiarRegion = function(){

		$('#modalCambiarRegion').modal('show')

	}

	$scope.seleccionarRegion = function(id){

		$scope.region = id

		$http({

			method: 'GET',
			url: 'routes/servicios/obtener_reportes.php',
			params: {region: id}

		}).then(function successCallback(response){

			console.log(response.data)

			$('#modalCambiarRegion').modal('hide')

			toast({
  				type: 'success',
  				title: 'Región seleccionada exitosamente'
			})

			$scope.reportes = response.data
			$scope.filter_data = $scope.reportes.length

		})

	}


	$scope.modalReportaServicio = function(){

		$('#modalReportarServicio').modal('show')

		$scope.reporte = {}
		$scope.reporte.PORCENTAJE = 0
		$scope.reporte.SERVICIO = '?'		

	}

	$scope.obtenerServicios = function(){

		$http({

			method: 'GET',
			url: 'routes/servicios/obtener_servicios.php',
			params: { zona: $scope.reporte.ZONA }

		}).then(function successCallback(response){

			$scope.servicios = response.data
			$scope.reporte.SERVICIO = '?'

			console.log(response.data)

		})

	}

	$scope.realizarReporte = function(){

		if ($.cookie("id_rol_usuario") == 223) {

			$scope.reporte.REGION = '5'

		}else{

			$scope.reporte.REGION = $scope.region

		}
		

		$http({

			method: 'POST',
			url: 'routes/servicios/registrar_reporte.php',
			data: $scope.reporte

		}).then(function successCallback(response){

			console.log(response.data)

			$scope.reportes = response.data
			$scope.filter_data = $scope.reportes.length

			toast({
  				type: 'success',
  				title: 'Reporte realizado exitosamente'
			})

			$('#modalReportarServicio').modal('hide')

		})
		
	}

	$scope.modalEditarReporte = function(id){

		/* Solo si es usuario no es vecino */
		if ($.cookie("id_rol_usuario") != 223) {

			$http({

				method: 'GET',
				url: 'routes/servicios/detalles_reporte.php',
				params: {id: id}

			}).then(function successCallback(response){

				console.log(response.data)

				$scope.detalles_reporte = response.data

				if (!$scope.detalles_reporte.PORCENTAJE_CONFIRMADO) {

					$scope.detalles_reporte.PORCENTAJE_CONFIRMADO = $scope.detalles_reporte.PORCENTAJE

				}

				$scope.porcentaje_daño = $scope.detalles_reporte.PORCENTAJE
				$scope.observacion = $scope.detalles_reporte.OBSERVACION

				// if ($scope.detalle_reporte.OBSERVACION_CONFIRMACION) {
				// 	$scope.observacion_confirmacion = $scope.detalle_reporte.OBSERVACION_CONFIRMACION
				// }
				
				$scope.sin_cambios = true

				$('#modalEditarReporte').modal('show')

				console.log($scope.porcentaje_daño)

			})

		}
		
	}

	$scope.actualizarPorcentaje = function(){

		/* Si cambia el porcentaje */
		if ($scope.detalles_reporte.PORCENTAJE != $scope.porcentaje_daño) {

			$scope.sin_cambios = false

		}else{

			$scope.sin_cambios = true

		}

	}

	$scope.actualizarObservacion = function(){

		/* Si cambia la observacion */
		if ($scope.detalles_reporte.OBSERVACION != $scope.observacion) {

			$scope.sin_cambios = false

		}else{

			$scope.sin_cambios = true

		}

	}

	$scope.actualizarObservacionConfirmacion = function(){

		/* Si cambia la observacion */
		if ($scope.detalles_reporte.OBSERVACION_CONFIRMACION != $scope.observacion_confirmacion) {

			$scope.sin_cambios = false

		}else{

			$scope.sin_cambios = true

		}

	}

	$scope.actualizarReporte = function(){

		$scope.detalles_reporte.REGION = $scope.region

		$http({

			method: 'POST',
			url: 'routes/servicios/actualizar_reporte.php',
			data: $scope.detalles_reporte

		}).then(function successCallback(response){

			$scope.reportes = response.data
			console.log(response.data)

			toast({
	  			type: 'success',
	  			title: 'Reporte actualizado exitosamente'
			})

			$('#modalEditarReporte').modal('hide')

			/* Preguntar si desea enviar peticion */
			/* Mostrar unicamente si no existe ya una peticion */	

			if (!$scope.detalles_reporte.PETICION_SERVICIO) {

				swal({
				  	title: '¿Desea generar una petición?',
				  	type: 'warning',
				  	showCancelButton: true,
				  	confirmButtonColor: '#3085d6',
				  	cancelButtonColor: '#d33',
				  	confirmButtonText: 'Generar Petición',
				  	cancelButtonText: 'Cancelar'
				}).then((result) => {
				  
			  		if (result.value) {
					/* Mostrar modal para realizar peticion */

						$scope.solicitud = {}
						$scope.solicitud.TIPO_INSUMO = ''
						$scope.solicitud.INSUMO = ''
						$scope.solicitud.ROL = ''
						$scope.solicitud.OBSERVACIONES = ''
						$scope.solicitud.OTRO_INSUMO = ''

						/* Solicitar los tipos de insumos */
						$http({

							method: 'GET',
							url: 'routes/peticiones/tipos_insumos.php'

						}).then(function successCallback(response){

							$scope.tipos_insumos = response.data
							$('#modalRealizarPeticion').modal('show')

							$('#modalRealizarPeticion').on('shown.bs.modal', function (e) {

								$('#selectpicker').selectpicker('render')

								$('#selectpicker').on('show.bs.select', function () {
				  					$('#selectpicker').selectpicker('refresh')
								});

								$('#selectpicker2').selectpicker('render')

								$('#selectpicker2').on('show.bs.select', function () {
				  					$('#selectpicker2').selectpicker('refresh')
								});

							})

						})
					}	
				})
			}

		})

	}

	$scope.obtenerInsumos = function(){

		if ($scope.solicitud.TIPO_INSUMO != 'O') {

			console.log($scope.solicitud.TIPO_INSUMO)

			$http({

				method: 'GET',
				url: 'routes/peticiones/listado_insumos.php',
				params: { tipo_insumo: $scope.solicitud.TIPO_INSUMO, id_usuario: $.cookie("id_usuario_ave") }

			}).then(function successCallback(response){

				$scope.listado_insumos = response.data[0]
				$scope.roles = response.data[1]

				console.log(response.data);

			})

		}else{

			$http({

				method: 'GET',
				url: 'routes/peticiones/obtener_roles.php',
				params: { id_usuario: $.cookie("id_usuario_ave") }

			}).then(function successCallback(response){

				$scope.roles = response.data

				$('#selectpicker2').selectpicker('render')

				$('#selectpicker2').on('show.bs.select', function () {
  					$('#selectpicker2').selectpicker('refresh')
				});

			})

		}

	}

	$scope.realizarSolicitud = function(){

		
		$scope.solicitud.USUARIO_ID = $.cookie("id_usuario_ave")
		$scope.solicitud.ID_SERVICIO = $scope.detalles_reporte.ID_CORRELATIVO

		$http({

			method: 'POST',
			url: 'routes/peticiones/realizar_peticion_servicio.php',
			data: $scope.solicitud

		}).then(function successCallback(response){

			/* Obtener de nuevo los reportes de la región */
			console.log(response.data)

			$http({

				method: 'GET',
				url: 'routes/servicios/obtener_reportes.php',
				params: {region: $scope.region}

			}).then(function successCallback(response){

				console.log(response.data)

				$scope.reportes = response.data
				$scope.filter_data = $scope.reportes.length

				$('#modalRealizarPeticion').modal('hide')

				toast({
		  			type: 'success',
		  			title: 'Reporte actualizado exitosamente'
				})

			})

		})
		
	}

}])
