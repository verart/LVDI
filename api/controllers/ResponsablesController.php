<?php
class ResponsablesController extends AppController {

	var $name = "Responsables";
	var $uses = array('Responsables');
	

	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('responsables', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$responsables = $this->Responsables->getResponsables();
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
	
	
	
	function update() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('responsables', 'update'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
				
			$params = getPutParameters();	

			// Campos obligatorios
			if ( !$this->parametrosRequeridosEn(array('nombre'), $params) )
				throw new BadRequestException('Debe completar los campos obligatorios'); 
				
			$this->Responsables->setResponsable($params);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	function delete($idResponsable) {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('responsables', 'delete'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$this->Responsables->delResponsable($idResponsable);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
}
?>