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
	
	
	 	$set_limit = (($requested_page - 1) * 15) . ",15";
	
	
		$conditions = (isset($opciones['conditions']))? $this->_buildConditions($opciones['conditions']): "";	
		
		//traigo la pagina correspondiente de ventas
		$sql = "SELECT V.*, Pr.nombre as producto, Pr.precio, M.id as modelos_id, M.nombre as modelo, VM.cantidad, VM.id as idVenMod 
				FROM ventas V 
				INNER JOIN ventas_modelos VM ON VM.ventas_id = V.id 
				INNER JOIN modelos M ON VM.modelos_id = M.id 
				INNER JOIN productos Pr ON Pr.id = M.productos_id  
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
			$resultsFormat[$iF]['FP'] = $results[$i]['FP']; 
			
			//Si mientras se recorren los modelos alguno no tiene stock se cambia reponer a 1.
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
	 * Retorna la venta que coincide con el id
	 * @param $idVenta
	 */
	function getVentaPorId($idVenta) {
		
				
		$sql = "SELECT V.*, Pr.nombre as producto, VM.precio, M.id as modelos_id, M.nombre as modelo, VM.cantidad, 
		 		VM.id as idVenMod
				FROM ventas V
				INNER JOIN ventas_modelos VM ON VM.ventas_id = V.id
				INNER JOIN modelos M ON VM.modelos_id = M.id
				INNER JOIN productos Pr ON Pr.id = M.productos_id 
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
		$resultsFormat['FP'] = $results[$i]['FP'];
			
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
	* $venta = array( ['id'], 'bonificacion', 'created',['FP'], total )
	* $modelos  = array( 	
	* 					array('id', precio ) )
	* $pagos  = array( 	
	* 					array(monto, bonificacion, FP ) )	
	*/
	function setVenta($venta, $modelos, $pagos){
		
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
						$created = isset($value['created'])?$value['created']: date('dd/mm/yyyy');
						$FP = $value['FP'];
						$bonif = $value['bonificacion'];
						
						$sql = "INSERT INTO ventas_pagos (ventas_id,monto,FP,created, bonificacion) VALUES ($idVenta,$monto,'$FP','$created',$bonif) "; 
						$query = $this->con->query($sql);
						
						if(@PEAR::isError($query))
							throw new BadRequestException('Hubo un error al agregar los pagos de la venta.');
			
					}
					
				}else
					throw new BadRequestException('Hubo un error al crear la venta');
					
							
			}else{

				//Pagos realizado 
				$idVenta = $venta['id'];
				foreach($pagos as $field => $value) {
					
					$monto = $value['monto'];
					$created = isset($value['created'])?$value['created']: date('dd/mm/yyyy');
					$FP = $value['FP'];
					$bonif = $value['bonificacion'];
						
					$sql = "INSERT INTO ventas_pagos (ventas_id,monto,FP,created, bonificacion) VALUES ($idVenta,$monto,'$FP','$created',$bonif) ";
					$query = $this->con->query($sql);
						
					if(@PEAR::isError($query))
						throw new BadRequestException('Hubo un error al agregar los pagos de la venta.');
			
				}
			}
			$this->commitTransaction();
			
			return array('success'=>true, 'ventas_id'=>$idVenta);
			
		} catch (Exception $e) {
			
			$this->rollbackTransaction();
			
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
		
		
	}
	

		
	
	
	function eliminarVenta($idVenta){
	
		try{
		
			$prod = $this->getVentaPorId($idVenta);

			foreach($prod['modelos'] as $field => $value)
				$result = $this->removeModelo($value['idVenMod'],$value['id']);
		
			$this->delete($idVenta);
		
		}catch (Exception $e) {
			$this->rollbackTransaction();
			
			return array('success'=>false, 'msg'=>$e->getMsg());

		}
	
	}
	
	
	
	function removeModelo($idVenMod, $idModelo){
	
		try{
		
			$res = $this->Modelos->reponer($idModelo,1,'');
		    if(!$res['success'])
					throw new BadRequestException($res['msg']);	
		
			$sql = "DELETE FROM ventas_modelos WHERE id = $idVenMod";

			$result = $this->con->query($sql);
		
			if(@PEAR::isError($result)) {
		    	throw new BadRequestException('Ocurrió un error al retornar un producto.');				
		    }
		    
			return array('success'=>true, 'msg'=>'');
		
		}catch (Exception $e) {
			$this->rollbackTransaction();
			
			return array('success'=>false, 'msg'=>$e->getMsg());

		}
	
	}
	
	
	
	
	
	function addPago($pago, $idVenta){					
					
		try{			
			$monto = $pago['monto'];
			$created = isset($pago['created'])?$pago['created']: date('dd/mm/yyyy');
			$FP = $pago['FP'];
			$bonif = isset($pago['bonificacion'])?$pago['bonificacion']:0;
						
			$sql = "INSERT INTO ventas_pagos (ventas_id,monto,FP,created, bonificacion) VALUES ($idVenta,$monto,'$FP','$created',$bonif) "; 
			$query = $this->con->query($sql);
			
			
			if(@PEAR::isError($query))
					throw new BadRequestException('Hubo un error al agregar el pago a la venta.');
			else{
				$idPago = $this->getLastId(); 
				$pago['id'] = $idPago;
			}
			
			$venta = $this->getVentaPorId($idVenta);	
			$nuevaDeuda = $venta['deuda'] - ($pago['monto'] + ($pago['monto']*$pago['bonificacion']/100));
			 
			if(!$this->update(array('deuda'=>$nuevaDeuda), array('id'=> $idVenta)))
				throw new BadRequestException('Hubo un error al agregar el pago a la venta.');
			else{
				$idPago = $this->getLastId(); 
				$pago['id'] = $idPago;
			}
			
			
			return array('success'=>true, 'pago'=>$pago);
			
		
		}catch (Exception $e) {
			$this->rollbackTransaction();
			
			return array('success'=>false, 'msg'=>$e->getMsg());

		}	
		
	}
	
	
	
	
		
	
	function deletePago($idPago){
	
		try{
		
		    $pago = $this->getPagoPorId($idPago);
		    

		    $sql = "DELETE FROM ventas_pagos WHERE id = $idPago";

			$result = $this->con->query($sql);
		
			if(@PEAR::isError($result)) {
		    	throw new BadRequestException('Ocurrió un error al eliminar el pago.');				
		    }
		    
			$venta = $this->getVentaPorId($pago['ventas_id']);	
			$nuevaDeuda = $venta['deuda'] + ($pago['monto'] + ($pago['monto']*$pago['bonificacion']/100));
			 
			if(!$this->update(array('deuda'=>$nuevaDeuda), array('id'=> $idVenta)))
				throw new BadRequestException('Hubo un error al agregar el pago a la venta.');

			
			return array('success'=>true, 'msg'=>'');
		
		}catch (Exception $e) {
			$this->rollbackTransaction();
			
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
	
	
}
?>