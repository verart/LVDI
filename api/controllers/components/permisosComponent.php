<?php
class PermisosComponent extends AppComponent{

	var $name = "PermisosComponent";
	var $uses = array('Usuarios');

	
	var $acceso = array(
		'productos'=>array(	'index','show','update','create','delete', 'baja', 'venta', 'reponer', 
							'productosName', 'productosDisponibles', 'upload', 'saveFile', 'removeFile', 'notas')
	);
		
	var $edicion = array('productos','producciones','ventas','notas');
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
					$this->acceso['clientesPM'] = array('index', 'clientesName','show');			
					$this->acceso['ColaImpresion'] = array('index','delete','create');
					$this->edicion['pedidos'] = array('update');	
					$this->acceso['ventas']=array('index','show');	
					$this->acceso['notas']=array('index','create','delete');
					$this->acceso['pedidosespeciales'] = array('index', 'show', 'update'); 
					$this->edicion['clientesPM']=array('create','update', 'delete');
			        break;
			    case 'local':
					$this->acceso['productos']= array('index', 'show', 'update','productoModelo','reponer');
					$this->acceso['producciones']= array('index', 'show', 'update', 'delete','create');
					$this->acceso['ventas']=array('index','show', 'addPago','deletePago');
					$this->acceso['clientes']=array('index','create','show','update','delete');
					$this->acceso['responsables']= array('index', 'show', 'update', 'create','delete');
					$this->edicion['ventas']=array('create','delete', 'addPago','deletePago','update');					
					$this->edicion['clientes']=array('create','update', 'delete');
					$this->edicion['producciones']= array('create','update', 'delete');
					$this->edicion['responsables']= array('create','update', 'delete');	
					$this->acceso['ColaImpresion']=array('index','create','delete');
					$this->acceso['notas']= array('index','create','delete');
					$this->edicion['notas']= array('create','delete');
					$this->acceso['pedidosespeciales'] = array('created','index', 'show', 'update'); 
			        break;
			    case 'cuentas' :
			    	$this->acceso['resumen']= array('index');
			    	$this->acceso['ventas']=array('index','show');	
					$this->acceso['notas']= array('index','create','delete');
					$this->acceso['gastos']= array('index','create','delete');
					$this->acceso['categorias']= array('index','create','delete');					
					$this->acceso['productos']= array('index','seguimiento','productoModelo');
					$this->edicion['gastos']= array('index','create','delete');
					$this->edicion['notas']= array('index','create','delete');
			    	break;
			}
		
		}
	}
	
	
	function puedeAcceder($controller, $accion){
		if($this->perfil== 'admin')
			return true;
		else
			if($this->perfil != '')
				if (array_key_exists($controller, $this->acceso))
					return in_array($accion, $this->acceso[$controller]);
				else return false;
			else return false;
		
	}
	
	
	function puedeEditar($controller, $accion){
		
		if($this->perfil== 'admin')
			return true;
		else
			if($this->perfil != '')
				if (array_key_exists($controller, $this->edicion))
					return in_array($accion, $this->edicion[$controller]);
				else return false;
			else return false;
		
	}

	
	function verPerfil(){		
		return $this->perfil;
	}
	
}