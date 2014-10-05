app.service('resumenService', ['$http', function ($http) {
        return {
            resumen:function(d, h) {
	           return $http({
	            	method: 'POST',
	            	url: dir_api + '/resumen/index',
	            	data: $.param({desde:d,hasta:h}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            }
        }
}]);



