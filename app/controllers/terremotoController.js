app.controller('terremotoController', ['$scope', '$http', '$timeout', '$rootScope', '$location', '$window', function($scope, $http, $timeout, $rootScope, $location, $window){

	if (!$.cookie("usuario_ave")) {

		$location.path('/login');
		$location.replace();

	}else{

		$rootScope.logueado = true
		$rootScope.NOMBRE_USUARIO = $.cookie("usuario_ave")

	}

	$rootScope.tiempo_carga = 100000000000000

	$scope.maxSize = 5
	$scope.bigCurrentPage = 1
	$rootScope.peticiones_pendientes = false

	/* Obtener peticiones pendientes */
	$http({

		method: 'GET',
		url: 'routes/peticiones/solicitudes_recibidas_pendientes.php',
		params: { id_usuario: $.cookie("id_usuario_ave") }

	}).then(function successCallback(response){

		console.log(response.data)

		$scope.pendientes = response.data[0]
		$scope.devueltas = response.data[1]

		console.log($scope.pendientes);

	})

	$scope.peticionesPendientes = function(){


		$rootScope.peticiones_pendientes = true	
		console.log('Peticiones pendientes')

	}


	$scope.peticiones = function(){

		console.log('peticiones');

		$window.location.href = '#/peticiones';

	}

	$scope.servicios_basicos = function(){

		console.log('servicios_basicos');

		$window.location.href = '#/servicios_basicos';

	}

	$scope.actividades = function(){

		console.log('actividades');

		$window.location.href = '#/actividades';

	}

	$scope.albergues = function(){

		console.log('albergues');

		$window.location.href = '#/albergues';

	}

	$scope.damages = function(){

		console.log('damages');

		$window.location.href = '#/daños';

	}

	$scope.dashboard = function(){

		 $window.open('https://udicat.muniguate.com/apps/monitor_dic4', '_blank');

	}

}])
