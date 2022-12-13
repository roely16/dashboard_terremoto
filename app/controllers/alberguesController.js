app.controller('alberguesController', ['$scope', '$http', '$timeout', '$rootScope', '$location', '$window', function($scope, $http, $timeout, $rootScope, $location, $window){

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
	$scope.tipo_registro = ''
	
	/* Obtener albergues activos */

	if ($.cookie("id_rol_usuario") != 223 ) {
		
		$('#modalCambiarRegion').modal('show')

	}else{

		$http({

			method: 'GET',
			url: 'routes/albergues/obtener.php',
			params: {region: '5'}

		}).then(function successCallback(response){

			$scope.albergues_habilitados = response.data
			$scope.filter_data = $scope.albergues_habilitados.length

			$scope.current_grid = 1
			$scope.data_limit = 5

			console.log(response.data);

		})

	}

	$scope.modalCambiarRegion = function(){

		$('#modalCambiarRegion').modal('show')

	}

	$scope.seleccionarRegion = function(id){

		$scope.region = id

		$http({

			method: 'GET',
			url: 'routes/albergues/obtener.php',
			params: {region: id}

		}).then(function successCallback(response){

			console.log(response.data)

			$('#modalCambiarRegion').modal('hide')

			toast({
  				type: 'success',
  				title: 'Región seleccionada exitosamente'
			})

			$scope.albergues_habilitados = response.data
			$scope.filter_data = $scope.albergues_habilitados.length

			$scope.current_grid = 1
			$scope.data_limit = 5

		})

	}
	

	$scope.modalHabilitar = function(){

		/** Obtener zonas */
		$scope.albergue = {}
		$scope.albergue.ZONA = ''
		$scope.albergue.ALBERGUE_HABILITADO = ''

		if ($scope.region == 1) {

			$scope.zonas = [1, 4, 5, 8, 9, 11]

		}else if ($scope.region == 2) {

			$scope.zonas = [2, 3, 6, 7, 10, 19]

		}else if ($scope.region == 3){

			$scope.zonas = [12, 13, 14, 15, 16, 21]

		}else if($scope.region == 4){

			$scope.zonas = [17, 18, 24, 25]

		}

		// $http({

		// 	method: 'GET',
		// 	url: 'routes/albergues/obtener_zonas.php'

		// }).then(function successCallback(response){

		// 	console.log(response.data);

		// 	$scope.zonas = response.data

		// 	$('#exampleModalCenter').modal('show')

		// 	$('#exampleModalCenter').on('shown.bs.modal', function () {

		// 		$('.bootstrap-select').selectpicker('render')
		// 		//$('#select_dependencias_agregar_persona').selectpicker('render')

		// 	})

		// })
		$('#exampleModalCenter').modal('show')
	}

	$scope.seleccionarZona = function(){

		console.log($scope.albergue.ZONA);

		$http({

			method: 'GET',
			url: 'routes/albergues/albergues_zona.php',
			params: { zona: $scope.albergue.ZONA }

		}).then(function successCallback(response){

			$scope.albergues_sin_habilitar = response.data

			console.log($scope.albergues_sin_habilitar);

		})

	}

	$scope.habilitarAlbergue = function(){

		console.log($scope.albergue);

		$scope.albergue.REGION = $scope.region

		$http({

			method: 'POST',
			url: 'routes/albergues/habilitar_albergue.php',
			data: $scope.albergue

		}).then(function successCallback(response){

			$scope.albergues_habilitados = response.data
			$scope.filter_data = $scope.albergues_habilitados.length

			toast({
  				type: 'success',
  				title: 'Albergue habilitado exitosamente'
			})

			$('#exampleModalCenter').modal('hide')

			console.log(response.data);

		})

	}

	$scope.modalAgregarPersonas = function(id){

		$http({

			method: 'GET',
			url: 'routes/albergues/capacidad_albergue.php',
			params: {id: id}

		}).then(function successCallback(response){

			$scope.detalle_albergue = response.data
			console.log(response.data);

			$('#modalAgregarPersonas').modal('show')

		})

	}

	$scope.ingresarPersonas = function(){
		
		$scope.detalle_albergue.REGION = $scope.region

		if ($scope.tipo_registro == 1) {

			//Agregar
			
			$scope.detalle_albergue.TIPO_REGISTRO = $scope.tipo_registro

			$http({

				method: 'POST',
				url: 'routes/albergues/registrar_personas.php',
				data: $scope.detalle_albergue

			}).then(function successCallback(response){

				console.log(response.data)

				toast({
	  				type: 'success',
	  				title: 'Actualización realizada exitosamente'
				})

				$('#modalAgregarPersonas').modal('hide')

				$scope.tipo_registro = ''

				$scope.albergues_habilitados = response.data

			})
			
		}else{

			//Retirar

			if ((parseInt($scope.detalle_albergue.USO) - parseInt($scope.detalle_albergue.PERSONAS_AGREGAR)) < 0) {

					toast({
		  				type: 'error',
		  				title: 'No se puede retirar, la cantidad de personas es mayor a las albergadas'
				})

			}else{

				$scope.detalle_albergue.TIPO_REGISTRO = $scope.tipo_registro

				$http({

					method: 'POST',
					url: 'routes/albergues/registrar_personas.php',
					data: $scope.detalle_albergue

				}).then(function successCallback(response){

					console.log(response.data)

					toast({
		  				type: 'success',
		  				title: 'Actualización realizada exitosamente'
					})

					$('#modalAgregarPersonas').modal('hide')

					$scope.tipo_registro = ''

					$scope.albergues_habilitados = response.data

				})

			}

		}

	}

	$scope.tipoRegistro = function(tipo){

		$scope.tipo_registro = tipo

	}
}])
