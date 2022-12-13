var app = angular.module("app", ["ngRoute", "ui.bootstrap"])

/* Filtro */
app.filter('beginning_data', function(){
    return function(input, begin){
        if (input) {
            begin = +begin
            return input.slice(begin)
        }
        return []
    }
})

/* Directiva para subir archivo */
app.directive('fileInput', function($parse){
    return{
        restrict : 'A',
        link: function(scope, elem, attrs){
            elem.bind('change', function(){
                $parse(attrs.fileInput).assign(scope,elem[0].files)
            })
        }
    }
})

//Configuracion de rutas
app.config(['$routeProvider', function($routeProvider){
    $routeProvider
    .when('/', {
		controller: 'terremotoController',
        templateUrl: 'views/layouts/terremoto.html',
    })
    .when('/login', {
        controller: 'loginController',
        templateUrl: 'views/layouts/login.html'
    })
	.when('/peticiones', {
        controller: 'peticionesController',
        templateUrl: 'views/layouts/peticiones.html'
    })
	.when('/servicios_basicos', {
        controller: 'serviciosBasicosController',
        templateUrl: 'views/layouts/servicios_basicos.html'
    })
	.when('/actividades', {
        controller: 'actividadesController',
        templateUrl: 'views/layouts/actividades.html'
    })
	.when('/albergues', {
        controller: 'alberguesController',
        templateUrl: 'views/layouts/albergues.html'
    })
	.when('/daños', {
        controller: 'damageController',
        templateUrl: 'views/layouts/damage.html'
    })
    .otherwise({redirectTo:'/'})

}])

app.run(function ($rootScope, $route) {
    $rootScope.$route = $route;
})
