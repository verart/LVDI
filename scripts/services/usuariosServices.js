app.service('usuariosService', ['$http', function ($http) {
        return {
        
        	/******************************
            USUARIOS
            ******************************/        
            usuarios:function(success) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/usuarios/index',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
	            .success(function(data) { success(data.DATA);} );
            },
            
            
            /******************************
            ADDUSUARIO
            ******************************/
            addUsuario:function (usuario) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/usuarios/create',
	            	data: $.param(usuario),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
            
            /******************************
            EDITUSUARIO
            ******************************/
            editUsuario: function(usuario){ 
	            
	            return $http({
	            	method: 'PUT',
	            	url: dir_api + '/usuarios/update',
	            	data: $.param(usuario),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            },
            
            
                   
            /******************************
            DELETEUSUARIOS
            ******************************/
            deleteUsuario:function (id) { 
	                    
	            return $http({
	            	method: 'DELETE',
	            	url: dir_api + '/usuarios/'+id+'/delete',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            }
        }
}]);