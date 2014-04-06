<?php
class PermisosComponent extends AppComponent{

	var $name = "PermisosComponent";
	var $uses = array('Usuarios');

	
	var $acceso = array(
		'productos'=>array(	'index','show','update','create','delete', 'baja', 'venta', 'reponer', 
							'productosName', 'productosDisponibles', 'upload', 'saveFile', 'removeFile')
	);
		
	var $edicion = array('productos','producciones','ventas');
	var $perfil = '';

	
	function asignarUsuario($idUsuario){
		
		if($this->perfil == ''){
			
			$usuario = $this->Usuarios->getUsuarioPorId($idUsuario);	
			$this->perfil = $usuario['perfil'];
			
			
			switch ($this->perfil) {
			    case 'admin':
					$this->acceso['cuentas'] = array('crear','eliminar');
					$this->acceso['pedidos'] = array('index','show','update','create');
					$this->acceso['producciones']= array('index', 'show', 'update', 'create');
					$this->acceso['ventas']=array('index');
					$this->acceso['clientesPM']=array('index','clientesName');
					$this->acceso['clientes']=array('index', 'clientesName');
			        break;
			    case 'taller':
					$this->acceso['pedidos'] = array('index','show','update','create'); 
					$this->acceso['clientesPM']=array('clientesName');
			        break;
			    case 'local':
					$this->acceso['producciones']= array('index', 'show', 'update', 'create');
					$this->acceso['ventas']=array('index');
	
			        break;
			}
		
		}
	}
	
	
	function puedeAcceder($controller, $accion){
		if($this->perfil== 'admin')
			return true;
		else
			return in_array($accion, $this->acceso[$controller]);
		
	}
	
	
	function puedeEditar($controller, $accion){
		
		if($this->perfil== 'admin')
			return true;
		else
			return in_array($accion, $this->edicion[$controller]);
		
	}


	
	function verPerfil(){		
		return $this->perfil;
	}
	
}