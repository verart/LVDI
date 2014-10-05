app.controller('gastosCtrl', ['$scope','$modal',  'gastosService', 'AlertService', '$filter', 


	function ($scope, $modal, gastosService, AlertService, $filter) {
       

	    /**********************************************************************
	    ALERTS
	    Mensajes a mostrar
	    **********************************************************************/
	    $scope.alerts = [ ];


		hoy = (new Date()).toISOString().slice(0, 10);
		
		$scope.fps = [
		  	{'label':'Efectivo','value':'Efectivo'}, 
		  	{'label':'Tarjeta','value':'Tarjeta'},
		  	{'label':'Cheque','value':'Cheque'}, 
		  	{'label':'Débito','value':'Debito'},
		  	{'label':'Transferencia','value':'Transferencia'}];

		$scope.gasto = {created: hoy, descripcion:'', monto:'', FP:'Efectivo'};	
	
		$scope.desde = hoy;
		$scope.hasta = hoy;
		
		
		/*****************************************************************************************************
	    CARGAR     
	    *****************************************************************************************************/    	    
	    $scope.cargar = function(){

			gastosService.gastos($scope.desde, $scope.hasta).then(
				//success
				function(promise){
				     $scope.gastos = promise.data.DATA;                   
				},
				//Error al actualizar
				function(error){ AlertService.add('danger', error.data.MSG);}
			);		
		}
			

		/***************************************************
		ADDGASTOS
		Agrega un gasto
		****************************************************/	  
		$scope.addGasto= function() {
		  
		  	if(( $scope.gasto.descripcion  !=  '') &($scope.gasto.monto  !=  '')) {
		  	
			  	gastosService.addGasto($scope.gasto).then(
					//success
					function(promise){
						$scope.gastos.splice(0,0,promise.data.DATA);
						angular.element("#descripcion").focus();
						angular.element("#descripcion").val('');
						angular.element("#monto").val('');
					},
					//Error al actualizar
					function(error){ AlertService.add('danger', error.data.MSG);}
				);		
			  	

			  }
		}
	
	
	
	 
		/***************************************************
		REMOVEGASTO
		Quita un gasto
		****************************************************/	  
		$scope.removeGasto= function(index) {		  	
		  	
		  	
		  	//Solicita confirmación
			var txt_confirm = { msj: "¿Está seguro que desea eliminar este gasto?", accept:"Si", cancel:"No"};
			
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
					gastosService.deleteGasto($scope.gastos[index].id).then(
						//success
						function(promise){
							$scope.gastos.splice(index,1);
						},
						//Error al actualizar
						function(error){ AlertService.add('danger', error.data.MSG);}
					);	  	
						
				}, 
				// Si el modal cierra por CANCELAR
				function (res){}
			);	
		  	
		  }	 
		  	  

		  $scope.cargar();
		  
}]);	


