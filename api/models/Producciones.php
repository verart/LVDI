<?php
class Producciones extends AppModel {
	
	public $name = "Producciones";
	public $primaryKey = 'id';	
	
	
	public $hasMany = array('Modelos', 'MovimientosStock','ColaImpresion'); 
	
	
	/**
	 * Retorna todos las producciones
	 * params (array) $opciones = array([conditions=>array(campo=>valor)])
	 */
	function getProducciones($opciones = array()) {
	
		$set_limit = " LIMIT ".(($opciones['page'] - 1) * $opciones['pageSize']) . ",".$opciones['pageSize'];
	
		$conditions = (isset($opciones['conditions']))? $this->_buildConditions($opciones['conditions']): "";	
		
		$sql = "SELECT P.*, R.nombre as responsable 
				FROM producciones P 
				INNER JOIN responsables R ON R.id = P.responsables_id
				$conditions
				ORDER BY P.fecha DESC, P.id DESC  
				$set_limit "; 
				
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
		
		$results = $query->fetchAll();

	   	$iF = 0;
		//Proceso los pedidos 
		while($iF < count($results)){ 
			$results[$iF]['responsable'] = utf8_encode($results[$iF]['responsable']);
			
			$iF++;
		}	
		
		return $results;

	}
	
	
	
	
	
	/**
	 * Retorna la produccion que coincide con el id
	 * @param $idProduccion
	 */
	function getProduccionPorId($idProduccion) {
		
				
		$sql = "SELECT P.*, R.nombre as responsable, Pr.nombre as producto, Pr.precio as precio, 
				M.id as modelos_id, M.nombre as modelo,PM.estado as estadoProducto, 
				PM.id as idProdMod 
				FROM producciones P
				INNER JOIN responsables R ON R.id = P.responsables_id
				INNER JOIN producciones_modelos PM ON P.id = PM.producciones_id
				INNER JOIN modelos M ON M.id = PM.modelos_id
				INNER JOIN productos Pr ON Pr.id = M.productos_id	
				WHERE P.id = ?
				ORDER BY Pr.nombre, M.nombre";
				
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idProduccion));
		$results = $query->fetchAll();
		
		$i = 0;
		$resultsFormat = array();
		//Proceso los pedidos 
		while($i < count($results)){
			$resultsFormat['id'] = $results[$i]['id'];
			$resultsFormat['fecha'] = $results[$i]['fecha'];
			$resultsFormat['fecha_devolucion'] = $results[$i]['fecha_devolucion'];
			$resultsFormat['responsables_id'] = $results[$i]['responsables_id']; 
			$resultsFormat['responsable'] = utf8_encode($results[$i]['responsable']);
			$resultsFormat['estado'] = $results[$i]['estado']; 
			$resultsFormat['nota'] = $results[$i]['nota']; 
			$resultsFormat['motivo'] = $results[$i]['motivo'];
			
			//Si mientras se recorren los modelos alguno no tiene stock se cambia reponer a 1.
			$resultsFormat['modelos'] = array();
			while($i < count($results)){
				$resultsFormat['modelos'][$i]['id'] = $results[$i]['modelos_id'];
				$resultsFormat['modelos'][$i]['idProdMod'] = $results[$i]['idProdMod'];
				$resultsFormat['modelos'][$i]['precio'] = $results[$i]['precio'];
				$resultsFormat['modelos'][$i]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
				$resultsFormat['modelos'][$i]['estado'] = $results[$i]['estadoProducto'];	
				$i++;			
			}
		}
		
		
		return $resultsFormat;	
	}
	
	
	
	
	
	
	
	
	/**
	 * Retorna un modelo de una produccion que coincide con el id de relacion
	 * @param $idModeloProduccion
	 */
	function getModeloEnProduccionPorId($idModeloProduccion) {
		
				
		$sql = "SELECT * 
				FROM producciones_modelos PM 
				WHERE PM.id = ?";
				
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idModeloProduccion));
		$results = $query->fetchAll();
		
		return $results[0];	
	}
	
	





	/**
	 * Retorna todos los modelos del pedido
	 * params (int) 
	 */
	function getModelos($idProduccion) {
	
				
		$sql = "SELECT Pr.nombre as producto, Pr.precio, M.id as modelos_id, M.nombre as modelo,  
		 		PM.estado, PM.id as idProdMod 
				FROM producciones P
				INNER JOIN responsables R ON R.id = P.responsables_id
				INNER JOIN producciones_modelos PM ON P.id = PM.producciones_id
				INNER JOIN modelos M ON M.id = PM.modelos_id
				INNER JOIN productos Pr ON Pr.id = M.productos_id	
				WHERE P.id = ?
				ORDER BY Pr.nombre, M.nombre"; 
				
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idProduccion));
		$results = $query->fetchAll();		
		
		$i = 0;
		$resultsFormat = array();
		while($i < count($results)){
				$resultsFormat[$i]['precio'] = $results[$i]['precio'];
				$resultsFormat[$i]['id'] = $results[$i]['modelos_id'];
				$resultsFormat[$i]['idProdMod'] = $results[$i]['idProdMod'];
				$resultsFormat[$i]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
				$resultsFormat[$i]['estado'] = $results[$i]['estado'];		
				$i++;										
			}
		return $resultsFormat;
	}
	

	
	
	
	
	
	
	/**
	* SETPRODUCCION
	* @param $pedido (array)  ( ['id'], 'responsables_id', 'motivo', 'fecha', 'fecha_devolucion', 'nota', 'estado')
	* @param $modelos (array) ( ['idProdMod'],'id') ) //Si no viene idProdMod es porq es un producto nuevo para la produccion
	*
	* 
	* Los estado pueden ser: 'Retirado','Devuelto'
	*/
	function setProduccion($produccion, $modelos, $mod2delete=array()){
						
		try{
			$this->beginTransaction(); 
		
			if(isset($producto['motivo'])) $producto['motivo'] = utf8_decode($producto['motivo']);
			if(isset($producto['nota'])) $producto['nota'] = utf8_decode($producto['nota']);
			
					
			if(!isset($produccion['id'])){ 
				
				/*********** NUEVA produccion *************************/						
				if($this->create($produccion)) {
					
					//Agrego los modelos
					$idProduccion = $this->con->lastInsertID('producciones', 'id');
					
					foreach($modelos as $field => $value) {
										
						$idModelo = $value['id'];
						
						//Decremento el stock del modelo agregado a la produccion
						$res = $this->Modelos->baja($idModelo, 1,'','BajaProduccion');
						if(!$res['success'])
							throw new BadRequestException($res['msg']);
						
					
						$sql = "INSERT INTO producciones_modelos (producciones_id,modelos_id,estado) 
								VALUES ($idProduccion, $idModelo,  'Retirado') ";
					
								
						$query = $this->con->query($sql);
						
						if(@PEAR::isError($query))
							throw new BadRequestException('Hubo un error al agregar los modelos a la produccion');
							
							
							
			
					}
					
				}else
					throw new BadRequestException('Hubo un error en al creación de la produccion');
							
			}else{
				
				/*********** UPDATE produccion *************************/
				
				$idProduccion =  $produccion['id']; 

				if($this->update($produccion, array('id'=>$produccion['id']))){

					//Recupero el estado de los productos de la produccio
					$produccionBefore = $this->getProduccionPorId($idProduccion);

					foreach($modelos as $field => $value) {
					
						$idModelo = $value['id'];
						$estado = $value['estado']; 
						
						if(!isset($value['idProdMod'])){
							
							//Agrego producto
							
							if($estado == 'Retirado'){
								//Decremento el stock del modelo agregado a la produccion
								$res = $this->Modelos->baja($idModelo, 1,'','BajaProduccion');
								if(!$res['success'])
									throw new BadRequestException($res['msg']);
							}	
							
							// Nuevo modelo para la produccion
							$sql = "INSERT INTO producciones_modelos (producciones_id,modelos_id,estado) 
									VALUES ($idProduccion, $idModelo, '$estado') ";	
						
							
							$query = $this->con->query($sql);
								
							if(@PEAR::isError($query))
								throw new BadRequestException('Hubo un error al agregar modelos a la producción.');
									
									
							
						}else{
							
							// Edicion de un modelo ya cargado a la produccion
								
							$idProdMod = $value['idProdMod'];
							
							//Busco el prducto a actualizar	
							$found = false;  
							foreach($produccionBefore['modelos'] as $index => $value) {
								if ($value['idProdMod'] == $idProdMod) {
					        		$found = true;
					        		break;
					        	}
					        }


					        if (!$found){
					        
						        throw new BadRequestException('Hubo un error al actualizar los productos de la producción.');
					        
					        }else{
						       
						        //Si encontró el prod en la produccion, hace el cambio
						        
						        $estadoBefore = $produccionBefore['modelos'][$index]['estado'];
							
								if(( $estadoBefore == 'Retirado') && ($estado == 'Devuelto')){
									
										//Incremento el stock del modelo agregado a la produccion
										$res = $this->Modelos->reponer($idModelo, 1,'AltaProduccion');
										if(!$res['success'])
											throw new BadRequestException($res['msg']);
											
										//Genero etiqueta en la cola de impresion
										$res = $this->ColaImpresion->set($idModelo,null,$idProduccion,null);
									
										
								}else{
									
									if(( $estadoBefore == 'Devuelto') && ($estado == 'Retirado')){
										
											//Decremento el stock del modelo agregado a la produccion
											$res = $this->Modelos->baja($idModelo, 1,'','BajaProduccion');	
											if(!$res['success'])
												throw new BadRequestException($res['msg']);
									}
										
								}	
								
								$sql = "UPDATE producciones_modelos 
										SET producciones_id = $idProduccion,modelos_id=$idModelo,estado='$estado' 
										WHERE (id = $idProdMod)";
								
								$query = $this->con->query($sql);
								
								if(@PEAR::isError($query))
									throw new BadRequestException('Hubo un error con los modelos de la producción.');
									
							    unset($produccionBefore['modelos'][$index]); 
						        
						        
					        } //else !found
					        
					        
						} //else isset idProdMod 
					
					}//for modelos
					
					
					// DELETE de modelos de la produccion
					if(!empty($mod2delete)){
					
						foreach($mod2delete as $field => $value){
							$result = $this->removeModelo($value['id']);
														
							if(!$result['success'])
								throw new BadRequestException($result['msg']);									
						}
					}
					
					
					
				}else  
					throw new BadRequestException('Hubo un error al actualizar la producción.');				
					
					
			}// else UPDATE

			
			$this->commitTransaction();
			return array('success'=>true, 'producciones_id'=>$idProduccion);
			
			
		} catch (Exception $e) {
			
			$this->rollbackTransaction();			
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
		
		
	}
	
	
	
	/**
	 * REMOVEMODELO 
	 * Quita un modelo de producto de la produccion
	 * @param $idProdModelo
	 */
	function removeModelo($idModeloProduccion){
		
		try{
		
			
			//Recupero el estado del producto de la produccion
			$prod = $this->getModeloEnProduccionPorId($idModeloProduccion);
			
			if( $prod['estado'] == 'Retirado'){
									
				//Incremento el stock del modelo que se va a eliminar de la produccion
				$res = $this->Modelos->reponer($prod['modelos_id'], 1,'');
				if(!$res['success'])
					throw new BadRequestException($res['msg']);
			
			}
			
			$sql = "DELETE FROM producciones_modelos WHERE id = $idModeloProduccion";

			$result = $this->con->query($sql);
		
			if(@PEAR::isError($result)) {
		    	throw new BadRequestException('Ocurrió un error al quitar el producto de la producción.');				
		    }
		    
			return array('success'=>true, 'msg'=>'');
			
		} catch (Exception $e) {
			
			return array('success'=>false, 'msg'=>$e->getMsg());

		}
		
	}
	
	
	
	
	
	
	function eliminarProduccion($idProd){
	
		try{
		
			$this->beginTransaction();
		
			$prod = $this->getProduccionPorId($idProd);
		
			foreach($prod['modelos'] as $field => $value)
				$result = $this->removeModelo($value['idProdMod']);
		
			$this->delete($idProd);
			
			
			$this->commitTransaction();
		
		}catch (Exception $e) {
			$this->rollbackTransaction();
			
			return array('success'=>false, 'msg'=>$e->getMsg());

		}
	
	}
	

	
	
	
}
?>