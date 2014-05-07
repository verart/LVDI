var dir_root = '/LVDI';
var dir_api = '/LVDI/api';


app.config(function($locationProvider, $routeProvider, USER_ROLES){

    $locationProvider.hashPrefix('!');
    $routeProvider
    .when("/index", {
        controller : "loginCtrl",
        templateUrl : dir_root + "/templates/login.html",
        auth: {
        	needAuth: false
        }
    })
    .when("/productos", {
        controller : "productosCtrl",
        templateUrl :  dir_root + "/templates/productos.html",
        auth: {
        	needAuth: true,
	        authorizedRoles: [USER_ROLES.admin, USER_ROLES.local, USER_ROLES.taller]
        }
    })
    .when("/pedidos", {
        controller : "pedidosCtrl",
        templateUrl :  dir_root + "/templates/pedidos.html",
        auth: {
        	needAuth: true,
	        authorizedRoles: [USER_ROLES.admin, USER_ROLES.taller]
        }
    })
    .when("/producciones", {
        controller : "produccionesCtrl",
        templateUrl :  dir_root + "/templates/producciones.html",
        auth: {
        	needAuth: true,
	        authorizedRoles:  [USER_ROLES.admin]
        }
    })
    .when("/clientesPM", {
        controller : "clientesPMCtrl",
        templateUrl :  dir_root + "/templates/clientesPM.html",
        auth: {
        	needAuth: true,
	        authorizedRoles: [USER_ROLES.admin]
        }
    })
    .when("/clientes", {
        controller : "clientesCtrl",
        templateUrl :  dir_root + "/templates/clientes.html",
        auth: {
        	needAuth: true,
	        authorizedRoles:  [USER_ROLES.admin]
        }
    })    
    .when("/responsables", {
        controller : "responsablesCtrl",
        templateUrl :  dir_root + "/templates/responsables.html",
        auth: {
        	needAuth: true,
	        authorizedRoles:  [USER_ROLES.admin]
        }
    })
    .when("/ventas", {
        controller : "ventasCtrl",
        templateUrl :  dir_root + "/templates/ventas.html",
        auth: {
        	needAuth: true,
	        authorizedRoles:  [USER_ROLES.admin, USER_ROLES.local]
        }
    })    
    .when("/colaImpresion", {
        controller : "colaImpresionCtrl",
        templateUrl :  dir_root + "/templates/colaImpresion.html",
        auth: {
        	needAuth: true,
	        authorizedRoles:  [USER_ROLES.admin, USER_ROLES.local]
        }
    })       
    .when("/reportes", {
        controller : "reportesCtrl",
        templateUrl :  dir_root + "/templates/reportes.html",
        auth: {
        	needAuth: true,
	        authorizedRoles:  [USER_ROLES.admin]
        }
    })
    .when("/usuarios", {
        controller : "usuariosCtrl",
        templateUrl :  dir_root + "/templates/usuarios.html",
        auth: {
        	needAuth: true,
	        authorizedRoles:  [USER_ROLES.admin]
        }
    })  
    .otherwise({
       redirectTo: '/index'
    });
})


.config(function ($httpProvider) {  

	$httpProvider.interceptors.push([    
		'$injector',    
		function ($injector) {      
			return $injector.get('AuthInterceptor');
	}]);
	
});
