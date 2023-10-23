<?php
    if(isset($_SESSION[SESSION_NAME])){
        header("Location: ".URL."/panel");
        
    }
    $tpl = new TplEngine('view/register.html');
    $tpl->print();
?>