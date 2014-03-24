<?php
class Database {  
	
	private static $con = null;
  	
  	/**
	 * Crea la conexion con la base de datos 
	 */
	static function getConnection() {
		if(self::$con === null) {
		 
			$usuario = 'root';
			$clave = 'root';
			$bd = 'lvdi';
			$servidor = 'localhost';

			$dsn = "mysql://$usuario:$clave@$servidor/$bd"; 
			$options = array ('debug'=>2,'persistent' => true,'portability' => MDB2_PORTABILITY_NONE);
			self::$con = MDB2::factory($dsn, $options);

			if (@PEAR::isError(self::$con)) die(self::$con->getMessage());
			
			self::$con->setFetchMode(MDB2_FETCHMODE_ASSOC);
		}
		
		return self::$con; 
	}
}
?>