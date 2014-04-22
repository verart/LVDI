var modalPdfProduccionCtrl = function ($scope, $modalInstance, $sce, produccion) {		  
	


			var doc = new jsPDF("Portrait", "mm", "a4");
			doc.setFont("helvetica");
			
			doc.setFontSize(18);
			doc.text(15, 20, 'Los Vados del Isen');
			doc.setFontSize(10);
			doc.text(15, 27, 'Dirección: Araoz 1928 - Capital Federal');
			doc.text(15, 32, 'Tel: 4802-8969');
			
			doc.line(15, 35, 200, 35); 
			
			doc.setFontSize(14);
			doc.text(15, 45, 'Producción');
			var row = 57;
			doc.setFontSize(11);
			doc.setFontType("bold");
			doc.text(17, row, 'Fecha: ');
			doc.setFontType("normal");
			doc.text(31, row, produccion.fecha);			
			doc.setFontType("bold");
			doc.text(78, row, 'Fecha devolución:');
			doc.setFontType("normal");
			doc.text(115, row, produccion.fecha_devolucion);
			
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
			
			row = row + 20;
			doc.setFontSize(14);
			doc.text(16, row, 'Detalle');
			
			doc.setFontSize(11);
			row = row + 10;
			produccion.modelos.forEach(function(m){
				doc.text(30, row, m.nombre);
				doc.text(140, row, '$'+m.precio);
				row = row +10;
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
