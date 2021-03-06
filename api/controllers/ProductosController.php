<?php
class ProductosController extends AppController {

	var $name = "Productos";
	var $uses = array('Productos', 'Modelos', 'ClientesPMAcceso');
	



	function index() {
		
		try {			
			if (!$this->PermisosComponent->puedeAcceder('productos', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			$params = getPutParameters(); 
			
			if(isset($params['enProduccion']))
				$opciones['conditions']= array('enProduccion'=>1);
			else	
				$opciones['conditions']= array(); 
				
			$prods = $this->Productos->getProductos($opciones);	 
			echo $this->json('Productos', $prods);

		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	

	function productos() {
		try {			
			if (!$this->PermisosComponent->puedeAcceder('productos', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			$opciones['conditions']= array('enProduccion'=>1);
				
			if(isset($_POST['filter']) && ($_POST['filter']!= ''))
				$opciones['conditions']['LIKE'] = array('P.nombre'=>$_POST['filter']);

			$prods = $this->Productos->getProductosBasico($opciones, $_POST['pag']);
			echo $this->json('Productos', $prods);

		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}


	
	function show($idProducto) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('productos', 'show'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$producto = $this->Productos->getProductoPorId($idProducto); 
			echo $this->json('', $producto); 

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
		
	
	/*******************************************************************************************
	PRODUCTOMODELOBYID
	Muestra monProducto-NomModelo con el precio
	*******************************************************************************************/
	function productoModeloById($idModelo) {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('productos', 'productoModelo'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$res = $this->Productos->getProductoModeloPorId($idModelo); 
			if($res['success'])
				echo $this->json('Producto', $res['producto']);
			else
				throw new BadRequestException($res['msg']); 

		} catch (Exception $e) {	
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}

	
	/*******************************************************************************************
	PRODUCTOMODELOBYNAME
	Muestra monProducto-NomModelo con el precio - chequea con la concatenacion del nombre del 
	producto y del modelo
	*******************************************************************************************/
	function productoModeloByName($nombre) {
		try {
			if (!$this->PermisosComponent->puedeAcceder('productos', 'productoModelo'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			$res = $this->Productos->getProductoModeloPorNombre($nombre); 
			if($res['success']) 
				echo $this->json('', $res['producto']);
			else 
				throw new BadRequestException($res['msg']); 
		} catch (Exception $e) {	
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}	
	
	
	/*******************************************************************************************
	* UPDATE
	* Actualiza un producto.
	* Params (PUT): array(nombre, [id], precio, modelos=array([id],nombre), mod2delete=array(id) )
	*******************************************************************************************/
	function update() {
		
		try {
		
			if (!$this->PermisosComponent->puedeAcceder('productos', 'update'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$params = getPutParameters(); 
			
			//Datos del producto
			$prod = array(	'nombre'=>$params['nombre'], 
							'precio'=>$params['precio'], 
							'id'=>$params['id'], 
							'enProduccion'=>$params['enProduccion']);
	
			// UPDATE de producto
			$res =  $this->Productos->setProducto($prod,$params['modelos']);
				
			if(!$res['success'])	
				throw new BadRequestException($res['msg']);
			
			
			// IMG del producto
			// Si se recibe un archivo del producto, se lo busca en el dir tmp y se lo ubica el el dir definitivo 	
			$urlImg='';
			if(isset($params['fileName'])){
				$urlImg = $this->saveFile($params['id'], $params['fileName']);
			}	

			// BAJA de modelos
			if(!empty($params['mod2baja']))
				foreach($params['mod2baja'] as $field => $value){
					$nota = isset($value['nota'])?$value['nota']:'';
					$result = $this->Modelos->baja($value['id'],$value['cantBaja'],$nota);
					
					if(!$result['success'])
						throw new BadRequestException($result['msg']);									
			}

			// ALTA de modelos
			if(!empty($params['mod2alta']))
				foreach($params['mod2alta'] as $field => $value){
					$result = $this->Productos->reponer($value['id'],$value['cantAlta'],'Alta de stock - Edición de producto');
					
					if(!$result['success'])
						throw new BadRequestException($result['msg']);									
			}
			
			// DELETE de modelos
			if(!empty($params['mod2delete']))
				foreach($params['mod2delete'] as $field => $value){
					$result = $this->Modelos->logicDelete($value['id']);
					
					if(!$result['success'])
						throw new BadRequestException($result['msg']);									
			}					
			
			echo $this->json('Producto', $this->Productos->getProductoPorId($res['productos_id']));
			
		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}

	/*******************************************************************************************
	* CREATE
	* Crea un producto.
	* Params (POST): array(nombre, precio, modelos=array([id],nombre), mod2delete=array(id), [fileName] )
	*******************************************************************************************/
	function create() {
		
		try {	
			if (!$this->PermisosComponent->puedeAcceder('productos', 'create'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$prod = array(
				'nombre'=>$_POST['nombre'],
				'precio'=>$_POST['precio'],
				'enProduccion'=>$_POST['enProduccion']
			);
				
			$mod = isset($_POST['modelos']) ? $_POST['modelos']:array();
			
			$res = $this->Productos->setProducto($prod,$mod);
			
			if(!($res['success'])){
				throw new BadRequestException($res['msg']);
			}
			
			// Si se recibe un archivo del producto, se lo busca en el dir tmp y se lo ubica el el dir definitivo 	
			if(isset($_POST['fileName'])){
				$this->saveFile($res['productos_id'], $_POST['fileName']);
			}

			echo $this->json('Producto', $this->Productos->getProductoPorId($res['productos_id']));

		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	/*******************************************************************************************
	* DELETE
	* Elimina un producto.
	* Params (DELETE): idProducto
	*******************************************************************************************/
	function delete($idProducto) {
		
		try {
			if (!$this->PermisosComponent->puedeAcceder('productos', 'delete'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			if($this->Productos->delProducto($idProducto))
				$this->removeFile($idProducto);

		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	

	}
	
	
	
	
	function reponer() {
		
		try {
			if (!$this->PermisosComponent->puedeAcceder('productos', 'reponer'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$idModelo = $_POST['idMod']; 
			$this->Productos->reponer($idModelo);

		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}
	
	
	
	
	
	function baja($idModelo) {
		
		$params = getPutParameters(); 	
		$nota = isset($params['nota'])? $params['nota']:'';
		
		try {		
			if (!$this->PermisosComponent->puedeAcceder('productos', 'baja'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$this->Productos->baja($idModelo,$nota);

		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	

	}
	
	
		
	function venta($idModelo) {	

		$nota = 'Venta';
		
		try {
			if (!$this->PermisosComponent->puedeAcceder('productos', 'venta'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$this->Modelos->baja($idModelo,$nota);

		} catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	

	}
	
	
	
	function productosDisponibles(){
		
		try {		
			if (!$this->PermisosComponent->puedeAcceder('productos', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			$options = array('conditions'=>array('enProduccion' => 1,'stock >' => 1));
				
			$prods = $this->Productos->getProductosNames($options);
			echo $this->json('Productos', $prods);

		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
		
		
	}



	function productosName() {
		
		try {
			
			if (!$this->PermisosComponent->puedeAcceder('productos', 'index'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
		
			$params = getPutParameters(); 
			$options = array('conditions'=>array());

			if(isset($params['enProduccion']))  $options['conditions'] = array('enProduccion' => 1);
				
			$prods = $this->Productos->getProductosNames($options);
			echo $this->json('Productos', $prods);

		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}
	
	/*******************************************************************************************
	PEDIDOSDECLIENTES
	retorna los producto disponibles para los pedidos realizados por clientes
	*******************************************************************************************/
	function pedidosdeclientes($token){

		try{
			$infoToken = $this->ClientesPMAcceso->getToken($token);

			if($infoToken['success'] != 1)
				throw new ForbiddenException('No es posible visualizar esta información');
			else{ 
				$params = getPutParameters(); 
				$opciones['conditions']= array('pedido'=>1);
						
				$prods = $this->Productos->getProductosBasico($opciones); 
				echo $this->json('Productos', $prods);
			}
		}catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}
	}


	function habilitarModelo() {
		try {
			if (!$this->PermisosComponent->puedeAcceder('productos', 'habilitar'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 
			
			$res=$this->Modelos->habilitarPorModelo($_POST['idMod'],$_POST['habilitar']);
			if(!$res['success'])
				throw new BadRequestException('No se pudo habilitar el modelo.'); 

		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}

	function habilitarProducto() {
		try {
			if (!$this->PermisosComponent->puedeAcceder('productos', 'habilitar'))
				throw new ForbiddenException('No tiene permiso para acceder a esta página'); 

			$res = $this->Modelos->habilitarPorProducto($_POST['idProd'],$_POST['habilitar']);
			if(!$res['success'])
				throw new BadRequestException('No se pudieron habilitar los modelos del producto.'); 
			
		} catch (Exception $e) {	
			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}	
	}


	/******************************************************************************************* 
	* UPLOAD
	* Sube la imagen q se emcuentra en tmp al dir de img temporales de LVDI.
	* Posteriormente, si el usuario acepta los datos cargados la imagen se gurdará definitivamente en img/productos/id_de_img.jpg
	* Si el usuario cancela la operación, se borra la imagen temporal
	*******************************************************************************************/
	function upload(){
		move_uploaded_file($_FILES["uploader"]["tmp_name"],  COMPLETE_ROOT_DIR."img/tmp/".$_FILES["uploader"]["name"]);
	}
	
	
	
	/**
	* SAVEFILE
	* Ubica el archivo con nombre fileName del tmp en el dir definitivo 
	* Retorna la url de la imagen guardada
	*/
	function saveFile($id, $fileName){    
	
		if (file_exists(COMPLETE_ROOT_DIR."img/tmp/".$fileName)){
		
			copy(COMPLETE_ROOT_DIR."img/tmp/".urlencode($fileName), COMPLETE_ROOT_DIR."img/productos/".$id.'.jpg');
			unlink(COMPLETE_ROOT_DIR."img/tmp/".urlencode($fileName));
			
			return ROOT_URL."img/productos/".$id.'.jpg';
		}else
			return '';
	}

	/**
	* REMOVEFILE
	* Elimina el archivo del producto con id $id, si existe
	*/
	function removeFile($id){    
	
		if (file_exists(COMPLETE_ROOT_DIR."img/productos/".$id.'.jpg'))
			unlink(COMPLETE_ROOT_DIR."img/productos/".$id.'.jpg');

	}


	function seguimiento(){

		try{
			if (!$this->PermisosComponent->puedeAcceder('productos', 'seguimiento'))
				throw new ForbiddenException('No tiene permiso para visualizar los movimientos de productos'); 

			if(isset($_POST['id'])){
				$desde = isset($_POST['desde'])?$_POST['desde']:date("Y/m/d");
				$hasta = isset($_POST['hasta'])?$_POST['hasta']:date("Y/m/d");

				$movimientos = $this->Productos->getMovimientos($_POST['id'], $desde, $hasta, $_POST['page']); 
				
				if(!$movimientos['success'])
					throw new BadRequestException($movimientos['msg']); 

				echo $this->json('Movimientos', $movimientos);
			}	
		}catch (Exception $e) {	

			if ($e instanceof RequestException) 
				echo $this->json( $e->getMsg(), $e->getData(), $e->getSatusCode() );
		}

	}

}
?>