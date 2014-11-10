<?php
class ColaImpresionController extends AppController {

	var $name = "Impresiones";
	var $uses = array('ColaImpresion');
	

	function index($userId) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('ColaImpresion', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			$imp = $this->ColaImpresion->getProductos($userId); 
			echo $this->json('ColaImpresion', $imp);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
		
	/*******
	* CREATE
	* Agrega un producto a imprimir.
	* Params (POST): modelos_id, [pedidos_id], [belongsTo]
	*/
	function create() {
		
		try {
			$idModelo = $_POST['modelos_id'];
			$idPedido = (isset($_POST['pedidos_id']))? $_POST['pedidos_id'] : null;
			$idProduccion = (isset($_POST['producciones_id']))? $_POST['producciones_id'] : null;
			$belongsTo = (isset($_POST['belongsTo']))? $_POST['belongsTo'] : NULL;

			
		
			if (!$this->PermisosComponent->puedeAcceder('ColaImpresion', 'create'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			$res = $this->ColaImpresion->set($idModelo, $idPedido, $idProduccion, $belongsTo);
			
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
				throw new ForbiddenException('No tiene permiso para quitar los productos a imprimir.'); 
			
			$this->ColaImpresion->delete($idImpresion);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}


	/*******
	* DELETEPEDIDO
	* Elimina los productos a imprimir de un pedido.
	* Params (DELETE): $idPedido
	*/
	function deletePedido($idPedido) {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('ColaImpresion', 'delete'))
				throw new ForbiddenException('No tiene permiso para quitar los productos a imprimir.'); 
			
			$this->ColaImpresion->deletePedido($idPedido);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	
	/*******
	* DELETEPRODUCCION
	* Elimina los productos a imprimir de una produccion.
	* Params (DELETE): $idPedido
	*/
	function deleteProduccion($idProduccion) {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('ColaImpresion', 'delete'))
				throw new ForbiddenException('No tiene permiso para quitar los productos a imprimir.'); 
			
			$this->ColaImpresion->deleteProduccion($idProduccion);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
}
?>