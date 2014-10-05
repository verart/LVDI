<?php
class Gastos extends AppModel {
	
	public $name = "Gastos";
	public $primaryKey = 'id';	
	
	
	/** 
	 * GETGASTOS
	 * Retorna todos los gastos
	 * params (array) $opciones = array([conditions])
	 */
	function getGastos($opciones = array()) {
	
		$conditions = (isset($opciones['conditions']))? $this->_buildConditions($opciones['conditions']): "";	
		
		
		$sql = "SELECT G.* 
				FROM gastos G 
				 $conditions
				 ORDER BY G.created DESC, G.id DESC"; 
				 				
	   	$query = $this->con->prepare($sql, array(), MDB2_PREPARE_RESULT);    	
	   	$query = $query->execute();	
	   	
		$results = $query->fetchAll();
		
		return $results;
	}
	
	
	
	
	
	
	
	/**
	* SETGASTO
	* $gasto = array( 'created','descripcion','monto' )
	*/
	function setGasto($gasto){
		
		try{
			
			if(!isset($gasto['id'])){ 
				
				if(!$this->create($gasto))				
					throw new BadRequestException('Hubo un error al crear el gasto.');
					
				$gasto['id'] = $this->getLastId();

							
			}else{
			
				if(!$this->update($gasto, array('id'=>$gasto['id'])))
					throw new BadRequestException('Hubo un error al actualizar el gasto.');				
					
			}
			
			return array('success'=>true, 'gasto'=>$gasto);

		} catch (Exception $e) {

			return array('success'=>false, 'msg'=>$e->getMsg());

		}
		
		
	}
	

	
	/**
	 * DELGASTO
	 * Elimina el gasto que coincide con el id
	 * @param $idGasto
	 */
	function delGasto($idGasto){
		
		try{
		
			if(!$this->delete($idGasto))
				throw new BadRequestException('Hubo un error al eliminar el gasto.');
				
					
		}catch (Exception $e) {
			echo $e->getMsg();

		}
			
	}
	
	
	
}
?>