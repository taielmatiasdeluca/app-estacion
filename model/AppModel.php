<?php   
    class App extends Database{
        public $conexion;

        public function __construct() {
            parent::__construct();
            $response = $this->conexion->query("SELECT * FROM `apps`");
            $campos = $response->fetch_fields();

            
            foreach ($campos as $key => $campo) {
                $buffer = $campo->name;
                $this->$buffer = "";
            }            
        }

        function list(){
            $sql = "SELECT * FROM apps;";
            return $this->conexion->query($sql)->fetch_all(MYSQLI_ASSOC);
        }
    }


?>