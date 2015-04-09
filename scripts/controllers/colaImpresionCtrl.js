app.controller('colaImpresionCtrl', 
	['$scope', '$modal', '$filter','$log', 'AlertService','colaImpresionService','productosService', '$location','$window', '$timeout', 'Session',
	function ($scope, $modal, $filter,$log, AlertService, colaImpresionService, productosService, $location,$window, $timeout, Session) {
       		
 
	    	    
	    /**********************************************************************
	     Recupera en data los codigo de producutos de la cola a imprimir
	    **********************************************************************/
	    colaImpresionService.impresiones(Session.getUserId()).then(
	    	//Success
			function(promise){
				$scope.data = promise.data.DATA;
			},
			//Error al guardar
			function(error){}
		);
		
		$scope.mod_options = [];
	    $scope.modelo = {nombre:'', id:'', precio:'', cantidad:''};
	      
	    /************************************************************************
	    PRODUCTOS - Recupera todos los modelos de cada producto. Retorna como nombre NomProd-NomMod
	    *************************************************************************/	
	 //    productosService.nombresProductos(1).then(
		// 	//success
		// 	function(promise){
		// 	     promise.data.DATA.forEach(function (prod) {
		//              $scope.mod_options.push({'nombre':prod.nombre, 'id':prod.id, 'precio':prod.precio});  });                   
		// 	},
		// 	//Error al actualizar
		// 	function(error){ AlertService.add('danger', error.data.MSG);}
		// );	

	    
	   /************************************************************************
	    DELETEIMPRESION
	    Abre un modal con un form para eliminar un producto de la cola para imprimir
	    param: idImp -> id de la entrada de la cola 
	    *************************************************************************/	
		$scope.deleteImpresion = function(idImp) {
	 					    	
			//Solicita confirmación
			var txt_confirm = { msj: "¿Está seguro que desea eliminar este producto de la cola de impresión?", accept:"Si", cancel:"No"};
			
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
		
		
		// SEARCHBYNAME producto *** Busca un producto
		$scope.searchByName= function() {		  
			if($scope.form.modelo.nombre != ''){
			  	// Recupera el producto. Retorna como nombre NomProd-NomMod
			  	productosService.getProductoModeloByName($scope.form.modelo.nombre).then(
					//success
					function(promise){
						$scope.mod_options = promise.data.DATA;
					},					
					//no existe
					function(error){
						if((error.status == 403) || (error.status == 401)){
						    $modalInstance.dismiss({action:'cancel'});
							$location.path('/index');
						}
						$scope.mod_options = [];
					}
				);		
			}		  
		}
		
		// SETMODEL  *** guarda en form.modelo el modelo seleccionado
		$scope.setModel= function(item) {		  
			$scope.modelo = item;	
		}

		/***************************************************
		 ADD producto
		 Agrega un modelo a la cola. 
		 ****************************************************/	  
		  $scope.add= function() {
		  
		  	if( $scope.modelo.nombre  !=  '') {
		  		colaImpresionService.addModeloImpresion({modelos_id:$scope.modelo.id, belongsTo:Session.getUserId()}).then(
						//Success
						function(promise){
							$scope.data.sueltos.modelos.push({'nombre':$scope.modelo.nombre, 'modelos_id':$scope.modelo.id, 'precio':$scope.modelo.precio, 'id':promise.data.DATA.id});	
							angular.element("#newMod").val('');
							angular.element("#newMod").focus();
						},
						//Error al eliminar
						function(promise){
							AlertService.add('danger', promise.data.MSG);
						}
					);
		  	}	
		  }	 
		
		
		
		
		/***************************************************
		 REMOVEPRODUCTO 
		 Elimina el modelo 'id' de la cola de impresion. Lo quita de la tabla 'from' en el indice 'index'. 
		 ****************************************************/
		 $scope.removeProducto= function(id, index, from) {
		  
		  	colaImpresionService.deleteModeloImpresion(id).then(
				//Success
				function(promise){
					switch(from){
						case 'reposicion': $scope.data.reposicion.modelos.splice(index,1);
						case 'sueltos': $scope.data.sueltos.modelos.splice(index,1);
					} 	
				},
				//Error al eliminar
				function(promise){
					AlertService.add('danger', promise.data.MSG);
				}
			);
		
		  }
				  
		  
		
		/***************************************************
		 REMOVEFROMPEDIDOS producto
		 Elimina un modelo de la cola de pedidos
		 ****************************************************/
		 $scope.removeFromPedidos= function(id, index,index2) {
		  
		  	colaImpresionService.deleteModeloImpresion(id).then(
				//Success
				function(promise){
					
					if($scope.data.pedidos[index].productos.length == 1)
						$scope.data.pedidos.splice(index,1);
					else	
						$scope.data.pedidos[index].productos.splice(index2,1);
					
				},
				//Error al eliminar
				function(promise){
					AlertService.add('danger', promise.data.MSG);
				}
			);
		
		  }
		
		
		/***************************************************
		 REMOVEFROMPRODUCCIONES producto
		 Elimina un modelo de la cola de producciones. 
		 ****************************************************/
		 $scope.removeFromProducciones= function(id, index,index2) {
		  
		  	colaImpresionService.deleteModeloImpresion(id).then(
				//Success
				function(promise){
					
					if($scope.data.producciones[index].productos.length == 1)
						$scope.data.producciones.splice(index,1);
					else	
						$scope.data.producciones[index].productos.splice(index2,1);
					
				},
				//Error al eliminar
				function(promise){
					AlertService.add('danger', promise.data.MSG);
				}
			);
		
		  }
		  
		  
		
		/*******************************************************************
		 VACIARCOLA
			 vacia la cola 'from' (resposicion / sueltos / indexDePedido). 
		 ********************************************************************/
		 $scope.vaciarCola = function(from,index) {
		  
		  	//Solicita confirmación
		  	var txt_confirm;
			switch(from){
				
				case 'reposicion': 
					txt_confirm = { msj: "Se eliminarán todos los productos de la cola de impresión de resposición. ¿Desea continuar?", 
									accept:"Si", cancel:"No"};
					break;
					
				case 'sueltos': 
					txt_confirm = { msj: "Se eliminarán todos los productos de su cola de impresión. ¿Desea continuar?", 
									accept:"Si", cancel:"No"};
					break;
				
				case 'producciones': 
					txt_confirm = { msj: "Se eliminará la cola de impresión de la producción de "+$scope.data.producciones[index].responsable+". ¿Desea continuar?", 
									accept:"Si", cancel:"No"};
					break;

				case 'ventas': 
					txt_confirm = { msj: "Se eliminará la cola de impresión de las devoluciones en ventas. ¿Desea continuar?", 
									accept:"Si", cancel:"No"};
					break;
						
				default:
					txt_confirm = { msj: "Se eliminará la cola de impresión del pedido de "+$scope.data.pedidos[index].clientePM+". ¿Desea continuar?", 
									accept:"Si", cancel:"No"};	
				
			}		
				
				
			var confirm = $modal.open({
				templateUrl: dir_root+'/templates/confirm.html',
				windowClass: 'wndConfirm',
				controller: modalConfirmCtrl,
				resolve: { txt: function(){ return txt_confirm } }
			});
			

			// Comportamiento al cerrar el modal		    
			confirm.result.then( 
			
				// Si el modal cierra por ACEPTAR
				function (r) {
					
					switch(from){
					
						case 'reposicion': 		
						  	$scope.data.reposicion.modelos.forEach(function (imp) {						  	
						        colaImpresionService.deleteModeloImpresion(imp.id).then(
									function(promise){},
									function(promise){ AlertService.add('danger', promise.data.MSG);}
								)
							});		
							$scope.data.reposicion.modelos = [];
							break;	
											
						case 'sueltos': 		
							$scope.data.sueltos.modelos.forEach(function (imp) {					  	
						        colaImpresionService.deleteModeloImpresion(imp.id).then(
									function(promise){},
									function(promise){ AlertService.add('danger', promise.data.MSG);}
								)
							});
							$scope.data.sueltos.modelos = [];
							break;			
						
						case 'ventas': 
							$scope.data.ventas.modelos.forEach(function (imp) {					  	
						        colaImpresionService.deleteModeloImpresion(imp.id).then(
									function(promise){},
									function(promise){ AlertService.add('danger', promise.data.MSG);}
								)
							});
							$scope.data.ventas.modelos = [];
							break;	
						
						case 'producciones':  // Productos de producciones				
							var stop = false;
							var prods = $scope.data.producciones[index].modelos;
							
							prods.every(function (imp) {
						  	  		
						        colaImpresionService.deleteModeloImpresionProduccion($scope.data.producciones[index].producciones_id).then(
									function(promise){},
									function(promise){ 	AlertService.add('danger', promise.data.MSG);
														stop = true;
									}
								);
								return (stop === false);
							});
							$scope.data.producciones.splice(index, 1);
							break;
						
						default:  // Productos de pedidos
						
							var stop = false;
							var prods = $scope.data.pedidos[index].productos
							
							prods.every(function (imp) {
						  	  		
						        colaImpresionService.deleteModeloImpresionPedido($scope.data.pedidos[index].pedidos_id).then(
									function(promise){},
									function(promise){ 	AlertService.add('danger', promise.data.MSG);
														stop = true;
									}
								);	
								return (stop === false);
							});
							$scope.data.pedidos.splice(index, 1);

							
							
					}; //switch	
				}, 
				
				// Si el modal cierra por CANCELAR
				function (res){}

			); //then

		};

		 
		 
		
		 /***************************************************
		 IMPRIMIR
		 imprimir la cola from. (reposicion / sueltos / pedidoIndex) 
		 ****************************************************/ 
		 $scope.imprimir= function(from,index){
			
			if((from == 'reposicion') || (from == 'sueltos')|| (from == 'ventas')){
				productosAImprimir = (from == 'reposicion')? $scope.data.reposicion.modelos : (from == 'sueltos')? $scope.data.sueltos.modelos: $scope.data.ventas.modelos;
				$scope.imprimirEtiquetasBarcode(productosAImprimir);
			}else
				if(from == 'producciones'){ 
					productosAImprimir = $scope.data.producciones[index].modelos;    
					$scope.imprimirEtiquetasBarcode(productosAImprimir);
				}else
					$scope.imprimirPedido(index);
			
		 }; 
		
		
		/***************************************************************************************
		 IMPRIMIRETIQUETASBARCODE
		 imprime la lista productosAImprimir con el formato de etiqueta de producto con código de barras. 
		 ****************************************************************************************/
		 $scope.imprimirEtiquetasBarcode= function(productosAImprimir) {
		    	
		    var index = 0;	
		    var d; 	
		    
		    $('#codes').empty();
		    
		    if(productosAImprimir.length >0){
		    		
		    	style = {barWidth:1, barHeight:21, fontSize:8};
		    		
				productosAImprimir.forEach(function (prod) {
					  
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
					d.setAttribute('id',"bc"+index);
					d.setAttribute('class',"bc");
					
					p = document.createElement("p");
					p.setAttribute('id',"prod"+index);
					p.setAttribute('class',"nombre");			
					if(prod.nombre.length > 52)
						p.setAttribute('class',"nombreChico")
						
					pr = document.createElement("p");
					pr.setAttribute('class',"precio");
					pr.setAttribute('id',"prec"+index);			
					
					$("#bcTarget"+index).append(d);
					$("#bcTarget"+index).append(p);
					$("#bcTarget"+index).append(pr);
					
					
					$("#prec"+index).text('$'+ prod.precio);
					$("#bc"+index).barcode({'code':cod, crc:false} , "int25", style);
					$("#prod"+index++).text(prod.nombre);
					 
					salto = document.createElement("div");
					salto.setAttribute('class',"saltopagina"); 
					$("#codes").append(salto);                 
				});
				
					
				$("#codes :last-child").last().remove();
				
				$window.print();
				
				
			}	
		}
		
		  
		  
		 
		 
		  /***************************************************
		 IMPRIMIRPEDIDO
		 imprimir la cola de pedidos. Sin codigo de barras 
		 ****************************************************/
		 $scope.imprimirPedido = function(index) {	  
		  
		    var d;
		    	
		    $('#codes').empty();
		    		
			if($scope.data.pedidos[index].productos.length > 0){
				
				var i = 0;
				
				$scope.data.pedidos[index].productos.forEach(function (prod) {	    	

		            
		            bcdiv = document.createElement("div");
					bcdiv.setAttribute('id',"bcTarget"+i);
					bcdiv.setAttribute('class',"etiqueta");
					$("#codes").append(bcdiv);
					
					
					p = document.createElement("p");
					p.setAttribute('class',"nombrePedido");
					p.setAttribute('id',"prod"+i);
					c = document.createElement("p");
					c.setAttribute('class',"cantidadPedido");
					c.setAttribute('id',"cant"+i);
					
					$("#bcTarget"+i).append(p);
					$("#bcTarget"+i).append(c);
					
					
					$("#prod"+i).text(prod.nombre);
					$("#cant"+i++).text(' x '+ prod.cantidad);					
					                  
					salto = document.createElement("div");
					salto.setAttribute('class',"saltopagina"); 
					$("#codes").append(salto);                 
				
				});
				
				$("#codes :last-child").last().remove();
				
				$window.print();
			}	
		
		}
		
 
				
		       
}]);





