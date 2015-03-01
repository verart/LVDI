<?php
class ClientesPMController extends AppController {

	var $name = "ClientesPM";
	var $uses = array('ClientesPM', 'ClientesPMAcceso');
	
	
	
	

	function index() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('clientesPM', 'index'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			$opciones = array('order'=>'nombre ASC','page'=>$_POST['pag'],'pageSize'=>15);
			
			if(isset($_POST['filter'])&& ($_POST['filter']!= ''))
				$opciones['conditions']= array('LIKE' => array('nombre'=>$_POST['filter'], 'localidad'=>$_POST['filter']));


			$clientes = $this->ClientesPM->getClientes($opciones);
			echo $this->json('Clientes', $clientes);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	
	
	
	
	function show($idCliente) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('clientesPM', 'show'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			$cliente = $this->ClientesPM->getClientePorId($idCliente); 
			echo $this->json('', $cliente[0]); 

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	function clientesName() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('clientesPM', 'clientesName'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			$opciones = array('order'=>'nombre ASC', 'fields'=>array('id','nombre','bonificacion'));
			$clientes = $this->ClientesPM->getClientesNames($opciones);
		
			echo $this->json('Clientes por mayor', $clientes);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	
	
	
	
	function create() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('clientesPM', 'create'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
				
			$params = (isset($_POST['clientesPM']))? $_POST['clientesPM'] : array(); 

			// Campos obligatorios
			if ( !$this->parametrosRequeridosEn(array('nombre'), $params) )
				throw new BadRequestException('Debe completar los campos obligatorios'); 

			$res = $this->ClientesPM->setCliente($params);
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
		

			echo $this->json('clientePM', $res['clientesPM']);
				

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	
	
	
	function update() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('clientesPM', 'update'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
				
			$params = getPutParameters();	
			$params = (isset($params['clientesPM']))? $params['clientesPM'] : array();
			
			unset($params['$$hashKey']); 
			
			// Campos obligatorios
			if ( !$this->parametrosRequeridosEn(array('nombre','id'), $params) )
				throw new BadRequestException('Debe completar los campos obligatorios'); 
			
			$res = $this->ClientesPM->setCliente($params);			
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
	
			echo $this->json('clientesPM', $res['clientesPM']);
					
		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	function delete($idCliente) {
		try {
			if (!$this->PermisosComponent->puedeAcceder('clientesPM', 'delete'))
				throw new BadRequestException('No tiene permiso para acceder a esta página'); 
			
			$msg = $this->ClientesPM->delCliente($idCliente);
			echo $this->json('clientesPM', $msg);

		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}

	/*
	* Envia un mail al cliente idCliente. Genera un token para dicho cliente
	*/
	function enviarMail(){
		
		try{			
			$params = getPutParameters();	

			//Si ya tiene acceso en el sistema y es actual no se vuelve a enviar mail.
			$res =$this->ClientesPMAcceso->getCliente($params['idCliente']);
			if($res['success']){
				$fechaFin = date('Y-m-d H:i:s', strtotime ('+7 day',strtotime($res['token']['created'])) );
				if($fechaFin >= date ('Y-m-d H:i:s')) 
					throw new BadRequestException('El cliente ya tiene acceso al sitio.');
				else{ 
					if($fechaFin < date ('Y-m-d H:i:s'))
						$this->ClientesPMAcceso->deleteToken($res['token']['token']);
				}
			}

			$token = md5(uniqid(rand(), FALSE));
			$infoToken = array('clientes_id'=>$params['idCliente'], 'token'=>$token, 'created'=> date('Y-m-d H:i:s'));
			$res =$this->ClientesPMAcceso->setToken($infoToken);
			if(!$res['success'])
				throw new BadRequestException($res['msg']);

			//Mail
			$link = "<a style='font-weight:900;text-decoration:inherit;font-size:20px;color:cadetblue;margin:80px;background-color:papayawhip;padding:7px;' href='http://localhost:8888/LVDI/#!/pedidosdeclientes/".$token."'> Accedé aquí para armar tu pedido </a>";
			$mail = '<html><body style="font-family:sans-serif;font-size:15px;color:rgb(56, 56, 56);">';
			$mail .= "<p style='font-size:16px;'>".$params['saludo']."</p><p>".str_replace("\n"," </p><p> ",$params['texto'])."</p><br/>".$link.'<br/><p>'.str_replace("\n"," </p><p>",$params['despedida']).'</p>'; 
			$mail .= '</body></html>';
			//Titulo
			$titulo = "Los Vados Del Isen - Pedidos";
			//cabecera
			$headers = "MIME-Version: 1.0\r\n"; 
			$headers .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
			$headers .= "From: Los Vados Del Isen <no-replay@losvadosdelisen.com>\r\n";
			$res = mail("veroartola@gmail.com",$titulo,$mail,$headers);
			if(!$res)
				throw new BadRequestException('Mensaje no enviado');
			
			echo $this->json('El mensaje fue enviado.');

		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		} 		
		
	}

	/*
	* Chequea que el token esté activo
	*/
	function tienePermiso($token){
		try {
			$infoToken = $this->ClientesPMAcceso->getToken($token);  
			if(!$infoToken['success'])
				throw new BadRequestException($infoToken['msg']); 

			$fechaFin = strtotime ( '+7 day' , strtotime ( $infoToken['token']['created'] ) ) ;
			$nuevafecha = date ( 'Y-m-d H:i:s' , $fechaFin );
			if($nuevafecha <= date ('Y-m-d H:i:s')) {
					$this->ClientesPMAcceso->deleteToken($token);
					throw new BadRequestException('Su cuenta ha caducado.');
			}
	
			$cliente = $this->ClientesPM->getClientePorId($infoToken['token']['clientes_id']);
			$cli = array('nombre'=> $cliente[0]['nombre'], 'id'=>$cliente[0]['id']);
			
			echo $this->json('clientePM', $cli);

		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		} 
	}
	
}
?>