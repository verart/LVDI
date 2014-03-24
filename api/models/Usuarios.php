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
	

	
}
?>