<?php
class CategoriasController extends AppController {

	var $name = "Categorias";
	var $uses = array('Categorias');
	
	function index() {
		try {
			if (!$this->PermisosComponent->puedeAcceder('categorias', 'index'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			$categorias = $this->Categorias->readAll();
			echo $this->json('Categorias', $categorias);

		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	function create() {
		
		try {
			if (!$this->PermisosComponent->puedeAcceder('categorias', 'create'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
				
			$params = (isset($_POST['categoria']))? $_POST['categoria'] : array();

			// Campos obligatorios
			if ( !$this->parametrosRequeridosEn(array('nombre'), $params) )
				throw new BadRequestException('Debe completar los campos obligatorios'); 
						
			$res = !$this->Categorias->setCategoria($params);
			if($res['succes'])	
				throw new BadRequestException($res['msg']);		
	
			echo $this->json('Categoria', array('id'=>$this->Categorias->getLastId(), 'nombre'=> $params['nombre'] ));

		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	function delete($idC) {
		
		try {
			if (!$this->PermisosComponent->puedeAcceder('categorias', 'delete'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			//Si esta en uso no se elimina
			if($this->Categorias->notUsed($idC))
				$this->Categorias->delete($idC);
			else
				throw new BadRequestException('La categoría está siendo utilizada en algún gasto. No puede eliminarse.'); 


		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}


	/*******************************************************************************************
	CLIENTEBYNAME
	Muestra el/las categorias /s con nombre que coincida con nombre
	*******************************************************************************************/
	function categoriasByName($nombre) {
		try {
			if (!$this->PermisosComponent->puedeAcceder('categorias', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			$res = $this->Categorias->getCategoriasPorNombre($nombre); 
			if($res['success']) 
				echo $this->json('', $res['categorias']);
			else 
				throw new BadRequestException($res['msg']); 
		} catch (Exception $e) {	
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}	
	
}
?>