app.controller('ventasCtrl', ['$scope','$modal',  'ventasService', 'productosService', 'AlertService', '$filter', 


	function ($scope, $modal, ventasService, productosService, AlertService, $filter) {
       
       

		/**********************************************************************
	    ALERTS
	    Mensajes a mostrar
	    **********************************************************************/
	    $scope.alerts = [ ];
	    
	    $scope.order = ['-created','-id'];//'-created';

	    

	    /*****************************************************************************************************
	     VENTAS     
	    *****************************************************************************************************/    	    
	    fechaHoy =  (formatLocalDate()); //(new Date()).toISOString().slice(0, 10);
	    $scope.filterVentas = {filter:'hoy'};
	    $scope.totalVentas = 0;
	    $scope.page = 0;            
	    $scope.data = [];
	    $scope.parar = false;
	    $scope.pending = false;
	    
	    
	    $scope.cargarVentas = function () {
	 		
	 		
	 		desde = ($scope.filterVentas.filter == 'hoy')? fechaHoy : '';
	 		conDeuda = ($scope.filterVentas.filter == 'conDeuda')? 1 : 0;
	 		$scope.page ++;                   
	 		
	    	$scope.pending = true;
	 		ventasService.ventas(desde,'',conDeuda,$scope.page).then(
				//success
				function(promise){
					if(promise.data.DATA.length > 0){
						
						//tomo la última venta agregada
						lastVenta = ($scope.data.length == 0) ? -1 : $scope.data[$scope.data.length-1].id;
						firstNewVenta = promise.data.DATA[0].id;
						
						//Si es la misma que la primera que me traigo en la nueva pag, le agrego los productos.
						if(lastVenta == firstNewVenta){
							for( j=0 ; j < promise.data.DATA[0].modelos.length; j++)
								lastVenta = $scope.data[$scope.data.length-1].modelos.push(promise.data.DATA[0].modelos[j]);				
							n = 1;
						}else 
							n = 0;
							
						for(i = n; i < promise.data.DATA.length; i++)
							$scope.data.push(promise.data.DATA[i]);
							
					}else{
						if($scope.data.length > 0)
							$('.finVentas').html('<div class="fin"></div>');
						$scope.parar = true;
					}
					$scope.pending = false;
				},
				//Error al actualizar
				function(error){ 
					$scope.pending = false;
					AlertService.add('danger', error.data.MSG);
				}
			);
	


        };
	    	    
	    
	    /*****************************************************************************************************
	     CARGAR Ventas segun fecha	    
	    *****************************************************************************************************/
	    $scope.$watch('filterVentas.filter', function(newValue, oldValue) {
		  		
		  	 if(newValue != oldValue){ 
		  	   	$scope.parar = false;
		  	   	$scope.data = [];
		  	   	$scope.page = 0;
		  	 	$scope.cargarVentas();
		  	 }
		  	 
		}, true);
	    		
	    
	    
	    /*****************************************************************************************************
	     PRODUCTOS 
	     Información facilitada para crear/modificar una venta --> listado de productos	    
	    *****************************************************************************************************/
	    $scope.infoModal = {}
	    $scope.infoModal.p = {mod_options:[]};
	    
	    
	    //PRODUCTOS - Recupera todos los modelos de cada producto. Retorna como nombre NomProd-NomMod
	    productosService.nombresProductosDisponibles(1).then(
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
		    		 NUEVA VENTA
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
						    	AlertService.add('danger', error.data.MSG, 5000);
			    			}
			    		);
			    	}else{
				    	
				    	/******************************************
			    		 EDIT VENTA
			    		******************************************/
				    	ventasService.editVenta(res).then(
				    			//Success
				    			function(promise){ 
				    				var index = $filter('getIndexById')($scope.data, res.venta.id);
					    			$scope.data[index] = promise.data.DATA;
				    				AlertService.add('success', promise.data.MSG, 5000);
				    			},
				    			//Error al guardar
				    			function(error){
							    	AlertService.add('danger', error.data.MSG, 5000);
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
					    				AlertService.add('success', 'Se eliminó la venta.', 3000);
					    			},
					    			//Error al eliminar
					    			function(error){
						    			AlertService.add('danger', error.data.MSG,3000);
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
	     
	     		
		/* NUEVO *******************/
	 	$scope.nuevo = function (userRole) {
            $scope.openVenta('', userRole);
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
						    			AlertService.add('danger', error.data.MSG, 5000);
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
	     
	     	
                
        /************************************************************************
	    VIEWVENTA
	    Abre un modal con un form para VER/EDITAR una nueva venta.
	    Si la venta tiene deuda se puede editar. Si ya fue paga solo se puede ver
	    *************************************************************************/	
        $scope.viewVenta = function (idVen, deuda,userRole) {
  
	 		if(deuda==0)
	 			$scope.showVenta(idVen, userRole);
	 		else
	 			$scope.openVenta(idVen, userRole);
	 				
		}
		
        
        /************************************************************************
	    OPENNOTAS
	    Abre un modal con un form para crear una nueva notas o editarlas
	    *************************************************************************/	
        $scope.openNotas = function () {
  
	 			
	 		angular.element("#nota").focus();
	 	
	 		
	 	    var modalInstance = $modal.open({
		    	templateUrl: dir_root+'/templates/ventas/notas.html',
		    	windowClass: 'wndNotas',
		    	controller: 'ModalNotaInstanceCtrl',
		    	backdrop: 'static',
		    	keyboard: true
		    });
		
		}

        
        
	    	    
	    /*****************************************************************************************************
	     INFINITE SCROLL	    
	    *****************************************************************************************************/
	    if ($('#infinite-scrolling').size() > 0) {
	    
			$(window).on('scroll', function() {

				if (($(window).scrollTop() > $(document).height() - $(window).height() - 60)& !$scope.parar & !$scope.pending) {		     	
			  		$scope.cargarVentas();
		    	}
		  	});
		  	return;
		};
	    
	    

        	
}]);





/*************************************************************************************************************************
 ModalVentaInstanceCtrl
 Controller del modal para agregar/editar modelos  
**************************************************************************************************************************/
var ModalVentaInstanceCtrl = function ($scope, $modalInstance, productosService, AlertService, ventasService, $filter, info) {
		  
		  
		  $scope.fps = [
		  	{'label':'Efectivo','value':'Efectivo'}, 
		  	{'label':'Tarjeta','value':'Tarjeta'},
		  	{'label':'Cheque','value':'Cheque'}, 
		  	{'label':'Débito','value':'Debito'}];	 
		 
		  $scope.userRole = info.userRole;
		  $scope.form = {};
		  $scope.p = {};
		  $scope.form.modelo = {nombre:'', id:'', precio:'', cantidad:''};
 		  $scope.form.pago = {monto:'', FP:'', created:formatLocalDate(), bonificacion:0};

		  $scope.p.mod_options = info.p.mod_options;		  
		  $scope.p.cl_options = info.p.cl_options;
		  
		  		  
		  
		  /***************************************************
		   SUMARPAGOS
		   Retorna la suma de pagos registrados
		  ****************************************************/	
		  $scope.sumarPagos = function(){
			  if($scope.venta.pagos != undefined){
			  		var tot = 0;
			    	for( i=0; i < $scope.venta.pagos.length; i++){
				    	tot = tot + (parseFloat($scope.venta.pagos[i].monto, 10) + (parseFloat($scope.venta.pagos[i].monto, 10)*(parseInt($scope.venta.pagos[i].bonificacion)/100))); 
				    }	
			    	return tot;	    
			  }
		  }	 
		  

		  //Inicializo los datos de la venta	
		  if(info.venta != ''){
		  
			  	$scope.venta = info.venta;
			  	var original = angular.copy(info.venta);

				//Pagos de la venta
			  	$scope.venta.pagos = [];
			  	ventasService.pagosVenta(info.venta.id).then(
						
						//Success
						function(promise){
						    	$scope.venta.pagos = (promise.data.DATA || []);
						    	$scope.venta.totalPagos = $scope.sumarPagos();
						},
						//Error al eliminar
						function(error){
								AlertService.add('danger', error.data.MSG,5000);
						}
						
				);
				
				
						  	
		  }else{
		  	
			  $scope.venta = {
			  			created: (formatLocalDate()),
			  			total:'0',
			  			nota:'', 
			  			modelos:[],
			  			pagos:[], 
			  			bonificacion:0, 
			  			FP:null,
			  			totalPagos:0,
			  			deuda:0,
			  			montoFavor:0,
			  			variosPagos:0  //Es el model del radiobutton de uno o varios pagos
			  };
		  			
		  }
		  
		  $scope.nuevoModeloId = '';
		  $scope.venta.mod2delete = [];
		  $scope.venta.pagos2delete = [];
		  $scope.venta.variosPagos = (($scope.venta.pagos.length > 0)||($scope.venta.deuda > 0))?1:0;
						    	

		  /***************************************************
		   BACK2ORIGINAL
		  Copia en venta los campos originales que se enviaron.
		  ****************************************************/  
		  $scope.back2original = function(){
			  $scope.venta.created = original.created;
			  $scope.venta.total = original.total;
			  $scope.venta.nota = original.nota;			  
			  $scope.venta.FP = original.FP;			  
			  $scope.venta.deuda = original.deuda;	
			  $scope.venta.montoFavor = original.montoFavor;			  		  
			  $scope.venta.bonificacion = original.bonificacion;
			  $scope.venta.modelos = original.modelos;
			  $scope.venta.mod2delete = [];
			  $scope.venta.pagos2delete = [];
		  };
		  
		  
		  /***************************************************
		   OK
		   Se cierra el modal y retornan los datos de la nueva venta
		  ****************************************************/ 
		  $scope.ok = function () {
		    
		    //Si varios pagos es 0, cancela la deuda.
		    if($scope.venta.variosPagos == 1) $scope.venta.FP = null;
		  	$scope.venta.deuda = $scope.venta.variosPagos * $scope.venta.deuda;
		  	
		  	$modalInstance.close({venta:$scope.venta, action:''});
		  };
		  
		  
		  /***************************************************
		   CANCEL
		   Se cierra el modal y retornan los datos de la venta original, sin cambios
		  ****************************************************/
		  $scope.cancel = function () {
		  		if($scope.venta.id != undefined)
		    		  	$scope.back2original();
		    	$modalInstance.dismiss({action:'cancel'});
		  };
		  
		  
		   
		  
		  
		  
		  /***************************************************
		   DELETE
		   Se cierra el modal y retorna un indicador de que hay que eliminar la venta
		  ****************************************************/
		  $scope.deleteVenta = function () {
			  $modalInstance.dismiss({action:'delete', idVenta:$scope.venta.id});
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
		  	if( ($scope.form.modelo.nombre  !=  undefined) &&($scope.form.modelo.id  !=  undefined)&&($scope.form.modelo.nombre  !=  "") &&($scope.form.modelo.id  !=  "") )  { 
		  	
		  		$scope.form.modelo.cantidad = ($scope.form.modelo.cantidad || 1) 
		  		
			  	$mod = {id: $scope.form.modelo.id, 
			  			nombre: $scope.form.modelo.nombre, 
			  			precio: $scope.form.modelo.precio, 
			  			cantidad: 1
			  		};
			  		
			  	$scope.venta.modelos.push($mod);
			  	
			  	$scope.venta.total =  parseFloat($scope.venta.total,10) + (parseFloat($scope.form.modelo.precio,10) *  parseInt($scope.form.modelo.cantidad,10));
			  
			  	$scope.venta.totalFinal =  parseFloat($scope.venta.total,10) - (parseFloat($scope.venta.total,10) *  parseInt($scope.venta.bonificacion,10)/100);
			  	
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
		  	
		  	if($scope.venta.modelos[index].idVenMod != null)
		  		$scope.venta.mod2delete.push({id:$scope.venta.modelos[index].idVenMod});	
		  		
		  	$scope.venta.total =  parseFloat($scope.venta.total,10) - parseFloat($scope.venta.modelos[index].precio,10);		  		  	
		  	$scope.venta.modelos.splice(index,1);
		  	
		  }	 
		  
		 
		  
		  
		  
		  /***************************************************
		   WATCH VENTA.BONIFICACION
		   Actualiza  totalFinal, deuda
		  ****************************************************/	 
		  $scope.$watch('venta.bonificacion', function(newValue, oldValue) {		  		
		    		$scope.refreshTotal() ;		  			    
		  });
		  
		  
		  /***************************************************
		   WATCH VENTA.TOTAL
		   Actualiza  totalFinal, deuda
		  ****************************************************/	 
		  $scope.$watch('venta.total', function(newValue, oldValue) {		  		
		    	$scope.refreshTotal() ;			   			    
		  });
		  
		  
		  /***************************************************
		   WATCH VENTA.MONTOFAVOR
		   Actualiza  totalFinal, deuda
		  ****************************************************/	 
		  $scope.$watch('venta.montoFavor', function(newValue, oldValue) {		  		
		    	$scope.refreshTotal() ;			   			    
		  });
		  
		  
		  /***************************************************
		   WATCH VENTA.totalPagos
		   Actualiza  totalFinal, deuda
		  ****************************************************/	 
		  $scope.$watch('venta.totalPagos', function(newValue, oldValue) {		  		
		    	$scope.refreshTotal() ;			   			    
		  });


		  /***************************************************
		   REFRESHTOTAL
		   Actualiza  totalFinal, deuda
		  ****************************************************/	 
		  $scope.refreshTotal = function(){
		  
		  		var mon = !(($scope.venta.montoFavor == "" || $scope.venta.montoFavor == null))? parseFloat($scope.venta.montoFavor,10):0.0;
				
		    	$scope.venta.totalFinal = parseFloat($scope.venta.total,10) - mon;
		    	
		    	var desc =  parseFloat($scope.venta.totalFinal,10) * (parseFloat($scope.venta.bonificacion,10)/100);
		    	$scope.venta.totalFinal = $scope.venta.totalFinal - desc;
		    	
		    	//Actualiza la deuda
		    	//Se pueden dar dos situaciones
		    	// 1- Venta creada con deuda
		    	// 			- la deuda se puede ser 0 si se quitan productos
		    	//			- Existe original con los datos de la venta
		    	// 2- Venta nueva que se esta creando
		    	//			- la deuda puede ser 0 porque recien se crea
		    	//			- original es undefined
		    	// 3- Venta creada sin deuda (en un pago con deuda 0), con lo cual no se 
		    	// 	  debería calcular la deuda
		    	// Solo actualiza la deuda en los dos primero casos
		    	if((original != undefined)&&(original.deuda != 0))
			    	$scope.venta.deuda = ($scope.venta.totalFinal - $scope.venta.totalPagos);
			    else 
			    	if(original == undefined)
			    		$scope.venta.deuda = ($scope.venta.totalFinal - $scope.venta.totalPagos);

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
		  
		  
		  		  
		  /******************************************************************************************************/
		  /** MANEJO DE NOTA **/
		  /******************************************************************************************************/		   
		   
		  /***************************************************
		  ACTUALIZARNOTA
		  Actualiza una nota
		  ****************************************************/	  
		  	$scope.actualizarNota= function() {
		  		  	
			  		ventasService.actualizarNota($scope.venta.nota,$scope.venta.id).then(
						//success
						function(promise){ 
							AlertService.add('success', promise.data.MSG, 5000);	
						},
						//Error al actualizar
						function(error){ AlertService.add('danger', error.data.MSG);}
					);		

				
			}
		  





		  
		  
		   /******************************************************************************************************/
		  /** MANEJO DE PAGOS **/
		  /******************************************************************************************************/		   
		   
		  /***************************************************
		   ADDPAGO
		   Agrega el pago guardado en form.venta.  Actualiza los totales
		  ****************************************************/	  
		  $scope.addPago= function() {

		  	if(( $scope.form.pago.monto  !=  '') & ( $scope.form.pago.FP  !=  '')) {
		  	
		  		$scope.form.pago.created = ($scope.form.pago.created ||  formatLocalDate());//(new Date()).toISOString().slice(0, 10)) 
		  					  		
			  	$scope.venta.pagos.push($scope.form.pago);
			  	 
			  	$scope.venta.totalPagos =  parseFloat($scope.venta.totalPagos) + parseFloat($scope.form.pago.monto) + 		
			  		(parseFloat($scope.form.pago.monto)*($scope.form.pago.bonificacion/100));		  	
			  	
			  	angular.element("#montoPago").focus();
			  	
			  	$scope.form.pago = {monto:'', FP:'Efectivo', created:formatLocalDate(), bonificacion:0};

			  	
			  }
			}
		  
		  
		  
		  
		  
		  	/***************************************************
		  	REMOVEPAGO
		  	Quita un pago de la venta. Actualiza los totales
		  	****************************************************/	  
		  	$scope.removePago= function(index) {		  	
		  	
		  		bonif = ($scope.venta.pagos[index].monto * ($scope.venta.pagos[index].bonificacion/100));
		  		$scope.venta.totalPagos =  parseInt($scope.venta.totalPagos,10) - (parseInt($scope.venta.pagos[index].monto,10) + bonif);
		  	
			  	if($scope.venta.pagos[index].id != null)
			  		$scope.venta.pagos2delete.push({id:$scope.venta.pagos[index].id});
			  	
			  	$scope.form.pago = {monto:'', FP:'Efectivo', created:formatLocalDate(), bonificacion:0};

			  	$scope.venta.pagos.splice(index,1);
			  	
		  	}	 

		 
		 
		 
		 
		  /***************************************************
		   ADDPAGODEFINITIVO
		   Agrega el pago guardado en form.venta.  Actualiza los totales. Guarda en la BD el pago
		  ****************************************************/	  
		  $scope.addPagoDefinitivo= function() {

		  	if(( $scope.form.pago.monto  !=  '') & ( $scope.form.pago.FP  !=  '')) {
		  		
			  	ventasService.addPago($scope.form.pago, $scope.venta.id).then(
			  	
					//success
					function(promise){
						
						$scope.form.pago.created = ($scope.form.pago.created ||  formatLocalDate()) 
		  					  		
					  	$scope.venta.pagos.push(promise.data.DATA.pago);
					  	 
					  	$scope.venta.totalPagos =  parseFloat($scope.venta.totalPagos) + parseFloat($scope.form.pago.monto) + 		
						  							  (parseFloat($scope.form.pago.monto)*(parseInt($scope.form.pago.bonificacion)/100));		  	
					  	
					  	$scope.form.pago = {monto:'', FP:'Efectivo', created:formatLocalDate(), bonificacion:0};
					  	angular.element("#montoPago").focus();
					  	
					},
					//Error al actualizar
					function(error){ AlertService.add('danger', error.data.MSG);}
				);		
			  	
			  }
			  
		  }
		
		
		  /***************************************************
		   REMOVEPAGODEFINITIVO
		   Agrega el pago guardado en form.venta.  Actualiza los totales. Guarda en la BD el pago
		  ****************************************************/	  
		  $scope.removePagoDefinitivo= function(index) {

			  if($scope.venta.pagos[index].id != null){
			  	
			  	ventasService.deletePago($scope.venta.pagos[index].id).then(
			  	
					//success
					function(promise){
						
						$scope.venta.totalPagos =  parseInt($scope.venta.totalPagos,10) - ($scope.venta.pagos[index].monto + 		
					  		($scope.venta.pagos[index].monto * ($scope.venta.pagos[index].bonificacion/100)));
				  	
					  	
					  	$scope.form.pago = {monto:'', FP:'Efectivo', created:formatLocalDate(), bonificacion:0};
		
					  	$scope.venta.pagos.splice(index,1);					  	
					},
					//Error al actualizar
					function(error){ AlertService.add('danger', error.data.MSG);}
				);
			}		
			  	
		  }



}






/*************************************************************************************************************************
 ModalNotaInstanceCtrl
 Controller del modal para agregar/editar Notas  
**************************************************************************************************************************/
app.controller('ModalNotaInstanceCtrl', ['$scope','$modal','$modalInstance', 'notasService', 'AlertService', 


	function ($scope, $modal, $modalInstance, notasService, AlertService) { 

	/**********************************************************************
	ALERTS
	Mensajes a mostrar
	**********************************************************************/
	$scope.alerts = [ ];



	hoy = formatLocalDate();

	$scope.nota = {created: hoy, nota:''};
	
	notasService.notas(hoy, hoy).then(
			//success
			function(promise){
			     $scope.notas = promise.data.DATA;                   
			},
			//Error al actualizar
			function(error){ AlertService.add('danger', error.data.MSG);}
	);		


	/***************************************************
	ADDNOTA
	Agrega una nota
	****************************************************/	  
	$scope.addNota= function() {
		  
		  	if( $scope.nota.nota  !=  '') {
		  	
			  	notasService.addNota($scope.nota).then(
					//success
					function(promise){
						$scope.notas.push(promise.data.DATA);
					},
					//Error al actualizar
					function(error){ AlertService.add('danger', error.data.MSG);}
				);		
			  	
		  		angular.element("#notaNota").focus();
				angular.element("#notaNota").val('');
			  }
	}
	
	
	
	 
	/***************************************************
	REMOVENOTA
	Quita una nota
	****************************************************/	  
	$scope.removeNota= function(index) {		  	
		  	
		  	//Solicita confirmación
			var txt_confirm = { msj: "¿Está seguro que desea eliminar esta nota?", accept:"Si", cancel:"No"};
			
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
					
					notasService.deleteNota($scope.notas[index].id).then(
						//success
						function(promise){
							$scope.notas.splice(index,1);
						},
						//Error al actualizar
						function(error){ AlertService.add('danger', error.data.MSG);}
					);	  		
						
				}, 
				// Si el modal cierra por CANCELAR
				function (res){}
			);	
		  	
	}		 
		 

	
	
	/***************************************************
	salir
	Se cierra el modal 
	****************************************************/ 
	$scope.salir = function () {
		$modalInstance.close();
	};
		 

}]);	



