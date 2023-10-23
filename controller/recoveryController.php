<?php
    if(isset($_SESSION[SESSION_NAME])){
        header("Location: ".URL."/panel");
        
    }

    if(!isset($url[1])){
        $tpl = new TplEngine('view/recover.html');
        $tpl->print();
        die();
    }
    $token = $url[1];
    $tpl = new TplEngine('view/resetPassword.html');
    $tpl->assignVar('TOKEN',$token);
    $tpl->print();
  

    
?>