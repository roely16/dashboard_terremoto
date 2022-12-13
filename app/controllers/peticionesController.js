app.controller('peticionesController', ['$scope', '$http', '$timeout', '$rootScope', '$location', '$window', function($scope, $http, $timeout, $rootScope, $location, $window){

	if (!$.cookie("usuario_ave")) {

		$location.path('/login');
		$location.replace();

	}else{

		$rootScope.logueado = true
		$rootScope.NOMBRE_USUARIO = $.cookie("usuario_ave")

	}

	const toast = swal.mixin({
  		toast: true,
  		position: 'top-end',
  		showConfirmButton: false,
  		timer: 3000
	});

	$scope.maxSize = 3
	$scope.opcionAtenderPeticion = ''

	if ($rootScope.peticiones_pendientes) {

		$('#myTab li:last-child a').tab('show')
		
	}

	/* Solicitudes realizadas */
	$http({

		method: 'GET',
		url: 'routes/peticiones/obtener_peticiones.php',
		params: { id_usuario: $.cookie("id_usuario_ave") }

	}).then(function successCallback(response){

		$scope.solicitudes_realizadas = response.data
		$scope.filter_data_a = $scope.solicitudes_realizadas.length

		$scope.current_grid = 1
		$scope.data_limit = 5

		console.log(response.data);

	})

	/* Solicitudes recibidas */
	$http({

		method: 'GET',
		url: 'routes/peticiones/solicitudes_recibidas.php',
		params: { id_usuario: $.cookie("id_usuario_ave") }

	}).then(function successCallback(response){

		$scope.solicitudes_recibidas = response.data
		$scope.filter_data_b = $scope.solicitudes_recibidas.length

		$scope.current_grid_b = 1
		$scope.data_limit_b = 5

		console.log(response.data);

	})


	$scope.modalRealizarPeticion = function(){

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

	$scope.obtenerInsumos = function(){

		$scope.obteniendoInsumos = true

		if ($scope.solicitud.TIPO_INSUMO != 'O') {

			console.log($scope.solicitud.TIPO_INSUMO)

			$http({

				method: 'GET',
				url: 'routes/peticiones/listado_insumos.php',
				params: { tipo_insumo: $scope.solicitud.TIPO_INSUMO, id_usuario: $.cookie("id_usuario_ave") }

			}).then(function successCallback(response){

				$scope.obteniendoInsumos = false

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
		$scope.realizando_peticion = true;

		$http({

			method: 'POST',
			url: 'routes/peticiones/realizar_peticion.php',
			data: $scope.solicitud

		}).then(function successCallback(response){

			console.log(response.data)

			$scope.solicitudes_realizadas = response.data
			$scope.filter_data_a = $scope.solicitudes_realizadas.length

			toast({
  				type: 'success',
  				title: 'Solicitud realizada exitosamente'
			})

			$('#modalRealizarPeticion').modal('hide')

			$scope.realizando_peticion = false;

		})

	}

	$scope.modalAtenderPeticion = function(id){

		$scope.opcionAtenderPeticion = ''

		$http({

			method: 'GET',
			url: 'routes/peticiones/detalles_peticion.php',
			params: { id: id }

		}).then(function successCallback(response){

			console.log(response.data)

			$scope.peticion_solicitada = response.data
			$scope.peticion_solicitada.ID_USUARIO = $.cookie("id_usuario_ave")

			$('#modalAtenderPeticion').modal('show')

		})

		// $scope.peticion_solicitada = {}
		// $scope.peticion_solicitada.COMENTARIO = ''
		// $scope.peticion_solicitada.ID_CORRELATIVO = id
		// $scope.peticion_solicitada.ID_USUARIO = $.cookie("id_usuario_ave")

		// 

	}

	$scope.atenderPeticion = function(opcion){

		$scope.opcionAtenderPeticion = opcion

	}

	$scope.modalDetallePeticion = function(id){

		$http({

			method: 'GET',
			url: 'routes/peticiones/detalles_peticion_realizada.php',
			params: { id: id }

		}).then(function successCallback(response){

			console.log(response.data)

			$scope.detalle_peticion_realizada = response.data

			$('#modalDetallesPeticion').modal('show')

		})

	}

	$scope.peticionRealizada = function(){

		$scope.peticion_solicitada.RESPUESTA = $scope.opcionAtenderPeticion
		$scope.atendiendo_peticion = true


		$http({

			method: 'POST',
			url: 'routes/peticiones/peticion_atendida.php',
			data: $scope.peticion_solicitada

		}).then(function successCallback(response){

			$scope.solicitudes_recibidas = response.data
			$scope.filter_data_b = $scope.solicitudes_recibidas.length
			
			$scope.opcionAtenderPeticion = ''

			toast({
  				type: 'success',
  				title: 'Solicitud atendida exitosamente'
			})

			$('#modalAtenderPeticion').modal('hide')
			console.log(response.data);

			$scope.atendiendo_peticion = false

		})

	}

	$scope.reasignarPeticion = function(){

		$scope.reasignandoPeticion = true

		$http({

			method: 'POST',
			url: 'routes/peticiones/reasignar_peticion.php',
			data: $scope.detalle_peticion_realizada

		}).then(function successCallback(response){

			$scope.solicitudes_realizadas = response.data

			$('#modalDetallesPeticion').modal('hide')

			toast({
  				type: 'success',
  				title: 'La petición se ha reasignado exitosamente'
			})

			console.log(response.data)

			$scope.reasignandoPeticion = false

		})

	}

}])
