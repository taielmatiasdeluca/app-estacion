
<?php
	if(!isset($url[1])){
		header("Location: ${URL}/panel");
	}
    $tpl = new TplEngine('view/detalle.html');
    $tpl->assignVar('CHIP',$url[1]);
    $tpl->print();
?>