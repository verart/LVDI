
app.service('colaImpresionService', ['$http', function ($http) {
        
        
      	var modelos = [];
        	
        return {
        
        
            /******************************
            IMPRESION
            ******************************/
            impresiones:function(success) {
	            $http({
	            	method: 'GET',
	            	url: dir_api + '/colaImpresion/index',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
	            .success(function(data) { 
	            	success(data.DATA);
	            })
	            .error(function(data, status) { 
	            	error(data);
	            })
            },
            
            /******************************
            ADDIMPRESION
            ******************************/
            addModeloImpresion:function (idProducto) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/colaImpresion/create',
	            	data: $.param(idProducto),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
            
            /******************************
            DELETEIMPRESION
            ******************************/
            deleteModeloImpresion:function (id) { 
	                    
	            return $http({
	            	method: 'DELETE',
	            	url: dir_api + '/colaImpresion/'+id+'/delete',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            },
            
            getModelos: function(){	            
	            return modelos;	            
            },
            
           
            setModelos:function(modelosImprimir) {
            	modelos = modelosImprimir;
            }
            
                  
        }
}]);


