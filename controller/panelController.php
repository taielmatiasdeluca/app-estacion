<?php

    $tpl = new TplEngine('view/panel.html');

    require 'model/UserModel.php';
    $user = new User();
    $user->getData([]);

    $tpl->print();
?>