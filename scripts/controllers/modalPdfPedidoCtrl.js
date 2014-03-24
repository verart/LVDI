var modalPdfPedidoCtrl = function ($scope, $modalInstance, $sce, $filter, pedido) {		  
	


			var doc = new jsPDF("portrait", "mm", "a4");
			doc.setFont("helvetica");
			
			console.log(pedido);
			doc.setFontSize(20);
			doc.text(10, 18, 'Pedido');
			doc.setFontSize(9);
			doc.setTextColor(100);
			doc.setFontType("italic");
			doc.text(10, 22, 'No válido como factura');
			
			doc.setTextColor(0, 0, 0);
			doc.setLineWidth(0.3);
			doc.line(10, 26, 200, 26); 
			row = 35;
			doc.setFontSize(13);
			doc.setFontType("bold");
			doc.text(15, row, 'Cliente: ');
			doc.setFontType("normal");
			doc.text(33, row, pedido.datosCliente.nombre);
			doc.setFontType("bold");
			doc.text(140, row, 'Fecha: ');
			doc.setFontType("normal");
			doc.text(160, row, $filter('date')(pedido.fecha, 'dd/MM/yyyy'));	
			
			var row = 43;
			doc.setFontSize(10);			
			doc.setFontType("bold");
			doc.text(17, row, 'Tel: ');
			doc.setFontType("normal");
			doc.text(25, row,  (pedido.datosCliente.Tel || 'Sin datos'));
			row = row+5;
			doc.setFontType("bold");
			doc.text(17, row, 'email: ');
			doc.setFontType("normal");
			doc.text(29, row,  (pedido.datosCliente.email || 'Sin datos'));
			row = row+5;
			doc.setFontType("bold");
			doc.text(17, row, 'Dirección: ');
			doc.setFontType("normal");
			doc.text(37, row,  (pedido.datosCliente.direccion || 'Sin datos'));

			row = row+15;
			doc.setLineWidth(0.2);
			doc.setDrawColor(120,120,120);
			doc.line(20, row, 190, row); 		
			doc.setFontSize(11);
			doc.setFontType("bold");
			row = row+5;
			doc.text(25, row, 'Descripción');
			doc.text(130, row, 'Cant.');
			doc.text(150, row, 'P. Unit.');
			doc.text(170, row, 'P. Tot.');
			row = row + 3;
			doc.line(20, row, 190, row); 
			
			doc.setLineWidth(0.1);
			doc.setDrawColor(150);
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
				doc.line(20, row, 190, row); 				
			});
			
			row = row+12;
			doc.setFontType("bold");
			doc.text(25, row, 'Subtotal: ');
			doc.setFontType("normal");
			doc.text(50, row, $filter('currency')(pedido.total));
				
			row = row+7;
			var bonif = (pedido.total * pedido.bonificacion / 100);
			doc.setFontType("bold");
			doc.text(25, row, 'Bonif. : ');
			doc.text(50, row, $filter('currency')(bonif.toString()));
			row = row+3;
			doc.line(23, row, 90, row); 
			
			doc.setFontSize(13);
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
		  
};
