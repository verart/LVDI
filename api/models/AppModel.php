<?php
class AppModel {
	var $name;
	var $con;
	var $table;
	var $alias;
	var $hasMany = array();
		
	function __construct($name = '') {
		
		if(!empty($name))
			$this->name = $name;
			
		$this->con = Database::getConnection();
		
		$this->table = strtolower($this->name);
		
		$this->_loadHasMany();
		
	}
	
	
	/***************** CRUD *************************/
	function _escapeField($field) {
		return "`$field`";
	}
	
	
	
	/**
	 * Crea un string para meter en un sql a partir de un array de campo => valor
	 */
	function _buildConditions($conditions = array()) {
		$result = " WHERE "; 
		foreach($conditions as $field => $value)
		
			if($field == 'LIKE'){
				$result .= "(";
				foreach($value as $field2 => $value2)
					$result .= " ( $field2 LIKE '%$value2%' ) OR ";	
				$result .= " (1 <> 1) ) AND " ;	
			}	
			else{		
				if(is_array($value)){ 			
					$result .= "(";
					foreach($value as $field2 => $value2)
						$result .= " ( $field= '$value2' ) OR ";
					$result .= " (1 <> 1) ) AND " ;
				}else		
					$result .= " $field= '$value' AND ";
			}		
			
			$result .= " (1 = 1) ";
		
		return $result;		
	}
	
	
	function _loadHasMany(){
	
		foreach($this->hasMany as $model) {
			if (file_exists("models/$model.php")) {
				include_once("models/$model.php");
				$this->{$model} = new $model(); 
			} else {
				$this->{$model} = new AppModel($model);
			}
		}
		
	}
	
	/**
	 * Realiza un insert en una tabla
	 * {array} $record Registro de campos a insertar. Viene de la forma array(campo => valor).
	 */
	function create($record) {
		
		$keys = array_map(array($this, '_escapeField'),array_keys($record));
		$fields = implode(',',$keys);

		$record_values = array_values($record);
		$values = array();
		foreach($record_values as $value) {
			if($value == 'null')
				$values[] = $value;
			else
				$values[] = "'$value'";
		}
		
		$values = implode(',',$values);

		$sql = " INSERT INTO ".$this->table." ($fields) VALUES ($values) ";

		$query = $this->con->query($sql);

		if(@PEAR::isError($query, $cod)) {  
			return false;
		}

		return true;
	}
	
	/**
	 * Lee un registro de la tabla
	 * {array} $fields. Array de campos que quieren devolverse 
	 */
	function read($id,$fields = array()) {
		if(empty($fields))
			$fields = '*';
		else
			$fields = "`".implode($fields,"`,`")."`";
			
		$sql = "SELECT $fields FROM ".$this->table." WHERE id = $id";
		
		$result = $this->con->query($sql);
		
		return $result;
	}
	
	
	/**
	 * Hace un select en la tabla.
	 * @params options 
	 * 		{array} fields: campos que se ponen en el select.
	 * 		{array} conditions: condiciones para el select.
	 * 		{string} order: campo que ordena el reultado.
	 */
	function readAll($options = array()) {
	
		$fields = (empty($options['fields']))?'*':implode(',',$options['fields']);
		$conditions = (empty($options['conditions']))?'':$this->_buildConditions($options['conditions']);
		$order = (empty($options['order']))?'': 'ORDER BY '.$this->table.'.'.$options['order'];

		$sql = "SELECT $fields FROM ".$this->table." $conditions $order";
		
		$sth = $this->con->prepare($sql);
		$sth = $sth->execute();
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $result;
	}
	
	/**
	 * Hace un select en la tabla y devuelve las pagin.
	 * @params options 
	 * 		{array} fields: campos que se ponen en el select.
	 * 		{array} conditions: condiciones para el select.
	 * 		{string} order: campo que ordena el reultado.
	 * 		{int} page: pagina
	 * 		{int} pageSize: tamaño pagina
	 */
	function readPage($options = array()) { 
		$fields = (empty($options['fields']))?'*':explode($options['fields'],',');
		$conditions = (empty($options['conditions']))?'':$this->_buildConditions($options['conditions']);
		$order = (empty($options['order']))?'': ' ORDER BY '.$this->table.'.'.$options['order'];		
		$limit =(empty($options['page']))?'':' LIMIT '.(($options['page']-1)*$options['pageSize']).', '.$options['pageSize'];


	
		$sql = "SELECT $fields FROM ".$this->table." $conditions $order $limit"; 
		$sth = $this->con->prepare($sql);
		$sth = $sth->execute();
		
		$result = $sth->fetchAll(PDO::FETCH_ASSOC);
		
		return $result;
	}
	
	
	
	/**
	 * Lee un registro de la tabla
	 * {array} $conditions Condiciones para el update. Viene de la forma array(campo => valor).
	 */
	function update($record,$conditions) {
		$sql = " UPDATE $this->table SET ";
		foreach($record as $field => $value) {
			if($value == 'null')
				$sql .= " $field = $value,";
			else
				$sql .= " $field = '".$value."',";
		}
		$sql = substr($sql,0,strlen($sql)-1);// Saco la ultima coma
		$sql .= $this->_buildConditions($conditions);
				
		$query = $this->con->query($sql);

		if(@PEAR::isError($query)) {
		    return false;
		}
		return true;	
	}
	
	function delete($id) {
		$sql = "DELETE FROM ".$this->table." WHERE id = $id";
		
		$result = $this->con->query($sql);
		
		if(@PEAR::isError($result)) {
		    return false;
		}
		return true;	
	}
	
	/**
	 * Inicia una transacción
	 */
	function beginTransaction() {
		$this->con->beginTransaction();
	}
	
	/**
	 * Commitea una transacción
	 */
	function commitTransaction() {
		$this->con->commit();
	}
	
	/**
	 * Hace rollback de una transacción
	 */
	function rollbackTransaction() {
		$this->con->rollback();
	}
	
	
	function getLastId(){
		$id = $this->con->lastInsertID($this->table, 'id');
		return $id;
		
	}
}?>