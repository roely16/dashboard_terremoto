app.controller('loginController', ['$scope', '$http', '$location', '$rootScope', function($scope, $http, $location, $rootScope){


	/*
	if ($.cookie("usuario_ave")) {

		$location.path('/home');
		$location.replace();

		console.log('se ejecuta cuando recarga')

		//$rootScope.principal_template = "views/layouts/dashboard.html"
		$rootScope.logueado = true

	}else{

		//$rootScope.principal_template = "views/layouts/login.html"
		$rootScope.logueado = false

	}
	*/

	$scope.start_login = function(){

		$http({

			method: 'POST',
			url: 'routes/login/login.php',
			data: $scope.login,
			headers : { 'Content-Type': 'application/x-www-form-urlencoded' }

		}).then(function successCallback(response){

			console.log(response.data)

			$scope.usuario = response.data

			if (typeof $scope.usuario === "object") {

				console.log(response.data)

				$rootScope.logueado = true

				$.cookie("usuario_ave", $scope.usuario.USUARIO)
				$.cookie("id_usuario_ave", $scope.usuario.ID_PERSONA)
				$.cookie("nombre_usuario", response.data["NOMBRE"])
				$.cookie("id_rol_usuario", response.data["ID_ROL"])
				$rootScope.nombre_usuario = $.cookie("nombre_usuario")

				$location.path('/home');
				$location.replace();

			}else{

				swal({
				  	type: 'error',
				  	title: 'Oops...',
				  	text: 'Usuario o contraseña incorrectos!',
				})

			}

		})
	}

	$scope.cerrar_sesion = function(){

		/* Destruir cookie */
		$.removeCookie('usuario_ave')
		$.removeCookie('id_usuario_ave')
		$.removeCookie('nombre_usuario')

		/* Redirigir */
		$location.path('/login');
		$location.replace();

		$rootScope.logueado = false

	}


}])
