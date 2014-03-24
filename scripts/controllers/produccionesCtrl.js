app.controller('produccionesCtrl', ['$scope','$modal', '$filter', 'produccionesService', 'productosService', 'responsablesService', 'AlertService',

	function ($scope, $modal, $filter, produccionesService, productosService, responsablesService, AlertService) {
       
        
	    $scope.optFilter = ['Retirado', 'Devuelto'];
	    $scope.order = '-fecha';
	    
	    /*****************************************************************************************************
	     PRODUCCIONES     
	    *****************************************************************************************************/
	    listProducciones = function(data){	    		
		    $scope.data = data;
	    }
	    produccionesService.producciones(listProducciones);
	    
	    
	    
	    
	    
	    /*****************************************************************************************************
	     PRODUCTOS / RESPONSABLES PARA PRODUCCIONES 
	     Información facilitada para crear/mdificar una producción --> listado de productos, listado de responsables	    
	    *****************************************************************************************************/
	    $scope.infoModal = {}
	    $scope.infoModal.p = {mod_options:[], resp_options:[]};
	    
	    
	    //PRODUCTOS - Recupera todos los modelos de cada producto. Retorna como nombre NomProd-NomMod
	    productosService.nombresProductos(1).then(
			//success
			function(promise){
			     promise.data.DATA.forEach(function (prod) {
		             $scope.infoModal.p.mod_options.push({'nombre':prod.nombre, 'id':prod.id});  });                   
			},
			//Error al actualizar
			function(error){ AlertService.add('danger', error.data.MSG);}
		);		
		
		//RESPONSABLES
		responsablesService.nombresResponsables().then(
			//success
			function(promise){
			    promise.data.DATA.forEach(function (resp){
		             $scope.infoModal.p.resp_options.push({'nombre':resp.nombre, 'id':resp.id, 'nota':resp.nota});    });                   
			},
			//Error al actualizar
			function(error){ AlertService.add('danger', error.data.MSG);}
		);
		
		
		
		
		
		
	    /************************************************************************
	    OPENPRODUCCION
	    Abre un modal con un form para crear una nueva produccion o para editarla
	    param: idProd -> id de produccion. Si viene en blanco es un create 
	    *************************************************************************/	
        $scope.openProduccion = function (idProd) {
     
     		if(idProd != '')
	 			$scope.infoModal.produccion = $filter('getById')($scope.data, idProd);
	 		else
	 			$scope.infoModal.produccion = '';
	 		
	 			
	 		angular.element("#fechaProduccion").focus();
	 	
	 		
	 	    var modalInstance = $modal.open({
		    	templateUrl: '/LVDI/templates/producciones/addedit.html',
		    	windowClass: 'wndProduccion',
		    	controller: 'ModalProduccionInstanceCtrl',
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
		    		 NUEVA PRODUCCION
		    		******************************************/
			    	if($scope.infoModal.produccion == '') {
			    		
				    	produccionesService.addProduccion(res.produccion).then(
			    			//Success
			    			function(promise){ 
			    				$scope.data.push(promise.data.DATA);
			    			},
			    			//Error al guardar
			    			function(error){
						    	AlertService.add('danger', error.data.MSG);
			    			}
			    		);

			    		
			    		
			    	}else{ 
				    	
				    	
				    	/******************************************
				    	UPDATE PRODUCCION
				    	******************************************/
			    		produccionesService.editProduccion(res.produccion).then(
			    			//SUCCESS
			    			function(promise){
				    		
			    			},
			    			//Error al actualizar
			    			function(error){
						    	AlertService.add('danger', error.data.MSG);
			    			}
			    		);
			    	};	
			    	
			    	
			    	if(res.action == 'print'){
			    		$scope.print(res.produccion);
			    	}
			    		
			    }, 
			    /************************************************************
		    	CANCELAR
		    	************************************************************/
			    function (res){
			    
			    	/******************************************
				    DELETE PRODUCCION
				    ******************************************/
				    if(res.action == 'delete'){
				    	
				    	//Solicita confirmación
				    	var txt_confirm = { msj: "¿Está seguro que desea eliminar esta producción?", accept:"Si", cancel:"No"};
				    	
				    	var idProd = res.idProduccion;
				    	
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
						    	 produccionesService.deleteProduccion(idProd).then(
					    			//Success
					    			function(promise){
					    				var index = $filter('getIndexById')($scope.data, idProd);
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
	 	$scope.nuevo = function () {
            $scope.openProduccion('');
        };
	   
	   
	    /************************************************************
		IMPRIMIR Produccion
		************************************************************/
		$scope.print = function (prod) {
		  	
		  	var printDoc = $modal.open({
					    	templateUrl: '/LVDI/templates/printDoc.html',
					    	windowClass: 'wndPdf',
					    	controller: modalPdfProduccionCtrl,
					    	resolve: { produccion: function(){return prod;} }
			});
		
			
					
		}
		
		
    
}]);






/*************************************************************************************************************************
 ModalProduccionInstanceCtrl
 Controller del modal para agregar/editar modelos  
**************************************************************************************************************************/
var ModalProduccionInstanceCtrl = function ($scope, $modalInstance, $filter, info) {
		  
		  
		  $scope.estados = ['Retirado', 'Devuelto'];	  			  		  
		  
		  $scope.form = {};
		  $scope.p = {};
		  $scope.form.modelo = {nombre:'', id:''};
		  $scope.p.mod_options = info.p.mod_options;		  
		  $scope.p.resp_options = info.p.resp_options;



		  //Inicializo los datos de la produccion	
		  if(info.produccion != ''){
		  
		  	var original = angular.copy(info.produccion);
		  	$scope.produccion = info.produccion;
		  	$scope.form.responsable = {nombre:$scope.produccion.responsable, id:$scope.produccion.responsables_id};
		  	
		  	$scope.produccion.fecha= (new Date($scope.produccion.fecha)).toISOString().slice(0, 10);
		  	$scope.produccion.fecha_devolucion = (new Date($scope.produccion.fecha_devolucion)).toISOString().slice(0, 10);
		  	
		  }else{
		  
			  $scope.produccion = {
			  			fecha: (new Date()).toISOString().slice(0, 10),
			  			fecha_devolucion: (new Date()).toISOString().slice(0, 10),
			  			estado:'Retirado', 
			  			responsables_id: "", 
			  			responsable: "",
			  			modelos:[],
			  			motivo:'', 
			  			nota: ''};
		  			
			  			var original = $scope.produccion;
			  			$scope.form.responsable = {nombre:'', id:''};
		  }
		  
		  $scope.produccion.mod2delete = [];


		  
		  /***************************************************
		   OK
		   Se cierra el modal y retornan los datos de la produccion
		  ****************************************************/ 
		  $scope.ok = function () {
		  	$modalInstance.close({produccion: $scope.produccion, action: ''});
		  };
		  
		  
		  /***************************************************
		   CANCEL
		   Se cierra el modal y retornan los datos de la produccion original, sin cambios
		  ****************************************************/
		  $scope.cancel = function () {
		    $modalInstance.dismiss({action:'cancel'});
		  };
		  
		 
		  
		  
		  /***************************************************
		   IMPRIMIR
		   Se cierra el modal e imprime la produccion modificada
		  ****************************************************/
		  $scope.print = function () {
		    $modalInstance.close({produccion: $scope.produccion, action: 'print'});
		  };
		  
		 
		  
		  
		  /***************************************************
		   DELETE
		   Se cierra el modal y retorna un indicador de que hay que eliminar la produccion
		  ****************************************************/
		  $scope.deleteProduccion = function () {
			  $scope.back2original();	
			  var res = {action:'delete'};	  		
			  $modalInstance.dismiss(res);
		  };
		  

		  // back2original
		  // Copia en produccion los campos originales que se enviaron.  
		  $scope.back2original = function(){
			  $scope.produccion.responsable = original.responsable;
			  $scope.produccion.nota = original.nota;			  
			  $scope.produccion.responsables_id = original.responsables_id;			  
			  $scope.produccion.fecha = original.fecha;		  
			  $scope.produccion.fecha_devolucion = original.fecha_devolucion;		  
			  $scope.produccion.estado = original.estado;		  
			  $scope.produccion.modelos = original.modelos;
			  $scope.produccion.mod2delete = [];
		  };	
		  
		  
		  
		  
		   
		 /***************************************************
		   WATCH FORM.RESPONSABLE
		   Cuando se cambia el responsable se guarda el  id del resp elegido en la prod
		  ****************************************************/	 		  
		  $scope.$watch('form.responsable', function(newValue, oldValue) {
		    	
		    	if (($scope.form.responsable.id != null) && ($scope.form.responsable.id != 0)) {
			    	
			    	$scope.produccion.responsables_id = $scope.form.responsable.id;
			    	$scope.produccion.responsable = $scope.form.responsable.nombre;
			    }
		  });
		  
		  
		  
		  
		  /***************************************************
		   Manejo de tabla de modelos
		  ****************************************************/		  
		  
		  

		  
		  /***************************************************
		   ADD producto
		   Agrega un modelo a la producción. 
		  ****************************************************/	  
		  $scope.add= function() {
		  
		  	if( $scope.form.modelo.nombre  !=  '') {
		  	
			  	$mod = {id: $scope.form.modelo.id, 
			  			nombre: $scope.form.modelo.nombre, 
			  			estado: 'Retirado'
			  		};
			  	$scope.produccion.modelos.push($mod);
			  	
			  	
			  	$scope.form.modelo = {nombre:'', id:''};
			  	angular.element("#newMod").focus();
			  }
		  }	 
		  
		 
		 
		 
		 
		 /***************************************************
		   REMOVE producto
		   Quita un modelo de la producción.
		  ****************************************************/	  
		  $scope.remove= function(index) {		  	
		  	
		  	if($scope.produccion.modelos[index].idPedMod != null)
		  		$scope.produccion.mod2delete.push({id:$scope.produccion.modelos[index].idPedMod});
		  	
		  	$scope.produccion.modelos.splice(index,1);
		  	
		  }	 
		  
		 
		 /***************************************************
		   TODOSDEVUELTOS producto
		   Marca todos los productos como devueltos
		  ****************************************************/	  
		  $scope.todosDevueltos= function() {		  	
		  	
		  	$scope.produccion.modelos.forEach(function (m) { m.estado = 'Devuelto'; });                   
			
		  }	
		  
		  
		  		  
}






