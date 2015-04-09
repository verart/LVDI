<?php
class Responsables extends AppModel {
	
	public $name = "Responsables";
	public $primaryKey = 'id';	

	/**
	 * Retorna todos los responsables
	 * params (array) $opciones = array([conditions], [order])
	 */
	function getResponsables($opciones = array()) {
	
		$results = $this->readPage($opciones);
	
		for($i=0; $i < count($results); $i++){
			$results[$i]['nombre'] = utf8_encode($results[$i]['nombre']);
			$results[$i]['direccion'] = utf8_encode($results[$i]['direccion']);
			$results[$i]['localidad'] = utf8_encode($results[$i]['localidad']);
		}
		return $results;
	}
	
	
	
	/**
	 * Retorna todos los responsables - nombre y descuento
	 * params (array) $opciones = array([fileds], [order])
	 */
	function getResponsablesList($opciones = array()) {
	
		
		$results = $this->readAll($opciones);
	
		for($i=0; $i < count($results); $i++){
			$results[$i]['nombre'] = utf8_encode($results[$i]['nombre']);
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
	 * Retorna el responsable que coincide con $nombre
	 * @param $nombre
	 */
	function getResponsablePorNombre($nombre) {

		$nombre = strtolower ($nombre);
		$nombre = str_replace("%20", " ", $nombre);
		$text = '%'.$nombre.'%';

		$sql = "SELECT *   
				FROM responsables r
				WHERE (r.nombre like '".$text."')
				ORDER BY r.nombre  
				LIMIT 0,10" ; 

		try{				
	    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
			$query = $query->execute(); 
			
			//Se formatea el resultado para que queden los datos del producto con su arreglo de modelos.
			$results = $query->fetchAll();
			$resultsFormat = array(); 

			if(!empty($results)){ 
				for ($i=0 ; $i<count($results) ; $i++) {
					$resultsFormat[$i]['nombre'] = utf8_encode($results[$i]['nombre']);
					$resultsFormat[$i]['nota'] = utf8_encode($results[$i]['nota']);
					$resultsFormat[$i]['id'] =  $results[$i]['id'];
				} 
				return array('success'=>true, 'responsables'=>$resultsFormat); 
			}else
				throw new BadRequestException('No existe responsable de producción que coincida con '.$nombre.'.');

		}catch(Exception $e){
			return array('success'=>false,'msg'=>$e->getMsg());
		}	
		
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
			if(isset($responsable['direccion'])) $responsable['direccion'] = utf8_decode($responsable['direccion']);
			if(isset($responsable['localidad'])) $responsable['localidad'] = utf8_decode($responsable['localidad']);
			
			
			if(!isset($responsable['id'])){ 
			
				$responsable['created'] = date('Y/m/d h:i:s', time()); 
				
				if(!$this->create($responsable))				
					throw new BadRequestException('Hubo un error al crear el responsable de producción.');
				
				$res['id'] = $this->getLastId();
							
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