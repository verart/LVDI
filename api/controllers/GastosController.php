<?php
class GastosController extends AppController {

	var $name = "Gastos";
	var $uses = array('Gastos');
	

	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('gastos', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			$opciones = array('conditions'=>array()); 	
			$opciones['conditions']['created >']= (isset($_POST['desde']) && ($_POST['desde'] != ''))? $_POST['desde']:date("Y/m/d");  
			$opciones['conditions']['created <']= (isset($_POST['hasta']) && ($_POST['hasta'] != ''))? $_POST['hasta']:date("Y/m/d");  
			if(isset($_POST['categorias_id']) && ($_POST['categorias_id']!= ''))
				$opciones['conditions']['categorias_id']= $_POST['categorias_id'];
	
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
			if (!$this->parametrosRequeridosEn(array('created', 'categoria','monto', 'FP'), $params))
				throw new BadRequestException('Los datos del gasto están incompletos'); 
				
					
			$gasto = array(
				'created'=>$params['created'],
				'descripcion'=>$params['descripcion'],
				'FP'=>$params['FP'],
				'categorias_id'=>$params['categoria']['id'],
				'monto'=>$params['monto']);
			
			
			$res =  $this->Gastos->setGasto($gasto);
	
			if(!($res['success'])){
				throw new BadRequestException($res['msg']);
			}
			
			// Retorna la info de la venta creada
			$gasto['id'] = $res['gasto']['id'];
			$gasto['categoria'] = $params['categoria']['nombre'];
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