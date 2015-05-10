app.controller('ApplicationController', ['$scope','$rootScope','USER_ROLES','AUTH_EVENTS','AuthService','Session','$location','$interval',
	
	function ($scope, $rootScope, USER_ROLES,AUTH_EVENTS,AuthService,Session,$location,$interval) {

		$scope.usuario = Session;

		$scope.stop = $interval(function() {
            var myelement = document.getElementById('logo'); 
            myelement.src = 'img/LVDI_s.png?rand=' + Math.random();
        }, 900000); 

		//RootScope es para que se vea el valro de activeTab desde diferentes controllers. (se actualiza tb desde el loginCtrl)
		$rootScope.activeTab = $location.$$path.replace('/','');

		$rootScope.$on(AUTH_EVENTS.notAuthorized, function() {
			console.log('No autorizado');	
	    	$scope.logout();
		});
 		   
		$rootScope.$on(AUTH_EVENTS.notAuthenticated, function() {
			console.log('No autenticado');
			$scope.logout();
		});
		
		if($scope.usuario == undefined){
			Session.destroy();
	    	$scope.stop = undefined;
		}
	
		$scope.logout = function() {
			Session.destroy();
			AuthService.logout();	
	    	$scope.stop = undefined;
	    	$location.path('/login');
		}
		
		$scope.refreshActiveTab = function(id){
			$rootScope.activeTab = id;
		};
}])



app.controller('loginCtrl', ['$scope', '$rootScope', '$location', 'AUTH_EVENTS', 'AuthService','Session', 

	function ($scope, $rootScope, $location, AUTH_EVENTS, AuthService, Session) {
		  $scope.credentials = {
		    usuario: '',
		    clave: '',
		    aviso: ''
		  };
		  $scope.login = function (credentials) {
		    AuthService.login(credentials).then(
		    //success
		    	function() {
			    	$rootScope.$broadcast(AUTH_EVENTS.loginSuccess);
			    	if(Session.getUserRole() == 'cuentas'){
			    		$location.path('/resumen');
			    		$rootScope.activeTab ='resumen';
			    	}else{ 
			    		$location.path('/productos');
			    		$rootScope.activeTab ='productos';
			    	}
			    }, 
			    
			    function (error) {
				    $rootScope.$broadcast(AUTH_EVENTS.loginFailed);
				    $scope.aviso = error.data.MSG;
				    $location.path('/login')
				});		    
		  };
}]);