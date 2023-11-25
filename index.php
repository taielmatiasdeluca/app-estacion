<?php
	session_start();

	//Levanta las configuraciones
	require 'env.php';
	//Levanta la base de datos
	require 'ddbb/Database.php';
	//Motor de Plantillas
	require 'lib/TplEngine.php';


	require 'lib/EmailEngine.php';

	include 'vendor/Mailer/src/PHPMailer.php';
	include 'vendor/Mailer/src/SMTP.php';
	include 'vendor/Mailer/src/Exception.php';



	

	//Config Del Router
	$main_url = 'alumno/3890/app-estacion'; //Url por defecto que debe tener la web
	$url = array_values(array_filter(explode('/',str_replace($main_url,'',$_SERVER['REQUEST_URI']))));//Url filtrada y con la url defecto eliminada
	
	$method = $_SERVER['REQUEST_METHOD']; //Metodo con el que se quizo ingresar

	if(isset($url[0])){
	
		if(file_exists('controller/'.$url[0].'Controller.php')){
			//Incluye el controlador
			include 'controller/'.$url[0].'Controller.php';
		}
		else{
			//No se encontro
			include 'controller/404Controller.php';
		}
		
	}else{
		//Se carga la landing
		include 'controller/landingController.php';
	}
	
?>