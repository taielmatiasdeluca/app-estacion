<?php
    function getComponents($component){
        $file_path = "view/components/$component.php";
        if(!file_exists($file_path)){
            return "<h2> RECURSO NO ENCONTRADO </h2>";
        }
        include $file_path;
    }

    function URL(){
        echo URL;
    }

?>