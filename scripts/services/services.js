
app.service('loginService', ['$http', function ($http) {

        return {
            login:function (usuario, success, error) {
	            $http({
	                method: 'POST', 
	                url: dir_api + '/sesion',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	                data: $.param(usuario),
	            })
	            .success(function(data, status) {
	            	success(data);
	            })
	            .error(function(data, status) { 
	            	error(data);
	            });
            }
       }
}]);


