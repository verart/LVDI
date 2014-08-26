
app.service('ventasService', ['$http', function ($http) {
        return {
            ventas:function(d, h) {
	           return $http({
	            	method: 'POST',
	            	url: dir_api + '/ventas/index',
	            	data: $.param({desde:d,hasta:h}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
            /******************************
            ADDVENTA
            ******************************/
            addVenta:function (pedido) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/ventas/create',
	            	data: $.param(pedido),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
                        
            /******************************
            DELETEVENTA
            ******************************/
            deleteVenta:function (id) { 
	                    
	            return $http({
	            	method: 'DELETE',
	            	url: dir_api + '/ventas/'+id+'/delete',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            }
            
           
        }
}]);




app.service('notasService', ['$http', function ($http) {
        return {
            notas:function(d, h) {
	           return $http({
	            	method: 'POST',
	            	url: dir_api + '/notas/index',
	            	data: $.param({desde:d,hasta:h}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
            /******************************
            ADDNOTA
            ******************************/
            addNota:function (nota) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/notas/create',
	            	data: $.param(nota),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
                        
            /******************************
            DELETENOTA
            ******************************/
            deleteNota:function (id) { 
	                    
	            return $http({
	            	method: 'DELETE',
	            	url: dir_api + '/notas/'+id+'/delete',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            }
            
           
        }
}]);



