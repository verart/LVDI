app.controller('loginCtrl', ['$scope', '$location','loginService', function ($scope, $location,loginService) {     
    
    $scope.doLogin = function() {
       loginService.login($scope.usuario, successHandler, errorHandler);
    };
    
    successHandler = function(data) { 
    	$location.path('/productos');
    };
    
    errorHandler = function(data) { 
    	$scope.aviso = data.MSG;
    }
    
    
}])





