<?php
class AppController {
	var $Command;
	var $name;
	var $layout = "default";
	var $viewVars = array();
	var $data = array();
	var $namedParams = array();
	var $uses = array('Usuarios');
	var $usesComponents = array('SesionComponent','PermisosComponent');
		
	function AppController(&$command) {
		$this->Command = $command;
		
		$this->_loadModels(); 
		$this->_loadComponents();
	}
 
	function _default() {
 
	}
      
	function _error() {
 
	}
	
	
	function beforeFilter() {

		$usuarioLoggueado = $this->SesionComponent->verEnSesion('usuario');
		$this->PermisosComponent->asignarUsuario($usuarioLoggueado['id']);
	
	}
	
	/**
	 * Carga los modelos 
	 */
	function _loadModels() {
		foreach($this->uses as $model) {
			if (file_exists("models/$model.php")) {
				include_once("models/$model.php");
				$this->{$model} = new $model(); 
			} else {
				$this->{$model} = new AppModel($model);
			}
		}
	}
	
	/**
	 * Carga los componentes 
	 */
	function _loadComponents() {
		foreach($this->usesComponents as $comp) { 
			if (file_exists("controllers/components/$comp.php")) { 
				include_once("controllers/components/$comp.php");
				$this->{$comp} = new $comp(); 
			} 
		}
	}
      
	function execute() {
		
		$this->beforeFilter();
		
		$functionToCall = $this->Command->getFunction();
		$parameters = $this->Command->getParameters();
		if($this->Command->getFunction() == '') {
			$functionToCall = 'default';
		}

		if(!is_callable(array(&$this,$functionToCall))) {
			$functionToCall = 'error';
		}
		$this->data = $parameters['POST'];
		$this->namedParams = $parameters['NAMED'];
		call_user_func_array(array(&$this,$functionToCall),$parameters['GET']);
	
	}


	function json($msg="", $data=array(), $code=200) {
	
		header('Content-type: application/json',true, $code);

		$response = array(
			'MSG' => $msg,
			'DATA' => $data,
		);
		
		return json_encode($response);
	}

	
	/**
	 * Chequea que los parámetros estén en $recibidos
	 */
	function parametrosRequeridosEn($requeridos = array(),$recibidos) {
		
		foreach ($requeridos as $param) {
			if(empty($recibidos[$param])) {
				return false;
			}
		}
		
		return true;
	}
	
	/**
	 * Chequea que los parámetros estén en this->data
	 */
	function parametrosRequeridos($requeridos = array()) { 
		foreach ($requeridos as $param) {
			if (empty($this->data[$param])) {
				return false;
			}
		}
		
		return true;
	}
	
	
	
}
?>