<?php
class ProduccionesController extends AppController {

	var $name = "Producciones";
	var $uses = array('Producciones');
	




	function index() {
	
		try {

			if (!$this->PermisosComponent->puedeAcceder('producciones', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			
			$opciones = array('page'=>$_POST['pag'],'pageSize'=>20);
			
			if(isset($_POST['filter']) && ($_POST['filter']!= ''))
				$opciones['conditions'] = array('LIKE' => array('nombre'=>$_POST['filter']));
			else
				$opciones['conditions'] = array();
				

			if (isset($_POST['estado']) && ($_POST['estado'] != '' ))
				$opciones['conditions']['P.estado'] = $_POST['estado'];


			$producciones = $this->Producciones->getProducciones($opciones); 
			echo $this->json('Producciones', $producciones);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	
	
	
	
	
	/**
	* CREATE
	* Crea una producción.
	* Params (POST): array(responsables_id, [estado], fecha, fecha_devolucion, [motivo], [nota], modelos=array(estado,id)
	*/
	function create() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('producciones', 'create'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$params = $_POST['produccion'];
			
			$prod = array(
				'fecha'=>$params['fecha'],
				'fecha_devolucion'=>$params['fecha_devolucion'],
				'responsables_id'=>$params['responsables_id'],
				'estado'=>$params['estado']);
			
			if (isset($params['nota'])) $prod['nota'] = $params['nota'];
			if (isset($params['motivo'])) $prod['motivo'] = $params['motivo'];
	

			$mod = isset($params['modelos'])?$params['modelos']:array();
			
			$res =  $this->Producciones->setProduccion($prod,$mod);

			if(!($res['success'])){
				throw new BadRequestException($res['msg']);
			}

			// Retorna la info del pedido actualizado	
			echo $this->json('Producción', $this->Producciones->getProduccionPorId($res['producciones_id']));


		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	
	/**
	* SHOW
	* Muestra el detalle de la produccion con id idProduccion
	*/
	function show($idProduccion) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('producciones', 'show'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$produccion = $this->Producciones->getProduccionPorId($idProduccion); 
			echo $this->json('', $produccion); 

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	
	
	/**
	* UPDATE
	* Actualiza una producción.
	* Params (POST): array(responsables_id, [estado], fecha, fecha_devolucion, [motivo], [nota], modelos=array(estado,id)
	*/
	function update() {
		
		try {
			
			
			if (!$this->PermisosComponent->puedeAcceder('producciones', 'update'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$params = getPutParameters();
			
			$params = (isset($params['produccion']))? $params['produccion'] : array(); 
			
			// Campos obligatorios
			if (!$this->parametrosRequeridosEn(array('id', 'fecha_devolucion', 'fecha', 'responsables_id', 'estado'), $params))
				throw new BadRequestException('Los datos de la producción están incompletos'); 
			
			$prod = array(
				'id'=>$params['id'],
				'fecha'=>$params['fecha'],
				'fecha_devolucion'=>$params['fecha_devolucion'],
				'responsables_id'=>$params['responsables_id'],
				'estado'=>$params['estado']); 
			
			if (isset($params['nota'])) $prod['nota'] = $params['nota'];
			if (isset($params['motivo'])) $prod['motivo'] = $params['motivo'];
	
			if(!isset($params['modelos'])) $params['modelos']=array();
			if(!isset($params['mod2delete'])) $params['mod2delete']=array();
			
			
			// UPDATE de producción
			$res = $this->Producciones->setProduccion($prod,$params['modelos'],$params['mod2delete']);
				
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
			
			echo $this->json('Producción', $this->Producciones->getProduccionPorId($res['producciones_id']));

			
		} catch (Exception $e) {	
		
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	
	
	
	
	
	
	function delete($idProduccion) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('producciones', 'delete'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$this->Producciones->eliminarProduccion($idProduccion);
				
			
			

		} catch (Exception $e) {	
			
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
		
	
	/**
	* MODELOS
	* Muestra los modelos de la produccion con id idProduccion
	*/
	function modelos($idProduccion) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('producciones', 'show'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$prod = $this->Producciones->getModelos($idProduccion); 
			echo $this->json('', $prod); 

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
}
?>