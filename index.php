<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html ng-app='app' xmlns="http://www.w3.org/1999/xhtml" xml:lang="es" lang="es">
<head profile="http://gmpg.org/xfn/11">
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" charset="utf-8" />
	<base href="/LVDI/">
	<title>LVDI</title>
</head>
<body ng-controller="ApplicationController">

	<div id="alertGlobal" style="z-index: 6000;">
		<alert ng-repeat="alert in alerts" type="alert.type" close="alert.close()">
			{{ alert.msg }}			
		</alert>
	</div>

	<nav class="navbar navbar-default" role="navigation" ng-if="(activeTab != 'index')&&(usuario.getUserName() != '')&&(usuario.getUserName() != undefined)">
	  <div class="container-fluid">
	
	    <div class="navbar-header">
	      <img src="img/LVDI_s.png" data-alt="Los Vados del Isen" style="width:32px; margin-right: 14px;" />
	    </div>
	    
	    
	    <!-- Collect the nav links, forms, and other content for toggling -->
	    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
	      <ul id="menu" class="nav navbar-nav">
	        <li id="productos" ng-if="(usuario.getUserRole()=='admin')||(usuario.getUserRole()=='taller')||(usuario.getUserRole()=='local')" 
	        	ng-class="{true:'active'}[(activeTab == 'productos')]">
	        	<a href="#!/productos" ng-click="refreshActiveTab('productos')">Productos</a></li>
	        <li class="dropdown" ng-if="(usuario.getUserRole()=='admin')||(usuario.getUserRole()=='taller')" 
	        	ng-class="{true:'active'}[(activeTab == 'pedidos')||(activeTab == 'clientesPM')]">
	        	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Pedidos <span class="caret"></span></a>
          		<ul class="dropdown-menu" role="menu">
	            <li><a href="#!/pedidos" ng-click="refreshActiveTab('pedidos')">Pedidos</a></li>
	            <li><a href="#!/clientesPM" ng-click="refreshActiveTab('clientesPM')">Clientes por mayor</a></li>
	            <li><a href="#!/productosparapedidos" ng-click="refreshActiveTab('productosparapedidos')">Productos para pedidos</a></li>
	        	</ul>
	        </li>
	        <li class="dropdown" ng-if="(usuario.getUserRole()=='admin')||(usuario.getUserRole()=='local')"  
	        	ng-class="{true:'active'}[(activeTab == 'producciones')||(activeTab == 'responsables')]">
	        	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Producciones <span class="caret"></span></a>
          		<ul class="dropdown-menu" role="menu">
	            <li><a href="#!/producciones" ng-click="refreshActiveTab('producciones')">Producciones</a></li>
	            <li><a href="#!/responsables" ng-click="refreshActiveTab('responsables')">Responsables</a></li>
	          </ul>
	        </li>
	        <li class="dropdown" ng-if="(usuario.getUserRole()=='admin')||(usuario.getUserRole()=='local')"  
	        	ng-class="{true:'active'}[(activeTab == 'clientes')||(activeTab == 'pedidosespeciales')]">
	        	<a href="#" class="dropdown-toggle" data-toggle="dropdown">Pedidos especiales <span class="caret"></span></a>
          		<ul class="dropdown-menu" role="menu">
	            <li><a href="#!/pedidosespeciales" ng-click="refreshActiveTab('pedidosespeciales')">Pedidos especiales</a></li>
	            <li><a href="#!/clientes" ng-click="refreshActiveTab('clientes')">Clientes</a></li>
	          </ul>
	        </li>
	        <li id="ventas" ng-if="(usuario.getUserRole()=='admin')||(usuario.getUserRole()=='local') || (usuario.getUserRole()=='taller')" 
	        	ng-class="{true:'active'}[(activeTab == 'ventas')]">
	        	<a href="#!/ventas" ng-click="refreshActiveTab('ventas')">Ventas</a></li>
	        <li id="ventas" ng-if="(usuario.getUserRole()=='admin')||(usuario.getUserRole()=='local') || (usuario.getUserRole()=='taller')" 
	        	ng-class="{true:'active'}[(activeTab == 'ventas')]">
	        <li id="colaImpresion" ng-if="(usuario.getUserRole()=='admin')||(usuario.getUserRole()=='local')" 
	        	ng-class="{true:'active'}[(activeTab == 'colaImpresion')]">
	        	<a href="#!/colaImpresion" ng-click="refreshActiveTab('colaImpresion')">Cola de impresión</a></li>
	        <li id="usuarios" ng-if="(usuario.getUserRole()=='admin')" 
	        	ng-class="{true:'active'}[(activeTab == 'usuarios')]">
	        	<a href="#!/usuarios" ng-click="refreshActiveTab('usuarios')">Usuarios</a></li>
	        <li id="resumen" ng-if="(usuario.getUserRole()=='cuentas')"
	        	ng-class="{true:'active'}[(activeTab == 'resumen')]">
	        	<a href="#!/resumen" ng-click="refreshActiveTab('resumen')">Resumen</a></li>
	        <li id="ventas"ng-if="(usuario.getUserRole()=='cuentas')"
		        ng-class="{true:'active'}[(activeTab == 'ventas')]">
	        	<a href="#!/ventas" ng-click="refreshActiveTab('ventas')">Ventas</a></li> 
	        <li id="gastos"ng-if="(usuario.getUserRole()=='cuentas')"
	        	ng-class="{true:'active'}[(activeTab == 'gastos')]">
	        	<a href="#!/gastos" ng-click="refreshActiveTab('gastos')">Gastos</a></li> 
	      </ul>
	      <ul class="nav navbar-nav navbar-right">        
	        <li class="dropdown">
	        	<a href="#" class="dropdown-toggle" data-toggle="dropdown">{{ usuario.getUserName()}} <span class="caret"></span></a>
          		<ul class="dropdown-menu" role="menu">
	            <li><a class="salir" href="#!/index" ng-click="logout()">Salir</a></li>
	          </ul>
	        </li>
	      </ul>
	    </div><!-- /.navbar-collapse -->
	  </div><!-- /.container-fluid -->
	</nav>

	


	      
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
	<script type="text/javascript" src="scripts/services/pedidosespecialesServices.js"></script>
	<script type="text/javascript" src="scripts/services/produccionesServices.js"></script>
	<script type="text/javascript" src="scripts/services/clientesServices.js"></script>
	<script type="text/javascript" src="scripts/services/commonServices.js"></script>	
	<script type="text/javascript" src="scripts/services/ventasServices.js"></script>
	<script type="text/javascript" src="scripts/services/colaImpresionServices.js"></script>	
	<script type="text/javascript" src="scripts/services/usuariosServices.js"></script>	
	<script type="text/javascript" src="scripts/services/resumenServices.js"></script>
	<script type="text/javascript" src="scripts/services/gastosServices.js"></script>

	
  	<script type="text/javascript" src="scripts/controllers/loginCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/modalConfirmCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/modalPdfProduccionCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/modalPdfPedidoCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/modalPdfPedidoNotAdminCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/modalPdfClientesMailsCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/productosCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/productosparapedidosCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/pedidosCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/pedidosdeclientesCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/pedidosespecialesCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/produccionesCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/clientesPMCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/responsablesCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/clientesCtrl.js"></script>	
	<script type="text/javascript" src="scripts/controllers/ventasCtrl.js"></script>
	<script type="text/javascript" src="scripts/controllers/colaImpresionCtrl.js"></script>	
	<script type="text/javascript" src="scripts/controllers/reportesCtrl.js"></script>	
	<script type="text/javascript" src="scripts/controllers/usuariosCtrl.js"></script>	
	<script type="text/javascript" src="scripts/controllers/resumenCtrl.js"></script>	
	<script type="text/javascript" src="scripts/controllers/gastosCtrl.js"></script>	


	
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