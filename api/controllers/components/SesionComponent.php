<?php
class SesionComponent extends AppComponent{

	var $name = "SesionComponent";
	
	/**
	 * GUARDARSESION
     * Guarda en $_SESSION en $index la info que llega en $data
	 */
	function guardarEnSesion($index, $data) {
		$_SESSION[$index] = $data;
		
	}
	
	
	function verEnSesion($index){
	
		if(isset($_SESSION[$index]))
			return $_SESSION[$index];
		else
			return false;
	}
	
	

}
?>