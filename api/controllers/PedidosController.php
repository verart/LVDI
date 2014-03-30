<?php
class PedidosController extends AppController {

	var $name = "Pedidos";
	var $uses = array('Pedidos');
	

	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('pedidos', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			$pedidos = $this->Pedidos->getPedidos(); 
			
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
	* CREATE
	* Crea un pedido.
	* Params (POST): array([FP], [bonificacion], clientesPM_id, [estado], fecha, total, modelos=array(cantidad,estado,id)
	*/
	function create() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('pedidos', 'create'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 


			$params = (isset($_POST['pedido']))? $_POST['pedido'] : array();
	
			// Campos obligatorios
			if (!$this->parametrosRequeridosEn(array('clientesPM_id', 'estado', 'fecha', 'total'), $params))
				throw new BadRequestException('Los datos del pedido están incompletos'); 
				
					
			$params = $_POST['pedido'];

			$pedido = array(
				'clientesPM_id'=>$params['clientesPM_id'],
				'estado'=>$params['estado'],
				'fecha'=>$params['fecha'],
				'total'=>$params['total']);
			
			if (isset($params['FP'])) $pedido['FP'] = $params['FP'];
			
			$pedido['bonificacion'] = (isset($params['bonificacion']))?$params['bonificacion']:0;				
			if(isset($params['fecha_entrega'])) $pedido['fecha_entrega'] = $params['fecha_entrega'];
			
			$mod = isset($params['modelos'])?$params['modelos']:array();
			
			$res =  $this->Pedidos->setPedido($pedido,$mod);
	
			if(!($res['success'])){
				throw new BadRequestException($res['msg']);
			}
			
			// Retorna la info del pedido actualizado	
			$pedido['id'] = $res['pedidos_id'];
			$pedido['modelos'] = $mod; 
			$pedido['cliente'] = $params['clientesPM'];		
			echo $this->json('Pedido', $pedido);
			

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
		
			if (!$this->PermisosComponent->puedeAcceder('pedidos', 'update'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$params = getPutParameters();
			
			$params = (isset($params['pedido']))? $params['pedido'] : array();
	
			// Campos obligatorios
			if (!$this->parametrosRequeridosEn(array('clientesPM_id', 'estado', 'fecha', 'total', 'id', 'bonificacion'), $params))
				throw new BadRequestException('Los datos del pedido están incompletos');
			
			//Datos del producto
			$ped = array(	'id'=>$params['id'],
							'clientesPM_id'=>$params['clientesPM_id'], 
							'total'=>$params['total'],  
							'estado'=>$params['estado'], 
							'bonificacion'=>$params['bonificacion'],
							'fecha'=>$params['fecha']);
							
			if(isset($params['FP'])) $ped['FP'] = $params['FP'];
			if(isset($params['fecha_entrega'])) $ped['fecha_entrega'] = $params['fecha_entrega'];
			if(isset($params['nota'])) $ped['nota'] = $params['nota'];
			
			
			// UPDATE de pedido
			$res = $this->Pedidos->setPedido($ped,$params['modelos']);
				
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
			
			
			// DELETE de modelos del pedido
			if(!empty($params['mod2delete']))
				foreach($params['mod2delete'] as $field => $value){
					$result = $this->Pedidos->removeModelo($value['id']);
					
					if(!$result['success'])
						throw new BadRequestException($result['msg']);									
			}



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