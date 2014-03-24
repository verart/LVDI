
/*******************************************************************************************************
NG-PRODUCTO (Element)
Un producto de la lista de productos
*******************************************************************************************************/
app.directive('ngProducto', function()
{
	return{
		restrict: 'E',
		templateUrl: 'templates/productos/producto.html',
	}
});



/*******************************************************************************************************
UPLOADES (Element)
Uploader de archivos
*******************************************************************************************************/
app.directive('uploader', [function() {

	return {
		restrict: 'E',
		controller: function($scope, $element, $attrs){ 

			$scope.progress = 0;
			$scope.action = "api/uploader.php";
			
			$scope.sendFile = function(el) {
		
				var $form = $(el).parents('form');
		
				if ($(el).val() == '') {
					return false;
				}
		
				$form.attr('action', $attrs.action);
		
				$scope.$apply(function() {
					$scope.progress = 0;
				});				
		
				$form.ajaxSubmit({
					type: 'POST',
					uploadProgress: function(event, position, total, percentComplete) { 
						
						$scope.$apply(function() {
							// upload the progress bar during the upload
							$scope.progress = percentComplete;
						});
		
					},
					error: function(event, statusText, responseText, form) { 
		
						// remove the action attribute from the form
						$form.removeAttr('action');
		
					},
					success: function(responseText, statusText, xhr, form) { 
		
						var ar = $(el).val().split('\\'), 
							filename =  ar[ar.length-1];
		
						// remove the action attribute from the form
						$form.removeAttr('action');
		
						$scope.$apply(function() {
							$scope.producto.fileName = filename;
							$scope.producto.img ='img/tmp/'+filename;
							$scope.progress = 0;
						});
						
		
					},
				});
		
			}
		},
		link: function(scope, elem, attrs, ctrl) {
			
			elem.find('.fake-uploader').click(function() {
				elem.find('input[type="file"]').click();
			});
		
		},
		replace: false,    
		templateUrl: 'templates/uploader.html'
	};

}]);



/*******************************************************************************************************
NG-ALERTS (Element)
Muestra la lista de alertas
Necesita en el scope un array 'alerts' {type,msg,show} 
*******************************************************************************************************/
app.directive('ngAlerts', [function() {

	return{
		restrict: 'E',
		templateUrl: 'templates/alerts.html',
		transclude: true,
		link: function(scope, element, attrs) {
            scope.$watch('alerts', function(newValue, oldValue) {
                if (newValue !== oldValue)
                    console.log("I see a data change!" + oldValue +'--'+newValue );
            });
        },
        controller: function($scope){
				    
			$scope.closeAlert = function(index) {
				$scope.alerts.splice(index, 1);
			};
	    
		}
	}
}]);





/*******************************************************************************************************
NG-ENTER (Atributo)
Capta el keypress de enter en un elemento y le asigna un comportamiento
*******************************************************************************************************/
app.directive('ngEnter', function () {
    return function (scope, element, attrs) {
    
        element.bind("keydown keypress", function (event) {
            if(event.which === 13) {
                scope.$apply(function (){
                    scope.$eval(attrs.ngEnter);
                });

                event.preventDefault();
            }
        });
    };
    
});



/*******************************************************************************************************
FOCUS-ME (boolean)
Coloca el focus en el element donde se encuentra el atributo
*******************************************************************************************************/
app.directive('focusMe', function($timeout) {
    return function(scope, element, attrs) {
        attrs.$observe('focusMe', function(value) {
            if ( value==="true" ) {
                $timeout(function(){
                    element[0].focus();
                },5);
            }
        });
    }
});




