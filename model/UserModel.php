<?php   
    class User extends Database{
        public $conexion;

        public function __construct() {
            parent::__construct();
            $response = $this->conexion->query("SELECT * FROM `appestacion__usuarios`");
            $campos = $response->fetch_fields();

            
            foreach ($campos as $key => $campo) {
                $buffer = $campo->name;
                $this->$buffer = "";
            }            
        }

        private function checkEmail($email){
            $sql = "SELECT `id` FROM `appestacion__usuarios` WHERE `email`='$email'";
            $query = $this->conexion->query($sql);
            if($query->num_rows >= 1){
                return true;
            }
            return false;
        }

        function register($parameters){

            if(!isset($parameters['email']) || !isset($parameters['password']) ){
                error('Faltan datos');
            }

            if($this->checkEmail($parameters['email'])){
                error('Email ya registrado');
            }

            $sql = "
            INSERT INTO `appestacion__usuarios` 
            (`token_action`, `email`,  `contraseña`,  `token`) 
            VALUES (?,?,?,?);";

            $token = md5(uniqid().$parameters['email'].$parameters['password']);

            $this->email = $parameters['email'];
            $this->token = $token;
            $this->contraseña = md5($parameters['password']);

            $query = $this->conexion->prepare($sql);
            $query->bind_param('ssss', $this->token,$this->email,$this->contraseña,$this->token);
            $query->execute();
            $query->store_result();
            if($query->errno == 0){
                $this->id = $query->insert_id;
                $this->sendEmail();
                success('Se agrego el usuario');
            }
            error('Hubo un error agregando el usuario');

        }

        function logout(){
            session_unset();
            session_destroy();
            success('Sesion Cerrada con exito');
        }

        function resetPass($parameters){
            if(!isset($parameters['password']) || !isset($parameters['token']) ){
                error('Faltan datos');
            }
            $sql = "UPDATE `appestacion__usuarios` SET `contraseña`=?,`bloqueado`=0,`recuperado`=0,`token_action`=null  WHERE token_action=?";
            //Prepara
            $query = $this->conexion->prepare($sql);
            //Bind de los datos
            $password = md5($parameters['password']);

            $query->bind_param('ss',$password,$parameters['token']);
            $query->execute();
            $query->store_result();
            if($query->errno == 0){
                success('Se modifico la contraseña con exito');
            }
            error('No se pudo modificar');
        }

        private function generateToken(){
            $token = bin2hex(openssl_random_pseudo_bytes(16));
            $sql = "UPDATE `appestacion__usuarios` SET `token_action`=? WHERE `id`=?";
            $query = $this->conexion->prepare($sql);
            $query->bind_param('si',$token,$this->id);
            $query->execute();
            $query->store_result();
            if($query->errno == 0){
                return $token;
            }
            error('Error interno');
        }

        function recover($parameters){
            if(!isset($parameters['email'])){
                error('Faltan datos');
            }
            $this->email = $parameters['email'];
            $sql = "SELECT `id` FROM `appestacion__usuarios` WHERE `email`=?;";
            //Prepara
            $query = $this->conexion->prepare($sql);
            //Bind de los datos
            $query->bind_param('s',$this->email);
            $query->execute();
            $query->store_result();
            if($query->errno == 0){
                if($query->num_rows==1){
                    $id = 0;
                    $query->bind_result($id);
                    $query->fetch(); 
                    $this->id = $id;
                    $this->token = $this->generateToken();
                    $emailEngine = new EmailEngine();

                    $emailEngine->send($this->email,'Reestablecer contraseña',"<a href='https://mattprofe.com.ar/alumno/3890/app-estacion/recovery/".$this->token."'>Reiniciar Contraseña</a>");


                }
            }
            success('Si su email corresponde a una cuenta, se le envio los pasos revise su inbox');

        }

        function block($parameters){
            $sql = "SELECT `id`,email FROM `appestacion__usuarios` WHERE `token`=?;";
            //Prepara
            $query = $this->conexion->prepare($sql);
            //Bind de los datos
            $query->bind_param('s',$parameters['token']);
            $query->execute();
            $query->store_result();
            if($query->errno == 0){
                if($query->num_rows==1){
                    $id = 0;

                    $query->bind_result($id,$email);
                    $query->fetch(); 
                    if(!$id){
                        error('El token no pertenece a ningun usuario');
                    }
                    //Se encontro el usuario
                    if($this->conexion->query("UPDATE `appestacion__usuarios` SET `bloqueado`=1,`blocked_date`= CURRENT_TIMESTAMP() WHERE `id`=$id")){
                        
                        $token = $this->generateToken();
                        $emailEngine = new EmailEngine();
                        $emailEngine->send($email,'Cuenta Bloqueada',"
                            <h2> Su cuenta ha sido bloqueada puede reiniciar su contraseña de requerirlo </h2>
                            <a href='https://mattprofe.com.ar/alumno/3890/app-estacion/recovery/".$token."'>Restablecer Contraseña</a>");
                    }

                }
            }
        }


        function login($parameters){
            if(!isset($parameters['email']) || !isset($parameters['password'])){
                error('Faltan datos');
            }
            $this->email = $parameters['email'];
            $this->password = md5($parameters['password']);

            $sql = "SELECT `id`, `bloqueado`,`activo`,`recuperado`,`token` FROM `appestacion__usuarios` WHERE `email`=? and `contraseña`=?;";
            //Prepara
            $query = $this->conexion->prepare($sql);
            //Bind de los datos
            $password = md5($this->password);
            $query->bind_param('ss',$this->email,$this->password);
            $query->execute();
            $query->store_result();
            if($query->errno == 0){
                if($query->num_rows==1){
                    $id = false;
                    $bloqueado = 0;
                    $activo = 0;
                    $recuperado = 0;
                    $token='';
                    $query->bind_result($id,$bloqueado,$activo,$recuperado,$token);
                    $query->fetch(); 
                    if($bloqueado){
                        error('La cuenta ha sido bloqueada');
                    }
                    if($recuperado){
                        error('La cuenta esta siendo recuperada');
                    }
                    if(!$activo){
                        error('La cuenta no ha sido activada');
                    }
                    if($id){
                        $_SESSION[SESSION_NAME] = $id;

                        $navegador = explode(' ',$_SERVER['HTTP_USER_AGENT'])[0];
                        $ip = $_SERVER['REMOTE_ADDR'];
                        $sistema  = explode(')',explode('(',$_SERVER['HTTP_USER_AGENT'])[1])[0];
                        $this->token = $token;
                        $emailEngine = new EmailEngine();
                        $emailEngine->send($this->email,'Ingresaron a Drive App ¿Fuiste Vos ?',"
                            Datos del ingresante:
                            Navegador: $navegador
                            Ip: $ip
                            Sistema Operativo: $sistema

                            <h2> No fuiste vos ? </h2>
                            <a href='https://mattprofe.com.ar/alumno/3890/app-estacion/blocked/".$this->token."'>Bloquear Cuenta</a>");


                        success('Se inicio sesion con exito');
                    }
                }
                error('Credeciales incorrectas');
            }
        }

        private function sendEmail(){
            $emailEngine = new EmailEngine();
            $emailEngine->send($this->email,'Verificar cuenta de app estacion',"<a href='https://mattprofe.com.ar/alumno/3890/app-estacion/verify/".$this->token."'>Verificat cuenta</a>");

        }
    }


?>