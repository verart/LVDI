var modalPdfPedidoNotAdminCtrl = function ($scope, $modalInstance, $sce, $filter, pedido) {		  
	


			var doc = new jsPDF("portrait", "mm", "a4");
			doc.setFont("helvetica");
			
			doc.setFontSize(18);
			doc.text(13, 19, 'Los Vados del Isen');
			doc.setFontSize(9);
			doc.setTextColor(100);
			doc.text(13, 24, 'Dirección: Araoz 2918 - Capital Federal');
			doc.text(13, 28, 'Tel: 4802-8969');
			doc.text(13, 32, 'Mail: losvadosdelisen@hotmail.com');
			
			row = 32;
			doc.setTextColor(0, 0, 0);
			doc.setLineWidth(0.3);
			doc.line(10, 36, 200, 36); 
			row = row + 17;
			doc.setFontSize(12);
			doc.setFontType("bold");
			doc.text(15, row, 'Cliente: ');
			doc.setFontType("normal");
			doc.text(33, row, pedido.datosCliente.nombre);
			doc.setFontType("bold");
			doc.text(120, row, 'Fecha de entrega: ');
			doc.setFontType("normal");
			doc.text(160, row, ($filter('date')(pedido.fecha_entrega, 'dd/MM/yyyy')) || 'Sin datos');	
			doc.setFontType("bold");
			row = row + 6;
			doc.setFontSize(10);
			doc.text(15, row, 'Localidad: ');
			doc.setFontType("normal");			
			doc.text(37, row, (pedido.datosCliente.localidad || 'Sin datos'));
			
			row = row+10;
			doc.setLineWidth(0.1);
			doc.setDrawColor(100,100,100);
			doc.line(21, row, 190, row); 		
			doc.setFontSize(11);
			doc.setFontType("bold");
			row = row+5;
			doc.text(25, row, 'Descripción');
			doc.text(130, row, 'Cant.');
			row = row + 3;
			doc.line(21, row, 190, row); 
			
			pageHeight = doc.internal.pageSize.height;
			pageHeight = pageHeight - 25;
				
			doc.setLineWidth(0.05);
			doc.setDrawColor(200);
			doc.setFontSize(11);
			doc.setFontType("normal");
			pedido.modelos.forEach(function(m){
				row = row + 5;
				var pTot = m.precio * m.cantidad;
				doc.text(25, row, m.nombre);
				doc.text(132, row, m.cantidad.toString());
				row = row +3;
				doc.line(21, row, 190, row); 
				
				// Before adding new content
				if ((row + 5) >= pageHeight){
				  doc.addPage();
				  row = 20 // Restart height position
				  doc.setLineWidth(0.05);
				  doc.setDrawColor(200);
				  doc.setFontSize(11);
				  doc.setFontType("normal");
				}
				
			});
			

			// Before adding new content
			if (row + 28 >= pageHeight){	
			  doc.addPage();
			  row = 20 // Restart height position
			}	
						

				
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
