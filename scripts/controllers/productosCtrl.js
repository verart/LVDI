app.controller('productosCtrl', ['$scope', '$modal', '$filter','productosService','$log', 'AlertService', 

	function ($scope, $modal, $filter, productosService,$log, AlertService, $timeout) {
       
        
		$scope.order = '-nombre';
	    $scope.filterProds = {enProduccion:1};
	    $scope.infoModal = {};

	    
	    /**********************************************************************
	    ALERTS
	    Mensajes a mostrar
	    **********************************************************************/
	    $scope.alerts = [ ];

	    
	    	    
	    /**********************************************************************
	     Recupera en data los productos
	    **********************************************************************/
	    productosService.productos().then(
	    	//Success
			function(promise){
				$scope.data = promise.data.DATA;
			},
			//Error al acceder
			function(error){
				//$location.path('/index');
			}
		);

 
	    /**********************************************************************
	      exceptEmptyComparator
	      Para poder filtrar por equals solo cuando el filtro es por id_producto
	    ***********************************************************************/
	    $scope.exceptEmptyComparator = function (actual, expected) {

		    if (!expected) return true;
		    
		    return angular.equals(expected, actual);
		}
	    

	    
	    
	    /************************************************************************
	    REPONER
	    Incrementa en el listado el stock de un modelo de un producto
	    Param: idProd -> id de producto
	    Param: indexMod -> indice donde se encuentra el modelo de ese producto
	    *************************************************************************/	
		$scope.reponer = function(idProd, indexMod){
		   	
		   	var prodFound = $filter('getById')($scope.data, idProd);
		   	
			productosService.reponerProducto(prodFound.modelos[indexMod].id);
			prodFound.modelos[indexMod].stock ++; 
			
			
		}
		
	
	
	    
	   /************************************************************************
	    OPENPRODUCTO
	    Abre un modal con un form para crear un nuevo producto o editarlo
	    param: idProd -> id de producto. Si viene en blanco es un create 
	    *************************************************************************/	
		$scope.openProducto = function(idProd, userRole) {
	 	
	 	
	 		if(idProd != ''){
	 			$scope.infoModal.producto = $filter('getById')($scope.data, idProd);
	 		}else{
	 			$scope.infoModal.producto = '';
	 		}	
	 		
	 		$scope.infoModal.userRole = userRole;
	 		
	 		angular.element("#nombre").focus();
	 	    
	 	    var modalInstance = $modal.open({
		    	templateUrl: dir_root+'/templates/productos/addedit.html',
		    	windowClass: 'wndProducto',
		    	controller: 'ModalInstanceCtrl',
		    	backdrop: 'static',
		    	keyboard: true,
		    	resolve: {
		        	info: function () { return $scope.infoModal; }
		        }
		    });
		    
		    
		    // Comportamiento al cerrar el modal		    
		    modalInstance.result
		    .then( 
		    	/*************************************************************************************************
		    	 GUARDAR
		    	*************************************************************************************************/
		    	function (res) {
		    	
		    	
		    		/******************************************
		    		 NUEVO PRODUCTO
		    		******************************************/
			    	if(idProd == '') {
			    		productosService.addProducto(res).then(
			    			//Success
			    			function(promise){
			    				$scope.data.push(promise.data.DATA);
			    			},
			    			//Error al guardar
			    			function(error){
				    			var res_msg = error.data.MSG;
				    			AlertService.add('danger', res_msg, 5000);
			    			}
			    		);
			    		
			    			
			    	}else{ 
				    	
				    	
				    	/******************************************
				    	UPDATE PRODUCTO
				    	******************************************/
			    		productosService.editProducto(res).then(
			    			//SUCCESS
			    			function(promise){
				    			var index = $filter('getIndexById')($scope.data, promise.data.DATA.id);
					    		$scope.data[index] = promise.data.DATA;
					    		AlertService.add('success', 'Se guardaron las modificaciones', 1000);
			    			},
			    			//Error al actualizar
			    			function(error){
				    			AlertService.add('danger', error.data.MSG,2000);
			    			}
			    		);
			    	}
			    		
			    }, 
			    
			    /*************************************************************************************************
		    	 CANCELAR
		    	*************************************************************************************************/
			    function (res){
			    
			    	/******************************************
				    DELETE PRODUCTO
				    ******************************************/
				    if(res.action == 'delete'){
				    	
				    	//Solicita confirmación
				    	var txt_confirm = { msj: "¿Está seguro que desea eliminar este producto?", accept:"Si", cancel:"No"};
				    	var idProd = res.idProd;
				    	
				    	var confirm = $modal.open({
					    	templateUrl: dir_root+'/templates/confirm.html',
					    	windowClass: 'wndConfirm',
					    	controller: modalConfirmCtrl,
					    	resolve: { txt: function(){ return txt_confirm } }
					     });

					    // Comportamiento al cerrar el modal		    
					    confirm.result
					    .then( 
					    	// Si el modal cierra por ACEPTAR
					    	function (r) {
						    	 productosService.deleteProducto(idProd).then(
					    			//Success
					    			function(promise){
					    				var index = $filter('getIndexById')($scope.data, idProd);
					    				$scope.data.splice(index, 1);
					    			},
					    			//Error al eliminar
					    			function(promise){
						    			AlertService.add('danger', promise.data.MSG);
					    			}
					    		);
						    }, 
						    // Si el modal cierra por CANCELAR
						    function (res){}

						);   	
					}
			   }
			
			);	
		}
		
		/* NUEVO *******************/
	 	$scope.nuevo = function (userRole) {
            $scope.openProducto('',userRole);
        };		

        
        $scope.search = function (producto) {

		    if ($scope.queryId === undefined || $scope.queryId.length === 0) {
		        return true;
		    }
		
		    var found = false;
		    angular.forEach(producto.modelos, function (mod) {          
		        if (mod.id == parseInt($scope.queryId)) { 
		            found = true;
		        }
		    });
		
		    return found;
		};
     
}]);

	
	
	  
/*************************************************************************************************************************
 ModalInstanceCtrl
 Controller del modal para agregar/editar productos  
**************************************************************************************************************************/
var ModalInstanceCtrl = function ($scope, $modalInstance, $filter, info, $modal) {
		  		  		  
		  
		  if(info.producto != ''){
		  	var original = angular.copy(info.producto);
		  	$scope.producto = info.producto;
		  }else{
		  	$scope.producto = {nombre:'',precio:'', img:'img/productos/noimg.jpg', modelos:[], enProduccion:"1"}
		  	var original = $scope.producto;
		  }
		  
		  $scope.producto.mod2delete = [];
		  $scope.producto.mod2baja = [];
		  $scope.producto.mod2alta = [];
		  
		  $scope.userRole = info.userRole; 
		  
		  /***************************************************
		   OK
		   Se cierra el modal y retornan los datos del producto
		  ****************************************************/ 
		  $scope.ok = function () {
		  	$modalInstance.close($scope.producto);
		  };
		  
		  
		  /***************************************************
		   CANCEL
		   Se cierra el modal y retornan los datos del producto original, sin cambios
		  ****************************************************/
		  $scope.cancel = function () {
		  	$scope.back2original();
		    $modalInstance.dismiss({action:'cancel'});
		  };
		  
		  
		  /***************************************************
		   DELETE
		   Se cierra el modal y retornan un indicador de que hay que eliminar el producto
		  ****************************************************/
		  $scope.deleteProducto = function () {
			  $scope.back2original();	
			  var res = {action:'delete', idProd:$scope.producto.id};	  		
			  $modalInstance.dismiss(res);
		  };
		  

		  // back2original
		  // Copia en producto los campos originales que se enviaron.  
		  $scope.back2original = function(){
			  $scope.producto.nombre = original.nombre;
			  $scope.producto.precio = original.precio;			  
			  $scope.producto.stock = original.stock;			  
			  $scope.producto.enProduccion = original.enProduccion;
			  $scope.producto.modelos = original.modelos;
			  $scope.producto.img = original.img;
			  $scope.producto.mod2delete = [];
			  $scope.producto.mod2baja = [];
			  $scope.producto.mod2alta = [];
		  };	
		  	  
		  	  
		  	  
		  	
		  /***************************************************
		   Manejo de tabla de modelos
		  ****************************************************/		  
		  
		  //REMOVE
		  // Elimina un modelo de la tabla y guarda su id en el array de modelos para eliminar
		  $scope.remove = function(indexMod){
		  
		  
		  				//Solicita confirmación
				    	var txt_confirm = { msj: "¿Está seguro que desea eliminar este color del producto?", accept:"Si", cancel:"No"};
				    	
				    	var confirm = $modal.open({
					    	templateUrl: dir_root+'/templates/confirm.html',
					    	windowClass: 'wndConfirm',
					    	controller: modalConfirmCtrl,
					    	resolve: { txt: function(){ return txt_confirm } }
					     });

					    // Comportamiento al cerrar el modal		    
					    confirm.result
					    .then( 
					    	// Si el modal cierra por ACEPTAR
					    	function (r) {

							  	idMod = $scope.producto.modelos[indexMod].id; 
							  	
							  	$scope.producto.modelos.splice(indexMod,1); 
					
							  	if(idMod != null)
							  		$scope.producto.mod2delete.push({id:idMod});							  		

						    }, 
						    // Si el modal cierra por CANCELAR
						    function (res){}

						);   	
		  

		  	
		  };
		  


		  // ADD
		  // Agrega un modelo a la tabla
		  $scope.add = function(mod){
			  $scope.producto.modelos.push({nombre: (mod||''), fechaVenta:'', fechaRep:'', stock:0});
			  angular.element("#newMod").val('');
			  angular.element("#newMod").focus();

		  };



		  // BAJA
		  // Decrementa el stock en 1
		  $scope.baja = function(indexMod){
		    
			  $scope.producto.modelos[indexMod].stock --;
			  var idMod = $scope.producto.modelos[indexMod].id;
		    
			  //Busco el id de modelo en el array de elementos dado de baja
			  var index2baja = $filter('getIndexById')($scope.producto.mod2baja, idMod); 
		    
			  //Actualizo mod2baja
			  if(index2baja != null)
			  	$scope.producto.mod2baja[index2baja].cantBaja = $scope.producto.mod2baja[index2baja].cantBaja + 1;
			  else{ 
			  
			  	//Busco el id de modelo en el array de elementos dado de alta
			  	var index2alta = $filter('getIndexById')($scope.producto.mod2alta, idMod);
			  	
			  	//Actualizo mod2alta
				  if(index2alta != null){
				  	$scope.producto.mod2alta[index2alta].cantAlta = $scope.producto.mod2alta[index2alta].cantAlta - 1;
				  	if($scope.producto.mod2alta[index2alta].cantAlta == 0) 
			  			$scope.producto.mod2alta.splice(index2alta,1);
			  	  }else
			  	  	$scope.producto.mod2baja.push({id: idMod, cantBaja: 1});
			  }
		  		  		
		  };
		  
		
		  // ALTA
		  // Decrementa el stock en 1
		  $scope.alta = function(indexMod){
		    
			  $scope.producto.modelos[indexMod].stock ++;
			  var idMod = $scope.producto.modelos[indexMod].id;
		    
			  //Busco el id de modelo en el array de elementos dado de alta
			  var index2alta = $filter('getIndexById')($scope.producto.mod2alta, idMod);
			
			  //Actualizo mod2alta
			  if(index2alta != null)
			  	$scope.producto.mod2alta[index2alta].cantAlta = $scope.producto.mod2alta[index2alta].cantAlta + 1; 		
			  
			  else{ 				   
				  //Busco el id de modelo en el array de elementos dado de baja
		    	  var index2baja = $filter('getIndexById')($scope.producto.mod2baja, idMod); 
		    	  
				  //Actualizo mod2baja
				  if(index2baja != null){
				  	$scope.producto.mod2baja[index2baja].cantBaja = $scope.producto.mod2baja[index2baja].cantBaja - 1;
				  	if($scope.producto.mod2baja[index2baja].cantBaja == 0) 
			  			$scope.producto.mod2baja.splice(index2baja,1)
			  	  }else
					$scope.producto.mod2alta.push({id: idMod, cantAlta: 1});
		  	  }  		
		  };		  		  
}


