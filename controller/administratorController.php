<?php
if(!isset($_SESSION[SESSION_NAME])){
        if($_SESSION[SESSION_NAME] != 27){
                header("Location: ".URL."/login");
        }
}
if(isset($url[1])){
        if($url[1] == 'map'){
                require 'model/UserModel.php';
                $user = new User();

                $tpl = new TplEngine('view/map.html');
                $tpl->assignVar('CANT_USER',$user->getUsers());
                $tpl->assignVar('CANT_CLIENT',$user->getClients());
                $tpl->print();   
                die();    
        }
}
    $tpl = new TplEngine('view/administrador.html');
    $tpl->print();
 ?>