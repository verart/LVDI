app.service('gastosService', ['$http', function ($http) {
        return {
            gastos:function(d, h) {
	           return $http({
	            	method: 'POST',
	            	url: dir_api + '/gastos/index',
	            	data: $.param({desde:d,hasta:h}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
            /******************************
            ADDGASTO
            ******************************/
            addGasto:function (gasto) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/gastos/create',
	            	data: $.param(gasto),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
                        
            /******************************
            DELETEGASTO
            ******************************/
            deleteGasto:function (id) { 
	                    
	            return $http({
	            	method: 'DELETE',
	            	url: dir_api + '/gastos/'+id+'/delete',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            }
            
           
        }
}]);



