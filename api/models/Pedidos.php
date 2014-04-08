<?php
class Pedidos extends AppModel {
	
	public $name = "Pedidos";
	public $primaryKey = 'id';	
	
	
	
	
	
	/**
	 * Retorna todos los pedidos
	 * params (array) $opciones = array([conditions])
	 */
	function getPedidos($opciones = array()) {
	
		$conditions = (isset($opciones['conditions']))? $this->_buildConditions($opciones['conditions']): "";	
		
		
		$sql = "SELECT P.id, P.total, P.bonificacion, P.FP as FP, P.nota as nota, C.nombre as cliente, C.id as clientesPM_id, P.estado, P.fecha,
		 		P.fecha_entrega, Pr.nombre as producto, Pr.precio, M.id as modelos_id, M.nombre as modelo, PM.cantidad, 
		 		PM.estado as estadoProducto, PM.id as idPedMod
				FROM pedidos P
				INNER JOIN clientesPM C ON C.id = P.clientesPM_id
				INNER JOIN pedidos_modelos PM ON PM.pedidos_id = P.id
				INNER JOIN modelos M ON PM.modelos_id = M.id
				INNER JOIN productos Pr ON Pr.id = M.productos_id 
				$conditions
				ORDER BY P.id"; 
				
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
	   	
		$results = $query->fetchAll();
		$iF = 0;
		$i = 0;
		$resultsFormat = array();
		//Proceso los pedidos 
		while($i < count($results)){
			$resultsFormat[$iF]['id'] = $results[$i]['id'];
			$resultsFormat[$iF]['fecha'] = $results[$i]['fecha'];
			$resultsFormat[$iF]['fecha_entrega'] = $results[$i]['fecha_entrega'];
			$resultsFormat[$iF]['total'] = $results[$i]['total'];
			$resultsFormat[$iF]['bonificacion'] = $results[$i]['bonificacion'];
			$resultsFormat[$iF]['clientesPM_id'] = $results[$i]['clientesPM_id']; 
			$resultsFormat[$iF]['cliente'] = utf8_encode($results[$i]['cliente']);
			$resultsFormat[$iF]['estado'] = $results[$i]['estado']; 
			$resultsFormat[$iF]['FP'] = $results[$i]['FP']; 
			$resultsFormat[$iF]['nota'] = $results[$i]['nota'];
			
			//Si mientras se recorren los modelos alguno no tiene stock se cambia reponer a 1.
			$resultsFormat[$iF]['modelos'] = array();
			$m = 0;
			while(($i < count($results))&&($resultsFormat[$iF]['id'] == $results[$i]['id'])){
				$resultsFormat[$iF]['modelos'][$m]['id'] = $results[$i]['modelos_id'];
				$resultsFormat[$iF]['modelos'][$m]['idPedMod'] = $results[$i]['idPedMod'];
				$resultsFormat[$iF]['modelos'][$m]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
				$resultsFormat[$iF]['modelos'][$m]['estado'] = $results[$i]['estadoProducto'];
				$resultsFormat[$iF]['modelos'][$m]['cantidad'] = $results[$i]['cantidad'];
				$resultsFormat[$iF]['modelos'][$m++]['precio'] = $results[$i++]['precio'];						
			}
			$iF++;
		}
		
		
		return $resultsFormat;
	}
	
	
	
	
	
	
	
	/**
	 * Retorna el pedido que coincide con el id
	 * @param $idProducto
	 */
	function getPedidoPorId($idPedido) {
		
				
		$sql = "SELECT P.total, P.bonificacion, C.nombre as cliente, Pr.nombre as producto, M.nombre as modelo, Pr.precio
				FROM pedidos P
				INNER JOIN clientesPM C ON C.id = P.clientesPM_id
				INNER JOIN pedidos_modelos PM ON P.id = PM.pedidos_id
				INNER JOIN modelos M ON M.id = PM.modelos_id
				INNER JOIN productos Pr ON Pr.id = M.productos_id	
				WHERE P.id = ?
				ORDER BY Pr.nombre, M.nombre";
				
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idPedido));
		$results = $query->fetchAll();
		
		$i = 0;
		$resultsFormat = array();
		//Proceso los pedidos 
		while($i < count($results)){
			$resultsFormat['id'] = $results[$i]['id'];
			$resultsFormat['fecha'] = $results[$i]['fecha'];
			$resultsFormat['fecha_entrega'] = $results[$i]['fecha_entrega'];
			$resultsFormat['total'] = $results[$i]['total'];
			$resultsFormat['bonificacion'] = $results[$i]['bonificacion'];
			$resultsFormat['clientesPM_id'] = $results[$i]['clientesPM_id']; 
			$resultsFormat['cliente'] = utf8_encode($results[$i]['cliente']);
			$resultsFormat['estado'] = $results[$i]['estado']; 
			$resultsFormat['FP'] = $results[$i]['FP']; 
			$resultsFormat['nota'] = $results[$i]['nota'];
			
			//Si mientras se recorren los modelos alguno no tiene stock se cambia reponer a 1.
			$resultsFormat['modelos'] = array();
			$m = 0;
			while(($i < count($results))&&($resultsFormat[$iF]['id'] == $results[$i]['id'])){
				$resultsFormat['modelos'][$m]['id'] = $results[$i]['modelos_id'];
				$resultsFormat['modelos'][$m]['idPedMod'] = $results[$i]['idPedMod'];
				$resultsFormat['modelos'][$m]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
				$resultsFormat['modelos'][$m]['estado'] = $results[$i]['estadoProducto'];
				$resultsFormat['modelos'][$m]['cantidad'] = $results[$i]['cantidad'];
				$resultsFormat['modelos'][$m++]['precio'] = $results[$i++]['precio'];						
			}
		}
		
		
		return $resultsFormat;
	}
	
	
	
	
	
	
	/**
	* SETPEDIDO
	* $pedido = array( ['id'=>''], 'clientesPM_id', 'bonificacion', 'fecha',['FP'], nota, total )
	* $modelos  = array( 	
	* 					array('id', 'cantidad', 'estado', 'idPedMod' ) )
	*/
	function setPedido($pedido, $modelos){
		
		try{
			$this->beginTransaction();
				
				
			if(!isset($pedido['id'])){ 
			
			
				// NUEVO MODELO
			
				if($this->create($pedido)) {
					
					//Agrego los modelos
					$idPedido = $this->con->lastInsertID('pedidos', 'id');
					
					foreach($modelos as $field => $value) {
					
						$idModelo = $value['id'];
						$cantidad = $value['cantidad'];
						$estado = isset($value['estado'])?$value['estado']:"'Pendiente'";
						
						$sql = "INSERT INTO pedidos_modelos (pedidos_id,modelos_id,cantidad,estado) VALUES ($idPedido, $idModelo, $cantidad, '$estado') ";
						$query = $this->con->query($sql);
						
						if(@PEAR::isError($query))
							throw new BadRequestException('Hubo un error al agregar los modelos al pedido.');
			
					}
					
				}else
					throw new BadRequestException('Hubo un error');
					
							
			}else{
				
				// UPDATE PEDIDO
				
				if($this->update($pedido, array('id'=>$pedido['id']))){
				
					$idPedido =  $pedido['id'];
				
					foreach($modelos as $field => $value) {
					
						$cantidad = $value['cantidad'];
						$idModelo = $value['id'];
						
						
						if(!isset($value['idPedMod'])){
						
							// Nuevo modelo para el Pedido
							
							$estado = isset($value['estado'])?"'".$value['estado']."'" :"'Pendiente'";	
							$fields = '(pedidos_id,modelos_id,cantidad,estado)';
							$values = "($idPedido, $idModelo, $cantidad, $estado)";
							
							$sql = "INSERT INTO pedidos_modelos $fields VALUES $values ";	
						
						}else{
						
							// Edicion de un modelo ya cargado al pedido	
						
							$id = $value['idPedMod'];
							$estado = (isset($value['estado'])) ? ", estado='".$value['estado']."'" : '' ;
							$sql = "UPDATE pedidos_modelos SET cantidad=$cantidad $estado WHERE (id = $id)";
						
						}

					
						$query = $this->con->query($sql);
						
						if(@PEAR::isError($query))
							throw new BadRequestException('Hubo un error en la modificación del pedido.');
			
					}
				}else
					throw new BadRequestException('Hubo un error al actualizar el pedido.');				
			}

			$this->commitTransaction();
			
			return array('success'=>true, 'pedidos_id'=>$idPedido);
			
		} catch (Exception $e) {
			
			$this->rollbackTransaction();
			
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
		
		
	}
	

	
	/**
	 * REMOVEMODELO 
	 * Quita un modelo de producto del pedido
	 * @param $idPedidoModelo
	 */
	function removeModelo($idPedidoModelo){
		
		try{
			
			$this->beginTransaction();
			
			$sql = "DELETE FROM pedidos_modelos WHERE id = $idPedidoModelo";

			$result = $this->con->query($sql);
		
			if(@PEAR::isError($result)) {
		    	throw new BadRequestException('Ocurrió un error al quitar el producto del pedido');				
		    }
		    
			$this->commitTransaction();
			
			return array('success'=>true, 'msg'=>'');
			
		} catch (Exception $e) {
			$this->rollbackTransaction();
			
			return array('success'=>false, 'msg'=>$e->getMsg());

		}
		
	}
	
	
	
	
	
}
?>