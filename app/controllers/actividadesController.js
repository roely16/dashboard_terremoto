app.controller('actividadesController', ['$scope', '$http', '$timeout', '$rootScope', '$location', '$window', function($scope, $http, $timeout, $rootScope, $location, $window){

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

	$http({

		method: 'GET',
		url: 'routes/actividades/obtener_actividades.php',
		params: { id_usuario: $.cookie("id_usuario_ave") }

	}).then(function successCallback(response){

		$scope.roles = response.data

		console.log(response.data);

	})

	$scope.modalEditarActividad = function(id){

		$http({

			method: 'GET',
			url: 'routes/actividades/detalles_actividad.php',
			params: {id: id}

		}).then(function successCallback(response){

			$scope.detalle_actividad = response.data
			$scope.detalle_actividad.COMENTARIO = ''
			console.log(response.data);

			$('#modalEditarActividad').modal('show')

		})

	}

	$scope.modalDetalleActividad = function(id){

		$http({

			method: 'GET',
			url: 'routes/actividades/detalles_actividad.php',
			params: {id: id}

		}).then(function successCallback(response){

			$scope.detalle_actividad = response.data

			$('#modalDetalleActividad').modal('show')

		})

	}

	$scope.finalizarTarea = function(){

		$scope.detalle_actividad.USUARIO = $.cookie("id_usuario_ave")

		$http({

			method: 'POST',
			url: 'routes/actividades/finalizar_actividad.php',
			data: $scope.detalle_actividad

		}).then(function successCallback(response){

			$scope.roles = response.data

			toast({
  				type: 'success',
  				title: 'Actividad finalizada exitosamente'
			})

			$('#modalEditarActividad').modal('hide')

			console.log(response.data);

		})

	}

}])
