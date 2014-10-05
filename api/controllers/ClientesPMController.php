<?php
class ClientesPMController extends AppController {

	var $name = "ClientesPM";
	var $uses = array('ClientesPM');
	
	
	
	

	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('clientesPM', 'index'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			$opciones = array('order'=>'nombre ASC','page'=>$_POST['pag'],'pageSize'=>10);
			
			if(isset($_POST['filter'])&& ($_POST['filter']!= ''))
				$opciones['conditions']= array('LIKE' => array('nombre'=>$_POST['filter'], 'localidad'=>$_POST['filter']));


			$clientes = $this->ClientesPM->getClientes($opciones);
			echo $this->json('Clientes', $clientes);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	
	
	
	
	function show($idCliente) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('clientesPM', 'show'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			$cliente = $this->ClientesPM->getClientePorId($idCliente); 
			echo $this->json('', $cliente[0]); 

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	function clientesName() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('clientesPM', 'clientesName'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			$opciones = array('order'=>'nombre ASC', 'fields'=>array('id','nombre','bonificacion'));
			$clientes = $this->ClientesPM->getClientesNames($opciones);
		
			echo $this->json('Clientes por mayor', $clientes);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	
	
	
	
	function create() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('clientesPM', 'create'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
				
			$params = (isset($_POST['clientesPM']))? $_POST['clientesPM'] : array(); 

			// Campos obligatorios
			if ( !$this->parametrosRequeridosEn(array('nombre'), $params) )
				throw new BadRequestException('Debe completar los campos obligatorios'); 

			$res = $this->ClientesPM->setCliente($params);
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
		

			echo $this->json('clientePM', $res['clientesPM']);
				

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	
	
	
	function update() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('clientesPM', 'update'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
				
			$params = getPutParameters();	
			$params = (isset($params['clientesPM']))? $params['clientesPM'] : array();
			
			unset($params['$$hashKey']); 
			
			// Campos obligatorios
			if ( !$this->parametrosRequeridosEn(array('nombre','id'), $params) )
				throw new BadRequestException('Debe completar los campos obligatorios'); 
			
			$res = $this->ClientesPM->setCliente($params);			
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
				
	
			echo $this->json('clientesPM', $res['clientesPM']);
					

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	function delete($idCliente) {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('clientesPM', 'delete'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			$this->ClientesPM->delCliente($idCliente);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
}
?>