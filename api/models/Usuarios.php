<?php
class Usuarios extends AppModel {
	
	public $name = "Usuarios";
	public $primaryKey = 'id';	

      
	/**
	 * Retorna el usuario que coincide con el usuario y clave
	 * @param $usuario
	 * @param $clave
	 */
	function getUsuario($usuario, $clave) {
		
		$sql = "SELECT U.*, P.perfil as perfil 
				FROM usuarios U 
				INNER JOIN perfiles P ON P.id = U.perfiles_id 
				WHERE U.nombre = ? and U.clave = ?";
				
	   	$query = $this->con->prepare($sql, array('text','text'), MDB2_PREPARE_RESULT); 
	   	$query = $query->execute(array($usuario,$clave));
	   	
		return $query->fetchRow();
		
	}
	
	/**
	 * Retorna el usuario que coincide con el usuario y clave
	 * @param $usuario
	 * @param $clave
	 */
	function getUsuarios() {
		
		$sql = "SELECT U.*, P.perfil as perfil 
				FROM usuarios U 
				INNER JOIN perfiles P ON P.id = U.perfiles_id";
				
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute(array());
	   	
		return $query->fetchAll();
		
	}
	
	
	/**
	 * Retorna el usuario que coincide con el id
	 * @param $idUsuario
	 */
	function getUsuarioPorId($idUsuario) {

		$sql = "SELECT U.*, P.perfil as perfil 
				FROM usuarios U 
				INNER JOIN perfiles P ON P.id = U.perfiles_id 
				WHERE U.id = ?"; 
				
    	$query = $this->con->prepare($sql, array('integer'));	
		$query = $query->execute(array($idUsuario));

		return $query->fetchRow();	
			
	}
	
		
	/**
	* SETUSUARIO
	* $usuario = array( ['id'], 'nombre', 'pass' )
	*/
	function setUsuario($usuario){
		
		try{
			$us = $usuario;
			
			if(isset($usuario['nombre'])) $usuario['nombre'] = utf8_decode($usuario['nombre']);
			
				
			if(!isset($usuario['id'])){ 
				
				if(!$this->create($usuario)){			
					throw new BadRequestException('Hubo un error al crear el usuario.');
				}
				print_r('creado');
				$us['id'] = $this->getLastId();
							
			}else{
				
				if(!$this->update($usuario, array('id'=>$usuario['id'])))
					throw new BadRequestException('Hubo un error al actualizar el usuario.');				
					
			}
		
			return array('success'=>true, 'usuario'=>$usuario);

		} catch (Exception $e) {

			return array('success'=>false, 'msg'=>$e->getMsg());

		}		
		
	}
	
	
	

	/**
	 * DELUSUARIO 
	 * Elimina el usuario que coincide con el id
	 * @param $idUsuario
	 */
	function delUsuario($idUsuario){
		
		try{
		
			if(!$this->delete($idUsuario))
				throw new BadRequestException('Hubo un error al eliminar el usuario.');
				
					
		}catch (Exception $e) {
			echo $e->getMsg();

		}
			
	}
	
	
	
	
}
?>