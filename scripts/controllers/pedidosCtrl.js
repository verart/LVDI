app.controller('pedidosCtrl', ['$scope','$modal',  'pedidosService', 'productosService', 'clientesPMService', 'AlertService', '$filter', 


	function ($scope, $modal, pedidosService, productosService, clientesPMService, AlertService, $filter) {
       
       
       	$scope.userRole =''; 
	    $scope.order = '-fecha';
	    $scope.filterPedidos = {estado:''};
	    
	    
	    
	    
	    
	    /*****************************************************************************************************
	     PEDIDOS     
	    *****************************************************************************************************/
	    listPedidos = function(data){	    		
		    $scope.data = data;
	    }	    	    
	    pedidosService.pedidos(listPedidos);
	    
	    
	    
	    
	    
	    
	    
	    /*****************************************************************************************************
	     PRODUCTOS / CLIENTES PARA PEDIDOS 
	     Información facilitada para crear/mdificar un pedido --> listado de productos, listado de clientes	    
	    *****************************************************************************************************/
	    $scope.infoModal = {}
	    $scope.infoModal.p = {mod_options:[], cl_options:[]};
	    
	    
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
		
		//CLIENTES
		clientesPMService.nombresClientes().then(
			//success
			function(promise){
			    promise.data.DATA.forEach(function (cl){
		             $scope.infoModal.p.cl_options.push({'nombre':cl.nombre, 'id':cl.id, 'bonificacion':cl.bonificacion});    });                   
			},
			//Error al actualizar
			function(error){ AlertService.add('danger', error.data.MSG);}
		);
		
		
		
		
		
	   
	    	    	    
	    /************************************************************************
	    OPENPEDIDO
	    Abre un modal con un form para crear un nuevo pedido o editarlo
	    param: idPed -> id de pedido. Si viene en blanco es un create 
	    *************************************************************************/	
        $scope.openPedido = function (idPed, userRole) {
  
  
	 		$scope.userRole = userRole;

     		if(idPed != ''){
	 			$scope.infoModal.pedido = $filter('getById')($scope.data, idPed);
	 		}else
	 			$scope.infoModal.pedido = '';
	 		
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
			    		
				    	pedidosService.addPedido(res).then(
			    			//Success
			    			function(promise){ 
			    				$scope.data.splice(0,0,promise.data.DATA);
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
		
		
        
        	
}]);



/*************************************************************************************************************************
 ModalPedidoInstanceCtrl
 Controller del modal para agregar/editar modelos  
**************************************************************************************************************************/
var ModalPedidoInstanceCtrl = function ($scope, $modalInstance, $filter, info) {
		  
		  
		  $scope.fps = ['Efectivo', 'Tarjeta', 'Cheque'];	 
		  $scope.estadosProductos = ['Pendiente', 'Terminado'];	 
		  
		  
		      $.mockjax({
			    url: '/estadosProductos',
			    status: 200,
			    responseTime: 400,
			    response: function(settings) {
			        this.responseText = $scope.estadosProductos;
			     }        
			});
		  
		  
		  $scope.userRole = info.userRole;
		  $scope.form = {};
		  $scope.p = {};
		  $scope.form.modelo = {nombre:'', id:'', precio:'', cantidad:''};
		  $scope.p.mod_options = info.p.mod_options;		  
		  $scope.p.cl_options = info.p.cl_options;

		  $scope.actionBeforeSave='';

		  //Inicializo los datos del pedido	
		  if(info.pedido != ''){
		  
		  	var original = angular.copy(info.pedido);
		  	$scope.pedido = info.pedido;
		  	$scope.pedido.bonificacion = parseInt($scope.pedido.bonificacion,10);
		  	$scope.form.cliente = {nombre:$scope.pedido.cliente, id:$scope.pedido.clientesPM_id, bonificacion:'0'};
		  	
		  	$scope.pedido.fecha= (new Date($scope.pedido.fecha)).toISOString().slice(0, 10);
		  	
		  	 if(($scope.pedido.estado == 'Entregado-Pago') || ($scope.pedido.estado == 'Entregado-Debe'))
		  	 	$scope.estados = ['Entregado-Pago', 'Entregado-Debe'];
		  	 else{
		  	 	$scope.estados = ['Pendiente', 'Terminado', 'Entregado-Pago', 'Entregado-Debe'];	  		  
		  	 	
		  	 	// si el pedido no fue entregado se puede editar (solo si es admin o taller)
		  	 	$scope.EditEnabled  = ( ($scope.userRole=='admin') || ($scope.userRole=='taller') )
		  	 }
		  	
		  	
		  }else{
		  
			  $scope.pedido = {
			  			fecha: (new Date()).toISOString().slice(0, 10),
			  			fecha_entrega: (new Date()).toISOString().slice(0, 10),
			  			estado:'Pendiente', 
			  			total:'0', 
			  			clientesPM_id: "", 
			  			cliente: "",
			  			modelos:[], 
			  			bonificacion:0, 
			  			totalFinal:0, 
			  			FP:'',
			  			nota: ''};
		  			
			  var original = $scope.pedido;
			  $scope.form.cliente = {nombre:'', id:'', bonificacion:0};
			  
			  $scope.EditEnabled =true;
			  
			  
	    	  $scope.estados = ['Pendiente', 'Terminado', 'Entregado-Pago', 'Entregado-Debe'];	
		  }
		  
		  $scope.pedido.mod2delete = [];


		  
		  
		  
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
		  

		  // back2original
		  // Copia en pedido los campos originales que se enviaron.  
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
		  
		  
		  
		  
		  
		  
		  
		  /***************************************************
		   Manejo de tabla de modelos
		  ****************************************************/		  
		  
		  

		  
		  /***************************************************
		   ADD producto
		   Agrega un modelo al pedido. Actualiza los totales
		  ****************************************************/	  
		  $scope.add= function() {
		  
		  	if( $scope.form.modelo.nombre  !=  '') {
		  	
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
			  }
		  }	 
		  
		  
		  
		  
		  
		 /***************************************************
		   REMOVE producto
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



