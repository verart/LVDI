<?php
class SesionController extends AppController {

	var $name = "Sesion";
	var $uses = array('Usuarios');
	
	/**
	 * LOGIN (/sesion/login)
	 * Realiza el login de un usuario a partir del usuario y clave.
	 * @param POST $usuario
	 * @param POST $clave
	 * @return
	 * 		{ 
	 *			MSG: "Usuario logueado con éxito",
	 *			DATA: {  
	 *				usuario { 
	 *					nombre: “nombre completo”
	 *			 	}  
	 *			}
	 *		} 
     *
	 */
	function login() {

		try {
			
			$params = $_POST;

			// Campos obligatorios
			if (!$this->parametrosRequeridosEn(array('usuario', 'clave'), $params))
				throw new BadRequestException('Parámetros incompletos'); 
			
			// Caracteres no permitidos
			if((preg_match('/[^0-9A-Za-z._!*@]/',$params['usuario'])) || (preg_match('/[^0-9A-Za-z._!*@]/',$params['clave'])))
				throw new BadRequestException('Se han ingresado caracteres no permitidos.');

			$usuario = $this->Usuarios->getUsuario($params['usuario'],$params['clave']);

			if(empty($usuario))
				throw new BadRequestException('Combinación de usuario y clave incorrecta');
			
			
			// Devuelvo la respuesta
			$respuesta = array (
					'nombre' => utf8_encode($usuario['nombre']),
					'id' => $usuario['id'],
					'perfil'=>$usuario['perfil']
			);
			
			$this->SesionComponent->guardarEnSesion('usuario', $respuesta);
			
			echo $this->json('Usuario logueado con éxito', $usuario['nombre']);

		} catch (Exception $e) {	
		
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		
		}
	}
	
	/**
	 * LOGOUT (/sesion/logout)
	 * Realiza el logout del usuario. Elimina el token asociado
	 * @return
	 * 		{ 
	 *			MSG: "Se ha cerrado la sesión",
	 *			DATA: 
	 *		} 
	 */
	function logout() {
		
		try {
			if (!$this->estaAutenticado())
				throw new BadRequestException('Parámetros incompletos'); 
			
			// Devuelvo la respuesta
			echo $this->json("Se ha cerrado la sesión");
		} catch (Exception $e) {
				
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
			
		}
	}

}
?>