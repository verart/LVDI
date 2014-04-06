<?php
class ClientesPMController extends AppController {

	var $name = "ClientesPM";
	var $uses = array('ClientesPM');
	
	
	
	

	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('clientesPM', 'index'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			$clientes = $this->ClientesPM->getClientes();
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

			$clientes = $this->ClientesPM->getClientesNames();
		
			echo $this->json('Clientes por mayor', $clientes);

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

			// Campos obligatorios
			if ( !$this->parametrosRequeridosEn(array('nombre'), $params) )
				throw new BadRequestException('Debe completar los campos obligatorios'); 
			
			$this->ClientesPM->setCliente($params);

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