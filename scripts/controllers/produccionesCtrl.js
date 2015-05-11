app.controller('produccionesCtrl', 

	['$scope','$modal', '$filter', 'produccionesService', 'productosService', 'responsablesService', 'AlertService',

	function ($scope, $modal, $filter, produccionesService, productosService, responsablesService, AlertService) {
       
       	/**********************************************************************
	    ALERTS
	    Mensajes a mostrar
	    **********************************************************************/
	    $scope.alerts = [ ];
	    
        /**********************************************************************
	    TABS
	    Manejo de pesatañas
	    **********************************************************************/
	    $('#tabs a').click(function (e) {
		  e.preventDefault()
		  $(this).tab('show')
		});
		
		
	    $scope.filterProds ={ estado:'Retirado'};
	    $scope.order = ['-fecha','-id'];;
	    $scope.query = '';
	    $scope.filterSubmitted = '';

	    //PRODUCCIONES     
	    $scope.page = 0;            
	    $scope.data = [];
	    $scope.parar = false;
	    $scope.pending = false;
	    
	    $scope.cargarProducciones = function () {
		    
		    if(!$scope.parar){
		    	$scope.parar = true;
	    		$scope.pending = true;
			    $scope.page ++; 
		    
		    	produccionesService.producciones($scope.filterProds.estado, $scope.page, $scope.filterSubmitted).then(
					//success
					function(promise){
					     if(promise.data.DATA.length > 0){
							for( i=0; i < promise.data.DATA.length; i++)
								$scope.data.push(promise.data.DATA[i]);
						    $scope.parar = false;
						}else{
							if($scope.data.length > 0)
								$('.finProducciones').html('<div class="fin"></div>');
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
	    }  
	    
	    	   
	   
	   
	    /*****************************************************************************************************
	     FILTRARPRODUCCIONES 
	     Filtra las producciones de contengan el texto en nombre de responsable o en motivo	    
	    *****************************************************************************************************/
	    $scope.filtrarProducciones = function () {
		  		
		  	 $scope.parar = false;
		  	 $scope.data = [];
		  	 $scope.page = 0;
		  	 $scope.filterSubmitted = $scope.query;
		  	 $scope.cargarProducciones();
		  	 
		};
	    
		    
	    /*****************************************************************************************************
	     CARGAR PRODUCCIONES segun estado	    
	    *****************************************************************************************************/
	    $scope.$watch('filterProds.estado', function(newValue, oldValue) {
		  	
		  	 if(newValue != oldValue) 
		  	 	$scope.filtrarProducciones();
    
		}, true);
	    
	    
	    
	    
	    
	    
	    
	    
	    /*****************************************************************************************************
	     PRODUCTOS / RESPONSABLES PARA PRODUCCIONES 
	     Información facilitada para crear/mdificar una producción --> listado de productos, listado de responsables	    
	    *****************************************************************************************************/
	    $scope.infoModal = {};
		
		
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
		    	templateUrl: dir_root+'/templates/producciones/addedit.html',
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
			    		
				    	produccionesService.addProduccion(res).then(
			    			//Success
			    			function(promise){ 
			    				$scope.data.splice(0,0,promise.data.DATA);
			    				AlertService.add('success', 'Se creó una nueva producción.', 1000); 
			    				//Imprimo el comprobante
						    	if(res.action == 'print'){
						    		$scope.print(promise.data.DATA);
						    	}

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
			    		produccionesService.editProduccion(res).then(
			    			//SUCCESS
			    			function(promise){
				    			var index = $filter('getIndexById')($scope.data, promise.data.DATA.id);
					    		$scope.data[index] = promise.data.DATA;
					    		AlertService.add('success', 'Se actualizó la información de la producción.', 1000); 
			    				//Imprimo el comprobante
						    	if(res.action == 'print'){
						    		$scope.print(promise.data.DATA);
						    	}
			    			},
			    			//Error al actualizar
			    			function(error){
				    			AlertService.add('danger', error.data.MSG);
			    			}
			    		);
			    	};	
			    	
			    		
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
	 	$scope.nuevo = function (userRole) {
            $scope.openProduccion('',userRole);
        };
	   

	   
	   
	    /************************************************************
		IMPRIMIR Produccion
		************************************************************/
		$scope.print = function (prod) {
		  	
		  	var printDoc = $modal.open({
					    	templateUrl: dir_root+'/templates/printDoc.html',
					    	windowClass: 'wndPdf',
					    	controller: modalPdfProduccionCtrl,
					    	resolve: { produccion: function(){return prod;} }
			});
		
			
					
		}
		
		
			    	    
	    /*****************************************************************************************************
	     INFINITE SCROLL	    
	    *****************************************************************************************************/
	    if ($('#infinite-scrolling').size() > 0) {	    
			$(window).on('scroll', function() {
				if (($(window).scrollTop() > $(document).height() - $(window).height() - 60) & !$scope.parar & !$scope.pending ) {		     	
			  		$scope.cargarProducciones();
		    	}
		  	});
		  	return;
		};
}]);






/*************************************************************************************************************************
 ModalProduccionInstanceCtrl
 Controller del modal para agregar/editar modelos  
**************************************************************************************************************************/
app.controller('ModalProduccionInstanceCtrl', ['$scope', '$modalInstance', '$filter', 'produccionesService', 'productosService', 'responsablesService', 'info',

	function ($scope, $modalInstance, $filter, produccionesService,productosService,responsablesService,info) {		  
		  
		  $scope.estados = ['Retirado','Devuelto'];
			    
		    $.mockjax({
			    url: '/estados',
			    status: 200,
			    responseTime: 400,
			    response: function(settings) {
			        this.responseText = $scope.estados;
			     }        
			});

  		  
		  
		  $scope.form = {};
		  $scope.p = {};
		  $scope.form.modelo = {nombre:'', id:''};
		  
		  //Inicializo los datos de la produccion	
		  if(info.produccion != ''){
		  
		  	var original = angular.copy(info.produccion);
		  	$scope.produccion = info.produccion;
		  	$scope.form.responsable = {nombre:$scope.produccion.responsable, id:$scope.produccion.responsables_id};
		  	
		  			  	
		  	//Modelos del pedido
		  	produccionesService.modelosProduccion(info.produccion.id).then(
					    			//Success
					    			function(promise){
					    				$scope.produccion.modelos = promise.data.DATA;
					    			},
					    			//Error al eliminar
					    			function(error){
						    			AlertService.add('danger', error.data.MSG);
					    			}
					    		);
		  	
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
		  $scope.actionBeforeSave='';
		  
		  
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
	    

	    /** Manejo de RESPONSABLES ****************************************************/		  	  


		// SEARCHRESPONSABLEBYNAME  *** Busca un responsable
		$scope.searchResponsableByName= function() {		  
			if($scope.form.responsable.nombre != ''){
			  	// Recupera el producto. Retorna como nombre NomProd-NomMod
			  	responsablesService.getResponsableByName($scope.form.responsable.nombre).then(
					//success
					function(promise){
						$scope.p.resp_options = promise.data.DATA;
					},					
					//no existe
					function(error){
						if((error.status == 403) || (error.status == 401)){
						    $modalInstance.dismiss({action:'cancel'});
							$location.path('/login');
						}
						$scope.p.cl_options = [];
					}
				);		
			}		  
		}

		// SETResponsble  *** guarda en form.cliente el cliente seleccionado
		$scope.setResponsable= function(item) {		  
			$scope.form.responsable = item;	
			$scope.form.isResponsable = item.id;
		}
		
		
		

		  
		  /***************************************************
		   OK
		   Se cierra el modal y retornan los datos de la produccion
		  ****************************************************/ 
		  $scope.ok = function () {
			  $modalInstance.close({produccion: $scope.produccion, action: $scope.actionBeforeSave});
		  };
		  

		  
		  /***************************************************
		   IMPRIMIR
		   Se cierra el modal e imprime la produccion modificada
		  ****************************************************/
		  $scope.print = function () {		  
		  	$scope.actionBeforeSave='print';
/* 		  	('#form').submit(); */
		  	
		  };
		  
		  /***************************************************
		   SAVE
		   Se cierra el modal NO imprime la produccion modificada
		  ****************************************************/
		  $scope.save = function () {
		  	$scope.actionBeforeSave='';
/* 			('#form').submit(); */
		  };
		  
		  
		  
		  
		  
		  
		  /***************************************************
		   CANCEL
		   Se cierra el modal y retornan los datos de la produccion original, sin cambios
		  ****************************************************/
		  $scope.cancel = function () {
		  	$scope.back2original();	
		  	$modalInstance.dismiss({action:'cancel'});
		  };
		  
		 
		  		  
		  
		  
		  /***************************************************
		   DELETE
		   Se cierra el modal y retorna un indicador de que hay que eliminar la produccion
		  ****************************************************/
		  $scope.deleteProduccion = function () {
			  $scope.back2original();	
			  var res = {action:'delete', idProduccion:$scope.produccion.id};	  		
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
		  
		  	if( ($scope.form.modelo.nombre  !=  '') && ( $scope.form.modelo.nombre  !=  undefined)){
		  	
			  	$mod = {id: $scope.form.modelo.id, 
			  			nombre: $scope.form.modelo.nombre, 
			  			estado: 'Retirado'
			  		};
			  	$scope.produccion.modelos.push($mod);
			  	
			  	
			  	$scope.form.modelo = {nombre:'', id:''};
			  	angular.element("#newModId").focus();
			  	angular.element("#newMod").val('');
			  }
		  }	 
		  
			 
		 /***************************************************
		   REMOVE producto
		   Quita un modelo de la producción.
		  ****************************************************/	  
		  $scope.remove= function(index) {		  	
		  	
		  	if($scope.produccion.modelos[index].idProdMod != null)
		  		$scope.produccion.mod2delete.push({id:$scope.produccion.modelos[index].idProdMod});
		  	
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
]);






