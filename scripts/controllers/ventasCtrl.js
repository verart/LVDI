app.controller('ventasCtrl', ['$scope','$modal',  'ventasService', 'productosService', 'AlertService', '$filter', 


	function ($scope, $modal, ventasService, productosService, AlertService, $filter) {
       
       
        
/* 	    $scope.order = '-fecha'; */
	    $scope.value = 'hoy';
	    /*****************************************************************************************************
	     VENTAS     
	    *****************************************************************************************************/    	    
	    fechaHoy = (new Date()).toISOString().slice(0, 10);
	    
	    ventasService.ventas(fechaHoy,'').then(
			//success
			function(promise){
			     $scope.data = promise.data.DATA;                   
			},
			//Error al actualizar
			function(error){ AlertService.add('danger', error.data.MSG);}
		);		

	    
	    
	    
	    
	    
	    
	    
	    /*****************************************************************************************************
	     PRODUCTOS 
	     Información facilitada para crear/modificar una venta --> listado de productos	    
	    *****************************************************************************************************/
	    $scope.infoModal = {}
	    $scope.infoModal.p = {mod_options:[]};
	    
	    
	    //PRODUCTOS - Recupera todos los modelos de cada producto. Retorna como nombre NomProd-NomMod
	    productosService.nombresProductos(1).then(
			//success
			function(promise){
			     promise.data.DATA.forEach(function (prod) {
		             $scope.infoModal.p.mod_options.push({'nombre':prod.nombre, 'id':prod.id, 'precio':prod.precio});  });                   
			},
			//Error al actualizar
			function(error){ AlertService.add('danger', error.data.MSG);}
		);		
		
		
		
		
		
		
	   
	    	    	    
	    /************************************************************************
	    OPENVENTA
	    Abre un modal con un form para crear una nueva venta o editarlo
	    param: idVen -> id de venta. Si viene en blanco es un create 
	    *************************************************************************/	
        $scope.openVenta = function (idVen, userRole) {
  
     		if(idVen != '')
	 			$scope.infoModal.venta = $filter('getById')($scope.data, idVen);
	 		else
	 			$scope.infoModal.venta = '';
	 		
	 		$scope.infoModal.userRole = userRole;
	 			
	 		angular.element("#fechaVenta").focus();
	 	
	 		
	 	    var modalInstance = $modal.open({
		    	templateUrl: dir_root+'/templates/ventas/addedit.html',
		    	windowClass: 'wndVenta',
		    	controller: 'ModalVentaInstanceCtrl',
		    	backdrop: 'static',
		    	keyboard: true,
		    	resolve: {
		        	info: function () { return $scope.infoModal; }
		        }
		    });
		
		    
		    // Comportamiento al cerrar el modal		    
		    modalInstance.result
		    .then( 
		    	/************************************************************
		    	GUARDAR
		    	************************************************************/
		    	function (res) {
			    		
			    	
			    	/******************************************
		    		 NUEVO VENTA
		    		******************************************/
			    	if($scope.infoModal.venta == '') {
			    		
				    	ventasService.addVenta(res).then(
			    			//Success
			    			function(promise){ 
			    				$scope.data.splice(0,0,promise.data.DATA);
			    				$scope.nuevo(userRole);
			    			},
			    			//Error al guardar
			    			function(error){
						    	AlertService.add('danger', error.data.MSG);
			    			}
			    		);

			    		
			    		
			    	}	
			    		
			    }, 
			    /************************************************************
		    	CANCELAR
		    	************************************************************/
			    function (res){
			    
			    	/******************************************
				    DELETE VENTA
				    ******************************************/
				    if(res.action == 'delete'){
				    	
				    	//Solicita confirmación
				    	var txt_confirm = { msj: "¿Está seguro que desea eliminar esta venta?", accept:"Si", cancel:"No"};
				    	
				    	var idVenta = res.idVenta;
				    	
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
						    	 ventasService.deleteVenta(idVenta).then(
					    			//Success
					    			function(promise){
					    				var index = $filter('getIndexById')($scope.data, idVenta);
					    				$scope.data.splice(index, 1);
					    			},
					    			//Error al eliminar
					    			function(error){
						    			AlertService.add('danger', error.data.MSG);
					    			}
					    		);
						    }, 
						    // Si el modal cierra por CANCELAR
						    function (res){ }

						);   	
					}  
			    
			    }			
			);	
		}
	     
	     		
		/* NUEVO *******************/
	 	$scope.nuevo = function (userRole) {
            $scope.openVenta('', userRole);
        };
        
        
        
	     		
		/* FILTER  *******************/
	 	$scope.filtrarVentas = function (value) {
	 		
	 		if (value == 'hoy') desde = fechaHoy;
	 		else desde = '';	
	 		
		    ventasService.ventas(desde,'').then(
				//success
				function(promise){
				     $scope.data = promise.data.DATA;                   
				},
				//Error al actualizar
				function(error){ AlertService.add('danger', error.data.MSG);}
			);		


        };
        

        /************************************************************************
	    SHOWVENTA
	    Abre un modal con un form para ver una  venta
	    param: idVen -> id de venta. 
	    *************************************************************************/	
        $scope.showVenta = function (idVen, userRole) {
  
     		$scope.infoModal.venta = $filter('getById')($scope.data, idVen);
     		$scope.infoModal.userRole = userRole;
	 		
	 	    var modalInstance = $modal.open({
		    	templateUrl: dir_root+'/templates/ventas/show.html',
		    	windowClass: 'wndShowVenta',
		    	controller: 'ModalVentaInstanceCtrl',
		    	backdrop: 'static',
		    	keyboard: true,
		    	resolve: {
		        	info: function () { return $scope.infoModal; }
		        }
		    });
		
		    
		    // Comportamiento al cerrar el modal		    
		    modalInstance.result
		    .then( 
		    	
		    	/************************************************************
		    	GUARDAR no se guarda nada
		    	************************************************************/
		    	function (res) { }, 
		    	
			    /************************************************************
		    	CERRAR
		    	************************************************************/
			    function (res){
			    
			    	/******************************************
				    DELETE VENTA
				    ******************************************/
				    if(res.action == 'delete'){
				    	
				    	//Solicita confirmación
				    	var txt_confirm = { msj: "¿Está seguro que desea eliminar esta venta?", accept:"Si", cancel:"No"};
				    	
				    	var idVenta = res.idVenta;
				    	
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
						    	 ventasService.deleteVenta(idVenta).then(
					    			//Success
					    			function(promise){
					    				var index = $filter('getIndexById')($scope.data, idVenta);
					    				$scope.data.splice(index, 1);
					    			},
					    			//Error al eliminar
					    			function(error){
						    			AlertService.add('danger', error.data.MSG);
					    			}
					    		);
						    }, 
						    // Si el modal cierra por CERRAR
						    function (res){ }

						);   	
					}  
			    
			    }			
			);	
		}
	     
	     	
        
        
        	
}]);





/*************************************************************************************************************************
 ModalVentaInstanceCtrl
 Controller del modal para agregar/editar modelos  
**************************************************************************************************************************/
var ModalVentaInstanceCtrl = function ($scope, $modalInstance, productosService, AlertService, $filter, info) {
		  
		  
		  $scope.fps = ['Efectivo', 'Tarjeta', 'Cheque'];	 
		 
		  $scope.userRole = info.userRole;
		  $scope.form = {};
		  $scope.p = {};
		  $scope.form.modelo = {nombre:'', id:'', precio:'', cantidad:''};
		  $scope.p.mod_options = info.p.mod_options;		  
		  $scope.p.cl_options = info.p.cl_options;

		  //Inicializo los datos de la venta	
		  if(info.venta != ''){
		  
		  	var original = angular.copy(info.venta);
		  	$scope.venta = info.venta;
		  	
		  	$scope.venta.fecha= (new Date($scope.venta.fecha)).toISOString().slice(0, 10);
		  	
		  }else{
		  
			  $scope.venta = {
			  			fecha: (new Date()).toISOString().slice(0, 10),
			  			total:'0', 
			  			modelos:[], 
			  			bonificacion:0, 
			  			FP:''};
		  			
			  var original = $scope.venta;
			  $scope.form.cliente = {nombre:'', id:'', bonificacion:0};
		  }
		  


		  
		  $scope.nuevoModeloId = '';

		  
		  
		  /***************************************************
		   OK
		   Se cierra el modal y retornan los datos de la venta
		  ****************************************************/ 
		  $scope.ok = function () {
		  	$modalInstance.close({venta:$scope.venta, action:''});
		  };
		  
		  
		  /***************************************************
		   CANCEL
		   Se cierra el modal y retornan los datos de la venta original, sin cambios
		  ****************************************************/
		  $scope.cancel = function () {
		    $modalInstance.dismiss({action:'cancel'});
		  };
		  
		  
		   
		  
		  
		  
		  /***************************************************
		   DELETE
		   Se cierra el modal y retorna un indicador de que hay que eliminar la venta
		  ****************************************************/
		  $scope.deleteVenta = function () {
			  $scope.back2original();	
			  var res = {action:'delete', idVenta:$scope.venta.id};	  		
			  $modalInstance.dismiss(res);
		  };
		  

		  // back2original
		  // Copia en venta los campos originales que se enviaron.  
		  $scope.back2original = function(){
			  $scope.venta.total = original.total;
			  $scope.venta.fecha = original.fecha;			  
			  $scope.venta.bonificacion = original.venta;
			  $scope.venta.modelos = original.modelos;
			  $scope.venta.FP = original.FP;
		  };	
		  
		  
		  
		  
		  
		  
		  
		  /***************************************************
		   Manejo de tabla de modelos
		  ****************************************************/		  
		  
		  
		  /***************************************************
		   SEARCHADD producto
		   Agrega un modelo a la venta. Actualiza los totales
		  ****************************************************/	  
		  $scope.searchAdd= function() {
		  
		  	if($scope.form.modelo.id != ''){
		  	
			  	$mod = [];
			  	
			  	// Recupera el producto. Retorna como nombre NomProd-NomMod
			  	productosService.getProductoModelo($scope.form.modelo.id).then(
					//success
					function(promise){
					    $mod = promise.data.DATA; 
					     
						if($mod.stock > 0){
							$scope.venta.modelos.push($mod);
							  	
							$scope.venta.total =  parseInt($scope.venta.total,10) + (parseInt($mod.precio,10));
							  
							$scope.venta.totalFinal =  parseInt($scope.venta.total,10) - (parseInt($scope.venta.total,10) *  parseInt($scope.venta.bonificacion,10)/100);
						}else{
							AlertService.add('danger', 'No es posible agregar este producto a la venta.	');
						}
						
						$scope.form.modelo.id = '';	  	
							  	
					                       
					},
					//Error al actualizar
					function(error){ AlertService.add('danger', error.data.MSG);}
				);
		
			}
			  
		  }	
  
		  
		  /***************************************************
		   ADD producto
		   Agrega un modelo a la venta. Actualiza los totales
		  ****************************************************/	  
		  $scope.add= function() {
		  
		  	if( $scope.form.modelo.nombre  !=  '') {
		  	
		  		$scope.form.modelo.cantidad = ($scope.form.modelo.cantidad || 1) 
		  		
			  	$mod = {id: $scope.form.modelo.id, 
			  			nombre: $scope.form.modelo.nombre, 
			  			precio: $scope.form.modelo.precio, 
			  			cantidad: 1
			  		};
			  		
			  	$scope.venta.modelos.push($mod);
			  	
			  	$scope.venta.total =  parseInt($scope.venta.total,10) + (parseInt($scope.form.modelo.precio,10) *  parseInt($scope.form.modelo.cantidad,10));
			  
			  	$scope.venta.totalFinal =  parseInt($scope.venta.total,10) - (parseInt($scope.venta.total,10) *  parseInt($scope.venta.bonificacion,10)/100);
			  	
			  	$scope.form.modelo = {nombre:'', id:'', precio:'', cantidad:''};
			  	angular.element("#newModId").focus();
				angular.element("#newMod").val('');
			  }
		  }	 
		  
		 
		 
		 
		 
		 /***************************************************
		   REMOVE producto
		   Quita un modelo de la venta. Actualiza los totales
		  ****************************************************/	  
		  $scope.remove= function(index) {		  	
		  	
		  	$scope.venta.total =  parseInt($scope.venta.total,10) - parseInt($scope.venta.modelos[index].precio,10);
		  		  		
		  	/* $scope.venta.total =  parseFloat(document.getElementById(amtid4).innerHTML).toFixed(2) - (parseInt($scope.venta.modelos[index].precio,10) *  parseInt($scope.venta.modelos[index].cantidad,10)); */
		  		  	
		  	$scope.venta.modelos.splice(index,1);
		  	
		  }	 
		  
		 
		  
		  /***************************************************
		   WATCH VENTA.BONIFICACION
		   Actualiza  totalFinal
		  ****************************************************/	 
		  $scope.$watch('venta.bonificacion', function(newValue, oldValue) {
		  		
		    		$scope.refreshTotal() ;		  			    
		  });
		  
		  
		  
		  
		  
		  /***************************************************
		   WATCH VENTA.TOTAL
		   Actualiza  totalFinal
		  ****************************************************/	 
		  $scope.$watch('venta.total', function(newValue, oldValue) {
		  		
		    	$scope.refreshTotal() ;
			   			    
		  });
		  
		  
		  
		  
		  /***************************************************
		   WATCH VENTA.MONTOFAVOR
		   Actualiza  totalFinal
		  ****************************************************/	 
		  $scope.$watch('venta.montoFavor', function(newValue, oldValue) {
		  		
		    	$scope.refreshTotal() ;
			   			    
		  });
		  
		  
		  $scope.refreshTotal = function(){
		  
/* 				var desc =  parseFloat(document.getElementById(amtid4).innerHTML).toFixed(2) * (parseInt($scope.venta.bonificacion,10)/100);  */
				var mon = !(($scope.venta.montoFavor == "" || $scope.venta.montoFavor == null))? parseInt($scope.venta.montoFavor,10):0.0;
				
		    	$scope.venta.totalFinal = parseInt($scope.venta.total,10) - mon;
		    	
		    	var desc =  parseInt($scope.venta.totalFinal,10) * (parseInt($scope.venta.bonificacion,10)/100);
		    	$scope.venta.totalFinal = $scope.venta.totalFinal - desc;

		  }
		  		
		  		
		  /***************************************************
		   SEARCH
		   Busca el id ingresado en el listado de productos. Si existe lo muestra en el input de nombres
		  ****************************************************/	 
		  $scope.search = function(){
		  
		  	if($scope.form.modelo.id == '') angular.element("#newMod").val('');
		  	else{
		  		$scope.form.modelo.id = parseInt($scope.form.modelo.id);
			  	$mod = $scope.p.mod_options.filter( function( value ){ return value.id == $scope.form.modelo.id })[0]; 
			  	
			  	if($mod != undefined){		  	
			  		$scope.form.modelo.nombre = $mod.nombre;
			  		$scope.form.modelo.precio = $mod.precio;
			  		angular.element("#newMod").val($mod.nombre);
				}else{
					$scope.form.modelo.nombre = '';
			  		$scope.form.modelo.precio = '';
			  		angular.element("#newMod").val('');
				}
					
			}
		  }  
}



