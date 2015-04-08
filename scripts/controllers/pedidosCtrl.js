app.controller('pedidosCtrl', ['$scope','$modal',  'pedidosService', 'productosService', 'clientesPMService', 'AlertService', '$filter', 


	function ($scope, $modal, pedidosService, productosService, clientesPMService, AlertService, $filter) {
       
       
       	$scope.userRole =''; 
	    $scope.order = ['-fecha','-id'];;
	    $scope.filterPedidos = {estado:'Pendiente'};
	    $scope.query = '';
	    $scope.filterSubmitted = '';
	    
	    //ALERTS **** Mensajes a mostrar
	    $scope.alerts = [ ];
	    
	   /*****************************************************************************************************
	     PEDIDOS     
	    *****************************************************************************************************/
	    $scope.page = 0;            
	    $scope.data = [];
	    $scope.parar = false;
	    $scope.pending = false;

	    $scope.cargarPedidos = function () {
	    	
	    	$scope.page++;                   

	    	$scope.pending = true;
	    	pedidosService.pedidos($scope.filterPedidos.estado, $scope.page, $scope.filterSubmitted).then(
		    	//Success
				function(promise){
					if(promise.data.DATA.length > 0){
						for( i=0; i < promise.data.DATA.length; i++)
							$scope.data.push(promise.data.DATA[i]);
					}else{
						if($scope.data.length > 0)
							$('.finPedidos').html('<div class="fin"></div>');
						$scope.parar = true;
					}		
	    			$scope.pending = false;			
				},
				//Error al actualizar
				function(error){
	    			$scope.pending = false;	
					AlertService.add('danger', error.data.MSG);
					$location.path('/login');
	    		}
			); 
		}	


	    $scope.cargarPedidos();
	    
	    
	    
	    	   
	    /*****************************************************************************************************
	     FILTRARPRODUCCIONES 
	     Filtra las producciones de contengan el texto en nombre de responsable o en motivo	    
	    *****************************************************************************************************/
	    $scope.filtrarPedidos = function () {
		  	 $scope.parar = false;
		  	 $scope.data = [];
		  	 $scope.page = 0;
		  	 $scope.filterSubmitted = $scope.query;
		  	 $scope.cargarPedidos();
		  	 
		};
	    
	    
		    
	    /*****************************************************************************************************
	     CARGAR PEDIDOS segun estado	    
	    *****************************************************************************************************/
	    $scope.$watch('filterPedidos.estado', function(newValue, oldValue) {
		  	 if(newValue != oldValue)	 		  	
			  	 $scope.filtrarPedidos();
		}, true);


	    $scope.infoModal = {}
	    	    	    
	    /************************************************************************
	    OPENPEDIDO
	    Abre un modal con un form para crear un nuevo pedido o editarlo
	    param: idPed -> id de pedido. Si viene en blanco es un create 
	    *************************************************************************/	
        $scope.openPedido = function (idPed, userRole) {
  
  	 		$scope.userRole = userRole;
     		$scope.infoModal.pedido = (idPed != '') ? $filter('getById')($scope.data, idPed): '';
	 		$scope.infoModal.userRole = userRole;
	 			
	 		angular.element("#fechaPedido").focus();
	 		
	 	    var modalInstance = $modal.open({
		    	templateUrl: dir_root+'/templates/pedidos/addedit.html',
		    	windowClass: 'wndPedido',
		    	controller: 'ModalPedidoInstanceCtrl',
		    	backdrop: 'static',
		    	keyboard: true,
		    	resolve: {
		        	info: function () { return $scope.infoModal; }
		        }
		    });
		
		    
		    // Comportamiento al cerrar el modal		    
		    modalInstance.result.then( 
		    	/************************************************************
		    	GUARDAR
		    	************************************************************/
		    	function (res) {
			    	
			    	/******************************************
		    		 NUEVO PEDIDO
		    		******************************************/
			    	if($scope.infoModal.pedido == '') {
			    		
				    	pedidosService.addPedido(res).then(
			    			//Success
			    			function(promise){ 
			    				$scope.data.splice(0,0,promise.data.DATA);
			    				AlertService.add('success', 'Se creó un nuevo pedido.', 1000); 
			    			},
			    			//Error al guardar
			    			function(error){
						    	AlertService.add('danger', error.data.MSG);
			    			}
			    		);

			    	}else{ 
				    	
				    	/******************************************
				    	UPDATE PEDIDO
				    	******************************************/
			    		pedidosService.editPedido(res).then(
			    			//SUCCESS
			    			function(promise){
				    			var index = $filter('getIndexById')($scope.data, res.pedido.id);
					    		$scope.data[index] = promise.data.DATA;
					    		AlertService.add('success', 'Se actualizó la información del pedido.', 1000); 
			    			},
			    			//Error al actualizar
			    			function(error){
						    	AlertService.add('danger', error.data.MSG);
			    			}
			    		);
			    	}
			    	
			    	if(res.action == 'print'){
			    		$scope.print(res.pedido);
			    	}	
			    		
			    }, 
			    /************************************************************
		    	CANCELAR
		    	************************************************************/
			    function (res){
			    
			    	/******************************************
				    DELETE PEDIDO
				    ******************************************/
				    if(res.action == 'delete'){
				    	
				    	//Solicita confirmación
				    	var txt_confirm = { msj: "¿Está seguro que desea eliminar este pedido?", accept:"Si", cancel:"No"};	
				    	var idPed = res.idPedido;
				    	
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
						    	 pedidosService.deletePedido(idPed).then(
					    			//Success
					    			function(promise){
					    				var index = $filter('getIndexById')($scope.data, idPed);
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
	 		$scope.userRole = userRole;
            $scope.openPedido('',userRole);
        };
        
        
        /************************************************************
		IMPRIMIR Pedido
		************************************************************/
		$scope.print = function (pedido) {
		  	
		  	if(($scope.userRole == '') || ($scope.userRole == 'admin'))
		  		controllerPrint = modalPdfPedidoCtrl;
		  	else
		  		controllerPrint = modalPdfPedidoNotAdminCtrl;
		  	
		  	//Datos completos del cliente del pedido
			clientesPMService.cliente(pedido.clientesPM_id).then(
				//success
				function(promise){
				    pedido.datosCliente = promise.data.DATA;
			
				  	var printDoc = $modal.open({
							    	templateUrl: dir_root+'/templates/printDoc.html',
							    	windowClass: 'wndPdf',
							    	controller: controllerPrint,
							    	resolve: { pedido: function(){return pedido;} }
					});			
				},
				//Error al actualizar
				function(error){ AlertService.add('danger', error.data.MSG);}
			);					
		}
		
		
        
	    	    
	    /*****************************************************************************************************
	     INFINITE SCROLL	    
	    *****************************************************************************************************/
	    if ($('#infinite-scrolling').size() > 0) {
	    
			$(window).on('scroll', function() {

				if (($(window).scrollTop() > $(document).height() - $(window).height() - 60)& !$scope.parar & !$scope.pending ) {		     	
			  		$scope.cargarPedidos();
		    	}
		  	});
		  	return;
		};
	    
	    	    

        	
}]);



/*************************************************************************************************************************
 ModalPedidoInstanceCtrl
 Controller del modal para agregar/editar modelos  
**************************************************************************************************************************/
var ModalPedidoInstanceCtrl = function ($scope, $modalInstance, $filter, pedidosService, productosService, clientesPMService, info) {
		  
		  
		/**********************************************************************
	    ALERTS
	    Mensajes a mostrar
	    **********************************************************************/
	    $scope.alerts = [ ];
		
		$scope.userRole = info.userRole;
		$scope.form = {};
		$scope.p = {mod_options:[], cl_options:[]};
		$scope.form.idModelo = '';
		$scope.form.modelo = {nombre:'', id:'', precio:'', cantidad:''};
		$scope.form.pago = {monto:'', FP:'Efectivo', created:(new Date()).toISOString().slice(0, 10)};

		$scope.actionBeforeSave='';
 
		  		  
		$scope.fps = [
		  	{'label':'Efectivo','value':'Efectivo'}, 
		  	{'label':'Tarjeta','value':'Tarjeta'},
		  	{'label':'Cheque','value':'Cheque'}, 
		  	{'label':'Transf. Victor','value':'Transf. Victor'},
		  	{'label':'Transf. Fede','value':'Transf. Fede'}];
		  		 
		  $scope.estadosProductos = ['Pendiente', 'Terminado'];	 
		  
		  //campos editables
		  $.mockjax({
			    url: '/estadosProductos',
			    status: 200,
			    responseTime: 400,
			    response: function(settings) {
			        this.responseText = $scope.estadosProductos;
			     }        
		   });

	    /** Manejo de modelos ****************************************************/		  	  
		  
		// SEARCH producto *** Busca un producto 
		$scope.search = function() {		  
			if($scope.form.idModelo != ''){
			  	// Recupera el producto. Retorna como nombre NomProd-NomMod
			  	productosService.getProductoModelo($scope.form.idModelo).then(
					//success
					function(promise){
						$scope.form.modelo = promise.data.DATA; 
					},
					//No existe
					function(error){ 
						if((error.status == 403) || (error.status == 401)){
						    $modalInstance.dismiss({action:'cancel'});
							$location.path('/index');
						}
						$scope.form.modelo.nombre ='';}
				);		
			}		  
		}	
	    // SEARCHBYNAME producto *** Busca un producto
		$scope.searchByName= function() {		  
			if($scope.form.modelo.nombre != ''){
			  	// Recupera el producto. Retorna como nombre NomProd-NomMod
			  	productosService.getProductoModeloByName($scope.form.modelo.nombre).then(
					//success
					function(promise){
						$scope.p.mod_options = promise.data.DATA;
					},					
					//no existe
					function(error){
						if((error.status == 403) || (error.status == 401)){
						    $modalInstance.dismiss({action:'cancel'});
							$location.path('/index');
						}
						$scope.p.mod_options = [];
					}
				);		
			}		  
		}

		// SETMODEL  *** guarda en form.modelo el modelo seleccionado
		$scope.setModel= function(item) {		  
			$scope.form.modelo = item;	
			$scope.form.idModelo = item.id;
		}
	    

	    /** Manejo de cliente ****************************************************/		  	  


		// SEARCHCLIENTEBYNAME  *** Busca un cliente
		$scope.searchClienteByName= function() {		  
			if($scope.form.cliente.nombre != ''){
			  	// Recupera el producto. Retorna como nombre NomProd-NomMod
			  	clientesPMService.getClienteByName($scope.form.cliente.nombre).then(
					//success
					function(promise){
						$scope.p.cl_options = promise.data.DATA;
					},					
					//no existe
					function(error){
						if((error.status == 403) || (error.status == 401)){
						    $modalInstance.dismiss({action:'cancel'});
							$location.path('/index');
						}
						$scope.p.cl_options = [];
					}
				);		
			}		  
		}

		// SETCliente  *** guarda en form.cliente el cliente seleccionado
		$scope.setCliente= function(item) {		  
			$scope.form.cliente = item;	
			$scope.form.idCliente = item.id;
			$scope.pedido.bonificacion = $scope.form.cliente.bonificacion;
		}

		  
		  /***************************************************
		   SUMARPAGOS
		   Retorna la suma de pagos registrados
		  ****************************************************/	
		  $scope.sumarPagos = function(){
			  if($scope.pedido.pagos != undefined){
			  		var tot = 0;
			    	for( i=0; i < $scope.pedido.pagos.length; i++){
				    	tot = tot + parseFloat($scope.pedido.pagos[i].monto, 10); 
				    }	
			    	return tot;	    
			  }
		  }	 
		  
		  
		  /*** Pedido para editar ***/
		  if(info.pedido != ''){
		  
		  	var original = angular.copy(info.pedido);
		  	$scope.pedido = info.pedido;
		  	$scope.pedido.bonificacion = parseInt($scope.pedido.bonificacion,10);
		  	$scope.pedido.fecha= (new Date($scope.pedido.fecha)).toISOString().slice(0, 10);
		  	$scope.pedido.pagos = [];
		  	
		  	//Estado
		  	if(($scope.pedido.estado == 'Entregado-Pago') || ($scope.pedido.estado == 'Entregado-Debe'))
		  	 	$scope.estados = ['Entregado-Pago', 'Entregado-Debe'];
		  	else{
		  	 	$scope.estados = ['Pendiente', 'Terminado', 'Entregado-Pago', 'Entregado-Debe'];	  		  
		  	 	// si el pedido no fue entregado se puede editar (solo si es admin o taller)
		  	 	$scope.EditEnabled  = ( ($scope.userRole=='admin') || ($scope.userRole=='taller') )
		  	}
		  	
		  	//Modelos del pedido
		  	pedidosService.modelosPedido(info.pedido.id).then(
					    			//Success
					    			function(promise){
					    				$scope.pedido.modelos = promise.data.DATA;
					    			},
					    			//Error al eliminar
					    			function(error){
						    			AlertService.add('danger', error.data.MSG);
					    			}
					    		);
					    		
			//Pagos del pedido
		  	pedidosService.pagosPedido(info.pedido.id).then(
					    			//Success
					    			function(promise){
					    				$scope.pedido.pagos = (promise.data.DATA || []);
					    				$scope.pedido.totalPagos = $scope.sumarPagos();

					    			},
					    			//Error al eliminar
					    			function(error){
						    			AlertService.add('danger', error.data.MSG);
					    			}
					    		);		    		
		  	
		  	//Cliente del pedido 
		  	$scope.form.cliente = {nombre:$scope.pedido.cliente, id:$scope.pedido.clientesPM_id, bonificacion:'0'};
		  	
		  	 
		  	
		  	
		  /*** Pedido nuevo ***/
		  }else{
		  
			  $scope.pedido = {
			  			fecha: (new Date()).toISOString().slice(0, 10),
			  			fecha_entrega: (new Date()).toISOString().slice(0, 10),
			  			estado:'Pendiente', 
			  			total:'0', 
			  			clientesPM_id: "", 
			  			cliente: "",
			  			modelos:[],
			  			pagos:[], 
			  			bonificacion:0, 
			  			totalFinal:0, 
			  			FP:'',
			  			nota: ''};
		  			

			  $scope.form.cliente = {nombre:'', id:'', bonificacion:0};
			  
			  $scope.EditEnabled =true;
			  
	    	  $scope.estados = ['Pendiente', 'Terminado', 'Entregado-Pago', 'Entregado-Debe'];	
	    	  
	    	  $scope.pedido.totalPagos = 0;
		  }
		  
		  
		  //Arreglo de modelos para eliminar del pedido. 
		  $scope.pedido.mod2delete = [];
		  $scope.pedido.pagos2delete = [];



		  /****************************************************** FUNCIONES ********************************************************/
		  
		  
		  /***************************************************
		   OK
		   Se cierra el modal y retornan los datos del pedido
		  ****************************************************/ 
		  $scope.ok = function () {
		  	$modalInstance.close({pedido:$scope.pedido, action:$scope.actionBeforeSave});
		  };
		  
		  
		  /***************************************************
		   IMPRIMIR
		   Se cierra el modal e imprime la produccion modificada
		  ****************************************************/
		  $scope.print = function () {		  
		  	$scope.actionBeforeSave='print';		  	
		  };
		  
		  /***************************************************
		   SAVE
		   Se cierra el modal NO imprime la produccion modificada
		  ****************************************************/
		  $scope.save = function () {
		  	$scope.actionBeforeSave='';
		  };
		  
		  
		  /***************************************************
		   CANCEL
		   Se cierra el modal y retornan los datos del pedido original, sin cambios
		  ****************************************************/
		  $scope.cancel = function () {
		  	if($scope.pedido.id != undefined)
		  		$scope.back2original();
		    $modalInstance.dismiss({action:'cancel'});
		  };
		  
		  
		  /***************************************************
		   DELETE
		   Se cierra el modal y retorna un indicador de que hay que eliminar el pedido
		  ****************************************************/
		  $scope.deletePedido = function () {
			  $scope.back2original();	
			  var res = {action:'delete', idPedido:$scope.pedido.id};	  		
			  $modalInstance.dismiss(res);
		  };
		  

		  /***************************************************
		   BACK2ORIGINAL
		  Copia en pedido los campos originales que se enviaron.
		  ****************************************************/  
		  $scope.back2original = function(){
			  $scope.pedido.cliente = original.cliente;
			  $scope.pedido.total = original.total;
			  $scope.pedido.estado = original.estado;
			  $scope.pedido.nota = original.nota;			  
			  $scope.pedido.clientesPM_id = original.clientesPM_id;			  
			  $scope.pedido.bonificacion = original.bonificacion;
			  $scope.pedido.modelos = original.modelos;
			  $scope.pedido.mod2delete = [];
		  };	
		  
		  
		  
		  
		     
		  /******************************************************************************************************/
		  /** MANEJO DE MODELOS **/
		  /******************************************************************************************************/		   
		   
		   
		  /***************************************************
		   ADD modelo
		   Agrega un modelo al pedido. Actualiza los totales
		  ****************************************************/	  
		  $scope.add= function() {
		  
		  	if( ($scope.form.modelo.nombre  !=  '') && ( $scope.form.modelo.nombre  !=  undefined)){
		  	
		  		$scope.form.modelo.cantidad = ($scope.form.modelo.cantidad || 1) 
		  		
			  	$mod = {id: $scope.form.modelo.id, 
			  			nombre: $scope.form.modelo.nombre, 
			  			precio: $scope.form.modelo.precio, 
			  			cantidad: $scope.form.modelo.cantidad,
			  			estado: 'Pendiente'
			  		};
			  		
			  	$scope.pedido.modelos.push($mod);
			  	
			  	$scope.pedido.total =  parseInt($scope.pedido.total,10) + (parseInt($scope.form.modelo.precio,10) *  parseInt($scope.form.modelo.cantidad,10));
			  
			  	$scope.pedido.totalFinal =  parseInt($scope.pedido.total,10) - (parseInt($scope.pedido.total,10) *  parseInt($scope.pedido.bonificacion,10)/100);
			  	
			  	
			  	$scope.form.modelo = {nombre:'', id:'', precio:'', cantidad:''};
			  	angular.element("#newModId").focus();
			  	angular.element("#newMod").val('');
			  	angular.element("#newModId").val('');
			  }
		  }	 
		  
		  
		  
		 /***************************************************
		   REMOVE modelo
		   Quita un modelo del pedido. Actualiza los totales
		  ****************************************************/	  
		  $scope.remove= function(index) {		  	
		  	
		  	$scope.pedido.total =  parseInt($scope.pedido.total,10) - (parseInt($scope.pedido.modelos[index].precio,10) *  parseInt($scope.pedido.modelos[index].cantidad,10));
		  	
		  	if($scope.pedido.modelos[index].idPedMod != null)
		  		$scope.pedido.mod2delete.push({id:$scope.pedido.modelos[index].idPedMod});
		  	
		  	$scope.pedido.modelos.splice(index,1);
		  	
		  }	 
		  
		 
		 
		 /*************************************************************************
		 Cuando se modifica una cantidad debe actualizar los totales.
		 Antes de modificarlo se quita la cantidad anterior y despues se suma el producto con la nueva cantidad
		 *************************************************************************/		 
		 $scope.removeOld= function(index){		 
			 $scope.pedido.total =  parseInt($scope.pedido.total,10) - (parseInt($scope.pedido.modelos[index].precio,10) *  parseInt($scope.pedido.modelos[index].cantidad,10));
		 }
		 
		 $scope.refreshTotal= function(index){		 
			 $scope.pedido.total =  parseInt($scope.pedido.total,10) + (parseInt($scope.pedido.modelos[index].precio,10) *  parseInt($scope.pedido.modelos[index].cantidad,10));
			 	 
		 }
		 
		 
		 
		 /***************************************************
		   WATCH FORM.CLIENTE
		   Cuando se cambia el cliente:
		   - se asigna la bonificacion del cliente
		   - Se guarda el  id del cliente elegido en el modelo
		  ****************************************************/	 		  
		  $scope.$watch('form.cliente', function(newValue, oldValue) {
		    	
		    	if (($scope.form.cliente.id != null) && ($scope.form.cliente.id != 0)) {
			    	
			    	if(($scope.pedido.bonificacion != null) && ($scope.pedido.bonificacion == "0"))
			    		$scope.pedido.bonificacion =  angular.copy($scope.form.cliente.bonificacion); 				    	
			   
			    	$scope.pedido.clientesPM_id = $scope.form.cliente.id;
			    	$scope.pedido.clientesPM = $scope.form.cliente.nombre;
			    }
		  });
		  

		  /***************************************************
		   WATCH PEDIDO.BONIFICACION
		   Actualiza los totalFinal
		  ****************************************************/	 
		  $scope.$watch('pedido.bonificacion', function(newValue, oldValue) {
		  		
		    	var desc =  parseInt($scope.pedido.total,10) * (parseInt($scope.pedido.bonificacion,10)/100);
		    	$scope.pedido.totalFinal =  parseInt($scope.pedido.total,10) - desc;
			  			    
		  });
		  
		  /***************************************************
		   WATCH PEDIDO.TOTAL
		   Actualiza los totalFinal
		  ****************************************************/	 
		  $scope.$watch('pedido.total', function(newValue, oldValue) {
		  		
		    	var desc =  parseInt($scope.pedido.total,10) * (parseInt($scope.pedido.bonificacion,10)/100);
		    	$scope.pedido.totalFinal =  parseInt($scope.pedido.total,10) - desc;
			   			    
		  });
		  
		  
		  /***************************************************
		   WATCH PEDIDO.ESTADO
		   Si el nuevo estado es 'Terminado' pasa el estado de todos los productos a terminado
		  ****************************************************/	 

		  $scope.$watch('pedido.estado', function(newValue, oldValue) {
		  		
		    	if((newValue == 'Terminado')&(oldValue != 'Terminado')){
			    	
			    	for( i=0; i < $scope.pedido.modelos.length; i ++){
				    	
				    	$scope.pedido.modelos[i].estado = 'Terminado';
				    	
			    	}
		    	}	
		    		    
		  });

		  /***************************************************
		   WATCH PEDIDO.MODELOS
		   Si se modifica el listado de modelos se actualiza el total
		  ****************************************************/	
		  $scope.$watch('pedido.modelos', function(newValue, oldValue) {
		  		
		  		if($scope.pedido.modelos != undefined){
			  		var tot = 0;
			    	for( i=0; i < $scope.pedido.modelos.length; i ++){
				    	tot = tot + $scope.pedido.modelos[i].cantidad * $scope.pedido.modelos[i].precio; 
				    }	
			    	$scope.pedido.total = tot;	    
			    }	
		  }, true);

		   
		   
		  /******************************************************************************************************/
		  /** MANEJO DE PAGOS **/
		  /******************************************************************************************************/		   
		   
		  /***************************************************
		   ADDPAGO
		   Agrega el pago guardado en form.pago.  Actualiza los totales
		  ****************************************************/	  
		  $scope.addPago= function() {

		  	if(( $scope.form.pago.monto  !=  '') & ( $scope.form.pago.FP  !=  '')) {
		  	
		  		$scope.form.pago.created = ($scope.form.pago.created ||  (new Date()).toISOString().slice(0, 10)) 
		  					  		
			  	$scope.pedido.pagos.push($scope.form.pago);
			  	 
			  	$scope.pedido.totalPagos =  parseFloat($scope.pedido.totalPagos) + parseFloat($scope.form.pago.monto);		  	
			  	
			  	$scope.form.pago = {monto:'', FP:'Efectivo', created:(new Date()).toISOString().slice(0, 10)};
			  	angular.element("#montoPago").focus();
			  	angular.element("#montoPago").val('');
			  	
			  	if(($scope.pedido.totalFinal - $scope.pedido.totalPagos) == 0){
				  	$scope.pedido.estado = "Entregado-Pago";
			  	} 
			  	
			  }
			}
		  
		  
		  	/***************************************************
		  	REMOVEPAGO
		  	Quita un pago del pedido. Actualiza los totales
		  	****************************************************/	  
		  	$scope.removePago= function(index) {		  	
		  	
		  		$scope.pedido.totalPagos =  parseInt($scope.pedido.totalPagos,10) - parseInt($scope.pedido.pagos[index].monto, 10);
		  	
			  	//Solo si tiene id (esta guardado en la BD), guardo el id en pago2delete
			  	if($scope.pedido.pagos[index].id != null)
			  		$scope.pedido.pagos2delete.push({id:$scope.pedido.pagos[index].id});
			  	
			  	$scope.pedido.pagos.splice(index,1);
			  	
		  	}	 

		   
		   
}



