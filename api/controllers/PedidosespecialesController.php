<?php
class PedidosespecialesController extends AppController {

	var $name = "Pedidosespeciales";
	var $uses = array('Pedidosespeciales');
	

	function index() {
		
		try {
			
			
			if (!$this->PermisosComponent->puedeAcceder('pedidosespeciales', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			if(isset($_POST['estado']) && ($_POST['estado']!= ''))
				if($_POST['estado'] == 'Entregado')
					$opciones = array('conditions'=>array('estado'=>array('Entregado-Pago','Entregado-Debe')));
				else
					$opciones = array('conditions'=>array('estado'=>$_POST['estado']));
			else
					$opciones = array(); 
			
			if(isset($_POST['filter']) && ($_POST['filter']!= ''))
				$opciones['conditions']['LIKE'] = array('clientes_id'=>$_POST['filter']);

			$pedidos = $this->Pedidosespeciales->getPedidos($opciones, $_POST['pag']); 
			
			echo $this->json('Pedidos especiales', $pedidos);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	
	
	
	
	
	/**
	* SHOW
	* Muestra el detalle del pedido especial con id idPedido
	*/
	function show($idPedido) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('pedidosespeciales', 'show'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$pedido = $this->Pedidosespeciales->getPedidoPorId($idPedido); 
			echo $this->json('Pedido especial', $pedido); 

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	/**
	* PAGOS
	* Muestra los pagos del pedido con id idPedido
	*/
	function pagos($idPedido) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('pedidosespeciales', 'show'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$pagos = $this->Pedidosespeciales->getPagos($idPedido); 
			echo $this->json('', $pagos); 

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	
	
	/**
	* CREATE
	* Crea un pedido.
	* Params (POST): pedidos = array([bonificacion], clientes_id, [estado], created, total, descripcion,[pagos=array(monto,bonificacion,[id],created)]
	*/
	function create() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('pedidosespeciales', 'create'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 


			$params = (isset($_POST['pedido']))? $_POST['pedido'] : array();
	
			// Campos obligatorios
			if (!$this->parametrosRequeridosEn(array('clientes_id', 'estado', 'created', 'total','descripcion'), $params))
				throw new BadRequestException('Los datos del pedido están incompletos'); 

			$pedido = array(
				'clientes_id'=>$params['clientes_id'],
				'descripcion'=>$params['descripcion'],
				'estado'=>$params['estado'],
				'created'=>$params['created'],
				'total'=>$params['total']);
						
			$pedido['bonificacion'] = (isset($params['bonificacion']))?$params['bonificacion']:0;				
			if(isset($params['fecha_entrega'])) $pedido['fecha_entrega'] = $params['fecha_entrega'];
			$params['pagos']= (isset($params['pagos']))?$params['pagos']:array();
			
			$res =  $this->Pedidosespeciales->setPedido($pedido, $params['pagos']);
	
			if(!($res['success'])){
				throw new BadRequestException($res['msg']);
			}
			
			// Retorna la info del pedido actualizado	
			echo $this->json('El pedido fue creado', $this->Pedidosespeciales->getPedidoPorId($res['id']));

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}

	
	
	
	
	
	
	/**
	* UPDATE
	* Actualiza un pedido.
	* Params (POST): array([FP], [bonificacion], clientesPM_id, [estado], fecha, total, modelos=array(cantidad,estado,id)
	*/
	function update() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('pedidosespeciales','update'))
				throw new ForbiddenException('No tiene permiso para actualizar pedidos'); 
			
			$params = getPutParameters();
			
			$params = (isset($params['pedido']))? $params['pedido'] : array();
	
			// Campos obligatorios
			if (!$this->parametrosRequeridosEn(array('clientes_id', 'estado', 'created', 'total', 'id','descripcion'), $params))
				throw new BadRequestException('Los datos del pedido están incompletos');
			
			//Datos del pedido
			$ped = array(	'id'=>$params['id'],
							'clientes_id'=>$params['clientes_id'], 
							'total'=>$params['total'],  
							'estado'=>$params['estado'], 
							'bonificacion'=>$params['bonificacion'],
							'descripcion'=>$params['descripcion'],
							'created'=>$params['created']);
							
			if(isset($params['fecha_entrega'])) $ped['fecha_entrega'] = $params['fecha_entrega'];
			$pagos = (isset($params['pagos']))?$params['pagos']:array();
						
			// UPDATE de pedido
			$res = $this->Pedidosespeciales->setPedido($ped, $pagos);
				
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
			
			
			// DELETE de pagos del pedido
			if(!empty($params['pagos2delete']))
				foreach($params['pagos2delete'] as $field => $value){
					$result = $this->Pedidosespeciales->removePago($value['id']);
					
					if(!$result['success'])
						throw new BadRequestException($result['msg']);									
			}

			echo $this->json('Pedido especial', $this->Pedidosespeciales->getPedidoPorId($res['id']));

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	/*******
	* DELETE
	* Elimina un pedido.
	* Params (DELETE): $idPedido
	*/
	function delete($idPedido) {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('pedidosespeciales', 'delete'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$this->Pedidosespeciales->delete($idPedido);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	

	
	
}
?>