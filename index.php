<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html ng-app='app' xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<head>
	<meta charset="utf-8" />
	
	<base href="/LVDI/">
	
	<title>LVDI</title>

   
</head>
<body ng-controller="ApplicationController">

	<div id="alertGlobal" style="z-index: 6000;">
		<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">
			{{ alert.msg }}			
		</alert>
	</div>
	
	<div id="infoIndexSession" class="sessionInfo" ng-if="(usuario.getUserName()!= '')" >
		<p class="userName">
			Usuario: <i>{{ usuario.getUserName() }} </i>    
		</p>
		
		<p class="salir" ><a href="#!/" ng-click="logout()">Salir</a> </p>
	</div>

	      
	<div ng-view > </div>
	
	
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
 	<script type="text/javascript" src="scripts/main.js"></script>
 	<script type="text/javascript" src="scripts/services/services.js"></script>
	<script type="text/javascript" src="scripts/config.js"></script>
	<script type="text/javascript" src="scripts/filters/filters.js"></script>
	<script type="text/javascript" src="scripts/directives/directives.js"></script>
	<script type="text/javascript" src="scripts/services/loginServices.js"></script>	
	<script type="text/javascript" src="scripts/services/productosServices.js"></script>
	<script type="text/javascript" src="scripts/services/pedidosServices.js"></script>
	<script type="text/javascript" src="scripts/services/produccionesServices.js"></script>
	<script type="text/javascript" src="scripts/services/clientesServices.js"></script>
	<script type="text/javascript" src="scripts/services/commonServices.js"></script>	
	<script type="text/javascript" src="scripts/services/ventasServices.js"></script>
	<script type="text/javascript" src="scripts/services/colaImpresionServices.js"></script>	
	<script type="text/javascript" src="scripts/services/usuariosServices.js"></script>
  	<script type="text/javascript" src="scripts/controllers/loginCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/modalConfirmCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/modalPdfProduccionCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/modalPdfPedidoCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/modalPdfPedidoNotAdminCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/modalPdfClientesMailsCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/productosCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/pedidosCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/produccionesCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/clientesPMCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/responsablesCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/clientesCtrl.js"></script>	
	<script type="text/javascript" src="scripts/controllers/ventasCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/colaImpresionCtrl.js"></script>	
	<script type="text/javascript" src="scripts/controllers/reportesCtrl.js"></script>	
	<script type="text/javascript" src="scripts/controllers/usuariosCtrl.js"></script>	
	



	
	<!-- Incluimos estilos -->	
	<link rel="stylesheet" href='bootstrap/dist/css/bootstrap.min.css' type="text/css" />
	<link rel="stylesheet" href='bootstrap/dist/css/bootstrap-editable.css' type="text/css" />
    <link rel="stylesheet" href='lib/angular-xeditable/css/xeditable.css' type="text/css" />
    <link rel="stylesheet" href='css/comunes.css' type="text/css" />
    <link rel="stylesheet" href='css/login.css' type="text/css" />
    <link rel="stylesheet" href='css/productos.css' type="text/css" />
    <link rel="stylesheet" href='css/pedidos.css' type="text/css" />
    <link rel="stylesheet" href='css/producciones.css' type="text/css" />
    <link rel="stylesheet" href='css/ventas.css' type="text/css" />
    <link rel="stylesheet" href='css/impresion.css' type="text/css" />
  
</body>
</html>