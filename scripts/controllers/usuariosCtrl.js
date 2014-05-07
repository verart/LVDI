app.controller('usuariosCtrl', ['$scope', '$modal', '$filter','$log', 'AlertService','usuariosService','$timeout', 

	function ($scope, $modal, $filter, $log, AlertService, usuariosService, $timeout) {
       
        
		$scope.order = '-nombre';
	    
	    
	    	    
	    /**********************************************************************
	     Recupera en data los clientesPM
	    **********************************************************************/
	    listusuarios = function(data){	    		
		    $scope.data = data;console.log($scope.data);
	    }	    	    
	    usuariosService.usuarios(listusuarios);
	  
 
	
	    
	   /************************************************************************
	    OPENUSUARIO
	    Abre un modal con un form para crear un nuevo usuario o editarlo
	    param: idUs -> id de usuario. Si viene en blanco es un create 
	    *************************************************************************/	
		$scope.openUsuario = function(idUs) {
	 	
	 	
	 		if(idUs != ''){
	 			$scope.selectedUsuario = $filter('getById')($scope.data, idUs);
	 		}else{
	 			$scope.selectedUsuario = '';
	 		}	
	 	    
	 	    angular.element("#nombre").focus();
	 	    
	 	    var modalInstance = $modal.open({
		    	templateUrl: dir_root+'/templates/usuarios/addedit.html',
		    	windowClass: 'wndUsuarios',
		    	controller: 'ModalUsuariosInstanceCtrl',
		    	backdrop: 'static',
		    	keyboard: true,
		    	resolve: {
		        	usuarios: function () { return $scope.selectedUsuario; }
		        }
		    });
		    
		    
		    // Comportamiento al cerrar el modal		    
		    modalInstance.result
		    .then( 
		    	/*************************************************************************************************
		    	 GUARDAR
		    	*************************************************************************************************/
		    	function (res) {
		    	
		    	
		    		/******************************************
		    		 NUEVO USUARIO
		    		******************************************/
			    	if($scope.selectedUsuario == '') {
			    		usuariosService.addUsuario(res).then(
			    			//Success
			    			function(promise){
			    				$scope.data.push(promise.data.DATA);
			    			},
			    			//Error al guardar
			    			function(error){
				    			var res_msg = error.data.MSG;
				    			AlertService.add('danger', res_msg, 5000);
			    			}
			    		);
			    		
			    			
			    	}else{ 
				    	
				    	
				    	/******************************************
				    	UPDATE USUARIO
				    	******************************************/
			    		usuariosService.editUsuario(res).then(
			    			//SUCCESS
			    			function(promise){ },
			    			//Error al actualizar
			    			function(error){
				    			AlertService.add('danger', error.data.MSG);
			    			}
			    		);
			    	}
			    		
			    }, 
			    
			    /*************************************************************************************************
		    	 CANCELAR
		    	*************************************************************************************************/
			    function (res){
			    
			    	/******************************************
				    DELETE USUARIO
				    ******************************************/
				    if(res.action == 'delete'){
				    	
				    	//Solicita confirmación
				    	var txt_confirm = { msj: "¿Está seguro que desea eliminar este usuario?", accept:"Si", cancel:"No"};
				    	var idUsuario = res.idUsuario;
				    	
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
						    	 usuariosService.deleteUsuario(idUsuario).then(
					    			//Success
					    			function(promise){
					    				var index = $filter('getIndexById')($scope.data, idUsuario);
					    				$scope.data.splice(index, 1);
					    			},
					    			//Error al eliminar
					    			function(promise){
						    			AlertService.add('danger', promise.data.MSG);
					    			}
					    		);
						    }, 
						    // Si el modal cierra por CANCELAR
						    function (res){}

						);   	
					}
			   }
			
			);	
		}
		
		/* NUEVO *******************/
	 	$scope.nuevo = function () {
            $scope.openUsuario('');
        };			
        
        
               
}]);

	
	
	  
/*************************************************************************************************************************
 ModalClientesInstanceCtrl
 Controller del modal para agregar/editar productos  
**************************************************************************************************************************/
var ModalUsuariosInstanceCtrl = function ($scope, $modalInstance, $filter, usuarios) {
		  		  		  
		  
		  if(usuarios != ''){
		  	var original = angular.copy(usuarios);
		  	$scope.usuarios = usuarios;
		  }else{
		  	$scope.usuarios	 = {nombre:'',clave:'', perfiles_id:'', perfil:''};
		  	var original = $scope.usuarios;
		  }
		  
		  $scope.perfiles = [{value:"1",text:'Admin'}, {value:"3", text:'Local'}, {value:"2", text:'Taller'}];	  		  
		  
		  /***************************************************
		   OK
		   Se cierra el modal y retornan los datos del producto
		  ****************************************************/ 
		  $scope.ok = function () {
		  	$modalInstance.close({usuarios: $scope.usuarios});
		  };
		  
		  
		  /***************************************************
		   CANCEL
		   Se cierra el modal y retornan los datos del producto original, sin cambios
		  ****************************************************/
		  $scope.cancel = function () {
		  	$scope.back2original();
		    $modalInstance.dismiss({action:'cancel'});
		  };
		  
		  
		  /***************************************************
		   DELETE
		   Se cierra el modal y retornan un indicador de que hay que eliminar el producto
		  ****************************************************/
		  $scope.deleteUsuario = function () {
			  $scope.back2original();	
			  var res = {action:'delete', idUsuario:$scope.usuarios.id};	  		
			  $modalInstance.dismiss(res);
		  };
		  

		  // back2original
		  // Copia en producto los campos originales que se enviaron.  
		  $scope.back2original = function(){
			  $scope.usuarios.nombre = original.nombre;
			  $scope.usuarios.clave = original.email;			  
			  $scope.usuarios.perfiles_id = original.perfiles_id;	
		  };	
		  	  
		  	  
}