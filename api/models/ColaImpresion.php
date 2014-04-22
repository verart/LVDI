<?php
class ColaImpresion extends AppModel {
	
	public $name = "colaImpresion";
	public $primaryKey = 'id';	
	
	
	
	
	
	/**
	 * Retorna todos los prodcutos a imprimir
	 * 
	 */
	function getProductos() {
	
		$conditions = (isset($opciones['conditions']))? $this->_buildConditions($opciones['conditions']): "";	
		
		
		$sql = "SELECT CI.*, Pr.nombre as producto, Pr.precio, M.nombre as modelo
		 		FROM ColaImpresion CI
				INNER JOIN modelos M ON CI.modelos_id = M.id
				INNER JOIN productos Pr ON Pr.id = M.productos_id 
				ORDER BY CI.pedidos_id"; 
				
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
	   	
		$results = $query->fetchAll();
		$i = 0;
		$resultsFormat = array();
		//Proceso los reposicion 
		if($results[0]['pedidos_id'] == null){
			$resultsFormat['reposicion']['modelos'] = array();		
			$m = 0;
			while(($i < count($results))&&($results[$i]['pedidos_id'] == null)){
				$resultsFormat['reposicion']['modelos'][$m]['modelos_id'] = $results[$i]['modelos_id'];
				$resultsFormat['reposicion']['modelos'][$m]['id'] = $results[$i]['id'];
				$resultsFormat['reposicion']['modelos'][$m]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
				$resultsFormat['reposicion']['modelos'][$m++]['precio'] = $results[$i++]['precio'];						
			}
		}
		$m = 0;
		$iF = 0;
		//Proceso los pedidos 
		while($i < count($results)){
			$resultsFormat['pedidos'][$iF]['pedidos_id'] = $results[$i]['pedidos_id'];
			//Si mientras se recorren los modelos alguno no tiene stock se cambia reponer a 1.
			$resultsFormat['pedidos'][$iF]['modelos'] = array();
			$m = 0;
			while(($i < count($results))&&($resultsFormat['pedidos'][$iF]['pedidos_id'] == $results[$i]['pedidos_id'])){
				$resultsFormat['pedidos'][$iF]['modelos'][$m]['modelos_id'] = $results[$i]['modelos_id'];
				$resultsFormat['pedidos'][$iF]['modelos'][$m]['id'] = $results[$i]['id'];
				$resultsFormat['pedidos'][$iF]['modelos'][$m]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
				$resultsFormat['pedidos'][$iF]['modelos'][$m++]['precio'] = $results[$i++]['precio'];						
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
			$idImp = $this->con->lastInsertID('colaImpresion', 'id');
			$prod['id']=$idImp;
							
			return array('success'=>true, 'ColaImpresion'=>$prod);
			
		} catch (Exception $e) {
			
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
		
		
	}
	

	
	
}
?>