app.controller('seguimientoCtrl', ['$scope','$modal',  'productosService', 'AlertService', '$filter', 


	function ($scope, $modal, productosService, AlertService, $filter) {
       

	    //ALERTS Mensajes a mostrar
	    $scope.alerts = [ ];
	    $scope.p = {mod_options:[]};
	    $scope.movimientos = [];

		$scope.form = {};
		$scope.form.idModelo = '';
		$scope.form.modelo = {nombre:'', id:''};

		hoy = formatLocalDate();
		
		$scope.desde = hoy;
		$scope.hasta = hoy;

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

		/*****************************************************************************************************
	    CARGAR     
	    *****************************************************************************************************/ 

	   	$scope.mostrarMovimientos = function () {
		  	 $scope.parar = false;
		  	 $scope.movimientos = [];
		  	 $scope.page = 0;
		  	 $scope.cargar();
		  	 
		};

	    $scope.page = 0;            
	    $scope.movimientos = [];
	    $scope.parar = false;
	    $scope.pending = false;
	    
	    $scope.cargar = function(){
	    	$scope.page++;                   
	    	$scope.pending = true;
			productosService.movimientos($scope.form.idModelo, $scope.desde, $scope.hasta, $scope.page).then(
				//Success
				function(promise){ 
					if((promise.data.DATA != null)&(promise.data.DATA.movimientos.length > 0)){
						for( i=0; i < promise.data.DATA.movimientos.length; i++)
							$scope.movimientos.push(promise.data.DATA.movimientos[i]);
					}else{
						if($scope.movimientos.length > 0)
							$('.finMovimientos').html('<div class="fin"></div>');
						$scope.parar = true;
					}		
	    			$scope.pending = false;			
				},
				//Error al actualizar
				function(error){ 
					$scope.pending = false;
					$scope.parar = true;
				}
			);		
		}

		/*****************************************************************************************************
	     INFINITE SCROLL	    
	    *****************************************************************************************************/
	    if ($('#infinite-scrolling').size() > 0) {
	    
			$(window).on('scroll', function() {
				if (($(window).scrollTop() > $(document).height() - $(window).height() - 60)& !$scope.parar & !$scope.pending ) {		     	
					if($scope.movimientos.length >0)
			 	 		$scope.cargar();
		    	}
		  	});
		  	return;
		};
					  
}]);	


