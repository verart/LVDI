<?php
class Categorias extends AppModel {
	
	public $name = "Categorias";
	public $primaryKey = 'id';	

	/**
	 * NOTUSED
	 * Retorna si esta siendo usado en algun gasto
	 * @param (int) $idC
	 */
	function notUsed($idC) {
		$sql = "SELECT *
				FROM gastos G 
				WHERE G.categorias_id = ?";
							
    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
		$query = $query->execute(array($idC));
		$categoriasGastos = $query->fetchAll();	
	
		return (empty($categoriasGastos));
	}


	/**
	* SETCATEGORIA
	* $categoria = array( ['id'], 'nombre' )
	*/
	function setCategoria($categoria){
		
		try{
			$cat = $categoria;
			unset($categoria['$$hashKey']);
			
			if(isset($categoria['nombre'])) $categoria['nombre'] = utf8_decode($categoria['nombre']);
				
			if(!isset($categoria['id'])){ 
				if(!$this->create($categoria)){			
					throw new BadRequestException('Hubo un error al crear la categoria.');
				}
				$cat['id'] = $this->getLastId();			
			}else{
				if(!$this->update($categoria, array('id'=>$categoria['id'])))
					throw new BadRequestException('Hubo un error al actualizar la categoria.');		
			}
		
			return array('success'=>true, 'categoria'=>$cat);

		} catch (Exception $e) {
			return array('success'=>false, 'msg'=>$e->getMsg());
		}		
	}


		/**
	 * Retorna el cliente que coincide con $nombre
	 * @param $nombre
	 */
	function getCategoriasPorNombre($nombre) {

		$nombre = strtolower ($nombre);
		$nombre = str_replace("%20", " ", $nombre);
		$text = '%'.$nombre.'%';

		$sql = "SELECT *   
				FROM categorias c
				WHERE (c.nombre like '".$text."') 
				ORDER BY c.nombre  
				LIMIT 0,10" ; 
	
		try{
	    	$query = $this->con->prepare($sql, array('integer'), MDB2_PREPARE_RESULT);	
			$query = $query->execute(); 
			
			//Se formatea el resultado para que queden los datos del producto con su arreglo de modelos.
			$results = $query->fetchAll();

			if(!empty($results)){ 
				for ($i=0 ; $i<count($results) ; $i++) {
					$results[$i]['nombre'] = utf8_encode($results[$i]['nombre']);
				}
				return array('success'=>true, 'categorias'=>$results); 
			}else
				throw new BadRequestException('No existe categoria que coincida con '.$nombre.'.');

		}catch(Exception $e){
			return array('success'=>false,'msg'=>$e->getMsg());
		}
	}

}
?>