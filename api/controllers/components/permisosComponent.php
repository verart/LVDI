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
			        break;
			    case 'taller':
					$this->acceso['productos'] = array('index', 'show', 'update','productoModelo','reponer');
					$this->acceso['pedidos'] = array('index', 'show', 'update'); 
					$this->acceso['clientesPM'] = array('clientesName','show');			
					$this->acceso['colaImpresion'] = array('index','delete','create');
					$this->edicion['pedidos'] = array('update');		
			        break;
			    case 'local':
					$this->acceso['productos']= array('index', 'show', 'update','productoModelo','reponer');
					$this->acceso['producciones']= array('index', 'show', 'update', 'delete','create');
					$this->acceso['ventas']=array('index','show');
					$this->acceso['clientes']=array('index');
					$this->acceso['responsables']= array('index', 'show', 'update');
					$this->edicion['ventas']=array('create','delete');					
					$this->edicion['clientes']=array('create','update', 'delete');
					$this->edicion['producciones']= array('create','update', 'delete');
					$this->edicion['responsables']= array('create','update', 'delete');	
					$this->acceso['ColaImpresion']=array('index','create','delete');
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