<?php
class PermisosComponent extends AppComponent{

	var $name = "PermisosComponent";
	var $uses = array('Usuarios');

	
	var $acceso = array(
		'productos'=>array('index','show','update','create'),
		'pedidos'=>array('index','show','update','create'),
		'producciones'=>array('index'),
		'ventas'=>array('index'));
		
	var $edicion = array('productos','producciones','ventas');
	var $perfil = '';

	
	function asignarUsuario($idUsuario){
		
		if($this->perfil == ''){
			
			$usuario = $this->Usuarios->getUsuarioPorId($idUsuario);	
			$this->perfil = $usuario['perfil'];
			
			if($this->perfil == 'ADMIN')
				$this->acceso['cuentas'] = array('crear','eliminar');
		
		}
	}
	
	
	function puedeAcceder($controller, $accion){
		
		if($this->perfil== 'ADMIN')
			return true;
		else
			return isset($this->acceso[$controller][$accion]);
		
	}
	
	
	function puedeEditar($controller, $accion){
		
		if($this->perfil== 'ADMIN')
			return true;
		else
			return isset($this->edicion[$controller][$accion]);
		
	}


	
	function verPerfil(){		
		return $this->perfil;
	}
	
}