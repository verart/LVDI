
app.service('ventasService', ['$http', function ($http) {
        return {
            ventas:function(success) {
	            $http({
	            	method: 'GET',
	            	url: dir_api + '/ventas/index',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
	            .success(function(data) { success(data.DATA);} );
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
            EDITPEDIDO
            ******************************/
            editPedido: function(pedido){ 
	            
	            return $http({
	            	method: 'PUT',
	            	url: dir_api + '/pedidos/update',
	            	data: $.param(pedido),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
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


