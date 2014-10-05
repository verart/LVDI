<?php
class Clientes extends AppModel {
	
	public $name = "Clientes";
	public $primaryKey = 'id';	

	
	
	/**
	 * GETCLIENTES
	 * Retorna todos los clientes 
	 * params (array) $opciones = array([conditions], [order], [page], [pageSize]])
	 */
	function getClientes($opciones = array()) {
	
		$results = $this->readPage($opciones);
	
		for($i=0; $i < count($results); $i++){
			$results[$i]['nombre'] = utf8_encode($results[$i]['nombre']);
			$results[$i]['email'] = utf8_encode($results[$i]['email']);
			$results[$i]['nota'] = utf8_encode($results[$i]['nota']);
	
		}
		return $results;
	}
	
	
	
	
	
	
	/**
	 * GETCLIENTEPORID
	 * Retorna el cliente que coincide con el id
	 * @param $idCliente
	 */
	function getClientePorId($idCliente) {
		
				
		$sql = "SELECT *
				FROM clientes C	
				WHERE C.id = ?";
				
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idCliente));
		$results = $query->fetchAll();
		
		for($i=0; $i < count($results); $i++){
			$results[$i]['nombre'] = utf8_encode($results[$i]['nombre']);
			$results[$i]['email'] = utf8_encode($results[$i]['email']);
			$results[$i]['nota'] = utf8_encode($results[$i]['nota']);
			
		}
		
		return $results;		
	}
	
	
	
	
	/**
	* SETCLIENTE
	* $cliente = array( ['id'], 'nombre', 'local','bonificacion', 'fecha' )
	*/
	function setCliente($cliente){
		
		try{
			$cl = $cliente;
			
			if(isset($cliente['nombre'])) $cliente['nombre'] = utf8_decode($cliente['nombre']);
			if(isset($cliente['email']))  $cliente['email'] = utf8_decode($cliente['email']);
			if(isset($cliente['nota'])) $cliente['nota'] = utf8_decode($cliente['nota']);
		
				
			if(!isset($cliente['id'])){ 
				
				$cliente['created'] = date('Y/m/d h:i:s', time());
	
				if(!$this->create($cliente))				
					throw new BadRequestException('Hubo un error al crear el cliente.');
				
				
				$cl['id'] = $this->getLastId();
							
			}else{
				
				if(!$this->update($cliente, array('id'=>$cliente['id'])))
					throw new BadRequestException('Hubo un error al actualizar el cliente.');				
					
			}
		
			return array('success'=>true, 'clientes'=>$cl);

		} catch (Exception $e) {

			return array('success'=>false, 'msg'=>$e->getMsg());

		}
		
		
	}
	
	

	/**
	 * DELCLIENTE 
	 * Elimina el cliente que coincide con el id
	 * @param $idCliente
	 */
	function delCliente($idCliente){
		
		try{
		
			if(!$this->delete($idCliente))
				throw new BadRequestException('Hubo un error al eliminar el cliente.');
				
					
		}catch (Exception $e) {
			echo $e->getMsg();

		}
			
	}
	
	
	
}
?>