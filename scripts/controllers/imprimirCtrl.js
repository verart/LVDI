var imprimirCtrl = function ($scope, colaImpresionService) {		  
	

			/**********************************************************************
		     Recupera en data los codigo de producutos de la cola a imprimir
		    **********************************************************************/
		    $scope.data = colaImpresionService.getModelos();
		    	
		    var index = 0;	
		    var d;
		    		
			$scope.data.forEach(function (prod) {
				
		    	
	            style = {barWidth:2, barHeight:30};
	            
	            var cod = prod.modelos_id   
	            var long = cod.length;
				var cant = 7 - long;
				for(var i=0; i<cant; i++)
					cod = "0"+cod;
				
				bcdiv = document.createElement("div");
				bcdiv.setAttribute('id',"bcTarget"+index);
				bcdiv.setAttribute('class',"etiqueta");
				$("#codes").append(bcdiv);
				
				
				d = document.createElement("div");
				d.setAttribute('id',"bd"+index);
				p = document.createElement("p");
				p.setAttribute('id',"prod"+index);
				$("#bcTarget"+index).append(p);
				$("#bcTarget"+index).append(d);
				
				$("#prod"+index).text(prod.nombre);
				$("#bd"+index++).barcode(cod, "ean8", style);
				                  
			});

        	
			
		  
};
