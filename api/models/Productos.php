<?php
class Productos extends AppModel {
	
	public $name = "Productos";
	public $primaryKey = 'id';	
	
	public $hasMany = array('Modelos', 'MovimientosStock', 'ColaImpresion'); 


    
	/**
	 * Retorna todos los productos
	 */
	function getProductos($opciones) {
		
		$conditions = (isset($opciones['conditions']))? $this->_buildConditions($opciones['conditions']): "";	
		
		$sql = "SELECT P.precio,P.nombre as nomProducto, P.id as producto_id, P.enProduccion as enProduccion, M.nombre as nomModelo, M.stock, M.id as modelo_id, Rep.ultRep as fechaRep, Venta.ultVenta as fechaVenta
				FROM productos P
				INNER JOIN modelos M ON (P.id = M.productos_id)
				LEFT JOIN (
					SELECT MovS.modelos_id, MAX(created) as ultRep
					FROM movimientos_stock MovS
					WHERE (tipo= 'Reposicion') || (tipo= 'Nuevo')
					GROUP BY modelos_id) Rep ON Rep.modelos_id = M.id				
				LEFT JOIN (
					SELECT MovS.modelos_id, MAX(created) as ultVenta
					FROM movimientos_stock MovS
					WHERE tipo= 'venta'
					GROUP BY modelos_id) Venta ON Venta.modelos_id = M.id	
				$conditions & (M.baja = 0)
				ORDER BY P.id";

				
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
		
		//Se formatea el resultado para que quede un arreglo de productos, cada uno con su arreglo de modelos.
		$results = $query->fetchAll();
		$i=0;
		$resultsFormat = array();
		$iF = 0;
		$dir = 'img/productos/';
		while($i < count($results)){
			$resultsFormat[$iF]['nombre'] = utf8_encode($results[$i]['nomProducto']);
			$resultsFormat[$iF]['precio'] = $results[$i]['precio'];
			$resultsFormat[$iF]['id'] = $results[$i]['producto_id'];
			$resultsFormat[$iF]['img'] = file_exists('../img/productos/'.$results[$i]['producto_id'].'.jpg')?$dir.$results[$i]['producto_id'].'.jpg': $dir.'noimg.jpg';
			$resultsFormat[$iF]['enProduccion'] = $results[$i]['enProduccion'];
			$resultsFormat[$iF]['reponer'] = 0; //Si mientras se recorren los modelos alguno no tiene stock se cambia reponer a 1.
			
			$resultsFormat[$iF]['modelos'] = array();
			$m = 0;
			while(($i < count($results))&&($resultsFormat[$iF]['nombre'] == utf8_encode($results[$i]['nomProducto']))){
				$resultsFormat[$iF]['modelos'][$m]['id'] = $results[$i]['modelo_id'];
				$resultsFormat[$iF]['modelos'][$m]['fechaRep'] = $results[$i]['fechaRep'];
				$resultsFormat[$iF]['modelos'][$m]['fechaVenta'] = $results[$i]['fechaVenta'];
				$resultsFormat[$iF]['modelos'][$m]['nombre'] = utf8_encode($results[$i]['nomModelo']);
				if($results[$i]['stock'] == 0) $resultsFormat[$iF]['reponer'] = 1;
				$resultsFormat[$iF]['modelos'][$m++]['stock'] = $results[$i++]['stock'];		
			}
			$iF++;
		}
		
			
		
		return $resultsFormat;
	}
	
	
	
	
	/**
	 * Retorna los nombres de los productos
	 */
	function getProductosNames($opciones='') {
		
		$conditions = (isset($opciones['conditions']))? $this->_buildConditions($opciones['conditions']): "";
		

		$sql = "SELECT P.nombre nombre_prod, P.precio, M.id as id, M.nombre as nombre_mod
				FROM productos P
				INNER JOIN modelos M ON (M.productos_id = P.id) 
				$conditions & (M.baja = 0) 
				ORDER BY nombre_prod, nombre_mod ASC";

	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
		$results = $query->fetchAll();
		
		for($i=0; $i < count($results); $i++){
			$results[$i]['nombre']= utf8_encode($results[$i]['nombre_prod']).' - '.utf8_encode($results[$i]['nombre_mod']);
			unset($results[$i]['nombre_prod']); unset($results[$i]['nombre_mod']);
			$results[$i]['id']= $results[$i]['id'];
		}	
		return $results;
	}
	
	
	
	
	
	
	/**
	 * Retorna el producto que coincide con el id
	 * @param $idProducto
	 */
	function getProductoPorId($idProducto) {
		
		$sql = "SELECT P.precio,P.nombre as nomProducto, P.id as producto_id, P.enProduccion as enProduccion, M.nombre as nomModelo, M.stock, M.id as modelo_id, Rep.ultRep as fechaRep, Venta.ultVenta as fechaVenta
				FROM productos P
				INNER JOIN modelos M ON (P.id = M.productos_id) & (M.baja = 0) 
				LEFT JOIN (
					SELECT MovS.modelos_id, MAX(created) as ultRep
					FROM movimientos_stock MovS
					WHERE (tipo= 'Reposicion') || (tipo= 'Nuevo')
					GROUP BY modelos_id) Rep ON Rep.modelos_id = M.id				
				LEFT JOIN (
					SELECT MovS.modelos_id, MAX(created) as ultVenta
					FROM movimientos_stock MovS
					WHERE tipo= 'venta'
					GROUP BY modelos_id) Venta ON Venta.modelos_id = M.id	
				
				WHERE (M.baja = 0) & (P.id = ?)
				ORDER BY M.nombre";
				
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idProducto));
		
		//Se formatea el resultado para que queden los datos del producto con su arreglo de modelos.
		$results = $query->fetchAll();
		$resultsFormat = array();
		
		if(!empty($results)){
			$dir = 'img/productos/';
			
			$resultsFormat['nombre'] = utf8_encode($results[0]['nomProducto']);
			$resultsFormat['precio'] = $results[0]['precio'];
			$resultsFormat['id'] = $results[0]['producto_id'];
			$resultsFormat['img'] = file_exists('../img/productos/'.$results[0]['producto_id'].'.jpg')?$dir.$results[0]['producto_id'].'.jpg': $dir.'noimg.jpg';
			$resultsFormat['enProduccion'] = $results[0]['enProduccion'];
			$resultsFormat['reponer'] = 0; //Si mientras se recorren los modelos alguno no tiene stock se cambia reponer a 1.
				
			$resultsFormat['modelos'] = array();
			$m = 0;
			while($m < count($results)){
				$resultsFormat['modelos'][$m]['id'] = $results[$m]['modelo_id'];
				$resultsFormat['modelos'][$m]['fechaRep'] = $results[$m]['fechaRep'];
				$resultsFormat['modelos'][$m]['fechaVenta'] = $results[$m]['fechaVenta'];
				$resultsFormat['modelos'][$m]['nombre'] = utf8_encode($results[$m]['nomModelo']);
				if($results[$m]['stock'] == 0) $resultsFormat['reponer'] = 1;
				$resultsFormat['modelos'][$m]['stock'] = $results[$m]['stock'];
				$m++;		
			}
		}	
		return $resultsFormat;
		
		
	}
	
	
	
	
	
	/**
	 * Retorna el producto-modelo que coincide con el id
	 * @param $idModelo
	 */
	function getProductoModeloPorId($idModelo) {
		
		$sql = "SELECT P.precio,P.nombre as nomProducto, M.nombre as nomModelo, M.stock as stock 
				FROM modelos M
				INNER JOIN productos P ON (P.id = M.productos_id) 
				WHERE (M.baja = 0)  & (M.id = ?)";
		
		try{
				
	    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
			$query = $query->execute(array($idModelo));
			
			//Se formatea el resultado para que queden los datos del producto con su arreglo de modelos.
			$results = $query->fetchAll();
			$resultsFormat = array();
			
			if(!empty($results)){
				
				$resultsFormat['nombre'] = utf8_encode($results[0]['nomProducto'].'-'.$results[0]['nomModelo']);
				$resultsFormat['precio'] = $results[0]['precio'];
				$resultsFormat['id'] = $idModelo;
				$resultsFormat['stock'] = $results[0]['stock'];
				
				return array('success'=>true, 'producto'=>$resultsFormat);
				
			}else
				throw new BadRequestException('No existe producto con id '.$idModelo.'.');
			
				
			
		}catch(Exception $e){
			return array('success'=>false,'msg'=>$e->getMsg());
		}	
		
	}
	
	
	
	
	
	
	
	
	
	/**
	* SETPRODUCTO
	* $producto = array( ['id'=>''], 'nombre'=>'', 'precio'=>'' )
	* $modelos  = array( 	
	* 					array(['id'], 'nombre', 'stock')
	* 
	* 
	* Este módulo no carga stock. Si se crea un modelo de producto se crea con stock en 0, por lo tanto no se manda a imprimir.
	* Nota: Stock solo se actualiza desde reponer/vender/baja. (Se debe registrar el movimiento por cada reposición.)
	*/
	function setProducto($producto, $modelos){
		
		try{
			$this->beginTransaction();
			
			if(!isset($producto['id'])){
			
				//Creo el producto
				$producto['nombre'] = utf8_decode($producto['nombre']);
				if(!isset($producto['enProduccion'])) $producto['enProduccion'] = 1;
				$producto['created'] = date('Y/m/d h:i:s', time());		
	
				if($this->create($producto)) {
					
					//Agrego los modelos
					$idProducto = $this->getLastId(); 
					
					// varios modelos
					if (!empty($modelos)){ 
					
						foreach($modelos as $field => $value) {
						
							$value['productos_id'] = $idProducto;
							$value['stock'] = (isset($value['stock']))?$value['stock']:0;
							$value['created'] = date('Y/m/d', time());
							$value['nombre'] = utf8_decode($value['nombre']);
							unset($value['fechaVenta']); unset($value['fechaRep']);unset($value['$$hashKey']);
						
							if(!$this->Modelos->create($value) )
								throw new BadRequestException('Hubo un error al crear el modelo.');
	
/*
							//Solo creo el movimiento si hay stock	
							if($value['stock'] > 0){
								// Agrego un movimiento
								// tipo de movimiento:  'Reposicion','produccion','venta','baja'
								$movimiento = array(
										'modelos_id'=> $this->Modelos->getLastId(), 
										'created'=> date('Y/m/d h:i:s', time()), 
										'tipo'=> 'Reposicion', 
										'cantidad'=> $value['stock']);
		
								if(!$this->MovimientosStock->setMovimiento($movimiento))
									throw new BadRequestException('Hubo un error al crear el movimiento.');
								
							}	
*/	
						}
					
					}else{
					//Modelo único
					
						$modUnico = array('productos_id' => $idProducto, 'created'=> date('Y/m/d h:i:s', time()), 'nombre' => utf8_decode("Único"));
							
						if(!$this->Modelos->create($modUnico) )
							throw new BadRequestException('Hubo un error al crear el modelo.');
									

					}
					
				}else
					throw new BadRequestException('Hubo un error al crear el productos.');
							
			}else{
				
				$idProducto = $producto['id'];
				$producto['nombre'] = utf8_decode($producto['nombre']);
				
				if($this->update($producto, array('id'=>$producto['id']))){
				
					foreach($modelos as $field => $value)	{
					
						$value['nombre'] = utf8_decode($value['nombre']);

						if(!isset($value['id'])){
						
							// Modelo nuevo para el producto
						
							$value['productos_id'] = $idProducto;
							$value['stock'] = (isset($value['stock']))?$value['stock']:0;
							$value['created'] = date('Y/m/d', time());
							unset($value['fechaVenta']); unset($value['fechaRep']);unset($value['$$hashKey']);
								
							if(!$this->Modelos->create($value))
								throw new BadRequestException('Hubo un error al crear el modelo.');
								
							/*
							//Solo creo el movimiento y mando a imprimir si hay stock	
							if($value['stock'] > 0){
								
								$movimiento = array(
											'modelos_id'=> $this->Modelos->getLastId(), 
											'created'=> date('Y/m/d h:i:s', time()), 
											'tipo'=> 'Reposicion', 
											'cantidad'=> $value['stock']);
											
								if(!$this->MovimientosStock->setMovimiento($movimiento))
										throw new BadRequestException('Hubo un error al crear el movimiento.');	
							}	
							*/		
									
						}else{ 
						
							// OJO! Aquí SOLO se modifica el nombre del modelo. 
							// Si se incrementó la cantidad de stock NO se guarda acá la modificación.
							/*  La modificación en el stock de un modelo del producto se realiza con la function reponer(). 
								La invoca el controller de producto por cada reposición hecha. */
								
							if(!$this->Modelos->update(array('nombre'=>$value['nombre']), array('id'=>$value['id'])))
								throw new BadRequestException('Hubo un error al modificar el modelo.');
						}
						
					}
				}else
					throw new BadRequestException('Hubo un error al actualizar el producto.');				
			}
			
			$this->commitTransaction();
			
			
			return array('success'=>true, 'productos_id'=>$idProducto);	

		} catch (Exception $e) {
			$this->rollbackTransaction();
			return array('success'=>false, 'msg'=>$e->getMsg());	
		
		}
	}





	/**
	 * DELPRODUCTO 
	 * Elimina el producto que coincide con el id
	 * @param $idProducto
	 */
	function delProducto($idProducto){
		
		try{
			$this->beginTransaction();
						
			if($this->Modelos->notUsedPorProducto($idProducto)){

				if (!$this->delete($idProducto))				
					throw new BadRequestException('Hubo un error. No se pudo eliminar el producto.');	
				
			}else 
				throw new BadRequestException('Alguno de los modelos de este producto está en un pedido o producción');				
			
			$this->commitTransaction();
			
		} catch (Exception $e) {
			echo $e->getMsg();
			$this->rollbackTransaction();
		}
		
	}
	
	
	
	/**
	* REPONER
	* Incrementa en 1 el stock del modelo
	*/
	function reponer($idMod){
		
		try{
		
			$this->beginTransaction();
			
			$res = $this->Modelos->reponer($idMod); // Incrementa el stcok del modelo y marca el movimiento de stock
			
			if($res['success'])
				$res = $this->ColaImpresion->set($idMod); 
			else
				throw new BadRequestException($res['msg']);
				
			if($res['success'])
				$this->commitTransaction();
			else
				throw new BadRequestException($res['msg']);
			
			return array('success'=>true, 'modelos_id'=>$idMod);	
					
		} catch (Exception $e) {
			$this->rollbackTransaction();
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
		
	}
	
	
	/**
	* BAJA
	* Decrementa en 1 el stock del modelo
	*/
	function baja($idMod, $nota=''){
		
		
			$this->beginTransaction();
			
			$res = $this->Modelos->baja($idMod,$nota);
			
			if($res['success'])
				$this->commitTransaction();
			else
				$this->rollbackTransaction();
		
	}
	

	
}
?>