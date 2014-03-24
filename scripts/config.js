var dir_root = '/LVDI';
var dir_api = '/LVDI/api';


app.config(function($locationProvider, $routeProvider){
/*     $locationProvider.html5Mode(true); */
    $locationProvider.hashPrefix('!');
    $routeProvider
    .when("/index", {
        controller : "loginCtrl",
        templateUrl : dir_root + "/templates/login.html"
    })
    .when("/productos", {
        controller : "productosCtrl",
        templateUrl :  dir_root + "/templates/productos.html"
    })
    .when("/pedidos", {
        controller : "pedidosCtrl",
        templateUrl :  dir_root + "/templates/pedidos.html"
    })
    .when("/producciones", {
        controller : "produccionesCtrl",
        templateUrl :  dir_root + "/templates/producciones.html"
    })
    .when("/clientesPM", {
        controller : "clentesPMCtrl",
        templateUrl :  dir_root + "/templates/clientesPM.html"
    })
    .when("/clientes", {
        controller : "clentesCtrl",
        templateUrl :  dir_root + "/templates/clientes.html"
    })
    .otherwise({
       redirectTo: '/index'
    });
});


