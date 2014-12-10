app.controller('pedidosespecialesCtrl', ['$scope','$modal', 'pedidosespecialesService', 'AlertService', '$filter', 


	function ($scope, $modal, pedidosespecialesService, AlertService, $filter) {
       
       
       	$scope.userRole =''; 
	    $scope.order = ['-created','-id'];
	    $scope.filterPedidos = {estado:'Pendiente'};
	    $scope.query = '';
	    $scope.filterSubmitted = '';
	    
	    
	    /**********************************************************************
	    ALERTS
	    Mensajes a mostrar
	    **********************************************************************/
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

	    	pedidosespecialesService.pedidos($scope.filterPedidos.estado, $scope.page, $scope.filterSubmitted).then(
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


  	    	    
	    /************************************************************************
	    OPENPEDIDO
	    Abre un modal con un form para crear un nuevo pedido o editarlo
	    param: idPed -> id de pedido. Si viene en blanco es un create 
	    *************************************************************************/	
        $scope.openPedido = function (idPed, userRole) {
  
  
	        $scope.infoModal = {}
	 		$scope.userRole = userRole;

     		if(idPed != ''){
	 			$scope.infoModal.pedido = $filter('getById')($scope.data, idPed);
	 		}else
	 			$scope.infoModal.pedido = '';
	 		
	 		$scope.infoModal.userRole = userRole;
	 			
	 		angular.element("#fechaPedido").focus();
	 	
	 		
	 	    var modalInstance = $modal.open({
		    	templateUrl: dir_root+'/templates/pedidosespeciales/addedit.html',
		    	windowClass: 'wndPedidoespecial',
		    	controller: 'ModalPedidoespecialInstanceCtrl',
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
		    		 NUEVO PEDIDO
		    		******************************************/
			    	if($scope.infoModal.pedido == '') {
			    		
				    	pedidosespecialesService.addPedido(res).then(
			    			//Success
			    			function(promise){ 
			    				$scope.data.splice(0,0,promise.data.DATA);
					    		AlertService.add('success', promise.data.MSG, 3000);
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
			    		pedidosespecialesService.editPedido(res).then(
			    			//SUCCESS
			    			function(promise){ 
				    			var index = $filter('getIndexById')($scope.data, res.pedido.id); 
					    		$scope.data[index] = promise.data.DATA;
					    		AlertService.add('success', promise.data.MSG, 3000);

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
					    confirm.result
					    .then( 
					    	// Si el modal cierra por ACEPTAR
					    	function (r) {
						    	 pedidosespecialesService.deletePedido(idPed).then(
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
        
		
        
	    	    
	    /*****************************************************************************************************
	     INFINITE SCROLL	    
	    *****************************************************************************************************/
	    if ($('#infinite-scrolling').size() > 0) {
	    
			$(window).on('scroll', function() {

				if (($(window).scrollTop() > $(document).height() - $(window).height() - 60) & !$scope.parar & !$scope.pending ) {		     	
			  		$scope.cargarPedidos();
		    	}
		  	});
		  	return;
		};
	    
	    	    

        	
}]);



/*************************************************************************************************************************
 ModalPedidoespecialInstanceCtrl
 Controller del modal para administrar un pedido 
**************************************************************************************************************************/
var ModalPedidoespecialInstanceCtrl = function ($scope, $modalInstance, $filter, pedidosespecialesService, clientesService, info) {
		  
		  
		/**********************************************************************
	    ALERTS
	    Mensajes a mostrar
	    **********************************************************************/
	    $scope.alerts = [ ];
		  
		  		  
		$scope.fps = [
		  	{'label':'Efectivo','value':'Efectivo'}, 
		  	{'label':'Tarjeta','value':'Tarjeta'},
		  	{'label':'Cheque','value':'Cheque'}, 
		  	{'label':'Transferencia','value':'Transferencia'}];
		  		 
		  
		  
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
		  
		  
		$scope.userRole = info.userRole;
		$scope.form = {};
		$scope.p = {};
		$scope.form.pago = {monto:'', FP:'Efectivo', created:(new Date()).toISOString().slice(0, 10), bonificacion:''};
		  


		
		// CLIENTES		
		$scope.getList = function(term) {
			return clientesService.list(term);
		}




		$scope.actionBeforeSave='';

		/*** Pedido para editar ***/
		if(info.pedido != ''){
		  
		 	var original = angular.copy(info.pedido);
		  	$scope.pedido = info.pedido;
		  	$scope.pedido.total = parseFloat(info.pedido.total);
		  	$scope.pedido.bonificacion = parseInt($scope.pedido.bonificacion,10);
		  	$scope.pedido.fecha= (new Date($scope.pedido.created)).toISOString().slice(0, 10);
		  	$scope.pedido.pagos = [];
		  	
		  	//Estado
		  	if(($scope.pedido.estado == 'Entregado-Pago') || ($scope.pedido.estado == 'Entregado-Debe'))
		  	 	$scope.estados = ['Entregado-Pago', 'Entregado-Debe'];
		  	else{
		  	 	$scope.estados = ['Pendiente', 'Terminado', 'Entregado-Pago', 'Entregado-Debe'];	  		  
		  	 	// si el pedido no fue entregado se puede editar (solo si es admin o taller)
		  	 	$scope.EditEnabled  = ( ($scope.userRole=='admin') || ($scope.userRole=='taller') )
		  	}

					    		
			//Pagos del pedido
		  	pedidosespecialesService.pagosPedido(info.pedido.id).then(
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
		  	$scope.form.cliente = {nombre:$scope.pedido.cliente, id:$scope.pedido.clientes_id};
		  	
		  	 
		  	
		  	
		  /*** Pedido nuevo ***/
		  }else{
		  
			  $scope.pedido = {
			  			created: formatLocalDate(),
			  			fecha_entrega: formatLocalDate(),
			  			estado:'Pendiente', 
			  			total:'', 
			  			clientes_id: "", 
			  			cliente: "",
			  			pagos:[], 
			  			bonificacion:0, 
			  			FP:'',
			  			descripcion: ''};
		  			

			  $scope.form.cliente = {nombre:'', id:''};
			  
			  $scope.EditEnabled =true;
			  
	    	  $scope.estados = ['Pendiente', 'Terminado', 'Entregado-Pago', 'Entregado-Debe'];	
	    	  
	    	  $scope.pedido.totalPagos = 0;
		  }
		  
		  
		  //Arreglo para eliminar pagos del pedido. 
		  $scope.pedido.pagos2delete = [];



		  /****************************************************** FUNCIONES ********************************************************/
		  
		  
		  /***************************************************
		   OK
		   Se cierra el modal y retornan los datos del pedido
		  ****************************************************/ 
		  $scope.ok = function () {
		  	$scope.pedido.clientes_id =  $scope.form.cliente.id;
		  	$scope.pedido.cliente =  $scope.form.cliente.nombre;
		  	$modalInstance.close({pedido:$scope.pedido, action:$scope.actionBeforeSave});
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
			  $scope.pedido.descripcion = original.descripcion;			  
			  $scope.pedido.clientes_id = original.clientes_id;			  
			  $scope.pedido.bonificacion = original.bonificacion;
		  };	
		  
		  
		  
		  
		   
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
		  		$scope.form.pago.bonificacion = ($scope.form.pago.bonificacion || 0) 			  		
			  	$scope.pedido.pagos.push($scope.form.pago);
			  	 
			  	$scope.pedido.totalPagos =  parseFloat($scope.pedido.totalPagos) + parseFloat($scope.form.pago.monto);		  	
			  	
			  	$scope.form.pago = {monto:'', FP:'Efectivo', created:(new Date()).toISOString().slice(0, 10)};
			  	angular.element("#montoPago").focus();
			  	angular.element("#montoPago").val('');
			  	
			  	if((parseFloat($scope.pedido.total) - $scope.pedido.totalPagos) == 0){
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



