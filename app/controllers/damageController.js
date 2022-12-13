app.controller('damageController', ['$scope', '$http', '$timeout', '$rootScope', '$location', '$window', function($scope, $http, $timeout, $rootScope, $location, $window){

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
			url: 'routes/damage/obtener_reportes.php',
			params: { region: '5' }

		}).then(function successCallback(response){

			$scope.reportes = response.data
			$scope.filter_data = $scope.reportes.length

			$scope.current_grid = 1
			$scope.data_limit = 5

			console.log(response.data)

		})

	}

	const toast = swal.mixin({
  		toast: true,
  		position: 'top-end',
  		showConfirmButton: false,
  		timer: 3000
	});

	$scope.maxSize = 3
	$rootScope.id_rol = $.cookie("id_rol_usuario")

	$scope.modalCambiarRegion = function(){

		$('#modalCambiarRegion').modal('show')

	}

	$scope.seleccionarRegion = function(id){

		$scope.region = id

		$http({

			method: 'GET',
			url: 'routes/damage/obtener_reportes.php',
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

			$scope.current_grid = 1

		})

	}

	$scope.modalIngresoReporte = function(){

		$scope.reporte = {}
		$scope.reporte.TIPO_REPORTE = ''
		$scope.reporte.INCIDENTE = '?'
		$scope.reporte.ZONA = ''
		$scope.reporte.CANTIDAD_PERSONAS = ''
		$scope.reporte.CANTIDAD_ESTIMADA = ''
		$scope.reporte.OBSERVACION = ''

		$scope.zonas = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 21, 24, 25]

		console.log($scope.zonas)

		$('#modalIngresoReporte').modal('show')

	}

	$scope.realizarReporte = function(){

		$scope.reporte.REGION = '5'

		$http({

			method: 'POST',
			url: 'routes/damage/realizar_reporte.php',
			data: $scope.reporte


		}).then(function successCallback(response){

			console.log(response.data)

			$scope.reportes = response.data
			$scope.filter_data = $scope.reportes.length

			toast({
  				type: 'success',
  				title: 'Reporte de daño registrado exitosamente'
			})

			$('#modalIngresoReporte').modal('hide')

		})

	}

	$scope.tipoReporte = function(){

		$scope.reporte.ZONA = ''
		$scope.reporte.INCIDENTE = '?'

	}

	$scope.tipoIncidente = function(){

		/* Obtener tipos */
		$http({

			method: 'GET',
			url: 'routes/damage/tipo_damage.php',
			params: {tipo_reporte: $scope.reporte.TIPO_REPORTE, zona: $scope.reporte.ZONA}

		}).then(function successCallback(response){

			$scope.tipos = response.data
			$scope.reporte.INCIDENTE = '?'
			
			console.log(response.data)

		})

	}

	$scope.modalAvalarReporte = function(id){

		if ($.cookie("id_rol_usuario") != 223) {

			$http({

				method: 'GET',
				url: 'routes/damage/detalle_reporte.php',
				params: { id: id }

			}).then(function successCallback(response){

				console.log(response.data)

				$('#modalAvalarReporte').modal('show')
				$scope.detalle_reporte = response.data

				if (!$scope.detalle_reporte.FECHA_AVALADO) {

					$scope.detalle_reporte.CANTIDAD_CONFIRMADA = ''
					$scope.detalle_reporte.VALOR_CONFIRMADO = ''
					$scope.detalle_reporte.OBSERVACION_CONFIRMACION = ''

				}

			})

		}
	}

	$scope.confirmarReporte = function(){

		$scope.detalle_reporte.REGION = $scope.region

		$http({

			method: 'POST',
			url: 'routes/damage/confirmar_reporte.php',
			data: $scope.detalle_reporte

		}).then(function successCallback(response){

			$scope.reportes = response.data

			toast({
  				type: 'success',
  				title: 'Reporte confirmado exitosamente'
			})

			$('#modalAvalarReporte').modal('hide')

			console.log(response.data)

			if (!$scope.detalle_reporte.PETICION_REALIZADA) {
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
		$scope.solicitud.ID_DANIO = $scope.detalle_reporte.ID_CORRELATIVO

		$http({

			method: 'POST',
			url: 'routes/peticiones/realizar_peticion_damage.php',
			data: $scope.solicitud

		}).then(function successCallback(response){

			/* Obtener de nuevo los reportes de la región */
			console.log(response.data)

			$http({

				method: 'GET',
				url: 'routes/damage/obtener_reportes.php',
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
