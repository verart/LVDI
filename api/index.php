<?php
ini_set("session.cookie_lifetime","86400");
ini_set("session.gc_maxlifetime","86400");
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
$route->add('/productos/productoModeloById','GET','Productos','productoModeloById');
$route->add('/productos/productoModeloByName','GET','Productos','productoModeloByName');
$route->add('/productos/show','GET','Productos','show');
$route->add('/productos/delete','DELETE','Productos','delete');
$route->add('/productos/update','PUT','Productos','update');
$route->add('/productos/create','POST','Productos','create');
$route->add('/productos/reponer','POST','Productos','reponer');
$route->add('/productos/baja','DELETE','Productos','baja');
$route->add('/upload','POST','Productos','upload');


$route->add('/pedidos/index','POST','Pedidos','index');
$route->add('/pedidos/show','GET','Pedidos','show');
$route->add('/pedidos/modelos','GET','Pedidos','modelos');
$route->add('/pedidos/pagos','GET','Pedidos','pagos');
$route->add('/pedidos/delete','DELETE','Pedidos','delete');
$route->add('/pedidos/update','PUT','Pedidos','update');
$route->add('/pedidos/create','POST','Pedidos','create');

$route->add('/clientesPM/index','POST','ClientesPM','index');
$route->add('/clientesPM/clientesName','GET','ClientesPM','clientesName');
$route->add('/clientesPM/show','GET','ClientesPM','show');
$route->add('/clientesPM/delete','DELETE','ClientesPM','delete');
$route->add('/clientesPM/update','PUT','ClientesPM','update');
$route->add('/clientesPM/create','POST','ClientesPM','create');

$route->add('/responsables/index','POST','Responsables','index');
$route->add('/responsables/listAll','GET','Responsables','listAll');
$route->add('/responsables/show','GET','Responsables','show');
$route->add('/responsables/delete','DELETE','Responsables','delete');
$route->add('/responsables/update','PUT','Responsables','update');
$route->add('/responsables/create','POST','Responsables','create');

$route->add('/producciones/index','POST','Producciones','index');
$route->add('/producciones/show','GET','Producciones','show');
$route->add('/producciones/modelos','GET','Producciones','modelos');
$route->add('/producciones/delete','DELETE','Producciones','delete');
$route->add('/producciones/update','PUT','Producciones','update');
$route->add('/producciones/create','POST','Producciones','create');


$route->add('/clientes/index','POST','Clientes','index');
$route->add('/clientes/show','GET','Clientes','show');
$route->add('/clientes/mails','GET','Clientes','mails');
$route->add('/clientes/delete','DELETE','Clientes','delete');
$route->add('/clientes/update','PUT','Clientes','update');
$route->add('/clientes/create','POST','Clientes','create');
$route->add('/clientes/list','GET','Clientes','nameList');

$route->add('/pedidosespeciales/index','POST','Pedidosespeciales','index');
$route->add('/pedidosespeciales/show','GET','Pedidosespeciales','show');
$route->add('/pedidosespeciales/pagos','GET','Pedidosespeciales','pagos');
$route->add('/pedidosespeciales/delete','DELETE','Pedidosespeciales','delete');
$route->add('/pedidosespeciales/update','PUT','Pedidosespeciales','update');
$route->add('/pedidosespeciales/create','POST','Pedidosespeciales','create');

$route->add('/ventas/index','POST','Ventas','index');
$route->add('/ventas/show','GET','Ventas','show');
$route->add('/ventas/pagos','GET','Ventas','pagos');
$route->add('/ventas/devoluciones','GET','Ventas','devoluciones');
$route->add('/ventas/delete','DELETE','Ventas','delete');
$route->add('/ventas/create','POST','Ventas','create');
$route->add('/ventas/update','PUT','Ventas','update');
$route->add('/ventas/addPago','POST','Ventas','addPago');
$route->add('/ventas/addNota','POST','Ventas','addNota');
$route->add('/ventas/deletePago','DELETE','Ventas','deletePago');


$route->add('/notas/index','POST','Notas','index');
$route->add('/notas/delete','DELETE','Notas','delete');
$route->add('/notas/create','POST','Notas','create');


$route->add('/colaImpresion/index','GET','colaImpresion','index');
$route->add('/colaImpresion/delete','DELETE','colaImpresion','delete');
$route->add('/colaImpresion/deletePedido','DELETE','colaImpresion','deletePedido');
$route->add('/colaImpresion/deleteProduccion','DELETE','colaImpresion','deleteProduccion');
$route->add('/colaImpresion/create','POST','colaImpresion','create');



$route->add('/usuarios/index','GET','Usuarios','index');
$route->add('/usuarios/show','GET','Usuarios','show');
$route->add('/usuarios/delete','DELETE','Usuarios','delete');
$route->add('/usuarios/update','PUT','Usuarios','update');
$route->add('/usuarios/create','POST','Usuarios','create');


$route->add('/resumen/index','POST','Resumen','index');

$route->add('/gastos/index','POST','Gastos','index');
$route->add('/gastos/delete','DELETE','Gastos','delete');
$route->add('/gastos/create','POST','Gastos','create');

$route->submit();
?>