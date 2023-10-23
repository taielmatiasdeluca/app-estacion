<?php
	session_start();
	
	// Cabeceras para hacer que la API sea pública
	header("Access-Control-Allow-Origin: *");
	header('Access-Control-Allow-Credentials: true');
	header('Access-Control-Allow-Methods: PUT, GET, POST, DELETE, OPTIONS');
	header("Content-Type: application/json");
	//Levanta las configuraciones


	require '../env.php';
	//Levanta la base de datos
	require '../ddbb/Database.php';
	//Motor de Emails
	require '../lib/EmailEngine.php';

	include '../vendor/Mailer/src/PHPMailer.php';
	include '../vendor/Mailer/src/SMTP.php';
	include '../vendor/Mailer/src/Exception.php';

	//Config Del Router
	$main_url = '/alumno/3890/app-estacion/api/'; //Url por defecto que debe tener la web

	$url = array_values(array_filter(explode('/',str_replace($main_url,'',$_SERVER['REQUEST_URI']))));//Url filtrada y con la url defecto eliminada
	$method = $_SERVER['REQUEST_METHOD']; //Metodo con el que se quizo ingresar
	//Levanta funciones de las api
	require '../config/apiTools.php';


	//Modelo
	if(isset($url[0])){
		$model_requested = ucfirst($url[0]);
		$url_model = "../model/{$model_requested}Model.php";
		if(file_exists($url_model)){
			//Incluye el controlador
			require $url_model;
			$modelo = new $model_requested;
			if(isset($url[1])){
				$method_requested = $url[1];
				if(method_exists($modelo,$method_requested)){
					switch ($method) {
						case 'GET': // obtener
								unset($url[0]);
								unset($url[1]);
								$parameters = array_values($url);
							break;
						
						case 'POST': // colocar
							$parameters = $_POST;
							break;

						case 'PUT': // actualizar
							
							$_PUT = json_decode(file_get_contents("php://input"));

							$parameters = $_PUT;				

							break;
				 
						case 'DELETE': // borrar
								unset($url[0]);
								unset($url[1]);
								$parameters = array_values($url);
							break;
					}
					$response = $modelo->$method_requested($parameters);
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


?>