
app.service('clientesPMService', ['$http', function ($http) {
        return {
        
        	/******************************
            CLIENTES POR MAYOR
            ******************************/        
            clientes:function(success) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/clientesPM/index',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
	            .success(function(data) { success(data.DATA);} );
            },
            
            
            /******************************
            NOMBRES DE CLIENTES POR MAYOR
            ******************************/        
            nombresClientes:function() {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/clientesPM/clientesName',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
            /******************************
            CLIENTE
            ******************************/        
            cliente:function(idCl) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/clientesPM/'+idCl+'/show',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
            
            /******************************
            ADDCLIENTE
            ******************************/
            addCliente:function (cliente) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/clientesPM/create',
	            	data: $.param(cliente),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
            
            /******************************
            EDITCLIENTE
            ******************************/
            editCliente: function(cliente){ 
	            
	            return $http({
	            	method: 'PUT',
	            	url: dir_api + '/clientesPM/update',
	            	data: $.param(cliente),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            },
             
             
                   
            /******************************
            DELETEPEDIDO
            ******************************/
            deleteCliente:function (id) { 
	                    
	            return $http({
	            	method: 'DELETE',
	            	url: dir_api + '/clientesPM/'+id+'/delete',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            }
            
            
        }
}])




.service('pedidosService', ['$http', function ($http) {
        return {
            pedidos:function(success) {
	            $http({
	            	method: 'GET',
	            	url: dir_api + '/pedidos/index',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
	            .success(function(data) { success(data.DATA);} );
            },
            
            /******************************
            ADDPEDIDO
            ******************************/
            addPedido:function (pedido) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/pedidos/create',
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
            DELETEPEDIDO
            ******************************/
            deletePedido:function (id) { 
	                    
	            return $http({
	            	method: 'DELETE',
	            	url: dir_api + '/pedidos/'+id+'/delete',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            }
            
           
        }
}]);


