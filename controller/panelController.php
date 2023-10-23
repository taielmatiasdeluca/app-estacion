<?php
    if(!isset($_SESSION[SESSION_NAME])){
        header("Location: ".URL."/login");
        
    }

    $tpl = new TplEngine('view/panel.html');

    $tpl->print();
?>