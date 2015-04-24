<?php
class Config {

/**
 * Returns a singleton instance of the Config class.
 *
 * @return Configure instance
 * @access public
 */
	function &getInstance($boot = true) {
		static $instance = array();
		if (!$instance) {
			$instance[0] = new Config();
			//$instance->__loadBootstrap($boot);
		}
		return $instance[0];
	}


/**
 * Checks $name for dot notation to create dynamic Configure::$var as an array when needed.
 *
 * @param mixed $name Name to split
 * @return array Name separated in items through dot notation
 * @access private
 */
	function __configVarNames($name) {
		if (is_string($name)) {
			if (strpos($name, ".")) {
				return explode(".", $name);
			}
			return array($name);
		}
		return $name;
	}
  
  
/**
 * Used to store a dynamic variable in the Configure instance.
 *
 * Usage:
 * {{{
 * Configure::write('One.key1', 'value of the Configure::One[key1]');
 * Configure::write(array('One.key1' => 'value of the Configure::One[key1]'));
 * Configure::write('One', array(
 *     'key1' => 'value of the Configure::One[key1]',
 *     'key2' => 'value of the Configure::One[key2]'
 * );
 *
 * Configure::write(array(
 *     'One.key1' => 'value of the Configure::One[key1]',
 *     'One.key2' => 'value of the Configure::One[key2]'
 * ));
 * }}}
 *
 * @link http://book.cakephp.org/view/412/write
 * @param array $config Name of var to write
 * @param mixed $value Value to set for var
 * @return void
 * @access public
 */
	function write($config, $value = null) {
		$_this =& Config::getInstance();

		if (!is_array($config)) {
			$config = array($config => $value);
		}

		foreach ($config as $names => $value) {
			$name = $_this->__configVarNames($names);

			switch (count($name)) {
				case 3:
					$_this->{$name[0]}[$name[1]][$name[2]] = $value;
				break;
				case 2:
					$_this->{$name[0]}[$name[1]] = $value;
				break;
				case 1:
					$_this->{$name[0]} = $value;
				break;
			}
		}
}

/**
 * Used to read information stored in the Configure instance.
 *
 * Usage
 * Configure::read('Name'); will return all values for Name
 * Configure::read('Name.key'); will return only the value of Configure::Name[key]
 *
 * @link          http://book.cakephp.org/view/413/read
 * @param string $var Variable to obtain
 * @return string value of Configure::$var
 * @access public
 */
	function read($var = 'debug') {
		$_this =& Config::getInstance();

	/*	if ($var === 'debug') {
			if (!isset($_this->debug)) {
				if (defined('DEBUG')) {
					$_this->debug = DEBUG;
				} else {
					$_this->debug = 0;
				}
			}
			return $_this->debug;
		}
    */

		$name = $_this->__configVarNames($var);

		switch (count($name)) {
			case 3:
				if (isset($_this->{$name[0]}[$name[1]][$name[2]])) {
					return $_this->{$name[0]}[$name[1]][$name[2]];
				}
			break;
			case 2:
				if (isset($_this->{$name[0]}[$name[1]])) {
					return $_this->{$name[0]}[$name[1]];
				}
			break;
			case 1:
				if (isset($_this->{$name[0]})) {
					return $_this->{$name[0]};
				}
			break;
		}
		return null;
	 }
/**
 * Used to delete a variable from the Configure instance.
 *
 * Usage:
 * Configure::delete('Name'); will delete the entire Configure::Name
 * Configure::delete('Name.key'); will delete only the Configure::Name[key]
 *
 * @link          http://book.cakephp.org/view/414/delete
 * @param string $var the var to be deleted
 * @return void
 * @access public
 */
	function delete($var = null) {
  		$_this =& Config::getInstance();
  		$name = $_this->__configVarNames($var);
  
  		if (isset($name[1])) {
  			 unset($_this->{$name[0]}[$name[1]]);
  		} else {
  			 unset($_this->{$name[0]});
  		}
	}
  
  
}

?>
