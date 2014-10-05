
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



/*******************************************************************************************************
FORMAUTOFILLFIL
Cuando en un input tiene un valor cargado por defecto (caso de los inputs del login), el controller no detecta 
que el dato esta cargado y el scope queda sin actualizar y cuando el usuario hace submit no lo loguea.
*******************************************************************************************************/
app.directive('formAutofillFix', function ($timeout) {

  return function (scope, element, attrs) {
    element.prop('method', 'post');
    if (attrs.ngSubmit) {
      $timeout(function () {
        element
          .unbind('submit')
          .bind('submit', function (event) {
            event.preventDefault();
            element
              .find('input, textarea, select')
              .trigger('input')
              .trigger('change')
              .trigger('keydown');
            scope.$apply(attrs.ngSubmit);
          });
      });
    }
  };

});



/*******************************************************************************************************
LOADING
Cuando se está cargando información se muestra un gif.
*******************************************************************************************************/
app.directive('loading',   ['$http' ,function ($http)
    {
        return {
            restrict: 'A',
            link: function (scope, elm, attrs)
            {
                scope.isLoading = function () {
                    return $http.pendingRequests.length > 0;
                };

                scope.$watch(scope.isLoading, function (v)
                {
                    if(v){
                        elm.show();
                    }else{
                        elm.hide();
                    }
                });
            }
        };

    }]);
    
    
    
    
/*******************************************************************************************************
XEDITABLE
*******************************************************************************************************/    
app.directive('xeditable', function($timeout) {
    return {
        restrict: 'A',
        require: "ngModel",
        link: function(scope, element, attrs, ngModel) {
            var loadXeditable = function() {
                angular.element(element).editable({
                    display: function(value, srcData) {
                        ngModel.$setViewValue(value);
                        scope.$apply();
                    }
                });
            }
            $timeout(function() {
                loadXeditable();
            }, 10);
        }
    };
});    



