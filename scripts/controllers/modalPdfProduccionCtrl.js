var modalPdfProduccionCtrl = function ($scope, $modalInstance, $sce, produccion) {		  
	


			var doc = new jsPDF("Portrait", "mm", "a4");
			doc.setFont("helvetica");

			pageHeight = doc.internal.pageSize.height;
			pageHeight = pageHeight - 25;
			
			doc.setFontSize(18);
			doc.text(13, 20, 'Los Vados del Isen');
			doc.setFontSize(10);
			doc.text(13, 27, 'Dirección: Araoz 2918 - Capital Federal');
			doc.text(13, 32, 'Tel: 4802-8969');
			doc.text(13, 37, 'Mail: losvadosdelisen@hotmail.com');
			
			doc.line(13, 40, 200, 39); 
			
			doc.setFontSize(14);
			doc.text(15, 52, 'Producción');
			var row = 60;
			doc.setFontSize(11);
			doc.setFontType("bold");
			doc.text(17, row, 'Fecha devolución: ');
			doc.setFontType("normal");
			doc.text(54, row, produccion.fecha_devolucion);			
			doc.setFontType("bold");
			doc.text(88, row, 'Fecha:');
			doc.setFontType("normal");
			doc.text(102, row, produccion.fecha);
			
			row = row + 7;
			doc.setFontType("bold");
			doc.text(17, row, 'Responsable: ');
			doc.setFontType("normal");
			doc.text(44, row, produccion.responsable);
			
			row = row+7;
			doc.setFontType("bold");
			doc.text(17, row, 'Motivo: ');
			doc.setFontType("normal");
			doc.text(33, row, produccion.motivo);
			
			row = row+10;
			doc.setLineWidth(0.1);
			doc.setDrawColor(100,100,100);
			doc.line(24, row, 190, row); 		
			doc.setFontSize(11);
			doc.setFontType("bold");
			row = row+5;
			doc.text(25, row, 'Detalle');
			row = row + 3;
			doc.line(24, row, 190, row); 
			
			doc.setLineWidth(0.05);
			doc.setDrawColor(200);
			doc.setFontSize(11);
			doc.setFontType("normal");
			produccion.modelos.forEach(function(m){			
				row = row + 5;
				doc.text(25, row, m.nombre);
				doc.text(140, row, '$'+m.precio);
				row = row + 3;
				doc.line(24, row, 190, row);
				
				// Before adding new content
				if ((row + 7) >= pageHeight){
				  doc.addPage();
				  row = 20; // Restart height position
				  doc.setLineWidth(0.05);
				  doc.setDrawColor(200);
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
		  
};
