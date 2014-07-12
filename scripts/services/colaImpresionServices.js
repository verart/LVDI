
app.service('colaImpresionService', ['$http', function ($http) {
        
        
      	var modelos = [];
        	
        return {
        
        
            /******************************
            IMPRESION
            ******************************/
            impresiones:function(userId) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/colaImpresion/'+userId+'/index',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
	    
            },
            
            /******************************
            ADDIMPRESION
            ******************************/
            addModeloImpresion:function (idProducto,belongsTo) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/colaImpresion/create',
	            	data: $.param(idProducto,belongsTo),
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


