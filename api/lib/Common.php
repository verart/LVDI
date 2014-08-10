<?php
if (!defined('ENTORNO'))
	define('ENTORNO','DESARROLLO'); /* DESARROLLO, PRODUCCION */

if (!defined('ROOT_DIR'))
	define('ROOT_DIR','LVDI');

if (!defined('ROOT_URL'))
	define('ROOT_URL','/LVDI/');
	
if (!defined('INCLUDE_PATH'))
	define('INCLUDE_PATH',"./" . PATH_SEPARATOR . "./lib");
		
if (!defined('COMPLETE_ROOT_DIR'))
	define('COMPLETE_ROOT_DIR','/Applications/MAMP/htdocs/LVDI/');
	
	
function isAjax() {
	return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest');
}



function getPutParameters(){
	parse_str(file_get_contents("php://input"),$post_vars);
	return $post_vars;
	
}


?>