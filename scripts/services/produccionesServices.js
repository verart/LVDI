
app.service('responsablesService', ['$http', function ($http) {
        return {
        
        	/******************************
			RESPONSABLES
			******************************/        
            responsables:function(p,f){
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/responsables/index',
	            	data: $.param({pag:p, filter:f}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
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
	            	url: dir_api + '/responsables/listAll',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            /******************************
            GETCLIENTEBYNAME
            ******************************/
            getResponsableByName:function(respName) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/responsables/'+respName+'/responsableByName',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            }
            
        }
}])






.service('produccionesService', ['$http', function ($http) {
        return {
            producciones:function(e,p,f) {
	           return $http({
	            	method: 'POST',
	            	url: dir_api + '/producciones/index',
	            	data: $.param({estado:e, pag:p, filter:f}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
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
	            
            },
            
            /******************************
            MODELOS DEL PRODUCCION
            ******************************/
            modelosProduccion:function (id) { 
	                    
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/producciones/'+id+'/modelos',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            }
            
            
        }
}])