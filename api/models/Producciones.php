<?php
class Producciones extends AppModel {
	
	public $name = "Producciones";
	public $primaryKey = 'id';	
	
	
	public $hasMany = array('Modelos', 'MovimientosStock'); 
	
	
	/**
	 * Retorna todos las producciones
	 * params (array) $opciones = array([conditions=>array(campo=>valor)])
	 */
	function getProducciones($opciones = array()) {
	
		$conditions = (isset($opciones['conditions']))? $this->_buildConditions($opciones['conditions']): "";	
		
		$sql = "SELECT P.*, R.nombre as responsable, Pr.nombre as producto, 
				M.id as modelos_id, M.nombre as modelo,PM.estado as estadoProducto, 
				PM.id as idProdMod 
				FROM producciones P
				INNER JOIN responsables R ON R.id = P.responsables_id
				INNER JOIN producciones_modelos PM ON PM.producciones_id = P.id
				INNER JOIN modelos M ON PM.modelos_id = M.id
				INNER JOIN productos Pr ON Pr.id = M.productos_id 
				$conditions"; 
				
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
			$resultsFormat[$iF]['fecha_devolucion'] = $results[$i]['fecha_devolucion'];
			$resultsFormat[$iF]['responsables_id'] = $results[$i]['responsables_id']; 
			$resultsFormat[$iF]['responsable'] = utf8_encode($results[$i]['responsable']);
			$resultsFormat[$iF]['estado'] = $results[$i]['estado']; 
			$resultsFormat[$iF]['nota'] = $results[$i]['nota']; 
			$resultsFormat[$iF]['motivo'] = $results[$i]['motivo'];
			
			//Si mientras se recorren los modelos alguno no tiene stock se cambia reponer a 1.
			$resultsFormat[$iF]['modelos'] = array();
			$m = 0;
			while(($i < count($results))&&($resultsFormat[$iF]['id'] == $results[$i]['id'])){
				$resultsFormat[$iF]['modelos'][$m]['id'] = $results[$i]['modelos_id'];
				$resultsFormat[$iF]['modelos'][$m]['idProdMod'] = $results[$i]['idProdMod'];
				$resultsFormat[$iF]['modelos'][$m]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
				$resultsFormat[$iF]['modelos'][$m++]['estado'] = $results[$i++]['estadoProducto'];				
			}
			$iF++;
		}
		
		
		return $resultsFormat;
	}
	
	
	
	
	
	/**
	 * Retorna la produccion que coincide con el id
	 * @param $idProduccion
	 */
	function getProduccionPorId($idProduccion) {
		
				
		$sql = "SELECT P.*, R.nombre as responsable, Pr.nombre as producto, 
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
				$resultsFormat['modelos'][$i]['nombre'] = utf8_encode($results[$i]['producto']).'-'.utf8_encode($results[$i]['modelo']);
				$resultsFormat['modelos'][$i]['estado'] = $results[$i]['estadoProducto'];	
				$i++;			
			}
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
	function setProduccion($produccion, $modelos){
						
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
					
					}//for
					
				}else  
					throw new BadRequestException('Hubo un error al actualizar la producción.');				
					
					
					
			}// else UPDATE

			
			$this->commitTransaction();
			return array('success'=>true, 'id'=>$idProduccion);
			
			
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
	function removeModelo($idProdModelo){
		
		try{
		
			$this->beginTransaction();
			
			$sql = "DELETE FROM produccion_modelos WHERE id = $idProdModelo";

			$result = $this->con->query($sql);
		
			if(@PEAR::isError($result)) {
		    	throw new BadRequestException('Ocurrió un error al quitar el producto de la producción.');				
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