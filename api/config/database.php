<?php
class Database {  
	
	private static $con = null;
  	
  	/**
	 * Crea la conexion con la base de datos 
	 */
	static function getConnection() {
		if(self::$con === null) {
		 
			
			$mysql_host = 'localhost';
			$mysql_database = 'lvdi';
			$usuario = 'root';
			$clave = 'root';

			
			
/*
			$mysql_host = "mysql6.000webhost.com";
			$mysql_database = "a7827408_lvdi";
			$usuario = "a7827408_LVDIbd";
			$clave = "LVDI2918";
*/
			

			$dsn = "mysql://$usuario:$clave@$mysql_host/$mysql_database"; 
			$options = array ('debug'=>2,'persistent' => true,'portability' => MDB2_PORTABILITY_NONE);
			self::$con = MDB2::factory($dsn, $options);

			if (@PEAR::isError(self::$con)) die(self::$con->getMessage());
			
			self::$con->setFetchMode(MDB2_FETCHMODE_ASSOC);
		}
		
		return self::$con; 
	}
}
?>