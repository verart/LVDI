app.controller('clientesCtrl', ['$scope', '$modal', '$filter','$log', 'AlertService','clientesService','$timeout', 

	function ($scope, $modal, $filter, $log, AlertService, clientesService, $timeout) {
       
        
		$scope.order = '-nombre';
	    
	    
	    	    
	    /**********************************************************************
	     Recupera en data los clientesPM
	    **********************************************************************/
	    listClientes = function(data){	    		
		    $scope.data = data;
	    }	    	    
	    clientesService.clientes(listClientes);
	   
 
 
	 
	
	    
	   /************************************************************************
	    OPENCLIENTE
	    Abre un modal con un form para crear un nuevo cliente o editarlo
	    param: idCl -> id de cliente. Si viene en blanco es un create 
	    *************************************************************************/	
		$scope.openCliente = function(idCl) {
	 	
	 	
	 		if(idCl != ''){
	 			$scope.selectedCliente = $filter('getById')($scope.data, idCl);
	 		}else{
	 			$scope.selectedCliente = '';
	 		}	
	 	    
	 	    angular.element("#nombre").focus();
	 	    
	 	    var modalInstance = $modal.open({
		    	templateUrl: dir_root+'/templates/clientes/addedit.html',
		    	windowClass: 'wndClientes',
		    	controller: 'ModalClientesInstanceCtrl',
		    	backdrop: 'static',
		    	keyboard: true,
		    	resolve: {
		        	clientes: function () { return $scope.selectedCliente; }
		        }
		    });
		    
		    var orderBy = $filter('orderBy');
		    
		    // Comportamiento al cerrar el modal		    
		    modalInstance.result
		    .then( 
		    	/*************************************************************************************************
		    	 GUARDAR
		    	*************************************************************************************************/
		    	function (res) {
		    	
		    	
		    		/******************************************
		    		 NUEVO CLIENTE
		    		******************************************/
			    	if($scope.selectedCliente == '') {
			    		clientesService.addCliente(res).then(
			    			//Success
			    			function(promise){
			    				$scope.data.push(promise.data.DATA);
			    				orderBy($scope.data, '-nombre', false);
			    			},
			    			//Error al guardar
			    			function(error){
				    			var res_msg = error.data.MSG;
				    			AlertService.add('danger', res_msg, 5000);
			    			}
			    		);
			    		
			    			
			    	}else{ 
				    	
				    	
				    	/******************************************
				    	UPDATE CLIENTE
				    	******************************************/
			    		clientesService.editCliente(res).then(
			    			//SUCCESS
			    			function(promise){ },
			    			//Error al actualizar
			    			function(error){
				    			AlertService.add('danger', error.data.MSG);
			    			}
			    		);
			    	}
			    		
			    }, 
			    
			    /*************************************************************************************************
		    	 CANCELAR
		    	*************************************************************************************************/
			    function (res){
			    
			    	/******************************************
				    DELETE CLIENTE
				    ******************************************/
				    if(res.action == 'delete'){
				    	
				    	//Solicita confirmación
				    	var txt_confirm = { msj: "¿Está seguro que desea eliminar este cliente?", accept:"Si", cancel:"No"};
				    	var idCliente = res.idCliente;
				    	
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
						    	 clientesService.deleteCliente(idCliente).then(
					    			//Success
					    			function(promise){
					    				var index = $filter('getIndexById')($scope.data, idCliente);
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
	 	$scope.nuevo = function () {
            $scope.openCliente('');
        };			
        
        
        
        
       /************************************************************
		IMPRIMIR mails
		************************************************************/
		$scope.exportarMails = function () {
		  	
		  	
		  	var printDoc = $modal.open({
							    	templateUrl: dir_root+'/templates/printDoc.html',
							    	windowClass: 'wndPdf',
							    	controller: modalPdfClientesMailsCtrl,
							    	resolve: { clientes: function(){return $scope.data;} }
			});
			
		}
        
        
               
}]);

	
	
	  
/*************************************************************************************************************************
 ModalClientesInstanceCtrl
 Controller del modal para agregar/editar productos  
**************************************************************************************************************************/
var ModalClientesInstanceCtrl = function ($scope, $modalInstance, $filter, clientes) {
		  		  		  
		  
		  if(clientes != ''){
		  	var original = angular.copy(clientes);
		  	$scope.clientes = clientes;
		  }else{
		  	$scope.clientes	 = {nombre:'',email:'', nota:''};
		  	var original = $scope.clientes;
		  }
		  
		  
		  /***************************************************
		   OK
		   Se cierra el modal y retornan los datos del producto
		  ****************************************************/ 
		  $scope.ok = function () {
		  	$modalInstance.close({clientes: $scope.clientes});
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
		  $scope.deleteCliente = function () {
			  $scope.back2original();	
			  var res = {action:'delete', idCliente:$scope.clientes.id};	  		
			  $modalInstance.dismiss(res);
		  };
		  

		  // back2original
		  // Copia en producto los campos originales que se enviaron.  
		  $scope.back2original = function(){
			  $scope.clientes.nombre = original.nombre;
			  $scope.clientes.email = original.email;			  
			  $scope.clientes.nota = original.nota;	
		  };	
		  	  
		  	  
}