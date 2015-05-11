app.controller('modalPdfClientesMailsCtrl', ['$scope', '$modalInstance', '$sce', '$filter', 'clientes', 

	function ($scope, $modalInstance, $sce, $filter, clientes) {		  
	
			var doc = new jsPDF("portrait", "mm", "a4");
			doc.setFont("helvetica");
			
			
			pageHeight = doc.internal.pageSize.height;
			pageHeight = pageHeight - 25;
				
			doc.setFontSize(20);
			doc.text(10, 18, 'Mails de clientes');

			
			doc.setTextColor(0, 0, 0);
			doc.setLineWidth(0.3);
			doc.line(10, 26, 200, 26); 
			row = 35;
			
			doc.setFontSize(11);	
			clientes.forEach(function(c){
				row = row + 5;
				doc.text(25, row, c.email);
				row = row +3;	

				// Before adding new content
				if ((row + 5) >= pageHeight){
				  doc.addPage();
				  row = 20; // Restart height position
				  doc.setFontSize(11);
				  doc.setFontType("normal");
				}

			});			
			
				
			var output = $sce.trustAsResourceUrl(doc.output('datauristring'));

		
		
			$scope.pdfOutput = output;
			
	
		/***************************************************
		   OK
		   Se cierra el modal y retornan los datos del producto
		  ****************************************************/ 
		  $scope.ok = function () {
		  	$modalInstance.close();
		  };
	}
]);
