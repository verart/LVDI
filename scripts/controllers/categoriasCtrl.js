app.controller('categoriasCtrl', ['$scope', '$modal', '$filter','$log', 'AlertService','gastosService','AuthService','$location',  

	function ($scope, $modal, $filter, $log, AlertService, gastosService, AuthService,$location) {
       
        
		$scope.order = '-nombre';
		$scope.categoria = {id: '', nombre: ''};

	    
		//ALERTS - Mensajes a mostrar
	    $scope.alerts = [ ];
	    
	    /**********************************************************************
	     Recupera en data las categorias
	    **********************************************************************/
	    gastosService.categorias().then(
	    	//Success
			function(promise){ $scope.data = promise.data.DATA;},
			//Error al acceder
			function(error){AuthService.logout();$location.path('/login');}
		);
		
	   /************************************************************************
	    OPENUCATEGORIA
	    Abre un modal con un form para crear/modificar una categoria
	    param: idC -> id de cat. Si viene en blanco es un create 
	    *************************************************************************/	
		$scope.openCategoria = function(idC) {
	 	
	 		$scope.selectedCategoria = (idC != '')? $filter('getById')($scope.data, idC): '';
	 	 	 	    
	 	    var modalInstance = $modal.open({
		    	templateUrl: dir_root+'/templates/categorias/addedit.html',
		    	windowClass: 'wndUsuarios',
		    	controller: 'ModalCategoriasInstanceCtrl',
		    	backdrop: 'static',
		    	keyboard: true,
		    	resolve: {
		        	categoria: function () { return $scope.selectedCategoria; }
		        }
		    });

		    angular.element("#nombre").focus();

		    
		    // Comportamiento al cerrar el modal		    
		    modalInstance.result
		    .then( 
		    	/*************************************************************************************************
		    	 GUARDAR
		    	*************************************************************************************************/
		    	function (res) {
		    		//NUEVO CATEGORIA
		    		if($scope.selectedCategoria == '') {
			    		gastosService.addCategoria(res).then(
			    			//Success
			    			function(promise){
			    				$scope.data.push(promise.data.DATA);
			    				AlertService.add('success', 'La categoría fue creada', 1000);
			    			},
			    			//Error al guardar
			    			function(error){AlertService.add('danger', error.data.MSG, 5000); }
			    		);			
			    	}else{ 
				    	//UPDATE CATEGORIA
				    	gastosService.addCategoria(res).then(
			    			//SUCCESS
			    			function(promise){ AlertService.add('success', 'La categoría fue actualizada', 1000);},
			    			//Error al actualizar
			    			function(error){ AlertService.add('danger', error.data.MSG);}
			    		);
			    	}	
			    }, 
			    /*************************************************************************************************
		    	 CANCELAR
		    	*************************************************************************************************/
			    function (res){ }
			);	
		}
		
		/* NUEVO *******************/
	 	$scope.nuevo = function () {
            $scope.openCategoria('');
        };			
        

        $scope.eliminarCategoria = function(idC){
        	//Solicita confirmación
			var txt_confirm = { msj: "¿Está seguro que desea eliminar esta categoría?", accept:"Si", cancel:"No"};
							    	
			var confirm = $modal.open({
				templateUrl: dir_root+'/templates/confirm.html',
				windowClass: 'wndConfirm',
				controller: 'modalConfirmCtrl',
				resolve: { txt: function(){ return txt_confirm } }
			});

			// Comportamiento al cerrar el modal		    
			confirm.result
				.then( 
					// Si el modal cierra por ACEPTAR
					function (r) {
						gastosService.deleteCategoria(idC).then(
							//Success
						    function(promise){
						    	var index = $filter('getIndexById')($scope.data, idC);
						    	$scope.data.splice(index, 1);
						    },
								//Error al eliminar
						    function(promise){
								AlertService.add('danger', promise.data.MSG);
					    	}
						);
					}, 
				    // Si el modal cierra por CANCELAR
				    function (res){ }
				);   	
        };
        
               
}]);

	
	
	  
/*************************************************************************************************************************
 ModalClientesInstanceCtrl
 Controller del modal para agregar/editar productos  
**************************************************************************************************************************/
app.controller('ModalCategoriasInstanceCtrl',['$scope', '$modalInstance','categoria',

	function ($scope, $modalInstance, categoria) {
		  		  		
		  if(categoria != ''){
		  	var original = angular.copy(categoria);
		  	$scope.categoria = categoria;
		  }else{
		  	$scope.categoria = {nombre:''};
		  	var original = $scope.categoria;
		  }
		  
		  /***************************************************
		   OK
		   Se cierra el modal y retornan los datos del producto
		  ****************************************************/ 
		  $scope.ok = function () {
		  	$modalInstance.close({categoria: $scope.categoria});
		  };
		  
		  
		  /***************************************************
		   CANCEL
		   Se cierra el modal y retornan los datos del producto original, sin cambios
		  ****************************************************/
		  $scope.cancel = function () {
		  	$scope.back2original();
		    $modalInstance.dismiss();
		  };
		  
		  // back2original
		  // Copia en producto los campos originales que se enviaron.  
		  $scope.back2original = function(){
			  $scope.categoria.nombre = original.nombre;
		  };	
		  	  		  	  
}]);