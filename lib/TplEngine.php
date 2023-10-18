<?php
    class TplEngine {

        public $url_tpl;
        public $tpl;
        public function __construct($url_tpl) {

            $this->url_tpl = $url_tpl;
            if(!file_exists($url_tpl)){
                echo "<h1> Error no se encontro la plantilla </h1>";
                die();
            }
            $this->tpl = file_get_contents($url_tpl);

            
            $this->tpl = $this->checkForComponent($this->tpl);




           

        
         

            if($this->testVar("URL_APP")){
				$this->assignVar("URL_APP", URL);
            }
        
        }

        private function checkForComponent($tpl){
            while(strpos($tpl,'@component:') !== false) {
                if($pure_component = $this->get_string_between($tpl,'@component:','~}')){
                    $component = trim(strtolower($pure_component));
                    $route = "view/component/{$component}.html";
                    if(file_exists($route)){
                        $component_content = file_get_contents($route);
                        $component_content = $this->checkForComponent($component_content);
                        $tpl = str_replace("@component:{$pure_component}~}",$component_content,$tpl);
                    }
                    else{
                        echo "<h1> error en el componente {$component} </h1>";
                        die();
                    }
                }
                else{
                    echo "<h1> No se encontro el ~} </h1>";
                    die();
                }
            }
            return $tpl;
        }

        private function get_string_between($string, $start, $end){
            $string = ' ' . $string;
            $ini = strpos($string, $start);
            if ($ini == 0) return false;
            $ini += strlen($start);
            $len = strpos($string, $end, $ini) - $ini;
            return substr($string, $ini, $len);
        }


   

        private function testComponent($component_tpl){
            return file_exists("view/component/{$component_tpl}.html");	
        }

        public function getComponent($var,$component_tpl){
            if(!$this->testComponent($component_tpl)){
				echo "<b>error tpl:</b> No se encontro el componente <u>$component_tpl</u>";
			}
            $this->assignVar($var,file_get_contents("view/component/{$component_tpl}.html"));
        }



        private function testVar($var_tpl){
			return strpos($this->tpl, "{{{$var_tpl}}}");		
		}

		public function assignVar($var_tpl, $value){
			if(!$this->testVar($var_tpl)){
				echo "<b>error tpl:</b> No se encontro la variable <u>$var_tpl</u>";
			}
			$this->tpl=str_replace("{{{$var_tpl}}}", $value, $this->tpl);
		}

		public function print(){
			echo $this->tpl;
		}
        
    }

?>