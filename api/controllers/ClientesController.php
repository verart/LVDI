<?php
class ClientesController extends AppController {

	var $name = "Clientes";
	var $uses = array('Clientes');
	
	
	
	
	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('clientes', 'index'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			$opciones = array('order'=>'nombre ASC','page'=>$_POST['pag'],'pageSize'=>15);
			
			if(isset($_POST['filter'])&& ($_POST['filter']!= ''))
				$opciones['conditions']= array('LIKE' => array('nombre'=>$_POST['filter']));
			
			$clientes = $this->Clientes->getClientes($opciones);
			
			echo $this->json('Clientes', $clientes);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	
	
	
	
	function show($idCliente) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('clientes', 'show'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			$cliente = $this->Clientes->getClientePorId($idCliente); 
			echo $this->json('', $cliente[0]); 

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	function create() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('clientes', 'create'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
				
			$params = (isset($_POST['clientes']))? $_POST['clientes'] : array();

			// Campos obligatorios
			if ( !$this->parametrosRequeridosEn(array('nombre', 'email'), $params) )
				throw new BadRequestException('Debe completar los campos obligatorios'); 
			
			$res = $this->Clientes->setCliente($params);
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
				
	
			echo $this->json('El cliente ha sido agregado.', $res['clientes']);
				

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	
	
	
	function update() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('clientes', 'update'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
				
			$params = getPutParameters();	
			$params = (isset($params['clientes']))? $params['clientes'] : array();
			
			unset($params['$$hashKey']); 
			
			// Campos obligatorios
			if ( !$this->parametrosRequeridosEn(array('nombre','id'), $params) )
				throw new BadRequestException('Debe completar los campos obligatorios'); 
			
			$res = $this->Clientes->setCliente($params);			
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
				
	
			echo $this->json('clientes', $res['clientes']);
					

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	function delete($idCliente) {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('clientes', 'delete'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			$this->Clientes->delCliente($idCliente);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	function mails() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('clientes', 'show'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			$mails = $this->Clientes->getMails(); 
			echo $this->json('Mails', $mails); 

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
}
?>