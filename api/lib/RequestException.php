<?php

class RequestException extends Exception {
	
	const BAD_REQUEST	 	= 400;
    const UNAUTHORIZED		= 401;
    const FORBIDDEN 		= 403;
	const NOT_FOUND 		= 404;
	
	private $_data;
	private $_code;
	private $_msg;
	
	// Redefinir la excepción, por lo que el mensaje no es opcional
    public function __construct($msg, $code, $data = array()) {
    	
		$this->_data = $data;
		$this->_code = $code;
		$this->_msg = $msg;
		
	    parent::__construct();
    }

    public function getMsg() {
        return $this->_msg;
    }
	
	public function getData() {
        return $this->_data;
    }
	
	public function getSatusCode() {
		return $this->_code;
	}
}

class BadRequestException extends RequestException {
	
	public function __construct($message) {
    	
		parent::__construct($message, self::BAD_REQUEST);
    }
	
}

class UnauthorizedException extends RequestException {
	
	public function __construct($message="Necesita autenticarse para acceder al recurso") {
    	
		parent::__construct($message, self::UNAUTHORIZED);
    }
	
}

class ForbiddenException extends RequestException {
	
	public function __construct($message="No cuenta con permisos para acceder al recurso") {
    	
		parent::__construct($message, self::FORBIDDEN);
    }
	
}


?>