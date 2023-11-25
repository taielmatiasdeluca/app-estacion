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

        function getClients(){
            $query = $this->conexion->query("SELECT count(id) FROM `appestacion__ips`");
            return $query->fetch_all()[0][0];
        }

        function getUsers(){
            $query = $this->conexion->query("SELECT count(id) FROM `appestacion__usuarios`");
            return $query->fetch_all()[0][0];
        }

        function location($parameters){
            $query = $this->conexion->query("SELECT `ip`,`latitud`,`latitud`,`longitud`,`accesos` FROM `appestacion__ips`");

            return $query->fetch_all(MYSQLI_ASSOC);
        }

        function getData($parameters){
            $token = bin2hex(openssl_random_pseudo_bytes(16));
            $ip = $_SERVER['REMOTE_ADDR'];
            $info = json_decode(file_get_contents("http://ipwho.is/$ip"));
            $latitud = $info->latitude;
            $longitud = $info->longitude;
            $pais = $info->country;
            $navegador = explode(' ',$_SERVER['HTTP_USER_AGENT'])[0];
            $sistema  = explode(')',explode('(',$_SERVER['HTTP_USER_AGENT'])[1])[0];

            //Verifica si existe la ip en la base de datos
            $query = $this->conexion->query("SELECT id FROM `appestacion__ips` WHERE `ip`='$ip'");
            if($query->num_rows >= 1){
                $id_ip = $query->fetch_all()[0][0];
                $this->conexion->query("UPDATE `appestacion__ips` SET `accesos` = `accesos` +1  WHERE `id`=$id_ip");
            }
            else{
                $query = $this->conexion->query("INSERT INTO `appestacion__ips`(`ip`, `accesos`,latitud,longitud) VALUES ('$ip',1,'$latitud','$longitud')");
                $id_ip = $this->conexion->insert_id;
            }

            //Verifica si existe el navegador en la base de datos
            $query = $this->conexion->query("SELECT id FROM `appestacion__navegadores` WHERE navegador='$navegador'");
            if($query->num_rows >= 1){

                $id_navegador = $query->fetch_all()[0][0];
            }
            else{
                $this->conexion->query("INSERT INTO `appestacion__navegadores` (`id`, `navegador`) VALUES (NULL, '$navegador')");
                $id_navegador = $this->conexion->insert_id;
            }

            //Verifica si existe el pais en la base de datos
            $query = $this->conexion->query("SELECT `id` FROM `appestacion__paises` WHERE pais='$pais'");
            if($query->num_rows >= 1){
                $id_pais = $query->fetch_all()[0][0];
            }
            else{
                $this->conexion->query("INSERT INTO `appestacion__paises` (`id`, `pais`) VALUES (NULL, '$pais')")->insert_id;
                $id_pais =$this->conexion->insert_id;

            }

            //Verifica si existe el sistema en la base de datos
            $query = $this->conexion->query("SELECT `id` FROM `appestacion__sistemas` WHERE sistema='$sistema'");
            if($query->num_rows >= 1){
                $id_sistema = $query->fetch_all()[0][0];
            }
            else{

                $this->conexion->query("INSERT INTO `appestacion__sistemas` (`id`, `sistema`) VALUES (NULL, '$sistema') ")->insert_id;
                $id_sistema =$this->conexion->insert_id;
                
            }
            $sql = "INSERT INTO `appestacion__tracker` (`token`, `idIp`, `idPais`, `idNavegador`, `idSistema`) VALUES ('$token', '$id_ip','$id_pais', '$id_navegador','$id_sistema');";
            $this->conexion->query($sql);
            if(!$this->conexion->error){
                return 'exito';
            }
            return 'error';    
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
                        $emailEngine->send($this->email,'Ingresaron a App Estacion ¿Fuiste Vos ?',"
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