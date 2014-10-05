
app.controller('ApplicationController', ['$scope','USER_ROLES', 'AuthService', 'Session', '$location',
	
	function ($scope, USER_ROLES, AuthService, Session, $location) {

		$scope.usuario = Session;
		
		if($scope.usuario == undefined)
			Session.destroy();
	
		$scope.logout = function() {
			Session.destroy();
	    	$location.path('/index');	
		}
		
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
			    	if(Session.getUserRole() == 'cuentas')
			    		$location.path('/resumen');
			    	else
			    		$location.path('/productos');
			    	
			    	
			    }, 
			    
			    function (error) {
				    $rootScope.$broadcast(AUTH_EVENTS.loginFailed);
				    $scope.aviso = error.data.MSG;
				});
		    
		  };
	  
}]);