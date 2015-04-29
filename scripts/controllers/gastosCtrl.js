app.controller('gastosCtrl', ['$scope','$modal',  'gastosService', 'AlertService', '$filter','AuthService', '$location',  


	function ($scope, $modal, gastosService, AlertService, $filter, AuthService,$location) {
       

	    /**********************************************************************
	    ALERTS
	    Mensajes a mostrar
	    **********************************************************************/
	    $scope.alerts = [ ];


		hoy = formatLocalDate();
		
		$scope.fps = [
		  	{'label':'Efectivo','value':'Efectivo'}, 
		  	{'label':'Tarjeta','value':'Tarjeta'},
		  	{'label':'Cheque','value':'Cheque'}, 
		  	{'label':'Débito','value':'Debito'},
		  	{'label':'Transferencia','value':'Transferencia'}];

		$scope.gasto = {created: hoy, descripcion:'', monto:'', FP:'Efectivo'};	
	
		$scope.desde = hoy;
		$scope.hasta = hoy;
		$scope.cat_options = [];
		$scope.catFilter_options = [];
		$scope.categoria = {id:'', nombre:''};
		$scope.categoriaFilter_id = '';
		
		/*****************************************************************************************************
	    CARGAR     
	    *****************************************************************************************************/    	    
	    $scope.cargar = function(){
			gastosService.gastos($scope.desde, $scope.hasta, $scope.categoriaFilter_id).then(
				//success
				function(promise){ $scope.gastos = promise.data.DATA;},
				//Error al actualizar
				function(error){ AuthService.logout(); $location.path('/login');}
			);		
		}

		// ************** FILTER *******************************************

		// SEARCHCATEGORIAFILTERBYNAME  *** Busca una categoria para el filtro
		$scope.searchCategoriaFilterByName= function() {		  
			if($scope.categoriaFilter.nombre != ''){
			  	// Recupera el producto. Retorna como nombre NomProd-NomMod
			  	gastosService.getCategoriasByName($scope.categoriaFilter.nombre).then(
					//success
					function(promise){
						$scope.catFilter_options = promise.data.DATA;
					},					
					//no existe
					function(error){
						if((error.status == 403) || (error.status == 401)){
							$location.path('/login');
						}
						$scope.catFilter_options = [];
					}
				);		
			}		  
		}

		// SETCategoria  *** guarda en gasto la categoria seleccionado
		$scope.setCategoriaFilter= function(item) {		  
			$scope.categoriaFilter_id = item['id'];	
		}



		// ********* NUEVO GASTO ******************************************
			
		// SEARCHCATEGORIABYNAME  *** Busca una categoria
		$scope.searchCategoriaByName= function() {		  
			if($scope.categoria.nombre != ''){
			  	// Recupera el producto. Retorna como nombre NomProd-NomMod
			  	gastosService.getCategoriasByName($scope.categoria.nombre).then(
					//success
					function(promise){
						$scope.cat_options = promise.data.DATA;
					},					
					//no existe
					function(error){
						if((error.status == 403) || (error.status == 401)){
							$location.path('/login');
						}
						$scope.cat_options = [];
					}
				);		
			}		  
		}

		// SETCategoria  *** guarda en gasto la categoria seleccionado
		$scope.setCategoria= function(item) {		  
			$scope.gasto.categoria = item;	
		}




		//ADDGASTOS - Agrega un gasto
		$scope.addGasto= function() {
		  	if(( $scope.gasto.categoria  !=  '') &($scope.gasto.monto  !=  '')) {
			  	gastosService.addGasto($scope.gasto).then(
					//success
					function(promise){
						$scope.gastos.splice(0,0,promise.data.DATA);
						angular.element("#monto").focus();
						angular.element("#descripcion").val('');
						angular.element("#monto").val('');
						angular.element("#categoria").val('');
						$scope.gasto = {created: hoy, descripcion:'', monto:'', FP:'Efectivo', categoria:''};	
					},
					//Error 
					function(error){ AlertService.add('danger', error.data.MSG);}
				);		
			}else
				AlertService.add('danger', 'Debe completar los datos del gasto.', 1000 ); 
		}
	 
		//REMOVEGASTO - Quita un gasto
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


