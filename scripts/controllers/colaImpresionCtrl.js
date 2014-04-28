app.controller('colaImpresionCtrl', 
	['$scope', '$modal', '$filter','$log', 'AlertService','colaImpresionService','productosService', '$location','$window', '$timeout', 
	function ($scope, $modal, $filter,$log, AlertService, colaImpresionService, productosService, $location,$window, $timeout) {
       

	    	    
	    /**********************************************************************
	     Recupera en data los codigo de producutos de la cola a imprimir
	    **********************************************************************/
	    listImpresiones = function(data){	    		
		    $scope.data = data;
	    }	    	    
	    colaImpresionService.impresiones(listImpresiones);
	   

	    $scope.mod_options = [];
	    $scope.modelo = {nombre:'', id:'', precio:'', cantidad:''};
	     
	     
	     
	     
	     
	    /************************************************************************
	    PRODUCTOS - Recupera todos los modelos de cada producto. Retorna como nombre NomProd-NomMod
	    *************************************************************************/	
	    productosService.nombresProductos(1).then(
			//success
			function(promise){
			     promise.data.DATA.forEach(function (prod) {
		             $scope.mod_options.push({'nombre':prod.nombre, 'id':prod.id, 'precio':prod.precio});  });                   
			},
			//Error al actualizar
			function(error){ AlertService.add('danger', error.data.MSG);}
		);	




	    
	   /************************************************************************
	    DELETEIMPRESION
	    Abre un modal con un form para eliminar un producto de la cola para imprimir
	    param: idImp -> id de la entrada de la cola 
	    *************************************************************************/	
		$scope.deleteImpresion = function(idImp) {
	 					    	
			//Solicita confirmación
			var txt_confirm = { msj: "¿Está seguro que desea eliminar este producto de la cola de impresión?", accept:"Si", cancel:"No"};
			
			var confirm = $modal.open({
				templateUrl: '/LVDI/templates/confirm.html',
				windowClass: 'wndConfirm',
				controller: modalConfirmCtrl,
				resolve: { txt: function(){ return txt_confirm } }
			});

			// Comportamiento al cerrar el modal		    
			confirm.result
			.then( 
				// Si el modal cierra por ACEPTAR
				function (r) {
					colaImpresionService.deleteImpresion(idImp).then(
						//Success
						function(promise){
							var index = $filter('getIndexById')($scope.data, idImp);
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
		};
		
		
		
		
		
		/***************************************************
		 ADD producto
		 Agrega un modelo a la cola. 
		 ****************************************************/	  
		  $scope.add= function() {
		  
		  	if( $scope.modelo.nombre  !=  '') {
		  		colaImpresionService.addModeloImpresion({modelos_id:$scope.modelo.id}).then(
						//Success
						function(promise){
							$scope.data.reposicion.modelos.push({'nombre':$scope.modelo.nombre, 'modelos_id':$scope.modelo.id, 'precio':$scope.modelo.precio});	
						},
						//Error al eliminar
						function(promise){
							AlertService.add('danger', promise.data.MSG);
						}
					);
		  	}	
		  }	 
		
		
		
		
		/***************************************************
		 REMOVEFROMREPOSICION producto
		 Elimina un modelo de la cola. 
		 ****************************************************/
		 $scope.removeFromReposicion= function(id, index) {
		  
		  	colaImpresionService.deleteModeloImpresion(id).then(
				//Success
				function(promise){
					$scope.data.reposicion.modelos.splice(index,1);	
				},
				//Error al eliminar
				function(promise){
					AlertService.add('danger', promise.data.MSG);
				}
			);
		
		  }
		
		
		
		
		
		/***************************************************
		 REMOVEFROMPEDIDOS producto
		 Elimina un modelo de la cola. 
		 ****************************************************/
		 $scope.removeFromPedidos= function(id, index,index2) {
		  
		  	colaImpresionService.deleteModeloImpresion(id).then(
				//Success
				function(promise){
					$scope.data.pedidos[index].modelos.splice(index2,1);
				},
				//Error al eliminar
				function(promise){
					AlertService.add('danger', promise.data.MSG);
				}
			);
		
		  }
		
		
		  
		  
		  
		 /***************************************************
		 VACIARCOLAREPOSICIONES producto
		 vacia la cola. 
		 ****************************************************/
		 $scope.vaciarColaReposiciones= function() {
		  
		  	$scope.data.reposicion.modelos.forEach(function (imp) {
		  	
		        colaImpresionService.deleteModeloImpresion(imp.id).then(
					//Success
					function(promise){},
					//Error al eliminar
					function(promise){
						AlertService.add('danger', promise.data.MSG);
					}
				)
			});
				
			$scope.data.reposicion.modelos = [];
		}
		
		
		
		 /***************************************************
		 VACIARCOLAPEDIDOS 
		 vacia la cola. 
		 ****************************************************/
		 $scope.vaciarColaPedidos = function(index) {
		  
		  	$scope.data.pedidos[index].modelos.forEach(function (imp) {
		  	
		        colaImpresionService.deleteModeloImpresion(imp.id).then(
					//Success
					function(promise){},
					//Error al eliminar
					function(promise){
						AlertService.add('danger', promise.data.MSG);
					}
				);
			});
			
			$scope.data.pedidos[index].modelos = [];
		
		  }
		
		
		
		 /***************************************************
		 IMPRIMIRPEDIDOS 
		 imprimir la cola. 
		 ****************************************************/
		 $scope.imprimirPedidos= function(index) {	  
		  	
		    var index = 0;	
		    var d;
		    	
		    $('#codes').empty();
		    		
			if($scope.data.pedidos[index].modelos.length > 0){
			
				$scope.data.pedidos[index].modelos.forEach(function (prod) {	    	
		            
		            style = {barWidth:2, barHeight:25, fontSize:8};
		            
		            var cod = prod.modelos_id   
		            var long = cod.length;
					var cant = 7 - long;
					for(var i=0; i<cant; i++)
						cod = "0"+cod;
					
					bcdiv = document.createElement("div");
					bcdiv.setAttribute('id',"bcTarget"+index);
					bcdiv.setAttribute('class',"etiqueta");
					$("#codes").append(bcdiv);
					
					
					d = document.createElement("div");
					d.setAttribute('id',"bd"+index);
					p = document.createElement("p");
					p.setAttribute('id',"prod"+index);
					$("#bcTarget"+index).append(p);
					$("#bcTarget"+index).append(d);
					
					$("#prod"+index).text(prod.nombre + ' $'+ prod.precio );
					$("#bd"+index++).barcode({'code':cod,crc:false} , "ean8", style);
					                  
				});
				
				$window.print();
			}	
		
		}
		
		
		
		/***************************************************
		 IMPRIMIRREPOSICONES 
		 imprimir la cola. 
		 ****************************************************/
		 $scope.imprimirReposiciones= function() {
		    	
		    var index = 0;	
		    var d;
		    	
		    
		    $('#codes').empty();
		    
		    
		    if($scope.data.reposicion.modelos.length >0){
		    		
				$scope.data.reposicion.modelos.forEach(function (prod) {
					
			    	
		           style = {barWidth:2, barHeight:25, fontSize:8};
		            
		            var cod = prod.modelos_id   
		            var long = cod.length;
					var cant = 7 - long;
					for(var i=0; i<cant; i++)
						cod = "0"+cod;
					
					bcdiv = document.createElement("div");
					bcdiv.setAttribute('id',"bcTarget"+index);
					bcdiv.setAttribute('class',"etiqueta");
					$("#codes").append(bcdiv);
					
					
					d = document.createElement("div");
					d.setAttribute('id',"bd"+index);
					p = document.createElement("p");
					p.setAttribute('id',"prod"+index);
					$("#bcTarget"+index).append(p);
					$("#bcTarget"+index).append(d);
					
					$("#prod"+index).text(prod.nombre + ' $'+ prod.precio );
					$("#bd"+index++).barcode({'code':cod,crc:false} , "ean8", style);
					                  
				});
	
	
	
	
				$window.print();
			}	
		}
		  
		  
		  
		  
		
		       
}]);



