app.service('gastosService', ['$http','$q','pendingRequests', function ($http,$q,pendingRequests) {
        return {
            gastos:function(d, h, idC) {
	           var canceller = $q.defer();
				pendingRequests.add({
					url: dir_api + '/gastos/index',
					canceller: canceller
				});
				var promise = $http({
	            	method: 'POST',
	            	url: dir_api + '/gastos/index',
	            	data: $.param({desde:d,hasta:h, categorias_id:idC}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            promise.finally(function() {
      				pendingRequests.remove(url);
    			});
				return promise;
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
            },  
            categorias:function() {
	           return $http({
	            	method: 'GET',
	            	url: dir_api + '/categorias/index',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },      
            /******************************
            ADDCATEGORIA
            ******************************/
            addCategoria:function (cat) { 
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/categorias/create',
	            	data: $.param(cat),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
            },        
            /******************************
            DELETECATEGORIA
            ******************************/
            deleteCategoria:function (id) { 
	            return $http({
	            	method: 'DELETE',
	            	url: dir_api + '/categorias/'+id+'/delete',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
            },
            /******************************
            GETCATEGORIASBYNAME
            ******************************/
            getCategoriasByName:function(cName) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/categorias/'+cName+'/categoriasByName',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            }
        }
}]);



