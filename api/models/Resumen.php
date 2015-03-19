<?php
class Resumen extends AppModel {
	
	public $name = "Resumen";
	
	
	/** 
	 * GETRESUMEN
	 * Retorna todos las notas
	 * params (array) $opciones = array([conditions])
	 */
	function getResumen($opciones = array()) {
	
		$finalResults = array();

		$conditions = (isset($opciones['conditions']))? $this->_buildConditions($opciones['conditions']): "";	


		// total de ventas en pagos
		$sql = "SELECT SUM(VP.monto) as resumenVentas, VP.FP
				FROM ventas_pagos VP 
				$conditions 
				GROUP BY VP.FP ";
				 				
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();		   	
		$results = $query->fetchAll();
		
		$finalResults['resumenVentas'] = array('Efectivo'=>0,'Tarjeta'=>0,'Debito'=>0,'Cheque'=>0);
		$i=0;
		while($i < count($results))
			$finalResults['resumenVentas'][utf8_encode($results[$i]['FP'])] = $results[$i++]['resumenVentas'];
			
			
		// total de ventas 	 - en un solo pago
		$sql = "SELECT SUM((V.total - V.montoFavor-IFNULL(totalDevoluciones,0)) - ((V.total - V.montoFavor-IFNULL(totalDevoluciones,0))*V.bonificacion/100)) as resumenVentas, V.FP 
				FROM ventas V 	
				LEFT JOIN (
					SELECT ventas_id, SUM(precio) as totalDevoluciones
					FROM ventas_devoluciones VD 
					GROUP BY ventas_id 
				) as VDV ON VDV.ventas_id = V.id 			
				LEFT JOIN 
					(select ventas_id
					FROM ventas_pagos VP
					GROUP BY ventas_id) as pagos ON pagos.ventas_id = V.id
				$conditions AND (deuda <= 0) AND ((montoFavor+IFNULL(totalDevoluciones,0)) <= total) AND  ISNULL(pagos.ventas_id)    
				GROUP BY V.FP ";
			
		$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();		   	
		$results = $query->fetchAll();
		
		$i=0; 
		while($i < count($results))
			$finalResults['resumenVentas'][utf8_encode($results[$i]['FP'])] = $finalResults['resumenVentas'][utf8_encode($results[$i]['FP'])] + $results[$i++]['resumenVentas'];
			
		//total pedidos especiales
		//$finalResults['resumenPedidosespeciales'] = array('Efectivo'=>0,'Tarjeta'=>0,'Cheque'=>0,'Transferencia'=>0);
		$sql = "SELECT SUM(PP.monto) as resumenPedidosespeciales, PP.FP 
				FROM pedidosespeciales_pagos PP 				
				$conditions     
				GROUP BY PP.FP "; 
			
		$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();		   	
		$results = $query->fetchAll();
		
		$i=0;
		while($i < count($results))
			$finalResults['resumenVentas'][utf8_encode($results[$i]['FP'])]=$finalResults['resumenVentas'][utf8_encode($results[$i]['FP'])] + $results[$i++]['resumenPedidosespeciales'];

		
		//total por mayor
		$sql = "SELECT SUM((PP.monto) ) as resumenPorMayor, PP.FP
				FROM pedidos_pagos PP 
				$conditions 
				GROUP BY PP.FP"; 
				 				
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();		   	
		$results = $query->fetchAll();
		
		$finalResults['resumenPorMayor'] = array('Efectivo'=>0,'Tarjeta'=>0,'Transferencia'=>0,'Cheque'=>0, 'Transf. Victor'=>0,'Transf. Fede'=>0,);
		$i=0;
		while($i < count($results))
			$finalResults['resumenPorMayor'][utf8_encode($results[$i]['FP'])]=$results[$i++]['resumenPorMayor'];	

		
		//total gastos
		$sql = "SELECT SUM(G.monto) as resumenGastos, G.FP 
				FROM gastos G  
				$conditions 
				GROUP BY G.FP "; 
				 				
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();		   	
		$results = $query->fetchAll();
		
		$finalResults['resumenGastos'] = array('Efectivo'=>0,'Tarjeta'=>0,'Debito'=>0,'Cheque'=>0,'Transferencia'=>0);
		$i=0;
		while($i < count($results))
			$finalResults['resumenGastos'][utf8_encode($results[$i]['FP'])]=$results[$i++]['resumenGastos'];	

			
		
	
		 
		return $finalResults;
	}
	
	
	
	
	
}
?>