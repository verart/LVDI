<?php
class ResponsablesController extends AppController {

	var $name = "Responsables";
	var $uses = array('Responsables');
	

	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('responsables', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$opciones = array('order'=>'nombre ASC','page'=>$_POST['pag'],'pageSize'=>10);
			$responsables = $this->Responsables->getResponsables($opciones);
			echo $this->json('Responsables', $responsables);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	
	
	function show($idResponsable) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('responsables', 'show'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$responsable = $this->Responsables->getResponsablePorId($idResponsable); 
			echo $this->json('', $responsable); 

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	function create() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('responsables', 'create'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
				
			$params = (isset($_POST['responsables']))? $_POST['responsables'] : array(); 

			// Campos obligatorios
			if ( !$this->parametrosRequeridosEn(array('nombre'), $params) )
				throw new BadRequestException('Debe completar los campos obligatorios'); 

			$res = $this->Responsables->setResponsable($params);
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
		

			echo $this->json('El responsable de producción fue agregado.', $res['responsables']);
				

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	
	
	
	function update() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('responsables', 'update'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
				
			$params = getPutParameters();	

			$params = (isset($params['responsables']))? $params['responsables'] : array();
			
			 unset($params['$$hashKey']);
			
			// Campos obligatorios
			if ( !$this->parametrosRequeridosEn(array('nombre'), $params) )
				throw new BadRequestException('Debe completar los campos obligatorios'); 
				
			$res = $this->Responsables->setResponsable($params);
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
				
	
			echo $this->json('El responsable de producción fue modificado.', $res['responsables']);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	function delete($idResponsable) {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('responsables', 'delete'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$res = $this->Responsables->delResponsable($idResponsable);
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
				
	
			echo $this->json('responsables', $res['responsables']);
			

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
				
		}	
	}
	
	
	
	function listAll() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('responsables', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$opciones = array('order'=>'nombre ASC', 'fields'=>array('id','nombre'));
			$responsables = $this->Responsables->getResponsablesList($opciones);
			echo $this->json('Responsables', $responsables);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
}
?>