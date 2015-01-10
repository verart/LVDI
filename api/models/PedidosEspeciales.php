<?php
class Pedidosespeciales extends AppModel {
	
	public $name = "Pedidosespeciales";
	public $primaryKey = 'id';	
	
	

	/**
	 * Retorna todos los pedidos especiales
	 * params (array) $opciones = array([conditions])
	 */
	function getPedidos($opciones = array(), $requested_page = 1) {
	
		
	 	$set_limit = " LIMIT ".(($requested_page - 1) * 15) . ",15"; 

		$conditions = (isset($opciones['conditions']))? $this->_buildConditions($opciones['conditions']): "";	
				
	
		$sql = "SELECT P.*, C.nombre as cliente, C.id as clientes_id 		
				FROM pedidosespeciales P 
				INNER JOIN clientes C ON C.id = P.clientes_id 
				$conditions
				ORDER BY P.id   
				$set_limit ";
				
		
				
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
	   	$results = $query->fetchAll();


	   	$iF = 0;
		//Proceso los pedidos especiales
		while($iF < count($results)){ 
			$results[$iF]['cliente'] = utf8_encode($results[$iF]['cliente']);
			$iF++;
		}	


		return $results;
	}
	


	/**
	 * Retorna todos los pagos del pedido especial
	 * params (int) 
	 */
	function getPagos($idPedido) {
	
				
		$sql = "SELECT *
				FROM pedidosespeciales_pagos PP 
				WHERE PP.pedidosespeciales_id = ? 
				ORDER BY PP.created DESC"; 
				
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idPedido));
		$results = $query->fetchAll();		
		
		return $results;
	}
	
	
	




	
	
	/**
	 * Retorna el pedido que coincide con el id
	 * @param $idProducto
	 */
	function getPedidoPorId($idPedido) {
		
				
		$sql = "SELECT P.*, C.nombre as cliente, C.id as clientes_id 
				FROM pedidosespeciales P
				INNER JOIN clientes C ON C.id = P.clientes_id
				WHERE P.id = ?";
				
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idPedido));
		$results = $query->fetchAll();
		
		$resultsFormat = array();

		$resultsFormat['id'] = $results[0]['id'];
		$resultsFormat['created'] = $results[0]['created'];
		$resultsFormat['fecha_entrega'] = $results[0]['fecha_entrega'];
		$resultsFormat['total'] = $results[0]['total'];
		$resultsFormat['bonificacion'] = $results[0]['bonificacion'];
		$resultsFormat['clientes_id'] = $results[0]['clientes_id']; 
		$resultsFormat['cliente'] = utf8_encode($results[0]['cliente']);
		$resultsFormat['estado'] = $results[0]['estado']; 
		$resultsFormat['descripcion'] = $results[0]['descripcion'];

		return $resultsFormat;
	}
	
	
	
	
	
	
	/**
	* SETPEDIDO
	* $pedido = array( ['id'=>''], 'clientes_id', 'bonificacion', 'created',['Fecha_entrega'],['FP'], descripcion, total )
	* $pagos  = array( 	
	* 					array('id', 'monto', 'created', 'FP' ) )
	*/
	function setPedido($pedido, $pagos = array()){
		
		try{
			$this->beginTransaction();
				
				
			if(!isset($pedido['id'])){ 
			
				// NUEVO PEDIDO
				if($this->create($pedido))					
					//id del pedido creado
					$pedido['id'] = $this->con->lastInsertID('pedidos', 'id');
				else
					throw new BadRequestException('Hubo un error al crear el pedido.');		
							
			}else{
				
				// UPDATE PEDIDO				
				if(!$this->update($pedido, array('id'=>$pedido['id'])))	
					throw new BadRequestException('Hubo un error al actualizar el pedido.');				
					
			}
			$idPedido =  $pedido['id'];
			//Pagos realizado 
			foreach($pagos as $field => $value) {
					
				if(!isset($value['id'])){
					$monto = $value['monto'];
					$created = isset($value['created'])?$value['created']: date('dd/mm/yyyy');
					$FP = $value['FP'];
						
					$sql = "INSERT INTO pedidosespeciales_pagos (pedidosespeciales_id,monto,FP,created) VALUES ($idPedido,$monto,'$FP','$created') "; 
					$query = $this->con->query($sql);
					
					if(@PEAR::isError($query))
						throw new BadRequestException('Hubo un error al agregar los pagos del pedido.');
				}
			}	

			$this->commitTransaction();
			return array('success'=>true, 'id'=>$idPedido);
			
		} catch (Exception $e) {
			
			$this->rollbackTransaction();
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
		
		
	}
	
		
	
	/**
	 * ELIMINARPEDIDO
	 * Elimina un pedido y todos sus pagos
	 * @param $idPedido
	 */
	function eliminarPedido($idPedido){
	
		try{
			
			$this->beginTransaction();

			$result = $this->removePagosPedido($idPedido);
			if(!$result['success'])
				throw new BadRequestException($result['msg']);	

			$this->delete($idPedido);
			
			$this->commitTransaction();
			return array('success'=>true, 'msg'=>'El pedido fue eliminado.');

		}catch (Exception $e) {
			$this->rollbackTransaction();
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
	
	}


	/**
	 * REMOVEPAGOPEDIDO
	 * Quita un pago del pedido especial idPedido
	 * @param $idPedido
	 */
	function removePagosPedido($idPedido){
		
		try{
			
			$this->beginTransaction();
			
			$sql = "DELETE FROM pedidosespeciales_pagos WHERE pedidosespeciales_id = $idPedido";

			$result = $this->con->query($sql);
			if(@PEAR::isError($result))
		    	throw new BadRequestException('Ocurrió un error al eliminar los pagos del pedido.');				
		    
			$this->commitTransaction();
			
			return array('success'=>true, 'msg'=>'');
			
		} catch (Exception $e) {
			$this->rollbackTransaction();
			
			return array('success'=>false, 'msg'=>$e->getMsg());

		}
		
	}

	
	/**
	 * REMOVEPAGO 
	 * Quita un pago del pedido especial
	 * @param $idPagoPedido
	 */
	function removePago($idPagoPedido){
		
		try{
			
			$this->beginTransaction();
			
			$sql = "DELETE FROM pedidosespeciales_pagos WHERE id = $idPagoPedido";

			$result = $this->con->query($sql);
		
			if(@PEAR::isError($result)) {
		    	throw new BadRequestException('Ocurrió un error al eliminar el pago del pedido.');				
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