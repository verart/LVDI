app.service('resumenService', ['$http','$q','pendingRequests', function ($http,$q,pendingRequests) {
	return {
		resumen:function(d, h) {
			var canceller = $q.defer();
			pendingRequests.add({
				url: dir_api + '/resumen/index',
				canceller: canceller
			});
			var promise = $http({
				method: 'POST',
	            url: dir_api + '/resumen/index',
	            data: $.param({desde:d,hasta:h}),
	            headers: {'Content-Type': 'application/x-www-form-urlencoded'},
			});
	            promise.finally(function() {
      				pendingRequests.remove(url);
    			});
				return promise;
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



