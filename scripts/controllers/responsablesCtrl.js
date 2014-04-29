app.controller('responsablesCtrl', ['$scope', '$modal', '$filter','$log', 'AlertService','responsablesService', '$timeout', 

	function ($scope, $modal, $filter,$log, AlertService, responsablesService, $timeout) {
       
        
		$scope.order = '-nombre';
	    
	    	    
	    /**********************************************************************
	     Recupera en data los clientesPM
	    **********************************************************************/
	    listRes = function(data){	    		
		    $scope.data = data;
	    }	    	    
	    responsablesService.responsables(listRes);
	   

	    
	   /************************************************************************
	    OPENCLIENTE
	    Abre un modal con un form para crear un nuevo cliente o editarlo
	    param: idR -> id de resp. Si viene en blanco es un create 
	    *************************************************************************/	
		$scope.openRes = function(idR) {
	 	
	 	
	 		if(idR != ''){
	 			$scope.selectedRes = $filter('getById')($scope.data, idR);
	 		}else{
	 			$scope.selectedRes = '';
	 		}	
	 		
	 		
	 		angular.element("#nombre").focus();
	 	    
	 	    var modalInstance = $modal.open({
		    	templateUrl: '/LVDI/templates/responsables/addedit.html',
		    	windowClass: 'wndClientesPM',
		    	controller: 'ModalResponsablesInstanceCtrl',
		    	backdrop: 'static',
		    	keyboard: true,
		    	resolve: {
		        	responsable: function () { return $scope.selectedRes; }
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
		    		 NUEVO RESPONSABLE
		    		******************************************/
			    	if($scope.selectedRes == '') {
			    		responsablesService.addRes(res).then(
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
				    	UPDATE RESPONSABLE
				    	******************************************/
			    		responsablesService.editRes(res).then(
			    		
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
				    	var txt_confirm = { msj: "¿Está seguro que desea eliminar este responsables de producción?", accept:"Si", cancel:"No"};
				    	var idR = res.idR;
				    	
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
						    	 responsablesService.deleteRes(idR).then(
					    			//Success
					    			function(promise){
					    				var index = $filter('getIndexById')($scope.data, idR);
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
            $scope.openRes('');
        };			
        
               
}]);

	
	
	  
/*************************************************************************************************************************
 ModalClientesPMInstanceCtrl
 Controller del modal para agregar/editar clientes  
**************************************************************************************************************************/
var ModalResponsablesInstanceCtrl = function ($scope, $modalInstance, $filter, responsable) {
		  		  		  
		  
		  if(responsable != ''){
		  	var original = angular.copy(responsable);
		  	$scope.responsable = responsable;
		  }else{
		  	$scope.responsable = {nombre:'',local:'', tel:'', tel2:'', direccion:'', localidad:'', email:'' ,bonificacion:'', nota:''}
		  	var original = $scope.responsable;
		  }
		
		  
		  
		  /***************************************************
		   OK
		   Se cierra el modal y retornan los datos del responsable
		  ****************************************************/ 
		  $scope.ok = function () {
		  	$modalInstance.close({responsables:$scope.responsable});
		  };
		  
		  
		  /***************************************************
		   CANCEL
		   Se cierra el modal y retornan los datos del responsable original, sin cambios
		  ****************************************************/
		  $scope.cancel = function () {
		  	$scope.back2original();
		    $modalInstance.dismiss({action:'cancel'});
		  };
		  
		  
		  /***************************************************
		   DELETE
		   Se cierra el modal y retornan un indicador de que hay que eliminar el cliente
		  ****************************************************/
		  $scope.deleteRes = function () { console.log($scope.responsable);
			  $scope.back2original();	
			  var res = {action:'delete', idR:$scope.responsable.id};	  		
			  $modalInstance.dismiss(res);
		  };
		  

		  // back2original
		  // Copia en cliente los campos originales que se enviaron.  
		  $scope.back2original = function(){
			  $scope.responsable.id = original.id;
			  $scope.responsable.nombre = original.nombre;
			  $scope.responsable.marca = original.local
			  $scope.responsable.tel = original.tel
			  $scope.responsable.tel2 = original.tel2
			  $scope.responsable.direccion = original.direccion
			  $scope.responsable.localidad = original.localidad
			  $scope.responsable.email = original.email
			  $scope.responsable.nota = original.nota
		  };	
		  	  		  		  
}


