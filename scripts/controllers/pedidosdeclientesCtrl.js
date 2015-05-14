app.controller('pedidosdeclientesCtrl', ['$scope','$modal', 'pedidosService', 'productosService','clientesPMService','AlertService','$filter','$routeParams','$location', '$rootScope', 

	function ($scope,$modal,pedidosService,productosService,clientesPMService,AlertService,$filter,$routeParams,$location, $rootScope) {
      
	   	$(window).unbind('scroll');

      	$rootScope.activeTab = 'pedidosdeclientes';


	    $scope.order = ['-nombre'];
	    $scope.query = '';
	    $scope.pedido = {};
	    $scope.data = '';

	    //ALERTS    Mensajes a mostrar
	    $scope.alerts = [ ];

	    var token = $routeParams.token; // this name is from config :token

	    //Recupero información del cliente
	    clientesPMService.tienePermiso(token).then(
			//Success
			function(promise){ 
				$scope.pedido.cliente_name = promise.data.DATA.nombre;
				$scope.pedido.clientesPM_id = promise.data.DATA.id;
				$scope.pedido.bonificacion = promise.data.DATA.bonificacion;
				$scope.pedido.fecha = (formatLocalDate());
				$scope.pedido.total = 0;
				$scope.pedido.totalFinal = 0;
				$scope.pedido.nota = '';
				$scope.pedido.modelos = [];
				//Recupera en data los productos
			    productosService.prodsPedido(token).then(
			    	//Success
					function(p){ $scope.data = p.data.DATA;  $scope.initCantidad();},
					//Error al acceder
					function(e){ AlertService('danger',e.data.MSG,3000); 
	    						$scope.data = '';}
				);
			},
			//Error al acceder
			function(error){
				AlertService.add('danger', error.data.MSG,3000); }
	    );
	   
	    $scope.initCantidad= function(index) {
	    	$scope.data.forEach(function(data){
	    		data.modelos.forEach(function(m){m.cantidad = 0;});})
	    }

		/***************************************************
		   ADD producto - Agrega cada modelo con la cantidad indicada. Actualiza los totales
		****************************************************/	  
		$scope.add= function(index) {
		  	
		  	for (var k=0; k < $scope.data[index].modelos.length; k++){
		  		if($scope.data[index].modelos[k].cantidad != 0){
			   		m = $filter('getIndexById')($scope.pedido.modelos, $scope.data[index].modelos[k].id);
			   		if(m!=null)
			   			$scope.pedido.modelos[m].cantidad=parseInt($scope.pedido.modelos[m].cantidad)+parseInt($scope.data[index].modelos[k].cantidad);
			   		else
		    			$scope.pedido.modelos.push(
		    				{ id: $scope.data[index].modelos[k].id,
		    				nombre:$scope.data[index].nombre + '-'+$scope.data[index].modelos[k].nombre,
		    				precio:$scope.data[index].precio,
		    				cantidad:$scope.data[index].modelos[k].cantidad,
		    				estado: 'Pendiente'
		    			});
			   		$scope.pedido.total= $scope.pedido.total+($scope.data[index].modelos[k].cantidad*$scope.data[index].precio);
			   		$scope.pedido.totalFinal = parseFloat($scope.pedido.total) - ($scope.pedido.total * $scope.pedido.bonificacion / 100);
			   		$scope.data[index].modelos[k].cantidad = 0;
		    	}					
			}
			AlertService.add('success', 'Se agregó al pedido '+$scope.data[index].nombre, 2000);	
	    				  	
		}	 

	    /************************************************************************
	    VIEWPEDIDO  Abre un modal con info del nuevo pedido
	    *************************************************************************/	
        $scope.viewPedido = function() {
	 	    var modalInstance = $modal.open({
		    	templateUrl: dir_root+'/templates/pedidosdeclientes/pedido.html',
		    	windowClass: 'wndPedido',
		    	controller: 'ModalPedidodeclientesCtrl',
		    	backdrop: 'static',
		    	keyboard: true,
		    	resolve: {
		        	info: function () { return $scope.pedido; }
		        }
		    });

			modalInstance.result.then( 
			    //GUARDAR
			    function (res) {
					//Solicita confirmación
					var txt_confirm = { msj: "Se enviará el pedido. ¿Desea continuar?", accept:"Si", cancel:"No"};
									    	
					var confirm = $modal.open({
						templateUrl: dir_root+'/templates/confirm.html',
						windowClass: 'wndConfirm',
						controller: 'modalConfirmCtrl',
						resolve: { txt: function(){ return txt_confirm } }
					});
					confirm.result.then( 
						// ACEPTAR
						function (r) {
							pedidosService.confirmarPedido($scope.pedido, token).then(
								//Success
								function(promise){ 
									AlertService.add('success', "El pedido fue realizado.",3000);
									$scope.data = [];
								},
								//Error al eliminar
								function(error){ 
									AlertService.add('danger', error.data.MSG);
	    							$scope.data = '';
	    						}
							)
						}
					);
				}, 
				// CANCELAR
				function (res){ }
			);
		}	
}]);


/*************************************************************************************************************************
 ModalPedidodeclientesCtrl
 Controller del modal para agregar/editar modelos  
**************************************************************************************************************************/
app.controller('ModalPedidodeclientesCtrl', [ '$scope', '$modalInstance', 'productosService', 'AlertService', 'ventasService', '$filter', 'info', '$location', 

	function ($scope, $modalInstance, productosService, AlertService, ventasService, $filter, info, $location) {
		  
		$scope.pedido = info;

		// CANCEL *** Se cierra el modal y retornan los datos de la venta original, sin cambios
		$scope.cancel = function () {
			$modalInstance.dismiss();
		};

		$scope.ok = function () {
			$modalInstance.close();
		};

		// REMOVE modelo *** Quita un modelo del pedido. Actualiza los totales 	  
		$scope.remove= function(index) {		  	
			$scope.pedido.total =  parseFloat($scope.pedido.total,10) - (parseFloat($scope.pedido.modelos[index].precio,10) *  parseInt($scope.pedido.modelos[index].cantidad,10));
			$scope.pedido.modelos.splice(index,1);	  	
		}	 
			 

		// WATCH pedido.modelos *** Actualiza  total
		$scope.$watch('pedido.modelos', function(newValue, oldValue) {
			$scope.refreshTotal();
		}, true);

		//REFRESH modelo  ***  Quita un modelo del pedido. Actualiza los totales   
		$scope.refreshTotal= function() {	
			total = 0;
			$scope.pedido.modelos.forEach(function(m){
		    	total =  total + (m.precio * parseInt(m.cantidad));
		    }) 	
			$scope.pedido.total = total;
		}
	}
]);



