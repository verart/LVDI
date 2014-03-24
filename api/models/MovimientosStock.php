<?php
class MovimientosStock extends AppModel {
	
	public $name = "movimientos_stock";
	public $primaryKey = 'id';	
	
	    
	/**
	 * Retorna todos los movimientos
	 */
	function getMovimientos($opciones = array()) {
	
		if(isset($opciones['fechaIni'])) $fechaIni = $opciones['fechaIni'];
		if(isset($opciones['fechaFin'])) $fechaFin = $opciones['fechaFin']; 
		
		$sql = "SELECT *
				FROM movimientos_stock MS
				INNER JOIN modelos M ON M.id = MS.modelos_id
				ORDER BY MS.created";
				
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
		
		$results = $query->fetchAll();
		
		return $results;
	}
	
	

	
	/**
	* SETMOVIMIENTO
	* $movimiento = array( 'modelo_id'=>'', 'cantidad'=>'', 'tipo'=>'', ['nota'=>''] )
	* 
	* 
	*/
	function setMovimiento($movimiento){
		
		try{
			$this->beginTransaction();
			
			$movimiento['created'] = date('Y/m/d', time());
			
			if(!$this->create($movimiento))				
				throw new BadRequestException('Hubo un error al crear el movimiento.');
							
		
			$this->commitTransaction();
			return true;

		} catch (Exception $e) {
			//echo $e->getMsg();
			$this->rollbackTransaction();
			return false;
		}
		
		
	}
	

	/**
	 * DELMOVIMIENTO 
	 * Elimina el movimiento que coincide con el id
	 * @param $idMovimiento
	 */
	function delMovimiento($idMovimiento){
		
		try{

			if (!$this->delete($idMovimiento))				
					throw new BadRequestException('Hubo un error. No se pudo eliminar el movimiento.');			
			
			$this->commitTransaction();
			
		} catch (Exception $e) {
			echo $e->getMsg();
			$this->rollbackTransaction();
		}
		
	}
	
}
?>