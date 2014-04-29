<?php
class Responsables extends AppModel {
	
	public $name = "Responsables";
	public $primaryKey = 'id';	

	/**
	 * Retorna todos los responsables
	 * params (array) $opciones = array([conditions], [order])
	 */
	function getResponsables($opciones = array()) {
	
		$opciones = array('order'=>'nombre DESC');
		$results = $this->readAll($opciones);
	
		for($i=0; $i < count($results); $i++){
			$results[$i]['nombre'] = utf8_encode($results[$i]['nombre']);
			$results[$i]['direccion'] = utf8_encode($results[$i]['direccion']);
			$results[$i]['localidad'] = utf8_encode($results[$i]['localidad']);
		}
		return $results;
	}
	
	
	
	
	
	/**
	 * GETRESPONSABLEPORID
	 * Retorna el responsable por mayor que coincide con el id
	 * @param $idResponsable
	 */
	function getResponsablePorId($idResponsable) {
		
				
		$sql = "SELECT *
				FROM responsables R	
				WHERE R.id = ?";
				
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idResponsable));
		$results = $query->fetchAll();
		
		for($i=0; $i < count($results); $i++){
			$results[$i]['nombre'] = utf8_encode($results[$i]['nombre']);
			$results[$i]['direccion'] = utf8_encode($results[$i]['direccion']);
			$results[$i]['localidad'] = utf8_encode($results[$i]['localidad']);		
		}
		
		return $results;		
	}
	
	
	
	
	/**
	* SETRESPONSABLE
	* $cliente = array( ['id'=>''], 'nombre'=>'', 'local'=>'','bonificacion'=>'', 'fecha'=>'' )
	*/
	function setResponsable($responsable){
		
		try{
		
			$res = $responsable;
			
			if(isset($responsable['nombre'])) $responsable['nombre'] = utf8_decode($responsable['nombre']);
			if(isset($responsable['marca'])) $responsable['marca'] = utf8_decode($responsable['marca']);
			if(isset($responsable['local']))  $responsable['local'] = utf8_decode($responsable['local']);
			if(isset($responsable['direccion'])) $responsable['direccion'] = utf8_decode($responsable['direccion']);
			if(isset($responsable['localidad'])) $responsable['localidad'] = utf8_decode($responsable['localidad']);
			
			
			if(!isset($responsable['id'])){ 
			
				$responsable['created'] = date('Y/m/d h:i:s', time());
				
				if(!$this->create($responsable))				
					throw new BadRequestException('Hubo un error al crear el responsable de producción.');
							
			}else{
				
				if(!$this->update($responsable, array('id'=>$responsable['id'])))
					throw new BadRequestException('Hubo un error al actualizar el responsable de producción.');				
					
			}

			return array('success'=>true, 'responsables'=>$res);

		} catch (Exception $e) {
			
			return array('success'=>false, 'msg'=>$e->getMsg());		
		
		}
		
		
	}
	
	/**
	 * NOTUSED
	 * Retorna si esta siendo usado en alguna produccion
	 * @param (int) $idResponsable
	 */
	function notUsed($idResponsable) {
		
		// Chequeo que alguno de los modelos no este en algun pedido
		$sql = "SELECT *
				FROM responsables R
				INNER JOIN producciones P ON P.responsables_id = R.id
				WHERE R.id = ?";
							
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idResponsable));
		$respnsablesProducciones = $query->fetchAll();	
	
		return (empty($respnsablesProducciones));
			
	}



	/**
	 * DELRESPONSABLE
	 * Elimina el responsable de producción que coincide con el id
	 * @param $idResponsable
	 */
	function delResponsable($idResponsable){
		
		try{
		
			if($this->notUsed($idResponsable)){ 
			
				if(!$this->delete($idResponsable))
					throw new BadRequestException('Hubo un error al eliminar el responsable de producción.');
			}else
					throw new BadRequestException('Existen producciones de este responsable. No se puede eliminar. ');		
					
			return array('success'=>true, 'responsables'=>$idResponsable);
					
		}catch (Exception $e) {
			
			return array('success'=>false, 'msg'=>$e->getMsg());

		}
			
	}
	
	
	
}
?>