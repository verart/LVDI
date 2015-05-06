
app.service('clientesPMService', ['$http','$q', 'pendingRequests',function ($http, $q,pendingRequests) {
        
        return {
        	/******************************
            CLIENTES POR MAYOR
            ******************************/        
            clientes:function(p,f) {
	            var canceller = $q.defer();
				pendingRequests.add({
					url: dir_api + '/clientesPM/index',
					canceller: canceller
				});
				var promise = $http({
	            	method: 'POST',
	            	url: dir_api + '/clientesPM/index',
	            	data: $.param({pag:p, filter:f}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            promise.finally(function() {
      				pendingRequests.remove(url);
    			});
				return promise; 
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
            DELETECLIENTE
            ******************************/
            deleteCliente:function (id) { 
	            return $http({
	            	method: 'DELETE',
	            	url: dir_api + '/clientesPM/'+id+'/delete',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	        },
            /******************************
            tienePermiso
            ******************************/        
            tienePermiso:function(token) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/clientesPM/'+token+'/tienePermiso',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
            /******************************
            ENVIARMAIL
            ******************************/
            enviarMail: function(mail){ 
	            return $http({
	            	method: 'PUT',
	            	url: dir_api + '/clientesPM/enviarmail',
	            	data: $.param(mail),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
            },
            /******************************
            GETCLIENTEBYNAME
            ******************************/
            getClienteByName:function(clName) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/clientesPM/'+clName+'/clienteByName',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
        
        }
}])




.service('pedidosService', ['$http','$q','pendingRequests', function ($http,$q,pendingRequests) {
        return {
            pedidos:function(e,p,f) {
				pendingRequests.add({
					url: dir_api + '/pedidos/index',
					canceller: $q.defer()
				});
				var promise = $http({
	            	method: 'POST',
	            	url: dir_api + '/pedidos/index',
	            	data:  $.param({estado:e,pag:p,filter:f}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
				promise.finally(function() {
      				pendingRequests.remove(url);
    			});
				return promise; 
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
            }, 
            /******************************
            MODELOS DEL PEDIDO
            ******************************/
            modelosPedido:function (id) {        
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/pedidos/'+id+'/modelos',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });    
            },
            /******************************
            PAGOS DEL PEDIDO
            ******************************/
            pagosPedido:function (id) { 
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/pedidos/'+id+'/pagos',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });  
            },             
            /******************************
            CONFIRMPEDIDO
            ******************************/
            confirmarPedido:function (p, t) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/pedidos/confirm',
	            	data: $.param({pedido:p, token:t}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
        }
}]);


