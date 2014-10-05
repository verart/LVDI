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
					$opciones = array('conditions'=>array('V.created >'=> $_POST['desde'], 'V.created<'=>$_POST['hasta']));
				else{
					$opciones = array('conditions'=>array('V.created >'=> $_POST['desde']));
				}
			}else{
				if(isset($_POST['hasta']) && ($_POST['hasta']!= ''))
					$opciones = array('conditions'=>array('V.created<'=>$_POST['hasta']));
				else
					if($_POST['conDeuda'] != 0)
						$opciones = array('conditions'=>array('V.deuda >'=> 1));
					else
						$opciones = array(); 
			}			
	
				
	
			$ventas = $this->Ventas->getVentas($opciones, $_POST['pag']); 
			
			echo $this->json('Ventas', $ventas);
			
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
	* Params (POST): array([FP], [bonificacion], [montoFavor], created, total, modelos=array(cantidad,id)
	*/
	function create() {
		
		try {
		
			if (!$this->PermisosComponent->puedeEditar('ventas', 'create'))
				throw new ForbiddenException('No tiene permiso para crear una venta.'); 

			$params = (isset($_POST['venta']))? $_POST['venta'] : array();
	
			// Campos obligatorios
			if (!$this->parametrosRequeridosEn(array('created', 'total'), $params))
				throw new BadRequestException('Los datos de la venta están incompletos'); 
				
					
			$params = $_POST['venta'];

			$venta = array(
				'created'=>$params['created'],
				'total'=>$params['total'],
				'deuda'=>$params['deuda']);
			
			if (isset($params['FP'])) $venta['FP'] = $params['FP'];
			if (isset($params['montoFavor'])) $venta['montoFavor'] = $params['montoFavor'];
			
			$venta['bonificacion'] = (isset($params['bonificacion']))?$params['bonificacion']:0;				
			
			$mod = isset($params['modelos'])?$params['modelos']:array();
			$pagos = isset($params['pagos'])?$params['pagos']:array();

			$res =  $this->Ventas->setVenta($venta,$mod, $pagos);
	
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
		
			if (!$this->PermisosComponent->puedeEditar('ventas', 'delete'))
				throw new ForbiddenException('No tiene permiso para eliminar una venta'); 
			
			$this->Ventas->eliminarVenta($idVenta);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	

	/************************************* PAGOS **********************************/
	
	
	/**
	* PAGOS
	* Muestra los pagos del pedido con id idVenta
	*/
	function pagos($idVenta) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('ventas', 'show'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$venta = $this->Ventas->getPagos($idVenta); 
			echo $this->json('', $venta); 

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	/**
	* PAGOS
	* Guarda el pago $pago recibido 
	*/
	function addPago($pago) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('ventas', 'addPago'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			if(isset($_POST['pago']) && isset($_POST['idVenta'])){
				
				$pago = $this->Ventas->addPago($_POST['pago'], $_POST['idVenta']); 
			
				echo $this->json('', $pago); 
				
			}else
				throw new BadRequestException('Los datos del pago están incompletos.');
			
			

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
		
	/*******
	* DELETE
	* Elimina un pago de la venta.
	* Params (DELETE): $idPago
	*/
	function deletePago($idPago) {
		
		try {
		
			if (!$this->PermisosComponent->puedeEditar('ventas', 'deletePago'))
				throw new ForbiddenException('No tiene permiso para eliminar un pago de la venta'); 
			
			$this->Ventas->deletePago($idPago);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}

	
	
}
?>