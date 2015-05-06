app.service('resumenService', ['$http', function ($http) {
	return {
		resumen:function(d, h) {
			return $http({
				method: 'POST',
	            url: dir_api + '/resumen/index',
	            data: $.param({desde:d,hasta:h}),
	            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			});
	    },
		detalle:function(d, h, fp) {
			return $http({
				method: 'POST',
	            url: dir_api + '/resumen/detalle',
	            data: $.param({desde:d,hasta:h,fp:fp}),
	            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			})
        }
	}
}]);



