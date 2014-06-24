var modalPdfProduccionCtrl = function ($scope, $modalInstance, $sce, produccion) {		  
	


			var doc = new jsPDF("Portrait", "mm", "a4");
			doc.setFont("helvetica");
			
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
