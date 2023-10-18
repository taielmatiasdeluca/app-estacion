<?php
	session_start();

	//Levanta las configuraciones
	require 'env.php';
	//Levanta la base de datos
	require 'ddbb/Database.php';
	//Motor de Plantillas
	require 'lib/TplEngine.php';




	

	//Config Del Router
	$main_url = 'alumno/3890/app-estacion'; //Url por defecto que debe tener la web
	$url = array_values(array_filter(explode('/',str_replace($main_url,'',$_SERVER['REQUEST_URI']))));//Url filtrada y con la url defecto eliminada
	
	$method = $_SERVER['REQUEST_METHOD']; //Metodo con el que se quizo ingresar

	if(isset($url[0])){
		//Ingreso a alguna api

		if($url[0] == 'api'){
			//Levanta funciones de las api
			require 'config/apiTools.php';
			//Transforma la respuesta a un json
			header("Content-Type: application/json");

			//Modelo
			if(isset($url[1])){
				$model_requested = ucfirst($url[1]);
				$url_model = "model/{$model_requested}Model.php";
				if(file_exists($url_model)){
					//Incluye el controlador
					require $url_model;
					$modelo = new $model_requested;
					if(isset($url[2])){
						$method_requested = $url[2];
						if(method_exists($modelo,$method_requested)){
							$response = $modelo->$method_requested();
							echo json_encode($response);
							die();
						}
						else{
							error('No existe esa funcionalidad');
						}
					}
				}else{
					error('No se encontro esa api');
				}
			}else{
				error('No se ingreso ninguna api');
			}
		}
		else{
			if(file_exists('controller/'.$url[0].'Controller.php')){
				//Incluye el controlador
				include 'controller/'.$url[0].'Controller.php';
			}
			else{
				//No se encontro
				include 'controller/404Controller.php';
			}
		}
	}else{
		//Se carga la landing
		include 'controller/panelController.php';
	}
	
?>