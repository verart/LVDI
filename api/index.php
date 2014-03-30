<?php
session_start();





include_once('lib/Common.php');



if (ENTORNO == 'DESARROLLO'){	
	ini_set('display_errors', 'On');
	error_reporting(E_ERROR | E_WARNING | E_PARSE);
	error_reporting(E_ALL);
}else
	error_reporting(E_NONE);



ini_set('include_path', constant('INCLUDE_PATH'));

include_once('lib/Command.php');
include_once('lib/CommandDispatcher.php');
include_once('lib/Config.php');
include_once('lib/RequestException.php');
include_once('lib/Route.php');
include_once('config/database.php');
include_once('controllers/components/AppComponent.php');
include_once('controllers/AppController.php');
include_once('models/AppModel.php');
include_once('lib/MDB2/MDB2.php');

// Routes
$route = new Route();
$route->add('/sesion','POST','Sesion','login');
$route->add('/productos/index','GET','Productos','index');
$route->add('/productos/productosName','GET','Productos','productosName');
$route->add('/productos/productosDisponibles','GET','Productos','productosDisponibles');
$route->add('/productos/show','GET','Productos','show');
$route->add('/productos/delete','DELETE','Productos','delete');
$route->add('/productos/update','PUT','Productos','update');
$route->add('/productos/create','POST','Productos','create');
$route->add('/productos/reponer','POST','Productos','reponer');
$route->add('/productos/baja','DELETE','Productos','baja');
$route->add('/upload','POST','Productos','upload');


$route->add('/pedidos/index','GET','Pedidos','index');
$route->add('/pedidos/show','GET','Pedidos','show');
$route->add('/pedidos/delete','DELETE','Pedidos','delete');
$route->add('/pedidos/update','PUT','Pedidos','update');
$route->add('/pedidos/create','POST','Pedidos','create');

$route->add('/clientesPM/index','GET','ClientesPM','index');
$route->add('/clientesPM/clientesName','GET','ClientesPM','clientesName');
$route->add('/clientesPM/show','GET','clientesPM','show');
$route->add('/clientesPM/delete','DELETE','clientesPM','delete');
$route->add('/clientesPM/update','PUT','clientesPM','update');

$route->add('/responsables/index','GET','Responsables','index');
$route->add('/responsables/show','GET','Responsables','show');
$route->add('/responsables/delete','DELETE','Responsables','delete');
$route->add('/responsables/update','PUT','Responsables','update');

$route->add('/producciones/index','GET','Producciones','index');
$route->add('/producciones/show','GET','Producciones','show');
$route->add('/producciones/delete','DELETE','Producciones','delete');
$route->add('/producciones/update','PUT','Producciones','update');
$route->add('/producciones/create','POST','Producciones','create');



$route->submit();
?>