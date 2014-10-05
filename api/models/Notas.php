<?php
class Notas extends AppModel {
	
	public $name = "Notas";
	public $primaryKey = 'id';	
	
	
	/** 
	 * GETNOTAS
	 * Retorna todos las notas
	 * params (array) $opciones = array([conditions])
	 */
	function getNotas($opciones = array()) {
	
		$conditions = (isset($opciones['conditions']))? $this->_buildConditions($opciones['conditions']): "";	
				
		$opciones['order'] = "created DESC, id DESC";
		
		$results = $this->readAll($opciones);
		
		return $results;
		
	}
	
	
	
	
	
	
	
	/**
	* SETNOTA
	* $nota = array( 'id', 'nota' )
	*/
	function setNota($nota){
		
		try{
			
			if(!isset($nota['id'])){ 
				
				if(!$this->create($nota))				
					throw new BadRequestException('Hubo un error al crear la nota.');
					
				$nota['id'] = $this->getLastId();

							
			}else{
			
				if(!$this->update($nota, array('id'=>$nota['id'])))
					throw new BadRequestException('Hubo un error al actualizar la nota.');				
					
			}
			
			return array('success'=>true, 'nota'=>$nota);

		} catch (Exception $e) {

			return array('success'=>false, 'msg'=>$e->getMsg());

		}
		
		
	}
	

	
	/**
	 * DELNOTA 
	 * Elimina la nota que coincide con el id
	 * @param $idNota
	 */
	function delNota($idNota){
		
		try{
		
			if(!$this->delete($idNota))
				throw new BadRequestException('Hubo un error al eliminar la nota.');
				
					
		}catch (Exception $e) {
			echo $e->getMsg();

		}
			
	}
	
	
	
}
?>