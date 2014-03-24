<?php
class AppComponent{
	var $name;
	var $uses = array();
	var $usesComponents = array();
		
	function AppComponent() {
			
		$this->_loadModels();
		$this->_loadComponents();
	}
 
	/**
	 * Carga los modelos 
	 *
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
	 *
	 */
	function _loadComponents() {
		foreach($this->usesComponents as $comp) {
			if (file_exists("components/$comp.php")) {
				include_once("components/$comp.php");
				$this->{$comp} = new $comp(); 
			} 
		}
	}
}
?>