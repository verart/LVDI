app.controller('resumenCtrl', ['$scope','$modal', 'resumenService', 'AlertService', '$filter', 'AuthService', 


	function ($scope, $modal, resumenService, AlertService, $filter, AuthService) {
       
       
       	/**********************************************************************
	    ALERTS
	    Mensajes a mostrar
	    **********************************************************************/
	    $scope.alerts = [ ];
	    
       
        fechaHoy = formatLocalDate();


	    $scope.value = 'hoy';
	    $scope.desde = fechaHoy;
	    $scope.hasta = fechaHoy;
	    
	    $(window).unbind('scroll');
	    /*****************************************************************************************************
	    CARGAR     
	    *****************************************************************************************************/    	    
	    $scope.cargar = function(){

		    resumenService.resumen($scope.desde, $scope.hasta).then(
				//success
				function(promise){
				     $scope.resumenVentas = {
				     	tarjeta:parseFloat(promise.data.DATA.resumenVentas['Tarjeta'],10), 
				     	debito:parseFloat(promise.data.DATA.resumenVentas['Debito'],10), 
				     	cheque:parseFloat(promise.data.DATA.resumenVentas['Cheque'],10), 
				     	efectivo:parseFloat(promise.data.DATA.resumenVentas['Efectivo'],10)}; 
				                                          
				     $scope.resumenPorMayor = {
				     	tarjeta:{
				     		'total': parseFloat(promise.data.DATA.resumenPorMayor['Tarjeta'].total,10),
				     		'pagos': promise.data.DATA.resumenPorMayor['Tarjeta'].pagos},   
				     	transfVictor:{
				     		'total': parseFloat(promise.data.DATA.resumenPorMayor['Transf. Victor'].total,10),
				     		'pagos': promise.data.DATA.resumenPorMayor['Transf. Victor'].pagos},   
				     	transfFede:{
				     		'total': parseFloat(promise.data.DATA.resumenPorMayor['Transf. Fede'].total,10), 
				     		'pagos': promise.data.DATA.resumenPorMayor['Transf. Fede'].pagos},   
				     	cheque:{
				     		'total': parseFloat(promise.data.DATA.resumenPorMayor['Cheque'].total,10), 
				     		'pagos': promise.data.DATA.resumenPorMayor['Cheque'].pagos},   
				     	efectivo:{
				     		'total': parseFloat(promise.data.DATA.resumenPorMayor['Efectivo'].total,10),
				     		'pagos': promise.data.DATA.resumenPorMayor['Efectivo'].pagos}}; 
				     	
				     $scope.resumenGastos = {
				     	tarjeta:parseFloat(promise.data.DATA.resumenGastos['Tarjeta'],10), 
				     	debito:parseFloat(promise.data.DATA.resumenGastos['Debito'],10), 
				     	cheque:parseFloat(promise.data.DATA.resumenGastos['Cheque'],10), 
				     	efectivo:parseFloat(promise.data.DATA.resumenGastos['Efectivo'],10),
				     	transferencia:parseFloat(promise.data.DATA.resumenGastos['Transferencia'],10)}; 	             
	               
				},
				//Error al actualizar
				function(error){ AuthService.logout();}
			);
		};	

		/*****************************************************************************************************
	     DETALLE     
	    *****************************************************************************************************/
	    $scope.loadDetalle = function (fp) {
	    	
			resumenService.detalle($scope.desde, $scope.hasta, fp).then(		    	
				//Success
				function(promise){
					switch(fp) {
					    case 'Efectivo':
					        $scope.resumenPorMayor['efectivo']['pagos'] = promise.data.DATA;
					        break;
					    case 'Tarjeta':
					        $scope.resumenPorMayor['tarjeta']['pagos'] = promise.data.DATA;
					        break;
						case 'Cheque':
					        $scope.resumenPorMayor['cheque']['pagos'] = promise.data.DATA;
					        break;
					    case 'Tranf. Victor':
					        $scope.resumenPorMayor['transfVictor']['pagos'] = promise.data.DATA;
					        break;
						case 'Tranf. Fede':
					        $scope.resumenPorMayor['transfFede']['pagos'] = promise.data.DATA;
					        break;
					}
				},
				//Error al actualizar
				function(error){}
			); 
		};


	    $scope.cargar();

	
        
}]);


