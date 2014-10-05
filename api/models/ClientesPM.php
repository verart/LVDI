<?php
class ClientesPM extends AppModel {
	
	public $name = "ClientesPM";
	public $primaryKey = 'id';	

	
	
	/**
	 * GETCLIENTES
	 * Retorna todos los clientes
	 * params (array) $opciones = array([conditions], [order])
	 */
	function getClientes($opciones = array()) {
	
		$results = $this->readPage($opciones);
	
		for($i=0; $i < count($results); $i++){
			$results[$i]['nombre'] = utf8_encode($results[$i]['nombre']);
			$results[$i]['local'] = utf8_encode($results[$i]['local']);
			$results[$i]['direccion'] = utf8_encode($results[$i]['direccion']);
			$results[$i]['localidad'] = utf8_encode($results[$i]['localidad']);
	
		}
		return $results;
	}
	
	
	
	/**
	 * Retorna los nombres de los clientes
	 */
	function getClientesNames($opciones = array()) {
		
		$results = $this->readAll($opciones);
			
	   	for($i=0; $i < count($results)-1; $i++)
			$results[$i]['nombre']= utf8_encode($results[$i]['nombre']);
					
		return $results;
	}
	
	
	
	
	
	/**
	 * GETCLIENTEPORID
	 * Retorna el cliente por mayor que coincide con el id
	 * @param $idCliente
	 */
	function getClientePorId($idCliente) {
		
				
		$sql = "SELECT *
				FROM clientespm C	
				WHERE C.id = ?";
				
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idCliente));
		$results = $query->fetchAll();
		
		for($i=0; $i < count($results); $i++){
			$results[$i]['nombre'] = utf8_encode($results[$i]['nombre']);
			$results[$i]['local'] = utf8_encode($results[$i]['local']);
			$results[$i]['direccion'] = utf8_encode($results[$i]['direccion']);
			$results[$i]['localidad'] = utf8_encode($results[$i]['localidad']);
			
		}
		
		return $results;		
	}
	
	
	
	
	/**
	* SETCLIENTE
	* $cliente = array( ['id'], 'nombre', 'local','bonificacion', 'fecha'=>' )
	*/
	function setCliente($cliente){
		
		try{
			$cl = $cliente;
			
			if(isset($cliente['nombre'])) $cliente['nombre'] = utf8_decode($cliente['nombre']);
			if(isset($cliente['local']))  $cliente['local'] = utf8_decode($cliente['local']);
			if(isset($cliente['direccion'])) $cliente['direccion'] = utf8_decode($cliente['direccion']);
			if(isset($cliente['localidad'])) $cliente['localidad'] = utf8_decode($cliente['localidad']);
		
		
			if(!isset($cliente['id'])){ 
				
				$cliente['created'] = date('Y/m/d h:i:s', time());
	
				if(!$this->create($cliente))				
					throw new BadRequestException('Hubo un error al crear el cliente.');
				
				$cl['id'] = $this->getLastId();
							
			}else{
				
				if(!$this->update($cliente, array('id'=>$cliente['id'])))
					throw new BadRequestException('Hubo un error al actualizar el cliente.');				
					
			}
			
				
			return array('success'=>true, 'clientesPM'=>$cl);

		} catch (Exception $e) {

			return array('success'=>false, 'msg'=>$e->getMsg());

		}
		
		
	}
	
	/**
	 * NOTUSED
	 * Retorna si esta siendo usado en algun pedido 
	 * @param (int) $idCliente
	 */
	function notUsed($idCliente) {
		
		// Chequeo que alguno de los modelos no este en algun pedido
		$sql = "SELECT *
				FROM clientespm C
				INNER JOIN pedidos P ON P.clientesPM_id = C.id
				WHERE C.id = ?";
							
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idCliente));
		$clientesPedidos = $query->fetchAll();	
	
		return (empty($clientesPedidos));
			
	}

	/**
	 * DELCLIENTE 
	 * Elimina el cliente que coincide con el id
	 * @param $idCliente
	 */
	function delCliente($idCliente){
		
		try{
		
			if($this->notUsed($idCliente)){
			
				if(!$this->delete($idCliente))
					throw new BadRequestException('Hubo un error al eliminar el cliente.');
			}else
					throw new BadRequestException('Existen pedidos de este cliente. No se puede eliminar. ');		
					
		}catch (Exception $e) {
			echo $e->getMsg();

		}
			
	}
	
	
	
}
?>