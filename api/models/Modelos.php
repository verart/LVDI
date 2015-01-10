<?php
class Modelos extends AppModel {
	
	public $name = "Modelos";
	public $primaryKey = 'id';	
	 
	
	public $hasMany = array('MovimientosStock');
	    
	
	/**
	 * Retorna el modelo idModelo
	 */
	function getModeloPorId($idModelo) {
		
		$sql = "SELECT *
				FROM modelos M
				WHERE M.id = ?";
				
	   	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute(array($idModelo));	
		return $query->fetchRow();
		
	}
	
	/**
	 * Retorna todos los modelos del producto idProducto
	 */
	function getModelos($idProducto) {
		
		$sql = "SELECT *
				FROM modelos M
				WHERE M.productos_id = ?
				ORDER BY M.nombre";
				
	   	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute(array($idProduccion));	
		return $query->fetchAll();
		
	}



	/**
	 * DELMODELOPORPRODUCTOID
	 * Elimina todos los modelos del producto $idProducto
	 * @param (int) $idProducto
	 */
	function delModeloPorProductoId($idProducto) {
		
	
		// Solo se elimina si no esta en uso ninguno de los modelos
		if($this->notUsedPorProducto($idProducto)){
		
			$sql = "DELETE FROM ".$this->table." WHERE productos_id = $idProducto";		
			$result = $this->con->query($sql);

			if(PEAR::isError($result))
			    return false;
			
			return true;
		}else
			return false;
			
	}
	
	
	/**
	 * NOTUSEDPORPRODUCTO
	 * Retorna si alguno de los modelos del idProducto esta siendo usado
	 * @param (int) $idProducto
	 */
	function notUsedPorProducto($idProducto) {
		
		// Chequeo que alguno de los modelos no este en algun pedido
		$sql = "SELECT *
				FROM productos P
				INNER JOIN modelos M ON P.id = M.productos_id
				INNER JOIN pedidos_modelos PM ON PM.modelos_id = M.id
				WHERE P.id = ?";
							
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idProducto));
		$modelosPedidos = $query->fetchAll();	
		
		
		// Chequeo que alguno de los modelos no este en alguna produccion		
		$sql = "SELECT *
				FROM productos P
				INNER JOIN modelos M ON P.id = M.productos_id
				INNER JOIN producciones_modelos PM ON PM.modelos_id = M.id
				WHERE P.id = ?";	
					
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idProducto));
		$modelosProducciones = $query->fetchAll();	
		
	
		return (empty($modelosPedidos) && empty($modelosProducciones));
			
	}
	
	
	
	
	/**
	* REPONER
	* Incrementa en 1 el stock del modelo y registra el movimiento con el tipo: 
	*/
	function reponer($idModelo, $cantidad =1, $tipo="Reposicion"){
		
		try{
			
			$mod = $this->getModeloPorId($idModelo);
	
			$modelo = array('id'=>$idModelo,'stock'=>$mod['stock']+$cantidad);
			
			if(!$this->update($modelo, array('id'=>$idModelo)))
				throw new BadRequestException('Hubo un error al reponer el modelo '.$idModelo);
				
			$movimiento = array(
				'modelos_id'=> $idModelo, 'created'=> date('Y/m/d h:i:s', time()), 'tipo'=> $tipo, 'cantidad'=> $cantidad);	
			if(!$this->MovimientosStock->setMovimiento($movimiento))
				throw new BadRequestException('Hubo un error al crear el movimiento para el modelo '.$idModelo);
							
			
			return (array('success'=>true, 'msg'=>'El modelo se repuso con éxito.'));

		} catch (Exception $e) {
		
			return (array('success'=>false, 'msg'=>$e->getMsg()));
			
		}
	}



	/**
	* BAJA
	* Decrementa en 1 el stock del modelo y registra el movimiento
	*/
	function baja($idModelo, $cantidad = 1,$motivo='', $tipoBaja='Baja'){
		
		try{
		
			$mod = $this->getModeloPorId($idModelo);
					
			$modelo = array('id'=>$idModelo,'stock'=>($mod['stock']-$cantidad));
			
			if(!$this->update($modelo, array('id'=>$idModelo)))
				throw new BadRequestException('Hubo un error al dar de baja el modelo');
				
			$movimiento = array(
				'modelos_id'=> $idModelo, 'created'=> date('Y/m/d h:i:s', time()), 'tipo'=> $tipoBaja, 'cantidad'=> $cantidad, 'nota'=>$motivo);
					
			if(!$this->MovimientosStock->setMovimiento($movimiento))
				throw new BadRequestException('Hubo un error al crear el movimiento de baja.');
							

			return (array('success'=>true, 'msg'=>'El modelo ha sido dado de baja.'));
			
		} catch (Exception $e) {

			return (array('success'=>false, 'msg'=>$e->getMsg()));
			
		}
	}
	
	
	
	
	/**
	 * NOTUSED
	 * Retorna si el modelo esta siendo usado
	 * @param (int) $idMod
	 */
	function notUsed($idMod) {
		
		// Chequeo que el modelo no este en algun pedido
		$sql = "SELECT *
				FROM pedidos_modelos PM
				WHERE PM.modelos_id = ?";
							
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idMod));
		$modelosPedidos = $query->fetchAll();	
		
		if(!empty($modelosPedidos)) 
			return false;
		
		// Chequeo que alguno de los modelos no este en alguna produccion		
		$sql = "SELECT *
				FROM producciones_modelos PM 
				WHERE PM.modelos_id = ?";	
					
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idMod));
		$modelosProducciones = $query->fetchAll();	
		
	
		return (empty($modelosProducciones));
			
	}
	
	
	
	/**
	 * NOTUNIQUE
	 * Retorna si el modelo es unico para su producto
	 * @param (int) $idMod
	 */
	function notUnique($idMod) {
		
		// Chequeo que el modelo no este en algun pedido
		$sql = "SELECT productos_id as id
				FROM modelos M
				WHERE M.id = ?";
							
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idMod));
		$producto = $query->fetchAll();	
	
		// Chequeo que el producto tenga mas de un modelo		
		$sql = "SELECT id
				FROM modelos M 
				WHERE M.productos_id = ?";
		
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($producto[0]['id'])); 			
		
		$mods = $query->fetchAll();	

		return ( count($mods) > 1 );
			
	}
	
	
	
	/**
	 * DELETE
	 * Elimina el modelo $idMod
	 * @param (int) $idMod
	 */
	function delete($idMod) {
		
		// Solo se elimina si no esta en uso ninguno de los pedidos/produccion
		if($this->notUsed($idMod) ){ 

			if(!($this->notUnique($idMod)) )
				return array('success'=>false, 'msg'=>'No se puede eliminar este modelo. Es el único del producto');
			
			$sql = "DELETE FROM modelos WHERE id = $idMod";		
			$result = $this->con->query($sql);

			if(@PEAR::isError($result))
				return array('success'=>false, 'msg'=>'No se puede eliminar este modelo.');
				
			return array('success'=>true, 'msg'=>'');
			
		}else
			return array('success'=>false, 'msg'=>'No se puede eliminar este modelo. Está incluido en un pedido o producción.');
	}
	
	
	/**
	 * LOGICDELETE
	 * elimina de manera lógica el modelo $idMod
	 * @param (int) $idMod
	 */
	function logicDelete($idMod) {
		
		
		if(!($this->notUnique($idMod)) )
			return array('success'=>false, 'msg'=>'No se puede eliminar este modelo. Es el único del producto');
			
		$sql = "UPDATE modelos SET baja=1 WHERE id = $idMod";	
			
		$result = $this->con->query($sql);

		if(@PEAR::isError($result))
				return array('success'=>false, 'msg'=>'No se puede eliminar este modelo.');
				
		return array('success'=>true, 'msg'=>'');
			
		
	}
	
}
?>