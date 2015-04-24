
app.service('productosService', ['$http', function ($http) {
        return {
            
            /******************************
            PRODUCTOS
            ******************************/
            productos:function() {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/productos/index',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
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
            GETPRODUCTOMODELO
            ******************************/
            getProductoModelo:function(idMod) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/productos/'+idMod+'/productoModeloById',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            /******************************
            GETPRODUCTOMODELOBYNAME
            ******************************/
            getProductoModeloByName:function(modName) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/productos/'+modName+'/productoModeloByName',
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
	            .success(function(data) {return true;} )
	            .error(function(data, status) {return false;} )        
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
            },
            /******************************
            PRODUCTOS EN PRODUCCION CON STOCK > 0
            ******************************/
            nombresProductosDisponibles:function() {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/productos/productosDisponibles',	     
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            /******************************
            PRODUCTOSPEDIDOS
            ******************************/
            prodsPedido:function(token) {
	            return $http({
	            	method: 'GET',
	            	url: dir_api + '/pedidosdeclientes/'+token+'/productos',
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            getproductos:function(p,f) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/productos/productos',
	            	data:  $.param({pag:p,filter:f}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
            /******************************
            HABILITARPRODUCTO
            ******************************/
            habilitarProducto: function(idP, h){ 
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/productos/habilitarproducto',
	            	data: $.param({idProd:idP, habilitar:h}),
	            	headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            }); 
            }, 
            /******************************
            HABILITARMODELO
            ******************************/
            habilitarModelo: function(idM, h){ 
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/productos/habilitarmodelo',
	            	data: $.param({idMod:idM, habilitar:h}),
	            	headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            });       
            },    
            /******************************
            PRODUCTOS EN PRODUCCION
            ******************************/  
            movimientos:function(mod,d,h,p) {
	            return $http({
	            	method: 'POST',
	            	url: dir_api + '/productos/seguimiento',	            	
	            	data: $.param({id:mod, desde:d, hasta:h, page:p}),
	                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
	            })
            },
        }
}]);


