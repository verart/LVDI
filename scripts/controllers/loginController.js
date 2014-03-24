/*
app.controller('LoginController', function ($scope, $rootScope, $location, AUTH_EVENTS, AuthService) {
  
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

		});
    
  };
  
})

*/

