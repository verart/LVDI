<?php
class Ventas extends AppModel {
	
	public $name = "Ventas";
	public $primaryKey = 'id';	
	
	
	public $hasMany = array('Modelos', 'MovimientosStock'); 
	
	
	
	/**
	 * Retorna todos las ventas
	 * params (array) $opciones = array([conditions])
	 */
	function getVentas($opciones = array(), $requested_page = 1) {
	
	
	 	$set_limit = (($requested_page - 1) * 30) . ",30";
	
		$conditions = (isset($opciones['conditions']))? $this->_buildConditions($opciones['conditions']): "";	
		
		//traigo la pagina correspondiente de ventas
		$sql = "SELECT V.*, FPV.FP as FP2, Pr.nombre as producto, Pr.precio, M.id as modelos_id, M.nombre as modelo, VM.cantidad, VM.id as idVenMod, totalDevoluciones  
				FROM ventas V 
				INNER JOIN ventas_modelos VM ON VM.ventas_id = V.id 
				INNER JOIN modelos M ON VM.modelos_id = M.id 
				INNER JOIN productos Pr ON Pr.id = M.productos_id  
				LEFT JOIN (
							SELECT ventas_id,GROUP_CONCAT(DISTINCT SUBSTRING(FP,1,2) SEPARATOR '/') as FP
							from ventas_pagos VP
							GROUP BY ventas_id 
				) as FPV ON FPV.ventas_id = V.id 
				LEFT JOIN (
							SELECT ventas_id, SUM(precio) as totalDevoluciones
							from ventas_devoluciones VD 
							GROUP BY ventas_id 
				) as VDV ON VDV.ventas_id = V.id 
				$conditions 
				ORDER BY V.created DESC, V.id DESC  
				LIMIT $set_limit "; 
			
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
	   	
		$results = $query->fetchAll();

		$iF = 0;
		$i = 0;
		$resultsFormat = array(); 
		//Proceso las ventas 
		while($i < count($results)){
			$resultsFormat[$iF]['id'] = $results[$i]['id'];
			$resultsFormat[$iF]['created'] = $results[$i]['created'];
			$resultsFormat[$iF]['total'] = $results[$i]['total'];
			$resultsFormat[$iF]['deuda'] = $results[$i]['deuda'];
			$resultsFormat[$iF]['montoFavor'] = $results[$i]['montoFavor'];
			$resultsFormat[$iF]['bonificacion'] = $results[$i]['bonificacion'];
			$resultsFormat[$iF]['nota'] = $results[$i]['nota'];
			$resultsFormat[$iF]['FP'] = ($results[$i]['FP'] != '')? $results[$i]['FP'] :$results[$i]['FP2']; 
			$resultsFormat[$iF]['totalDevoluciones'] = ($results[$i]['totalDevoluciones']!=null)?$results[$i]['totalDevoluciones']:0;
			
			//Modelos de la venta
			$resultsFormat[$iF]['modelos'] = array();
			$m = 0;
			while(($i < count($results))&&($resultsFormat[$iF]['id'] == $results[$i]['id'])){
				$resultsFormat[$iF]['modelos'][$m]['id'] = $results[$i]['modelos_id'];
				$resultsFormat[$iF]['modelos'][$m]['idVenMod'] = $results[$i]['idVenMod'];
				$resultsFormat[$iF]['modelos'][$m]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
				$resultsFormat[$iF]['modelos'][$m]['cantidad'] = $results[$i]['cantidad'];
				$resultsFormat[$iF]['modelos'][$m++]['precio'] = $results[$i++]['precio'];						
			}
			$iF++;
		}
		return $resultsFormat;
	}
	
	
	
	
	/**
	 * Retorna todos los pagos de la venta
	 * params (int) 
	 */
	function getPagos($idVenta) {

		$sql = "SELECT *
				FROM ventas_pagos VP 
				WHERE VP.ventas_id = ? 
				ORDER BY VP.created DESC"; 
				
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idVenta));
		$results = $query->fetchAll();		
		
		return $results;
	}
	
	
	/**
	 * Retorna todos las devoluciones de la venta
	 * params (int) 
	 */
	function getDevoluciones($idVenta) {

		$sql = "SELECT *, VD.id as idDevMod, VD.modelos_id as id, P.nombre as nomProd, M.nombre as nomMod   
				FROM ventas_devoluciones VD 
				INNER JOIN modelos M ON M.id=VD.modelos_id 
				INNER JOIN productos P ON P.id=M.productos_id 
				WHERE VD.ventas_id = ? 
				ORDER BY VD.id ASC"; 
				
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idVenta));
		$results = $query->fetchAll();	

		$m = 0;
		$resultsFormat = array();
		while($m < count($results)){
			$resultsFormat[$m]['id'] = $results[$m]['id'];
			$resultsFormat[$m]['precio'] = $results[$m]['precio'];
			$resultsFormat[$m]['nombre'] = utf8_encode($results[$m]['nomProd']).'-'.utf8_encode($results[$m]['nomMod']);
			$resultsFormat[$m]['idDevMod'] = $results[$m]['idDevMod'];
			$resultsFormat[$m]['created'] = $results[$m]['created'];
			$m++;
		}

		return $resultsFormat;
	}

	
	/**
	 * Retorna la venta que coincide con el id
	 * @param $idVenta
	 */
	function getVentaPorId($idVenta) {
		
				
		$sql = "SELECT V.*, FPV.FP as FP2, Pr.nombre as producto, VM.precio, M.id as modelos_id, M.nombre as modelo, VM.cantidad, 
		 		VM.id as idVenMod, totalDevoluciones 
				FROM ventas V
				INNER JOIN ventas_modelos VM ON VM.ventas_id = V.id
				INNER JOIN modelos M ON VM.modelos_id = M.id
				INNER JOIN productos Pr ON Pr.id = M.productos_id 				
				LEFT JOIN (
							SELECT ventas_id,GROUP_CONCAT(DISTINCT SUBSTRING(FP,1,2) SEPARATOR '/') as FP
							from ventas_pagos VP
							GROUP BY ventas_id 
				) as FPV ON FPV.ventas_id = V.id 
				LEFT JOIN (
							SELECT ventas_id, SUM(precio) as totalDevoluciones
							from ventas_devoluciones VD 
							GROUP BY ventas_id 
				) as VDV ON VDV.ventas_id = V.id 
				WHERE V.id = ?";
				
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idVenta));
		$results = $query->fetchAll();
		
		$i = 0;
		$resultsFormat = array();
		
		$resultsFormat['id'] = $results[$i]['id'];
		$resultsFormat['created'] = $results[$i]['created'];
		$resultsFormat['total'] = $results[$i]['total'];
		$resultsFormat['montoFavor'] = $results[$i]['montoFavor'];
		$resultsFormat['bonificacion'] = $results[$i]['bonificacion'];
		$resultsFormat['deuda'] = $results[$i]['deuda'];
		$resultsFormat['FP'] = ($results[$i]['FP'] != null)? $results[$i]['FP'] :$results[$i]['FP2']; 
		$resultsFormat['nota'] = $results[$i]['nota'];
		$resultsFormat['totalDevoluciones'] = ($results[$i]['totalDevoluciones']!=null)?$results[$i]['totalDevoluciones']:0;
				
		$resultsFormat['modelos'] = array();
		$m = 0;
		while($m < count($results)){
				$resultsFormat['modelos'][$m]['id'] = $results[$i]['modelos_id'];
				$resultsFormat['modelos'][$m]['idVenMod'] = $results[$i]['idVenMod'];
				$resultsFormat['modelos'][$m]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
				$resultsFormat['modelos'][$m]['cantidad'] = $results[$i]['cantidad'];
				$resultsFormat['modelos'][$m++]['precio'] = $results[$i++]['precio'];						
		}
		
		return $resultsFormat;
	}
	
	
	
	
	
	
	/**
	* SETVENTA
	* $venta = array( ['id'], 'bonificacion', 'created',['FP'], total, ['montoFavor'], ['nota'] )
	* $modelos  = array(  array('id', precio) )
	* $pagos  = array( 	array(monto, bonificacion, FP) )
	* $dev  = array(id)
	* $mod2delete, $pagos2delete, $dev2delete 
	*/
	function setVenta($venta, $modelos, $pagos, $dev, $mod2delete=array(), $pagos2delete=array(), $dev2delete=array()){
		
		try{
			$this->beginTransaction();
				
			if(!isset($venta['id'])){ 
			
			
				// NUEVA VENTA
				if($this->create($venta)) {
					
					//Agrego los modelos
					$idVenta = $this->con->lastInsertID('ventas', 'id');
					foreach($modelos as $field => $value) {		
						$idModelo = $value['id'];
						$precio = $value['precio'];
						
						//Decremento el stock del modelo agregado a la venta
						$res = $this->Modelos->baja($idModelo, 1,'','venta');
						if(!$res['success'])
							throw new BadRequestException($res['msg']);
							
						$sql = "INSERT INTO ventas_modelos (ventas_id,modelos_id,cantidad,precio) VALUES ($idVenta, $idModelo,1, $precio) ";
						
						$query = $this->con->query($sql);
						
						if(@PEAR::isError($query))
							throw new BadRequestException('Hubo un error al agregar los modelos a la venta.');
					}
					
					//Pagos realizado 
					foreach($pagos as $field => $value) {
						$monto = $value['monto'];
						$created = isset($value['created'])?$value['created']: date('d/m/Y');
						$FP = $value['FP'];
						$bonif = $value['bonificacion'];
						
						$sql = "INSERT INTO ventas_pagos (ventas_id,monto,FP,created, bonificacion) VALUES ($idVenta,$monto,'$FP','$created',$bonif) "; 
						$query = $this->con->query($sql);
						
						if(@PEAR::isError($query))
							throw new BadRequestException('Hubo un error al agregar los pagos de la venta.');
					}
			
					//Devoluciones 
					foreach($dev as $field => $value) {
						$idModelo = $value['id'];
						$created = isset($value['created'])?$value['created']: date('d/m/Y');
						$precio = $value['precio'];
						
						//Repone stock	
						$res = $this->Modelos->reponer($idModelo,1,'Devolucion');
						if(!$res['success'])
							throw new BadRequestException($res['msg']);	

						$sql = "INSERT INTO ventas_devoluciones (ventas_id,modelos_id,created,precio) VALUES ($idVenta,$idModelo,'$created',$precio) "; 
						$query = $this->con->query($sql);
						
						if(@PEAR::isError($query))
							throw new BadRequestException('Hubo un error al agregar las devoluciones de la venta.');
					}
				}else
					throw new BadRequestException('Hubo un error al crear la venta');
					
							
			}else{
				$idVenta= $venta['id'];
				
				if($this->update($venta, array('id'=>$venta['id']))){
					//modelos
					foreach($modelos as $field => $value) {
						
						if(!isset($value['idVenMod'])){
						
							$idModelo = $value['id'];
							$precio = $value['precio'];
							
							//Decremento el stock del modelo agregado a la venta
							$res = $this->Modelos->baja($idModelo, 1,'','venta');
							if(!$res['success'])
								throw new BadRequestException($res['msg']);	
							
							$sql = "INSERT INTO ventas_modelos (ventas_id,modelos_id,cantidad,precio) VALUES ($idVenta, $idModelo,1, $precio) ";
							
							$query = $this->con->query($sql);
							
							if(@PEAR::isError($query))
								throw new BadRequestException('Hubo un error al agregar los modelos a la venta.');
						}
					}
					
					
					//Pagos realizado 
					foreach($pagos as $field => $value) {
					
						if(!isset($value['id'])){
	
							$monto = $value['monto'];
							$created = isset($value['created'])?$value['created']: date('d/m/Y');
							$FP = $value['FP'];
							$bonif = $value['bonificacion'];
								
							$sql = "INSERT INTO ventas_pagos (ventas_id,monto,FP,created, bonificacion) VALUES ($idVenta,$monto,'$FP','$created',$bonif) ";
							$query = $this->con->query($sql);
								
							if(@PEAR::isError($query))
								throw new BadRequestException('Hubo un error al agregar los pagos de la venta.');
						}
					}

					//Devoluciones 
					foreach($dev as $field => $value) {

						if(!isset($value['idDevMod'])){
						
							$idModelo = $value['id'];
							$created = isset($value['created'])?$value['created']: date('d/m/Y');
							$precio = $value['precio'];
							
							//Repone stock	
							$res = $this->Modelos->reponer($idModelo,1,'Devolucion');
						    if(!$res['success'])
								throw new BadRequestException($res['msg']);	

							$sql = "INSERT INTO ventas_devoluciones (ventas_id,modelos_id,created,precio) VALUES ($idVenta,$idModelo,'$created',$precio) "; 
							$query = $this->con->query($sql);
							
							if(@PEAR::isError($query))
								throw new BadRequestException('Hubo un error al agregar las devoluciones de la venta.');
						}
					}
				}		
			}
			
			
			// DELETE de modelos de la venta
			if(!empty($mod2delete))
				foreach($mod2delete as $field => $value){
					$result = $this->removeModelo($value['id']);
					
					if(!$result['success'])
						throw new BadRequestException($result['msg']);									
				}
			
			
			// DELETE de pagos de la venta
			if(!empty($pagos2delete))
				foreach($pagos2delete as $field => $value){
					$result = $this->removePago($value['id']);
					
					if(!$result['success'])
						throw new BadRequestException($result['msg']);									
				}

			// DELETE de devoluciones de la venta
			if(!empty($dev2delete)){
				foreach($dev2delete as $field => $value){
					$result = $this->removeDevolucion($value['idDevMod']);
					
					if(!$result['success'])
						throw new BadRequestException($result['msg']);									
				}
			}	
			$this->commitTransaction();
			
			$venta = $this->getVentaPorId($idVenta);

			return array('success'=>true, 'venta'=>$venta);
			
		} catch (Exception $e) {
			
			$this->rollbackTransaction();
			
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
		
		
	}
	

		
	
	
	function eliminarVenta($idVenta){
	
		try{
			
			$this->beginTransaction();

			$prod = $this->getVentaPorId($idVenta);
			foreach($prod['modelos'] as $field => $value){
				$result = $this->removeModelo($value['idVenMod']);
				if(!$result['success'])
					throw new BadRequestException($result['msg']);	
			}

			$devoluciones = $this->getDevoluciones($idVenta);
			foreach($devoluciones as $field => $value){
				$result = $this->removeDevolucion($value['idDevMod']);
				if(!$result['success'])
					throw new BadRequestException($result['msg']);	
			}
		
			$this->delete($idVenta);
			
			$this->commitTransaction();
			
		}catch (Exception $e) {
			$this->rollbackTransaction();
			
			return array('success'=>false, 'msg'=>$e->getMsg());

		}
	
	}
	
	
	
	function removeModelo($idVenMod){
	
		try{
			//Recupero el modelo
			$sql = "SELECT modelos_id FROM ventas_modelos WHERE id = $idVenMod";
			$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);
			$query = $query->execute();
			$result = $query->fetchAll();		
			$idModelo = $result[0]['modelos_id'];
			
			//Repone stock	
			$res = $this->Modelos->reponer($idModelo,1,'');
		    if(!$res['success'])
				throw new BadRequestException($res['msg']);	
		
			$sql = "DELETE FROM ventas_modelos WHERE id = $idVenMod";

			$result = $this->con->query($sql);
		
			if(@PEAR::isError($result))
		    	throw new BadRequestException('Ocurrió un error al quitar un producto de la venta.');				
		    
			return array('success'=>true, 'msg'=>'El modelo fue quitado de la venta.');
		
		}catch (Exception $e) {			
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
	
	}
	
	
	
	/*
	* REMOVEPAGO
	* Solo elimina un pago y NO actualiza la deuda de la venta
	*/
	function removePago($idPago){
	
		try{
		
		    $pago = $this->getPagoPorId($idPago);
		    $sql = "DELETE FROM ventas_pagos WHERE id = $idPago";
			$result = $this->con->query($sql);
			if(@PEAR::isError($result)) 
		    	throw new BadRequestException('Ocurrió un error al eliminar el pago.');				
		    
			return array('success'=>true, 'msg'=>'El pago fue quitado de la venta');
		
		}catch (Exception $e) {			
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
	}
	

	function removeDevolucion($idDevMod){
	
		try{
			//Recupero el modelo
			$sql = "SELECT modelos_id FROM ventas_devoluciones WHERE id = $idDevMod";
			$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);
			$query = $query->execute();
			$result = $query->fetchAll();		
			$idModelo = $result[0]['modelos_id']; 
			
			//Decrementa stock	
			$res = $this->Modelos->baja($idModelo,1,'Devolucion');
		    if(!$res['success'])
				throw new BadRequestException($res['msg']);	
		
			$sql = "DELETE FROM ventas_devoluciones WHERE id = $idDevMod";
			$result = $this->con->query($sql);
		
			if(@PEAR::isError($result)) 
		    	throw new BadRequestException('Ocurrió un error al quitar la devolución de la venta.');				
		    
			return array('success'=>true, 'msg'=>'La devolución fue quitada de la venta.');
		
		}catch (Exception $e) {			
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
	
	}




	
	/**
	 * GETPAGOPORID
	 * Retorna el pago que coincide con el id
	 * @param $idPago
	 */
	function getPagoPorId($idPago) {
		$sql = "SELECT VP.* 
				FROM ventas_pagos VP 
				WHERE VP.id = ?";
				
		$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idPago));
		$results = $query->fetchRow();
		return $results;
	}
	

	/**
	 * ADDNOTA
	 * guarda la nota $nota en la venta $idVenta
	 * @param (string) $nota, (int) $idVenta
	 */
	function addNota($nota, $idVenta){					
					
		try{						
			$sql = "UPDATE ventas SET nota='$nota' WHERE ventas.id = $idVenta "; 
			$query = $this->con->query($sql);
			
			if(@PEAR::isError($query))
					throw new BadRequestException('Hubo un error al actualizar la nota.');
			
			return array('success'=>true);
			
		}catch (Exception $e) {			
			return array('success'=>false, 'msg'=>$e->getMsg());
		}	
		
	}
		
}
?>