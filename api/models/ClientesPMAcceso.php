<?php
class ClientesPMAcceso extends AppModel {
	
	public $name = "clientespm_acceso";
	public $primaryKey = 'id';	
	
	/**
	 * Guarda un cliente con el token que le corresponde
	 */
	function setToken($infoToken){
		try{
			if(!$this->create($infoToken))
				throw new BadRequestException('Hubo un error dar permiso al cliente');

			return array('success'=>true);

		} catch (Exception $e) {
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
	}


	/** 
	* GETTOKEN
	* Retorna el token 
	*/
	function getToken($token){
		try{		
			$sql="SELECT *
				  FROM clientespm_acceso c 
				  WHERE c.token = '".$token."'"; 

			$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   		$query = $query->execute();
			$res = $query->fetchAll();
			
			if(empty($res))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
		
			return array('success'=>true, 'token'=>$res[0]);
		} catch (Exception $e) {
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
	}

	/** 
	* GETCLIENTE
	* Retorna el token de ese cliente
	*/
	function getCliente($idCliente){
		try{		
			$sql="SELECT *
				  FROM clientespm_acceso c 
				  WHERE c.clientes_id = ".$idCliente; 

			$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   		$query = $query->execute();
			$res = $query->fetchAll();
			
			if(empty($res))
				return array('success'=>false);
		
			return array('success'=>true, 'token'=>$res[0]);
		} catch (Exception $e) {
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
	}

	/**
	* DELETETOKEN
	* Elimina el token recibido
	*/
	function deleteToken($token){
		try{		
			$sql="	DELETE 
					FROM  clientespm_acceso 
				  	WHERE token = '".$token."'"; 

			$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   		$query = $query->execute();
			$res = $query->fetchAll();
			return array('success'=>true);
			
		} catch (Exception $e) {
			return array('success'=>false, 'msg'=>$e->getMsg());
		}
	}

}	
?>
