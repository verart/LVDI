<?php
class ColaImpresionController extends AppController {

	var $name = "Impresiones";
	var $uses = array('ColaImpresion');
	

	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('ColaImpresion', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			$imp = $this->ColaImpresion->getProductos(); 
			
			echo $this->json('ColaImpresion', $imp);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
		
	/*******
	* CREATE
	* Agrega un producto a imprimir.
	* Params (POST): modelos_id, [pedidos_id]
	*/
	function create() {
		
		try {
			$idModelo = $_POST['modelos_id'];
			$idPedido = (isset($_POST['pedidos_id']))? $_POST['pedidos_id'] : null;
			
		
			if (!$this->PermisosComponent->puedeAcceder('ColaImpresion', 'create'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$res = $this->ColaImpresion->set($idModelo, $idPedido);
			
			if(!$res['success'])
				throw new BadRequestException($res['msg']);
				
			echo $this->json('ColaImpresion', $res['ColaImpresion']);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
		
	/*******
	* DELETE
	* Elimina un producto a imprimir.
	* Params (DELETE): $idImpresion
	*/
	function delete($idImpresion) {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('ColaImpresion', 'delete'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$this->ColaImpresion->delete($idImpresion);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	

	
	
}
?>