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
	* Params (POST): array([FP], [bonificacion], [montoFavor], created, total, [nota], modelos=array(cantidad,id)
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
			if (isset($params['nota'])) $venta['nota'] = $params['nota'];
				
			$venta['bonificacion'] = (isset($params['bonificacion']))?$params['bonificacion']:0;				
			
			$mod = isset($params['modelos'])?$params['modelos']:array();
			$pagos = isset($params['pagos'])?$params['pagos']:array();

			$res =  $this->Ventas->setVenta($venta,$mod, $pagos);
	
			if(!($res['success'])){
				throw new BadRequestException($res['msg']);
			}
			
			// Retorna la info de la venta creada
			$venta = $res['venta'];

			echo $this->json('La venta fue guardada', $venta);
			

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}

	
	
	
	
	/**
	* UPDATE
	* Actualiza una venta.
	* Params (POST): array(id, [FP], [bonificacion], [montoFavor], created, total, deuda [nota], modelos=array(cantidad,id), pagos =array(created,monto, bonificacion),  mod2delete, pagos2delete
	*/
	function update() {
		
		try {
		
			if (!$this->PermisosComponent->puedeEditar('ventas','update'))
				throw new ForbiddenException('No tiene permiso para actualizar ventas'); 
			
			$params = getPutParameters();
			
			$params = (isset($params['venta']))? $params['venta'] : array();
	
			// Campos obligatorios
			if (!$this->parametrosRequeridosEn(array('created', 'total', 'id'), $params))
				throw new BadRequestException('Los datos de la venta están incompletos');
			
			//Datos del pedido
			$venta = array(	'id'=>$params['id'],
							'total'=>$params['total'], 
							'nota'=>$params['nota'],
							'deuda'=>$params['deuda'], 
							'bonificacion'=>$params['bonificacion'],
							'montoFavor'=>$params['montoFavor'],
							'created'=>$params['created']);
							
			if(isset($params['FP'])) $ped['FP'] = $params['FP'];
			if(isset($params['nota'])) $ped['nota'] = $params['nota'];
			
			
			// UPDATE de venta			
			$mod = isset($params['modelos'])?$params['modelos']:array();
			$pagos = isset($params['pagos'])?$params['pagos']:array();
			$pagos2delete = isset($params['pagos2delete'])?$params['pagos2delete']:array();
			$mod2delete = isset($params['mod2delete'])?$params['mod2delete']:array();

			$res =  $this->Ventas->setVenta($venta,$mod, $pagos, $mod2delete, $pagos2delete );
				
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);

			echo $this->json('La venta fue actualizada.', $this->Ventas->getVentaPorId($venta['id']));

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
	* ADDPAGO
	* Guarda el pago $pago recibido 
	*/
	function addPago() {
		
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

	
	/**
	* ADDNOTA
	* Guarda el pago $nota recibido 
	*/
	function addNota() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('ventas', 'create'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			if(isset($_POST['nota']) && isset($_POST['idVenta'])){
				
				$res = $this->Ventas->addNota($_POST['nota'], $_POST['idVenta']); 
			
				if(!$res['success'])	
					throw new BadRequestException($res['msg']);
			
	
				echo $this->json('La nota fue actualizada');
 
				
			}else
				throw new BadRequestException('Los datos de la nota están incompletos.');
			
			

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
}
?>