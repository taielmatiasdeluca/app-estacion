<?php 
	class EmailEngine {
		public $conexion;
		function __construct()
		{
		$mail = new PHPMailer\PHPMailer\PHPMailer();
		$mail->isSMTP();

	        $mail->SMTPDebug = 0 ;
	        $mail->Host = 'smtp.gmail.com';
	        $mail->Port = '587';
	        $mail->SMTPAuth = true; 
	        $mail->SMTPSecure = 'tls';
	        $mail->Username = '';
	        $mail->Password = '';
	        $mail->setFrom('', '');
	        $this->conexion = $mail;
		}

		function send($destino,$asunto,$contenido){
			$this->conexion->addAddress($destino);
        	$this->conexion->isHTML(true);
	        $this->conexion->Subject = utf8_decode($asunto);
	        $this->conexion->Body = utf8_decode($contenido);
	        if(!$this->conexion->send()){
	            error_log("Mailer no se pudo enviar el correo!" );
				return array("errno" => 1, "error" => "No se pudo enviar.");
	        }else{
				return array("errno" => 0, "error" => "Enviado con exito.");
			}   
		}


	}


?>
