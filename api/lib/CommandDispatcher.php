<?php
class CommandDispatcher {
	var $Command;
 
	function CommandDispatcher(&$command) {
		$this->Command = $command;
	}
 
	function isController($controllerName) {
		return (file_exists("controllers/{$controllerName}Controller.php"));
	}

	function Dispatch() {
		$controllerName = $this->Command->getControllerName();
		
		if($this->isController($controllerName) == false) {
			$controllerName = 'error';
		}
		include_once("controllers/{$controllerName}Controller.php");
		$controllerClass = $controllerName."Controller"; 
		$controller = new $controllerClass($this->Command);
		$controller->execute();
	}
}
?>