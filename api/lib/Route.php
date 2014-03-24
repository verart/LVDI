<?php
class Route
{
	/**
	* @var array $_listUri List of URI's to match against
	*/
	private $_requests = array();
	
	/**
	* @var array $_listCall List of closures to call 
	*/
	private $_listCall = array();
	
	/**
	* @var string $_trim Class-wide items to clean
	*/
	private $_trim = '/\^$';
		
	/**
	* add - Adds a URI and Function to the two lists
	*
	* @param string $uri A path such as about/system
	* @param object $function An anonymous function
	*/
	public function add($uri, $method, $controller, $action)
	{
		$request = array (
			'uri' => trim($uri, $this->_trim),
			'method' => $method,
			'controller' => $controller,
			'action' => $action
		);
		
		array_push($this->_requests, $request);
	}
	
	private function getNamedParameters() {
		
		$namedParams = array();
		$scriptName = explode('/',$_SERVER['REQUEST_URI']);
		
		$result = end($scriptName);
		
		if (strpos($result, '?') === FALSE) return $namedParams;
		$result = end(explode('?',$result));
		if (empty($result)) return $namedParams;

		$result = explode('&',$result);
		if (empty($result) == 1) return $namedParams;
		
		foreach ($result as $param) {
			$var_and_value = explode('=',$param);
			$var = $var_and_value[0];
			$value = $var_and_value[1];
			
			$namedParams[$var] = $value;
		}
		return $namedParams;
	}
	
	/**
	* submit - Looks for a match for the URI and runs the related function
	*/
	public function submit()
	{
		$namedParams = $this->getNamedParameters();
		
		$requestURI = explode('/', $_SERVER['REQUEST_URI']); 
		
		// Si tiene namedParams saco la última porción de la url
		if (!empty($namedParams))
			array_pop($requestURI);
		
		$scriptName = explode('/',$_SERVER['SCRIPT_NAME']); 
		$commandArray = array_diff_assoc($requestURI,$scriptName); 			
		$commandArray = array_values($commandArray);
		$resource = $commandArray[1];
		$action = end($commandArray);
		$uri = "$resource/$action";
		$requestMethod = $_SERVER['REQUEST_METHOD'];
		$parameters = array('GET' => array_slice($commandArray,2), 'POST' => $_POST, 'NAMED' => $namedParams);
		$uri = trim($uri, $this->_trim);
		
		$replacementValues = array();
		//echo "<pre>";print_r($uri); 
		/**
		* List through the stored URI's
		*/

		foreach ($this->_requests as $request)
		{
			$listURI = $request['uri'];
			$listMethod = $request['method'];
			/**
			* Matchea con alguna uri?
			*/
		if (preg_match("#^$listURI#", $uri) && ($listMethod == $requestMethod))
			{ 
				// Creo un command y lo ejecuto
				$controllerName = $request['controller']; 
				$controllerAction = $request['action'];

				$command = new Command($controllerName,$controllerAction,$parameters,$requestMethod);
				$commandDispatcher = new CommandDispatcher($command);
				$commandDispatcher->Dispatch();
			}
			
		}
		
	}
	
}
?>