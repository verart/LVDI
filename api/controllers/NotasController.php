<?php
class NotasController extends AppController {

	var $name = "Notas";
	var $uses = array('Notas');
	

	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('notas', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
				
			if(isset($_POST['desde']) && ($_POST['desde'] != '')){
				if(isset($_POST['hasta']) && ($_POST['hasta']!= ''))
					$opciones = array('conditions'=>array('created >'=> $_POST['desde'], 'created<'=>$_POST['hasta']));
				else{
					$opciones = array('conditions'=>array('created >'=> $_POST['desde']));
				}
			}else
				if(isset($_POST['hasta']) && ($_POST['hasta']!= ''))
					$opciones = array('conditions'=>array('created<'=>$_POST['hasta']));
				else
					$opciones = array(); 

	
			$notas = $this->Notas->getNotas($opciones); 
			
			echo $this->json('Notas', $notas);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	
	
	
	
	
	/**
	* CREATE
	* Crea una nota.
	* Params (POST): array(fecha, nota)
	*/
	function create() {
		
		try {
		
			if (!$this->PermisosComponent->puedeEditar('notas', 'create'))
				throw new ForbiddenException('No tiene permiso para crear una nota.'); 


			$params = (isset($_POST))? $_POST : array();
	
			// Campos obligatorios
			if (!$this->parametrosRequeridosEn(array('created', 'nota'), $params))
				throw new BadRequestException('Los datos de la nota están incompletos'); 
				
					
			$nota = array(
				'created'=>$params['created'],
				'nota'=>$params['nota']);
			
			
			$res =  $this->Notas->setNota($nota);
	
			if(!($res['success'])){
				throw new BadRequestException($res['msg']);
			}
			
			// Retorna la info de la venta creada
			$nota['id'] = $res['nota']['id'];
			echo $this->json('Nota', $nota);
			

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}

	
	
	
	
	
	/*******
	* DELETE
	* Elimina una nota.
	* Params (DELETE): $idNota
	*/
	function delete($idNota) {
		
		try {
		
			if (!$this->PermisosComponent->puedeEditar('nota', 'delete'))
				throw new ForbiddenException('No tiene permiso para eliminar una nota'); 
			
			$this->Notas->delNota($idNota);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	

	
	
}
?>