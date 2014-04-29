
app.service('responsablesService', ['$http', function ($http) {
        return {
        
        	/******************************
			RESPONSABLES
			******************************/        
            responsables:function(success) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/responsables/index',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
	            .success(function(data) { success(data.DATA);} );
            },
            
            
            
            
            /******************************
            ADDRES
            ******************************/
            addRes:function (res) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/responsables/create',
	            	data: $.param(res),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
            
            /******************************
            EDITRES
            ******************************/
            editRes: function(res){ 
	            
	            return $http({
	            	method: 'PUT',
	            	url: dir_api + '/responsables/update',
	            	data: $.param(res),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            },
             
             
                   
            /******************************
            DELETERES
            ******************************/
            deleteRes:function (id) { 
	                    
	            return $http({
	            	method: 'DELETE',
	            	url: dir_api + '/responsables/'+id+'/delete',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            },
            
            /******************************
            NOMBRES DE RESPONSABLES
            ******************************/        
            nombresResponsables:function() {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/responsables/index',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            }
            
        }
}])






.service('produccionesService', ['$http', function ($http) {
        return {
            producciones:function(success) {
	            $http({
	            	method: 'GET',
	            	url: dir_api + '/producciones/index',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
	            .success(function(data) { success(data.DATA);} );
            },
            
            
            /******************************
            ADDPRODUCCION
            ******************************/
            addProduccion:function (prod) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/producciones/create',
	            	data: $.param(prod),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
             
            /******************************
            EDITPRODUCCION
            ******************************/
            editProduccion: function(prod){ 
	            
	            return $http({
	            	method: 'PUT',
	            	url: dir_api + '/producciones/update',
	            	data: $.param(prod),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            },
            
            
                        
            /******************************
            DELETEPRODUCCION
            ******************************/
            deleteProduccion:function (id) { 
	                    
	            return $http({
	            	method: 'DELETE',
	            	url: dir_api + '/producciones/'+id+'/delete',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            }
            
            
        }
}])