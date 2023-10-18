<?php
    function error($text){
        echo json_encode([
            'state'=>'error',
            'type'=>$text
        ]);
        die();
    }

    function handleData(){
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        return $data;
    }

    function errorVerificacion(){
        $return = [
            'state'=>'error',
            'type'=>'Corrobore los Datos'
        ]; 
        echo json_encode($return);
        die();
    }

    function success($text){
        echo json_encode([
            'state'=>'success',
            'type'=>$text
        ]);
        die();
    }
    
    function verifyData($dataArray,$dataNeeded){
        foreach ($dataNeeded as $key => $value) {
            if(!isset($dataArray->$value)){
                return false;
            }
        }
        return true;
    }

?>