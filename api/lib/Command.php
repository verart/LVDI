<?php
class Command { 
	var $Name = '';
	var $Function = '';
	var $RequestMethod = '';
	var $Parameters = array();

	function Command($controllerName,$functionName,$parameters,$requestMethod) {
		$this->Parameters = $parameters;
		$this->Name = $controllerName;
		$this->Function =$functionName;
		$this->RequestMethod = $requestMethod;
	}

	function getControllerName() {
		return ucwords($this->Name);
	}

	function setControllerName($controllerName) {
		$this->Name = $controllerName;
	}

	function getFunction() {
		return $this->Function;
	}

	function setFunction($functionName) {
		$this->Function = $functionName;
	}

	function getParameters() {
		return $this->Parameters;
	}

	function setParameters($controllerParameters) {
		$this->Parameters = $controllerParameters;
	}
}
?>