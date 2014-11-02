<?php
class Pedidos extends AppModel {
	
	public $name = "Pedidos";
	public $primaryKey = 'id';	
	
	
	public $hasMany = array('ColaImpresion'); 
	



	/**
	 * Retorna todos los pedidos
	 * params (array) $opciones = array([conditions])
	 */
	function getPedidos($opciones = array(), $requested_page = 1) {
	
		
	 	$set_limit = " LIMIT ".(($requested_page - 1) * 15) . ",15"; 

		$conditions = (isset($opciones['conditions']))? $this->_buildConditions($opciones['conditions']): "";	
				
	
		$sql = "SELECT P.id, P.total, P.bonificacion, P.nota as nota, C.nombre as cliente, C.localidad as localidad, C.id as clientesPM_id, 					P.estado, P.fecha,P.fecha_entrega,  modelosPedidos.totalActual 
				FROM pedidos P 
				INNER JOIN clientespm C ON C.id = P.clientesPM_id 
				INNER JOIN (
					SELECT  PM.pedidos_id, SUM(Pr.precio*PM.cantidad) as totalActual 
					FROM pedidos_modelos PM
					INNER JOIN modelos M ON PM.`modelos_id` = M.id
					INNER JOIN productos Pr ON Pr.id = M.productos_id 
					GROUP BY PM.pedidos_id	
				) as modelosPedidos ON modelosPedidos.pedidos_id = P.id  
				$conditions
				ORDER BY P.id   
				$set_limit "; 
				
		
				
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
	   	$results = $query->fetchAll();


	   	$iF = 0;
		//Proceso los pedidos 
		while($iF < count($results)){ 
			$results[$iF]['cliente'] = utf8_encode($results[$iF]['cliente']);
			$results[$iF]['localidad'] = utf8_encode($results[$iF]['localidad']);
			$iF++;
		}	


		return $results;
	}
	





	/**
	 * Retorna todos los modelos del pedido
	 * params (int) 
	 */
	function getModelos($idPedido) {
	
				
		$sql = "SELECT Pr.nombre as producto, Pr.precio, M.id as modelos_id, M.nombre as modelo, PM.cantidad, 
		 		PM.estado as estadoProducto, PM.id as idPedMod, PM.precio as PedProdPrecio, P.estado 
				FROM pedidos P
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
		while($i < count($results)){
				$resultsFormat[$i]['id'] = $results[$i]['modelos_id'];
				$resultsFormat[$i]['idPedMod'] = $results[$i]['idPedMod'];
				$resultsFormat[$i]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
				$resultsFormat[$i]['estado'] = $results[$i]['estadoProducto'];
				$resultsFormat[$i]['cantidad'] = $results[$i]['cantidad'];
				$resultsFormat[$i]['precio'] = (($results[$i]['estado'] == 'Entregado-Pago')||($results[$i]['estado'] == 'Entregado-Debe'))?$results[$i]['PedProdPrecio'] : $results[$i]['precio'];	
				$i++;										
			}
		return $resultsFormat;
	}
	



	/**
	 * Retorna todos los pagos del pedido
	 * params (int) 
	 */
	function getPagos($idPedido) {
	
				
		$sql = "SELECT *
				FROM pedidos_pagos PP 
				WHERE PP.pedidos_id = ? 
				ORDER BY PP.created DESC"; 
				
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idPedido));
		$results = $query->fetchAll();		
		
		return $results;
	}
	
	
	




	
	
	/**
	 * Retorna todos los pedidos
	 * params (array) $opciones = array([conditions])
	 */
/*

	function getPedidos($opciones = array()) {
	
		$conditions = (isset($opciones['conditions']))? $this->_buildConditions($opciones['conditions']): "";	
		
		
		$sql = "SELECT P.id, P.total, P.bonificacion, P.FP as FP, P.nota as nota, C.nombre as cliente, C.localidad as localidad, C.id as clientesPM_id, P.estado, P.fecha,P.fecha_entrega, Pr.nombre as producto, Pr.precio, M.id as modelos_id, M.nombre as modelo, PM.cantidad, 
		 		PM.estado as estadoProducto, PM.id as idPedMod, PM.precio as PedProdPrecio
				FROM pedidos P
				INNER JOIN clientespm C ON C.id = P.clientesPM_id
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
			$resultsFormat[$iF]['bonificacion'] = $results[$i]['bonificacion'];
			$resultsFormat[$iF]['clientesPM_id'] = $results[$i]['clientesPM_id']; 
			$resultsFormat[$iF]['cliente'] = utf8_encode($results[$i]['cliente']);
			$resultsFormat[$iF]['localidad'] = utf8_encode($results[$i]['localidad']);
			$resultsFormat[$iF]['estado'] = $results[$i]['estado']; 
			$resultsFormat[$iF]['FP'] = $results[$i]['FP']; 
			$resultsFormat[$iF]['nota'] = $results[$i]['nota'];
			$resultsFormat[$iF]['total'] =$results[$i]['total'];
			
			//Si mientras se recorren los modelos alguno no tiene stock se cambia reponer a 1.
			$resultsFormat[$iF]['modelos'] = array();
			$m = 0;
			$total = 0;
			while(($i < count($results))&&($resultsFormat[$iF]['id'] == $results[$i]['id'])){
				$resultsFormat[$iF]['modelos'][$m]['id'] = $results[$i]['modelos_id'];
				$resultsFormat[$iF]['modelos'][$m]['idPedMod'] = $results[$i]['idPedMod'];
				$resultsFormat[$iF]['modelos'][$m]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
				$resultsFormat[$iF]['modelos'][$m]['estado'] = $results[$i]['estadoProducto'];
				$resultsFormat[$iF]['modelos'][$m]['cantidad'] = $results[$i]['cantidad'];
				//Tomo el precio actual del producto o el que se le asigno al pedido, dependiendo del estado.
				//Cuando el estado es entregado-* el precio de los productos se congela.
				$resultsFormat[$iF]['modelos'][$m]['precio'] = (($resultsFormat[$iF]['estado'] == 'Entregado-Pago')||($resultsFormat[$iF]['estado'] == 'Entregado-Debe'))?$results[$i]['PedProdPrecio'] : $results[$i]['precio'];	
				$total = $total + ($resultsFormat[$iF]['modelos'][$m++]['precio'] * $results[$i]['cantidad']);	
				$i++;				
			}
			
			//Si el pedido ya fue entregado el precio es el que tenian lo productos es en ese momento. En cualquier otro estado del pedido
			// se calcula el total del pedido en base a los precios actuales de los productos
			$resultsFormat[$iF]['total'] = (($resultsFormat[$iF]['estado'] == 'Entregado-Pago')||($resultsFormat[$iF]['estado'] == 'Entregado-Debe'))? $resultsFormat[$iF]['total'] : $total;
			$iF++;
		}
		
		
		return $resultsFormat;
	}
	
*/
	
	
	
	
	
	
	/**
	 * Retorna el pedido que coincide con el id
	 * @param $idProducto
	 */
	function getPedidoPorId($idPedido) {
		
				
		$sql = "SELECT P.id, P.total, P.bonificacion, P.FP as FP, P.nota as nota, C.nombre as cliente, C.localidad as localidad, C.id as clientesPM_id, P.estado, P.fecha,P.fecha_entrega, Pr.nombre as producto, Pr.precio, M.id as modelos_id, M.nombre as modelo, PM.cantidad, 
		 		PM.estado as estadoProducto, PM.id as idPedMod, PM.precio as PedProdPrecio
				FROM pedidos P
				INNER JOIN clientespm C ON C.id = P.clientesPM_id
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

			$resultsFormat['id'] = $results[$i]['id'];
			$resultsFormat['fecha'] = $results[$i]['fecha'];
			$resultsFormat['fecha_entrega'] = $results[$i]['fecha_entrega'];
			$resultsFormat['total'] = $results[$i]['total'];
			$resultsFormat['bonificacion'] = $results[$i]['bonificacion'];
			$resultsFormat['clientesPM_id'] = $results[$i]['clientesPM_id']; 
			$resultsFormat['cliente'] = utf8_encode($results[$i]['cliente']);
			$resultsFormat['localidad'] = utf8_encode($results[$i]['localidad']);
			$resultsFormat['estado'] = $results[$i]['estado']; 
			$resultsFormat['nota'] = $results[$i]['nota'];
			
			$resultsFormat['modelos'] = array();
			$m = 0;
			$total = 0;
			while($i < count($results)){
				$resultsFormat['modelos'][$m]['id'] = $results[$i]['modelos_id'];
				$resultsFormat['modelos'][$m]['idPedMod'] = $results[$i]['idPedMod'];
				$resultsFormat['modelos'][$m]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
				$resultsFormat['modelos'][$m]['estado'] = $results[$i]['estadoProducto'];
				$resultsFormat['modelos'][$m]['cantidad'] = $results[$i]['cantidad'];
				$resultsFormat['modelos'][$m]['precio'] = (($resultsFormat['estado'] == 'Entregado-Pago')||($resultsFormat['estado'] == 'Entregado-Debe'))?$results[$i]['PedProdPrecio'] : $results[$i]['precio'];	
				$total = $total + ($resultsFormat['modelos'][$m++]['precio']*$results[$i]['cantidad']);	
				$i++;										
			}
			$resultsFormat['total'] = (($resultsFormat['estado'] == 'Entregado-Pago')||($resultsFormat['estado'] == 'Entregado-Debe'))?$resultsFormat['total']:$total;
		return $resultsFormat;
	}
	
	
	
	
	
	
	/**
	* SETPEDIDO
	* $pedido = array( ['id'=>''], 'clientesPM_id', 'bonificacion', 'fecha',['FP'], nota, total )
	* $modelos  = array( 	
	* 					array('id', 'cantidad', 'estado', 'idPedMod' ) )
	* $pagos  = array( 	
	* 					array('id', 'monto', 'created', 'FP' ) )
	*/
	function setPedido($pedido, $modelos, $pagos = array()){
		
		try{
			$this->beginTransaction();
				
				
			if(!isset($pedido['id'])){ 
			
			
				// NUEVO PEDIDO

				if($this->create($pedido)) {
					
					//Agrego los modelos
					$idPedido = $this->con->lastInsertID('pedidos', 'id');
					
					//Modelos del pedido
					foreach($modelos as $field => $value) {
					
						$idModelo = $value['id'];
						$cantidad = $value['cantidad'];
						$estado = isset($value['estado'])?$value['estado']:"'Pendiente'";
						$precio = (($pedido['estado']=='Entregado-Pago')||($pedido['estado']=='Entregado-Debe'))?$value['precio']:0;
						
						$sql = "INSERT INTO pedidos_modelos (pedidos_id,modelos_id,cantidad,estado,precio) VALUES ($idPedido, $idModelo, $cantidad, '$estado',$precio) "; 
						$query = $this->con->query($sql);
						
						
						if(($cantidad >= 1) && ($estado == 'Terminado'))
							$res = $this->ColaImpresion->set($idModelo,$idPedido, null, null);
						
						if(@PEAR::isError($query))
							throw new BadRequestException('Hubo un error al agregar los modelos al pedido.');
			
					}
					
					//Pagos realizado 
					foreach($pagos as $field => $value) {
					
						$monto = $value['monto'];
						$created = isset($value['created'])?$value['created']: date('dd/mm/yyyy');
						$FP = $value['FP'];
						
						$sql = "INSERT INTO pedidos_pagos (pedidos_id,monto,FP,created) VALUES ($idPedido,$monto,'$FP','$created') "; 
						$query = $this->con->query($sql);
						
						if(@PEAR::isError($query))
							throw new BadRequestException('Hubo un error al agregar los pagos del pedido.');
			
					}
					
					
				}else
					throw new BadRequestException('Hubo un error');
					
							
			}else{
				
				// UPDATE PEDIDO
				
				$estadoPedido = $this->getPedidoPorId($pedido['id']);
				$estadoModelosPedidos = $estadoPedido['modelos'];

				if($this->update($pedido, array('id'=>$pedido['id']))){
				
					$idPedido =  $pedido['id'];
				
				
					// MODELOS del pedido
					foreach($modelos as $field => $value) {
					
						$cantidad = $value['cantidad'];
						$idModelo = $value['id'];						

						
						if(!isset($value['idPedMod'])){
						
							// Nuevo modelo para el Pedido
							
							$estado = isset($value['estado'])?"'".$value['estado']."'" :"'Pendiente'";	
							$precio = (($pedido['estado']=='Entregado-Pago')||($pedido['estado']=='Entregado-Debe'))?$value['precio']:0;
							$fields = '(pedidos_id,modelos_id,cantidad,estado,precio)';
							$values = "($idPedido, $idModelo, $cantidad, $estado, $precio)";
							
							$sql = "INSERT INTO pedidos_modelos $fields VALUES $values ";	
							
							if(($cantidad >=1) && ($estado == "'Terminado'")){
								$res = $this->ColaImpresion->set($idModelo,$idPedido);
							
								if(!$res['success']) 
									throw new BadRequestException('No puedo agregarse la etiqueta del producto a la cola de impresión.');
							}	

						
						}else{
							// Edicion de un modelo ya cargado al pedido	
						
							//Busco el modelo en el pedido
							$modPed = null; 
							foreach($estadoModelosPedidos as $mod) {
							    if ($value['idPedMod'] == $mod['idPedMod']) {
							        $modPed = $mod;
							        break;
							    }
							}
						
							$id = $value['idPedMod'];
							$idModelo = $value['id'];
							$precio = (($pedido['estado']=='Entregado-Pago')||($pedido['estado']=='Entregado-Debe'))?$value['precio']:0;
							$estado = (isset($value['estado'])) ? ", estado='".$value['estado']."'" : '' ;
							$sql = "UPDATE pedidos_modelos SET cantidad=$cantidad, precio=$precio $estado WHERE (id = $id)";
							
							//Si el antiguo estado era pendiente y el nuevo es terminado, guarda la etiqueta para imprimir
							if(($modPed['estado'] == 'Pendiente')&&($value['estado'] == 'Terminado')){
								$res = $this->ColaImpresion->set($idModelo,$idPedido, null);	
								
								if(!$res['success']) 
									throw new BadRequestException('No puedo agregarse la etiqueta del producto a la cola de impresión.');
							}
						
						}

					
						$query = $this->con->query($sql);
						
						if(@PEAR::isError($query))
							throw new BadRequestException('Hubo un error en la modificación del pedido.');
			
					}//while modelos
					
					
					// PAGOS realizado 
					foreach($pagos as $field => $value) {
					
					
						if(!isset($value['id'])){
						
							// Nuevo pago para el Pedido
							$monto = $value['monto'];
							$created = isset($value['created'])?$value['created']: date('dd/mm/yyyy');
							$FP = $value['FP'];
							
							$sql = "INSERT INTO pedidos_pagos (pedidos_id,monto,FP,created) VALUES ($idPedido,$monto,'$FP','$created') "; 
							$query = $this->con->query($sql);
							
							if(@PEAR::isError($query))
								throw new BadRequestException('Hubo un error al agregar los pagos del pedido.');
								
								
						}
					
						
			
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
	
	
	/**
	 * REMOVEPAGO 
	 * Quita un pago del pedido
	 * @param $idPagoPedido
	 */
	function removePago($idPagoPedido){
		
		try{
			
			$this->beginTransaction();
			
			$sql = "DELETE FROM pedidos_pagos WHERE id = $idPagoPedido";

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