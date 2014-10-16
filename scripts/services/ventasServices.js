
app.service('ventasService', ['$http', function ($http) {
        return {
            ventas:function(d, h, cd, p) {
	           return $http({
	            	method: 'POST',
	            	url: dir_api + '/ventas/index',
	            	data: $.param({desde:d,hasta:h,conDeuda:cd,pag:p}),
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
	            
            }, 
            
            
            /******************************
            PAGOS DE LA VENTA
            ******************************/
            pagosVenta:function (id) { 
	                    
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/ventas/'+id+'/pagos',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            },
            
            /******************************
            ADDPAGO
            ******************************/
            addPago:function (pago, idVenta) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/ventas/addPago',
	            	data: $.param({pago:pago, idVenta:idVenta}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
            
            /******************************
            DELETEPAGO
            ******************************/
            deletePago:function (idPago) {
	            return $http({
	            	method: 'DELETE',
	            	url: dir_api + '/ventas/'+idPago+'/deletePago',
	            	data: $.param(idPago),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
            /******************************
            ACTUALIZARNOTA
            ******************************/
            actualizarNota:function (n, idVenta) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/ventas/addNota',
	            	data: $.param({nota:n, idVenta:idVenta}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
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



