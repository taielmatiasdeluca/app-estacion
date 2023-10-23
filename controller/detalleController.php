<?php

	if(!isset($_SESSION[SESSION_NAME])){
        header("Location: ".URL."/login");
    }

	if(!isset($url[1])){
        header("Location: ".URL."/panel");
	}
    $tpl = new TplEngine('view/detalle.html');
    $tpl->assignVar('CHIP',$url[1]);
    $tpl->print();
?>