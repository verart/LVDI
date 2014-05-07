<?php
class UsuariosController extends AppController {

	var $name = "Usuarios";
	var $uses = array('Usuarios');
	
	
	
	

	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('usuarios', 'index'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			$usuarios = $this->Usuarios->getUsuarios();
			echo $this->json('Usuarios', $usuarios);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	
	
	
	
	function show($idUsuario) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('usuarios', 'show'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			$usuario = $this->Usuarios->getUsuarioPorId($idUsuario); 
			echo $this->json('', $usuario[0]); 

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	function create() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('usuarios', 'create'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
				
			$params = (isset($_POST['usuarios']))? $_POST['usuarios'] : array();

			unset($params['perfil']);

			// Campos obligatorios
			if ( !$this->parametrosRequeridosEn(array('nombre', 'clave', 'perfiles_id'), $params) )
				throw new BadRequestException('Debe completar los campos obligatorios'); 
					
			$res = $this->Usuarios->setUsuario($params);
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
				
	
			echo $this->json('usuario', $res['usuario']);
				

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	
	
	
	function update() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('usuarios', 'update'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
				
			$params = getPutParameters();	
			$params = (isset($params['usuarios']))? $params['usuarios'] : array();
			
			unset($params['$$hashKey']); 
			
			// Campos obligatorios
			if ( !$this->parametrosRequeridosEn(array('nombre','id','clave'), $params) )
				throw new BadRequestException('Debe completar los campos obligatorios'); 
			
			$res = $this->Usuarios->setUsuario($params);			
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
				
	
			echo $this->json('usuario', $res['usuario']);
					

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	function delete($idUsuario) {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('usuarios', 'delete'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			$this->Usuarios->delUsuario($idUsuario);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
}
?>