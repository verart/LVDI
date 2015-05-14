app.controller('modalPdfPedidoCtrl',['$scope', '$modalInstance', '$sce', '$filter', 'pedido', 

	function ($scope, $modalInstance, $sce, $filter, pedido) {		  
	
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
			doc.text(150, row, 'P. Unit.');
			doc.text(170, row, 'P. Tot.');
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
				doc.text(150, row, $filter('currency')(m.precio));
				doc.text(170, row, $filter('currency')(pTot.toString()));
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
				
			row = row+12;
			doc.setFontType("bold");
			doc.text(25, row, 'Subtotal: ');
			doc.setFontType("normal");
			doc.text(50, row, $filter('currency')(pedido.total));
				
			row = row+7;
			doc.setFontType("bold");
			doc.text(25, row, 'Bonif. : ');
			doc.text(50, row, (pedido.bonificacion.toString())+ '%');
			row = row+3;
			doc.setDrawColor(0);
			doc.line(23, row, 90, row); 
			
			doc.setFontSize(13);
			var bonif = (pedido.total * pedido.bonificacion / 100);
			var totalPed = (pedido.total - bonif);
			row = row+6;
			doc.setFontType("bold");
			doc.text(25, row, 'Total: ');
			doc.text(50, row, $filter('currency')(totalPed.toString()));	
			
			

				
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
