<?php
class VentasController extends AppController {

	var $name = "Ventas";
	var $uses = array('Ventas');
	

	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('ventas', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
				
			if(isset($_POST['desde']) && ($_POST['desde'] != '')){
				if(isset($_POST['hasta']) && ($_POST['hasta']!= ''))
					$opciones = array('conditions'=>array('fecha >'=> $_POST['desde'], 'fecha<'=>$_POST['hasta']));
				else{
					$opciones = array('conditions'=>array('fecha >'=> $_POST['desde']));
				}
			}else
				if(isset($_POST['hasta']) && ($_POST['hasta']!= ''))
					$opciones = array('conditions'=>array('fecha<'=>$_POST['hasta']));
				else
					$opciones = array(); 

	
			$pedidos = $this->Ventas->getVentas($opciones); 
			
			echo $this->json('Ventas', $pedidos);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	
	
	
	
	
	/**
	* SHOW
	* Muestra el detalle de la venta con id idVenta
	*/
	function show($idVenta) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('ventas', 'show'))
				throw new ForbiddenException('No tiene permiso para ver una venta.'); 
			
			$venta = $this->Ventas->getVentaPorId($idVenta); 
			echo $this->json('', $venta); 

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	
	
	
	/**
	* CREATE
	* Crea una venta.
	* Params (POST): array([FP], [bonificacion], [montoFavor], fecha, total, modelos=array(cantidad,id)
	*/
	function create() {
		
		try {
		
			if (!$this->PermisosComponent->puedeEditar('ventas', 'create'))
				throw new ForbiddenException('No tiene permiso para crear una venta.'); 


			$params = (isset($_POST['venta']))? $_POST['venta'] : array();
	
			// Campos obligatorios
			if (!$this->parametrosRequeridosEn(array('fecha', 'total'), $params))
				throw new BadRequestException('Los datos de la venta están incompletos'); 
				
					
			$params = $_POST['venta'];

			$venta = array(
				'fecha'=>$params['fecha'],
				'total'=>$params['total']);
			
			if (isset($params['FP'])) $venta['FP'] = $params['FP'];
			if (isset($params['montoFavor'])) $venta['montoFavor'] = $params['montoFavor'];
			
			$venta['bonificacion'] = (isset($params['bonificacion']))?$params['bonificacion']:0;				
			
			$mod = isset($params['modelos'])?$params['modelos']:array();
			
			$res =  $this->Ventas->setVenta($venta,$mod);
	
			if(!($res['success'])){
				throw new BadRequestException($res['msg']);
			}
			
			// Retorna la info de la venta creada
			$venta['id'] = $res['ventas_id'];
			$venta['modelos'] = $mod; 
			echo $this->json('Venta', $venta);
			

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}

	
	
	
	
	
	/*******
	* DELETE
	* Elimina una venta.
	* Params (DELETE): $idVenta
	*/
	function delete($idVenta) {
		
		try {
		
			if (!$this->PermisosComponent->puedeEditar('venta', 'delete'))
				throw new ForbiddenException('No tiene permiso para eliminar una venta'); 
			
			$this->Ventas->eliminarVenta($idVenta);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	

	
	
}
?>