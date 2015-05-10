app.service('pedidosespecialesService', ['$http', function ($http) {
        return {
            pedidos:function(e,p,f) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/pedidosespeciales/index',
	            	data:  $.param({estado:e,pag:p,filter:f}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
			},
            //ADDPEDIDO
            addPedido:function (pedido) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/pedidosespeciales/create',
	            	data: $.param(pedido),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            //EDITPEDIDO
            editPedido: function(pedido){         
	            return $http({
	            	method: 'PUT',
	            	url: dir_api + '/pedidosespeciales/update',
	            	data: $.param(pedido),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	        },
            //DELETEPEDIDO
            deletePedido:function (id) { 
	            return $http({
	            	method: 'DELETE',
	            	url: dir_api + '/pedidosespeciales/'+id+'/delete',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
            }, 
            //PAGOS DEL PEDIDO
            pagosPedido:function (id) { 
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/pedidosespeciales/'+id+'/pagos',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
            }
        }
}]);


