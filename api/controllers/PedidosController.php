<?php
class PedidosController extends AppController {

	var $name = "Pedidos";
	var $uses = array('Pedidos', 'ClientesPMAcceso');
	

	function index() {
		
		try {
			
			
			if (!$this->PermisosComponent->puedeAcceder('pedidos', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			if(isset($_POST['estado']) && ($_POST['estado']!= ''))
				if($_POST['estado'] == 'Entregado')
					$opciones = array('conditions'=>array('estado'=>array('Entregado-Pago','Entregado-Debe')));
				else
					$opciones = array('conditions'=>array('estado'=>$_POST['estado']));
			else
					$opciones = array(); 
			
			if(isset($_POST['filter']) && ($_POST['filter']!= ''))
				$opciones['conditions']['LIKE'] = array('localidad'=>$_POST['filter']);

			$pedidos = $this->Pedidos->getPedidos($opciones, $_POST['pag']); 
			
			echo $this->json('Pedidos', $pedidos);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	
	
	
	
	
	/**
	* SHOW
	* Muestra el detalle del pedido con id idPedido
	*/
	function show($idPedido) {
		try {
			if (!$this->PermisosComponent->puedeAcceder('pedidos', 'show'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$pedido = $this->Pedidos->getPedidoPorId($idPedido); 
			echo $this->json('', $pedido); 

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	/**
	* MODELOS
	* Muestra los modelos del pedido con id idPedido
	*/
	function modelos($idPedido) {
		try {	
			if (!$this->PermisosComponent->puedeAcceder('pedidos', 'show'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$pedido = $this->Pedidos->getModelos($idPedido); 
			echo $this->json('', $pedido); 

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
			if (!$this->PermisosComponent->puedeAcceder('pedidos', 'show'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$pedido = $this->Pedidos->getPagos($idPedido); 
			echo $this->json('', $pedido); 

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	
	
	/**
	* CREATE
	* Crea un pedido.
	* Params (POST): pedidos = array([FP], [bonificacion], clientesPM_id, [estado], fecha, total, modelos=array(cantidad,estado,id)
	*/
	function create() {
		try {
			if (!$this->PermisosComponent->puedeAcceder('pedidos', 'create'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			$params = (isset($_POST['pedido']))? $_POST['pedido'] : array();
	
			// Campos obligatorios
			if (!$this->parametrosRequeridosEn(array('clientesPM_id', 'estado', 'fecha', 'total'), $params))
				throw new BadRequestException('Los datos del pedido están incompletos'); 
				
			$pedido = array(
				'clientesPM_id'=>$params['clientesPM_id'],
				'estado'=>$params['estado'],
				'fecha'=>$params['fecha'],
				'total'=>$params['total']);
			
			if (isset($params['FP'])) $pedido['FP'] = $params['FP'];
			
			$pedido['bonificacion'] = (isset($params['bonificacion']))?$params['bonificacion']:0;				
			if(isset($params['fecha_entrega'])) $pedido['fecha_entrega'] = $params['fecha_entrega'];
			
			$mod = isset($params['modelos'])?$params['modelos']:array();
			$pagos = isset($params['pagos'])?$params['pagos']:array();

			$res =  $this->Pedidos->setPedido($pedido,$mod,$pagos);
	
			if(!($res['success'])){
				throw new BadRequestException($res['msg']);
			}
			
			// Retorna la info del pedido actualizado	
			echo $this->json('Pedido', $this->Pedidos->getPedidoPorId($res['pedidos_id']));

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}

	
	/**
	* CONFIRMPEDIDO
	* Crea el pedido, elimina el acceso del usuario
	* Params (POST): pedidos = array([FP], [bonificacion], clientesPM_id, [estado], fecha, total, modelos=array(cantidad,estado,id)
	*/
	function confirmarPedido(){
		try{

			$token = (isset($_POST['token']))? $_POST['token'] :'';
			$infoToken = $this->ClientesPMAcceso->getToken($token);
			if($infoToken['success'] != 1)
				throw new ForbiddenException('No tiene permiso para realizar esta acción.');

			$params = (isset($_POST['pedido']))? $_POST['pedido'] : array();

			// Campos obligatorios
			if (!$this->parametrosRequeridosEn(array('clientesPM_id','fecha','total'), $params))
				throw new BadRequestException('Los datos del pedido están incompletos'); 
				
			$pedido = array(
				'clientesPM_id'=>$params['clientesPM_id'],
				'estado'=>'Pendiente',
				'fecha'=>$params['fecha'],
				'bonificacion'=>$params['bonificacion'],
				'total'=>$params['total']
			);
			
			if(isset($params['nota'])) $pedido['nota'] = '(Pedido realizado por el cliente) - '.$params['nota'];

			$mod = isset($params['modelos'])?$params['modelos']:array();
			$res =  $this->Pedidos->setPedido($pedido,$mod, array());		

			if(!$res['success'])
				throw new BadRequestException('Hubo un error al crear el pedido.');

			$this->ClientesPMAcceso->deleteToken($token);
			echo $this->json('Pedido', $this->Pedidos->getPedidoPorId($res['pedidos_id']));

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
		
			if (!$this->PermisosComponent->puedeAcceder('pedidos','update'))
				throw new ForbiddenException('No tiene permiso para actualizar pedidos'); 
			
			$params = getPutParameters();
			$params = (isset($params['pedido']))? $params['pedido'] : array();
	
			// Campos obligatorios
			if (!$this->parametrosRequeridosEn(array('clientesPM_id', 'estado', 'fecha', 'total', 'id'), $params))
				throw new BadRequestException('Los datos del pedido están incompletos');
			
			//Datos del pedido
			$ped = array(	'id'=>$params['id'],
							'clientesPM_id'=>$params['clientesPM_id'], 
							'total'=>$params['total'],  
							'estado'=>$params['estado'], 
							'bonificacion'=>$params['bonificacion'],
							'fecha'=>$params['fecha']);
							
			if(isset($params['FP'])) $ped['FP'] = $params['FP'];
			if(isset($params['fecha_entrega'])) $ped['fecha_entrega'] = $params['fecha_entrega'];
			if(isset($params['nota'])) $ped['nota'] = $params['nota'];
			
			$pagos = isset($params['pagos'])?$params['pagos']:array();

			
			// UPDATE de pedido
			$res = $this->Pedidos->setPedido($ped,$params['modelos'], $pagos);
				
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
			
			
			// DELETE de modelos del pedido
			if(!empty($params['mod2delete']))
				foreach($params['mod2delete'] as $field => $value){
					$result = $this->Pedidos->removeModelo($value['id']);
					
					if(!$result['success'])
						throw new BadRequestException($result['msg']);									
			}
			
			
			// DELETE de pagos del pedido
			if(!empty($params['pagos2delete']))
				foreach($params['pagos2delete'] as $field => $value){
					$result = $this->Pedidos->removePago($value['id']);
					
					if(!$result['success'])
						throw new BadRequestException($result['msg']);									
			}

			echo $this->json('Pedido', $this->Pedidos->getPedidoPorId($res['pedidos_id']));

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
		
			if (!$this->PermisosComponent->puedeAcceder('pedidos', 'delete'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$this->Pedidos->delete($idPedido);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	

	
	
}
?>