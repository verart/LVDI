<?php
class ResumenController extends AppController {

	var $name = "Resumen";
	var $uses = array('Resumen');
	

	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('resumen', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
				
			if(isset($_POST['desde']) && ($_POST['desde'] != '')){
				if(isset($_POST['hasta']) && ($_POST['hasta']!= '')){
					$opciones = array('conditions'=>array('created >'=> $_POST['desde'], 'created<'=>$_POST['hasta']));

				}else{
					$opciones = array('conditions'=>array('created >'=> $_POST['desde']));
				}
			}else
				if(isset($_POST['hasta']) && ($_POST['hasta']!= '')){
					$opciones = array('conditions'=>array('created <'=> $_POST['hasta']));
				}else
					$opciones = array(); 

	
			$resumen = $this->Resumen->getResumen($opciones); 
			
			echo $this->json('Resumen', $resumen);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
		
	
}
?>