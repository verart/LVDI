app.controller('resumenCtrl', ['$scope','$modal',  'resumenService', 'AlertService', '$filter', 


	function ($scope, $modal, resumenService, AlertService, $filter) {
       
       
       	/**********************************************************************
	    ALERTS
	    Mensajes a mostrar
	    **********************************************************************/
	    $scope.alerts = [ ];
	    
       
        fechaHoy = formatLocalDate();


	    $scope.value = 'hoy';
	    $scope.desde = fechaHoy;
	    $scope.hasta = fechaHoy;
	    
	    
	    /*****************************************************************************************************
	    CARGAR     
	    *****************************************************************************************************/    	    
	    $scope.cargar = function(){

		    resumenService.resumen($scope.desde, $scope.hasta).then(
				//success
				function(promise){
				     $scope.resumenVentas = {
				     	tarjeta:parseInt(promise.data.DATA.resumenVentas['Tarjeta'],10), 
				     	debito:parseInt(promise.data.DATA.resumenVentas['Debito'],10), 
				     	cheque:parseInt(promise.data.DATA.resumenVentas['Cheque'],10), 
				     	efectivo:parseInt(promise.data.DATA.resumenVentas['Efectivo'],10)}; 
				                                          
				     $scope.resumenPorMayor = {
				     	tarjeta:parseInt(promise.data.DATA.resumenPorMayor['Tarjeta'],10), 
				     	debito:parseInt(promise.data.DATA.resumenPorMayor['Debito'],10), 
				     	cheque:parseInt(promise.data.DATA.resumenPorMayor['Cheque'],10), 
				     	efectivo:parseInt(promise.data.DATA.resumenPorMayor['Efectivo'],10)};    
				     	
				     $scope.resumenGastos = {
				     	tarjeta:parseInt(promise.data.DATA.resumenGastos['Tarjeta'],10), 
				     	debito:parseInt(promise.data.DATA.resumenGastos['Debito'],10), 
				     	cheque:parseInt(promise.data.DATA.resumenGastos['Cheque'],10), 
				     	efectivo:parseInt(promise.data.DATA.resumenGastos['Efectivo'],10),
				     	transferencia:parseInt(promise.data.DATA.resumenGastos['Transferencia'],10)}; 	             
	               
				},
				//Error al actualizar
				function(error){ AlertService.add('danger', error.data.MSG);}
			);
		}	



	    $scope.cargar();
        
        
        	
}]);


