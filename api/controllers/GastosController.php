<?php
class GastosController extends AppController {

	var $name = "Gastos";
	var $uses = array('Gastos');
	

	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('gastos', 'index'))
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

	
			$gastos = $this->Gastos->getGastos($opciones); 
			
			echo $this->json('Gastos', $gastos);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	
	
	
	
	
	/**
	* CREATE
	* Crea un gasto.
	* Params (POST): array(created, descripcion, monto)
	*/
	function create() {
		
		try {
		
			if (!$this->PermisosComponent->puedeEditar('gastos', 'create'))
				throw new ForbiddenException('No tiene permiso para crear un gasto.'); 


			$params = (isset($_POST))? $_POST : array();
	
			// Campos obligatorios
			if (!$this->parametrosRequeridosEn(array('created', 'descripcion', 'monto', 'FP'), $params))
				throw new BadRequestException('Los datos del gasto están incompletos'); 
				
					
			$gasto = array(
				'created'=>$params['created'],
				'descripcion'=>$params['descripcion'],
				'FP'=>$params['FP'],
				'monto'=>$params['monto']);
			
			
			$res =  $this->Gastos->setGasto($gasto);
	
			if(!($res['success'])){
				throw new BadRequestException($res['msg']);
			}
			
			// Retorna la info de la venta creada
			$gasto['id'] = $res['gasto']['id'];
			echo $this->json('Gasto', $gasto);
			

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}

	
	
	
	
	
	/*******
	* DELETE
	* Elimina un gasto.
	* Params (DELETE): $idGasto
	*/
	function delete($idGasto) {
		
		try {
		
			if (!$this->PermisosComponent->puedeEditar('gastos', 'delete'))
				throw new ForbiddenException('No tiene permiso para eliminar un gasto.'); 
			
			$this->Gastos->delGasto($idGasto);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	

	
	
}
?>