<?php
class VentasController extends AppController {

	var $name = "Ventas";
	var $uses = array('Ventas');
	

	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('ventas', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			$pedidos = $this->Ventas->getVentas(); 
			
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
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
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
	* Params (POST): array([FP], [bonificacion],  fecha, total, modelos=array(cantidad,id)
	*/
	function create() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('ventas', 'create'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 


			$params = (isset($_POST['venta']))? $_POST['venta'] : array();
	
			// Campos obligatorios
			if (!$this->parametrosRequeridosEn(array('fecha', 'total'), $params))
				throw new BadRequestException('Los datos de la venta están incompletos'); 
				
					
			$params = $_POST['venta'];

			$venta = array(
				'fecha'=>$params['fecha'],
				'total'=>$params['total']);
			
			if (isset($params['FP'])) $venta['FP'] = $params['FP'];
			
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
		
			if (!$this->PermisosComponent->puedeAcceder('venta', 'delete'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$this->Ventas->delete($idVenta);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	

	
	
}
?>