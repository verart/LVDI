<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html ng-app='app' xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" charset="utf-8" />
	<base href="/LVDI/">
	<title>LVDI</title>
</head>
<body ng-controller="ApplicationController">


	<div ng-include ng-if="(activeTab != 'login')" src="'templates/navbar.html'"></div>
	
	 <!-- Agregamos primero jQuery antes que angular es una buena practica -->
	<script type="text/javascript" src="lib/javascript/jquery.js"></script>
	<script type="text/javascript" src="lib/javascript/jquery.form.js"></script>
	<script type="text/javascript" src="lib/javascript/angular.min.js"></script>
	<script type="text/javascript" src="lib/javascript/angular-route.min.js"></script>
	<script type="text/javascript" src="lib/javascript/angular-sanitize.min.js"></script>
	<script type="text/javascript" src="bootstrap/dist/js/bootstrap.min.js"></script>
	<script type="text/javascript" src="bootstrap/dist/js/bootstrap-editable.min.js"></script>
	<script type="text/javascript" src="bootstrap/ui-bootstrap-0.10.0.js"></script>
	<script type="text/javascript" src="lib/angular-xeditable/js/xeditable.min.js"></script>
	<script type="text/javascript" src="lib/javascript/jspdf/jspdf.js"></script>
	<script type="text/javascript" src="lib/javascript/jspdf/jspdf.plugin.standard_fonts_metrics.js"></script>
  
	<script type="text/javascript" src="lib/javascript/barcode/jquery-barcode.js"></script> 
	
	<script type="text/javascript" src="lib/javascript/jquery.mockjax.js"></script> 
	
	
	<!-- Propios -->
	<script type="text/javascript" src="scripts/constants.js"></script>
 	<script type="text/javascript" src="scripts/main.min.js"></script>
 	<script type="text/javascript" src="scripts/services/services.min.js"></script>
	<script type="text/javascript" src="scripts/config.min.js"></script>
	<script type="text/javascript" src="scripts/filters/filters.min.js"></script>
	<script type="text/javascript" src="scripts/directives/directives.min.js"></script>

	<script type="text/javascript" src="scripts/services/clientesServices.min.js"></script>
	<script type="text/javascript" src="scripts/services/colaImpresionServices.min.js"></script>
	<script type="text/javascript" src="scripts/services/commonServices.min.js"></script>	
	<script type="text/javascript" src="scripts/services/gastosServices.min.js"></script>
	<script type="text/javascript" src="scripts/services/loginServices.min.js"></script>	
	<script type="text/javascript" src="scripts/services/pedidosespecialesServices.min.js"></script>
	<script type="text/javascript" src="scripts/services/pedidosServices.min.js"></script>
	<script type="text/javascript" src="scripts/services/produccionesServices.min.js"></script>
	<script type="text/javascript" src="scripts/services/productosServices.min.js"></script>
	<script type="text/javascript" src="scripts/services/resumenServices.min.js"></script>
	<script type="text/javascript" src="scripts/services/usuariosServices.min.js"></script>	
	<script type="text/javascript" src="scripts/services/ventasServices.min.js"></script>	
	
	<script type="text/javascript" src="scripts/controllers/categoriasCtrl.min.js"></script>
	<script type="text/javascript" src="scripts/controllers/clientesCtrl.min.js"></script>	
	<script type="text/javascript" src="scripts/controllers/clientesPMCtrl.min.js"></script>
	<script type="text/javascript" src="scripts/controllers/colaImpresionCtrl.min.js"></script>	
	<script type="text/javascript" src="scripts/controllers/gastosCtrl.min.js"></script>	
	<script type="text/javascript" src="scripts/controllers/loginCtrl.min.js"></script>
	<script type="text/javascript" src="scripts/controllers/modalConfirmCtrl.min.js"></script>
	<script type="text/javascript" src="scripts/controllers/modalPdfClientesMailsCtrl.min.js"></script>
	<script type="text/javascript" src="scripts/controllers/modalPdfPedidoCtrl.min.js"></script>
	<script type="text/javascript" src="scripts/controllers/modalPdfPedidoNotAdminCtrl.min.js"></script>
	<script type="text/javascript" src="scripts/controllers/modalPdfProduccionCtrl.min.js"></script>
	<script type="text/javascript" src="scripts/controllers/pedidosCtrl.min.js"></script>
	<script type="text/javascript" src="scripts/controllers/pedidosdeclientesCtrl.min.js"></script>
	<script type="text/javascript" src="scripts/controllers/pedidosespecialesCtrl.min.js"></script>
	<script type="text/javascript" src="scripts/controllers/produccionesCtrl.min.js"></script>
	<script type="text/javascript" src="scripts/controllers/productosCtrl.min.js"></script>
	<script type="text/javascript" src="scripts/controllers/productosparapedidosCtrl.min.js"></script>
	<script type="text/javascript" src="scripts/controllers/responsablesCtrl.min.js"></script>
	<script type="text/javascript" src="scripts/controllers/resumenCtrl.min.js"></script>	
	<script type="text/javascript" src="scripts/controllers/seguimientoCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/usuariosCtrl.min.js"></script>	
	<script type="text/javascript" src="scripts/controllers/ventasCtrl.min.js"></script>

	<!-- Incluimos estilos -->	
	<link rel="stylesheet" href='bootstrap/dist/css/bootstrap.min.css' type="text/css" />
	<link rel="stylesheet" href='bootstrap/dist/css/bootstrap-editable.css' type="text/css" />
    <link rel="stylesheet" href='lib/angular-xeditable/css/xeditable.css' type="text/css" />
    <link rel="stylesheet" href='css/comunes.min.css' type="text/css" />
    <link rel="stylesheet" href='css/login.min.css' type="text/css" />
    <link rel="stylesheet" href='css/productos.min.css' type="text/css" />
    <link rel="stylesheet" href='css/pedidos.min.css' type="text/css" />
    <link rel="stylesheet" href='css/producciones.min.css' type="text/css" />
    <link rel="stylesheet" href='css/ventas.min.css' type="text/css" />
    <link rel="stylesheet" href='css/impresion.min.css' type="text/css" />

		      
	<div ng-view > </div>


</body>
</html>