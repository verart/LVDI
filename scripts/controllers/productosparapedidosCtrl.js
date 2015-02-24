app.controller('productosparapedidosCtrl', ['$scope', '$modal', '$filter','productosService','$log', 'AlertService', 

	function ($scope, $modal, $filter, productosService,$log, AlertService, $timeout) {
       
        
		$scope.order = '-nombre';
	    $scope.textAdd = {0:'Agregar', 1:'Quitar'};
	    
	    // *** ALERTS Mensajes a mostrar
	    $scope.alerts = [ ];

	    /**********************************************************************
	     Recupera en data los productos
	    **********************************************************************/
	    $scope.page = 0;            
	    $scope.data = [];
	    $scope.parar = false;
	    $scope.pending = false;
	    $scope.filterSubmitted = '';

	    $scope.cargarProductos = function () {
	    	$scope.page++;                   
	    	$scope.pending = true;
	    	productosService.getproductos($scope.page, $scope.filterSubmitted).then(
		    	//Success
				function(promise){
					if(promise.data.DATA.length > 0){
						for( i=0; i < promise.data.DATA.length; i++)
							$scope.data.push(promise.data.DATA[i]);
					}else{
						if($scope.data.length > 0)
							$('.finProductos').html('<div class="fin"></div>');
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
		}	

	    $scope.cargarProductos();
	    
	    /*****************************************************************************************************
	     FILTRARPRODUCTOS 
	     Filtra las productos de contengan el texto en nombre de responsable o en motivo	    
	    *****************************************************************************************************/
	    $scope.filtrarProductos = function () {
		  	 $scope.parar = false;
		  	 $scope.data = [];
		  	 $scope.page = 0;
		  	 $scope.filterSubmitted = $scope.query;
		  	 $scope.cargarPruductos();
		};

	    
		/************************************************************************
	    HABILITAR - Agrega el modelo de producto a la lista de disponibles para pedido
	    Param: idProd -> id de producto
	    Param: indexMod -> indice donde se encuentra el modelo de ese producto
	    *************************************************************************/	
		$scope.habilitar = function(idProd, indexMod){
		   	var prodFound = $filter('getById')($scope.data, idProd);
		   	var hab;
		   	if(prodFound.modelos[indexMod].pedido == 1) 
		   		hab = 0;
		   	else 
		   		hab =1;	
			productosService.habilitarModelo(prodFound.modelos[indexMod].id, hab).then(
		    	//Success
				function(promise){ prodFound.modelos[indexMod].pedido = hab; },
				//Error al actualizar
				function(error){ AlertService.add('danger', error.data.MSG);}
			); 
			
		}  

	    /************************************************************************
	    HABILITARTODOS - Agrega todos los modelos del producto a la lista de disponibles para pedido
	    Param: idProd -> id de producto
	    Param: habilitar -> 1: habilita 0: deshabilita
	    *************************************************************************/	
		$scope.habilitarTodos = function(idProd,habilitar){
		   	var prodFound = $filter('getById')($scope.data, idProd);
			productosService.habilitarProducto(prodFound.id, habilitar).then(
		    	//Success
				function(promise){ 			
					prodFound.modelos.forEach(function(mod){
						mod.pedido = habilitar;});	
				},
				//Error al actualizar
				function(error){ AlertService.add('danger', error.data.MSG);}
			); 

		} 

	    /*****************************************************************************************************
	     INFINITE SCROLL	    
	    *****************************************************************************************************/
	    if ($('#infinite-scrolling').size() > 0) {
			$(window).on('scroll', function() {
				if (($(window).scrollTop() > $(document).height() - $(window).height() - 60)& !$scope.parar & !$scope.pending ) {		     	
			  		$scope.cargarProductos();}
			});
			return;
		};   
}]);