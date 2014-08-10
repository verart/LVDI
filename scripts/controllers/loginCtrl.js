
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




app.controller('loginCtrl', ['$scope', '$rootScope', '$location', 'AUTH_EVENTS', 'AuthService',

	function ($scope, $rootScope, $location, AUTH_EVENTS, AuthService) {
  
		  
		  $scope.credentials = {
		    usuario: '',
		    clave: '',
		    aviso: ''
		  };
  
  
  
		  $scope.login = function (credentials) {
	    
		    AuthService.login(credentials).then(
		    
		    	function () {
			    	$rootScope.$broadcast(AUTH_EVENTS.loginSuccess);
			    	$location.path('/productos');
			    	
			    	
			    }, 
			    
			    function (error) {
				    $rootScope.$broadcast(AUTH_EVENTS.loginFailed);
				    $scope.aviso = error.data.MSG;
				});
		    
		  };
	  
}]);