
app.service('clientesService', ['$http', function ($http) {
        return {
        
        	/******************************
            CLIENTES
            ******************************/        
            clientes:function(success) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/clientes/index',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
	            .success(function(data) { success(data.DATA);} );
            },
            
            
            /******************************
            CLIENTE
            ******************************/        
            cliente:function(idCl) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/clientes/'+idCl+'/show',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
            
            /******************************
            ADDCLIENTE
            ******************************/
            addCliente:function (cliente) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/clientes/create',
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
	            	url: dir_api + '/clientes/update',
	            	data: $.param(cliente),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            },
            
            
        }
}]);