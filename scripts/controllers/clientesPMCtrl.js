app.controller('clientesPMCtrl', ['$scope', '$modal', '$filter','$log', 'AlertService','clientesPMService', '$timeout', 

	function ($scope, $modal, $filter,$log, AlertService, clientesPMService, $timeout) {
       
        
		$scope.order = '-nombre';
	    $scope.query = '';
	    $scope.filterSubmitted = '';
	    	    
	    /**********************************************************************
	     Recupera en data los clientesPM
	    **********************************************************************/
 	    $scope.page = 0;            
	    $scope.data = [];
	    $scope.parar = false;
	    $scope.pending = false;
	    
	    $scope.cargarClientes = function () {	
	 		$scope.page ++;
	    	$scope.pending = true;
	 		clientesPMService.clientes($scope.page,$scope.filterSubmitted).then(
				//success
				function(promise){
					if(promise.data.DATA.length > 0){
						for( i=0; i < promise.data.DATA.length; i++)
							$scope.data.push(promise.data.DATA[i]);                   
					}else{
						if($scope.data.length > 0)
							$('.finClientes').html('<div class="fin"></div>');
						$scope.parar = true;
					}
	    			$scope.pending = false;
				},
				//Error al actualizar
				function(error){ 
	    			$scope.pending = false;
	    			AlertService.add('danger', error.data.MSG);
					$location.path('/index');
	    		}
			);
        };
	    
	    $scope.cargarClientes();
	    
	    /*****************************************************************************************************
	     FILTRARCLIENTES 
	     Filtra los clientes que contienen en su nombre u localidad el texto	    
	    *****************************************************************************************************/
	    $scope.filtrarClientes = function () {
		  		
		  	 $scope.parar = false;
		  	 $scope.data = [];
		  	 $scope.page = 0;
		  	 $scope.filterSubmitted = $scope.query;
		  	 $scope.cargarClientes();
		  	 
		};
	    	    

	   

	    
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
		    	templateUrl: dir_root+'/templates/clientesPM/addedit.html',
		    	windowClass: 'wndClientesPM',
		    	controller: 'ModalClientesPMInstanceCtrl',
		    	backdrop: 'static',
		    	keyboard: true,
		    	resolve: {
		        	clientePM: function () { return $scope.selectedCliente; }
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
		    		 NUEVO CLIENTE
		    		******************************************/
			    	if($scope.selectedCliente == '') {
			    		clientesPMService.addCliente(res).then(
			    			//Success
			    			function(promise){ console.log(promise.data.DATA);
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
				    	UPDATE CLIENTE
				    	******************************************/
			    		clientesPMService.editCliente(res).then(
			    		
			    			//SUCCESS
			    			function(promise){},
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
						    	 clientesPMService.deleteCliente(idCliente).then(
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
        
        	    	    
	    /*****************************************************************************************************
	     INFINITE SCROLL	    
	    *****************************************************************************************************/
	    if ($('#infinite-scrolling').size() > 0) {
	    
			$(window).on('scroll', function() {

				if (($(window).scrollTop() > $(document).height() - $(window).height() - 60)& !$scope.parar & !$scope.pending ) {		     	
			  		$scope.cargarClientes();
		    	}
		  	});
		  	return;
		};
	    
               
}]);

	
	
	  
/*************************************************************************************************************************
 ModalClientesPMInstanceCtrl
 Controller del modal para agregar/editar clientes  
**************************************************************************************************************************/
var ModalClientesPMInstanceCtrl = function ($scope, $modalInstance, $filter, clientePM) {
		  		  		  
		  
		  if(clientePM != ''){
		  	var original = angular.copy(clientePM);
		  	$scope.clientePM = clientePM;
		  }else{
		  	$scope.clientePM = {nombre:'',local:'', tel:'', tel2:'', direccion:'', localidad:'', email:'' ,bonificacion:'', nota:''}
		  	var original = $scope.clientePM;
		  }
		
		  
		  
		  /***************************************************
		   OK
		   Se cierra el modal y retornan los datos del cliente
		  ****************************************************/ 
		  $scope.ok = function () {
		  	$modalInstance.close({clientesPM:$scope.clientePM});
		  };
		  
		  
		  /***************************************************
		   CANCEL
		   Se cierra el modal y retornan los datos del cliente original, sin cambios
		  ****************************************************/
		  $scope.cancel = function () {
		  	$scope.back2original();
		    $modalInstance.dismiss({action:'cancel'});
		  };
		  
		  
		  /***************************************************
		   DELETE
		   Se cierra el modal y retornan un indicador de que hay que eliminar el cliente
		  ****************************************************/
		  $scope.deleteCliente = function () { console.log($scope.clientePM);
			  $scope.back2original();	
			  var res = {action:'delete', idCliente:$scope.clientePM.id};	  		
			  $modalInstance.dismiss(res);
		  };
		  

		  // back2original
		  // Copia en cliente los campos originales que se enviaron.  
		  $scope.back2original = function(){
			  $scope.clientePM.id = original.id;
			  $scope.clientePM.nombre = original.nombre;
			  $scope.clientePM.local = original.local
			  $scope.clientePM.tel = original.tel
			  $scope.clientePM.tel2 = original.tel2
			  $scope.clientePM.direccion = original.direccion
			  $scope.clientePM.localidad = original.localidad
			  $scope.clientePM.email = original.email
			  $scope.clientePM.bonificacion = original.bonificacion
			  $scope.clientePM.nota = original.nota
		  };	
		  	  		  		  
}


