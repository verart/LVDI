
app.service('productosService', ['$http', function ($http) {
        return {
            
            /******************************
            PRODUCTOS
            ******************************/
            productos:function(success) {
	            $http({
	            	method: 'GET',
	            	url: dir_api + '/productos/index',
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
            ADDPRODUCTO
            ******************************/
            addProducto:function (producto) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/productos/create',
	            	data: $.param(producto),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
            /******************************
            GETPRODUCTO
            ******************************/
            getProducto:function(idProd) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/productos/'+idProd+'/show',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            
            /******************************
            EDITPRODUCTO
            ******************************/
            editProducto: function(producto){ 
	            
	            return $http({
	            	method: 'PUT',
	            	url: dir_api + '/productos/update',
	            	data: $.param(producto),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            },
            
            
            /******************************
            DELETEPRODUCTO
            ******************************/
            deleteProducto:function (id) { 
	                    
	            return $http({
	            	method: 'DELETE',
	            	url: dir_api + '/productos/'+id+'/delete',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });
	            
            },
           
           
            /******************************
            REPONERPRODUCTO
            ******************************/
            reponerProducto: function(idMod){ 
	            $http({
	            	method: 'POST',
	            	url: dir_api + '/productos/reponer',
	            	data: $.param({idMod:idMod}),
	            	headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            }) 
	            .success(function(data) { 
	            	return true;
	            })
	            .error(function(data, status) { 
	            	return false;
	            })        
            },
            
               
            /******************************
            PRODUCTOS EN PRODUCCION
            ******************************/
            nombresProductos:function(enProd) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/productos/productosName',	            	
	            	data: $.param({enProduccion:enProd}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            } 
                  
        }
}]);


