<?php

    if(!isset($url[1])){
        header("Location: ".URL."/panel");
    }
    $token = $url[1];

    $db = new Database();
    $sql = "SELECT `id` FROM `appestacion__usuarios` WHERE `token_action`=?;";
    //Prepara
    $query = $db->conexion->prepare($sql);
    //Bind de los datos
    $query->bind_param('s',$token);
    $query->execute();
    $query->store_result();
    if($query->errno == 0){
        if($query->num_rows==1){
            $id = 0;
            $query->bind_result($id);
            $query->fetch(); 
            if($db->conexion->query("UPDATE `appestacion__usuarios` SET `activo`=1,`token_action`=NULL,`active_date`= CURRENT_TIMESTAMP() WHERE `id`=$id")){
                $tpl = new TplEngine('view/verificar.html');
                $tpl->print();
                die();
            }

        }
    }
?>
<h1>Error no se pudo verificar</h1>