<?php
class ColaImpresion extends AppModel {
	
	public $name = "colaimpresion";
	public $primaryKey = 'id';	
	
	
	
	
	
	/**
	 * Retorna todos los prodcutos a imprimir
	 * params (INT) $userId 
	 */
	function getProductos($userId) {
	
	
		/************************************************
		PRODUCTOS DE REPOSICIONES HECHAS EN TALLER
		************************************************/
		
		$sql = "SELECT CI.*, Pr.nombre as producto, Pr.precio, M.nombre as modelo 
		 		FROM colaimpresion CI
				INNER JOIN modelos M ON CI.modelos_id = M.id
				INNER JOIN productos Pr ON Pr.id = M.productos_id 
				WHERE (CI.pedidos_id IS NULL) & (CI.belongsTo IS NULL) & (CI.ventas_id IS NULL)& (CI.producciones_id IS NULL)
				ORDER BY Pr.nombre"; 
				
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
	   	
		$results = $query->fetchAll();
		$i = 0;
		$resultsFormat = array();
		//Proceso los reposicion 
		$resultsFormat['reposicion']['modelos'] = array();
		$m = 0;
		while(($i < count($results))&&($results[$i]['pedidos_id'] == null)){
			$resultsFormat['reposicion']['modelos'][$m]['modelos_id'] = $results[$i]['modelos_id'];
			$resultsFormat['reposicion']['modelos'][$m]['id'] = $results[$i]['id'];
			$resultsFormat['reposicion']['modelos'][$m]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
			$resultsFormat['reposicion']['modelos'][$m++]['precio'] = $results[$i++]['precio'];						
		}
		
		
		/************************************************
		PRODUCTOS SUELTOS QUE EL USUARIO DESEA IMPRIMIR
		************************************************/ 
					
		$sql = "SELECT CI.*, Pr.nombre as producto, Pr.precio, M.nombre as modelo 
		 		FROM colaimpresion CI
				INNER JOIN modelos M ON CI.modelos_id = M.id
				INNER JOIN productos Pr ON Pr.id = M.productos_id 
				WHERE CI.belongsTo = $userId
				ORDER BY Pr.nombre"; 
				
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
	   	
		$results = $query->fetchAll();
		$i = 0;
		$resultsFormat['sueltos']['modelos'] = array();
		$m = 0;
		while(($i < count($results))&&($results[$i]['pedidos_id'] == null)){
			$resultsFormat['sueltos']['modelos'][$m]['modelos_id'] = $results[$i]['modelos_id'];
			$resultsFormat['sueltos']['modelos'][$m]['id'] = $results[$i]['id'];
			$resultsFormat['sueltos']['modelos'][$m]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
			$resultsFormat['sueltos']['modelos'][$m++]['precio'] = $results[$i++]['precio'];						
		}
		
		
		
		/************************************************
		PRODUCTOS DE PEDIDOS HECHOS EN TALLER
		************************************************/
		
		
		$sql = "SELECT CI.*, Pr.nombre as producto, Pr.id as productos_id, SUM(PM.cantidad) as cantidad, CL.nombre as clientePM, CL.localidad as localidad
				FROM colaimpresion CI
				INNER JOIN modelos M ON CI.modelos_id = M.id
				INNER JOIN productos Pr ON Pr.id = M.productos_id 
				INNER JOIN pedidos_modelos PM ON (PM.modelos_id = M.id) & (PM.pedidos_id = CI.pedidos_id)
				INNER JOIN pedidos P ON PM.pedidos_id = P.id
				INNER JOIN clientespm CL ON P.clientesPM_id = CL.id			
				GROUP BY CI.pedidos_id, M.productos_id	
				ORDER BY CI.pedidos_id";
				
		$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
	   	
		$results = $query->fetchAll();
		$i=0;
		$m = 0;
		$iF = 0;
		//Proceso los pedidos 
		while($i < count($results)){
			$resultsFormat['pedidos'][$iF]['pedidos_id'] = $results[$i]['pedidos_id'];
			$resultsFormat['pedidos'][$iF]['clientePM']= utf8_encode($results[$i]['clientePM']).' - '.utf8_encode($results[$i]['localidad']);
			//los modelos
			$resultsFormat['pedidos'][$iF]['modelos'] = array();
			$m = 0;
			while(($i < count($results))&&($resultsFormat['pedidos'][$iF]['pedidos_id'] == $results[$i]['pedidos_id'])){
				$resultsFormat['pedidos'][$iF]['productos'][$m]['productos_id'] = $results[$i]['productos_id'];
				$resultsFormat['pedidos'][$iF]['productos'][$m]['id'] = $results[$i]['id'];
				$resultsFormat['pedidos'][$iF]['productos'][$m]['nombre'] = utf8_encode($results[$i]['producto']);
				$resultsFormat['pedidos'][$iF]['productos'][$m++]['cantidad'] = $results[$i++]['cantidad'];						
			}
			$iF++;
		}
		
		
		/************************************************
		PRODUCTOS DE PRODUCCIONES DEVUELTAS
		************************************************/
		
		$sql = "SELECT CI.*, Pr.nombre as producto, Pr.precio, M.nombre as modelo, R.nombre as responsable 
				FROM colaimpresion CI
				INNER JOIN modelos M ON CI.modelos_id = M.id
				INNER JOIN productos Pr ON Pr.id = M.productos_id 
				INNER JOIN producciones_modelos PM ON (PM.modelos_id = M.id) & (PM.producciones_id = CI.producciones_id)
				INNER JOIN producciones P ON PM.producciones_id = P.id
				INNER JOIN responsables R ON P.responsables_id = R.id			
				GROUP BY CI.producciones_id, PM.id				
				ORDER BY CI.producciones_id";
				
		$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
	   	
		$results = $query->fetchAll();
		$i=0;
		$m = 0;
		$iF = 0;
		//Proceso las producciones 
		while($i < count($results)){
			$resultsFormat['producciones'][$iF]['producciones_id'] = $results[$i]['producciones_id'];
			$resultsFormat['producciones'][$iF]['responsable']= utf8_encode($results[$i]['responsable']);
			//los modelos
			$resultsFormat['producciones'][$iF]['modelos'] = array();
			$m = 0;
			while(($i < count($results))&&($resultsFormat['producciones'][$iF]['producciones_id'] == $results[$i]['producciones_id'])){
				$resultsFormat['producciones'][$iF]['modelos'][$m]['modelos_id'] = $results[$i]['modelos_id'];
				$resultsFormat['producciones'][$iF]['modelos'][$m]['id'] = $results[$i]['id'];
				$resultsFormat['producciones'][$iF]['modelos'][$m]['nombre'] =  utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
				$resultsFormat['producciones'][$iF]['modelos'][$m++]['precio'] = $results[$i++]['precio'];	
			}
			$iF++;
		}
		
		/************************************************
		PRODUCTOS DE DEVOLUCIONES DE VENTAS
		************************************************/
		
		$sql = "SELECT CI.*, Pr.nombre as producto, Pr.precio, M.nombre as modelo  
				FROM colaimpresion CI
				INNER JOIN modelos M ON CI.modelos_id = M.id
				INNER JOIN productos Pr ON Pr.id = M.productos_id 
				WHERE not(CI.ventas_id IS NULL)
				ORDER BY CI.ventas_id";
				
		$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
	   	
		$results = $query->fetchAll();
		$i = 0;
		$resultsFormat['ventas']['modelos'] = array();
		$m = 0;
		while(($i < count($results))&&($results[$i]['pedidos_id'] == null)){
			$resultsFormat['ventas']['modelos'][$m]['modelos_id'] = $results[$i]['modelos_id'];
			$resultsFormat['ventas']['modelos'][$m]['id'] = $results[$i]['id'];
			$resultsFormat['ventas']['modelos'][$m]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
			$resultsFormat['ventas']['modelos'][$m++]['precio'] = $results[$i++]['precio'];						
		}

		return $resultsFormat;
	}
	
	
	
	

	
	
	/**
	* SET
	* $productoAImprimir = array( $idModelo, $idPedido, $idProduccion, $belongsTo )
	*/
	function set($idModelo, $idPedido=NULL, $idProduccion=NULL, $belongsTo=NULL, $idVenta=NULL){
		
		try{
			$prod = array('modelos_id'=>$idModelo);
			if($idPedido != NULL)$prod['pedidos_id']= $idPedido;
			if($idProduccion != NULL)$prod['producciones_id']= $idProduccion;
			if($idVenta != NULL)$prod['ventas_id']= $idVenta;
			if($belongsTo != NULL)$prod['belongsTo']= $belongsTo;
			
			if(!$this->create($prod))
				throw new BadRequestException('Hubo un error al agregar el producto a la cola de impresión.');

			//Agrego los modelos
			$idImp = $this->con->lastInsertID('colaimpresion', 'id');
			$prod['id']=$idImp;
							
			return array('success'=>true, 'ColaImpresion'=>$prod);
			
		} catch (Exception $e) {
			
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
		
		
	}
	

	/**
	* DELETEPEDIDO
	* $pedidoAImprimir (int)
	*/
	function deletePedido($pedidoAImprimir){
		
		try{
			
			$sql = 'DELETE FROM colaimpresion WHERE pedidos_id = '.$pedidoAImprimir; 
			
			$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);	
			$query = $query->execute();	
				
			return array('success'=>true, 'cant'=>$query);
			
		} catch (Exception $e) {
			
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
		
		
	}	

	
	/**
	* DELETEPRODUCCION
	* $produccionAImprimir (int)
	*/
	function deleteProduccion($produccionAImprimir){
		
		try{
			
			$sql = 'DELETE FROM colaimpresion WHERE producciones_id = '.$produccionAImprimir; 
			
			$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);	
			$query = $query->execute();	
				
			return array('success'=>true, 'cant'=>$query);
			
		} catch (Exception $e) {
			
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
		
		
	}	
	
}
?>