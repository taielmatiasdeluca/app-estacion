<?php


    if(!isset($url[1])){
        header("Location: ".URL."/panel");
    }
    $token = $url[1];
    require 'model/UserModel.php';
    $user = new User();
    $user->block(['token'=>$token]);
    header("Location: ".URL);



?>
