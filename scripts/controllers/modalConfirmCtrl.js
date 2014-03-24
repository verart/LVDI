
var modalConfirmCtrl = function ($scope, $modalInstance, txt) {		  
	
		  $scope.msj = txt.msj;
		  $scope.accept_txt = txt.accept;
		  $scope.cancel_txt = txt.cancel;
		  
		  /***************************************************
		   OK
		   Se cierra el modal y retornan los datos del producto
		  ****************************************************/ 
		  $scope.ok = function () {
		  	$modalInstance.close();
		  };
		  
		  
		  /***************************************************
		   CANCEL
		   Se cierra el modal y retornan los datos del producto original, sin cambios
		  ****************************************************/
		  $scope.cancel = function () {
		    $modalInstance.dismiss();
		  };
};
