app.controller('homeController', ['$scope', '$http', '$location', '$rootScope', function($scope, $http, $location, $rootScope){
	
	if (!$.cookie("usuario_ave")) {

		$location.path('/login');
		$location.replace();

	}else{

		$rootScope.logueado = true
		$rootScope.NOMBRE_USUARIO = $.cookie("usuario_ave")

	}

	$rootScope.nombre_usuario = $.cookie("nombre_usuario")

	console.log($rootScope.nombre_usuario)

}])