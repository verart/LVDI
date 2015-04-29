<?php
class ResumenController extends AppController {

	var $name = "Resumen";
	var $uses = array('Resumen');
	

	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('resumen', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
				
			$opciones = array('conditions'=>array()); 	
			$opciones['conditions']['created >']= (isset($_POST['desde']) && ($_POST['desde'] != ''))? $_POST['desde']:date("Y/m/d");  
			$opciones['conditions']['created <']= (isset($_POST['hasta']) && ($_POST['hasta'] != ''))? $_POST['hasta']:date("Y/m/d");  
			
			$resumen = $this->Resumen->getResumen($opciones); 
			echo $this->json('Resumen', $resumen);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}

	//GETDETALLE retorna el detalle de un item
	function getDetalle(){
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('resumen', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
				
			$opciones['conditions'][] = $_POST['fp'];

			if(isset($_POST['desde']) && ($_POST['desde'] != ''))
				$opciones['conditions']['created >']=$_POST['desde'];
			if(isset($_POST['hasta']) && ($_POST['hasta']!= ''))
				$opciones['conditions']['created <']= $_POST['hasta'];
				
			$detalle = $this->Resumen->getDetalle($opciones); 
			
			echo $this->json('Detalle', $detalle);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}

	}
		
	
}
?>