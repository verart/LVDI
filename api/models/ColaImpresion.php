<?php
class ColaImpresion extends AppModel {
	
	public $name = "colaimpresion";
	public $primaryKey = 'id';	
	
	
	
	
	
	/**
	 * Retorna todos los prodcutos a imprimir
	 * 
	 */
	function getProductos() {
	
		$conditions = (isset($opciones['conditions']))? $this->_buildConditions($opciones['conditions']): "";	
		
		
		$sql = "SELECT CI.*, Pr.nombre as producto, Pr.precio, M.nombre as modelo, CL.nombre as clientePM
		 		FROM colaimpresion CI
				INNER JOIN modelos M ON CI.modelos_id = M.id
				INNER JOIN productos Pr ON Pr.id = M.productos_id 
				LEFT JOIN pedidos Ped ON Ped.id = CI.pedidos_id
				LEFT JOIN clientespm CL ON CL.id = Ped.clientesPM_id 
				WHERE CI.pedidos_id IS NULL 
				ORDER BY Pr.nombre"; 
				
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
	   	
		$results = $query->fetchAll();
		$i = 0;
		$resultsFormat = array();
		//Proceso los reposicion 
		$resultsFormat['reposicion']['modelos'] = array();
		if($results[0]['pedidos_id'] == null){		
			$m = 0;
			while(($i < count($results))&&($results[$i]['pedidos_id'] == null)){
				$resultsFormat['reposicion']['modelos'][$m]['modelos_id'] = $results[$i]['modelos_id'];
				$resultsFormat['reposicion']['modelos'][$m]['id'] = $results[$i]['id'];
				$resultsFormat['reposicion']['modelos'][$m]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
				$resultsFormat['reposicion']['modelos'][$m++]['precio'] = $results[$i++]['precio'];						
			}
		}
		
		$sql = "SELECT CI.id as id, CI.pedidos_id, Pr.nombre as producto, Pr.id as productos_id, SUM(PM.cantidad) as cantidad, CL.nombre as clientePM, CL.localidad as localidad
				FROM colaimpresion CI
				INNER JOIN modelos M ON CI.modelos_id = M.id
				INNER JOIN productos Pr ON Pr.id = M.productos_id 
				INNER JOIN pedidos_modelos PM ON (PM.modelos_id = M.id) & (PM.pedidos_id = CI.pedidos_id)
				INNER JOIN pedidos P ON PM.pedidos_id = P.id
				INNER JOIN clientespm CL ON P.clientespm_id = CL.id			
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
			//Si mientras se recorren los modelos alguno no tiene stock se cambia reponer a 1.
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
		
		
		return $resultsFormat;
	}
	
	
	
	

	
	
	/**
	* SET
	* $pedido = array( $idModelo, $idPedido )
	*/
	function set($idModelo, $idPedido=NULL){
		
		try{
			$prod = array('modelos_id'=>$idModelo);
			if($idPedido != NULL)$prod['pedidos_id']= $idPedido;
			
			if(!$this->create($prod))
				throw new BadRequestException('Hubo un error al agregar el código del producto a la cola.');
			
			//Agrego los modelos
			$idImp = $this->con->lastInsertID('colaimpresion', 'id');
			$prod['id']=$idImp;
							
			return array('success'=>true, 'ColaImpresion'=>$prod);
			
		} catch (Exception $e) {
			
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
		
		
	}
	

	
	
}
?>